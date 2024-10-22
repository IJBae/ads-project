<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Controllers\HumanResource\HumanResourceController;
use App\Libraries\slack_api\SlackChat;
use App\Services\LoggerService;
use App\ThirdParty\googleclient\GoogleCalendar; // GoogleCalendar 클래스 추가


class CheckDayOff extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Slack';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'todayDayOff';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = '당일 연차/시차 슬랙 전송';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'command:dayoff [arguments] [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    private $sendList = ['개발팀', '디자인실', '운영실'];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $sendType = "";
        if(isset($params[0]))
            $sendType = $params[0];
        $Tdate = date('Ymd'); //기본값은 오늘
        if(isset($params[1]) && preg_match('/^\d{8}$/', $params[1])) $Tdate = $params[1];
        $dayOff = new HumanResourceController();
        $lists = $dayOff->getTodayDayOff($Tdate);
        $slack = new SlackChat();
        $googleCalendar = new GoogleCalendar(); // GoogleCalendar 인스턴스 생성
        $msg = [];
        $now = date('H');
        foreach($lists as $row) {
            if($now == "10" || $sendType == 'today') { //업무시작시간에 당일 연/시차 전체 전송
                if(date('Ymd', strtotime($row['start'])) != $Tdate) continue;
            } else { //그 외 매시간 새로 등록된 당일 연/시차 전송
                if(date('Ymd', strtotime($row['start'])) != $Tdate || strtotime($row['datetime']) <= strtotime('-1 hour')) continue;
            }
            if($row['type'] == '시차') {
                $start = date('Y년m월d일 H시부터', strtotime($row['start']));
                $end = date('H시까지', strtotime($row['end']));
                $term = "{$start} {$end}({$row['term']}시간)";
                $remain = "잔여:{$row['total_remain']}시간";
                if(date('H', strtotime($row['start'])) == 10)
                    $term = date('Y년m월d일 H시', strtotime($row['end']))." 출근({$row['term']}시간 사용)";
                if(date('H', strtotime($row['end'])) == 19)
                    $term = date('Y년m월d일 H시', strtotime($row['start']))." 퇴근({$row['term']}시간 사용)";
                if($row['total_remain'] <= 0) {
                    $data = [
                        'channel' => $row['name'],
                        'text' => "{$row['name']}님, 시차 잔여량이 부족합니다. [{$remain}]",
                    ];
                    $slack->sendMessage($data);
                }
            } else {
                $start = date('Y년m월d일부터', strtotime($row['start']));
                $end = date('Y년m월d일까지', strtotime($row['end']));
                $remain = "사용:{$row['total_used']}일/잔여:{$row['total_remain']}일";
                $term = "{$start} {$end}({$row['term']}일)";
                if($row['start'] == $row['end'])
                    $term = date('Y년m월d일', strtotime($row['start']));
            }
            // Google Calendar 이벤트 생성
            $eventData = [
                'summary' => "[{$row['type']}]{$row['name']}({$row['team']})",
                'description' => "{$term}",
            ];
            if ($row['type'] == '연차' || $row['type'] == '출산휴가' || $row['type'] == '예비군훈련') {
                $eventData['start'] = [
                    'date' => date('Y-m-d', strtotime($row['start'])),
                ];
                $eventData['end'] = [
                    'date' => date('Y-m-d', strtotime($row['end'] . ' +1 day')),
                ];
                $eventData['colorId'] = '11';
            } else {
                $eventData['start'] = [
                    'dateTime' => date('Y-m-d\TH:00:00P', strtotime($row['start'])),
                ];
                $eventData['end'] = [
                    'dateTime' => date('Y-m-d\TH:00:00P', strtotime($row['end'])),
                ];
                //type에 생일휴가 또는 오후가 포함되어 있을 경우 start, end를 각각 당일 15시, 19시로 강제로 조정
                if(strpos($row['type'], '생일휴가') !== false || strpos($row['type'], '오후') !== false) {
                    $eventData['start']['dateTime'] = date('Y-m-d\T15:00:00P', strtotime($row['start']));
                    $eventData['end']['dateTime'] = date('Y-m-d\T19:00:00P', strtotime($row['end']));
                }
                //type에 오전 포함되어 있을 경우 start, end를 각각 당일 10시, 15시로 강제로 조정
                if(strpos($row['type'], '오전') !== false) {
                    $eventData['start']['dateTime'] = date('Y-m-d\T10:00:00P', strtotime($row['start']));
                    $eventData['end']['dateTime'] = date('Y-m-d\T15:00:00P', strtotime($row['end']));
                }
                $eventData['colorId'] = '5';
            }
            $googleCalendar->createEvent($eventData); // 이벤트 생성 메서드 호출
            $slackMsg = [
                "type" => "section",
                "text" => [
                    "type" => "mrkdwn",
                    "text" => "*{$row['name']}* _({$row['team']})_ [{$remain}]\n`[{$row['type']}]` {$term}"
                ]
            ];
            $msg['연차공유'][] = ["type"=>"divider"];
            $msg['연차공유'][] = $slackMsg;
            $msg['연차알림'][] = ["type"=>"divider"];
            $msg['연차알림'][] = $slackMsg;
            foreach($this->sendList as $list) {
                if($list != $row['team'] && $list != $row['division']) continue;
                $msg[$list][] = ["type"=>"divider"];
                $msg[$list][] = $slackMsg;
            }
        }
        if(!count($msg)) return;
        if($sendType == 'debug') dd($msg);
        foreach($msg as $channel => $row) {
            if(preg_match('/.+(팀|실)/', $channel))
                $channel = $channel."_연차공유";
            $data = [
                'channel' => $channel,
                'text' => '',
                'blocks' => json_encode($row,JSON_UNESCAPED_UNICODE)
            ];
            // d($data);
            $response = $slack->sendMessage($data);
        }

        //로그 기록
        $data = [
            'type' => 'tasks',
            'command' => $this->name
        ];

        $logger = new LoggerService();
        $logger->insertLog($data);
    }
}
