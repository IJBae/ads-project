<?php

namespace App\Models\HumanResource;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time;
use Mpdf\Tag\P;

class HumanResourceModel extends Model
{
    protected $zenith, $gwdb;

    public function __construct()
    {
        $this->zenith = \Config\Database::connect();
        $this->gwdb = \Config\Database::connect('gwdb');
    }

    public function getMemberList()
    {
        $builder = $this->zenith->table('users as usr');
        $builder->select('usr.*, ai.secret, up.*');
        $builder->join('auth_identities as ai', 'usr.id = ai.user_id AND ai.type = "email_password"', 'left');
        $builder->join('users_department as up', 'usr.id = up.user_id');
        $builder->groupBy('usr.id');
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function getUserByEmail($email)
    {
        $builder = $this->zenith->table('users as usr');
        $builder->select('usr.*, ai.secret, up.*');
        $builder->join('auth_identities as ai', 'usr.id = ai.user_id AND ai.type = "email_password"', 'left');
        $builder->join('users_department as up', 'usr.id = up.user_id', 'left');
        $builder->where('ai.secret', $email);
        $builder->groupBy('usr.id');
        $result = $builder->get()->getRowArray();

        return $result;
    }

    public function updateUserByEmail($data)
    {
        $usr = $this->getUserByEmail($data['email']);
        if (is_null($usr)) return;
        if (is_null($usr)) return;
        $name = $data['name'];
        $data = [
            'user_id' => $usr['id'],
            'division' => $data['division'],
            'team' => $data['team'],
            'position' => $data['position']
        ];
        $builder = $this->zenith->table('users_department');
        $builder->setData($data)->upsert();
        $builder = $this->zenith->table('users');
        $result = $builder->set('nickname', $name)->where('id', $usr['id'])->update();

        return $result;
    }

    public function getHourTicketUse()
    {
        $builder = $this->zenith->table('chainsaw_old.hourticket_use AS hu');
        $builder->select('mem.mb_email AS email, mem.mb_name AS name, mem.hourticket_issue, mem.hourticket_use, (mem.hourticket_issue - mem.hourticket_use) as remain, hu.*');
        $builder->join('chainsaw_old.g5_member AS mem', 'hu.mb_id = mem.mb_id');
        $builder->where('hu.date >= DATE_SUB(NOW(), INTERVAL 2 DAY)');
        $builder->orderBy("hu.seq", "DESC");
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function gwData()
    {
        $builder = $this->zenith->table("users AS u");
        $builder->join("users_department AS ud", "u.id=ud.user_id", "left");
        $builder->join("users_groupware AS ug", "u.id=ug.user_id", "left");
        $builder->select('*');
        $builder->where("u.nickname IS NOT NULL", NULL, FALSE);

        $builder->groupStart();
        $builder->where("ud.user_id IS NULL", NULL, FALSE);
        $builder->orWhere("ug.gw_user IS NULL", NULL, FALSE);
        $builder->groupEnd();

        $dvList = $builder->get()->getNumRows();

        if ($dvList > 0) {
            $builder = $this->gwdb->table("v_user_info");
            $builder->select("emp_seq, email_addr, SUBSTRING_INDEX(emp_name, '_', -1) AS emp_name, path_name ");
            $builder->where("work_status <> ", "001");
            $builder->like("path_name", "DM 사업부%");
            $builder->orderBy("CAST(emp_seq AS int)");
            $gwDvList = $builder->get()->getResultArray();

            $not_members = [2315, 2418, 2416, 2419, 2508, 2123, 2127, 2411];

            for ($i = 0; $i < count($gwDvList); $i++) {
                if (!in_array($gwDvList[$i]['emp_seq'], $not_members)) {
                    $builder = $this->zenith->table("users");
                    $builder->select("id");
                    $builder->like("nickname", "{$gwDvList[$i]['emp_name']}");
                    $selectUserID = $builder->get()->getResultArray();

                    // gw_user insert
                    $builder = $this->zenith->table("users_groupware");
                    $builder->select("*");
                    $builder->where("user_id", $selectUserID[0]['id']);
                    $ugselect = $builder->get()->getNumRows();

                    if($ugselect==0) {
                        $this->zenith->transStart();
                        $builder = $this->zenith->table("users_groupware");
                        $builder->set("user_id", $selectUserID[0]["id"]);
                        $builder->set("gw_user", $gwDvList[$i]['emp_seq']);
                        $builder->insert();
                        $this->zenith->transComplete();
                    }

                    $builder = $this->zenith->table("users_department");
                    $builder->select("*");
                    $builder->where("user_id", $selectUserID[0]['id']);
                    $gdselect = $builder->get()->getNumRows();

                    if($gdselect==0) {
                        // users_department insert
                        $path = explode('|', $gwDvList[$i]['path_name'] ?? '');
                        $this->zenith->transStart();
                        $builder = $this->zenith->table("users_department");
                        $builder->set("user_id", $selectUserID[0]["id"]);
                        $builder->set("division", $path[1] ?? "-");
                        $builder->set("team", $path[2] ?? "-");
                        $builder->set("position", "-");
                        $builder->insert();
                        $this->zenith->transComplete();
                    }
                }
            }
        }

        $builder = $this->zenith->table("hourticket_issue");
        $builder->select("GROUP_CONCAT(doc_no) AS doc_nos");
        $builder->where("rep_datetime >= DATE_SUB(NOW(), INTERVAL 1 DAY)", null, false);
        $recentDocList = $builder->get()->getResultArray();
        $doc_no = explode(",", $recentDocList[0]['doc_nos'] ?? "");

        $builder = $this->gwdb->table("viberc_gw AS gw");
        $builder->join("viberc_gw_contents AS vgc", "gw.doc_id=vgc.doc_id", "left");
        $builder->join("viberc_gw_interlock AS vgi", "gw.doc_id=vgi.doc_id", "left");
        $builder->select("gw.*, vgc.doc_contents, vgi.doc_xml");
        $builder->where("gw.form_id", "18");
        $builder->where("gw.doc_sts", "90");
        $builder->where("gw.end_dt >= DATE_SUB(NOW(), INTERVAL 2 DAY)", null, false);

        if (count($doc_no)) {
            $builder->whereNotIn("gw.doc_no", $doc_no);
        }

        $builder->groupBy("gw.doc_no");
        $builder->orderBy("gw.rep_dt", "DESC");
        $gwDocList = $builder->get()->getResultArray();
        $gwDocCnt = $builder->get()->getNumRows();

        if ($gwDocCnt > 0) {
            for ($i = 0; $i < count($gwDocList); $i++) {
                $gwDocList[$i]["doc_contents"] = str_replace('&nbsp;', ' ', $gwDocList[$i]["doc_contents"]);
                $gwDocList[$i]['is_break_hour'] = preg_match('/시간차/m', $gwDocList[$i]['doc_contents']);
                preg_match_all('@(연차차감)\s+?\:\s+?([0-9\.]+)@m', $gwDocList[$i]['doc_xml'], $matches);
                $gwDocList[$i]['break_balance'] = $matches[2][0];
                if ($gwDocList[$i]['is_break_hour']) {
                    if (!isset($gwDocList[$i]['user_id'])) continue;
                    $data[] = $gwDocList[$i];
                }
            }

            if (count($data)) {
                echo '<pre>' . print_r($data, 1) . '</pre>';
                for ($i = 0; $i < count($data); $i++) {
                    $builder = $this->zenith->table("users AS u");
                    $builder->join("users_groupware AS ug","u.id=ug.user_id","left");
                    $builder->select("id");
                    $builder->where("ug.gw_user", $data[$i]['user_id']);
                    $issueInsertData = $builder->get()->getResultArray();
                    if (isset($issueInsertData[0]['id'])) {
                        $this->zenith->transStart();
                        $builder = $this->zenith->table("hourticket_issue");
                        $setData=[
                            "gw_user"=> $data[$i]['user_id'],
                            "doc_id"=> $data[$i]['doc_id'],
                            "doc_no"=> $data[$i]['doc_no'],
                            "doc_title"=> $data[$i]['doc_title'],
                            "rep_datetime"=> $data[$i]['rep_dt'],
                            "user_id"=> $issueInsertData[0]['id'],
                            "break_balance"=> $data[$i]['break_balance'],
                            "hour_ticket"=>$data[$i]['is_break_hour']*8
                        ];

                        $builder->set($setData);
                        $sql = $builder->getCompiledInsert();
                        $result = $builder->set($setData)->insert();
                        $this->zenith->transComplete();

                        echo "result:".$result;
                        echo "sql:".$sql;
                    }
                }
            }
        }
    }

    public function getGwData($data)
    {
        $builder = $this->gwdb->table("viberc_gw as gw");
        $builder->select("gw.*, vgc.doc_contents, vgi.doc_xml");
        $builder->join("viberc_gw_contents as vgc", "gw.doc_id=vgc.doc_id", "left");
        $builder->join("viberc_gw_interlock as vgi", "gw.doc_id=vgi.doc_id", "left");
        $builder->where("gw.form_id", 18);
        $builder->where("doc_no", $data["doc_no"]);
        $builder->groupBy("gw.doc_id");
        $builder->orderBy("gw.rep_dt", "DESC");
        $gw_result = $builder->get()->getResultArray();

        $builder = $this->zenith->table("users AS u");
        $builder->select("u.id, u.nickname");
        $builder->join("users_groupware AS ug", "u.id = ug.user_id", "left");
        $builder->where("ug.gw_user", $gw_result[0]["user_id"]);
        $users_result = $builder->get()->getResultArray();

        $result = array();
        $result["gw_user"] = $gw_result[0]["user_id"];
        $result["user_id"] = $users_result[0]["id"];
        $result["nickname"] = $users_result[0]["nickname"];
        $result["rep_datetime"] = $gw_result[0]["rep_dt"];
        $result["doc_id"] = $gw_result[0]["doc_id"];
        $result["doc_title"] = $gw_result[0]["doc_title"];
//        $result["doc_contents"]=str_replace('&nbsp;', ' ', $gw_result[0]['doc_contents']);
//        $result["break_hour"]=preg_match('/시간차/m', $gw_result[0]['doc_contents']);
        $_balance = preg_match_all('@(연차차감)\s+?\:\s+?([0-9\.]+)@m', $gw_result[0]['doc_xml'], $matches);
        $result['break_balance'] = $matches[2][0];
        return $result;
    }

    public function issueProc($data)
    {
        if ($data["seq"] != "") {
            $this->zenith->transStart();
            $builder = $this->zenith->table('hourticket_issue');
            $builder->set('apr_user_id', auth()->user()->id);
            $builder->set('apr_datetime', Time::now());
            $builder->where('seq', $data['seq']);
            $builder->update();
            $result = $builder->get()->getResultArray();
            $this->zenith->transComplete();
        } else {
            $this->zenith->transStart();
            $builder = $this->zenith->table("hourticket_issue");
            $builder->set("gw_user", $data["gw_user"]);
            $builder->set("user_id", $data["user_id"]);
            $builder->set("doc_id", $data["doc_id"]);
            $builder->set("doc_no", $data["doc_no"]);
            $builder->set("doc_title", $data["doc_title"]);
            $builder->set("break_balance", $data["break_balance"]);
            $builder->set("rep_datetime", $data["rep_datetime"]);
            $builder->set("hour_ticket", $data["hourticket"]);
            $builder->set("apr_user_id", auth()->user()->id);
            $builder->set("apr_datetime", Time::now());
            $result = $builder->insert();
            $this->zenith->transComplete();
        }

        return $result;
    }


    public function hourTicketConfirm($data)
    {
        $this->zenith->transStart();

        if ($data["type"] === "use") {
            $builder = $this->zenith->table('hourticket_use');
            if ($data["method"] === "agree") {
                $builder->set('apr_user_id', auth()->user()->id);
                $builder->set('apr_datetime', Time::now());
                $builder->where('seq', $data['seq']);
                $builder->update();
            } elseif ($data["method"] === "reject") {
                $builder->where('seq', $data['seq']);
                $builder->delete();
            }
        } else if ($data["type"] === "issue") {
            $builder = $this->zenith->table('hourticket_issue');
            if ($data["method"] === "agree") {
                $builder->set('apr_user_id', auth()->user()->id);
                $builder->set('apr_datetime', Time::now());
                $builder->where('doc_id', $data['seq']);
                $builder->update();
            } elseif ($data["method"] === "reject") {
                $builder->where('doc_id', $data['seq']);
                $builder->delete();
            }
        }

        $result = $this->zenith->transComplete();
        return $result;
    }

    public function hourTicketReg($data)
    {
        $this->zenith->transStart();
        $builder = $this->zenith->table("hourticket_use");
        $builder->set("user_id", auth()->user()->id);
        $builder->set("hour_ticket", $data["hour_ticket"]);
        $builder->set("date", $data["date"]);
        $builder->set("time", $data["time"]);
        $builder->set("reg_datetime", Time::now());
        $builder->insert();
        $result = $this->zenith->transComplete();

        return $result;
    }

    public function getHourTicketAll($data)
    {
        $builder = $this->zenith->table("users AS u");
        $builder->select("u.id, u.nickname, 
            IFNULL(hi.hour_ticket,0) AS hourticket_issue, 
            IFNULL(hu.hour_ticket,0) AS hourticket_use, 
            IFNULL(issued, 0 ) AS issued, 
            IFNULL(used, 0 ) AS used, 
            (IFNULL(issued, 0 )- IFNULL(used, 0 )) AS total");
        $builder->join("(select user_id, gw_user, hour_ticket, SUM(hour_ticket) AS issued FROM hourticket_issue 
					WHERE apr_datetime IS NOT NULL AND DATE(rep_datetime) BETWEEN '{$data["sdate"]}' AND '{$data["edate"]}' GROUP BY user_id) AS hi", "u.id = hi.user_id", "left");
        $builder->join("(select user_id, hour_ticket, SUM(hour_ticket) as used from hourticket_use
					WHERE apr_datetime IS NOT NULL AND date BETWEEN '{$data["sdate"]}' AND '{$data["edate"]}' GROUP BY user_id) AS hu", "u.id = hu.user_id", "left");
        $builder->join("users_groupware AS ug", "u.id = ug.user_id and AND hi.gw_user = ug.gw_user", "left");

        $builder->where("u.nickname IS NOT NULL", NULL, FALSE);

        if (!auth()->user()->hasPermission('humanresource.management')) {
            $builder->where("u.id", auth()->user()->id);
        }

        if ($data["stx"]) {
            $builder->like("u.nickname", $data["stx"]);
        }

        $builder->groupBy("u.id");
        $builder->orderBy("u.id", "ASC");
        $builder->orderBy("ug.gw_user", "ASC");
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function getHourTicketUseNew($data)
    {
        $builder = $this->zenith->table('hourticket_use AS hu');
        $builder->select('u.nickname, hu.hour_ticket, hu.date, 
         CONCAT(
                DATE_FORMAT(hu.time, \'%H시%i\'), 
                \'분 ~ \', 
                DATE_FORMAT(DATE_ADD(hu.time, INTERVAL hu.hour_ticket HOUR), \'%H시%i\'), 
                \'분\'
            ) AS time,
         if(apr_user_id, \'완료\', hu.seq) AS seq');
        $builder->join('users AS u', 'hu.user_id=u.id', "left");
        if ($data["sdate"] && $data["edate"]) {
            $builder->where("hu.reg_datetime >=", $data["sdate"] . " 00:00:00");
            $builder->where("hu.reg_datetime <=", $data["edate"] . " 23:59:59");
        }
        if ($data["stx"]) {
            $builder->Like("u.nickname", $data["stx"]);
        }

        $builder->orderBy("hu.reg_datetime", "DESC");
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function getHourTicketIssue($data)
    {
        $builder = $this->zenith->table("hourticket_issue AS hi");
        $builder->select("u.nickname, DATE_FORMAT(hi.rep_datetime,'%Y-%m-%d') as rep_datetime, hi.doc_id, hi.doc_no, hi.break_balance, hi.hour_ticket, if(hi.apr_user_id, '완료', '진행중') apr_user_id");
        $builder->join("users AS u", "hi.user_id = u.id", "left");
        $builder->join("users_groupware AS ug", "u.id = ug.user_id and AND hi.gw_user = ug.gw_user", "left");
        if ($data["sdate"] && $data["edate"]) {
            $builder->where("hi.rep_datetime >=", $data["sdate"] . " 00:00:00");
            $builder->where("hi.rep_datetime <=", $data["edate"] . " 23:59:59");
        }
        if ($data["stx"]) {
            $builder->groupStart();
            $builder->like("doc_no", $data["stx"]);
            $builder->orLike("u.nickname", $data["stx"]);
            $builder->groupEnd();
        }

        if (!auth()->user()->hasPermission('humanresource.management')) {
            $builder->where("hi.user_id", auth()->user()->id);
        }
        $builder->orderBy('hi.rep_datetime', 'DESC');
        $result = $builder->get()->getResultArray();

        return $result;
    }
}
