<?=$this->extend('templates/front.php');?>

<?=$this->section('title');?>
ZENITH - 회계 관리 / 출금요청
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/css/datatables.css" rel="stylesheet">
<link href="/static/node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="/static/node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet">
<link href="/static/node_modules/datatables.net-staterestore-bs5/css/stateRestore.bootstrap5.min.css" rel="stylesheet">
<script src="/static/node_modules/datatables.net/js/dataTables.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<div class="sub-contents-wrap">
    <div class="title-area">
        <h2 class="page-title">출금요청</h2>
        <!-- <p class="title-disc">출금이 필요한 거래처를 경영지원실에 요청하세요~ 나연님, 서진님 고생이 많으십니다.</p> -->
    </div>

    <div class="search-wrap">
        <form class="search d-flex justify-content-center">
            <div class="term d-flex align-items-center">
                <label>
                    <input type="text" name="sdate" id="sdate" value="<?= date("Y-m-d", strtotime('-1 year +1 day')) ?>" readonly="readonly">
                    <i class="bi bi-calendar2-week"></i>
                </label>
                <span> ~ </span>
                <label>
                    <input type="text" name="edate" id="edate" value="<?= date("Y-m-d") ?>" readonly="readonly">
                    <i class="bi bi-calendar2-week"></i>
                </label>
            </div>
            <select class="form-select" id="stx_opt" name="stx_opt" aria-label="선택">
                <option value="" selected>-선택-</option>
                <option value="company">거래처명</option>
                <option value="detail">내역</option>
            </select>
            <div class="input ml15">
                <input class="" id="stx" name="stx" type="text" placeholder="검색어를 입력하세요">
                <button class="btn-primary" type="submit">조회</button>
            </div>
            <input type="hidden" id="userid" name="userid"/>
            <input type="hidden" id="type" name="type"/>
            <input type="hidden" id="complete" name="complete"/>
        </form>
    </div>
    <div class="section client-list biz">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 담당자</h3>
        <div class="row" id="nickname_row" name="nickname_row"></div>
    </div>
    <div class="section client-list biz">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 결과</h3>
        <div class="row">
            <div class="col">
                <div class="inner">
                    <button type="button" onclick="setVal(1,'complete')">완료</button>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button" onclick="setVal(0,'complete')">진행중</button>
                </div>
            </div>
        </div>
    </div>
    <div class="section client-list biz">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 구분</h3>
        <div class="row" id="type_row" name="type_row"></div>
    </div>
    <div class="section ">
        <div class="btn-wrap text-end mb-2">
            <a href="/accounting/withdrawList"><button type="button" class="btn btn-outline-danger">업체목록(출금요청)</button></a>
            <a href="#"><button type="button" class="btn btn-outline-danger">글쓰기(출금요청)</button></a>
            <button type="button" onclick="excel()" class="btn btn-outline-danger">엑셀백업</button>
        </div>

        <div class="table-responsive" id="withdrawTable">
            <table class="table table-striped table-hover table-default withdrawListTable">
                <!-- <colgroup>
                    <col style="width:3%">
                    <col style="width:8%">
                    <col style="width:5%">
                    <col style="width:5%">
                    <col style="width:10%">
                    <col style="width:8%">
                    <col style="width:10%">
                    <col style="">
                    <col style="width:10%">
                    <col style="">
                    <col style="width:5%">
                    <col style="width:5%">
                    <col style="width:8%">
                </colgroup> -->
                <thead class="table-dark">
                    <tr>
                        <th scope="col">번호</th>
                        <th scope="col">작성일</th>
                        <th scope="col">작성자</th>
                        <th scope="col">구분</th>
                        <th scope="col">거래처명(예금주명)</th>
                        <th scope="col">은행</th>
                        <th scope="col">계좌번호</th>
                        <th scope="col">내역(자세히)</th>
                        <th scope="col">총금액(VAT 포함)</th>
                        <th scope="col">비고</th>
                        <th scope="col">결제현황</th>
                        <th scope="col">결과</th>
                        <th scope="col">출금완료일</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="p-0">845</td>
                        <td>2023-03-13 18:34:39</td>
                        <td>김혜린</td>
                        <td>광고비</td>
                        <td>우주마켓_MOBON</td>
                        <td>국민은행</td>
                        <td>174892038457684</td>
                        <td>인라이플_모비온_우주마켓 광고비 지출결의</td>
                        <td>1,100,000</td>
                        <td></td>
                        <td>진행중</td>
                        <td>진행중</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>845</td>
                        <td>2023-03-13 18:34:39</td>
                        <td>김혜린</td>
                        <td>광고비</td>
                        <td>우주마켓_MOBON</td>
                        <td>국민은행</td>
                        <td>174892038457684</td>
                        <td>인라이플_모비온_우주마켓 광고비 지출결의</td>
                        <td>1,100,000</td>
                        <td></td>
                        <td class="text-primary">완료</td>
                        <td>완료</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script>
    $(document).ready(function() {
        typeList();
        userList();
        getList();
    });

    setDate('#sdate, #edate'); // 범위 날짜 선택기 - 출금요청
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

    $(".btn-primary").click(function(e) {
        e.preventDefault();
        logTable.ajax.reload();//getList();
    });

    // 조회 버튼
    $('form[name="search"]').bind('submit', function(e) {
        e.preventDefault();
        logTable.ajax.reload();//getList();
    });

    // datatable getList
    function getList(){
        logTable = $('#withdrawTable .withdrawListTable').DataTable({
            "dom": '<t>p', 
            "destroy": true,
            "autoWidth": true,
            "responsive": true,
            "processing": true,
            "paging": true,
            "info": false,
            "language": {"url": '/static/js/dataTables.i18n.json'},
            "ajax": {
                "url": "<?=base_url()?>accounting/withdraw/getlist",
                "type": "POST",
                "data": function(d) {
                    let searchData = $(".search").serializeArray();
                    $.each(searchData, function(index, item) {
                        d[item.name] = item.value; // d[name] = value (데이터 이름 = 데이터 값)
                    });
                    console.log('서버로 보내는 데이터:', d); // 서버로 보내는 데이터 로깅
                    return d; 
                },
                "dataType": "json",
                "dataSrc": function (json) {
                    console.log(json);
                    return json; // 또는 json.data 등 실제 데이터 배열을 반환
                }
            },
            columns: [
                { "data": "seq", "width":"7%", "className": "dt-type-center" }, //번호
                { "data": "reg_date", "width":"15%", "className": "dt-type-center" }, //작성일
                { "data": "nickname", "width":"10%", "className": "dt-type-center" }, //작성자
                { "data": "type", "width":"10%", "className": "dt-type-center" },//구분
                { "data": "company_name", "width":"15%", "className": "dt-type-center" },//거래처명 (예금주명)
                { "data": "bank", "width":"15%", "className": "dt-type-center" },//은행명
                { "data": "account", "width":"30%", "className": "dt-type-center" },//계좌번호
                { "data": "detail", "width":"50%", "className": "dt-type-center" },//내역(자세히)
                { "data": "total_price", "width":"15%", "className": "dt-type-center",
                    "render": function (data, type, row) {
                        return Number(row.total_price).toLocaleString('ko-KR');
                    }
                },//총금액(vat포함)
                { "data": "memo", "width":"10%", "className": "dt-type-center" },//비고
                { "data": "gwstatus",
                    "width":"10%",
                    "className": "dt-type-center",
                    "render": function (data, type, row) {
                        if (row.status === '완료' && row.gwstatus && row.gwstatus.startsWith('http')) {
                            return '<a href="' + row.gwstatus + '" target="_gw" class="text-primary" onclick="window.open(this.href, \'NeosMain\', \'width=750,height=900,left=50,top=50,scrollbars=1,resizable=1\'); return false;" class="gw_status gw_status_90" title="' + row.title + '">완료</a>';
                        } else {
                            return '진행중';
                        }
                    }
                },//결제현황 gwstatus
                { "data": "status", "width":"10%", "className": "dt-type-center" },//결과
                { "data": "date", "width":"15%", "className": "dt-type-center" }//출금완료일
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Korean.json"
            }
        });
    }

    function typeList(){
        $.ajax({
            url: "<?=base_url()?>accounting/withdraw/typelist",
            type: "GET",
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            success: function(data) {
                let $container = $('#type_row');
                let i=0;
                $.each(data, function() {
                    let $colDiv = $('<div class="col"></div>');
                    let $innerDiv = $('<div class="inner"></div>');
                    let $button = $('<button type="button" onclick=setVal("'+data[i]['type']+'","type") value="'+data[i]['type']+'"></button>').text(data[i]['type']);
                    $innerDiv.append($button);
                    $colDiv.append($innerDiv);
                    $container.append($colDiv);
                    i++;
                });
            }
        });
    }

    function userList(){
        $.ajax({
            url: "<?=base_url()?>accounting/withdraw/userlist",
            type: "GET",
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            success: function(data) {
                let $container = $('#nickname_row');
                let i=0;
                $.each(data, function() {
                    let $colDiv = $('<div class="col"></div>');
                    let $innerDiv = $('<div class="inner"></div>');
                    let $button = $('<button type="button" onclick=setVal("'+data[i]['id']+'","userid")></button>').text(data[i]['nickname']);
                    $innerDiv.append($button);
                    $colDiv.append($innerDiv);
                    $container.append($colDiv);
                    i++;
                });
            }
        });
    }
    function setVal(val,id){
        $("#"+id).val(val);
        logTable.ajax.reload();//getList();
    }

    $('body').on('click', '.client-list button', function() {
        $(this).toggleClass('active');
        logTable.ajax.reload();//getList();
    });

    function excel(){
        var data = $(".search").serialize();
        $.ajax({
            url: "<?=base_url()?>accounting/withdraw/excel",
            type: "POST",
            data:data,
            contentType: 'application/json; charset=utf-8',
            xhrFields: {
                responseType: 'blob' // 바이너리 데이터로 응답 받기
            },
            success: function(response, status, xhr) {
                const contentType = xhr.getResponseHeader('Content-Type');
                if (contentType && contentType.indexOf('application/json') !== -1) {
                    // 응답이 JSON인 경우
                    if(response.status == 'error') {
                        alert(response.message);
                    }
                } else {
                    // 응답이 Blob인 경우
                    const blob = new Blob([response], { type: contentType });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `출금요청_<?=date('Y-m-d_H-i-s')?>.xlsx`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr, status, error)
                alert(xhr, status, error);
            }
        });
    }
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>