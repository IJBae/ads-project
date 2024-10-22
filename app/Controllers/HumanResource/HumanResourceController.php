<?php

namespace App\Controllers\HumanResource;

use App\Controllers\BaseController;
use App\Models\HumanResource\HumanResourceModel;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\Douzone\Douzone;
use App\Libraries\Douzone\DouzoneModel;

class HumanResourceController extends BaseController
{
    use ResponseTrait;

    protected $hr;

    public function __construct()
    {
        $this->hr = model(HumanResourceModel::class);
    }

    public function humanResource()
    {
        return view('humanResource/humanresource');
    }

    private function getDayOff()
    { //연차
        $douzone = new Douzone();
        $list = $douzone->getDayOff();


        return $list;
    }

    public function getMemberList()
    {
        $lists = $this->hr->getMemberList();
        foreach($lists as $row) $data[] = [$row['nickname'],$row['division'],$row['secret']];
        foreach ($lists as $row) $data[] = [$row['nickname'], $row['division'], $row['secret']];
        dd($data);
        return $lists;
    }

    public function updateUsersByDouzone()
    {
        $douzone = new Douzone();
        $list = $douzone->getMemberList();
        $total = count($list);
        if (!$total) return null;
        if (!$total) return null;
        $i = 1;
        foreach ($list as $row) {
            foreach ($list as $row) {
                $result = $this->hr->updateUserByEmail($row);
                if ($result) $i++;
                if ($result) $i++;
            }
            if ($result == $i) return true;
            if ($result == $i) return true;
            return false;
        }
    }

    private function getHourTicketUse()
    {
        $hourticket = $this->hr->getHourTicketUse();

        return $hourticket;
    }

    public function getTodayDayOff($date = null)
    {
        $date = $date ? date('Y-m-d', strtotime($date)) : date('Y-m-d');
        $list = $this->getDayOff();
        $data = [];
        foreach ($list as $row) {
            if (isset($row['start']) && date('Y-m-d', strtotime($row['start'])) >= $date) {
                $data['day'][] = $row;
            }
        }
        $list = $this->getHourTicketUse($date);
        foreach ($list as $row) {
            if (isset($row['date']) && date('Y-m-d', strtotime($row['date'])) >= $date) {
                $data['hour'][] = $row;
            }
        }
        $result = $this->setMessageData($data);
        return $result;
    }

    public function gwData(){
        $result = $this->hr->gwData();

        return $result;
    }

    public function getGwData(){
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post') {
            $input = $this->request->getBody();
            $data = json_decode($input, true);
            $result = $this->hr->getGwData($data);

            return json_encode($result);
        } else {
            return $this->fail("잘못된 요청");
        }
    }

    public function issueProc()
    {
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post') {
            $data = $this->request->getRawInput();
            $result = $this->hr->issueProc($data);

            return json_encode($result);
        } else {
            return $this->fail("잘못된 요청");
        }
    }

    public function hourTicketConfirm()
    {
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post') {
            $input = $this->request->getBody();
            $data = json_decode($input, true);
            $result = $this->hr->hourTicketConfirm($data);

            return json_encode($result);
        } else {
            return $this->fail("잘못된 요청");
        }
    }

    public function hourTicketReg()
    {
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post') {
            $input = $this->request->getBody();
            $data = json_decode($input, true);
            $result = $this->hr->HourTicketReg($data);

            return json_encode($result);
        } else {
            return $this->fail("잘못된 요청");
        }
    }

    public function getHourTicketAll()
    {
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post') {
            $input = $this->request->getBody();
            $data = json_decode($input, true);
            $result = $this->hr->getHourTicketAll($data);

            return json_encode($result);
        } else {
            return $this->fail("잘못된 요청");
        }
    }

    public function getHourTicketIssue()
    {
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post') {
            $input = $this->request->getBody();
            $data = json_decode($input, true);
            $result = $this->hr->getHourTicketIssue($data);

            return json_encode($result);
        } else {
            return $this->fail("잘못된 요청");
        }
    }

    public function getHourTicketUseNew()
    {
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post') {
            $input = $this->request->getBody();
            $data = json_decode($input, true);
            $result = $this->hr->getHourTicketUseNew($data);

            return json_encode($result);
        } else {
            return $this->fail("잘못된 요청");
        }

    }

    private function setMessageData($lists)
    {
        $data = [];
        foreach ($lists as $type => $list) {
            foreach ($list as $row) {
                $user = $this->hr->getUserByEmail($row['email']);
                if (empty($user)) continue;
                $type = isset($row['type']) ? $row['type'] : '시차';
                $start = isset($row['start']) ? date('Y-m-d', strtotime($row['start'])) : date('Y-m-d H:m', strtotime($row['date'] . " " . $row['time']));
                $end = isset($row['end']) ? date('Y-m-d', strtotime($row['end'])) : date('Y-m-d H:m', strtotime($row['date'] . " " . $row['time'] . " +{$row['hour_ticket']} hours"));
                $term = isset($row['used']) ? $row['used'] : $row['hour_ticket'];
                $total_remain = isset($row['total']['remain']) ? $row['total']['remain'] : $row['remain'];
                $total_used = isset($row['total']['used']) ? $row['total']['used'] : '';
                $datetime = isset($row['reg_datetime']) ? $row['reg_datetime'] : $row['rep_dt'];
                $data[] = [
                    'type' => $type,
                    'name' => $user['nickname'],
                    'division' => $user['division'],
                    'team' => $user['team'],
                    'position' => $user['position'],
                    'term' => $term,
                    'total_used' => $total_used,
                    'total_remain' => $total_remain,
                    'start' => $start,
                    'end' => $end,
                    'datetime' => $datetime
                ];
            }
        }
        return $data;
    }
}
