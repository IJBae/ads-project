<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Controllers\EventManage\AdvertiserController;
use App\Services\LoggerService;

class EvtOverwatch extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'cron';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'EvtOverwatch';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = '';

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('그룹웨어 데이터 연동 작업을 시작합니다.', 'green');
        try{
            $advertiser = new AdvertiserController();
            $advertiser->evtOverwatch();
            CLI::write('오버워치 실행 완료', 'green');
        } catch (\Exception $e) {
            $this->showError($e);
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
