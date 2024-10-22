<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    ZENITH - 이벤트 / 이벤트 관리
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<!-- 임시저장 -->
<link href="/static/css/datatables.css" rel="stylesheet">
<link href="/static/node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet"> 
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
<script src="/static/js/tag-it.min.js"></script>
<style>
    ul.tagit {
        padding: 1px 5px;
        overflow: auto;
        margin-left: inherit; /* usually we don't want the regular ul margins. */
        margin-right: inherit;
    }
    ul.tagit li {
        display: block;
        float: left;
        margin: 2px 5px 2px 0;
    }
    ul.tagit li.tagit-choice {    
        position: relative;
        line-height: inherit;
    }
    input.tagit-hidden-field {
        display: none;
    }
    ul.tagit li.tagit-choice-read-only { 
        padding: .2em .5em .2em .5em; 
    } 

    ul.tagit li.tagit-choice-editable { 
        padding: .2em 18px .2em .5em; 
    } 

    ul.tagit li.tagit-new {
        padding: .25em 4px .25em 0;
    }

    ul.tagit li.tagit-choice a.tagit-label {
        cursor: pointer;
        text-decoration: none;
    }
    ul.tagit li.tagit-choice .tagit-close {
        cursor: pointer;
        position: absolute;
        right: .1em;
        top: 50%;
        margin-top: -8px;
        line-height: 17px;
    }

    /* used for some custom themes that don't need image icons */
    ul.tagit li.tagit-choice .tagit-close .text-icon {
        display: none;
    }

    ul.tagit li.tagit-choice input {
        display: block;
        float: left;
        margin: 2px 5px 2px 0;
    }
    ul.tagit input[type="text"] {
        -moz-box-sizing:    border-box;
        -webkit-box-sizing: border-box;
        box-sizing:         border-box;

        -moz-box-shadow: none;
        -webkit-box-shadow: none;
        box-shadow: none;

        border: none;
        margin: 0;
        padding: 0;
        width: inherit;
        background-color: inherit;
        outline: none;
    }

    .ui-autocomplete{
        z-index: 10000000;
        max-height: 300px;
        overflow-y: auto; /* prevent horizontal scrollbar */
        overflow-x: hidden;
    }

    .ads_status.enabled{
        display:inline-block;
        width:10px;height:10px;
        border-radius:100%;
        background:#3CB043;
    }
    .ads_status.disabled{
        display:inline-block;
        width:10px;height:10px;
        border-radius:100%;
        background:#FF0000;
    }
    .btn_landing.hide{
        display: none !important;
    }
    .create-btn-wrap, .update-btn-wrap{
        display: none;
    }
    .sub-contents-wrap .dt-container table.dataTable tbody tr td:first-of-type {padding:12px 0 12px 12px;}
    .sub-contents-wrap .event_seq{position:relative; z-index:1;padding:0;}
    /* 이벤트 관리에만 사용 */
    /* .dataTable tbody tr td:first-child{overflow:hidden;} */
</style>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<?php 
$site_name = "";
if(getenv("MY_SERVER_NAME") == 'carelabs')$site_name = "행부기"; 
else if(getenv("MY_SERVER_NAME") == 'resta') $site_name = "콜렉션";
?>
<div class="sub-contents-wrap eventmanage-container">
    <div class="title-area">
        <h2 class="page-title">이벤트 관리</h2>
    </div>

    <div class="search-wrap">
        <form name="search-form" class="search d-flex justify-content-center">
            <!-- <div class="term d-flex align-items-center">
                <label><input type="text" name="sdate" id="sdate" autocomplete="off"><i class="bi bi-calendar2-week"></i></label>
                <span> ~ </span>
                <label><input type="text" name="edate" id="edate" autocomplete="off"><i class="bi bi-calendar2-week"></i></label>
            </div> -->
            <div class="input">
                <input type="text" name="stx" id="stx" placeholder="검색어를 입력하세요">
                <button class="btn-primary" id="search_btn" type="submit">조회</button>
                <button class="btn-special ms-2" id="createBtn" data-bs-toggle="modal" data-bs-target="#regiModal" type="button">등록</button>
            </div>
        </form>
    </div>

    <div class="section position-relative">
        <div class="btn-wrap">
            <a href="/eventmanage/advertiser"><button type="button" class="btn btn-outline-danger">광고주 관리</button></a>
            <a href="/eventmanage/media"><button type="button" class="btn btn-outline-danger">매체 관리</button></a>
            <a href="/eventmanage/change"><button type="button" class="btn btn-outline-danger">전환 관리</button></a>
            <a href="/eventmanage/blacklist"><button type="button" class="btn btn-outline-danger">블랙리스트 관리</button></a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-default"  id="event-table">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">이벤트<br>번호</th>
                        <th scope="col">광고주</th>
                        <th scope="col">매체</th>
                        <th scope="col">브라우저 타이틀</th>
                        <th scope="col">이벤트 구분</th>
                        <th scope="col">외부<br>연동</th>
                        <th scope="col">사용<br>여부</th>
                        <th scope="col">유입수</th>
                        <th scope="col">유효DB</th>
                        <th scope="col">DB단가</th>
                        <th scope="col">작성자</th>
                        <th scope="col">디자이너</th>
                        <th scope="col">개발자</th>
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
<!-- 이벤트 등록 -->
<div class="modal fade" id="regiModal" tabindex="-1" aria-labelledby="regiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form name="event-register-form" id="event-register-form">
                <div class="modal-header">
                    <h1 class="modal-title" id="regiModalLabel"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h2 class="body-title">이벤트 정보</h2>
                    <div class="table-responsive">    
                        <input type="hidden" name="seq" value="">
                        <input type="hidden" name="advertiser" value="">
                        <input type="hidden" name="media" value="">
                        <table class="table table-bordered table-left-header">
                            <colgroup>
                                <col style="width:30%;">
                                <col style="width:70%;">
                            </colgroup>
                            <tbody>
                                <tr class="landing_info">
                                    <th scope="row" class="text-end">랜딩번호</th>
                                    <td class="landing_info_num"></td>
                                </tr>
                                <tr class="landing_info">
                                    <th scope="row" class="text-end">랜딩주소</th>
                                    <td>
                                        <a href="#" target="_blank" class="landing_info_link"></a>
                                        <button type="button" class="btn btn-secondary btn-sm copy">복사하기</button>
                                        <p class="mt-2 text-secondary"><label><input type="checkbox" name="no_hash" value="1"> 일반주소 같이 사용</label></p>
                                        <?php if(!empty($site_name)) {?><p class="mt-2 text-secondary"><label><input type="checkbox" name="another_url" value="1"> <?php echo $site_name;?> 사용</label></p><?php } ?>
                                    </td>
                                </tr>
                                <!-- <tr>
                                    <th scope="row" class="text-end">카카오싱크</th>
                                    <td class="kakaosync">
                                        <p class="desc hide"><span class="sch_word copy" data-clipboard-text=""></span></p>
                                        <p class="desc hide urlencode"><input type="text" name="encodeTxt" class="form-control" value="site=100"><i class="copy" data-clipboard-text=""></i></p>
                                    </td>
                                </tr> -->
                                <tr>
                                    <th scope="row" class="text-end">광고주</th>
                                    <td><input type="text" class="form-control" name="adv_name" placeholder="광고주명을 입력하세요." autocomplete="off"></td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-end">매체</th>
                                    <td><input type="text" class="form-control" name="media_name" placeholder="광고매체를 입력하세요." autocomplete="off"></td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-end">이벤트 구분</th>
                                    <td><input type="text" name="description" class="form-control" placeholder="이벤트구분을 입력하세요." autocomplete="off"></td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-end">DB 단가</th>
                                    <td><input type="text" name="db_price" class="form-control" placeholder="DB 단가를 입력하세요." autocomplete="off"></td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-end">랜딩 사용여부</th>
                                    <td>
                                        <div class="d-flex radio-wrap">
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="radio" value="0" name="is_stop" id="is_stop01" checked>
                                                <label class="form-check-label" for="is_stop01">사용</label>
                                            </div>
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="radio" value="1"  name="is_stop" id="is_stop02">
                                                <label class="form-check-label" for="is_stop02">미사용</label>
                                            </div>
                                        </div>
                                        <p class="mt-2 text-secondary">※ 광고주가 사용중지로 되어있을 경우 랜딩은 노출되지 않습니다.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-end">랜딩구분</th>
                                    <td>
                                        <div class="d-flex radio-wrap">
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="radio" value="0" name="lead" id="lead01" checked>
                                                <label class="form-check-label" for="lead01">일반</label>
                                            </div>
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="radio" value="1"  name="lead" id="lead02">
                                                <label class="form-check-label" for="lead02">잠재고객</label>
                                            </div>
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="radio" value="4" name="lead" id="lead03">
                                                <label class="form-check-label" for="lead03">비즈폼</label>
                                            </div>
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="radio" value="2" name="lead" id="lead04">
                                                <label class="form-check-label" for="lead04">엑셀업로드</label>
                                            </div>
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="radio" value="3" name="lead" id="lead05">
                                                <label class="form-check-label" for="lead05">API수신</label>
                                            </div>
                                        </div>
                                        <div class="mb-2" id="bizform">
                                            <input type="text" name="creative_id" class="form-control" style="float:left;width:49%;margin-right:2%" placeholder="소재번호를 입력하세요." title="소재 번호" autocomplete="off">
                                            <input type="text" name="bizform_apikey"  class="form-control" style="float:left;width:49%" placeholder="비즈폼 API KEY를 입력하세요." title="소재 번호">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-end">외부연동 사용여부</th>
                                    <td>
                                        <div class="d-flex radio-wrap">
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="radio" value="1" name="interlock" id="interlock01">
                                                <label class="form-check-label" for="interlock01">사용</label>
                                            </div>
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="radio" value="0"  name="interlock" id="interlock02" checked>
                                                <label class="form-check-label" for="interlock02">미사용</label>
                                            </div>
                                        </div>
                                        <div class="interlock_code mt5">
                                            <div class="d-flex mb-2">
                                                <input type="text" name="partner_id" placeholder="파트너아이디" class="form-control me-2">
                                                <input type="text" name="partner_name" placeholder="파트너명" class="form-control">
                                            </div>
                                            <div class="d-flex">
                                                <input type="text" name="paper_code" placeholder="지면코드" class="form-control me-2">
                                                <input type="text" name="paper_name" placeholder="지면명" class="form-control">
                                            </div>
                                        </div>        
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-end">랜딩 고정값</th>
                                    <td>
                                        <input type=hidden name="custom">
                                        <div class="custom-row-wrap">
                                            <div class="update_custom_box"></div>
                                            <div class="d-flex mb-2 custom_row">
                                                <select name="custom_key" class="custom form-select me-2" aria-label="선택">
                                                    <option selected disabled>개별설정 안함</option>
                                                    <option value="branch">지점</option>
                                                    <option value="sms_number">문자 발송번호</option>
                                                </select>
                                                <input type="text" class="form-control" id="custom_val" autocomplete="off">
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-primary btn-sm float-end" id="add_custom">추가</button>
                                    </td>
                                </tr>
                                <tr class="leadHide">
                                    <th scope="row" class="text-end">브라우저 제목(타이틀)</th>
                                    <td><input type="text" name="title" class="form-control" placeholder="이벤트를 잘 표현할 수 있는 핵심단어를 사용하여 제목을 입력해주세요.(약 20자내외)"></td>
                                </tr>
                                <tr class="leadHide">
                                    <th scope="row" class="text-end">키워드</th>
                                    <td>
                                        <input type="text" name="keyword" class="form-control" id="tag">
                                        <p class="mt-2 text-secondary">※ 향후 이벤트를 쉽게 찾을 수 있는 키워드를 입력해주세요.</p>
                                    </td>
                                </tr>
                                <tr class="leadHide">
                                    <th scope="row" class="text-end">설명</th>
                                    <td><input type="text" name="subtitle" class="form-control" placeholder="이벤트를 구체적으로 설명해주세요. (약 40자내외, 최대 100자이내)" autocomplete="off"></td>
                                </tr>
                                <tr class="leadHide">
                                    <th scope="row" class="text-end">수집목적</th>
                                    <td><input type="text" name="object" class="form-control" placeholder="ex) 라식,라섹" autocomplete="off"></td>
                                </tr>
                                <tr class="leadHide">
                                    <th scope="row" class="text-end">수집항목</th>
                                    <td><input type="text" name="object_items" placeholder="ex) 이름,나이,전화번호" class="form-control" autocomplete="off"></td>
                                </tr>
                                <tr class="leadHide">
                                    <th scope="row" class="text-end">페이스북 Pixel ID</th>
                                    <td><input type="text" class="form-control" name="pixel_id" placeholder="페이스북 픽셀을 사용하는 경우 체인쏘우에 등록된 픽셀ID를 입력해 주세요." autocomplete="off"></td>
                                </tr>
                                <tr class="leadHide">
                                    <th scope="row" class="text-end">View Script</th>
                                    <td><textarea name="view_script" id="view_script" placeholder="추적스크립트(View)"></textarea></td>
                                </tr>
                                <tr class="leadHide">
                                    <th scope="row" class="text-end">Complete Script</th>
                                    <td><textarea name="done_script" id="done_script" placeholder="추적스크립트(Complete)"></textarea></td>
                                </tr>
                            </tbody>
                        </table>           
                    </div>
                    <h2 class="body-title mt-4">인정기준</h2>
                    <div class="table-responsive">
                        <table class="table table-bordered table-left-header">
                            <colgroup>
                                <col style="width:30%;">
                                <col style="width:70%;">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <th scope="row" class="text-end">성별</th>
                                    <td>
                                        <div class="d-flex radio-wrap">
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="radio" value="0" name="check_gender" id="check_gender01" checked>
                                                <label class="form-check-label" for="check_gender01">무관</label>
                                            </div>
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="radio" value="m"  name="check_gender" id="check_gender02">
                                                <label class="form-check-label" for="check_gender02">남</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="f"  name="check_gender" id="check_gender03">
                                                <label class="form-check-label" for="check_gender03">여</label>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-end">나이</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <input type="text" name="check_age_min" class="form-control" autocomplete="off">
                                            <span class="m-2">~</span>
                                            <input type="text" name="check_age_max" class="form-control" autocomplete="off">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-end">중복기간</th>
                                    <td>
                                        <div class="d-flex radio-wrap mb5">
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="radio" value="1" name="duplicate_term" id="duplicate_term01">
                                                <label class="form-check-label" for="duplicate_term01">1일</label>
                                            </div>
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="radio" value="7" name="duplicate_term" id="duplicate_term02">
                                                <label class="form-check-label" for="duplicate_term02">1주</label>
                                            </div>
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="radio" value="30" name="duplicate_term" id="duplicate_term03">
                                                <label class="form-check-label" for="duplicate_term03">1개월</label>
                                            </div>
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="radio" value="60" name="duplicate_term" id="duplicate_term04">
                                                <label class="form-check-label" for="duplicate_term04">2개월</label>
                                            </div>
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="radio" value="90" name="duplicate_term" id="duplicate_term05">
                                                <label class="form-check-label" for="duplicate_term05">3개월</label>
                                            </div>
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="radio" value="120" name="duplicate_term" id="duplicate_term06">
                                                <label class="form-check-label" for="duplicate_term06">4개월</label>
                                            </div>
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="radio" value="150" name="duplicate_term" id="duplicate_term07">
                                                <label class="form-check-label" for="duplicate_term07">5개월</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="180" name="duplicate_term" id="duplicate_term08" checked>
                                                <label class="form-check-label" for="duplicate_term08">전체</label>
                                            </div>
                                        </div>
                                        <p class="text-secondary">※ 중복기간 변경이후 시점의 DB부터 적용됩니다.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-end">전화번호 중복</th>
                                    <td>
                                        <div class="d-flex radio-wrap mb5">
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="radio" value="0"  name="check_phone" id="check_phone01">
                                                <label class="form-check-label" for="check_phone01">미사용</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="1"  name="check_phone" id="check_phone02" checked>
                                                <label class="form-check-label" for="check_phone02">사용</label>
                                            </div>
                                        </div>
                                        <p class="text-secondary">※ 전화번호 중복 사용/미사용 변경이후 시점의 DB부터 적용됩니다.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-end">이름 중복</th>
                                    <td>
                                        <div class="d-flex radio-wrap mb5">
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="radio" value="0"  name="check_name" id="check_name01" checked>
                                                <label class="form-check-label" for="check_name01">미사용</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="1"  name="check_name" id="check_name02">
                                                <label class="form-check-label" for="check_name02">사용</label>
                                            </div>
                                        </div>
                                        <p class="text-secondary">※ 이름 중복 사용/미사용 변경이후 시점의 DB부터 적용됩니다.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-end">사전 중복 체크</th>
                                    <td>
                                        <select class="form-select me-2" aria-label="선택" name='duplicate_precheck'>
                                            <option value="0">사전 중복 체크 안함</option>
                                            <option value="1">동일 랜딩 내의 이름과 연락처 중복 여부</option>
                                            <option value="2">동일 광고주 내의 이름과 연락처 중복 여부</option>
                                            <option value="3">동일 광고주와 매체 내의 이름과 연락처 중복 여부</option>
                                            <option value="4">동일 랜딩 내의 IP 중복 여부</option>
                                            <option value="5">동일 광고주 내의 IP 중복 여부</option>
                                            <option value="6">동일 광고주와 매체 내의 IP 중복 여부</option>
                                            <option value="7">동일 랜딩 내의 연락처 중복 여부</option>
                                            <option value="8">동일 광고주 내의 연락처 중복 여부</option>
                                            <option value="9">동일 광고주와 매체 내의 연락처 중복 여부</option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="create-btn-wrap">
                        <button type="submit" class="btn btn-primary" form="event-register-form" id="createActionBtn">랜딩 생성</button>
                    </div>
                    <div class="update-btn-wrap">
                        <button type="button" class="btn btn-primary" id="copyActionBtn">복사</button>
                        <button type="button" class="btn btn-danger"  id="deleteActionBtn">삭제</button>
                        <button type="submit" class="btn btn-primary" form="event-register-form" id="updateActionBtn">랜딩 수정</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- //이벤트 등록 -->
<!-- 이벤트 랜딩보기 -->
<div class="modal fade" id="landingView" tabindex="-1" aria-labelledby="landingViewLabel"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="landingViewLabel">랜딩보기</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe src="" id="eventContent" width="100%" height="700"></iframe>
            </div>
        </div>
    </div>
</div>
<!-- //이벤트 랜딩보기 -->

<!-- 유입수 보기 -->
<div class="modal fade" id="impressionView" tabindex="-1" aria-labelledby="impressionViewLabel"  aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="impressionViewLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="dataTable table table-bordered" id="impression-view-table">
                        <!-- <colgroup>
                            <col style="width:30%;">
                            <col style="width:30%;">
                            <col style="width:40%;">
                        </colgroup> -->
                        <thead>
                            <tr>
                                <th scope="col">매체코드</th>
                                <th scope="col">사이트</th>
                                <th scope="col">유입수</th>
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
<!-- //유입수 보기 -->
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script>

if(window.localStorage.getItem('event_advertiser_name')){
    var advertiser = window.localStorage.getItem('event_advertiser_name');
    var advertiser = JSON.parse(advertiser);
    $('#stx').val(advertiser.advertiser);
    window.localStorage.removeItem('event_advertiser_name');
}

if(window.localStorage.getItem('event_media_name')){
    var media = window.localStorage.getItem('event_media_name');
    var media = JSON.parse(media);
    $('#stx').val(media.media);
    window.localStorage.removeItem('event_media_name');
}

let data = {};
let dataTable;

setDate();
getList();

function setData() {
    data = {
        'sdate': $('#sdate').val(),
        'edate': $('#edate').val(),
        'stx': $('#stx').val(),
    };

    return data;
}

function getList(){
    $.fn.DataTable.ext.pager.numbers_length = 10;
    dataTable = $('#event-table').DataTable({
        "dom": '<r<t>ip>',
        "autoWidth": true,
        "fixedHeader": true,
        "order": [[0,'desc']],
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
            "url": "<?=base_url()?>/eventmanage/event/list",
            "data": function(d) {
                d.searchData = setData();
            },
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
        },
        "columnDefs": [
            { targets: [10], orderable: false},
            { targets: [11], orderable: false},
            { targets: [12], orderable: false},
        ],
        "columns": [
            { 
                "data": "seq",
                "width": "8%",
                "render": function(data, type, row) {
                    config = '';
                    if(row.config == 'disabled'){
                        config = '<span class="ads_status '+row.config+'" title="광고 비활성화"></span>';
                    }

                    if(row.config == 'enabled'){
                        config = '<span class="ads_status '+row.config+'" title="광고 운영중"></span>';
                    }
                    //돋보기 아이콘 추가
                    searchIcon = '';
                    searchIcon = '<button data-bs-target="#landingView" data-bs-toggle="modal" data-link="'+row.event_url+'" class="btn_search btn_landing hide" data-filename="v_'+row.seq+'"><i class="bi bi-search"></i></button>';

                    ico = '';
                    if(row.another_url == 1)
                        ico = `<span class="another_url">🐢</span>`;
                    return config+ico+searchIcon+'<button data-text="'+row.event_url+'" class="event_seq copy" data-clipboard-target=".modal" data-clipboard-text="'+row.event_url+'">'+row.hash_no+'</button>';
                }
            },
            { 
                "data": "advertiser_name", 
                "width": "12%",
                "render": function(data, type, row) {
                    // adv_name = '<div class="landing-box"><button type="button" id="updateBtn" data-bs-toggle="modal" data-bs-target="#regiModal">'+(data ? data : '')+'</button>'+'<button data-bs-target="#landingView" data-bs-toggle="modal" data-link="'+row.event_url+'" class="btn_landing hide" data-filename="v_'+row.seq+'">[랜딩보기]</button></div>';
                    adv_name = '<div class="landing-box"><button type="button" id="updateBtn" data-bs-toggle="modal" data-bs-target="#regiModal">'+(data ? data : '')+'</button></div>';
                    return adv_name;
                }
            },
            { "data": "media_name", "width": "8%"},
            { "data": "title", "width": "16%"},
            { "data": "description","width": "16%"},
            { "data": "interlock", "width": "4%"},
            { 
                "data": "is_stop",
                "width": "4%",
                "render": function(data, type, row) {
                    if(data != 'O'){
                        is_stop = '<span class="is_stop">'+data+'</span>';  
                    }else{
                        is_stop = '<span class="is_ing">'+data+'</span>';     
                    }        
                    return is_stop;
                }
            },
            { 
                "data": "impressions",
                "width": "4%",
                "render": function(data, type, row) {
                    impressions = '<button type="button" data-bs-toggle="modal" data-bs-target="#impressionView">'+data+'</button>';
                    return impressions;
                }
            },
            { 
                "data": "db",
                "width": "4%",
                "render": function(data, type, row) {
                    if(data == 0 || data == null){
                        db = 0;
                    }else{
                        db = '<button id="sumDB" data-description="'+row.description+'">'+data+'</button>';
                    }
                    
                    return db;
                }
            },
            { "data": "db_price", "width": "5%"},
            { "data": "username", "width": "5%"},
            { "data": "designer", "width": "5%"},
            { "data": "developer", "width": "5%"},
            { 
                "data": "ei_datetime", 
                "width": "7%",
                "render": function(data){
                    return data.substr(0, 10);
                }
            }
        ],
        "language": {
            "url": '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        },
        "initComplete": function(settings, json) {
            // fadeIn 효과 적용 $('#event-table')
            let advTable = document.getElementById('event-table');
            fadeIn(advTable, 1000); // 1초 동안 페이드 인
        },
        "infoCallback": function(settings, start, end, max, total, pre){
            return "<i class='bi bi-check-square'></i>현재" + "<span class='now'>" +start +" - " + end + "</span>" + " / " + "<span class='total'>" + total + "</span>" + "건";
        },
        "drawCallback": function( settings ) {
            fileCheck();
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

function getImpressions(seq){
    $.fn.DataTable.ext.pager.numbers_length = 10;
    impressionsTable = $('#impression-view-table').DataTable({
        "destroy": true,
        "autoWidth": false,
        "processing" : true,
        "serverSide" : true,
        "responsive": true,
        "searching": false,
        "ordering": false,
        "deferRender": false,
        "paging": false,
        "info": false,
        "ajax": {
            "url": "<?=base_url()?>/eventmanage/event/impressions",
            "data": {"seq": seq},
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
            "dataSrc": function(res){
                return res;
            }
        },
        "columns": [
            { "data": "code", "width": "30%"},
            { "data": "site", "width": "30%"},
            { "data": "impressions", "width": "40%"},
        ],
        "language": {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        }
    });
}

function setDate(){
    $('#sdate, #edate').daterangepicker({
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

        $checkinInput = $('#sdate');
        $checkoutInput = $('#edate');

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

function fileCheck() {
    $.getJSON("<?php echo getenv('app.eventURL')?>getfiles", function(response) {
        $('.btn_landing').each(function(i, obj) {
            var filename = $(obj).data('filename') + '.php';
            if ($.inArray(filename, response) != -1 && $(obj).data('link').includes('<?php echo getenv('app.eventURL')?>')) { //임시로 행부기도메인은 랜딩보기 오픈
                $(obj).removeClass('hide');
            }
        });
    });
    $.getJSON("<?php echo getenv('app.newEventURL')?>getfiles", function(response) {
        $('.btn_landing').each(function(i, obj) {
            var filename = $(obj).data('filename') + '.php';
            if ($.inArray(filename, response) != -1 && $(obj).data('link').includes('<?php echo getenv('app.newEventURL')?>')) { //임시로 행부기도메인은 랜딩보기 오픈
                $(obj).removeClass('hide');
            }
        });
    });
}
		
function createEvent(data){
    $.ajax({
        url : "<?=base_url()?>/eventmanage/event/create", 
        type : "POST", 
        dataType: "JSON", 
        data : data, 
        contentType: 'application/json; charset=utf-8',
        success : function(data){
            if(data == true){
                dataTable.draw();
                alert("생성되었습니다.");
                $('#regiModal').modal('hide');
            }
        }
        ,error : function(error){
            var errorMessages = error.responseJSON.messages;
            var firstErrorMessage = Object.values(errorMessages)[0];
            alert(firstErrorMessage);
        }
    });
}

function updateEvent(data){
    $.ajax({
        url : "<?=base_url()?>/eventmanage/event/update", 
        type : "PUT", 
        dataType: "JSON", 
        data : data, 
        contentType: 'application/json; charset=utf-8',
        success : function(data){
            if(data == true){
                dataTable.draw();
                alert("수정되었습니다.");
                $('#regiModal').modal('hide');
            }
        }
        ,error : function(error){
            var errorMessages = error.responseJSON.messages;
            var firstErrorMessage = Object.values(errorMessages)[0];
            alert(firstErrorMessage);
        }
    });
}

function getAdv(inputId){
    $(inputId).autocomplete({
        source : function(request, response) {
            $.ajax({
                url : "<?=base_url()?>/eventmanage/event/adv", 
                type : "GET", 
                dataType: "JSON", 
                data : {'stx': request.term}, 
                contentType: 'application/json; charset=utf-8',
                success : function(data){
                    response(data);
                }
                ,error : function(){
                    alert("에러 발생");
                }
            });
        },
        minLength: 1,
        autoFocus : true,
        delay: 100,
        select: function(event, ui) {
            $('input[name="advertiser"]').val(ui.item.id);
        }
    });
}

function getMedia(inputId){
    $(inputId).autocomplete({
        source : function(request, response) {
            $.ajax({
                url : "<?=base_url()?>/eventmanage/event/media", 
                type : "GET", 
                dataType: "JSON", 
                data : {'stx': request.term}, 
                contentType: 'application/json; charset=utf-8',
                success : function(data){
                    response(data);
                }
                ,error : function(){
                    alert("에러 발생");
                }
            });
        },
        minLength: 1,
        autoFocus : true,
        delay: 100,
        select: function(event, ui) {
            $('input[name="media"]').val(ui.item.id);
        }
    });
}

function chkLead() {
    if ($('input[name="lead"][value="0"]').is(':checked')) {
        $('.leadHide').show();
        $('#bizform').hide();
    }else if($('input[name="lead"][value="4"]').is(':checked')){
        $('.leadHide').hide();
        $('#bizform').show();
    }else {
        $('.leadHide').hide();
        $('#bizform').hide();
    }
}

function chkInterlock(){
    if ($('input[name="interlock"][value="0"]').is(':checked')) {
        $('.interlock_code').hide();
        $('.interlock_code input').val('');
    } else {
        $('.interlock_code').show();
    }
}

function stripslashes(str) {
  return str.replace(/\\(.?)/g, function (match, char) {
    switch (char) {
      case '\\':
        return '\\';
      case '0':
        return '\u0000';
      case '':
        return '';
      default:
        return char;
    }
  });
}
var urlencode = function() {
    var txt = $('[name="encodeTxt"]').val();
    var encoded = encodeURIComponent("?"+txt);
    var cpText = $('.urlencode').prev('p').find('span.copy').attr('data-clipboard-text');
    $('.urlencode i').attr('data-clipboard-text',cpText+encoded).html(encoded);
}
$('[name="encodeTxt"]').bind('keyup', function() {
    urlencode();
});
$('input[name="another_url"]').on('click', function() {
    changeUrl();
});
var changeUrl = function(data) {
    if(typeof data == "undefined") {
        var data = {};
        data.hash_no = $('.landing_info_link').text().split('/').pop();
        data.another_url = $('input[name="another_url"]:checked').val();
    }
    var event_url = `<?php echo getenv('app.eventURL');?>${data.hash_no}`;
    var sync_url = `https://pf-link.kakao.com/qr/_ecxniG/pages/_el?query=${data.hash_no}`;
    if(data.another_url == 1) {
        var event_url = `<?php echo getenv('app.newEventURL');?>${data.hash_no}`;
        sync_url = `https://pf-link.kakao.com/qr/_ecxniG/pages/_dV?query=${data.hash_no}`;
    }
    $('.landing_info_link').attr('href', event_url).text(event_url);
    $('.landing_info_link').next('button').attr('data-clipboard-text', event_url);
    $('.sch_word').attr('data-clipboard-text', sync_url).html(sync_url);
    urlencode();
    new ClipboardJS('.copy', {
        container: document.getElementById('regiModal')
    });
}
function setEvent(data){
    $('form[name="event-register-form"]')[0].reset();
    $('form[name="event-register-form"] textarea').text('');
    $('.custom-row-wrap .update_custom_box').empty();
    $('input[name="seq"]').val(data.seq);
    $('input[name="advertiser"]').val(data.advertiser);
    $('input[name="media"]').val(data.media);
    $('.landing_info_num').text(data.seq);
    changeUrl(data);
    $('input[name="no_hash"][value="'+data.no_hash+'"]').prop('checked', true);
    $('input[name="another_url"][value="'+data.another_url+'"]').prop('checked', true);
    $('input[name="adv_name"]').val(data.advertiser_name).attr('disabled', true);
    $('input[name="media_name"]').val(data.media_name);
    $('input[name="description"]').val(data.description);
    $('input[name="db_price"]').val(data.db_price);
    $('input:radio[name="is_stop"][value="'+data.is_stop+'"]').prop('checked', true);
    $('input:radio[name="lead"][value="'+data.lead+'"]').prop('checked', true);
    $('input:radio[name="interlock"][value="'+data.interlock+'"]').prop('checked', true);
    $('input[name="creative_id"]').val(data.creative_id);
    $('input[name="bizform_apikey"]').val(data.bizform_apikey);
    $('input[name="partner_id"]').val(data.partner_id);
    $('input[name="partner_name"]').val(data.partner_name);
    $('input[name="paper_code"]').val(data.paper_code);
    $('input[name="paper_name"]').val(data.paper_name);

    if(data.custom && data.custom != '[]'){
        var json = JSON.parse(data.custom.replace(/\\/g, ''));
        for (var j = 0; j < json.length; j++) {

            custom_row = '<div class="d-flex mb-2 custom_row"><select name="custom_key" class="custom form-select me-2" aria-label="선택"><option selected disabled>개별설정 안함</option><option value="branch" ' + ((json[j].key === 'branch') ? 'selected' : '') + '>지점</option><option value="sms_number" ' + ((json[j].key === 'sms_number') ? 'selected' : '') + '>문자 발송번호</option></select><input type="text" class="form-control" id="custom_val" value="' + json[j].val + '"></div>';

            $('.custom-row-wrap .update_custom_box').prepend(custom_row);
        }
    }
    $('input[name="title"]').val(data.title);
    console.log(data.keywords);
    $.each(data.keywords, function(index, tag) {
        $('input[name="keyword"]').tagit('createTag', tag); // 태그 추가
    });
    $('input[name="subtitle"]').val(data.subtitle);
    $('input[name="object"]').val(data.object);
    $('input[name="object_items"]').val(data.object_items);
    $('input[name="pixel_id"]').val(data.pixel_id);
    $('textarea[name="view_script"]').text(stripslashes(data.view_script));
    $('textarea[name="done_script"]').text(stripslashes(data.done_script));
    $('input:radio[name="check_gender"][value="'+data.check_gender+'"]').prop('checked', true);
    $('input[name="check_age_min"]').val(data.check_age_min);
    $('input[name="check_age_max"]').val(data.check_age_max);

    if(data.duplicate_term){
        $('input:radio[name="duplicate_term"][value="'+data.duplicate_term+'"]').prop('checked', true);
    }else{
        $('input:radio[name="duplicate_term"][value="180"]').prop('checked', true);
    }
    $('input:radio[name="check_phone"][value="'+data.check_phone+'"]').prop('checked', true);
    $('input:radio[name="check_name"][value="'+data.check_name+'"]').prop('checked', true);
    $('select[name="duplicate_precheck"]').val(data.duplicate_precheck);
}

$('input[name="adv_name"]').on("focus", function(){
    $('input[name="advertiser"]').val("");
	$('input[name="adv_name"]').val("");
    getAdv('input[name="adv_name"]');
})

$('input[name="media_name"]').on("focus", function(){
    $('input[name="media"]').val("");
	$('input[name="media_name"]').val("");
    getMedia('input[name="media_name"]');
})

$('input[name="lead"]').bind('change', function() {
    chkLead();
});

$('input[name="interlock"]').bind('change', function() {
    chkInterlock();
});

$('#add_custom').click(function() {
    const custom_row = '<div class="d-flex mb-2 custom_row"><select name="custom_key" class="custom form-select me-2" aria-label="선택"><option selected disabled>개별설정 안함</option><option value="branch">지점</option><option value="sms_number">문자 발송번호</option></select><input type="text" class="form-control" id="custom_val" autocomplete="off"></div>';
    $('.custom-row-wrap').last().append(custom_row);
});

var keywords = [];
    $('input[name="keyword"]').tagit({
        availableTags: keywords
    }).data("ui-tagit").tagInput.addClass("tagit-input");
var setViewModal = function(seq) {
    $.ajax({
        type: "GET",
        url: "<?=base_url()?>/eventmanage/event/view",
        data: {'seq':seq},
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            setEvent(data);
            chkLead();
            chkInterlock();
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}
$('#regiModal').on('show.bs.modal', function(e) {
    var $btn = $(e.relatedTarget);
    if ($btn.attr('id') === 'updateBtn') {
        var $tr = $btn.closest('tr');
        var seq = $tr.attr('id');
        $('#regiModalLabel').text('이벤트 수정');
        $('.landing_info').show();
        $('.update-btn-wrap').show();
        $('.create-btn-wrap').hide();
        $('input[name="keyword"]').tagit("removeAll");
        setViewModal(seq);
    }else{
        $('#regiModalLabel').text('이벤트 등록');
        $('.landing_info').hide();
        $('.update-btn-wrap').hide();
        $('.create-btn-wrap').show();
        $('input[name="keyword"]').tagit("removeAll");
        $('input[name="adv_name"]').removeAttr('disabled');
        chkLead();
        chkInterlock();
    }
    new ClipboardJS('.copy', {
        container: document.getElementById('regiModal')
    });
})
.on('hidden.bs.modal', function(e) { 
    $('input[name="seq"]').val('');
    $('input[name="advertiser"]').val('');
    $('input[name="media"]').val('');
    $('form[name="event-register-form"]')[0].reset();
    $('form[name="event-register-form"] textarea').text('');
    $('.custom-row-wrap .update_custom_box').empty();
});

$('form[name="search-form"]').bind('submit', function() {
    dataTable.draw();
    return false;
});

$('form[name="event-register-form"]').bind('submit', function(e) {
    e.preventDefault();
    var cus_array = new Array();
    var jarray = new Object();
    for (var i = 0; i < $('.custom').length; i++) {
        if ($('.custom_row').eq(i).children('.custom').val() && $('.custom_row').eq(i).children('#custom_val').val()) {
            var key = $('.custom_row').eq(i).children('.custom').val();
            var val = $('.custom_row').eq(i).children('#custom_val').val();
            jarray = {
                key,
                val
            };
            cus_array.push(jarray);
        }
    }
    $('input[name=custom]').val(JSON.stringify(cus_array));
    var data = $(this).serialize();
    // var clickedButton = $(document.activeElement).attr('id');
    var clickedButton = e.originalEvent.submitter.id; // 변경된 부분
    
    if(clickedButton == 'createActionBtn'){
        createEvent(data);
    }
    // 이벤트 수정 버튼 클릭시 실행
    if(clickedButton == 'updateActionBtn'){
        updateEvent(data);
    }
    
    return false;
});
//유입수 모달
$('#impressionView').on('show.bs.modal', function(e) {
    var $btn = $(e.relatedTarget);
    var seq = $btn.closest('tr').attr('id');
    $('#impressionViewLabel').text(seq+" 유입수");
    getImpressions(seq);
})
.on('hidden.bs.modal', function(e) { //modal Reset
    $('#impression-view-table').DataTable().destroy();
    $('#impression-view-table tbody').empty();
});

$('#copyActionBtn').on('click', function(e) {
    seq = $('#regiModal input[name="seq"]').val();
    if(confirm("복사하시겠습니까?") && seq){
        $.ajax({
            type: "POST",
            url: "<?=base_url()?>/eventmanage/event/copy",
            data: {'seq':seq},
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            success: function(data){  
                if(data != false){
                    dataTable.draw();
                    if(confirm('복사 되었습니다.\n\n복사 된 이벤트로 이동하시겠습니까?')) {
                        setViewModal(data);
                    } else {
                        $('#regiModal').modal('hide');
                    }
                }
            },
            error: function(error, status, msg){
                console.log()
                alert("상태코드: " + error.responseJSON.status + " 에러메시지: " + error.responseJSON.messages.error );
            }
        });
    }
});

$('#deleteActionBtn').on('click', function(e) {
    seq = $('#regiModal input[name="seq"]').val();
    if(confirm("삭제하시겠습니까?") && seq){
        $.ajax({
            type: "DELETE",
            url: "<?=base_url()?>/eventmanage/event/delete",
            data: {'seq':seq},
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            success: function(data){  
                if(data == true){
                    dataTable.draw();
                    alert("삭제되었습니다.");
                    $('#regiModal').modal('hide');
                }
            },
            error: function(error, status, msg){
                console.log()
                alert("상태코드: " + error.responseJSON.status + " 에러메시지: " + error.responseJSON.messages.error );
            }
        });
    }
});

$('#landingView').on('show.bs.modal', function(e) {
    var $btn = $(e.relatedTarget);
    var link = $btn.data('link');
    var iframeContent = $('#eventContent');
    iframeContent.attr('src', link);
})
.on('hidden.bs.modal', function(e) { 
    $('#eventContent').attr('src', '');
});

$('body').on('click', '#sumDB', function() {
    var currentDate = new Date();
    var firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 2);
    var lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1);
    var seq = $(this).closest('tr').attr('id');

    var formattedFirstDay = firstDay.toISOString().slice(0, 10);
    var formattedLastDay = lastDay.toISOString().slice(0, 10);

    var description = $(this).data('description');
    if(window.localStorage.getItem('DataTables_deviceTable_/integrate')){
        var integrateStorage = window.localStorage.getItem('DataTables_deviceTable_/integrate');
        var data = JSON.parse(integrateStorage);
        // data.searchData.event = description;
        data.searchData.event = '';
        data.searchData.sdate = formattedFirstDay;
        data.searchData.edate = formattedLastDay;
        data.searchData.advertiser = '';
        data.searchData.media = '';
        data.searchData.status = '';
        data.searchData.stx = seq;
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
                advertiser: '',
                media: '',
                event: '',
                status: '',
                stx: seq
            }
        };
    }
    
    updatedStorageValue = JSON.stringify(data);
    window.localStorage.setItem('DataTables_deviceTable_/integrate', updatedStorageValue);
    window.location.href = '/integrate';
});
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>
