<?=$this->extend('templates/front.php');?>

<?=$this->section('title');?>
ZENITH - 회계 관리 / 미수금 관리
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
        <h2 class="page-title">미수금 관리</h2>
    </div>
    <div class="row mt-5">
        <div class="col half">
            <h3 class="content-title">미수금 내역</h3>
            <div class="search-wrap">
                <form class="search d-flex justify-content-center">
                    <div class="input">
                        <input class="" type="text" placeholder="검색어를 입력하세요">
                        <button class="btn-primary" type="submit">조회</button>
                    </div>
                </form>
            </div>
            <p class="mb-4">* 2019년 9월 1일부터 세부내역 조회가 가능하며, 문의사항이 있을 경우 경영지원실로 문의하시기 바랍니다.</p>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-default caption-top">
                    <caption class="text-end">[단위: 원, 부가세포함]</caption>
                    <colgroup>
                        <col style="width:10%">
                        <col>
                        <col style="width:20%">
                        <col style="width:17%">
                        <col style="width:15%">
                    </colgroup>
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">담당자</th>
                            <th scope="col">업체명</th>
                            <th scope="col">사업자 번호</th>
                            <th scope="col">최근 세금계산서</th>
                            <th scope="col">미수금</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>김혜린</td>
                            <td>우주마켓_MOBON</td>
                            <td>114-02-34856</td>
                            <td class="text-end">+865일</td>
                            <td class="text-end">2,345,789</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col half">
            <h3 class="content-title">입금 내역</h3>
            <div class="search-wrap">
                <form class="search d-flex justify-content-center">
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
                        <input class="" type="text" placeholder="검색어를 입력하세요">
                        <button class="btn-primary" type="submit">조회</button>
                    </div>
                </form>
            </div>
            <div class="regi-wrap">
                <form class="search d-flex justify-content-center">
                    <!-- <input type="text" name="sdate3" id="sdate3" class="form-control">
                    <button type="button"><i class="bi bi-calendar2-week"></i></button> -->
                    <div class="d-flex align-items-center justify-content-center">
                        <label class="d-flex align-items-center justify-content-center wid100">
                            <input type="text" name="sdate3" id="sdate3" class="form-control data-calendar">
                            <i class="bi bi-calendar2-week ml5"></i>
                        </label>
                        <input type="text" class="form-control" placeholder="광고주">
                        <input type="text" class="form-control" placeholder="사업자등록번호">
                        <input type="text" class="form-control" placeholder="입금액">
                        <input type="text" class="form-control" placeholder="적요/비고">
                        <button class="btn btn-primary ms-2" type="submit">등록</button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-default">
                    <colgroup>
                        <col style="width:15%">
                        <col>
                        <col style="width:20%">
                        <col style="width:15%">
                        <col style="width:15%">
                    </colgroup>
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">날짜</th>
                            <th scope="col">업체명</th>
                            <th scope="col">사업자 번호</th>
                            <th scope="col">입금액</th>
                            <th scope="col">비고</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>2023-3-24</td>
                            <td>우주마켓_MOBON</td>
                            <td>114-02-34856</td>
                            <td class="text-end">34,4567,324</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script>
    setDate('#sdate1, #edate1'); // 범위 날짜 선택기 - 입금내역
    setDateSingle('#sdate3');//단일 날짜 선택기 - 미수금내역
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
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>