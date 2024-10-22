<?=$this->extend('templates/front.php');?>

<?=$this->section('title');?>
ZENITH - 회계 관리 / 세금계산서 요청
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
        <h2 class="page-title">세금계산서 요청</h2>
        <!-- <p class="title-disc">세금계산서 발행을 경영지원실에 요청하세요~ 나연님, 서진님 고생이 많으십니다.</p> -->
    </div>

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
            <select class="form-select" aria-label="선택">
                <option selected>-선택-</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
            </select>
            <div class="input ml15">
                <input class="" type="text" placeholder="검색어를 입력하세요">
                <button class="btn-primary" type="submit">조회</button>
            </div>
        </form>
    </div>

    <div class="section client-list biz">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 담당자</h3>
        <div class="row">
            <div class="col">
                <div class="inner">
                    <button type="button">열혈 패밀리</button>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">케어랩스5</button>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">케어랩스7</button>
                </div>
            </div>
        </div>
    </div>
    <div class="section client-list biz">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 결과</h3>
        <div class="row">
            <div class="col">
                <div class="inner">
                    <button type="button">열혈 패밀리</button>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">케어랩스5</button>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">케어랩스7</button>
                </div>
            </div>
        </div>
    </div>
    <div class="section client-list biz">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 구분</h3>
        <div class="row">
            <div class="col">
                <div class="inner">
                    <button type="button">열혈 패밀리</button>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">케어랩스5</button>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">케어랩스7</button>
                </div>
            </div>
        </div>
    </div>

    <div class="section ">
        <div class="btn-wrap text-end mb-2">
            <a href="/accounting/taxList"><button type="button" class="btn btn-outline-danger">업체목록(세금계산서)</button></a>
            <a href="#"><button type="button" class="btn btn-outline-danger">글쓰기(세금계산서)</button></a>
            <a href="#"><button type="button" class="btn btn-outline-danger">엑셀백업</button></a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-default">
                <colgroup>
                    <col style="width:3%">
                    <col style="width:8%">
                    <col style="width:5%">
                    <col style="width:5%">
                    <col style="width:15%">
                    <col style="width:10%">
                    <col style="width:5%">
                    <col style="">
                    <col style="width:8%">
                    <col style="width:8%">
                    <col style="width:5%">
                    <col style="width:5%">
                </colgroup>
                <thead class="table-dark">
                    <tr>
                        <th scope="col">번호</th>
                        <th scope="col">발행일자</th>
                        <th scope="col">작성자</th>
                        <th scope="col">구분</th>
                        <th scope="col">사업자명</th>
                        <th scope="col">사업자등록번호</th>
                        <th scope="col">대표자명</th>
                        <th scope="col">내역</th>
                        <th scope="col">공급가액</th>
                        <th scope="col">총금액<br>(VAT 포함)</th>
                        <th scope="col">비고</th>
                        <th scope="col">결과</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="p-0">845</td>
                        <td>2023-03-13</td>
                        <td>김혜린</td>
                        <td>입금완료</td>
                        <td>우주마켓_MOBON</td>
                        <td>234-45-12456</td>
                        <td>이주옥</td>
                        <td>인라이플_모비온_우주마켓 광고비 지출결의</td>
                        <td>1,100,000</td>
                        <td>1,100,000</td>
                        <td></td>
                        <td>완료</td>
                    </tr>
                    <tr>
                        <td class="p-0">845</td>
                        <td>2023-03-13</td>
                        <td>김혜린</td>
                        <td>입금완료</td>
                        <td>우주마켓_MOBON</td>
                        <td>234-45-12456</td>
                        <td>이주옥</td>
                        <td>인라이플_모비온_우주마켓 광고비 지출결의</td>
                        <td>1,100,000</td>
                        <td>1,100,000</td>
                        <td>2/28 재발행 예정</td>
                        <td>완료</td>
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
    setDate('#sdate1, #edate1'); // 범위 날짜 선택기 - 세금계산서
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
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>