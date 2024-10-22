<?= $this->extend('templates/front.php'); ?>

<?= $this->section('title'); ?>
    ZENITH - 인사관리 / 시간차 관리
<?= $this->endSection(); ?>

    <!--헤더-->
<?= $this->section('header'); ?>
    <link href="/static/css/datatables.css" rel="stylesheet">
    <link href="/static/node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="/static/node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="/static/node_modules/datatables.net-staterestore-bs5/css/stateRestore.bootstrap5.min.css" rel="stylesheet">
    <script src="/static/node_modules/datatables.net/js/dataTables.min.js"></script>
    <script src="/static/node_modules/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<?= $this->endSection(); ?>

    <!--바디-->
<?= $this->section('body'); ?>
<?= $this->endSection(); ?>

    <!--컨텐츠영역-->
<?= $this->section('content'); ?>
    <div class="sub-contents-wrap dayOff-contanier">
        <div class="title-area">
            <h2 class="page-title">시간차 관리</h2>
            <button type="button" class="btn btn-dark ms-3" data-bs-toggle="modal" data-bs-target="#totalModal">전체조회</button>
        </div>
        <div class="row mt-5">
            <div class="col half">
                <h3 class="content-title">결재내역</h3>
                <div class="search-wrap">
                    <form id="issue" name="issue" class="search d-flex justify-content-center">
                        <div class="term d-flex align-items-center">
                            <label>
                                <input type="text" name="sdate1" id="sdate1" value="<?= date("Y-m-d", strtotime('-1 month')) ?>" readonly="readonly">
                                <i class="bi bi-calendar2-week"></i>
                            </label>
                            <span> ~ </span>
                            <label>
                                <input type="text" name="edate1" id="edate1" value="<?= date("Y-m-d") ?>" readonly="readonly">
                                <i class="bi bi-calendar2-week"></i>
                            </label>
                        </div>
                        <div class="input">
                            <input type="text" name="stx1" id="stx1" placeholder="검색어를 입력하세요">
                            <button class="btn-primary" id="search_btn" type="submit">조회</button>
                        </div>
                    </form>
                </div>
                <?php
                if (auth()->user()->hasPermission('humanresource.management')) {
                    ?>
                    <div class="table-responsive">
                        <form id="approval_write" name="approval_write">
                            <table class="table table-striped table-hover table-default">
                                <input type="hidden" id="seq" name="seq" value="">
                                <input type="hidden" id="gw_user" name="gw_user" value="">
                                <input type="hidden" id="user_id" name="user_id" value="">
                                <input type="hidden" id="doc_id" name="doc_id" value="">
                                <input type="hidden" id="doc_title" name="doc_title" value="">
                                <input type="hidden" id="rep_datetime" name="rep_datetime" value="">

                                <colgroup>
                                    <col style="width:15%">
                                    <col style="width:10%">
                                    <col style="width:10%">
                                    <col style="width:12%">
                                    <col style="width:12%">
                                    <col style="width:10%">
                                </colgroup>
                                <thead class="table-dark">
                                <tr>
                                    <th scope="col">문서번호</th>
                                    <th scope="col">작성자</th>
                                    <th scope="col">연차차감</th>
                                    <th scope="col">요청수량</th>
                                    <th scope="col">승인수량</th>
                                    <th scope="col">상태</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex">
                                            <label>
                                                <input type="text" name="doc_no" id="doc_no" required autocomplete="off" class="form-control">
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-center">
                                            <label>
                                                <input type="text" name="user_nickname" id="user_nickname" autocomplete="off" class="form-control">
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-center">
                                            <input type="text" name="break_balance" id="break_balance" autocomplete="off" class="form-control">
                                            <span class="ms-1">개</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-center">
                                            <input type="text" name="hourticket" id="hourticket" value="0" class="form-control wid70">
                                            <span class="ms-1">시간</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-center">
                                            <input type="text" name="hourticket2" id="hourticket2" autocomplete="off"  value="0" class="form-control wid70">
                                            <span class="ms-1">시간</span>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" id="reg_use1" name="reg_use1" class="btn btn-primary">등록
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                <?php } ?>
                <div class="table-responsive dayOff-payment-table" id="dayOff-payment-table">
                    <table class="table table-striped table-hover table-default">
                        <thead class="table-dark">
                        <tr>
                            <th scope="col">작성자</th>
                            <th scope="col">작성일</th>
                            <th scope="col">문서번호</th>
                            <th scope="col">연차차감</th>
                            <th scope="col">발급쿠폰</th>
                            <th scope="col">결과</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="col half">
                <h3 class="content-title">신청현황</h3>
                <div class="search-wrap">
                    <form id="use" name="use" class="search d-flex justify-content-center">
                        <div class="term d-flex align-items-center">
                            <label>
                                <input type="text" name="sdate2" id="sdate2"
                                       value="<?= date("Y-m-d", strtotime('-1 month')) ?>" readonly="readonly">
                                <i class="bi bi-calendar2-week"></i>
                            </label>
                            <span> ~ </span>
                            <label>
                                <input type="text" name="edate2" id="edate2" value="<?= date("Y-m-d") ?>"
                                       readonly="readonly">
                                <i class="bi bi-calendar2-week"></i>
                            </label>
                        </div>
                        <div class="input">
                            <input type="text" name="stx2" id="stx2" placeholder="검색어를 입력하세요">
                            <button class="btn-primary" type="submit">조회</button>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover table-default">
                        <colgroup>
                            <col style="width:30%">
                            <col style="width:20%;">
                            <col style="width:35%">
                            <col style="width:15%">
                        </colgroup>
                        <thead class="table-dark">
                        <tr>
                            <th scope="col">날짜</th>
                            <th scope="col">쿠폰사용</th>
                            <th scope="col">사용시간</th>
                            <th scope="col">상태</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <div class="d-flex">
                                    <label class="d-flex align-items-center justify-content-center">
                                        <input type="text" name="sdate3" id="sdate3" class="form-control">
                                        <i class="bi bi-calendar2-week"></i>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center justify-content-center">
                                    <select id="hour_ticket" name="hour_ticket" class="form-select">
                                        <option>1시간</option>
                                        <option>2시간</option>
                                        <option>3시간</option>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center justify-content-center">
                                    <select id="reg_hour" name="reg_hour" class="form-select wid70" aria-label="시간 선택">
                                        <option value="none" selected>선택</option>
                                        <?php
                                        for ($i = 10; $i < 19; $i++) {
                                            echo "<option value='{$i}'>{$i}</option>";
                                        }
                                        ?>
                                    </select>
                                    <span class="m-1">시</span>
                                    <select id="reg_min" name="reg_min" class="form-select wid70" aria-label="분 선택">
                                        <option value="none" selected>선택</option>
                                        <?php
                                        for ($i = 0; $i < 6; $i++) {
                                            echo "<option value='{$i}0'>{$i}0</option>";
                                        }
                                        ?>
                                    </select>
                                    <span class="ms-1">분</span>
                                </div>
                            </td>
                            <td>
                                <button type="button" id="reg_use2" name="reg_use2" class="btn btn-primary">등록</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive dayOff-application-table" id="dayOff-application-table">
                    <table class="table table-striped table-hover table-default">
                        <thead class="table-dark">
                        <tr>
                            <th scope="col">사용자</th>
                            <th scope="col">쿠폰사용</th>
                            <th scope="col">사용일자</th>
                            <th scope="col">시간</th>
                            <th scope="col">결과</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="5" class="none">검색된 데이터가 없습니다.</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection(); ?>

<?= $this->section('modal'); ?>
    <div class="modal fade dayOffModal" id="totalModal" tabindex="-1" aria-labelledby="totalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title" id="totalModalLabel">전체 조회</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <form id="all" name="all" class="row g-1">
                            <div class="col-7 d-flex align-items-center">
                                <label>
                                    <input type="text" name="sdate4" id="sdate4" value="<?= date("Y-01-01") ?>"
                                           class="form-control">
                                    <i class="bi bi-calendar2-week"></i>
                                </label>
                                <span class="me-2 pd10"> ~ </span>
                                <label>
                                    <input type="text" name="edate4" id="edate4" value="<?= date("Y-m-d") ?>"
                                           class="form-control">
                                    <i class="bi bi-calendar2-week"></i>
                                </label>
                            </div>
                            <div class="col-5 d-flex">
                                <input type="text" id="stx4" name="stx4" class="form-control">
                                <button type="submit" class="btn btn-primary w-25 ms-2">조회</button>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-modal">
                            <thead>
                            <tr>
                                <th scope="col" rowspan="2" class="align-middle">사용자</th>
                                <th scope="col" colspan="2">검색기간</th>
                                <th scope="col" colspan="3">전체</th>
                            </tr>
                            <tr>
                                <th scope="col">발급</th>
                                <th scope="col">사용</th>
                                <th scope="col">발급</th>
                                <th scope="col">사용</th>
                                <th scope="col">잔여</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade issueDetail" id="issueDetail" tabindex="-1" aria-labelledby="issueDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title" id="issueDetailLabel"> 승인처리</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-modal">
                            <thead></thead>
                            <tbody> </tbody>
                        </table>
                        <div class="d-flex justify-content-end">
                            <button type="submit" id="issue_agree_btn" name="issue_agree_btn" onclick="hourticketConfirm('agree',''+this.value+'','issue')" class="btn btn-primary mt15" value="">승인</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection(); ?>

    <!--스크립트-->
<?= $this->section('script'); ?>
    <script>
        $('#doc_no').on('keyup', function () {
            if (!$("#doc_no").val().match(/\(주\)케어랩스\-[0-9]+\-[0-9]{4,}/)) return;
            getGwData();
        });

        // issue payment dataTable (결재내역)
        let dataTablePayment = $('#dayOff-payment-table table').DataTable({
            "dom": '<r<t>p>', // DataTables의 DOM 구조 설정 (r=처리중 t=테이블 p=페이징)
            "autoWidth": true, // 자동 너비 조정 활성화
            "processing" : true, // 처리 중 표시 활성화
            "serverSide" : false, // 서버 사이드 처리 비활성화
            "responsive": true, // 반응형 테이블 활성화
            "searching": true, // 검색 기능 활성화
            "ordering": true, // 정렬 기능 활성화
            "orderMulti": false, // 다중 열 정렬 활성화
            "orderCellsTop": false, // 열 헤더 클릭 시 정렬 활성화
            "paging": true, // 페이징 비활성화
            "pageLength": 10, // 페이지당 표시할 행 수
            "info": false, // 정보 표시 비활성화
            "scroller": false, // 스크롤러 비활성화
            "scrollX": true, // 가로 스크롤 활성화
            // "stateSave": true, // 상태 저장 활성화
            "stateSave": false, // 상태 저장을 비활성화
            "stateLoadParams": function (settings, data) {
                data.start = 0;//데이터행 시작 위치
                data.length = 10;//데이터행 개수
            },
            "deferRender": false, // 렌더링 지연 비활성화
            "rowId": "seq",
            "language": {"url": '/static/js/dataTables.i18n.json'}, //한글화 파일
            "ajax": {
                "url": "<?=base_url()?>/humanresource/hourticketissue", // AJAX URL 설정
                "type": "POST", // HTTP 요청 방식 설정
                "dataType": "json",
                "data": function (d) {
                    return JSON.stringify({
                        "sdate": $('input[name="sdate1"]').val(),
                        "edate": $('input[name="edate1"]').val(),
                        "stx": $('#stx1').val() // 검색어 추가
                    });
                },
                "dataSrc": function (json) {
                    return json; // 또는 json.data 등 실제 데이터 배열을 반환
                }
            },
            "columns": [
                {"data": "nickname", "width": "15%", "className": "dt-type-center"},
                {"data": "rep_datetime", "width": "20%", "className": "dt-type-center"},
                {
                    "data": "doc_no",
                    "width": "20%",
                    "className": "dt-type-center",
                    "render": function (data, type, row) {
                        if(row.apr_user_id=="완료") {
                            return `<a href='http://gw.carelabs.co.kr/eap/ea/docpop/EAAppDocViewPop.do?doc_id=${row.doc_id}&form_id=18'>${data}</a>`;
                        } else {
                            return `<button id="viewdetail" name="viewdetail" data-bs-toggle="modal" data-bs-target="#issueDetail" value="${row.doc_no}">${data}</button>`;
                        }
                    }
                },
                {"data": "break_balance", "width": "10%", "className": "dt-type-center"},
                {"data": "hour_ticket", "width": "10%", "className": "dt-type-center"},
                {"data": "apr_user_id", "width": "10%", "className": "dt-type-center"}
            ],
        });

        // use application dataTable (신청현황)
        let dataTableApplication = $('#dayOff-application-table table').DataTable({
            "dom": '<r<t>p>', // DataTables의 DOM 구조 설정 (r=처리중 t=테이블 p=페이징)
            "autoWidth": true, // 자동 너비 조정 활성화
            "processing" : true, // 처리 중 표시 활성화
            "serverSide" : false, // 서버 사이드 처리 비활성화
            "responsive": true, // 반응형 테이블 활성화
            "searching": true, // 검색 기능 활성화
            "ordering": true, // 정렬 기능 활성화
            "orderMulti": false, // 다중 열 정렬 활성화
            "orderCellsTop": false, // 열 헤더 클릭 시 정렬 활성화
            "paging": true, // 페이징 비활성화
            "pageLength": 10, // 페이지당 표시할 행 수
            "info": false, // 정보 표시 비활성화
            "scroller": false, // 스크롤러 비활성화
            "scrollX": true, // 가로 스크롤 활성화
            "stateSave": true, // 상태 저장을 비활성화
            "stateLoadParams": function (settings, data) {
                data.start = 0;//데이터행 시작 위치
                data.length = 10;//데이터행 개수
            },
            "deferRender": false, // 렌더링 지연 비활성화
            "rowId": "seq",
            "language": {"url": '/static/js/dataTables.i18n.json'}, //한글화 파일
            "ajax": {
                "url": "<?=base_url()?>/humanresource/hourticketuse", // AJAX URL 설정
                "type": "POST", // HTTP 요청 방식 설정
                "dataType": "json",
                "data": function (d) {
                    return JSON.stringify({
                        "sdate": $('input[name="sdate2"]').val(),
                        "edate": $('input[name="edate2"]').val(),
                        "stx": $('#stx2').val() // 검색어 추가
                    });
                },
                "dataSrc": function (json) {
                    return json; // 또는 json.data 등 실제 데이터 배열을 반환
                }
            },
            "columns": [
                {"data": "nickname", "width": "15%", "className": "dt-type-center"},
                {"data": "hour_ticket", "width": "15%", "className": "dt-type-center"},
                {"data": "date", "width": "20%", "className": "dt-type-center"},
                {"data": "time", "width": "35%", "className": "dt-type-center"},
                {
                    "data": "seq",
                    "width": "15%",
                    "className": "dt-type-center",
                    "render": function (data, type, row) {
                        if (data === "완료") {
                            return data;
                        } else {
                            <?php if (auth()->user()->hasPermission('humanresource.management')) { ?>
                            return `<input type="button" class="btn btn-approve" value="승인" onclick="hourticketConfirm('agree','${data}','use')">
                                    <input type="button" class="btn btn-reject" value="반려" onclick="hourticketConfirm('reject','${data}','use')">`;
                            <?php } else { ?>
                            return '진행중';
                            <?php } ?>
                        }
                    }
                }
            ],
        });

        // use application dataTable (전체조회)
        let dataTableAllSearch = $('.dayOffModal table').DataTable({
            "dom": '<r<t>p>', // DataTables의 DOM 구조 설정 (r=처리중 t=테이블 p=페이징)
            "autoWidth": false, // 자동 너비 조정 활성화
            "processing" : true, // 처리 중 표시 활성화
            "serverSide" : false, // 서버 사이드 처리 비활성화
            "responsive": false, // 반응형 테이블 활성화
            "ordering": true, // 정렬 기능 활성화
            "orderMulti": true, // 다중 열 정렬 활성화
            "paging": true, // 페이징 비활성화
            "pageLength": 5, // 페이지당 표시할 행 수
            "info": false, // 정보 표시 비활성화
            "stateLoadParams": function (settings, data) {
                data.start = 0;//데이터행 시작 위치
                data.length = 10;//데이터행 개수
            },
            "deferRender": true, // 렌더링 지연 비활성화
            "rowId": "seq",
            "language": {"url": '/static/js/dataTables.i18n.json'}, //한글화 파일
            "ajax": {
                "url": "<?=base_url()?>/humanresource/hourticketall", // AJAX URL 설정
                "type": "POST", // HTTP 요청 방식 설정
                "dataType": "json",
                "data": function (d) {
                    return JSON.stringify({
                        "sdate": $('input[name="sdate4"]').val(),
                        "edate": $('input[name="edate4"]').val(),
                        "stx": $('#stx4').val() // 검색어 추가
                    });
                },
                "dataSrc": function (json) {
                    return json; // 또는 json.data 등 실제 데이터 배열을 반환
                }
            },
            "columns": [
                {"data": "nickname", "width": "50%", "className": "dt-type-center", "orderable": true},  // 정렬 가능하도록 설정
                {"data": "hourticket_issue", "width": "10%", "className": "dt-type-center"},
                {"data": "hourticket_use", "width": "10%", "className": "dt-type-center"},
                {"data": "issued", "width": "10%", "className": "dt-type-center"},
                {"data": "used", "width": "10%", "className": "dt-type-center"},
                {"data": "total", "width": "10%", "className": "dt-type-center"},
            ],
        });

        // issue submit (결재내역)
        $('form[name="issue"]').on('submit', function (event) {
            event.preventDefault();
            dataTablePayment.ajax.reload(); // 테이블 데이터 새로고침
        });
        // use submit (신청현황)
        $('form[name="use"]').on('submit', function (event) {
            event.preventDefault();
            dataTableApplication.ajax.reload(); // 테이블 데이터 새로고침
        });

        // all search submit (전체조회)
        $('form[name="all"]').on('submit', function (event) {
            event.preventDefault();
            dataTableAllSearch.ajax.reload(); //신청현황데이블 새로고침
        });

        // 전체 조회 클릭 시 list 출력 (전체조회)
        $('.ms-3').click(function () {
            dataTableAllSearch.ajax.reload(); //신청현황데이블 새로고침
        });

        $("#reg_use1").click(function () {
            issueProc();
        });

        //결제내역 진행중인 문서번호 클릭 시 상세 테이블 출력 start
        let issueDetailTable; //전역변수로 issueDetailTable datatable 선언
        $(document).on('click', '[data-bs-target="#issueDetail"]', function() {
            var searchDocNo = $(this).val();
            if(issueDetailTable){
                issueDetailTable.destroy();
            }
            initIssueDetailTable(searchDocNo);// searchDocNo를 매개변수로 하여 initIssueDetailTable 함수 호출
        });
        function initIssueDetailTable(searchDocNo){
           //datatables
            issueDetailTable = $('.issueDetail table').DataTable({
                "dom": '<t>', 
                "autoWidth": false,
                "processing": true,
                "serverSide": false,
                "paging": false,
                "info": false,
                "language": {"url": '/static/js/dataTables.i18n.json'},
                "ajax": {
                    "url": "<?=base_url()?>humanresource/getgwdata",
                    "type": "POST",
                    "dataType": "json",
                    "data": function (d) {
                        return JSON.stringify({
                            "doc_no": searchDocNo //매개변수로 받은 searchDocNo 전달
                        });
                    },
                    "contentType": "application/json; charset=utf-8",
                    "dataSrc": function (json) {
                        $("#issue_agree_btn").val(json.doc_id);
                        return [json]; //단일 객체를 배열로 변환
                    },
                },
                "columns": [
                    {"title": "작성자", "data": "nickname", "className": "dt-type-center"},
                    {"title": "작성일", "data": "rep_datetime", "className": "dt-type-center"},
                    {"title": "문서번호", "data": "doc_id", "className": "dt-type-center"},
                    {"title": "문서제목", "data": "doc_title", "className": "dt-type-center"},
                    {"title": "휴가일수", "data": "break_balance", "className": "dt-type-center"},
                ],
            });
        }
        //결제내역 진행중인 문서번호 클릭 시 상세 테이블 출력 end

        // reg use2 submit (쿠폰사용)
        $("#reg_use2").click(function () {
            // data filtering start
            if ($("#sdate3").val() === "") {
                alert("날짜 선택이 안되었습니다.")
                return
            }
            if ($("#hour_ticket").val() === "") {
                alert("쿠폰 사용 지정이 안되었습니다.")
                return
            }
            if ($("#reg_hour").val() === "none" || $("#reg_min").val() === "none") {
                alert("사용 시간이 올바르게 지정되지 않았습니다.")
                return
            }
            // data filtering end

            if (confirm("해당 데이터로 등록 하시겠습니까?\n" +
                "날짜 : " + $("#sdate3").val() + "\n" +
                "쿠폰사용 : " + $("#hour_ticket").val() + "\n" +
                "사용시간 : " + $("#reg_hour").val() + "시 " + $("#reg_min").val() + "분"
            )) {
                let data = {
                    "date": $('input[name="sdate3"]').val(),
                    "hour_ticket": $("#hour_ticket").val().replace("시간", ""),
                    "time": $("#reg_hour").val() + ":" + $("#reg_min").val() + ":00"
                }
                $.ajax({
                    url: "<?=base_url()?>humanresource/hourticketreg",
                    type: "POST",
                    dataType: "json",
                    data: JSON.stringify(data),
                    contentType: "application/json; charset=utf-8",
                    success: function (data) {
                        if (data) {
                            alert("등록이 완료되었습니다.");
                            dataTableApplication.ajax.reload(); //신청현황데이블 새로고침
                        }
                    },
                    error: function (e) {
                        alert("error:" + e);
                    }
                });
            }
        });

        function issueProc() {
            if(!$("#doc_no").val().match(/\(주\)케어랩스\-[0-9]+\-[0-9]{5,}/)) return;
            let data = $('#approval_write').serialize()
            $.ajax({
                url: "<?=base_url()?>humanresource/issueproc",
                type: "POST",
                data: data,
                contentType: "application/json; charset=utf-8",
                success: function (data) {
                    if(data){
                        dataTablePayment.ajax.reload(); // 테이블 데이터 새로고침
                    }
                },
                error: function (e) {
                    alert("error:" + e);
                }
            });
        }

        function getGwData() {
            let data = {
                "doc_no": $("#doc_no").val()
            }
            $.ajax({
                url: "<?=base_url()?>humanresource/getgwdata",
                type: "POST",
                dataType: "json",
                data: JSON.stringify(data),
                contentType: "application/json; charset=utf-8",
                success: function (data) {
                    let hourticket = data.break_balance * 8;
                    $("#seq").val(data.seq);
                    $("#gw_user").val(data.gw_user);
                    $("#user_id").val(data.user_id);
                    $("#user_nickname").val(data.nickname);
                    $("#rep_datetime").val(data.rep_datetime);
                    $("#doc_id").val(data.doc_id);
                    $("#doc_title").val(data.doc_title);
                    $("#hourticket2").val(hourticket);
                    $("#break_balance").val(data.break_balance);
                    $("#hourticket").val(hourticket);
                },
                error: function (e) {
                    alert("error:" + e);
                }
            });
        }

        setDate('#sdate1, #edate1'); // 범위 날짜 선택기 - 결제내역
        setDate('#sdate2, #edate2'); // 범위 날짜 선택기 - 신청현황
        setDate('#sdate4, #edate4'); // 범위 날짜 선택기 - 전체조회
        setDateSingle('#sdate3');//단일 날짜 선택기 - 쿠폰사용

        // 신청현황 - 승인버튼
        function hourticketConfirm(method,seq,type) {
            let msg="";
            if(method==="agree") msg="승인";
            else if(method==="reject") msg="반려";

            if (confirm(msg+'하시겠습니까?')) {
                let data = {
                    "method": method,
                    "seq": seq,
                    "type": type
                }
                $.ajax({
                    url: "<?=base_url()?>humanresource/hourticketconfirm",
                    type: "POST",
                    dataType: "json",
                    data: JSON.stringify(data),
                    contentType: "application/json; charset=utf-8",
                    success: function (data) {
                        if (data) {
                            alert('승인 되었습니다.');
                            dataTableApplication.ajax.reload(); //신청현황데이블 새로고침
                            dataTablePayment.ajax.reload();
                        }
                    },
                    error: function (e) {
                        alert("error:" + e);
                    }
                });
            }
        }

        // 범위 날짜 선택기
        function setDate(selector) {
            $(selector).daterangepicker({
                locale: {
                    "format": 'YYYY-MM-DD',     // 일시 노출 포맷
                    "applyLabel": "확인",                    // 확인 버튼 텍스트
                    "cancelLabel": "취소",                   // 취소 버튼 텍스트
                    "daysOfWeek": ["일", "월", "화", "수", "목", "금", "토"],
                    "monthNames": ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"]
                },
                alwaysShowCalendars: true,                        // 시간 노출 여부
                showDropdowns: true,                     // 년월 수동 설정 여부
                autoApply: true,                         // 확인/취소 버튼 사용여부
                maxDate: new Date(),
                autoUpdateInput: false,
                ranges: {
                    '오늘': [moment(), moment()],
                    '어제': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '지난 일주일': [moment().subtract(6, 'days'), moment()],
                    '지난 한달': [moment().subtract(29, 'days'), moment()],
                    '이번달': [moment().startOf('month'), moment().endOf('month')],
                }
            }, function (start, end, label) {
                // Lets update the fields manually this event fires on selection of range
                startDate = start.format('YYYY-MM-DD'); // selected start
                endDate = end.format('YYYY-MM-DD'); // selected end

                $checkinInput = $(selector.split(',')[0]);
                $checkoutInput = $(selector.split(',')[1]);

                // Updating Fields with selected dates
                $checkinInput.val(startDate);
                $checkoutInput.val(endDate);

                // Setting the Selection of dates on calender on CHECKOUT FIELD (To get this it must be binded by Ids not Calss)
                var checkOutPicker = $checkoutInput.data('daterangepicker');
                checkOutPicker.setStartDate(startDate);
                checkOutPicker.setEndDate(endDate);

                // Setting the Selection of dates on calender on CHECKIN FIELD (To get this it must be binded by Ids not Calss)
                var checkInPicker = $checkinInput.data('daterangepicker');
                checkInPicker.setStartDate($checkinInput.val(startDate));
                checkInPicker.setEndDate(endDate);

            });
        }

        //단일 날짜 선택기
        function setDateSingle(selector) {
            $(selector).daterangepicker({
                singleDatePicker: true,  // 단일 날짜 선택기 활성화
                locale: {
                    "format": 'YYYY-MM-DD',     // 일시 노출 포맷
                    "applyLabel": "확인",                    // 확인 버튼 텍스트
                    "cancelLabel": "취소",                   // 취소 버튼 텍스트
                    "daysOfWeek": ["일", "월", "화", "수", "목", "금", "토"],
                    "monthNames": ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"]
                },
                alwaysShowCalendars: true,                        // 시간 노출 여부
                showDropdowns: true,                     // 년월 수동 설정 여부
                autoApply: true,                         // 확인/취소 버튼 사용여부
                //maxDate: new Date(),
                autoUpdateInput: true,
                ranges: {
                    '오늘': [moment(), moment()],
                    '어제': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '지난 일주일': [moment().subtract(6, 'days'), moment()],
                    '지난 한달': [moment().subtract(29, 'days'), moment()],
                    '이번달': [moment().startOf('month'), moment().endOf('month')],
                }
            });
        }
    </script>
<?= $this->endSection(); ?>

    <!--푸터-->
<?= $this->section('footer'); ?>
<?= $this->endSection(); ?>