<?php

namespace App\Controllers\AdvertisementManager;

use App\Controllers\BaseController;
use App\Models\Advertiser\AdvEtcManagerModel;

class AdvEtcManagerController extends BaseController
{
    protected $advEtcManagerModel;

    public function __construct()
    {
        $this->advEtcManagerModel = model(AdvEtcManagerModel::class);
    }

    // 기본 페이지 로드
    public function index()
    {
        return view('advertisements/etc/etc');
    }

    public function getList() {
        if ($this->request->isAJAX()) {
            $date = $this->request->getPost('date') ?? ['sdate' => date('Y-m-d'), 'edate' => date('Y-m-d')];
            $sdate = (new \DateTime($date['sdate']))->format('Y-m-d') . ' 00:00:00';
            $edate = (new \DateTime($date['edate']))->format('Y-m-d') . ' 23:59:59';
    
            $data = $this->advEtcManagerModel->getList(['sdate' => $sdate, 'edate' => $edate]);
    
            return $this->response->setJSON([
                'data' => $data // dataTables에 맞게 'data' 키 사용
            ]);
        } else {
            return $this->response->setStatusCode(403)->setBody('Access denied');
        }
    }

    public function updateData()
    {
        $data = $this->request->getPost();
        
        $result = $this->advEtcManagerModel->updateOrInsertEventData($data);
        
        if ($result) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data updated successfully']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to update data']);
        }
    }
}
