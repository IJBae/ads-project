<!--헤더-->
<?=$this->section('header');?>
<link href="/static/css/scheduler.css" rel="stylesheet">  
<script src="/static/js/scheduler.js"></script>
<?=$this->endSection();?>

<div class="modal automationModal fade" id="automationModal" tabindex="-1" aria-labelledby="automationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-body">
                <div class="regi-content">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="step">
                        <ol id="myTab" role="tablist">
                            <li class="active" type="button" id="schedule-tab"  data-bs-toggle="tab" data-bs-target="#schedule" type="button" aria-controls="schedule" aria-selected="true">
                                <strong>일정</strong>
                            </li>
                            <li id="target-tab" data-bs-toggle="tab" data-bs-target="#target" type="button" aria-controls="target" aria-selected="false">
                                <strong>대상</strong>   
                            </li>
                            <li id="condition-tab" data-bs-toggle="tab" data-bs-target="#condition" type="button" aria-controls="condition" aria-selected="false">
                                <strong>조건</strong>
                                <p id="text-condition-1">
                                    <span class="typeText"></span>
                                    <span class="typeValueText"></span>
                                    <span class="compareText"></span>
                                </p>
                            </li>
                            <li id="preactice-tab" data-bs-toggle="tab" data-bs-target="#preactice" type="button" aria-controls="preactice" aria-selected="false">
                                <strong>실행</strong>
                            </li>
                            <li id="detailed-tab" data-bs-toggle="tab" data-bs-target="#detailed" type="button" aria-controls="messages" aria-selected="false">
                                <strong>상세정보</strong>
                                <p id="detailText">
                                    <span id="subjectText"></span><br>
                                    <span id="descriptionText"></span>
                                </p>
                            </li>
                        </ol>
                    </div>
                    <div class="detail-wrap">
                        <input type="hidden" name="seq">
                        <div class="detail show active" id="schedule" role="tabpanel" aria-labelledby="schedule-tab" tabindex="0"> 
                            <table id="scheduleTable"></table>
                            <label for="onceExecution" class="mt15"><input type="checkbox" name="exec_once" id="onceExecution"> 하루에 한번만 실행</label>
                        </div>
                        <div class="detail" id="target" role="tabpanel"  aria-labelledby="target-tab" tabindex="1">
                            <ul class="tab" id="targetTab">
                                <li class="active" data-tab="advertiser"><a href="javascript:void(0);">광고주</a></li>
                                <li data-tab="account"><a href="javascript:void(0);">매체광고주</a></li>
                                <li data-tab="campaign"><a href="javascript:void(0);">캠페인</a></li>
                                <li data-tab="adgroup"><a href="javascript:void(0);">광고그룹</a></li>
                                <li data-tab="ad"><a href="javascript:void(0);">광고</a></li>
                            </ul>
                            <div id="targetCreateType" class="targetCreateType">
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input" type="radio" name="target_create_type" value="target_sum" id="targetSum">
                                    <label class="form-check-label" for="targetSum">
                                        합산 적용
                                    </label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input" type="radio" name="target_create_type" value="target_seperate" id="targetSeperate">
                                    <label class="form-check-label" for="targetSeperate">
                                        개별 적용
                                    </label>
                                </div>
                            </div>
                            <div class="search">
                                <form name="search-target-form" class="search d-flex justify-content-center w-100">
                                    <div class="input">
                                        <input type="text" placeholder="검색어를 입력하세요" id="showTargetAdv">
                                        <button class="btn-primary" id="search_target_btn" type="submit">조회</button>
                                    </div>
                                </form>
                            </div>
                            <!-- <table class="table tbl-header w-100" id="targetCheckedTable">
                                <colgroup>
                                    <col style="width:12%">
                                    <col style="width:12%">
                                    <col style="width:28%">
                                    <col style="width:30%">
                                    <col style="width:10%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th scope="col" colspan="5"  class="text-center">선택 항목(선택 후 상단 탭 클릭 시 소속 항목 조회가 가능합니다)</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table> -->
                            <!-- 대상 컨텐츠 -->
                            <p class="sche-info-txt">선택 항목(선택 후 상단 탭 클릭 시 소속 항목 조회가 가능합니다)</p>
                            <table class="table tbl-header targetTable w-100" id="targetTable">
                                <!-- <colgroup>
                                    <col style="width:12%">
                                    <col style="width:12%">
                                    <col style="width:26%">
                                    <col style="width:30%">
                                    <col style="width:12%">
                                    <col style="width:8%">
                                </colgroup> -->
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">매체</th>
                                        <th scope="col" class="text-center">분류</th>
                                        <th scope="col" class="text-center">ID</th>
                                        <th scope="col" class="text-center">제목</th>
                                        <th scope="col" class="text-center">상태</th>
                                        <th scope="col" class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <table class="table tbl-header targetSelectTable w-100 mt-4" id="targetSelectTable">
                                <colgroup>
                                    <col style="width:17%">
                                    <col style="width:17%">
                                    <col style="width:17%">
                                    <col style="width:17%">
                                    <col style="width:20%">
                                    <col style="width:11%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th scope="col" colspan="6"  class="text-center">적용 항목</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <!-- 조건 컨텐츠 -->
                        <div class="detail" id="condition" role="tabpanel" aria-labelledby="condition-tab" tabindex="2">
                            <div class="d-flex align-items-center mb15">
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input" type="radio" name="operation" value="and" id="operationAnd">
                                    <label class="form-check-label" for="operationAnd">
                                        모두 일치
                                    </label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input" type="radio" name="operation" value="or" id="operationOr">
                                    <label class="form-check-label" for="operationOr">
                                        하나만 일치
                                    </label>
                                </div>
                            </div>
                            <table class="table tbl-header conditionTable" id="conditionTable">
                                <colgroup>
                                    <col>
                                    <col style="width:45%">
                                    <col style="width:10%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th scope="col">항목</th>
                                        <th scope="col">구분</th>
                                        <th scope="col"><button type="button" class="btn-add">추가</button></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="condition-1">
                                        <td>
                                            <div class="form-flex">
                                                <select name="type" class="form-select conditionType">
                                                    <option value="">조건 항목</option>
                                                    <option value="status">상태</option>
                                                    <option value="budget">예산</option>
                                                    <option value="dbcost">DB단가</option>
                                                    <option value="unique_total">유효DB</option>
                                                    <option value="spend">지출액</option>
                                                    <option value="margin">수익</option>
                                                    <option value="margin_rate">수익률</option>
                                                    <option value="sales">매출액</option>
                                                    <!-- <option value="impression">노출수</option>
                                                    <option value="click">링크클릭</option>
                                                    <option value="cpc">CPC</option>
                                                    <option value="ctr">CTR</option> -->
                                                    <option value="conversion">DB전환률</option>
                                                </select>
                                                <select name="type_value_status" class="form-select conditionTypeValueStatus" style="display: none;">
                                                    <option value="">상태값 선택</option>
                                                    <option value="ON">ON</option>
                                                    <option value="OFF">OFF</option>
                                                </select>
                                                <input type="text" name="type_value" class="form-control conditionTypeValue" placeholder="조건값" oninput="onlyNumberLeadingDashAndDot(this);">
                                            </div>
                                        </td>
                                        <td colspan="2">
                                            <div class="form-flex">
                                            <select name="compare" class="form-select conditionCompare">
                                                <option value="">일치여부</option>
                                                <option value="greater">초과</option>
                                                <option value="greater_equal">보다 크거나 같음</option>
                                                <option value="less">미만</option>
                                                <option value="less_equal">보다 작거나 같음</option>
                                                <option value="equal">같음</option>
                                                <option value="not_equal">같지않음</option>
                                            </select>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- 실행 컨텐츠 -->
                        <div class="detail" id="preactice" role="tabpanel" aria-labelledby="preactice-tab" tabindex="3">
                            <div id="execSearchWrap">
                                <ul class="tab" id="execTab">
                                    <li class="active" data-tab="campaign"><a href="javascript:void(0);">캠페인</a></li>
                                    <li data-tab="adgroup"><a href="javascript:void(0);">광고그룹</a></li>
                                    <li data-tab="ad"><a href="javascript:void(0);">광고</a></li>
                                </ul>
                                <div class="search">
                                    <div class="d-flex align-items-center mb-2">
                                        <input class="form-check-input" type="checkbox" value="1" id="searchAll">
                                        <label class="form-check-label" for="searchAll">
                                            전체검색
                                        </label>
                                    </div>
                                    <form name="search-exec-form" class="search d-flex justify-content-center w-100">
                                    <div class="input">
                                            <input type="text" placeholder="검색어를 입력하세요" id="showExecAdv">
                                            <button class="btn-primary" id="search_exec_btn" type="submit">조회</button>
                                    </div>
                                    </form>
                                </div>
                            
                                <table class="table tbl-header execTable w-100 mt-4" id="execTable">
                                    <!-- <colgroup>
                                        <col style="width:10%">
                                        <col style="width:10%">
                                        <col style="width:30%">
                                        <col style="width:40%">
                                        <col style="width:10%">
                                    </colgroup> -->
                                    <thead>
                                        <tr>
                                            <th scope="col">매체</th>
                                            <th scope="col">분류</th>
                                            <th scope="col">ID</th>
                                            <th scope="col">제목</th>
                                            <th scope="col">상태</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <table class="table tbl-header w-100 mt-4 execSelectTable" id="execSelectTable">
                                <colgroup>
                                    <col style="width:10%">
                                    <col style="width:10%">
                                    <col style="width:10%">
                                    <col style="width:10%">
                                    <col style="width:10%">
                                    <col style="width:11%">
                                    <col style="width:11%">
                                    <col style="width:18%">
                                    <col style="width:10%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th scope="col" colspan="9"  class="text-center">
                                            적용 항목
                                            <div class="callTargetBtnBox">
                                                <button class="callTargetBtn btn-primary">대상 불러오기</button>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5">일괄 적용</td>
                                        <td colspan="2">
                                            <div class="mb5">
                                                <select name="all_exec_condition_type" class="form-select">
                                                    <option value="">실행항목</option>
                                                    <option value="status">상태</option>
                                                    <option value="budget">예산</option>
                                                </select>
                                            </div>
                                            <select name="all_exec_condition_type_budget" class="form-select">
                                                <option value="">단위</option>
                                                <option value="won">원</option>
                                                <option value="percent">%</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="all_exec_condition_value_status" class="form-select" style="display: none;">
                                                <option value="">상태값</option>
                                                <option value="ON">ON</option>
                                                <option value="OFF">OFF</option>
                                            </select>
                                            <input type="text" name="all_exec_condition_value" class="form-control"placeholder="예산">
                                        </td> 
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                            <table class="table tbl-header w-100 mt-4 slackSendTable" id="slackSendTable">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">슬랙 메세지 보내기</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="form-flex">
                                                <input type="text" name="slack_webhook" class="form-control" placeholder="웹훅 URL">
                                                <input type="text" name="slack_msg" class="form-control" placeholder="메세지">
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <p><a href="https://carelabs-dm.atlassian.net/wiki/external/NGU5YTNlYjYwYTI0NDliMjg5NzhiYTg3MjEwZTJhODc" target="_blank">▶ 슬랙 웹훅 생성 메뉴얼 참고</a></p>
                        </div>
                        <!-- 상세정보 컨텐츠 -->
                        <div class="detail" id="detailed" role="tabpanel" aria-labelledby="detailed-tab" tabindex="4">
                            <table class="table tbl-side" id="detailTable">
                                <colgroup>
                                    <col style="width:35%">
                                    <col>
                                </colgroup>
                                <tr>
                                    <th scope="row">제목*</th>
                                    <td>
                                        <input type="text" name="subject" class="form-control bg">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">설명</th>
                                    <td>
                                        <textarea name="description" class="form-control"></textarea>
                                    </td>
                                </tr>
                            </table>
                            <!-- <div class="btn-area">
                                <button type="button" id="createAutomationBtn" class="btn-special">저장</button>
                                <button type="button" id="updateAutomationBtn" class="btn-special" style="display: none;">수정</button>
                            </div> -->
                        </div>
                    </div>
                </div>
                <div class="btn-area">
                    <button type="button" id="createAutomationBtn" class="btn-special">저장</button>
                    <button type="button" id="updateAutomationBtn" class="btn-special" style="display: none;">수정</button>
                </div>
            </div>
        </div>
    </div>
</div>
