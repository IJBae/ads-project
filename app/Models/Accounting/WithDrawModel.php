<?php

namespace App\Models\Accounting;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time;

class WithDrawModel extends Model
{
    protected $zenith, $gwdb;

    public function __construct()
    {
        $this->zenith = \Config\Database::connect();
        $this->gwdb = \Config\Database::connect('gwdb');
    }

    public function typeList()
    {
        $builder = $this->zenith->table("deposit");
        $builder->select("type");
        $builder->groupBy("type");
        $result = $builder->get()->getResultArray();
        return json_encode($result);
    }

    public function userList()
    {
        $builder = $this->zenith->table("company_account AS ca");
        $builder->join("users AS u", "u.id = ca.user_id");
        $builder->select("u.nickname, u.id");
        $builder->groupBy("user_id");
        $result = $builder->get()->getResultArray();
        return json_encode($result);
    }

    public function getList($data)
    {
        $builder = $this->zenith->table("deposit AS d");
        $builder->select("d.seq, d.reg_date, u.nickname, d.type, a.company_name, a.bank, a.account, d.detail, d.total_price, d.memo, if(d.date IS NULL,'완료','진행중') status, d.date, d.doc_no");
        $builder->join("company_account AS ca", "d.seq=ca.deposit_seq", "left");
        $builder->join("account AS a", "d.account_seq=a.seq AND ca.user_id=a.user_id", "left");
        $builder->join("users AS u", "a.user_id=u.id", "left");
        $builder->where("d.updated_at >= ", $data["sdate"] . " 00:00:00");
        $builder->where("d.updated_at <= ", $data["edate"] . " 23:59:59");

        if ($data["stx_opt"] && $data["stx"]) {
            if ($data["stx_opt"] == "company") {
                $builder->like("a.company_name", $data["stx"]);
            } else if ($data["stx_opt"] == "detail") {
                $builder->like("d.detail", $data["stx"]);
            }
        }

        if($data["userid"]){
            $builder->where("u.id", $data["userid"]);
        }

        if($data["type"]){
            $builder->where("d.type", $data["type"]);
        }

        if($data["complete"]){
            if($data["complete"]==1){
                $builder->where("d.date IS NOT NULL", NULL, FALSE);
            }else if($data["complete"]==0){
                $builder->where("d.date IS NULL", NULL, FALSE);
            }
        }

        $deposit_data = $builder->get()->getResultArray();
        $doc_no_arr=array();

        for($i = 0; $i < count($deposit_data); $i++){
            $doc_no_arr[] = $deposit_data[$i]["doc_no"];
        }

        $gwbuilder = $this->gwdb->table("viberc_gw AS a");
        $gwbuilder->join("viberc_gw_contents AS b","a.doc_id = b.doc_id","left");
        $gwbuilder->join("viberc_gw_line AS c","a.doc_id = c.doc_id","left");
        $gwbuilder->join("viberc_gw_interlock AS d","a.doc_id = d.doc_id","left");
        $gwbuilder->where("a.form_id",37);
        $gwbuilder->whereIn("doc_no", $doc_no_arr);
        $gwbuilder->groupBy("a.doc_id");
        $gwbuilder->orderBy("a.rep_dt","DESC");
        $gwbuilder->orderBy("c.doc_line_m_seq","ASC");
        $gw_result = $gwbuilder->get()->getResultArray();

        $gw_data=array();

        foreach($gw_result as $row){
            $row['doc_contents'] = str_replace('&nbsp;', ' ', $row['doc_contents']);
            $row['account_no'] = preg_match_all('@(<span[^>]*>.*(지급은행|계좌번호).+?\:\s+?(.+)[^<]*<\/span>)@m', $row['doc_contents'], $matches);
            $row['account_name'] = $matches[3][0] ?? '';
            $row['account_no'] = $matches[3][1] ?? '';
            $gw_data[] = $row;
        }

        function searchForDocNo($id, $array) {
            foreach ($array as $key => $val) {
                if ($val['doc_no'] === $id) {
                    return $key;
                }
            }
            return null;
        }

        for($i=0; $i<count($deposit_data); $i++) {
            $gwKey = searchForDocNo($deposit_data[$i]["doc_no"],$gw_data);
            if($gwKey >= 0 && $gwKey != "") {
                $doc_id = $gw_data[$gwKey]['doc_id'];
                $doc_sts = $gw_data[$gwKey]['doc_sts'];
                $form_id = $gw_data[$gwKey]['form_id'];
                $doc_title = $gw_data[$gwKey]['doc_title'];
                $user_nm = preg_replace('/(바이브알씨_|씨랩_)/', '', $gw_data[$gwKey]['user_nm']);
                $grade_nm = $gw_data[$gwKey]['grade_nm'];
                switch($doc_sts) {
                    case '10' : $status = $doc_title.'임시저장'; break;
                    case '20' : $status = $doc_title.'기안'; break;
                    case '30' : $status = $grade_nm.'('.$user_nm.') 진행중 '.$doc_title.'">진행중'; break;
                    case '90' : $status = 'http://gw.carelabs.co.kr/eap/ea/docpop/EAAppDocViewPop.do?doc_id='.$doc_id.'&form_id='.$form_id; break;
                    case '100' : $status = $doc_title.'반려'; break;
                    default : $status = '문서 번호 없음'; break;
                }
                $deposit_data[$i]["gwstatus"] = $status;
            }
        }

        return json_encode($deposit_data);
    }

    public function accountList($data){
        $builder = $this->zenith->table("account AS a");
        $builder->join("users AS u","a.user_id = u.id","left");
        $builder->select("a.*,u.nickname");
        $builder->where("a.reg_date >= ", $data["sdate"] . " 00:00:00");
        $builder->where("a.reg_date <= ", $data["edate"] . " 23:59:59");

        if ($data["stx_opt"] && $data["stx"]) {
            if ($data["stx_opt"] == "company") {
                $builder->like("a.company_name", $data["stx"]);
            } else if ($data["stx_opt"] == "no") {
                $builder->like("a.account", $data["stx"]);
            }
        }

        if($data["userid"]){
            $builder->where("u.id",$data["userid"]);
        }

        $builder->orderBy("reg_date","DESC");
        $result = $builder->get()->getResultArray();

        return json_encode($result);
    }

}