<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    ZENITH - 통합 DB 관리
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/css/datatables.css" rel="stylesheet">
<link href="/static/node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet">  
<link href="/static/node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css" rel="stylesheet"> 
<script src="/static/node_modules/datatables.net/js/dataTables.min.js"></script>
<script src="/static/node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
<script src="/static/node_modules/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
<script src="/static/node_modules/datatables.net-fixedheader-bs5/js/fixedHeader.bootstrap5.min.js"></script>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<?=$this->section('content');?>
<div class="sub-contents-wrap align-items-center automationContent" id="automationContent">
    <div class="title-area mb0">
        <h2 class="page-title">자동화 목록</h2>
    </div>
    <!-- 매체 카테고리 -->
    <!-- <div class="section client-list media mt50">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 매체</h3>
        <div class="row">
            <div class="col">
                <div class="inner"><button type="button" value="facebook" data-btn="media" class="media_btn"><span>페이스북</span></button></div>
            </div>
            <div class="col">
                <div class="inner"><button type="button" value="kakao" data-btn="media" class="media_btn"><span>카카오</span></button></div>
            </div>
            <div class="col">
                <div class="inner"><button type="button" value="google" data-btn="media" class="media_btn"><span>구글</span></button></div>
            </div>
        </div>
    </div> -->
    <!-- 매체 카테고리 -->

    <!-- 임시주석 2024.07.11 -->
    <!-- <div class="search-wrap type-single">
        <form name="search-form" class="search">
            <div class="input align-center">
                <button class="btn-special createBtn" type="button" data-bs-toggle="modal" data-bs-target="#automationModal">작성하기</button>
                <button class="btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#logModal">전체 로그 보기</button>
            </div>
        </form>
    </div> -->
    <!-- //임시주석 2024.07.11 -->

    <div class="section mt0">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-default" id="automation-table">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">제목</th>
                        <th scope="col">작성자</th>
                        <th scope="col">업데이트</th>
                        <th scope="col">마지막 실행</th>
                        <th scope="col">예상 실행 시간</th>
                        <th scope="col">사용</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal logModal fade" id="logModal" tabindex="-1" aria-labelledby="logModal" aria-hidden="true">
    <input type="hidden" name="log_seq">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="logModal-tit"> <span></span>감사 로그</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="search-wrap log-search-wrap">
                    <form name="log-search-form" class="search">
                        <div class="term term-small d-flex align-items-center">
                            <input type="hidden" name="sdate" id="log_sdate"><input type="hidden" name="edate" id="log_edate">
                            <label><input type="text" name="dates" id="log_dates" data="daterangepicker" autocomplete="off" aria-autocomplete="none"><i class="bi bi-calendar2-week"></i></label>
                        </div>
                        <div class="input">
                            <button class="btn-primary" id="search_btn" type="submit">조회</button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-default logTable" id="logTable">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">제목</th>
                                <th scope="col">작성자</th>
                                <th scope="col">결과</th>
                                <th scope="col">마지막 실행</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?=$this->include('templates/inc/automation_create_modal.php')?>
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script src="/static/js/automation/automation.js"></script>
<script>
let data = {};
let dataTable;

getList();

function getList(){
    $.fn.DataTable.ext.pager.numbers_length = 10;
    dataTable = $('#automation-table').DataTable({
        "dom": '<<"search-control-box"fB>r<t>ip>',
        
        "autoWidth": false,
        "fixedHeader": true,
        "order": [[2,'desc']],
        "processing" : true,
        "serverSide" : false,
        "responsive": true,
        "searching": true,
        "ordering": true,
        "scrollX": true,
        // "scrollY": 500,
        "scrollCollapse": true,
        "deferRender": true,
        "rowId": "seq",
        "lengthMenu": [[ 25, 10, 50, -1 ],[ '25개', '10개', '50개', '전체' ]],
        "buttons":[
            {text: '<span class="btn btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#automationModal">작성하기</span>'},
            {text: '<span class="btn btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#logModal">전체 로그 보기</span>'}
        ],
        "ajax": {
            "url": "<?=base_url()?>/automation/list",
            "data": function(d) {
            },
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
        },
        "columnDefs": [
            { "className": "dt-type-center", "targets": [2, 3, 4, 5] } // 특정 열 가운데 정렬
        ],
        "columns": [
            { 
                "data": "aa_subject",
                "width": "20%",
                "render": function(data){
                    subject = '<button type="button" data-bs-toggle="modal" data-bs-target="#automationModal" class="updateBtn">'+data+'</button>';
                    return subject;
                }
            },
            { 
                "data": "aa_nickname", 
                "width": "15%",
            },
            { 
                "data": "aa_mod_datetime", 
                "width": "15%",
                "render": function(data){
                    if(data != null){
                        data = data.substr(0, 16);
                    }else{
                        data = null;
                    }

                    return data;
                }
            },
            { 
                "data": "aar_exec_timestamp_success", 
                "width": "15%",
                "render": function(data){
                    if(data != null){
                        data = data.substr(0, 16);
                    }else{
                        data = null;
                    }

                    return data;
                }
            },
            { 
                "data": "expected_time", 
                "width": "15%",
                "render": function(data){
                    if(data != null){
                        data = data.substr(0, 16);
                    }else{
                        data = null;
                    }

                    return data;
                }
            },
            { 
                "data": "aa_status", 
                "width": "20%",
                "render": function(data, type, row){
                    checked = data == 1 ? 'checked' : '';
                    var status = '<div class="td-inner"><div class="ui-toggle"><input type="checkbox" name="status" id="status_' + row.aa_seq + '" ' + checked + ' value="'+row.aa_seq+'"><label for="status_' + row.aa_seq + '">사용</label></div><div class="more-action"><button type="button" class="btn-more"><span>더보기</span></button><ul class="action-list z-1"><li><a href="#" data-seq="' + row.aa_seq + '" class="log-btn" data-bs-target="#logModal" data-bs-toggle="modal">로그보기</a></li><li><a href="#" data-seq="' + row.aa_seq + '" class="copy-btn">복제하기</a></li><li><a href="#" data-seq="' + row.aa_seq + '" class="delete-btn">제거하기</a></li></ul></div></div>';

                    return status;
                }
            },
        ],
        "createdRow": function(row, data, dataIndex) {
            $(row).attr("data-id", data.aa_seq);
        },
        "language": {
            "url": '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        },
        "initComplete": function(settings, json) {
            // fadeIn 효과 적용 $('#automation-table')
            let advTable = document.getElementById('automation-table');
            fadeIn(advTable, 1000); // 1초 동안 페이드 인
        },
        "infoCallback": function(settings, start, end, max, total, pre){
            return "<i class='bi bi-check-square'></i>현재" + "<span class='now'>" +start +" - " + end + "</span>" + " / " + "<span class='total'>" + total + "</span>" + "건";
        },
    });
}
//테이블 페이드인 효과
function fadeIn(tableName, duration) {
    let opacity = 0;
    tableName.style.opacity = 0;
    tableName.style.display = "table";

    const jsInterval = 50;
    const increment = jsInterval / duration;
    const jsFade = setInterval(() => {
        opacity += increment;
        tableName.style.opacity = opacity;

        if(opacity >= 1) {
            clearInterval(jsFade)
        }
    }, jsInterval);
}

function setDate(){
    $('#log_sdate, #log_dates').val(moment().format('YYYY-MM-DD')); // 오늘 날짜를 입력 필드에 설정
    $('#log_edate').val(moment().format('YYYY-MM-DD')); // 오늘 날짜를 입력 필드에 설정
    $('[data="daterangepicker"]').daterangepicker({
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
    }, function(start, end, label) {
        // Lets update the fields manually this event fires on selection of range
        startDate = start.format('YYYY-MM-DD'); // selected start
        endDate = end.format('YYYY-MM-DD'); // selected end

        $sdateInput = $('[name="sdate"]', this.element.parentNode);
        $edateInput = $('[name="edate"]', this.element.parentNode);

        $sdateInput.val(startDate);
        $edateInput.val(endDate);
        var dates = `${startDate} ~ ${endDate}`;
        if(startDate == endDate) dates = startDate;
        $(this.element).val(dates);
    
    });
}

function setAutomationStatus(data){
    $.ajax({
        type: "put",
        url: "<?=base_url()?>/automation/set-status",
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data) {
            if (data != true) {
                alert('오류가 발생하였습니다.');
            } 
        },
        error: function(error, status, msg) {
            alert("상태코드 " + status + "에러메시지" + msg);
        }
    });
}

//검색
$('form[name="search-form"]').bind('submit', function() {
    dataTable.draw();
    return false;
});

//리스트 더보기 버튼
$('#automation-table').on('click', '.btn-more', function () {
    var seq = $(this).data('seq');
    var currentActionList = $(this).closest('.more-action').find('.action-list');
    $('.action-list').not(currentActionList).fadeOut(0);
    currentActionList.fadeToggle();
});

//status 변경
$('body').on('change', '.ui-toggle input[name=status]', function() {
    var isChecked = $(this).is(':checked');
    var seq = $(this).val();
    var status = isChecked ? 1 : 0;
    
    data = {
        'seq' : seq,
        'status' : status
    };
    setAutomationStatus(data);
});

//복제하기
$('body').on('click', '.copy-btn', function() {
    let seq = $(this).data('seq');
    $.ajax({
        type: "POST",
        url: "<?=base_url()?>/automation/copy",
        data: {'seq': seq},
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            if(data == true){
                dataTable.ajax.reload();
                $('#automationModal').modal('hide');
            }
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
});

//삭제하기
$('body').on('click', '.delete-btn', function() {
    let seq = $(this).data('seq');
    $.ajax({
        type: "DELETE",
        url: "<?=base_url()?>/automation/delete",
        data: {'seq': seq},
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            if(data == true){
                dataTable.ajax.reload();
                $('#automationModal').modal('hide');
            }
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
});
//로그
function getLogs($seq = null){
    $.fn.DataTable.ext.pager.numbers_length = 10;
    logTable = $('#logTable').DataTable({
        "destroy": true,
        "autoWidth": false,
        "processing" : true,
        "serverSide" : false,
        "responsive": false,
        "searching": false,
        "ordering": true,
        "order": [[3,'desc']],
        "deferRender": true,
        'lengthChange': true,
        'pageLength': 10,
        "scrollX": true,
        "info": false,
        "ajax": {
            "url": "<?=base_url()?>/automation/logs",
            "data": function(d) {
                $.extend(d, {
                    dates: {
                        'sdate': $('#log_sdate').val(),
                        'edate': $('#log_edate').val()
                    },
                    seq: $('input[name=log_seq]').val()
                });
            },
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
            "dataSrc": function(res){
                return res.data;
            }
        },
        "columnDefs": [
            { "className": "dt-type-center", "targets": [0, 1, 2, 3] } // 특정 열 가운데 정렬
        ],
        "columns": [
            { "data": "subject"},
            { "data": "nickname", width:"15%"},
            { 
                "data": "result",
                "width": "15%",
                "render": function(data, type, row){
                    let result;
                    if(data == '실행됨'){
                        result = '<b class="em">'+data+'</b>';
                    }else if(data == '실패'){
                        result = '<b class="fail">'+data+'</b>';
                    }else{
                        result = data;
                    }

                    return result;
                }
            },
            { "data": "exec_timestamp", "width":"15%"},
        ],
        "createdRow": function(row, data, dataIndex) {
            $(row).attr("data-id", data.id);
            //감사로그정보
            let detailRow = `<div class="detail-log-wrap">
                <h2>작업 세부 정보</h2>
                <div class="detail-log">
                    <dl class="log-item mb-3">
                        <dt class="mb-1">일정</dt>
                        <dd>${data.schedule_desc ? data.schedule_desc : ""}</dd>
                    </dl>
                    <dl class="log-item mb-3">
                        <dt class="mb-1">대상</dt>
                        <dd>${data.target_desc ? data.target_desc : ""}</dd>
                    </dl>
                    <dl class="log-item mb-3">
                        <dt class="mb-1">조건</dt>
                        <dd>${data.conditions_desc ? data.conditions_desc : ""}</dd>
                    </dl>
                    <dl class="log-item">
                        <dt class="mb-1">실행</dt>
                        <dd>${data.executions_desc != null ? (Array.isArray(data.executions_desc) ? data.executions_desc.join('<br>') : data.executions_desc) : ""}</dd>
                    </dl>
                </div>
            </div>`;
            logTable.row(row).child(detailRow).hide();
        },
        "language": {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        },
        "initComplete": function(settings, json) {
            // 열 너비 강제 설정
            $('#logTable').find('th, td').css('width', '');
        }
    });
}
//모달 보기
$('#logModal').on('show.bs.modal', function(e) {
    let $btn = $(e.relatedTarget);
    setDate();
    if ($btn.hasClass('log-btn')) {
        let seq = $btn.attr('data-seq');
        $('input[name=log_seq]').val(seq);
        let title = $btn.closest('tr').find('.updateBtn').text();
        $('#logModal .modal-header h1 span').text(title+" - ");
        getLogs(seq);
    }else{      
        getLogs();
    }
    
})//모달 닫기
.on('hidden.bs.modal', function(e) { 
    $('input[name=log_stx]').val('');
    $('input[name=log_seq]').val('');
    logTable = $('#logTable').DataTable();
    logTable.destroy();
});

$('form[name="log-search-form"]').bind('submit', function() {
    logTable.ajax.reload();
    return false;
});

$('body').on('click', '#logModal tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = logTable.row(tr);

    if ($(this).hasClass('selected')) {
        $(this).removeClass('selected');
        row.child.hide();
        tr.removeClass('shown');
    }else {
        $(this).addClass('selected');
        row.child.show();
        tr.addClass('shown');
    }
});

</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>