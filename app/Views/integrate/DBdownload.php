<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    ZENITH - 통합 DB 관리
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<!-- <style>
    .search-wrap .search .input {flex:0;}
</style> -->
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<?=$this->section('content');?>
<div class="sub-contents-wrap db-manage-contaniner">
    <div class="title-area">
        <h2 class="page-title">통합 DB 다운로드</h2>
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
    <?php if(auth()->user()->hasPermission('integrate.advertiser') || auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){?>
    <div class="section client-list mt20">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 광고주</h3>
        <div class="row" id="advertiser-list"></div>
    </div>
    <?php }?>
    <?php if(auth()->user()->hasPermission('integrate.media') || auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){?>
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
    </div>
    <!-- 메시지 입력 및 다운로드 버튼 추가 -->
    <form id="download_form" class="section d-flex justify-content-center align-items-center">
        <input type="text" id="message" name="message" placeholder="사유를 입력해주세요" class="form-control me-3">
        <div class="d-flex align-items-center">
            <select id="file_format" name="file_format" class="form-select me-3">
                <option value="xlsx">Excel</option>
                <option value="csv">CSV</option>
                <option value="pdf">PDF</option>
            </select>
            <button type="submit" id="download_btn" class="btn btn-primary btn-lg d-flex align-items-center" style="white-space: nowrap;">
                <i class="bi bi-file-earmark-spreadsheet me-2"></i> 다운로드
            </button>
        </div>
    </form>
</div>
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script>
$('#sdate, #dates').val(moment().format('YYYY-MM-DD')); // 오늘 날짜를 입력 필드에 설정
$('#edate').val(moment().format('YYYY-MM-DD')); // 오늘 날짜를 입력 필드에 설정
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

$(document).ready(function() {
    function getSelectedButtonValues(selector) {
        return $(selector).map(function() {
            return $(this).val();
        }).get().join('|');
    }

    function renderButtons(filters, validKeys, selectedValues) {
        const hasActive = Object.values(selectedValues).some(values => values.length > 0);

        validKeys.forEach(type => {
            if (filters[type]) {
                let $row = $(`#${type}-list`);
                let html = filters[type].map(v => {
                    const isActive = selectedValues[type] && selectedValues[type].includes(v.label);
                    const isOn = !isActive && hasActive && v.count;
                    var count = v.count == v.total ? v.count : `${v.count}/${v.total}`;
                    count = count ? count : "";
                    $(`#${type}-list`).find('.zenith-loading').fadeOut(function(){$(this).remove();});
                    return `
                        <div class="col">
                            <div class="inner">
                                <button type="button" value="${v.label}" data-btn="${type}" class="${type}_btn filter_btn ${isActive ? 'active' : ''} ${isOn ? 'on' : ''}">
                                    <span class="account_name">${v.label}</span>
                                    <div class="progress">
                                        <div class="txt">${count}</div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    `;
                }).join('');
                $row.html('').append(html);
            }
        });
    }

    function renderStatus(statusCountMap) {
        let statusHtml = Object.keys(statusCountMap).map(status => `
            <dl class="col" data-status="${status}">
                <dt>${status}</dt>
                <dd>${statusCountMap[status] || 0}</dd>
            </dl>
        `).join('');
        
        $('.statusCount.detail').html(statusHtml);
    }

    function fetchData() {
        const selectedValues = {
            company: getSelectedButtonValues('.company_btn.active').split('|').filter(Boolean),
            advertiser: getSelectedButtonValues('.advertiser_btn.active').split('|').filter(Boolean),
            media: getSelectedButtonValues('.media_btn.active').split('|').filter(Boolean),
            description: getSelectedButtonValues('.description_btn.active').split('|').filter(Boolean)
        };

        const hasActive = Object.values(selectedValues).some(values => values.length > 0);
        $('.client-list .row').filter(function(i, el) {
            if(!$(el).find('.zenith-loading').is(':visible')) $(el).append('<div class="zenith-loading"/>')
        });
        $.ajax({
            url: '<?=base_url()?>/integrate/buttons',
            type: 'POST',
            data: {
                sdate: $('#sdate').val(),
                edate: $('#edate').val(),
                stx: $('#stx').val(), // 검색어
                company: selectedValues.company.join('|'), // 회사
                advertiser: selectedValues.advertiser.join('|'), // 광고주
                media: selectedValues.media.join('|'), // 미디어
                description: selectedValues.description.join('|') // 설명
            },
            success: function(response) {
                if (response) {
                    const filters = response;
                    const validKeys = ['advertiser', 'description', 'media', 'company'];
                    
                    renderButtons(filters, validKeys, selectedValues);

                    if (filters.status) {
                        renderStatus(filters.status);
                    }
                } else {
                    console.error('Invalid response data:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching buttons:', error);
            }
        });
    }

    // .filter_btn 클릭 시 active 클래스 추가 및 데이터 다시 불러오기
    $(document).on('click', '.filter_btn', function() {
        $(this).toggleClass('active');
        fetchData();
    });

    // 조회 버튼 클릭 시 데이터 다시 불러오기
    $('#search_btn').on('click', function(event) {
        event.preventDefault();
        fetchData();
    });

    // 필터 초기화 버튼 클릭 이벤트
    $('.reset-btn').on('click', function() {
        window.location.reload(); // 현재 페이지를 다시 로드
    });

    // 초기 로드 시 선택된 버튼이 없으므로 hasActive를 false로 설정
    fetchData();

    // 다운로드 버튼 클릭 시 데이터 전송
    $('#download_form').on('submit', function(event) {
        event.preventDefault();
        const message = $.trim($('#message').val());
        if (message.length < 5) {
            alert('사유를 5글자 이상 입력해주세요.');
            return;
        }
        const data = {
            sdate: $('#sdate').val(),
            edate: $('#edate').val(),
            advertiser: getSelectedButtonValues('.advertiser_btn.active'),
            media: getSelectedButtonValues('.media_btn.active'),
            description: getSelectedButtonValues('.description_btn.active'),
            message: message,
            file_format: $('#file_format').val() // 파일 형식 추가
        };

        // 버튼 비활성화
        $('#download_btn').prop('disabled', true).text('다운로드 중...');

        $.ajax({
            url: '<?=base_url()?>/integrate/download-send',
            type: 'POST',
            data: data,
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
                    const fileFormat = $('#file_format').val();
                    const fileExtension = fileFormat === 'xlsx' ? 'xlsx' : fileFormat === 'csv' ? 'csv' : 'pdf';
                    a.download = `통합DB_다운로드_<?php echo date('Y-m-d_H-i-s'); ?>.${fileExtension}`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);

                    $('#download_form')[0].reset();
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr, status, error);
            },
            complete: function() {
                // 요청 완료 후 버튼을 다시 활성화
                $('#download_btn').prop('disabled', false).html('<i class="bi bi-file-earmark-spreadsheet me-2"></i> 다운로드');
            }
        });
    });
});
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>
