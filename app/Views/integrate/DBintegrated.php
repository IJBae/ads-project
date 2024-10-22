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
    .memo-all{text-align:right;}
    .sub-contents-wrap table.dataTable th.dt-type-center, 
    .sub-contents-wrap table.dataTable td.dt-type-center {text-align:center;}
    .table-responsive .dt-search {justify-content:flex-start;}
    .btns-memo-style {top:5px;}
</style>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<?=$this->section('content');?>
<div class="sub-contents-wrap db-manage-contaniner">
    <div class="title-area">
        <h2 class="page-title">통합 DB 관리</h2>
        <!-- <p class="title-disc">안하는 사람은 끝까지 할 수 없지만, 못하는 사람은 언젠가는 해 낼 수도 있다.</p> -->
    </div>

    <div class="search-wrap type-single">
        <form name="search-form" autocomplete='off' class="search d-flex justify-content-center">
            <div class="term term-small d-flex align-items-center">
                <input type="hidden" name="sdate" id="sdate"><input type="hidden" name="edate" id="edate">
                <label><input type="text" name="dates" id="dates" data="daterangepicker" autocomplete="off" aria-autocomplete="none"><i class="bi bi-calendar2-week"></i></label>
            </div>
            <div class="input">
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
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 분류</h3>
        <div class="row" id="company-list"></div>
    </div>
    <?php }?>
    <?php if(auth()->user()->hasPermission('integrate.advertiser') || auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user', 'agency')){?>
    <div class="section client-list mt20">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 광고주</h3>
        <div class="row" id="advertiser-list"></div>
    </div>
    <?php }?>
    <?php if(auth()->user()->hasPermission('integrate.media') || auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user', 'agency')){?>
    <div class="section client-list">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 매체</h3>
        <div class="row" id="media-list"></div>
    </div>
    <?php }?>
    <?php if(auth()->user()->hasPermission('integrate.description') || auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user', 'agency')){?>
    <div class="section client-list">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 이벤트 구분</h3>
        <div class="row" id="description-list"></div>
    </div>
    <?php }?>

    <div>
        <div class="search-wrap my-5 status_wrap">
            <div class="statusCount detail d-flex minWd"></div>     
        </div>

        <div class="table-responsive db-table-responsive">
            <div class="btns-memo-style">
                <span class="btns-title">메모 표시:</span>
                <button type="button" class="btns-memo" value="modal" title="새창으로 표시"><i class="bi bi-window-stack"></i></button>
                <button type="button" class="btns-memo" value="table" title="테이블에 표시"><i class="bi bi-table"></i></button>
                <button type="button" class="btns-memo" value="all" title="한번에 표시"><i class="bi bi-eye"></i></button>
            </div>
            <table class="dataTable table table-default deviceTable" id="deviceTable">
                <thead class="table-dark">
                    <tr>
                        <th class="first">#</th>
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
                        <?php if(auth()->user()->hasPermission('integrate.description') || auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){?>
                        <th>이벤트 구분</th>
                        <?php }?>
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
let active_status;
$('#sdate, #dates').val(moment().format('YYYY-MM-DD')); // 오늘 날짜를 입력 필드에 설정
$('#edate').val(moment().format('YYYY-MM-DD')); // 오늘 날짜를 입력 필드에 설정
jQuery.fn.dataTable.ext.type.search.select = function(data) {
    // data가 select 요소인지 확인
    if (typeof data === 'string' && data.indexOf('<select') !== -1) {
        // jQuery를 사용하여 select 요소를 파싱하고 선택된 옵션의 텍스트를 가져옴
        var select = $(data);
        var selectedText = select.find('option:selected').text();
        return selectedText ? selectedText : '';
    }
    return data;
};
var lead_status = {"1":"인정", "2":"중복", "3":"성별불량", "4":"나이불량", "5":"콜불량", "6":"번호불량", "7":"테스트", "8":"이름불량", "9":"지역불량", "10":"업체불량", "11":"미성년자", "12":"본인아님", "13":"쿠키중복", "99":"확인"};
let dataTable = $('.dataTable').DataTable({
    "dom": '<fr<t>ip>', // DataTables의 DOM 구조 설정
    "fixedHeader": {
        "header" : true, // 헤더 고정
        "footer" : true, // 푸터 고정
    },
    "autoWidth": true, // 자동 너비 조정 활성화
    "processing" : true, // 처리 중 표시 활성화
    "serverSide" : false, // 서버 사이드 처리 비활성화
    "responsive": true, // 반응형 테이블 활성화
    "searching": true, // 검색 기능 활성화
    "order": [[0, "desc"]], // seq 항목을 역순 정렬
    "ordering": true, // 정렬 기능 활성화
    "orderMulti": false, // 다중 열 정렬 활성화
    "orderCellsTop": false, // 열 헤더 클릭 시 정렬 활성화
    "paging": true, // 페이징 비활성화
    "pageLength": 25, // 페이지당 표시할 행 수
    "info": false, // 정보 표시 비활성화
    "scroller": false, // 스크롤러 비활성화
    "scrollX": true, // 가로 스크롤 활성화
    "stateSave": true, // 상태 저장 활성화
    "deferRender": false, // 렌더링 지연 비활성화
    "rowId": "seq",
    "language": {"url": '/static/js/dataTables.i18n.json'}, //한글화 파일
    "ajax": {
        "url": "<?=base_url()?>/integrate/list", // AJAX URL 설정
        "type": "POST", // HTTP 요청 방식 설정
        "data": function(d) {
            d.date = {
                sdate: $('#sdate').val(), // 시작 날짜
                edate: $('#edate').val() // 종료 날짜
            };
        },
        "dataSrc": function(json) {
            if (json && json.data) {
                setFilter(json.data);
            }
            return json.data;
        }
    },
    "columnDefs": [
    <?php 
    $target = 10;
    if(getenv('MY_SERVER_NAME') === 'resta' 
    && auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')) {
        ++$target;
    }
    if(auth()->user()->hasPermission('integrate.advertiser') 
    || auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){
        ++$target;
    }
    if(auth()->user()->hasPermission('integrate.media') 
    || auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){
        ++$target;
    }
    if(auth()->user()->hasPermission('integrate.description') 
    || auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){
        ++$target;
    }
    ?>
        { type: "select", target: <?php echo $target;?> }, // 13은 status 컬럼의 인덱스입니다. 필요에 따라 조정하세요.
    ],
    "columns": [
        { "data": "seq",  "width": "4%", "className": "dt-type-center" },
        <?php if(getenv('MY_SERVER_NAME') === 'resta' && auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){?>
        { "data": null, "render": function(data, type, row) {
                return row.company;
            }
        },
        <?php }?>
        { "data": "info_seq","width": "4%", "className": "dt-type-center", "render": function(data, type, row) {
                ico = '';
                if(row.another_url == 1) ico = `<span class="another_url">🐢</span>`;
                return data?ico+'<a href="'+row.event_url+'" target="event_pop">'+data+'</a>':'';
            }
        },
        <?php if(auth()->user()->hasPermission('integrate.advertiser') || auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){?>
        { "data": "advertiser","width": "7%", "className": "dt-type-center"},
        <?php }?>
        <?php if(auth()->user()->hasPermission('integrate.media') || auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){?>
        { "data": "media", "className": "dt-type-center" },
        <?php }?>
        <?php if(auth()->user()->hasPermission('integrate.description') || auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){?>
        { "data": "description","width":"10%", "className": "dt-type-center","render": function(data) {
                return `<div class="mw10">${data}</div>`
            } 
        },
        <?php }?>
        { "data": "name",  "width": "5%", "className": "dt-type-center", "render": function(data) {
                return '<span style="display:inline-block;width:50px;max-height:15px;overflow:hidden" title="'+$(`<span>${data}</span>`).text()+'">'+(data ? data : '')+'</span>';
            } 
        },
        { "data": "dec_phone","width":"8%", "className": "dt-type-center","render": function(data) {
                return `<div class="mw10">${data}</div>`
            } 
        },
        { "data": "age", "width": "3%", "className": "dt-type-center" },
        { "data": "gender", "width": "3%" },
        { "data": "add","render": function(data) {
                return `<div class="mw10">${data}</div>`
            } 
        },
        { "data": "site","width":"6%", "className": "dt-type-center" },
        { "data": "reg_date","width":"6%", "className": "dt-type-center","render": function(data) {
                return `<div class="mw10">${data}</div>`
            } 
        },
        { "data": "memo_cnt", "className": "memo","width":"3%", "className": "dt-type-center", "render" : function(data) { // data-bs-toggle="modal"
                var html = '<button class="btn_memo text-dark position-relative" data-bs-target="#modal-integrate-memo"><i class="bi bi-chat-square-text h4"></i>';
                html += '<span class="position-absolute top--10 start-100 translate-middle badge rounded-pill bg-danger badge-'+data+'">'+data+'</span>';
                html += '</button>';
                return html;
            }
        },
        { 
            "data": 'status',"width":"6%", "render": function (data, type, row) {
                <?php if(auth()->user()->hasPermission('integrate.status') || auth()->user()->inGroup('superadmin', 'admin')) { ?>
                    return `
                        <select class="lead-status form-select form-select-sm data-del">
                            ${Object.keys(lead_status).map(key => `
                                <option value="${key}" ${data == key ? "selected" : ""}>${lead_status[key]}</option>
                            `).join('')}
                        </select>
                    `;
                <?php } else { ?>
                    return `${lead_status[data] || ''}`;
                <?php } ?>
            }
        },
    ],
    "rowCallback": function(row, data) {
    },
    "stateSaveParams": function(settings, data) {
        // memoState를 저장 데이터에 추가
        data.memoState = settings.oInit.memoState;
        // folded 상태 저장
        data.foldedStates = settings.oInit.foldedStates;
    },
    "stateLoadParams": function(settings, data) {
        // 저장된 memoState를 불러와서 settings에 추가
        if (data.memoState) {
            settings.oInit.memoState = data.memoState;
        }
        if (data.foldedStates) {
            settings.oInit.foldedStates = data.foldedStates;
        }
    },
    "initComplete": function(settings, json) {
        // foldedStates 복원
        if (settings.oInit.foldedStates) {
            $('.client-list > .content-title').each(function(index) {
                if (settings.oInit.foldedStates[index]) {
                    $(this).addClass('folded');
                } else {
                    $(this).removeClass('folded');
                }
            });
        }

        // memoState 복원
        let savedMemoState = settings.oInit && settings.oInit.memoState ? settings.oInit.memoState : 'modal';
        $(`.btns-memo[value="${savedMemoState}"]`).addClass('active');
        if (savedMemoState === 'all') displayAllMemos();
        
        updateFilterButtons.call(this);
    },
    "drawCallback": function(settings) {
        updateFilterButtons.call(this);
        updateStatusCount.call(this);
        if ($('.btns-memo.active').val() === 'all') {
            debouncedDisplayAllMemos();
        }
        //통합DB 확인 시 테스트일 경우 표기 추가 240724
        $('#deviceTable td.dt-type-center span').each(function() {
            if ($(this).text().includes('테스트') || $(this).text().includes('test')) {
                $(this).closest('tr').addClass('on');
            }
        });
    },
});
var windowWidth = $(window).width();
if (windowWidth >= 1024) { // 태블릿 이상의 너비에서만 fixedColumns 적용
    new $.fn.dataTable.FixedColumns(dataTable, {
        leftColumns: 5, // 왼쪽에서 고정할 컬럼 수
        rightColumns: 0 // 오른쪽에서 고정할 컬럼 수
    });
}
// Debounce 함수 정의
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}
const debouncedDisplayAllMemos = debounce(displayAllMemos, 1000);
// 필터 버튼 업데이트 함수
function updateFilterButtons() {
    var api = this.api(); // DataTables API 인스턴스를 가져옵니다.
    var data = api.rows({ search: 'applied' }).data().toArray(); // 현재 페이지의 데이터를 배열로 변환합니다.
    let uniqueValues = { advertiser: [], media: [], description: [] }; // 고유 값 저장을 위한 객체 초기화
    var columnToListMap = {
        'company': 'company-list',
        'advertiser': 'advertiser-list',
        'media': 'media-list',
        'description': 'description-list'
    };

    // 필터링 상태에 따라 버튼에 active 클래스 적용
    dataTable.columns().every(function() {
        var column = this;
        var search = column.search();
        if (search) {
            if(typeof search == 'function') return true;
            var searchRegex = new RegExp(search.replace(/\\/g, ''));
            var expectedListId = columnToListMap[column.dataSrc()];

            if (expectedListId) {
                $(`#${expectedListId} .filter_btn`).each(function() {
                    var btnValue = $.trim($(this).text());
                    if (searchRegex.test(btnValue)) {
                        $(this).addClass('active');
                    }
                });
            }
        }
    });
    // 각 행의 데이터를 순회하면서 고유 값 배열에 추가합니다.
    data.forEach(row => {
        ['advertiser', 'media', 'description'].forEach(key => {
            if (!uniqueValues[key].includes(row[key])) { // 배열에 정확히 일치하는 값이 없으면 추가
                uniqueValues[key].push(row[key]);
            }
        });
    });

    // 데이터 수량 계산 (status가 '인정'인 것만 카운트)
    let countMap = { advertiser: {}, media: {}, description: {} };
    api.rows({ search: 'applied' }).data().each(function(row) {
        ['advertiser', 'media', 'description'].forEach(key => {
            let value = row[key];
            if (row['status'] == '1' && typeof value === 'string') {
                if(value == "") value = 'empty';
                countMap[key][value] = (countMap[key][value] || 0) + 1;
            }
        });
    });

    // 수량 업데이트
    Object.keys(countMap).forEach(key => {
        let $list = $(`#${key}-list`);
        if ($list.length) { // 요소가 존재하는지 확인
            $list.find('.filter_btn').each(function() {
                var btnValue = $(this).val();
                if(btnValue == "") btnValue = 'empty';
                var count = countMap[key][btnValue] || "";
                $(this).siblings('.progress').find('.txt').text(count);
            });
        }
    });

    // 활성화된 필터 버튼이 없는 경우, 모든 필터 버튼의 'on' 클래스를 제거하고 함수를 종료합니다.
    if (!$('.filter_btn.active').length) {
        $('.filter_btn').removeClass('on');
        return;
    }

    // 각 필터 유형에 대해 'on' 클래스를 토글합니다.
    Object.keys(uniqueValues).forEach(key => {
        let $list = $(`#${key}-list`);
        if ($list.length) { // 요소가 존재하는지 확인
            $list.find('.filter_btn').not('.active').each(function() {
                var btnText = $(this).text().trim();
                var isExactMatch = uniqueValues[key].some(function(value) { // 완전 일치 여부를 확인합니다.
                    return value === btnText;
                });
                $(this).toggleClass('on', isExactMatch); // 'on' 클래스를 토글합니다.
            });
        }
    });
}

// 필터 버튼 세팅 함수
function setFilter(data) {
    const unique = (prop) => [...new Set(data.map(item => item[prop]))];
    const filters = {
        media: unique('media'),
        advertiser: unique('advertiser'),
        description: unique('description'),
        company: unique('company')
    };

    Object.keys(filters).forEach(type => {
        let $row = $(`#${type}-list`);
        let html = filters[type].map(v => `
            <div class="col">
                <div class="inner">
                    <button type="button" value="${v}" data-btn="${type}" class="${type}_btn filter_btn">
                        <span class="account_name">${v}</span>
                    </button>
                    <div class="progress">
                        <div class="txt">0</div>
                    </div>
                </div>
            </div>
        `).join('');

        $row.html('').append(html);

        // 정렬
        let buttons = $row.find('.filter_btn').toArray();
        buttons.sort((a, b) => $(a).find('.account_name').text().localeCompare($(b).find('.account_name').text(), 'en', { sensitivity: 'base' }));
        buttons.forEach(button => $row.append(button.parentNode.parentNode));
    });
}
// statusCount 업데이트 함수
function updateStatusCount() {
    var api = this.api(); // DataTables API 인스턴스를 가져옵니다.
    let statusCountMap = {};

    // active_status 값으로 .statusCount dl dt 텍스트 찾아서 클래스 추가
    if(typeof active_status != 'undefined') {
        $('.statusCount dl dt').each(function() {
            if ($(this).text() === lead_status[active_status]) {
                $(this).closest('dl').addClass('active');
            }
        });
        return;
    }

    api.rows({ search: 'applied' }).data().each(function(row) {
        let status = row['status'];
        if (status) {
            statusCountMap[status] = (statusCountMap[status] || 0) + 1;
        }
    });

    let statusHtml = Object.keys(lead_status).map(status => `
        <dl class="col" data-status="${status}">
            <dt>${lead_status[status]}</dt>
            <dd>${statusCountMap[status] || 0}</dd>
        </dl>
    `).join('');

    $('.statusCount').html(statusHtml);

    // dl 클릭 이벤트 바인딩
    $('.statusCount dl').on('click', function() {
        var statusColumnIndex = getColumnIndexByHeader('status');

        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            active_status = undefined;
            dataTable.column(statusColumnIndex).search('').draw();
        } else {
            $('.statusCount dl').removeClass('active');
            $(this).addClass('active');
            let status = $(this).data('status');
            active_status = status;
            dataTable.column(statusColumnIndex).search((d) => {return d == lead_status[status]}).draw();
        }
    });
}
// 컬럼 인덱스 가져오기
function getColumnIndexByHeader(dataName) {
    var index = -1;
    dataTable.settings().init().columns.forEach(function(column, i) {
        if (column.data === dataName) {
            index = i;
            return false; // 반복문 종료
        }
    });
    return index;
}

// 상태 변경 이벤트
$(document).on('change', '.lead-status', function() {
    var $select = $(this);
    var $row = $select.closest('tr');
    var seq = $row.attr('id');
    var oldStatus = dataTable.row($row).data().status;
    var newStatus = $select.val();

    $.ajax({
        url: '<?=base_url()?>/integrate/setstatus',
        type: 'POST',
        data: {
            seq: seq,
            oldstatus: oldStatus,
            status: newStatus
        },
        success: function(response) {
            // 성공 시 처리할 내용
            console.log('Status updated successfully');
            // 메모 저장
            var memo = `인정기준 변경(${lead_status[oldStatus]} > ${lead_status[newStatus]})`;
            saveMemo(memo, seq, $row);
        },
        error: function(xhr, status, error) {
            // 에러 시 처리할 내용
            console.error('Error updating status:', error);
        }
    });
});
// 메모 가져오기
function fetchMemo(seq, callback) {
    $.ajax({
        url: '<?=base_url()?>/integrate/getmemo',
        type: 'GET',
        data: { seq: seq },
        success: function(response) {
            callback(response);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching memo:', error);
        }
    });
}

// 메모 표시
function displayMemo(memoList, displayType, $row = null) {
    var memoContent = '';

    if (Array.isArray(memoList)) {
        memoContent = memoList.map(function(memo) {
            return `
                <div class="memo-item">
                    <div class="memo-body">
                        <p>${memo.memo}</p>
                    </div>
                    <div class="memo-header">
                        <strong>${memo.username}</strong>
                        <span class="memo-date">${memo.reg_date}</span>
                    </div>
                </div>
            `;
        }).join('');
    }

    if (displayType === 'modal') {
        var $memoListContainer = $('#modal-integrate-memo .memo-list');
        $memoListContainer.empty().append(memoContent);
        $('#modal-integrate-memo').data('leads-seq', $row.attr('id')); // leads_seq 저장

        // 이름 컬럼의 인덱스를 동적으로 찾기
        var nameColumnIndex = getColumnIndexByHeader('name');
        if (nameColumnIndex !== -1) {
            var name = $row.find('td').eq(nameColumnIndex).text(); // 이름이 있는 열의 인덱스를 사용하여 이름을 가져옴
            $('#modal-integrate-memo-label').html(`<i class="bi bi-file-text"></i> 개별 메모 - ${name}`);
        } else {
            $('#modal-integrate-memo-label').html(`<i class="bi bi-file-text"></i> 개별 메모`);
        }

        $('#modal-integrate-memo').modal('show');
    } else if ((displayType === 'table' || displayType === 'all') && $row) {
        var memoForm = `
            <form class="regi-form">
                <fieldset>
                    <legend>메모 작성</legend>
                    <textarea></textarea>
                    <button type="button" class="btn-regi">작성</button>
                </fieldset>
            </form>
        `;

        if ($row.next().hasClass('memo-row')) {
            $row.next().remove();
        } else {
            $row.after(`<tr class="memo-row"><td class="pl10 pr10" colspan="15">${memoForm}${memoContent}</td></tr>`);
        }
    }
}

// 모든 메모 표시
function displayAllMemos() {
    $('.btn_memo').each(function() {
        var $row = $(this).closest('tr');
        var seq = $row.attr('id');
        var badgeCount = $(this).find('.badge').text();

        if (badgeCount > 0) {
            fetchMemo(seq, function(memoList) {
                displayMemo(memoList, 'table', $row);
            });
        }
    });
}
// 메모 저장 함수
function saveMemo(memo, leads_seq, $form, $row) {
    $.ajax({
        url: '<?=base_url()?>/integrate/addmemo',
        type: 'POST',
        data: {
            memo: memo,
            leads_seq: leads_seq
        },
        success: function(response) {
            // 메모 추가 후 폼 초기화
            $form.find('textarea').val('');
            // 필요 시 메모 목록 갱신
            fetchMemo(leads_seq, function(memoList) {
                var memoState = $('.btns-memo.active').val();
                displayMemo(memoList, memoState, $row);
                updateMemoBadge($row, memoList.length);
            });
        },
        error: function(xhr, status, error) {
            // 에러 시 처리할 내용
            console.error('Error adding memo:', error);
        }
    });
}
// 메모 배지 업데이트
function updateMemoBadge($row, count) {
    var $btnMemo = $row.find('.btn_memo');
    $btnMemo.find('.badge').text(count);
}

// 모든 메모 업데이트
function updateAllMemos() {
    $('.btn_memo').each(function() {
        var $row = $(this).closest('tr');
        var seq = $row.attr('id');

        fetchMemo(seq, function(memoList) {
            updateMemoBadge($row, memoList.length);
        });
    });
}
$('#dates').daterangepicker({
    locale: {
        "format": 'YYYY-MM-DD',     // 일시 노출 포맷
        "applyLabel": "확인",                    // 확인 버튼 텍스트
        "cancelLabel": "취소",                   // 취소 버튼 텍스트
        "daysOfWeek": ["일", "월", "화", "수", "목", "금", "토"],
        "monthNames": ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"]
    },
    // singleDatePicker: true, // 단일 날짜 선택 활성화
    alwaysShowCalendars: true,                        // 시간 노출 여부
    showDropdowns: true,                     // 년월 수동 설정 여부
    autoApply: true,                         // 확인/취소 버튼 사용여부
    maxDate: new Date(),
    autoUpdateInput: false,
    maxSpan: {
        days: 30
    },
    ranges: {
        오늘: [moment(), moment()],
        어제: [moment().subtract(1, "days"), moment().subtract(1, "days")],
        최근7일: [moment().subtract(7, "days").startOf("day"), moment().subtract(1, "days").endOf("day")],
        최근14일: [moment().subtract(14, "days").startOf("day"), moment().subtract(1, "days").endOf("day")],
        이번주: [moment().day(1), moment()],
        지난주: [moment().add(-1, "week").startOf("week").day(1), moment().add(-1, "week").startOf("week").add(7, "day")],
        이번달: [moment().startOf("month"), moment().endOf("month")],
        지난달: [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")],
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

/*
* click Event List 
*/
// 필터 초기화
$(document).on('click', '.reset-btn', function() {
    $('.filter_btn').removeClass('active on');
    dataTable.columns().search('').draw();

    // DataTable 상태 초기화
    dataTable.state.clear();
});
// content-title 클릭 시 folded 상태 토글 및 저장
$(document).on('click', '.client-list > .content-title', function() {
    // DataTables 상태 저장
    let settings = dataTable.settings();
    settings[0].oInit.foldedStates = [];
    $('.client-list > .content-title').each(function() {
        settings[0].oInit.foldedStates.push($(this).hasClass('folded'));
    });
    // DataTable 상태 저장
    dataTable.state.save();
});
// 메모 상태 저장 및 초기화
$(document).on('click', '.btns-memo', function() {
    $('.btns-memo').removeClass('active'); // 모든 버튼에서 active 클래스 제거
    $(this).addClass('active'); // 클릭된 버튼에 active 클래스 추가

    // DataTables 상태 저장
    let memoState = $(this).val();
    let settings = dataTable.settings();
    settings[0].oInit.memoState = memoState; // DataTables 상태에 memoState 추가
    dataTable.state.save(); // 상태 저장

    // 모달 및 테이블 초기화
    $('#modal-integrate-memo .memo-list').empty();
    $('.memo-row').remove();

    // 메모 목록 갱신
    if (memoState === 'all') {
        displayAllMemos();
    }
});
// 필터 버튼 클릭 시 해당 컬럼 필터링
$(document).on('click', '.filter_btn', function() {
    $(this).toggleClass('active'); // 버튼 활성화/비활성화 토글

    // 모든 열의 검색을 초기화
    dataTable.columns().search('');

    // 각 컬럼별로 활성화된 버튼의 텍스트를 배열로 수집하고 정규 표현식으로 조합
    dataTable.columns().every(function() {
        var column = this;
        var filterType = column.dataSrc(); // 컬럼 헤더를 기반으로 필터 타입 결정
        var activeFilters = $(`.filter_btn.active[data-btn="${filterType}"]`).map(function() {
            return $.fn.dataTable.util.escapeRegex($(this).val());
        }).get();
        if (activeFilters.length) {
            var regex = `^(${activeFilters.join('|')})$`; // '값1|값2|값3' 형태로 조합
            column.search(regex, true, false); // 정규 표현식 검색 활성화
        } else {
            column.search(''); // 필터가 없는 경우 검색 초기화
        }
    });

    dataTable.draw(); // 필터링 후 테이블 업데이트
});
// 메모 버튼 클릭 이벤트
$(document).on('click', '.btn_memo', function() {
    var $row = $(this).closest('tr');
    var seq = $row.attr('id');
    var memoState = $('.btns-memo.active').val();

    fetchMemo(seq, function(memoList) {
        displayMemo(memoList, memoState, $row);
    });
});
// regi-form 및 모달에서 메모 전송 이벤트 처리
$(document).on('click', '.btn-regi', function() {
    var $form = $(this).closest('form');
    var memo = $form.find('textarea').val();
    var $row, leads_seq;

    if ($form.closest('#modal-integrate-memo').length) {
        // 모달에서 전송
        leads_seq = $('#modal-integrate-memo').data('leads-seq');
        $row = $(`[id="${leads_seq}"]`);
    } else {
        // 테이블에서 전송
        $row = $form.closest('tr').prev();
        leads_seq = $row.attr('id');
    }

    saveMemo(memo, leads_seq, $form, $row);
});
$('#search_btn').on('click', function(e) {
    e.preventDefault(); // 폼 제출 방지
    dataTable.ajax.reload(); // DataTables의 AJAX 요청을 다시 실행
});
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>
