<?php

namespace App\Controllers\Integrate;

use App\Controllers\BaseController;
use App\Models\Integrate\IntegrateModel;
use CodeIgniter\API\ResponseTrait;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Mpdf\Mpdf;
class IntegrateController extends BaseController
{
    use ResponseTrait;
    
    protected $integrate;
    protected $is_pravate_perm = false;
    private $leads_status = [
        0 => "미확인", 1 => "인정", 2 => "중복", 3 => "성별불량", 4 => "나이불량", 5 => "콜불량", 6 => "번호불량", 7 => "테스트", 
        8 => "이름불량", 9 => "지역불량", 10 => "업체불량", 11 => "미성년자", 12 => "본인아님", 13 => "쿠키중복", 99 => "확인"
    ];
    public function __construct() 
    {
        $this->integrate = model(IntegrateModel::class);
        if(auth()->user()->inGroup('superadmin','admin','developer', 'agency', 'advertiser')) {
            $this->is_pravate_perm = true;
        }
    }

    public function index()
    {
        $_get = $this->request->getGet();
        return view('integrate/DBintegrated', $_get);
    }
    public function download()
    {
        $_get = $this->request->getGet();
        return view('integrate/DBdownload', $_get);
    }

    public function getList()
    {
        if ($this->request->isAJAX()) {
            $date = $this->request->getPost('date') ?? ['sdate' => date('Y-m-d'), 'edate' => date('Y-m-d')];
            $sdate = (new \DateTime($date['sdate']))->format('Y-m-d') . ' 00:00:00';
            $edate = (new \DateTime($date['edate']))->format('Y-m-d') . ' 23:59:59';

            //list
            $list = $this->integrate->getEventLead(['sdate' => $sdate, 'edate' => $edate]);
            foreach($list['data'] as &$d){
                // Trim all data
                $d = array_map('trim', $d);
                $d['hash_no'] = makeHash($d['info_seq']);
                $event_url = env('app.eventURL');
                if($d['another_url']) 
                    $event_url = env('app.newEventURL');
                $d['event_url'] = $event_url.$d['hash_no'];
                $etc = [];
                if(!empty($d['email'])) {
                    $etc[] = $d['email'];
                }
                $d['dec_phone'] = preg_replace("/([0-9]{3})([0-9]{4})([0-9]{4})/", "$1-$2-$3", $d['dec_phone']);
                $d['age'] = $d['age'] == 0 ? '' : $d['age'];
                if(!$this->is_pravate_perm) {
                    if(!preg_match('/test|테스트/i', $d['name']) && $d['status'] != 7) {
                        $d['dec_phone'] = substr($d['dec_phone'],0,3).'<i class="masking">&#9618;&#9618;</i>'.substr($d['dec_phone'],-4);
                        $d['dec_phone'] = html_entity_decode($d['dec_phone']);
                        $name = trim($d['name']);
                        $d['name'] = '';
                        for($ii=0; $ii<mb_strlen($name); $ii++) {
                            if(mb_strlen($name) >= 4 && $ii > 2 && mb_strlen($name)-1 != $ii)
                                continue;
                            else 
                                $d['name'] .= (($ii>0&&$ii<mb_strlen($name)-1)||$ii==1)?'<i class="masking">&#9618;</i>':mb_substr($name, $ii, 1);
                                $d['name'] = html_entity_decode($d['name']);
                        }
                    }
                }
                for($i2=1;$i2<7;$i2++){
                    if(!empty($d['add'.$i2])){		
                        if(strpos($d['add'.$i2], "uploads")){
                            $href = "<a href='".str_replace("./","{$event_url}uploads/",$d['add'.$i2])."' target='_blank'>[파일보기]</a>";
                            $etc[] = $href;
                        }else if(strpos($d['add'.$i2], "/v_")){
                            $href = "<a href='{$event_url}img_viewer.php?data={$d['add'.$i2]}' target='_blank'>[파일보기]</a>";
                            $etc[] = $href;
                        }
                        else{
                            $etc[] = $d['add'.$i2];
                        }
                    }
                }
                if(!empty($d['memo'])){
                    $etc[] = $d['memo'];
                }
                if(!empty($d['addr'])){
                    $etc[] = $d['addr'];
                }
                if(!empty($d['branch'])){
                    $etc[] = $d['branch'];				
                }
                $add = implode('/', $etc);    
                $d['add'][] = $add;
            }
            
            $result = $list;
            
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    private function getColumns()
    {
        $columns = [
            'event_seq' => '이벤트', 'site' => '사이트',
            'name' => '이름', 'phone' => '연락처',
            'age' => '나이', 'gender' => '성별', 'branch' => '지점', 'addr' => '주소',
            'email' => 'Email', 'add1' => '설문1', 'add2' => '설문2', 'add3' => '설문3',
            'add4' => '설문4', 'add5' => '설문5', 'add6' => '설문6', 'status' => '상태',
            'memos' => '메모', 'ip' => 'IP', 'reg_date' => '등록일시'
        ];
        if(auth()->user()->hasPermission('integrate.description') 
        || auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){
            $columns = array_merge(
                array_slice($columns, 0, 2, true),
                ['description' => '구분'],
                array_slice($columns, 2, null, true)
            );
        }
        if(auth()->user()->hasPermission('integrate.media') 
        || auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){
            $columns = array_merge(
                array_slice($columns, 0, 2, true),
                ['media' => '매체'],
                array_slice($columns, 2, null, true)
            );
        }
        if(auth()->user()->hasPermission('integrate.advertiser') 
        || auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){
            $columns = array_merge(
                array_slice($columns, 0, 2, true),
                ['advertiser' => '광고주'],
                array_slice($columns, 2, null, true)
            );
        }

        if (getenv('MY_SERVER_NAME') === 'resta' && auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')) {
            $columns = array_merge(['company' => '회사명'], $columns);
        }

        return $columns;
    }

    private function transformData($data, $columns)
    {
        $transformedData = [];
        foreach ($data as $row) {
            $transformedRow = [];
            $isTestOrStatus7 = preg_match('/test|테스트/i', $row['name']) || $row['status'] == 7;

            foreach ($columns as $key => $header) {
                $value = $row[$key];
                if ($key === 'phone') {
                    if (!$this->is_pravate_perm && !$isTestOrStatus7) {
                        $value = substr($value, 0, 3) . '&#9618;&#9618;' . substr($value, -4);
                        $value = html_entity_decode($value);
                    } else {
                        $value = preg_replace("/([0-9]{3})([0-9]{4})([0-9]{4})/", "$1-$2-$3", $value);
                    }
                } elseif ($key === 'status') {
                    $value = $this->leads_status[$row[$key]];
                } elseif ($key === 'age') {
                    $value = $value == 0 ? "" : $value;
                } elseif ($key === 'name' && !$this->is_pravate_perm && !$isTestOrStatus7) {
                    $name = trim($value);
                    $value = '';
                    for ($ii = 0; $ii < mb_strlen($name); $ii++) {
                        if (mb_strlen($name) >= 4 && $ii > 2 && mb_strlen($name) - 1 != $ii) {
                            continue;
                        } else {
                            $value .= (($ii > 0 && $ii < mb_strlen($name) - 1) || $ii == 1) ? '&#9618;' : mb_substr($name, $ii, 1);
                            $value = html_entity_decode($value);
                        }
                    }
                }
                $transformedRow[$key] = $value;
            }
            $transformedData[] = $transformedRow;
        }
        return $transformedData;
    }

    public function downloadData()
    {
        $sdate = $this->request->getPost('sdate');
        $edate = $this->request->getPost('edate');
        $advertiser = $this->request->getPost('advertiser');
        $media = $this->request->getPost('media');
        $description = $this->request->getPost('description');
        $message = $this->request->getPost('message');
        $fileFormat = $this->request->getPost('file_format'); // 파일 형식 추가
        $user = auth()->user();
    
        $data = [
            'sdate' => $sdate,
            'edate' => $edate,
            'advertiser' => $advertiser,
            'media' => $media,
            'description' => $description,
            'message' => $message,
            'username' => $user->username,
            'reg_date' => date('Y-m-d H:i:s')
        ];
    
        $this->integrate->saveDownloadData($data);
    
        $row = $this->integrate->getEventLeadRow($data);
        if (!$row) {
            return $this->response->setJSON(['status' => 'error', 'message' => '데이터를 찾을 수 없습니다.']);
        }
    
        $columns = $this->getColumns();
        $transformedData = $this->transformData($row, $columns);
    
        switch ($fileFormat) {
            case 'csv':
                $fileData = $this->createCSVFile($transformedData, $columns);
                $contentType = 'text/csv';
                $fileExtension = 'csv';
                break;
            case 'pdf':
                $fileData = $this->createPDFFile($transformedData, $columns);
                $contentType = 'application/pdf';
                $fileExtension = 'pdf';
                break;
            case 'xlsx':
            default:
                $fileData = $this->createExcelFile($transformedData, $columns);
                $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                $fileExtension = 'xlsx';
                break;
        }
    
        return $this->response
            ->setHeader('Content-Type', $contentType)
            ->setHeader('Content-Disposition', 'attachment; filename="event_lead_' . date('YmdHis') . '.' . $fileExtension . '"')
            ->setBody($fileData);
    }

    private function createCSVFile($data, $columns)
    {
        $output = fopen('php://temp', 'r+');
        // UTF-8 BOM 추가
        fputs($output, "\xEF\xBB\xBF");
        fputcsv($output, array_values($columns));
    
        foreach ($data as $row) {
            $rowData = [];
            foreach ($columns as $key => $header) {
                $rowData[] = $row[$key] ?? ''; // 키가 존재하지 않으면 빈 문자열로 대체
            }
            fputcsv($output, $rowData);
        }
    
        rewind($output);
        $csvData = stream_get_contents($output);
        fclose($output);
    
        return $csvData;
    }
    private function createPDFFile($data, $columns)
    {
        $html = '<table border="1" style="border-collapse: collapse; width: 100%;"><thead><tr>';
        foreach ($columns as $header) {
            $html .= "<th style='padding: 5px;'>{$header}</th>";
        }
        $html .= '</tr></thead><tbody>';
    
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($columns as $key => $header) {
                $value = $row[$key] ?? ''; // 키가 존재하지 않으면 빈 문자열로 대체
                $html .= "<td style='padding: 5px;'>{$value}</td>";
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
    
        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'nanumgothic', // 기본 폰트를 나눔고딕으로 설정
            'orientation' => 'L', // 가로 방향 설정
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 5,
            'margin_bottom' => 5,
            'tempDir' => WRITEPATH . 'tmp', // CodeIgniter 4 writable 디렉토리 설정
            'fontDir' => array_merge((new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'], [
                FCPATH . 'fonts', // 폰트 파일 경로
            ]),
            'fontdata' => (new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'] + [
                'nanumgothic' => [
                    'R' => 'NanumGothic-Regular.ttf',
                    'B' => 'NanumGothic-Bold.ttf',
                ]
            ],
            'default_font_size' => 12, // 기본 폰트 크기 설정
        ]);
    
        $mpdf->WriteHTML($html);
        return $mpdf->Output('', 'S');
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
            if (in_array($key, ['addr', 'email', 'add1', 'add2', 'add3', 'add4', 'add5', 'add6'])) {
                $sheet->getColumnDimension($columnIndex)->setAutoSize(true);
                $sheet->getColumnDimension($columnIndex)->setWidth(200 / 7); // 최대 200픽셀로 제한
            } else if ($key === 'name') {
                $sheet->getColumnDimension($columnIndex)->setWidth(100 / 7); // 100픽셀로 설정 (엑셀의 너비 단위는 약 1/7인치)
            } else {
                $sheet->getColumnDimension($columnIndex)->setAutoSize(true); // 자동 너비 조정
            }
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
                if ($key === 'phone') {
                    $value = preg_replace("/([0-9]{3})([0-9]{4})([0-9]{4})/", "$1-$2-$3", $value);
                    $sheet->setCellValue($columnIndex . $rowIndex, $value);
                } elseif($key === 'age') {
                    $value = $value == 0 ? "" : $value;
                    $sheet->setCellValue($columnIndex . $rowIndex, $value);
                } elseif ($key === 'site') {
                    $sheet->getCell($columnIndex . $rowIndex)->setValueExplicit($value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValue($columnIndex . $rowIndex, $value);
                }
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

    public function getButtons()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post'){
            $arg = $this->request->getPost();
            $arg['sdate'] = (new \DateTime($arg['sdate']))->format('Y-m-d') . ' 00:00:00';
            $arg['edate'] = (new \DateTime($arg['edate']))->format('Y-m-d') . ' 23:59:59';
            $leadsAll = $this->integrate->getEventLeadCount($arg);

            $filters = [];
            $filters['data'] = $leadsAll['filteredResult'];
            $buttons['filtered'] = $this->setCount($leadsAll['filteredResult'], 'count');
            $buttons['noFiltered'] = $this->setCount($leadsAll['noFilteredResult'], 'total');
            foreach($buttons['noFiltered'] as $type => $row){
                foreach($row as $k => $v) {
                    if(!isset($buttons['filtered'][$type][$k]))
                        $buttons['filtered'][$type][$k] = ['count' => 0];
                    $filter_lists[$type][$k] = array_merge($buttons['filtered'][$type][$k], $v);
                }                
            }
            if(!empty($filter_lists)){
                foreach($filter_lists as $type => $row) {
                    $sortedRow = array();

                    foreach ($row as $name => $v) {
                        $sortedRow[$name] = array_merge(['label' => $name], $v);
                    }
                
                    asort($sortedRow);
                    $sortedRow = array_values($sortedRow);
                
                    $filters[$type] = $sortedRow;
                }
            }
            $status = $this->integrate->getStatusCount($arg);
            $filters['status'] = $status;
           
            return $this->respond($filters);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getEventLeadCount()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            if(!isset($arg['searchData'])) {
                $arg['searchData'] = [
                    'sdate'=> date('Y-m-d'),
                    'edate'=> date('Y-m-d')
                ];
            }
            $data = $this->integrate->getEventLeadCount($arg);

            $adv_counts = array();
            $med_counts = array();
            $event_counts = array();
            foreach ($data as $row) {
                if (!array_key_exists($row['adv_name'], $adv_counts)) {
                    $adv_counts[$row['adv_name']] = array(
                        'countAll' => 0
                    );
                }

                if (!array_key_exists($row['med_name'], $med_counts)) {
                    $med_counts[$row['med_name']] = array(
                        'countAll' => 0
                    );
                }

                $event_counts[$row['description']] = array(
                    'countAll' => $row['countAll'],
                );

                $adv_counts[$row['adv_name']]['countAll'] += $row['countAll'];
                $med_counts[$row['med_name']]['countAll'] += $row['countAll'];
            }

            $result = [
                'advertiser' => $adv_counts,
                'media' => $med_counts,
                'description' => $event_counts,
            ];
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getStatusCount()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            if(!isset($arg['searchData'])) {
                $arg['searchData'] = [
                    'sdate'=> date('Y-m-d'),
                    'edate'=> date('Y-m-d')
                ];
            }
            $result = $this->integrate->getStatusCount($arg);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getMemo()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            $result = $this->integrate->getMemo($arg);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function addMemo() {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post'){
            $arg = $this->request->getPost();
            $result = $this->integrate->addMemo($arg);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function setStatus() {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post'){
            if(!auth()->user()->hasPermission('integrate.status') && !auth()->user()->inGroup('superadmin', 'admin')){
                return $this->fail("권한이 없습니다.");
            }
            $arg = $this->request->getPost();
            $result = $this->integrate->setStatus($arg);
                
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    private function setCount($leads, $type)
    {
        $data = [
            'advertiser' => [],
            'media' => [],
            'description' => [],
        ];
        if(getenv('MY_SERVER_NAME') === 'resta' && auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){
            $data['company'] = [];
        }
        foreach($leads as $row) {
            if(getenv('MY_SERVER_NAME') === 'resta' && auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){
            //분류 기준
                if (!isset($data['company']['리스타'])) {
                    $data['company']['리스타'][$type] = 0;
                }
                if($row['status'] == 1 && ($row['company'] != '케어랩스' && $row['company'] != '테크랩스')){
                    $data['company']['리스타'][$type]++;
                }

                if (isset($row['company'])) {
                    if (!isset($data['company']['케어랩스'])) {
                        $data['company']['케어랩스'][$type] = 0;
                    }

                    if (!isset($data['company']['테크랩스'])) {
                        $data['company']['테크랩스'][$type] = 0;
                    }

                    if($row['status'] == 1 && $row['company'] == '케어랩스'){
                        $data['company']['케어랩스'][$type]++;
                    }

                    if($row['status'] == 1 && $row['company'] == '테크랩스'){
                        $data['company']['테크랩스'][$type]++;
                    }
                }
            }
            //광고주 기준
            if (!empty($row['advertiser'])) {
                if (!isset($data['advertiser'][$row['advertiser']])) {
                    $data['advertiser'][$row['advertiser']][$type] = 0;
                }

                if($row['status'] == 1){
                    $data['advertiser'][$row['advertiser']][$type]++;
                }
            }

            //매체 기준
            if (!empty($row['media'])) {
                if (!isset($data['media'][$row['media']])) {
                    $data['media'][$row['media']][$type] = 0;
                }

                if($row['status'] == 1){
                    $data['media'][$row['media']][$type]++;
                }
            }

            //이벤트 기준
            if (!empty($row['description'])) {
                if (!isset($data['description'][$row['description']])) {
                    $data['description'][$row['description']][$type] = 0;
                }

                if($row['status'] == 1){
                    $data['description'][$row['description']][$type]++;
                }
            }
        }

        return $data;
    }
}
