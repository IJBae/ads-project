<?php

namespace App\Models\EventManage;

use CodeIgniter\Model;
use App\Libraries\SMS;

class AdvertiserModel extends Model
{
    public function getAdvertisers($data)
    {
        $srch = $data['searchData'];
        $builder = $this->db->table('(SELECT info.seq AS info_seq, adv.*, SUM(db.db_count) as db_count, SUM(db.db_count)* info.db_price as price
            FROM event_advertiser AS adv
            LEFT JOIN event_information AS info ON info.advertiser = adv.seq
            LEFT JOIN event_leads_count AS db ON db.seq = info.seq AND db.date BETWEEN LAST_DAY(NOW() - interval 1 month) + interval 1 DAY AND LAST_DAY(NOW())
            WHERE 1
            GROUP BY adv.seq, info.seq, db.seq
            ORDER BY adv.seq DESC) AS advertiser');
        $builder->select('advertiser.*, COUNT(advertiser.info_seq) AS total, SUM(advertiser.db_count) AS sum_db, SUM(advertiser.price) AS sum_price, (advertiser.account_balance - SUM(advertiser.price)) as remain_balance');
        $builder->groupBy('advertiser.seq');

        if (!empty($srch['stx'])) {
            $builder->groupStart();
            $builder->like('advertiser.name', $srch['stx']);
            $builder->orLike('advertiser.agent', $srch['stx']);
            $builder->groupEnd();
        }

        // limit 적용하지 않은 쿼리
        $builderNoLimit = clone $builder;

        $orderBy = [];
        if (!empty($data['order'])) {
            foreach ($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if ($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $orderBy[] = "advertiser.seq desc";
        $builder->orderBy(implode(",", $orderBy), '', true);
        if (isset($data['length']) && !isset($data['noLimit']) && ($data['length'] != -1)) $builder->limit($data['length'], $data['start']);

        $result = $builder->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();

        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getAdvertiser($data)
    {
        $builder = $this->db->table('event_advertiser');
        $builder->select('*');
        $builder->where('seq', $data['seq']);

        $result = $builder->get()->getRowArray();
        return $result;
    }

    public function getAdvertiserByName($data)
    {
        $builder = $this->db->table('event_advertiser');
        $builder->select('*');
        $builder->where('name', $data['name']);

        $result = $builder->get()->getRowArray();
        return $result;
    }

    public function getCompanies($stx)
    {
        $builder = $this->db->table('companies');
        $builder->select('*');
        $builder->like('name', $stx);

        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function getOverwatchByAdvertiser($seq)
    {
        $builder = $this->db->table('event_overwatch');
        $builder->select('*');
        $builder->where('advertiser', $seq);

        $result = $builder->get()->getRowArray();
        return $result;
    }

    public function getMedia($data)
    {
        $subBuilder = $this->db->table('event_information');
        $subBuilder->select('media');
        $subBuilder->where('advertiser', $data['seq']);
        $subResult = $subBuilder->getCompiledSelect();

        $builder = $this->db->table('event_media');
        $builder->select('*');
        $builder->where("seq IN($subResult)");

        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function createAdv($data)
    {
        $this->db->transStart();
        $builder = $this->db->table('event_advertiser');
        $builder->insert($data);
        $result = $this->db->transComplete();
        return $result;
    }

    public function updateAdv($data, $seq)
    {
        $this->db->transStart();
        $builder = $this->db->table('event_advertiser');
        $builder->where('seq', $seq);
        $builder->update($data);
        $result = $this->db->transComplete();
        return $result;
    }

    public function evtOverwatch()
    {
        $fp = fopen(WRITEPATH.'/logs/sms_log', 'w');
        $logText = '---------------------------'."\n";
        $logText .= '요청 시간: '.date('Y-m-d H:i:s')."\n";

        $monitor = [];                  // $monitor[휴대폰번호][seq] = 메세지
        $update_sql = [];
        $contact_check_by_media = [];   // $contact_check_by_media[seq][media] = date('Y-m-d')

        $builder = $this->db->table('event_overwatch');
        $builder->select("*");
        $result = $builder->get()->getResultArray();

        for ($i = 0; $i < count($result); $i++) {
            $media = json_decode($result[$i]['watch_list'], true);
            $last_send = json_decode($result[$i]['last_send'] ?? '', true);
            if ($result[$i]['watch_all'] != NULL && $result[$i]['watch_all'] > 0) {

                if ($last_send && array_key_exists('all', $last_send) && $last_send['all'] == date('Y-m-d')) continue; // 해당 당일자 알림 이력이 있으면 passvar_dump($result[$i]);
                $builder = $this->db->table('event_information');
                $builder->select('seq');
                $builder->where('advertiser', $result[$i]['advertiser']);
                $ei_result = $builder->get()->getResultArray();

                $ei_arr = array();
                for ($num = 0; $num < count($ei_result); $num++) {
                    $ei_arr[$num] = $ei_result[$num]['seq'];
                }

                $builder = $this->db->table('event_leads');
                $builder->select('event_seq, count(seq) AS count');
                $builder->whereIn('event_seq', $ei_arr);
                $builder->where('status', 1);
                $builder->where('reg_date >= ', date('Y-m-d 00:00:00'));
                $builder->where('reg_date <= ', date('Y-m-d 23:59:59'));
                $cnt = $builder->get()->getResultArray();

                if ($cnt[0]['count'] >= $result[$i]['watch_all']) {
                    $logText .= 'event_advertiser seq : '.$result[$i]['advertiser'].' === watch_all o , 목표 수량 표기 check o, count check o ==='."\n";
                    $builder = $this->db->table('event_advertiser');
                    $builder->select('name');
                    $builder->where('seq', $result[$i]['advertiser']);
                    $tmp_result = $builder->get()->getResultArray();
                    $message = "[{$tmp_result[0]['name']}]{$cnt[0]['count']}건으로 전체 목표 수량 도달하였습니다.\n";

                    // 전화번호 별 sms 배열
                    $contact_array = explode(';', $result[$i]['contact']);
                    for ($j = 0; $j < sizeof($contact_array); $j++) {
                        if ($contact_array[$j]) $monitor[$contact_array[$j]][$result[$i]['seq']] = $message;
                    }

                    // seq 별 발송 내역 배열
                    $contact_check_by_media[$result[$i]['seq']] = $last_send;
                    $contact_check_by_media[$result[$i]['seq']]['all'] = date('Y-m-d');
                }
            } else {
                foreach ($media as $key => $val) {
                    if ($val == 0) continue; // 목표 DB 수량 미표기 시 pass
                    if ($last_send && array_key_exists($key, $last_send) && $last_send[$key] == date('Y-m-d')) continue; // 해당 매체의 당일자 알림 이력이 있으면 pass

                    $builder = $this->db->table('event_information');
                    $builder->select('seq');
                    $builder->where('advertiser', $result[$i]['advertiser']);
                    $builder->where('media', $key);
                    $ei_result = $builder->get()->getResultArray();

                    $ei_arr = array();
                    for ($num = 0; $num < count($ei_result); $num++) {
                        $ei_arr[$num] = $ei_result[$num]['seq'];
                    }

                    $builder = $this->db->table('event_leads');
                    $builder->select('event_seq, count(seq) AS count');
                    $builder->whereIn('event_seq', $ei_arr);
                    $builder->where('status', 1);
                    $builder->where('reg_date >= ', date('Y-m-d 00:00:00'));
                    $builder->where('reg_date <= ', date('Y-m-d 23:59:59'));
                    $cnt = $builder->get()->getResultArray();

                    if ($cnt[0]['count'] >= $val) {
                        $logText .= 'event_advertiser seq : '.$result[$i]['advertiser'].'watch_all x , 목표 수량 표기 check o, count check o'."\n";
                        $builder = $this->db->table('event_advertiser');
                        $builder->select('name');
                        $builder->where('seq', $result[$i]['advertiser']);
                        $tmp_result = $builder->get()->getResultArray();
                        $builder = $this->db->table('event_media');
                        $builder->select('media');
                        $builder->where('seq', $key);
                        $med = $builder->get()->getResultArray();
                        $message = "[{$tmp_result[0]['name']}][{$med[0]['media']}][{$cnt[0]['count']}]\n";

                        // 전화번호 별 sms 배열
                        $contact_array = explode(';', $result[$i]['contact']);
                        for ($j = 0; $j < sizeof($contact_array); $j++) {
                            if ($contact_array[$j]) $monitor[$contact_array[$j]][$result[$i]['seq']] = $message;
                        }
                        // seq 별 발송 내역 배열
                        $contact_check_by_media[$result[$i]['seq']] = $last_send;
                        $contact_check_by_media[$result[$i]['seq']][$key] = date('Y-m-d');
                    }
                }
            }
        }

        $logText .= '생성 리스트: '.json_encode($monitor, JSON_UNESCAPED_UNICODE)."\n";

        $l = 0;
        foreach ($monitor as $key => $val) {
            $msg = '';
            foreach ($val as $k => $v) {
                $last_send_txt = json_encode($contact_check_by_media[$k]);
                $builder = $this->db->table('event_overwatch');
                $setData = [
                    'last_contact' => date('Y-m-d H:i:s'),
                    'last_send' => $last_send_txt,
                ];
                $builder->set($setData);
                $builder->where('seq', $k);
                $upd = $builder->getCompiledUpdate();

                $data[$l] = [
                    'last_contact' => date('Y-m-d H:i:s'),
                    'last_send' => $last_send_txt,
                    'seq' => $k,
                ];

                if (array_search($upd, $update_sql) === false) array_push($update_sql, $upd);
                $msg .= $v;
                $l++;
                $logText .= "문자 내용[{$l}]: ".$msg."\n";
            }

            $strDest = array($key);    // 문자 수신번호 배열
            $nCount = 1;                    // 문자 수신번호 갯수
            // 문자 발송에 필요한 항목을 배열에 추가
            $sms_result = SMS::Add($strDest, '01040010000', '', '', '', $msg, '', $nCount);
            // 패킷이 정상적이라면 발송에 시도합니다.
            if (!$sms_result = SMS::Send()) {
                $logText .= '문자전송 실패: '.date('Y-m-d H:i:s').PHP_EOL."\n";
            }
        }
        if (isset($data)) {
            $builder->updateBatch($data, 'seq');
        }

        $logText .= 'update sql: '.json_encode($update_sql, JSON_UNESCAPED_UNICODE)."\n";
        $logText .= '데이터: '.json_encode($data, JSON_UNESCAPED_UNICODE)."\n";
        $logText .= '요청 종료 시간: '.date('Y-m-d H:i:s')."\n";
        fwrite($fp, print_r($logText,true).PHP_EOL);
        fclose($fp);
    }
}
