<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\WithdrawModel;
use CodeIgniter\API\ResponseTrait;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class WithdrawController extends BaseController
{
    use ResponseTrait;

    protected $wd;

    public function __construct()
    {
        $this->wd = model(WithdrawModel::class);
    }
    public function withdraw()
    {
        return view('accounting/withdraw/withdraw');
    }

    public function withdrawList()
    {
        return view('accounting/withdraw/withdraw_list');
    }

    public function withdrawWrite(){
        return view('accounting/withdraw/withdraw_write');
    }

    public function accountList(){
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post') {
            $data = $this->request->getRawInput();
            $result = $this->wd->accountList($data);

            return $result;
        } else {
            return $this->fail("잘못된 요청");
        }
    }

    public function typeList(){
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get') {
            $result = $this->wd->typeList();

            return $result;
        } else {
            return $this->fail("잘못된 요청");
        }
    }

    public function userList(){
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get') {
            $result = $this->wd->userList();

            return $result;
        } else {
            return $this->fail("잘못된 요청");
        }
    }

    public function getList(){
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post') {
            $data = $this->request->getRawInput();
            $result = $this->wd->getList($data);

            return $result;
        } else {
            return $this->fail("잘못된 요청");
        }
    }

    public function excelDownload(){
        $data = $this->request->getRawInput();
        $transformedData = json_decode($this->wd->getList($data),true);
        $columns = $this->getColumns();

        $fileData = $this->createExcelFile($transformedData, $columns);
        $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $fileExtension = 'xlsx';

        return $this->response
            ->setHeader('Content-Type', $contentType)
            ->setHeader('Content-Disposition', 'attachment; filename="event_lead_' . date('YmdHis') . '.' . $fileExtension . '"')
            ->setBody($fileData);
    }

    private function createExcelFile($data, $columns)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 헤더 설정
        $columnIndex = 'A';
        $sheet->setCellValue($columnIndex . '1', '#');
        $columnIndex++;
        foreach ($columns as $key => $header) {
            $sheet->setCellValue($columnIndex . '1', $header);
//            if (in_array($key, ['addr', 'email', 'add1', 'add2', 'add3', 'add4', 'add5', 'add6'])) {
//                $sheet->getColumnDimension($columnIndex)->setAutoSize(true);
//                $sheet->getColumnDimension($columnIndex)->setWidth(200 / 7); // 최대 200픽셀로 제한
//            } else if ($key === 'name') {
//                $sheet->getColumnDimension($columnIndex)->setWidth(100 / 7); // 100픽셀로 설정 (엑셀의 너비 단위는 약 1/7인치)
//            } else {
            $sheet->getColumnDimension($columnIndex)->setAutoSize(true); // 자동 너비 조정
//            }
            $columnIndex++;
        }

        // 필터 설정
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());

        // 데이터 삽입
        $rowIndex = 2; // 데이터는 2번째 행부터 시작
        $totalRows = count($data);

        foreach ($data as $row) {

            $columnIndex = 'A';
            $sheet->setCellValue($columnIndex . $rowIndex, $totalRows); // 역순 카운트
            $columnIndex++;

            foreach ($columns as $key => $header) {
                $value = $row[$key];

                if(!isset($row["gwstatus"])){
                    $row["gwstatus"]="";
                }else if(strpos($row["gwstatus"],"http") > -1){
                    $row["gwstatus"]="완료";
                }

//                if ($key === 'phone') {
//                    $value = preg_replace("/([0-9]{3})([0-9]{4})([0-9]{4})/", "$1-$2-$3", $value);
//                    $sheet->setCellValue($columnIndex . $rowIndex, $value);
//                } elseif($key === 'age') {
//                    $value = $value == 0 ? "" : $value;
//                    $sheet->setCellValue($columnIndex . $rowIndex, $value);
//                } elseif ($key === 'site') {
//                    $sheet->getCell($columnIndex . $rowIndex)->setValueExplicit($value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
//                } else {
                $sheet->setCellValue($columnIndex . $rowIndex, $value);
//                }
                $columnIndex++;
            }

            $rowIndex++;
            $totalRows--;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $excelData = ob_get_contents();
        ob_end_clean();

        return $excelData;
    }

    private function getColumns()
    {
        $columns = [
            'reg_date' => '등록일시',
            'nickname' => '이름',
            'type' => '구분',
            'company_name' => '거래처명(입금주명)',
            'bank' => '은행',
            'account' => '계좌번호',
            'detail' => '내역(자세히)',
            'total_price' => '총금액(VAT 포함)',
            'memo' => '비고',
            'status' => '결과',
            'gwstatus'=>'결제현황',
            'date' => '출금완료일',
        ];

//        if(auth()->user()->hasPermission('integrate.description')
//            || auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){
//            $columns = array_merge(
//                array_slice($columns, 0, 2, true),
//                ['description' => '구분'],
//                array_slice($columns, 2, null, true)
//            );
//        }
//        if(auth()->user()->hasPermission('integrate.media')
//            || auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){
//            $columns = array_merge(
//                array_slice($columns, 0, 2, true),
//                ['media' => '매체'],
//                array_slice($columns, 2, null, true)
//            );
//        }
//        if(auth()->user()->hasPermission('integrate.advertiser')
//            || auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){
//            $columns = array_merge(
//                array_slice($columns, 0, 2, true),
//                ['advertiser' => '광고주'],
//                array_slice($columns, 2, null, true)
//            );
//        }

        return $columns;
    }

}
