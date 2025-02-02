<?php

namespace App\Controllers\Advertisement;

use CodeIgniter\CLI\CLI;
use App\ThirdParty\moment_api\ZenithKM;
use CodeIgniter\Controller;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use DateInterval;
use DatePeriod;

class KakaoMoment extends Controller
{
    private $chainsaw;
	private $run_proc = [];

    public function __construct(...$param)
    {
        $this->chainsaw = new ZenithKM();       
    }

	public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        $uname = php_uname();
        if(stripos($uname, 'windows') === false) {
            @exec("ps ax | grep -i kmapi | grep -v grep", $exec);
            if($exec) {
                foreach($exec as $v) $proc[] = preg_replace('/^.+php\skmapi\s(.+)$/', '$1', $v);
                foreach($proc as $v) {
                    $method = preg_replace('/^([a-z]+)\s.+$/i', '$1', $v);
                    $_params = preg_replace('/^([a-z]+)\s(.+)$/i', '$2', str_replace('\\', '', $v));
                    $params = explode(' ', $_params);
                    $this->run_proc = [
                        'method' => $method,
                        'params' => $params
                    ];
                }
            }
        }
    }
    public function oauth() {
        return $this->chainsaw->oauth();
    }
    //토큰 업데이트
    public function refresh_token()
    { 
        CLI::clearScreen();
        CLI::write("토큰 업데이트를 진행합니다.", "light_red");
        $this->chainsaw->refresh_token();
        CLI::write("토큰 업데이트 완료", "yellow");
    }

    //전체 광고계정 업데이트
    public function updateAdAccounts()
    {
        CLI::clearScreen();
        CLI::write("전체 광고계정 업데이트를 진행합니다.", "light_red");
        $this->chainsaw->updateAdAccounts();
        CLI::write("전체 광고계정 업데이트 완료", "yellow");
    }

    //캠페인 데이터 업데이트
    public function updateCampaigns()
    { 
        CLI::clearScreen();
        CLI::write("캠페인 업데이트를 진행합니다.", "light_red");
        $this->chainsaw->updateCampaigns();
        CLI::write("캠페인 업데이트 완료", "yellow");
    }

    //광고그룹 데이터 업데이트
    public function updateAdGroups()
    { 
        CLI::clearScreen();
        CLI::write("광고그룹 업데이트를 진행합니다.", "light_red");
        $this->chainsaw->updateAdGroups();
        CLI::write("광고그룹 업데이트 완료", "yellow");
    }

    //소재 데이터 업데이트
    public function updateCreatives()
    { 
        CLI::clearScreen();
        CLI::write("소재 데이터 업데이트를 진행합니다.", "light_red");
        $this->chainsaw->updateCreatives();
        CLI::write("소재 데이터 업데이트 완료", "yellow");
    }

    //전체 소재 보고서 BASIC 업데이트
    /*
    public function updateReportByAdgroup($date = null)
    { 
        CLI::clearScreen();
        CLI::write("소재 보고서 업데이트를 진행합니다.", "light_red");
        if(is_null($date))
            $date = CLI::prompt("전체 소재 보고서 BASIC 수신할 날짜를 입력해주세요.(ex. ".date('Y-m-d', strtotime('-1 day')).")", 'TODAY');
        $this->chainsaw->updateCreativesReportBasic($date);
        CLI::write("소재 보고서 업데이트 완료", "yellow");
    }
    */
    public function updateReportByHour($date = null)
    { 
        CLI::clearScreen();
        CLI::write("소재 보고서 업데이트를 진행합니다.", "light_red");
        if(is_null($date))
            $date = CLI::prompt("전체 소재 보고서 BASIC 수신할 날짜를 입력해주세요.(ex. ".date('Y-m-d', strtotime('-1 day')).")", 'TODAY');
        $this->chainsaw->updateHourReportBasic($date);
        CLI::write("소재 보고서 업데이트 완료", "yellow");
    }

    public function getAll() {
        $this->chainsaw->updateAdAccounts();
        $this->chainsaw->updateCampaigns();
        $this->chainsaw->updateAdGroups();
        $this->chainsaw->updateCreatives();
        $this->chainsaw->updateBizform();
    }
    

    //비즈폼 데이터 업데이트
    public function updateBizform()
    { 
        CLI::clearScreen();
        CLI::write("비즈폼 데이터 업데이트를 진행합니다.", "light_red");
        $this->chainsaw->updateBizform();
        CLI::write("비즈폼 데이터 업데이트 완료", "yellow");
    }

    //app_subscribe 데이터 업데이트
    /* public function moveToAppsubscribe()
    { 
        CLI::clearScreen();
        CLI::write("app_subscribe 데이터 업데이트를 진행합니다.", "light_red");
        $this->chainsaw->moveToLeads();
        CLI::write("app_subscribe 데이터 업데이트 완료", "yellow");
    } */

    //유효DB 개수 업데이트
    public function updateDB($sdate = null, $edate = null) {
        CLI::clearScreen();
        if($sdate == null)
            $sdate = CLI::prompt("유효DB 업데이트 할 시작날짜를 입력해주세요.", date('Y-m-d'));
        if($edate == null)
            $edate = CLI::prompt("유효DB 업데이트 할 종료날짜를 입력해주세요.", $sdate);
        $sdate = date_create($sdate);
        $edate = date_create(date('Y-m-d', strtotime($edate.'+1 days')));
        $interval = DateInterval::createFromDateString('1 day');
        $date_range = new DatePeriod($sdate, $interval, $edate);
        foreach($date_range as $date) {
            $date = $date->format('Y-m-d');
            CLI::write("{$date} 유효DB를 업데이트 합니다.", "light_red");
            $result = $this->chainsaw->getCreativesUseLanding($date);
        }
    }

    //리포트데이터를 업데이트
    public function updateReportByDate($sdate=null, $edate=null)
    { 
        CLI::clearScreen();
        CLI::write("리포트데이터를 업데이트를 진행합니다.", "light_red");
        if($sdate == null)
            $sdate = CLI::prompt("리포트데이터를 수신할 기간 중 시작날짜를 입력해주세요.", date('Y-m-d'));
        if($edate == null)
            $edate = CLI::prompt("리포트데이터를 수신할 기간 중 종료날짜를 입력해주세요.", $sdate);
        $this->chainsaw->updateReportByDate($sdate, $edate);
        CLI::write("리포트데이터 업데이트 완료", "yellow");
    }

    //소재 자동 변경
    public function autoCreativeOnOff()
    { 
        CLI::clearScreen();
        CLI::write("소재 자동 변경을 진행합니다.", "light_red");
        $check = CLI::prompt("전체 소재 보고서 BASIC 수신할 날짜를 입력해주세요.", ['on', 'off']);
        $this->chainsaw->autoCreativeOnOff();
        CLI::write("소재 자동 변경 완료", "yellow");
    }

    //자동 입찰가한도 리셋
    public function autoLimitBidAmountReset()
    { 
        CLI::clearScreen();
        CLI::write("자동 입찰가한도 리셋을 진행합니다.", "light_red");
        $date = CLI::prompt("자동 입찰가한도 리셋할 날짜를 입력해주세요.", date('Y-m-d'));
        $this->chainsaw->autoLimitBidAmountReset($date);
        CLI::write("자동 입찰가한도 리셋 완료", "yellow");
    }

    //자동 입찰가 설정
    public function autoLimitBidAmount()
    { 
        CLI::clearScreen();
        CLI::write("자동 입찰가한도 설정을 진행합니다.", "light_red");
        $date = CLI::prompt("자동 입찰가한도 설정할 날짜를 입력해주세요.", date('Y-m-d'));
        $this->chainsaw->autoLimitBidAmount($date);
        CLI::write("자동 입찰가한도 설정 완료", "yellow");
    }

    //자동 예산한도 리셋
    public function autoLimitBudgetReset()
    { 
        CLI::clearScreen();
        CLI::write("자동 예산한도 리셋을 진행합니다.", "light_red");
        $date = CLI::prompt("자동 예산한도 리셋할 날짜를 입력해주세요.", date('Y-m-d'));
        $this->chainsaw->autoLimitBudgetReset($date);
        CLI::write("자동 예산한도 리셋 완료", "yellow");
    }

    //자동 예산한도 설정
    public function autoLimitBudget()
    { 
        CLI::clearScreen();
        CLI::write("자동 예산한도 설정을 진행합니다.", "light_red");
        $date = CLI::prompt("자동 예산한도 설정할 날짜를 입력해주세요.", date('Y-m-d'));
        $this->chainsaw->autoLimitBudget();
        CLI::write("자동 예산한도 설정 완료", "yellow");
    }

    //광고그룹 AI 업데이트
    public function setAdGroupsAiRun()
    { 
        CLI::clearScreen();
        CLI::write("광고그룹 AI 업데이트를 진행합니다.", "light_red");
        $this->chainsaw->setAdGroupsAiRun();
        CLI::write("광고그룹 AI 업데이트 완료", "yellow");
    }

    //AI 자동켜기
    public function autoAiOn()
    { 
        CLI::clearScreen();
        CLI::write("AI를 시작합니다.", "light_red");
        $this->chainsaw->autoAiOn();
        CLI::write("AI 시작", "yellow");
    }
}
