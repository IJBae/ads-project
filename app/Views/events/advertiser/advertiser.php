<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    ZENITH - 이벤트 / 광고주 관리
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/css/datatables.css" rel="stylesheet">
<link href="/static/node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-staterestore-bs5/css/stateRestore.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css" rel="stylesheet"> 
<script src="/static/node_modules/datatables.net/js/dataTables.min.js"></script>
<script src="/static/node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
<script src="/static/node_modules/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
<script src="/static/node_modules/datatables.net-fixedheader-bs5/js/fixedHeader.bootstrap5.min.js"></script>
<style>
    .ui-autocomplete{
        z-index: 10000000;
        max-height: 300px;
        overflow-y: auto; /* prevent horizontal scrollbar */
        overflow-x: hidden;
    }
        
    hr{
        display: block !important;
    }

    .ui-widget{
        font-family: "NanumSquareNeo", "Noto Sans", dotum, Gulim, sans-serif;
    }
</style>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<div class="sub-contents-wrap eventmanage-container">
    <div class="title-area">
        <h2 class="page-title">광고주 관리</h2>
    </div>

    <div class="search-wrap">
        <form name="search-form" class="search d-flex justify-content-center">
            <div class="input">
                <input type="text" name="stx" id="stx" placeholder="검색어를 입력하세요">
                <button class="btn-primary" id="search_btn" type="submit">조회</button>
                <button class="btn-special ms-2" id="createBtn" data-bs-toggle="modal" data-bs-target="#clientModal" type="button">등록</button>
            </div>
        </form>
    </div>

    <div class="section position-relative">
        <div class="btn-wrap">
            <a href="/eventmanage/event"><button type="button" class="btn btn-outline-danger">이벤트 관리</button></a>
            <a href="/eventmanage/media"><button type="button" class="btn btn-outline-danger">매체 관리</button></a>
            <a href="/eventmanage/change"><button type="button" class="btn btn-outline-danger">전환 관리</button></a>
            <a href="/eventmanage/blacklist"><button type="button" class="btn btn-outline-danger">블랙리스트 관리</button></a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-default" id="advertiser-table">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">광고주명</th>
                        <th scope="col">매출</th>
                        <th scope="col">남은잔액</th>
                        <th scope="col">랜딩수</th>
                        <th scope="col">유효DB</th>
                        <th scope="col">사업자명</th>
                        <th scope="col">외부연동</th>
                        <th scope="col">개인정보<br>전문</th>
                        <th scope="col">사용여부</th>
                        <th scope="col">작성일</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?=$this->endSection();?>

<?=$this->section('modal');?>
<!-- 광고주 등록 -->
<div class="modal fade" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="clientModalLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form name="adv-register-form" id="adv-register-form">
                <div class="table-responsive">
                    <input type="hidden" name="seq" value="">
					<input type="hidden" name="checkname" value="">
                    <input type="hidden" name="watch_list" value="">
                    <table class="table table-bordered table-left-header" id="modalTable">
                        <colgroup>
                            <col style="width:30%;">
                            <col style="width:70%;">
                        </colgroup>
                        <tbody>
                            <tr>
                                <th scope="row" class="text-end">광고주명</th>
                                <td>
                                    <input type="hidden" name="company_id">
                                    <input type="text" class="form-control" name="name" placeholder="광고주명을 입력하세요." id="adv-name-input" title="광고주" autocomplete="off" <?php 
                                    if(!auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){
                                        echo "readonly disabled";
                                    };
                                    ?>>
                                    <p class="mt-2 text-secondary">※ 한번 등록 된 광고주는 수정이 불가능합니다. 띄어쓰기, 오타 확인 꼭 해주세요.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">수집주체(사업자명)</th>
                                <td>
                                    <input type="text" class="form-control" name="agent" placeholder="수집주체(사업자명)를 입력하세요." title="수집주체(사업자명)">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">홈페이지 주소</th>
                                <td><input type="text" class="form-control" name="homepage_url" placeholder="홈페이지 주소를 입력하세요." title="홈페이지 주소"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">외부연동 주소</th>
                                <td><input type="text" class="form-control" name="interlock_url" placeholder="외부연동 주소를 입력하세요." title="외부연동 주소"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">개인정보 전문 주소</th>
                                <td><input type="text" class="form-control" name="agreement_url" placeholder="개인정보 전문 주소를 입력하세요." title="개인정보 전문 주소"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">입금액</th>
                                <td><input type="text" class="form-control" name="account_balance" placeholder="광고주 입금액을 입력해주세요." title="광고주 입금액"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">사용여부</th>
                                <td>
                                    <div class="d-flex radio-wrap">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="is_stop" value="0" id="is_stop01" checked>
                                            <label class="form-check-label" for="is_stop01">사용</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="is_stop" value="1" id="is_stop02">
                                            <label class="form-check-label" for="is_stop02">사용중지</label>
                                        </div>
                                    </div>
                                    <p class="text-secondary">※ 사용중지로 변경할 경우 해당 광고주의 모든 랜딩이 중지됩니다.</p>
                                </td>
                            </tr>
                            <tr class="ow_update">
                                <th scope="row" class="text-end">문자 알림 사용여부</th>
                                <td>
                                    <div class="d-flex radio-wrap">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="sms_alert" value="1" id="sms_radio01">
                                            <label class="form-check-label" for="sms_radio01">사용</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="sms_alert" value="0" id="sms_radio02">
                                            <label class="form-check-label" for="sms_radio02">미사용</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="ow_info ow_update">
                                <th scope="row" class="text-end">Notice</th>
                                <td>
                                    <ul>
                                        <li>* 00시 ~ 06시에는 문자를 발송하지 않습니다.</li>
                                        <li>* 알림 문자는 매체별로 1일 1회 발송됩니다.</li>
                                        <li>* 기타 문의사항은 [개발팀-정문숙]에게 문의 부탁드립니다.</li>
                                        <li>* 전체값 입력시 매체별 db수량 입력과 문자발송이 비활성화됩니다.</li>
                                    </ul>
                                </td>
                            </tr>
                            <tr class="ow_info ow_update">
                                <th scope="row" class="text-end">알림 연락처</th>
                                <td>
                                    <input type="text" class="form-control mb-2" name="contact[]" id="contact_0" placeholder="숫자만 입력해주세요">
                                    <input type="text" class="form-control mb-2" name="contact[]" id="contact_1" placeholder="숫자만 입력해주세요">
                                    <input type="text" class="form-control" name="contact[]" id="contact_2" placeholder="숫자만 입력해주세요">
                                </td>
                            </tr>
                            <tr class="ow_info ow_update">
                                <th scope="row" class="text-end">전체</th>
                                <td>
                                    <input type="text" class="form-control mb-2" name="watch_all" placeholder="숫자만 입력해주세요">
                                    <p class="text-danger" id="watch_all_txt">전체값 입력시 매체별 db수량 입력과 문자발송이 비활성화됩니다.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>                    
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="create-btn-wrap">
                    <button type="submit" class="btn btn-primary" form="adv-register-form" id="createActionBtn">생성</button>
                </div>
                <div class="update-btn-wrap">
                    <button type="submit" class="btn btn-primary" form="adv-register-form" id="updateActionBtn">수정</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- //광고주 등록 -->
<!-- 이벤트 랜딩보기 -->
<div class="modal fade" id="agreementModal" tabindex="-1" aria-labelledby="agreementModalLabel"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="agreementModalLabel">개인정보전문</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe src="" id="agreementContent" width="100%" height="700"></iframe>
            </div>
        </div>
    </div>
</div>
<!-- //이벤트 랜딩보기 -->
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script>
let data = {};
let dataTable;

getList();

function setData() {
    data = {
        'stx': $('#stx').val(),
    };

    return data;
}

function getList(){
    $.fn.DataTable.ext.pager.numbers_length = 10;
    dataTable = $('#advertiser-table').DataTable({
        "dom": '<r<t>ip>',
        "order": [[0,'desc']],
        "fixedHeader": true,
        "autoWidth": true,
        "processing" : true,
        "serverSide" : true,
        "responsive": true,
        "searching": false,
        "ordering": true,
        "scrollX": true,
        // "scrollY": 500,
        "scrollCollapse": true,
        "deferRender": true,
        "rowId": "seq",
        "lengthMenu": [[ 25, 10, 50, -1 ],[ '25개', '10개', '50개', '전체' ]],
        "ajax": {
            "url": "<?=base_url()?>/eventmanage/advertiser/list",
            "data": function(d) {
                d.searchData = setData();
            },
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
        },
        "columns": [
            { "data": null, "width": "3%" },
            { 
                "data": "name", 
                "width": "15%",
                "render": function(data, type, row) {
                    return '<button type="button" id="updateBtn" data-bs-toggle="modal" data-bs-target="#clientModal">'+data+'</button>';
                }
            },
            { "data": "sum_price", "width": "8%"},
            { "data": "remain_balance","width": "8%"},
            { 
                "data": "total", 
                "width": "5%",
                "render": function(data, type, row) {
                    if(data == 0 || data == null){
                        total = '';
                    }else{
                        total = '<button id="totalBtn" data-name="'+row.name+'">'+data+'</button>';
                    }

                    return total;
                }
            },
            { 
                "data": "sum_db", 
                "width": "5%",
                "render": function(data, type, row) {
                    if(data == 0 || data == null){
                        sum_db = '';
                    }else{
                        sum_db = '<button id="sumDB" data-name="'+row.name+'">'+data+'</button>';
                    }

                    return sum_db;
                }
            },
            { "data": "agent","width": "15%"},
            { "data": "interlock_url","width": "5%"},
            { 
                "data": "agreement_url_exist",
                "width": "5%",
                "render": function(data, type, row) {
                    if(!data || data == null){
                        agreement_url = '';
                    }else{
                        agreement_url = '<button type="button" id="agreement_btn" data-bs-toggle="modal" data-bs-target="#agreementModal" data-link="'+row.agreement_url+'">'+data+'</button>';
                    }

                    return agreement_url;
                }
            },
            { "data": "is_stop","width": "5%"},
            { 
                "data": "ea_datetime", 
                "width": "10%",
                "render": function(data){
                    return data.substr(0, 10);
                }
            }
        ],
        "language": {
            "url": '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        },
        "initComplete": function(settings, json) {
            // fadeIn 효과 적용 $('#advertiser-table')
            let advTable = document.getElementById('advertiser-table');
            fadeIn(advTable, 1000); // 1초 동안 페이드 인
        },
        "rowCallback": function(row, data, index) {
            var api = this.api();
            var totalRecords = api.page.info().recordsTotal;
            var pageSize = api.page.len();
            var currentPage = api.page();
            var totalPages = Math.ceil(totalRecords / pageSize);
            
            var seqNumber = totalRecords - (currentPage * pageSize) - index; // 계산된 순번 (내림차순)
            
            $('td:eq(0)', row).html(seqNumber);
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


function createAdv(data){
    $.ajax({
        url : "<?=base_url()?>/eventmanage/advertiser/create", 
        type : "POST", 
        dataType: "JSON", 
        data : data, 
        contentType: 'application/json; charset=utf-8',
        success : function(data){
            if(data == true){
                dataTable.draw();
                alert("생성되었습니다.");
                $('#clientModal').modal('hide');
            }
        }
        ,error : function(error){
            var errorMessages = error.responseJSON.messages;
            var firstErrorMessage = Object.values(errorMessages)[0];
            alert(firstErrorMessage);
        }
    });
}

function updateAdv(data){
    $.ajax({
        url : "<?=base_url()?>/eventmanage/advertiser/update", 
        type : "PUT", 
        dataType: "JSON", 
        data : data, 
        contentType: 'application/json; charset=utf-8',
        success : function(data){
            if(data == true){
                dataTable.draw();
                alert("수정되었습니다.");
                $('#clientModal').modal('hide');
            }
        }
        ,error : function(error){
            var errorMessages = error.responseJSON.messages;
            var firstErrorMessage = Object.values(errorMessages)[0];
            alert(firstErrorMessage);
        }
    });
}

function setAdv(data){
console.log(data.advertiser);
    $('input[name="seq"]').val(data.advertiser.seq);
    $('input[name="checkname"]').val(data.advertiser.seq);
    $('input[name="name"]').val(data.advertiser.name);
    $('input[name="agent"]').val(data.advertiser.agent);
    $('input[name="homepage_url"]').val(data.advertiser.homepage_url);
    $('input[name="interlock_url"]').val(data.advertiser.interlock_url);
    $('input[name="agreement_url"]').val(data.advertiser.agreement_url);
    $('input[name="account_balance"]').val(data.advertiser.account_balance);
    $('input[name="company_id"]').val(data.advertiser.company_seq);
    $('input:radio[name="is_stop"][value="'+data.advertiser.is_stop+'"]').prop('checked', true);
    if(data.ow){
        $('input:radio[name="sms_alert"][value="1"]').prop('checked', true);
        $('input:hidden[name="watch_list"]').val(data.ow.watch_list);
        $('input[name="watch_all"]').val(data.ow.watch_all);
        var contact = data.ow.contact.split(';');
        
        for (let i = 0; i < 3; i++) {
            $('#contact_'+i+'').val(contact[i]);
        }
    }else{
        $('input:radio[name="sms_alert"][value="0"]').prop('checked', true);
    }
    
    if(data.wl){
        if(data.ow){
            console.log(data.ow.watch_list);
            watch_list = JSON.parse(data.ow.watch_list);
        }else{
            watch_list = 0;
        }
        for (let i = 0; i < data.wl.length; i++) {
            html = '<tr class="ow_info ow_update watch_list"><th scope="row" class="text-end">'+data.wl[i].media+'</th><td><input type="hidden" name="media_seq[]" value="'+data.wl[i].seq+'"><input type="text" class="form-control mb-2 wl-input" name="strain[]" value="'+(watch_list[data.wl[i].seq] ? watch_list[data.wl[i].seq] : 0)+'"></td></tr>';
            $('#modalTable tbody').append(html)
        }
    }
}

function getAdvs(){
    $('#adv-name-input').autocomplete({
        source : function(request, response) {
            $.ajax({
                url : "/eventmanage/advertiser/company", 
                type : "GET", 
                dataType: "JSON", 
                data : {'stx': request.term}, 
                contentType: 'application/json; charset=utf-8',
                success : function(data){
                    response(
                        $.map(data, function(item) {
                            return {
                                label: item.name,
                                value: item.name,
                                id: item.id
                            };
                        })
                    );
                }
                ,error : function(){
                    alert("에러 발생");
                }
            });
        },
        select: function(event, ui) {
            $(event.target).val(ui.item.value);
            $(event.target).siblings('input[name="company_id"]').val(ui.item.id);
        },
        focus : function(event, ui) {	
            return false;
        },
        classes : {
            'ui-autocomplete': 'highlight'
        },
        minLength: 1,
        autoFocus : true,
        delay: 100
    });
}

function chkInput() {
    if($('input:radio[name="sms_alert"][value="1"]').is(':checked')){
        $('.ow_info').show();
    }else{
        $('.ow_info').hide();
        $('input[name="contact[]"]').val('');
    }
}

$('input[name="sms_alert"]').bind('change', function() {
    chkInput();
});

$('#adv-name-input').on("focus", function(){
    getAdvs();
})

$('#clientModal').on('show.bs.modal', function(e) {
    var $btn = $(e.relatedTarget);
    if ($btn.attr('id') === 'updateBtn') {
        var $tr = $btn.closest('tr');
        var seq = $tr.attr('id');
        $('#clientModalLabel').text('광고주 수정');
        $('.ow_update').show();
        $('.update-btn-wrap').show();
        $('.create-btn-wrap').hide();
        $.ajax({
            type: "GET",
            url: "<?=base_url()?>/eventmanage/advertiser/view",
            data: {'seq':seq},
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            success: function(data){  
                setAdv(data);
                chkInput();
            },
            error: function(error, status, msg){
                alert("상태코드 " + status + "에러메시지" + msg );
            }
        });
        
    }else{
        $('#clientModalLabel').text('광고주 등록');
        $('.ow_update').hide();
        $('.update-btn-wrap').hide();
        $('.create-btn-wrap').show();
        $('.watch_list').remove();
        chkInput();
        addDisabled();
    }
})
.on('hidden.bs.modal', function(e) { 
    $('input[name="seq"]').val('');
    $('input[name="company_id"]').val('');
    $('input[name="checkname"]').val('');
    $('form[name="adv-register-form"]')[0].reset();
    $('.watch_list').remove();
});

$('form[name="search-form"]').bind('submit', function() {
    dataTable.draw();
    return false;
});

$('form[name="adv-register-form"]').bind('submit', function(e) {
    /*
    if(!$('input[name="company_id"]', this).val()) {
        alert('광고주가 연결되지 않았습니다.\n광고주명이 검색되지 않을 경우 "회원관리 > 광고주/광고대행사 관리"에서 광고주를 등록한 후에 검색해주세요.');
        return false;
    }
    */
    //var clickedButton = $(document.activeElement).attr('id');
    var clickedButton = e.originalEvent.submitter.id; // 변경된 부분
    if(clickedButton == 'createActionBtn'){
        var data = $(this).serialize();
        createAdv(data);
    }
    
    if(clickedButton == 'updateActionBtn'){
        var ja = {};
        for(var i=0;i<$("input[name='strain[]']").length;i++){
            ja[$("input[name='media_seq[]']").eq(i).val()] = Number($("input[name='strain[]']").eq(i).val());
        }
        $('input[name=watch_list]').val(JSON.stringify(ja));
        var data = $(this).serialize();
        updateAdv(data);
    }
    
    return false;
});

$('body').on('click', '#sumDB', function() {
    var currentDate = new Date();
    var firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 2);
    var lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1);

    var formattedFirstDay = firstDay.toISOString().slice(0, 10);
    var formattedLastDay = lastDay.toISOString().slice(0, 10);

    var advertiser = $(this).data('name');
    if(window.localStorage.getItem('DataTables_deviceTable_/integrate')){
        var integrateStorage = window.localStorage.getItem('DataTables_deviceTable_/integrate');
        var data = JSON.parse(integrateStorage);
        data.searchData.advertiser = advertiser;
        data.searchData.sdate = formattedFirstDay;
        data.searchData.edate = formattedLastDay;
        data.searchData.event = '';
        data.searchData.media = '';
        data.searchData.status = '';
        data.searchData.stx = '';
    }else{
        var data = {
            time: Date.now(),
            start: 0,
            length: 25,
            order: [[12, "desc"]],
            search: { search: "", smart: true, regex: false, caseInsensitive: true },
            columns: [],
            memoView: "modal",
            searchData: {
                sdate: formattedFirstDay,
                edate: formattedLastDay,
                advertiser: advertiser,
                media: '',
                event: '',
                status: '',
                stx: ''
            }
        };
    }
    
    updatedStorageValue = JSON.stringify(data);
    window.localStorage.setItem('DataTables_deviceTable_/integrate', updatedStorageValue);
    window.location.href = '/integrate';
});

$('#agreementModal').on('show.bs.modal', function(e) {
    var $btn = $(e.relatedTarget);
    var link = $btn.data('link');
    var iframeContent = $('#agreementContent');
    iframeContent.attr('src', link);
})
.on('hidden.bs.modal', function(e) { 
    $('#agreementContent').attr('src', '');
});

$('body').on('click', '#totalBtn', function() {
    var advertiser = $(this).data('name');
    var data = {
        advertiser: advertiser
    };
    
    storageValue = JSON.stringify(data);
    window.localStorage.setItem('event_advertiser_name', storageValue);
    window.location.href = '/eventmanage/event';
});

function addDisabled(){
    if ($('input[name="watch_all"]').val() === "") {
        $('.wl-input').prop('disabled', false);
    } else {
        $('.wl-input').prop('disabled', true);
    }
}

$('body').on('keyup', 'input[name="watch_all"]', function() {
    addDisabled();
});
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>