<?php

namespace App\Models\Integrate;

use CodeIgniter\Model;
use CodeIgniter\Database\RawSql;

class IntegrateModel extends Model
{
    protected $zenith;
    
    public function __construct()
    {
        $this->zenith = \Config\Database::connect();
    }

    public function getEventLead($searchParams)
    {
        if (!empty($searchParams['sdate'])) {
            $searchSDate = (new \DateTime($searchParams['sdate']))->format('Y-m-d');
        }
        if (!empty($searchParams['edate'])) {
            $searchEDate = (new \DateTime($searchParams['edate']))->format('Y-m-d');
        }

        $builder = $this->zenith->table('event_leads as el');
        $builder->select("
        info.seq as info_seq, 
        adv.name AS advertiser, 
        med.media, adv.is_stop, 
        info.description AS description, 
        info.partner_company as company,
        info.another_url as another_url,
        dec_data(el.phone) as dec_phone, 
        el.seq,
        el.name,
        el.reg_date,
        el.gender,
        el.age,
        el.branch,
        el.addr,
        el.email,
        el.site,
        el.add1,
        el.add2,
        el.add3,
        el.add4,
        el.add5,
        el.add6,
        el.status,
        count(lm.leads_seq) as memo_cnt,
        GROUP_CONCAT(
            DISTINCT CONCAT(lm.memo,'[',lm.username,']','[',lm.reg_date,']') 
            ORDER BY lm.reg_date DESC
            SEPARATOR '\n'
          ) AS memos
        ");
        $builder->join('event_information as info', "info.seq = el.event_seq", 'left');
        $builder->join('event_advertiser as adv', "info.advertiser = adv.seq AND adv.is_stop = 0", 'left');
        $builder->join('event_media as med', 'info.media = med.seq', 'left');
        $builder->join('event_leads_memo as lm', 'el.seq = lm.leads_seq', 'left');

        if(!auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){
            $builder->join('companies_users as cu', "adv.company_seq = cu.company_id", 'left');
            $builder->where('cu.user_id', auth()->user()->id);
        }

        $builder->where('el.is_deleted', 0);
        $builder->where('el.reg_date >=', $searchSDate. " 00:00:00");
        $builder->where('el.reg_date <=', $searchEDate. " 23:59:59");
        
        $builder->groupBy(['el.seq','lm.leads_seq'], true);
        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $orderBy[] = "seq DESC";
        $builder->orderBy(implode(",", $orderBy),'',true);
        // echo ($builder->getCompiledSelect()); exit;
        // 결과 반환
        $result = $builder->get()->getResultArray();

        return [
            'data' => $result,
        ];
    }

    public function getEventLeadCount($data)
    {
        $builder = $this->zenith->table('event_leads as el');
        $builder->select("
        el.seq,
        el.status,
        adv.name as advertiser,
        med.media as media,
        info.description as description,
        info.partner_company as company
        ");
        $builder->join('event_information as info', "info.seq = el.event_seq", 'left');
        $builder->join('event_advertiser as adv', "info.advertiser = adv.seq AND adv.is_stop = 0", 'left');
        $builder->join('event_media as med', 'info.media = med.seq', 'left');

        if(!auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){
            $builder->join('companies_users as cu', "adv.company_seq = cu.company_id", 'left');
            $builder->where('cu.user_id', auth()->user()->id);
        }

        $builder->where('el.is_deleted', 0);
        $builder->where('el.reg_date >=', $data['sdate']);
        $builder->where('el.reg_date <=', $data['edate']);
        
        $filteredBuilder = clone $builder;

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('adv.name', $data['stx']);
            $builder->orLike('info.seq', $data['stx']);
            $builder->orLike('med.media', $data['stx']);
            $builder->orLike('info.description', $data['stx']);
            $builder->orLike('el.name', $data['stx']);
            $builder->orLike('el.branch', $data['stx']);
            $builder->orLike('el.add1', $data['stx']);
            $builder->orLike('el.add2', $data['stx']);
            $builder->orLike('el.add3', $data['stx']);
            $builder->orLike('el.add4', $data['stx']);
            $builder->orLike('el.add5', $data['stx']);
            $builder->groupEnd();
        }

        if(!empty($data['company'])){
            $company = explode("|",$data['company']);
            $index = array_search('리스타', $company);
            if($index !== false){
                $company[$index] = '';
            }
            $builder->whereIn('info.partner_company', $company);
        }

        if(!empty($data['advertiser'])){
            $builder->whereIn('adv.name', explode("|",$data['advertiser']));
        }

        if(!empty($data['media'])){
            $builder->whereIn('med.media', explode("|",$data['media']));
        }

        if(!empty($data['description'])){
            $builder->whereIn('info.description', explode("|",$data['description']));
        }
        if($builder == $filteredBuilder) {
            $filteredResult = $builder->get()->getResultArray();
            $noFilteredResult = $filteredResult;
        } else {
            $filteredResult = $builder->get()->getResultArray();
            $noFilteredResult = $filteredBuilder->get()->getResultArray();
        }

        $result = [
            'filteredResult' => $filteredResult,
            'noFilteredResult' => $noFilteredResult
        ];

        return $result;
    }

    public function getEventLeadRow($data)
    {
        $builder = $this->zenith->table('event_leads as el');
        $builder->select("
        el.seq, el.event_seq, el.site, el.name, DEC_DATA(el.phone) as phone, el.age, el.gender, el.branch, el.addr, el.email, el.add1, el.add2, el.add3, el.add4, el.add5, el.add6, el.status, el.ip, el.reg_date,
        adv.name as advertiser,
        med.media as media,
        info.description as description,
        info.partner_company as company,
        count(lm.leads_seq) as memo_cnt,
        GROUP_CONCAT(
            DISTINCT CONCAT(lm.memo,'[',lm.username,']','[',lm.reg_date,']') 
            ORDER BY lm.reg_date DESC
            SEPARATOR '\n'
          ) AS memos
        ");
        $builder->join('event_information as info', "info.seq = el.event_seq", 'left');
        $builder->join('event_advertiser as adv', "info.advertiser = adv.seq AND adv.is_stop = 0", 'left');
        $builder->join('event_media as med', 'info.media = med.seq', 'left');
        $builder->join('event_leads_memo as lm', 'el.seq = lm.leads_seq', 'left');

        if(!auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){
            $builder->join('companies_users as cu', "adv.company_seq = cu.company_id", 'left');
            $builder->where('cu.user_id', auth()->user()->id);
        }

        $builder->where('el.is_deleted', 0);
        $builder->where('el.reg_date >=', $data['sdate']." 00:00:00");
        $builder->where('el.reg_date <=', $data['edate']." 23:59:59");
        
        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('adv.name', $data['stx']);
            $builder->orLike('info.seq', $data['stx']);
            $builder->orLike('med.media', $data['stx']);
            $builder->orLike('info.description', $data['stx']);
            $builder->orLike('el.name', $data['stx']);
            $builder->orLike('el.branch', $data['stx']);
            $builder->orLike('el.add1', $data['stx']);
            $builder->orLike('el.add2', $data['stx']);
            $builder->orLike('el.add3', $data['stx']);
            $builder->orLike('el.add4', $data['stx']);
            $builder->orLike('el.add5', $data['stx']);
            $builder->groupEnd();
        }

        if(!empty($data['company'])){
            $company = explode("|",$data['company']);
            $index = array_search('리스타', $company);
            if($index !== false){
                $company[$index] = '';
            }
            $builder->whereIn('info.partner_company', $company);
        }

        if(!empty($data['advertiser'])){
            $builder->whereIn('adv.name', explode("|",$data['advertiser']));
        }

        if(!empty($data['media'])){
            $builder->whereIn('med.media', explode("|",$data['media']));
        }

        if(!empty($data['description'])){
            $builder->whereIn('info.description', explode("|",$data['description']));
        }
        $builder->groupBy(['el.seq','lm.leads_seq'], true);
        $builder->orderBy('el.seq DESC',true);

        // echo $builder->getCompiledSelect(); exit;
        
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function saveDownloadData($data)
    {
        $builder = $this->zenith->table('event_leads_download');
        $builder->insert($data);
    }


    public function getStatusCount($data)
    {
        $builder = $this->zenith->table('event_leads as el');
        $builder->select("
        COUNT(CASE WHEN el.status=1 then 1 end) as 인정, 
        COUNT(CASE WHEN el.status=2 then 1 end) as 중복, 
        COUNT(CASE WHEN el.status=3 then 1 end) as 성별불량, 
        COUNT(CASE WHEN el.status=4 then 1 end) as 나이불량, 
        COUNT(CASE WHEN el.status=5 then 1 end) as 콜불량, 
        COUNT(CASE WHEN el.status=6 then 1 end) as 번호불량, 
        COUNT(CASE WHEN el.status=7 then 1 end) as 테스트, 
        COUNT(CASE WHEN el.status=8 then 1 end) as 이름불량, 
        COUNT(CASE WHEN el.status=9 then 1 end) as 지역불량, 
        COUNT(CASE WHEN el.status=10 then 1 end) as 업체불량, 
        COUNT(CASE WHEN el.status=11 then 1 end) as 미성년자, 
        COUNT(CASE WHEN el.status=12 then 1 end) as 본인아님, 
        COUNT(CASE WHEN el.status=13 then 1 end) as 쿠키중복, 
        COUNT(CASE WHEN el.status=99 then 1 end) as 확인");
        $builder->join('event_information as info', "info.seq = el.event_seq", 'left');
        $builder->join('event_advertiser as adv', "info.advertiser = adv.seq AND adv.is_stop = 0", 'left');
        $builder->join('event_media as med', 'info.media = med.seq', 'left');

        if(!auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){
            $builder->join('companies_users as cu', "adv.company_seq = cu.company_id", 'left');
            $builder->where('cu.user_id', auth()->user()->id);
        }

        $builder->where('el.is_deleted', 0);
        $builder->where('el.reg_date >=', $data['sdate']);
        $builder->where('el.reg_date <', date('Y-m-d', strtotime($data['edate'].' +1 day')));

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('adv.name', $data['stx']);
            $builder->orLike('info.seq', $data['stx']);
            $builder->orLike('med.media', $data['stx']);
            $builder->orLike('info.description', $data['stx']);
            $builder->orLike('el.name', $data['stx']);
            $builder->orLike('el.branch', $data['stx']);
            $builder->orLike('el.add1', $data['stx']);
            $builder->orLike('el.add2', $data['stx']);
            $builder->orLike('el.add3', $data['stx']);
            $builder->orLike('el.add4', $data['stx']);
            $builder->orLike('el.add5', $data['stx']);
            $builder->groupEnd();
        }

        if(!empty($data['company'])){
            $company = explode("|",$data['company']);
            $index = array_search('리스타', $company);
            if($index !== false){
                $company[$index] = '';
            }
            $builder->whereIn('info.partner_company', $company);
        }

        if(!empty($data['advertiser'])){
            $builder->whereIn('adv.name', explode("|",$data['advertiser']));
        }

        if(!empty($data['media'])){
            $builder->whereIn('med.media', explode("|",$data['media']));
        }

        if(!empty($data['description'])){
            $builder->whereIn('info.description', explode("|",$data['description']));
        }
        $result = $builder->get()->getRowArray();

        return $result;
    }

    public function getMemo($data) 
    {
        $builder = $this->zenith->table('event_leads_memo');
        $builder->select("*");
        $builder->where("leads_seq", $data['seq']);
        $builder->orderBy("reg_date", "desc");
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function addMemo($data) {
        $builder = $this->zenith->table('event_leads');
        $builder->select("event_seq");
        $builder->where("seq", $data['leads_seq']);
        $row = $builder->get()->getRowArray();
        $data['event_seq'] = $row['event_seq'];
        if(!$data['event_seq']) return null;
        $data['reg_date'] = date('Y-m-d H:i:s');
        $data['username'] = auth()->user()->username;
        $this->zenith->transStart();
        $builder = $this->zenith->table('event_leads_memo');
        $builder->set('leads_seq', $data['leads_seq']);
        $builder->set('event_seq', $data['event_seq']);
        $builder->set('username', $data['username']);
        $builder->set('memo', $data['memo']);
        $builder->set('reg_date', $data['reg_date']);
        $result = $builder->insert();
        $insertId = $this->zenith->insertID();
        $this->zenith->transComplete();

        
        $result = [
            'result' => $result,
            'data' => [$data]
        ];
        return $result;
    }

    public function setStatus($data) {
        if(!$data['seq']) return null;
        $this->zenith->transStart();
        $builder = $this->zenith->table('event_leads');
        $builder->set('status', $data['status']);
        $builder->where('seq', $data['seq']);
        $result = $builder->update();
        $this->zenith->transComplete();
        
        $result = [
            'result' => $result,
            'data' => $data
        ];
        return $result;
    }
}
