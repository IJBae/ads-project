<!-- 통합 DB 관리 - 이전버전 사용하지 않음 -->
 
<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    ZENITH - 통합 DB 관리
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/css/datatables.css" rel="stylesheet">
<script src="/static/node_modules/datatables.net/js/dataTables.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/buttons.html5.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/buttons.colVis.min.js"></script>
<script src="/static/node_modules/datatables.net-staterestore/js/dataTables.stateRestore.min.js"></script>
<script src="/static/node_modules/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
<script src="/static/node_modules/datatables.net-fixedcolumns/js/dataTables.fixedColumns.min.js"></script>
<script src="/static/node_modules/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
<!-- bootstrap5 -->
<link href="/static/node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-staterestore-bs5/css/stateRestore.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-keytable-bs5/css/keyTable.bootstrap5.min.css" rel="stylesheet"> 
<script src="/static/node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
<script src="/static/node_modules/datatables.net-staterestore-bs5/js/stateRestore.bootstrap5.min.js"></script>
<script src="/static/node_modules/datatables.net-fixedheader-bs5/js/fixedHeader.bootstrap5.min.js"></script>
<script src="/static/node_modules/datatables.net-fixedcolumns-bs5/js/fixedColumns.bootstrap5.min.js"></script>
<script src="/static/node_modules/datatables.net-keytable-bs5/js/keyTable.bootstrap5.min.js"></script>
<script src="/static/js/jquery.number.min.js"></script>
<script src="/static/node_modules/jszip/dist/jszip.min.js"></script>
<script src="/static/js/pdfmake/pdfmake.min.js"></script>
<script src="/static/js/pdfmake/vfs_fonts.js"></script>
<style>
    #advertiserBtn{display: none;}
    .memo-all{text-align:right;}
</style>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<?=$this->section('content');?>
<!-- 현재 사용하지 않는 파일 2024.07.02 -->
<!-- DBintegrationDB.php 로 변경 -->
<div class="sub-contents-wrap db-manage-contaniner">
    <div class="title-area">
        <h2 class="page-title">통합 DB 관리</h2>
        <!-- <p class="title-disc">안하는 사람은 끝까지 할 수 없지만, 못하는 사람은 언젠가는 해 낼 수도 있다.</p> -->
    </div>

    <div class="search-wrap">
        <form name="search-form" class="search d-flex justify-content-center">
            <div class="term term-small d-flex align-items-center">
            <input type="hidden" name="sdate" id="sdate"><input type="hidden" name="edate" id="edate">
                <label><input type="text" name="dates" id="dates" data="daterangepicker" autocomplete="off" aria-autocomplete="none"><i class="bi bi-calendar2-week"></i></label>
            </div>
            <div class="input">
                <input type="text" name="stx" id="stx" placeholder="검색어를 입력하세요">
                <button class="btn-primary" id="search_btn" type="submit">조회</button>
            </div>
        </form>
    </div>
    <div class="section reset-btn-wrap">
        <div class="reset-btn-box">
            <button type="button" class="reset-btn">필터 초기화</button>
        </div>
    </div>
    <?php if(getenv('MY_SERVER_NAME') === 'resta' && auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){?>
    <div class="section client-list">
        <h3 class="content-title toggle">
            <i class="bi bi-chevron-up"></i> 분류
        </h3>
        <div class="row" id="company-list"></div>
    </div>
    <?php }?>
    <?php if(auth()->user()->hasPermission('integrate.advertiser')){?>
    <div class="section client-list custom-margin-box-1" <?php if(!auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){echo 'id="advertiserBtn"'; }?>>
        <h3 class="content-title toggle">
            <i class="bi bi-chevron-up"></i> 광고주
        </h3>
        <div class="row" id="advertiser-list"></div>
    </div>
    <?php }?>
    <?php if(auth()->user()->hasPermission('integrate.media')){?>
    <div class="section client-list">
        <h3 class="content-title toggle">
            <i class="bi bi-chevron-up"></i> 매체
        </h3>
        <div class="row" id="media-list"></div>
    </div>
    <?php }?>
    <div class="section client-list">
        <h3 class="content-title toggle">
            <i class="bi bi-chevron-up"></i> 이벤트 구분
        </h3>
        <div class="row" id="event-list"></div>
    </div>

    <div>
        <div class="search-wrap my-5 status_wrap">
            <div class="statusCount detail d-flex minWd"></div>     
        </div>
        <div class="table-responsive">
            <div class="btns-memo-style">
                <span class="btns-title">메모 표시:</span>
                <button type="button" class="btns-memo" value="modal" title="새창으로 표시"><i class="bi bi-window-stack"></i></button>
                <button type="button" class="btns-memo" value="table" title="테이블에 표시"><i class="bi bi-table"></i></button>
                <button type="button" class="btns-memo" value="all" title="한번에 표시"><i class="bi bi-eye"></i></button>
            </div>
            <table class="dataTable table table-default" id="deviceTable">
                <thead class="table-dark">
                    <tr>
                        <th class="first">#</th>
                        <th>SEQ</th>
                        <?php if(getenv('MY_SERVER_NAME') === 'resta' && auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){?>
                        <th>분류</th>
                        <?php }?></php>
                        <th>이벤트</th>
                        <?php if(auth()->user()->hasPermission('integrate.advertiser') || auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){?>
                        <th>광고주</th>
                        <?php }?>
                        <?php if(auth()->user()->hasPermission('integrate.media') || auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){?>
                        <th>매체</th>
                        <?php }?>
                        <th>이벤트 구분</th>
                        <th>이름</th>
                        <th>전화번호</th>
                        <th>나이</th>
                        <th>성별</th>
                        <th>기타</th>
                        <th>사이트</th>
                        <th>등록일</th>
                        <th>메모</th>
                        <th class="last">인정기준</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
        <!-- 개별 메모 -->
        <div class="modal fade" id="modal-integrate-memo" tabindex="-1" aria-labelledby="modal-integrate-memo-label" aria-hidden="true">
            <div class="modal-dialog modal-sm sm-txt">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title" id="modal-integrate-memo-label"><i class="bi bi-file-text"></i> 개별 메모<span class="title"></span></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form class="regi-form">
                            <fieldset>
                                <legend>메모 작성</legend>
                                <textarea></textarea>
                                <button type="button" class="btn-regi">작성</button>
                            </fieldset>
                        </form>
                        <ul class="memo-list m-2"></ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- //개별 메모 -->
    </div>
</div>
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script>
var lead_status = {"1":"인정", "2":"중복", "3":"성별불량", "4":"나이불량", "5":"콜불량", "6":"번호불량", "7":"테스트", "8":"이름불량", "9":"지역불량", "10":"업체불량", "11":"미성년자", "12":"본인아님", "13":"쿠키중복", "99":"확인"};
var exportCommon = {
    'header': true,
    'footer': false,
    'exportOptions': { //{'columns': 'th:not(:last-child)'},
        'modifier': {'page':'all'},
    }
};
var today = moment().format('YYYY-MM-DD');
$('#sdate, #edate').val(today);
setDate();

let dataTable, tableParam = {};
let isPaginationClicked = false;
getList();
function setTableParam() {
    tableParam.searchData = {
        'sdate': $('#sdate').val(),
        'edate': $('#edate').val(),
        'stx': $('#stx').val(),
        'company' : $('#company-list button.active').map(function(){return $(this).val();}).get().join('|'),
        'advertiser' : $('#advertiser-list button.active').map(function(){return $(this).val();}).get().join('|'),
        'media' : $('#media-list button.active').map(function(){return $(this).val();}).get().join('|'),
        'event' : $('#event-list button.active').map(function(){return $(this).val();}).get().join('|'),
        'status' : $('.statusCount dl.active').map(function(){return $('dt',this).text();}).get().join('|')
    };
}
function setSearchData() { //state 에 저장된 내역으로 필터 active 세팅
    var data = tableParam;
    if(typeof data.searchData == 'undefined') return;
    $('#company-list button, #advertiser-list button, #media-list button, #event-list button, .statusCount dl').removeClass('active');
    if(typeof data.searchData.company != 'undefined') {
        data.searchData.company.split('|').map(function(txt){ $(`#company-list button[value="${txt}"]`).addClass('active'); });
    }
    if(typeof data.searchData.advertiser != 'undefined') {
        data.searchData.advertiser.split('|').map(function(txt){ $(`#advertiser-list button[value="${txt}"]`).addClass('active'); });
    }
    if(typeof data.searchData.media != 'undefined') {
        data.searchData.media.split('|').map(function(txt){ $(`#media-list button[value="${txt}"]`).addClass('active'); });
    }
    if(typeof data.searchData.event != 'undefined') {
        data.searchData.event.split('|').map(function(txt){ $(`#event-list button[value="${txt}"]`).addClass('active'); });
    }
    if(typeof data.searchData.status != 'undefined') {
        data.searchData.status.split('|').map(function(txt){
            $('.statusCount dt:contains("'+txt+'")').filter(function() { return $(this).text() === txt;}).parent().addClass('active');
        });
    }
    
    $('#sdate').val(data.searchData.sdate);
    $('#edate').val(data.searchData.edate);
    $('#dates').data('daterangepicker').setStartDate(data.searchData.sdate);
    $('#dates').data('daterangepicker').setEndDate(data.searchData.edate);
    var dates = `${data.searchData.sdate} ~ ${data.searchData.edate}`;
    if(data.searchData.sdate == data.searchData.edate) dates = data.searchData.sdate;
    $('#dates').val(dates);
    $('#stx').val(data.searchData.stx);
    debug('searchData 세팅')
    if(typeof dataTable != 'undefined') dataTable.state.save();
}
function getList(data = []) { //리스트 세팅
    $.fn.DataTable.ext.pager.numbers_length = 10;
    dataTable = $('#deviceTable').DataTable({
        "dom": '<Bfri<t>p>',
        "fixedHeader": true,
        "autoWidth": true,
        "order": [[12,'desc']],
        "processing" : true,
        "serverSide" : true,
        "responsive": true,
        "searching": false,
        "ordering": true,
        "scrollX": true,
        "scrollCollapse": true,
        "stateSave": true,
        "deferRender": true,
        "rowId": "seq",
        "lengthMenu": [[ 25, 10, 50, -1 ],[ '25개', '10개', '50개', '전체' ]],
        "language": {"url": '/static/js/dataTables.i18n.json'}, //한글화 파일
        initComplete : function(settings ,json) {
            let advTable = document.getElementById('deviceTable');
            fadeIn(advTable, 1000); // 1초 동안 페이드 인
        },
        "stateSaveParams": function (settings, data) { //LocalStorage 저장 시
            debug('state 저장')
            data.memoView = $('.btns-memo.active').val();
            data.searchData = tableParam.searchData;
        },
        "stateLoadParams": function (settings, data) { //LocalStorage 호출 시
            debug('state 로드')
            $(`.btns-memo[value="${data.memoView}"]`).addClass('active');
            if(data.memoView == 'table' || data.memoView == 'all') {
                $('.memo-all').show();
            } else {
                $('.memo-all').hide();
            }
            tableParam = data;
        },
        "buttons": [ //Set Button
            {
                'extend': 'collection',
                'text': "Menu",
                'className': 'custom-btn-collection',
                'fade': true,
                'buttons': [
                    'pageLength',
                    'colvis',
                    {
                        'extend':'savedStates',
                        'buttons': [
                            'createState',
                            'removeAllStates'
                        ]
                    },
                    '<div class="export">내보내기</div>',
                    $.extend( true, {}, exportCommon, {
                        extend: 'copyHtml5'
                    } ),
                    $.extend( true, {}, exportCommon, {
                        extend: 'excelHtml5'
                    } ),
                    $.extend( true, {}, exportCommon, {
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        pageSize: 'LEGAL'
                    } ),
                ]
            },
        ],
        "columnDefs": [
            { targets: [0], orderable: false},
            { targets: [1], visible: false},
            { targets: '_all', visible: true },
            { targets: [6], className: ''}
        ],
        "columns": [
            { "data": null , "width": "3%"},
            { "data": "seq" , "width": "5%"},
            <?php if(getenv('MY_SERVER_NAME') === 'resta' && auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){?>
            { 
                "data": null , 
                "width": "35px",
                "render": function(data, type, row) {
                    let company = '';
                    if(row.company == '케어랩스'){
                        company = '케어랩스';
                    }

                    if(row.company == '테크랩스'){
                        company = '테크랩스';
                    }
                    return company;
                }
            },
            <?php }?>
            { "data": "info_seq", "width": "45px",
                "render": function(data) {
                return data?'<a href="<?php echo env('app.eventURL')?>'+data+'" target="event_pop">'+data+'</a>':'';
                }
            },
            <?php if(auth()->user()->hasPermission('integrate.advertiser') || auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){?>
            { "data": "advertiser","width": "100px",
                "render": function(data) {
                    return '<span title="'+$(`<span>${data}</span>`).text()+'">'+(data ? data : '')+'</span>';
                } 
            },
            <?php }?>
            <?php if(auth()->user()->hasPermission('integrate.media') || auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){?>
            { "data": "media" , "width" : "5%",
                "render": function(data) {
                    return '<span title="'+$(`<span>${data}</span>`).text()+'">'+(data ? data : '')+'</span>';
                } 
            },
            <?php }?>
            { "data": "tab_name" },
            { "data": "name", "width": "50px",
                "render": function(data) {
                    return '<span style="display:inline-block;width:50px;max-height:15px;overflow:hidden" title="'+$(`<span>${data}</span>`).text()+'">'+(data ? data : '')+'</span>';
                } 
            },
            { "data": "dec_phone", "width": "90px" },
            { "data": "age", "width": "2%" },
            { "data": "gender", "width": "2%" },
            { "data": "add" },
            { "data": "site", "width": "5%" },
            { "data": "reg_date", "width": "70px" },
            { "data": "memo_cnt", "width": "2%", "className": "memo",
                "render" : function(data) { // data-bs-toggle="modal"
                    var html = '<button class="btn_memo text-dark position-relative" data-bs-target="#modal-integrate-memo"><i class="bi bi-chat-square-text h4"></i>';
                    html += '<span class="position-absolute top--10 start-100 translate-middle badge rounded-pill bg-danger badge-'+data+'">'+data+'</span>';
                    html += '</button>';
                    return html;
                }
            },
            { 
                "data": 'status', "width": "60px",
                "render": function (data, type, row) {
                    <?php if(auth()->user()->hasPermission('integrate.status')){?>
                    return '<select class="lead-status form-select form-select-sm data-del"><option value="1" '+(data=="1"?" selected":"")+'>인정</option><option value="2" '+(data=="2"?" selected":"")+'>중복</option><option value="3" '+(data=="3"?" selected":"")+'>성별불량</option><option value="4" '+(data=="4"?" selected":"")+'>나이불량</option><option value="6" '+(data=="6"?" selected":"")+'>번호불량</option><option value="7" '+(data=="7"?" selected":"")+'>테스트</option><option value="5" '+(data=="5"?" selected":"")+'>콜불량</option><option value="8" '+(data=="8"?" selected":"")+'>이름불량</option><option value="9" '+(data=="9"?" selected":"")+'>지역불량</option><option value="10" '+(data=="10"?" selected":"")+'>업체불량</option><option value="11" '+(data=="11"?" selected":"")+'>미성년자</option><option value="12" '+(data=="12"?" selected":"")+'>본인아님</option><option value="13" '+(data=="13"?" selected":"")+'>쿠키중복</option><option value="99" '+(data=="99"?" selected":"")+'>확인</option></select>';
                    <?php }else{?>
                        return '<span>' + (data == "1" ? '인정' : (data == "2" ? '중복' : (data == "3" ? '성별불량' : (data == "4" ? '나이불량' : (data == "5" ? '콜불량' : (data == "6" ? '번호불량' : (data == "7" ? '테스트' : (data == "8" ? '이름불량' : (data == "9" ? '지역불량' : (data == "10" ? '업체불량' : (data == "11" ? '미성년자' : (data == "12" ? '본인아님' : (data == "13" ? '쿠키중복' : (data == "99" ? '확인' : '')))))))))))))) + '</span>';
                    <?php }?>
                }
            },
        ],
        "rowCallback": function(row, data, index) {
            var api = this.api();
            var totalRecords = api.page.info().recordsTotal;
            var pageSize = api.page.len();
            var currentPage = api.page();
            var totalPages = Math.ceil(totalRecords / pageSize);
            
            var seqNumber = totalRecords - (currentPage * pageSize) - index; // 계산된 순번 (내림차순)
            
            $('td:eq(0)', row).html(seqNumber);
            $(`td.memo .btn_memo .badge`, row).html(data.memo_cnt);
            if($(`.btns-memo[value="all"]`).hasClass('active')) {
                setMemo($('td.memo', row)[0]);
            }
            if(data.status != "1"){
                $(row).addClass('abnormal');
            }
        },
        "infoCallback": function(settings, start, end, max, total, pre){ //페이지현황 세팅
            return "<i class='bi bi-check-square'></i>현재" + "<span class='now'>" +start +" - " + end + "</span>" + " / " + "<span class='total'>" + total + "</span>" + "건";
        },
        "ajax": { //ServerSide Load
            "url": "<?=base_url()?>/integrate/list",
            "data": function(d) {
                if(typeof tableParam != 'undefined')
                    d.searchData = tableParam.searchData;
            },
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
        }
    }).on('preXhr.dt', function (e, settings, data) {
        if (!isPaginationClicked) {
            $('.client-list .row').filter(function(i, el) {
                if(!$(el).find('.zenith-loading').is(':visible')) $(el).prepend('<div class="zenith-loading"/>')
            });
        }
    }).on('xhr.dt', function( e, settings, data, xhr ) { //ServerSide On Load Event
        /* setButtons(data.buttons);
        setLeadCount(data.buttons)
        setStatusCount(data.buttons.status); */
        //setSearchData();
    }).on('draw', function() {
        debug('draw');
        if (!isPaginationClicked) {
            getData('buttons');
        }
        let advTable = document.getElementById('deviceTable');
        fadeIn(advTable, 1000); // 1초 동안 페이드 인
        isPaginationClicked = false;
    }).on('page', function() {
        isPaginationClicked = true;
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


function getData(type) {
    $.ajax({
        type: "GET",
        url: `<?=base_url()?>integrate/${type}`,
        data: {
            "searchData":tableParam.searchData,
        },
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){
            setButtons(data);
            setLeadCount(data)
            setStatusCount(data.status);
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}




$('.btns-memo-style>button').bind('click', function() { //메모 표시타입
    $('.btns-memo-style button').removeClass('active');
    $(this).addClass('active');
    if(this.value == 'table' || this.value == 'all') {
        $('.memo-all').show();
    } else {
        $('.memo-all').hide();
    }
    debug('메모표시방식 설정');
    dataTable.state.save();
});
$('#deviceTable').on('click', 'td.memo', function(e) { //메모셀 클릭 시
    setMemo(this);
});
$(document).on('click', '.regi-form button', function(e) { //메모 작성
    var type = $('.btns-memo.active').val();
    if(!$.trim($('textarea', $(this).parents('.regi-form')).val())) {
        alert('메모 내용을 입력해주세요.');
        return false;
    }
    var data = {
        'memo': $('textarea', $(this).parents('.regi-form')).val()
    };
    if(type == 'table') {
        data['leads_seq'] =  $(this).parents('tr').prev('tr').attr('id');
    } else {
        data['leads_seq'] = $('#modal-integrate-memo').attr('data-seq');
    }
    registerMemo(data);
});
$('#modal-integrate-memo')
    .on('show.bs.modal', function(e) { //create memo data
        var seq = $(this).attr('data-seq');
        var $row = dataTable.row($(`#${seq}`));
        var name = $row.data().name;
        $('h1 .title', this).html(name);
        $('.memo-list', this).html('');
        getMemoList(seq);
    })
    .on('hidden.bs.modal', function(e) { //modal Reset
        $(this).removeAttr('data-seq');
        $('.memo-list, h1 .title', '#modal-integrate-memo').html('');
        $('#modal-integrate-memo form')[0].reset();
    });
function setMemo(t) {
    var type = $('.btns-memo.active').val();
    var tr = $(t).closest('tr');
    var row = dataTable.row(tr);
    var seq = row.data().seq;
    if(type == 'table' || type == 'all') {
        if (row.child.isShown()) { // 메모가 열려있을 때
            row.child.hide();
        } else { // 메모가 닫혀있을 때
            var html = '<form class="regi-form"><fieldset><legend>메모 작성</legend><textarea></textarea><button type="button" class="btn-regi">작성</button></fieldset></form>';
            html += '<ul class="memo-list">';
            row.child(html).show();
            getMemoList(seq, t);
        }
    } else {
        dataTable.rows().every(function(){ //모든 메모 닫음
            if(this.child.isShown()) this.child.hide();
        });
        $('#modal-integrate-memo').attr('data-seq', seq).modal('show'); //modal 호출
    }
}
function getMemoList(seq, obj) { //메모 수신
    if($(`.badge`, obj).text() < 1) return;
    $.ajax({
        type: "get",
        url: "<?=base_url()?>integrate/getmemo",
        data: {'seq': seq},
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            setMemoList(data);
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}
function registerMemo(data) { //메모 등록
    $.ajax({
        type: "post",
        url: "<?=base_url()?>integrate/addmemo",
        data: data,
        dataType: "json",
        success: function(response){  
            if(response.result == true) {
                setMemoList(response.data);
                var cnt = parseInt($(`tr[id="${response.data[0].leads_seq}"] td .btn_memo .badge`).text()) || 0;
                $(`tr[id="${response.data[0].leads_seq}"] td .btn_memo .badge`).removeClass('badge-0').text(++cnt); //뱃지 변경
            }
            $('.regi-form textarea').val('');
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}
function setMemoList(data) { //메모 리스트 생성
    if(typeof data[0] == "undefined") return;
    var seq = data[0].leads_seq;
    var html =  '';
    $.each(data, function(i,row) {
        html += '    <li class="d-flex justify-content-between align-items-start">';
        html += '        <div class="detail d-flex align-items-start">';
        html += '            <p class="ms-1">'+ row.memo +'</p>';
        html += '        </div>';
        html += '        <div class="info">';
        html += '            <i>'+ row.username +'</i>';
        html += '            <span>'+ row.reg_date +'</span>';
        html += '        </div>';
        html += '    </li>';
    });
    var wrap;
    var type = $('.btns-memo.active').val();
    if(type == 'table' || type == 'all') {
        wrap = $(`#${seq}`).next('tr').find('.memo-list');
    } else {
        wrap = $(`[data-seq="${seq}"] .memo-list`);
    }
    wrap.prepend(html);
}
function setLeadCount(data) { //Filter Count 표시
    $('.client-list').find('.zenith-loading').fadeOut(function(){$(this).remove();});
    $('.client-list button').removeClass('on');
    $('.client-list .col .txt').empty();
    $.each(data, function(type, row) {
        if(type == 'status') return true;
        var $container = $('#'+type+'-list');
        $.each(row, function(idx, v) {
            var cnt_txt = v.total;
            if(v.count != v.total) cnt_txt = v.count + "/" + v.total;
            button = $(`#${type}-list .col[data-name="${v.label}"] button`);
            button.siblings('.progress').children('.txt').text(`${cnt_txt}`);
            
            <?php if(getenv('MY_SERVER_NAME') === 'resta' && auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){?>
                if(typeof tableParam.searchData == 'undefined' || (tableParam.searchData.company == "" && tableParam.searchData.advertiser == "" && tableParam.searchData.media == "" && tableParam.searchData.event == "")) return true;
            <?php }else{?>
                if(typeof tableParam.searchData == 'undefined' || (tableParam.searchData.advertiser == "" && tableParam.searchData.media == "" && tableParam.searchData.event == "")) return true;
            <?php }?>
            if(v.count) button.addClass('on');
        });
    });
    setSearchData();
}

function setStatusCount(data){ //상태 Count 표시
    $('.client-list').find('.zenith-loading').fadeOut(function(){$(this).remove();});
    $('.statusCount').empty();
    $.each(data, function(key, value) {
        // $('.statusCount').append('<dl class="col"><dt>' + key + '</dt><dd>' + value + '</dd></dl>');
        const setDataBtn = $('<dl class="col"><dt>' + key + '</dt><dd>' + value + '</dd></dl>'); // 숨긴 상태로 생성
        $('.statusCount').append(setDataBtn); // DOM에 추가
        setDataBtn.hide().fadeIn(1000); // 페이드 인 효과

    });
    setSearchData();
}
function fontAutoResize() { //.client-list button 항목 가변폰트 적용
    $('.client-list .col').each(function(i, el) {
        var $el = $(el);
        var button = $('button', el);
        button.css({
            'white-space': 'nowrap',
            'overflow-x': 'auto',
            'font-size': '85%'
        });
        var i = 0;
        var btn_width = Math.round(button.width());
        // debug(button.val(), btn_scr_w, btn_width);
        while((button[0].scrollWidth+10) / 2 >= btn_width) {
            var size = parseFloat(button.css('font-size')) / 16 * 100;
            button.css({'font-size': --size+'%'});
            // debug(button.css('font-size'), size)
            if(button.css('font-size') < 8 || i > 60 || size < 70) break;
            i++;
        }
        button.css({
            'white-space': 'normal',
            'overflow-x': 'auto'
        });
    });
}
$(window).resize(function() {
    fontAutoResize();
});

function setButtons(data) { //광고주,매체,이벤트명 버튼 세팅   
    $('.client-list').find('.zenith-loading').fadeOut(function(){
        $(this).remove();
    });    
    $.each(data, function(type, row) {
        if(type == 'status') return true;
        <?php if(!auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){?>
                if(type == 'advertiser'){
                    if(data.advertiser.length > 1){
                        $('#advertiserBtn').show();
                    }else{
                        $('#advertiserBtn').hide();
                        return true;
                    }
                } 
        <?php }?>
        
        var html = "";
        $.each(row, function(idx, v) {
            html += '<div class="col" data-name="'+v.label+'"><div class="inner">';
            html += '<button type="button" value="'+v.label+'">' + v.label + '</button>';
            html += '<div class="progress">';
            html += '<div class="txt">'+v.count+'/'+v.total+'</div>';
            html += '</div>';
            html += '</div></div>';
        });
        const setButtonsList = $('#'+type+'-list').html(html);
        setButtonsList.hide().fadeIn(1000);
    });
    fontAutoResize();
    setSearchData();
}
//날짜
function setDate(){
    //$('#sdate, #edate').val(today);
    $('#dates').daterangepicker({
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
            오늘: [moment(), moment()],
            어제: [
                moment().subtract(1, "days"),
                moment().subtract(1, "days"),
            ],
            최근7일: [
                moment().subtract(7, "days").startOf("day"),
                moment().subtract(1, "days").endOf("day")
            ],
            최근14일: [
                moment().subtract(14, "days").startOf("day"),
                moment().subtract(1, "days").endOf("day")
            ],
            이번주: [moment().day(1), moment()],
            지난주: [
                moment().add(-1, "week").startOf("week").day(1),
                moment().add(-1, "week").startOf("week").add(7, "day"),
            ],
            이번달: [moment().startOf("month"), moment().endOf("month")],
            지난달: [
                moment().subtract(1, "month").startOf("month"),
                moment().subtract(1, "month").endOf("month"),
            ],
            한달전: [moment().subtract(1, "month"), moment()],
        },
    }, function(start, end, label) {
        startDate = start.format('YYYY-MM-DD'); // selected start
        endDate = end.format('YYYY-MM-DD'); // selected end
        $sdateInput = $('#sdate');
        $edateInput = $('#edate');
        $sdateInput.val(startDate);
        $edateInput.val(endDate);
        var dates = `${startDate} ~ ${endDate}`;
        if(startDate == endDate) dates = startDate;
        $('#dates').val(dates);
    });
}

$('body').on('click', '#company-list button, #advertiser-list button, #media-list button, #event-list button', function() {
    $(this).toggleClass('active');
    debug('필터링 탭 클릭');
    setTableParam();
    dataTable.draw();
});

$('form[name="search-form"]').bind('submit', function() {
    debug('검색 전송');
    setTableParam();
    dataTable.draw();
    return false;
});

$('.statusCount').on('click', 'dl', function(e) {
    debug('인정기준 필터')
    $(this).toggleClass('active');
    setTableParam();
    dataTable.draw();
});

$('body').on('click', '.reset-btn', function() {
    var today = moment().format('YYYY-MM-DD');
    $('#sdate, #edate').val(today);
    $('#company-list button, #advertiser-list button, #media-list button, #event-list button, .statusCount dl').removeClass('active');
    $('#stx').val('');
    tableParam.searchData = {
        'sdate': $('#sdate').val(),
        'edate': $('#edate').val(),
        'stx': $('#stx').val(),
        'company' : '',
        'advertiser' : '',
        'media' : '',
        'event' : '',
        'status' : ''
    };
    dataTable.state.clear();
    dataTable.state.save();
    dataTable.order([12, 'desc']).draw();
});
// 인정기준 변경처리
function setStatus(t) {
    var oldvalue = $(t).attr('data-oldvalue');
    var data = {
        'seq': $(t).closest('tr').attr('id'),
        'oldstatus' : oldvalue,
        'status' : t.value
    };
    $.ajax({
        type: "post",
        url: "<?=base_url()?>/integrate/setstatus",
        data: data,
        dataType: "json",
        success: function(response){  
            if(response.result == true) {
                var r = response.data;
                var data = {
                    'seq' : r.seq,
                    'memo' : `"${lead_status[r.oldstatus]}"에서 "${lead_status[r.status]}"(으)로 상태변경`
                };
                var $o_obj = $('.statusCount dt:contains("'+lead_status[r.oldstatus]+'")').filter(function() { return $(this).text() === lead_status[r.oldstatus];}).next('dd');
                var o_cnt = parseInt($o_obj.text());
                $o_obj.text(--o_cnt);
                var $n_obj = $('.statusCount dt:contains("'+lead_status[r.status]+'")').filter(function() { return $(this).text() === lead_status[r.status];}).next('dd');
                var n_cnt = parseInt($n_obj.text());
                $n_obj.text(++n_cnt);
                data.leads_seq = data.seq;
                registerMemo(data);
            }
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}
$('#deviceTable')
    .on('focus click', '.lead-status', function(e) {
        $(this).attr('data-oldvalue', this.value);
    })
    .on('change', '.lead-status', function(e) {
        setStatus(this);
    });
function debug(msg) {
    //console.log(msg);
}

</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>