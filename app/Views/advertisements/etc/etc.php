<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    ZENITH - 광고 관리 / 기타
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/css/datatables.css" rel="stylesheet">
<script src="/static/node_modules/datatables.net/js/dataTables.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/buttons.html5.min.js"></script>
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
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<div class="sub-contents-wrap etc-contents-wrap">
    <div class="title-area">
        <h2 class="page-title">기타 광고관리</h2>
        <p class="title-disc">여러 매체의 광고를 확인 하세요~</p>
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

    <div class="section client-list media_name">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 매체</h3>
        <div class="row" id="media-list"></div>
    </div>
    <div class="section client-list advertiser_name">
        <h3 class="content-title toggle folded"><i class="bi bi-chevron-up"></i> 광고주</h3>
        <div class="row" id="advertiser-list"></div>
    </div>
    <div class="section client-list event_seq">
        <h3 class="content-title toggle folded"><i class="bi bi-chevron-up"></i> 이벤트 번호</h3>
        <div class="row" id="event-list"></div>
    </div>
    <div class="section client-list description">
        <h3 class="content-title toggle folded"><i class="bi bi-chevron-up"></i> 이벤트 구분</h3>
        <div class="row" id="description-list"></div>
    </div>
    <div class="section">
        <div class="table-responsive">
            <div id="latestUpdateTime" class="latestUpdateTime"></div>
            <table class="dataTable table table-default">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" rowspan="2">날짜</th>
                        <th scope="col" rowspan="2">이벤트</th>
                        <th scope="col" rowspan="2">site</th>
                        <th scope="col" rowspan="2">광고주</th>
                        <th scope="col" rowspan="2">매체</th>
                        <th scope="col" rowspan="2">목표수량</th>
                        <th scope="col" rowspan="2">이벤트 구분</th>
                        <th scope="col" colspan="3">DB 수량</th>
                        <th scope="col" rowspan="2">정산DB단가</th>
                        <th scope="col" rowspan="2">소진금액<br>(vat별도)</th>
                        <th scope="col" rowspan="2">현재DB단가</th>
                        <th scope="col" rowspan="2">수익</th>
                        <th scope="col" rowspan="2">수익률</th>
                        <th scope="col" rowspan="2">확인</th>
                    </tr>
                    <tr>
                        <th scope="col">전체</th>
                        <th scope="col">유효</th>
                        <th scope="col" class="r-border">무효</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <!--엑셀 업로드 폼
    <form action="<?= base_url('AdvEtcManager/uploadExcel') ?>" method="post" enctype="multipart/form-data" class="d-flex align-items-center">
        <input type="file" class="form-control" id="fileUpload" name="fileUpload" accept=".csv" style="display: none;" onchange="document.getElementById('uploadForm').submit();">
        <button type="button" class="btn btn-secondary d-flex align-items-center" onclick="document.getElementById('fileUpload').click();">
            <i class="bi bi-filetype-csv fs-5 me-1"></i>파일 선택
        </button>
    </form>
    -->
</div>
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script>
$('#sdate, #dates').val(moment().format('YYYY-MM-DD')); // 오늘 날짜를 입력 필드에 설정
$('#edate').val(moment().format('YYYY-MM-DD')); // 오늘 날짜를 입력 필드에 설정
let dataTable = $('.dataTable').DataTable({
    "dom": '<fr<t>>', // DataTables의 DOM 구조 설정
    "fixedHeader": {
        "header" : true, // 헤더 고정
        "footer" : true, // 푸터 고정
    },
    "fixedColumns": {
        "leftColumns": 3 // 왼쪽에서 세 열 고정
    },
    "autoWidth": true, // 자동 너비 조정 활성화
    "processing" : true, // 처리 중 표시 활성화
    "serverSide" : false, // 서버 사이드 처리 비활성화
    "responsive": true, // 반응형 테이블 활성화
    "searching": true, // 검색 기능 활성화
    "ordering": true, // 정렬 기능 활성화
    "orderMulti": true, // 다중 열 정렬 활성화
    "orderCellsTop": true, // 열 헤더 클릭 시 정렬 활성화
    "colOrder": true, // 열 순서 조정 활성화
    "paging": false, // 페이징 비활성화
    "info": false, // 정보 표시 비활성화
    "scroller": false, // 스크롤러 비활성화
    "scrollX": true, // 가로 스크롤 활성화
    "stateSave": true, // 상태 저장 활성화
    "deferRender": false, // 렌더링 지연 비활성화
    "ajax": {
        "url": "<?= base_url('etcmanager/get-list') ?>", // AJAX URL 설정
        "type": "POST", // HTTP 요청 방식 설정
        "data": function(d) {
            d.date = {
                sdate: $('#sdate').val(), // 시작 날짜
                edate: $('#edate').val() // 종료 날짜
            };
        }
    },
    "columnDefs": [
        { "className": "dt-type-center", "targets": [0, 1, 2, 7, 8, 9, 12, 13, 14, 15] } // 특정 열 가운데 정렬
    ],
    "columns": [
        { "data": "date", "width": "10%" }, //날짜
        { "data": "event_seq", "width": "5%" }, //이벤트
        { "data": "site", "width": "5%" }, //site
        { "data": "advertiser_name", "width": "10%" }, //광고주
        { "data": "media_name", "width": "5%" }, //매체
        { "data": "goal", "width": "3%", "class": "dt-type-center", "render": function(data, type) { //목표수량
            if($('#sdate').val() != $('#edate').val()) return '-';
            return type === 'display' ? `<div class="mw10"><input type='text' class='form-control form-control-sm' name='goal' value='${data}'></div>` : data;
        }},
        { "data": "description", "width": "10%", "class": "dt-type-center", "render": function(data, type) { //이벤트구분
            return type === 'display' ? `<div class="mw10">${data}</div>` : data;
        }},
        { "data": "db_total", "width": "4%" }, //총DB
        { "data": "db_unique", "width": "4%" }, //유효DB
        { "data": "db_invalid", "width": "4%" }, //무효DB
        { "data": "unitprice", "width": "6%", "class": "dt-type-center", "render": function(data, type) { //정산DB단가
            if($('#sdate').val() != $('#edate').val()) return '-';
            return type === 'display' ? `<div class="mw10"><input type='text' class='form-control form-control-sm' name='unitprice' value='${parseInt(data).toLocaleString()}'></div>` : data;
        }},
        { "data": "total_price", "width": "6%", "class": "dt-type-center", "render": function(data, type) { //소진금액
            if($('#sdate').val() != $('#edate').val()) return '-';
            return type === 'display' ? `<div class="mw10"><input type='text' class='form-control form-control-sm' name='total_price' value='${parseInt(data).toLocaleString()}'></div>` : data;
        }},
        { "data": "now_unitprice", "width": "5%", "class": "dt-type-center", "render": function(data, type) { //현재DB단가
            if($('#sdate').val() != $('#edate').val()) return '-';
            var className = parseInt(data) < 0 ? 'text-danger' : '';
            return type === 'display' ? `<span class="${className}">${parseInt(data).toLocaleString()}원</span>` : data;
        }},
        { "data": "profit", "width": "6%", "class": "dt-type-center", "render": function(data, type) { //수익
            if($('#sdate').val() != $('#edate').val()) return '-';
            var className = parseInt(data) < 0 ? 'text-danger' : '';
            return type === 'display' ? `<span class="${className}">${parseInt(data).toLocaleString()}원</span>` : data;
        }},
        { "data": "profit_rate", "width": "4%", "class": "dt-type-center", "render": function(data, type) { //수익률
            if($('#sdate').val() != $('#edate').val()) return '-';
            var className = parseFloat(data) < 0 ? 'text-danger' : '';
            return type === 'display' ? `<span class="${className}">${data}%</span>` : data;
        }},
        { "data": "confirm", "width": "3%", "class": "dt-type-center", "render": function(data, type) { //확인
            if($('#sdate').val() != $('#edate').val()) return '-';
            return type === 'display' ? `<div class="d-flex justify-content-center"><div class="form-check form-switch"><input class="form-check-input form-switch-dark" type="checkbox" role="switch" name="confirm" style="width: 40px; height: 20px;" ${data == 1 ? 'checked' : ''}></div></div>` : data;
        }}
    ],
    "rowCallback": function(row, data) {
        // 체크박스와 텍스트 입력 필드의 변경을 감지합니다.
        $(row).find('input[type="checkbox"], input[type="text"]').off('change').on('change', function() {
            var $row = $(this).closest('tr');
            var rowData = dataTable.row($row).data();
            var inputName = $(this).attr('name');
            var newValue = $(this).attr('type') === 'checkbox' ? $(this).is(':checked') ? 1 : 0 : $(this).val();

            // 변경된 값과 기본 키 값들만 포함하여 서버에 전송합니다.
            var postData = {
                date: rowData.date,
                event_seq: rowData.event_seq,
                site: rowData.site
            };
            postData[inputName] = newValue;

            // 변경된 postData를 서버에 전송합니다.
            $.ajax({
                url: '<?= base_url('etcmanager/update-data') ?>',
                type: 'POST',
                data: postData,
                success: function(response) {
                    // 성공적으로 데이터를 업데이트했을 때의 처리 로직
                    dataTable.ajax.reload();
                    console.log('Update successful', response);
                },
                error: function(xhr, status, error) {
                    // 데이터 업데이트에 실패했을 때의 처리 로직
                    console.error('Update failed', error);
                }
            });
        });
        // confirm 값이 1이면 행의 배경색을 회색으로 설정
        if (data.confirm == 1) {
            $(row).find('td').css('background-color', '#e9ecef');
        }
    },
    "drawCallback": function(settings) {
        var api = this.api();
        var data = api.rows({page:'current'}).data();
        var latestDateTime = '데이터 없음';

        if (data.length > 0) {
            latestDateTime = data.reduce((latest, item) => {
                return new Date(latest.reg_date) > new Date(item.reg_date) ? latest : item;
            }).reg_date;
            if(latestDateTime === null) latestDateTime = '데이터 없음';
        }

        // 최신 datetime을 페이지 상단에 표시
        $('#latestUpdateTime').text('업데이트 시간 : ' + latestDateTime);
        var idx = {
            totalDB: 7,
            uniqueDB: 8,
            invalidDB: 9,
            setDbPrice: 10,
            costPrice: 11,
            dbPrice: 12,
            profitPrice: 13,
            profitRate: 14
        };
        var totalDB = api.column(idx['totalDB'], {page: 'current'}).data().reduce(function(a, b) { return parseInt(a) + parseInt(b); }, 0);
        var uniqueDB = api.column(idx['uniqueDB'], {page: 'current'}).data().reduce(function(a, b) { return parseInt(a) + parseInt(b); }, 0);
        var invalidDB = api.column(idx['invalidDB'], {page: 'current'}).data().reduce(function(a, b) { return parseInt(a) + parseInt(b); }, 0);
        var setDbPrice = api.column(idx['setDbPrice'], {page: 'current'}).data().reduce(function(a, b) { return parseInt(a) + parseInt(b); }, 0);
        var costPrice = api.column(idx['costPrice'], {page: 'current'}).data().reduce(function(a, b) { return parseInt(a) + parseInt(b); }, 0);
        var dbPrice = Math.round(costPrice / uniqueDB); //현재 DB단가 = 지출액 / 유효DB
        // var salePrice = ; //매출 = 각 정산DB단가 * 각 유효DB
        var salePrice = api.column(idx['setDbPrice'], {page: 'current'}).data().reduce(function(a, b, i) {
            var uniqueDB = api.column(idx['uniqueDB'], {page: 'current'}).data()[i];
            return b > 0 ? a + (b * uniqueDB) : a;
        }, 0);
        
        var profitPrice = api.column(idx['profitPrice'], {page: 'current'}).data().reduce(function(a, b) { return parseInt(a) + parseInt(b); }, 0); //수익 = 매출 - 지출
        var profitRate = (profitPrice/salePrice)*100; //수익률  = (수익/매출)*100
        if (isNaN(profitRate)) profitRate = 0; // NaN인 경우 0으로 설정
        
        var total = {
            totalDB: totalDB,
            uniqueDB: uniqueDB,
            invalidDB: invalidDB,
            costPrice: costPrice.toLocaleString() + '원',
            dbPrice: dbPrice.toLocaleString() + '원', //현재 DB단가 = costPrice / uniqueDB
            profitPrice: profitPrice.toLocaleString() + '원', //수익 = (DB단가-(소진금액/유효DB))*유효DB
            profitRate: profitRate.toFixed(1) + '%' //수익률  = (수익/매출)*100
        };

        if($('#sdate').val() != $('#edate').val()) {
            total.costPrice = '-';
            total.dbPrice = '-';
            total.profitPrice = '-';
            total.profitRate = '-';
        }

        $("table.dataTable thead tr.sum-row").remove();

        var newRow = '<tr class="sum-row">' +
            '<th colspan="7">합계</th>' +
            '<th>' + total.totalDB + '</th>' +
            '<th>' + total.uniqueDB + '</th>' +
            '<th>' + total.invalidDB + '</th>' +
            '<th></th>' +
            '<th>' + total.costPrice + '</th>' +
            '<th>' + total.dbPrice + '</th>' +
            '<th>' + total.profitPrice + '</th>' +
            '<th>' + total.profitRate + '</th>' +
            '<th></th>' + // 확인 열은 비워둠
            '</tr>';
        $(".dt-scroll-head table.dataTable thead").append(newRow);
        updateFilterButtons.call(this);
    },
    "initComplete": function(settings, data) {
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
        
        updateFilterButtons.call(this);
    },
    "language": {
            "url": '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        },
    "stateSaveParams": function(settings, data) {
        // folded 상태 저장
        data.foldedStates = settings.oInit.foldedStates;
    },
    "stateLoadParams": function(settings, data) {
        if (data.foldedStates) {
            settings.oInit.foldedStates = data.foldedStates;
        }
    },
}).on('xhr.dt', function( e, settings, data, xhr ) {
    if(data && data.data) {
        var data = data.data;
        const unique = (prop) => [...new Set(data.map(item => item[prop]))];
        setFilter('media_name', unique('media_name'));
        setFilter('advertiser_name', unique('advertiser_name'));
        setFilter('event_seq', unique('event_seq'));
        setFilter('description', unique('description'));
        // $('.media_btn[value="토스"]').click();
    }
});

function updateFilterButtons() {
    var api = this.api(); // DataTables API 인스턴스를 가져옵니다.
    var data = api.rows({ search: 'applied' }).data().toArray(); // 현재 페이지의 데이터를 배열로 변환합니다.
    let uniqueValues = { advertiser_name: [], media_name: [], event_seq: [], description: [] }; // 고유 값 저장을 위한 객체 초기화
    var columnToListMap = {
        'event_seq': 'event-list',
        'advertiser_name': 'advertiser-list',
        'media_name': 'media-list',
        'description': 'description-list'
    };

    // 필터링 상태에 따라 버튼에 active 클래스 적용
    dataTable.columns().every(function() {
        var column = this;
        var search = column.search();
        if (search) {
            var searchRegex = new RegExp(search.replace(/\\/g, ''));
            var expectedListId = columnToListMap[column.dataSrc()];
            $('.filter_btn').each(function() {
                var btnValue = $.trim($(this).text());
                var parentListId = $(this).closest('.row').attr('id');

                // DOM에 해당 id가 존재하는지 확인
                if (parentListId && $('#' + parentListId).length > 0) {
                    if (searchRegex.test(btnValue) && parentListId === expectedListId) {
                        $(this).addClass('active');
                    }
                }
            });
        }
    });
    // 각 행의 데이터를 순회하면서 고유 값 배열에 추가합니다.
    data.forEach(row => {
        ['advertiser_name', 'media_name', 'event_seq', 'description'].forEach(key => {
            if (!uniqueValues[key].includes(row[key])) { // 배열에 정확히 일치하는 값이 없으면 추가
                uniqueValues[key].push(row[key]);
            }
        });
    });

    // 활성화된 필터 버튼이 없는 경우, 모든 필터 버튼의 'on' 클래스를 제거하고 함수를 종료합니다.
    if (!$('.filter_btn.active').length) {
        $('.filter_btn').removeClass('on');
        return;
    }

    // 각 필터 유형에 대해 'on' 클래스를 토글합니다.
    Object.keys(uniqueValues).forEach(key => {
        let name = key.replace(/_(name|seq)/, '');
        let $list = $(`#${name}-list`);
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

function setFilter(type, data) {
    let $row = $(`.${type} .row`);
    $(`.${type}`).find('.zenith-loading').fadeOut(function(){$(this).remove();});
    let existingIds = $row.find('.filter_btn').map(function() { return $(this).val(); }).get();

    let html = data.filter(v => v && typeof v === 'string' && v.trim() !== '') // 빈 값과 undefined/null 제외
        .map(v => `
            <div class="col">
                <div class="inner">
                    <button type="button" value="${v}" data-btn="${type}" class="${type}_btn filter_btn">
                        <span class="account_name">${v}</span>
                    </button>
                </div>
            </div>
        `).join('');

    $row.html('').append(html);

    //정렬
    let buttons = $row.find('.filter_btn').toArray();
    buttons.sort((a, b) => $(a).find('.account_name').text().localeCompare($(b).find('.account_name').text(), 'en', { sensitivity: 'base' }));
    buttons.forEach(button => $row.append(button.parentNode.parentNode));
}
$('#search_btn').on('click', function(e) {
    e.preventDefault(); // 폼 제출 방지
    dataTable.ajax.reload(); // DataTables의 AJAX 요청을 다시 실행
});

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

$(document).on('focus', 'input[name="unitprice"], input[name="total_price"]', function() {
    var value = $(this).val();
    $(this).val(value.replace(/,/g, ''));
});

$(document).on('blur', 'input[name="unitprice"], input[name="total_price"]', function() {
    var value = parseInt($(this).val());
    if (!isNaN(value)) {
        $(this).val(value.toLocaleString());
    }
});
$(document).on('input', 'input[type="text"]', function() {
    $(this).val($(this).val().replace(/[^0-9]/g, ''));
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
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>
