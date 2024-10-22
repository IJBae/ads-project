<?=$this->extend('templates/front.php');?>

<?=$this->section('title');?>
ZENITH - 회계 관리 / 출금요청
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<script>
    console.log('header')
</script>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<div class="sub-contents-wrap">
    <div class="title-area">
        <h2 class="page-title">출금요청 - 업체목록</h2>
    </div>

    <div class="search-wrap">
        <form class="search d-flex justify-content-center">
            <input type="hidden" id="userid" name="userid">
            <div class="term d-flex align-items-center">
                <label>
                    <input type="text" name="sdate" id="sdate" value="<?= date("Y-m-d", strtotime('-1 month')) ?>" readonly="readonly">
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
                <option value="company">-거래처명</option>
                <option value="no">계좌번호</option>
            </select>
            <div class="input ml15">
                <input class="" type="text" id="stx" name="stx" placeholder="검색어를 입력하세요">
                <button class="btn-primary" type="submit">조회</button>
            </div>
        </form>
    </div>

    <div class="section client-list biz">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 담당자</h3>
        <div class="row" id="nickname_row" name="nickname_row"></div>
    </div>

    <div class="section ">
        <div class="btn-wrap text-end mb-2">
            <button type="button" class="btn btn-danger">업체등록(출금요청)</button>
            <button type="button" class="btn btn-danger" onclick="location.href='<?=base_url()?>accounting/withdraw/'">리스트(출금요청)</button>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-default table-sm">
                <colgroup>
                    <col style="">
                    <col style="width:8%">
                    <col style="width:15%">
                    <col style="width:5%">
                    <col style="width:15%">
                    <col style="width:5%">
                </colgroup>
                <thead class="table-dark">
                    <tr>
                        <th scope="col">거래처명(예금주명)</th>
                        <th scope="col">은행</th>
                        <th scope="col">계좌번호</th>
                        <th scope="col">작성자</th>
                        <th scope="col">작성일</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>(주)당금페이_상상의원3_21783</td>
                        <td>기업은행</td>
                        <td>0492173819374</td>
                        <td>윤재진</td>
                        <td>2023-03-02 :11:34:23</td>
                        <td>
                            <button type="button" class="btn btn-dark btn-sm">DEL</button>
                        </td>
                    </tr>
                    <tr>
                        <td>(주)당금페이_상상의원3_21783</td>
                        <td>기업은행</td>
                        <td>0492173819374</td>
                        <td>윤재진</td>
                        <td>2023-03-02 :11:34:23</td>
                        <td>
                            <button type="button" class="btn btn-dark btn-sm">DEL</button>
                        </td>
                    </tr>
                    <tr>
                        <td>(주)당금페이_상상의원3_21783</td>
                        <td>기업은행</td>
                        <td>0492173819374</td>
                        <td>윤재진</td>
                        <td>2023-03-02 :11:34:23</td>
                        <td>
                            <button type="button" class="btn btn-dark btn-sm">DEL</button>
                        </td>
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
        userList();
        accountList();
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

    function accountList(){
        var data=$(".search").serialize();
        $.ajax({
            url: "<?=base_url()?>accounting/withdrawList/accountlist",
            type: "POST",
            data: data,
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            success: function(data) {
                console.log(data);
            }
        });
    }

    function setVal(val,id){
        $("#"+id).val(val);
        getList();
    }
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>
