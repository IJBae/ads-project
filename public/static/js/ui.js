$(function(){
    // var btn_Array = [];
    // var result = null;
    // var last = btn_Array.at(-1);
    
    // 2024.05.09 메뉴클릭시 
    let $leftMenu = $('.left-side .btn-menu');
    let $utilNav = $('.left-side, .util-nav');
    let $sideBtn = $('.left-side .nav > li > button');
    let $sideCollapse = $('.left-side .nav .collapse');
    $leftMenu.on('click', function() {
        toggleLeftSide();
    });
    function toggleLeftSide() {
        $sideBtn.attr('aria-expanded', false);
        $sideCollapse.removeClass('show');
        $utilNav.toggleClass('active');
    }
    //left-side 메뉴 클릭시 
    $('.nav>li>button').on('click', function(){
        let allExpand = $('.nav>li>button[aria-expanded="true"]').length;
        let expand = $(this).attr('aria-expanded');
        if(expand == 'true' && allExpand == 1){
            $utilNav.addClass('active');
        }
        // if(expand == 'false' && !allExpand){
        //     $utilNav.removeClass('active');
        // }
    });
    //left-side js
    //left-side 다른영역 클릭 시 클래스 해제
    function leftMenuResize() {
        // var width = $(this).width();
        // if(width <= 1024){
        // }
        $sideBtn.attr('aria-expanded', false);
        $sideCollapse.removeClass('show');
        $utilNav.removeClass('open hide active');
    }
    $('.main-contents-wrap, .sub-contents-wrap').on('click', function(e) {
        if(!$utilNav.is(e.target) && $utilNav.has(e.target).length === 0) {
            if($utilNav.hasClass('active')) {
                leftMenuResize();
            }
        }
    });
    //left-side 리사이즈 시
    $(window).one('resize', function() {
        leftMenuResize();
    });


    // //웹
    // $('.nav-wrap').on(function(){  
    //     if($('.left-side').hasClass('active') && $('.left-side').hasClass('hide')){
    //         $utilNav.removeClass('hide');  
    //     }  
    //     else if($('.left-side').hasClass('active') && $('.left-side').hasClass('open')){
    //         $utilNav.removeClass('open');  
    //     }    
    // });
    // //모바일웹 
    // $('.nav-wrap').on('mouseleave','touchend',function(){    
    //     if($('.left-side').hasClass('active') && $(window).width() > 1024){
    //         $utilNav.addClass('hide');  
    //     }
    //     else if($('.left-side').hasClass('active') && $(window).width() <= 1024) {
    //         $utilNav.addClass('open');  
    //     }  
    // });

    // 2024.05.09 메뉴클릭시 끝
    $('.btn-top .head').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop:0},500)
    })
    $('.btn-top .list').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop:$('.dataTable').offset().top},500)
    })

    $(document).ready(function() {
        setTimeout(function() {
            if(!$('.dataTable').is(':visible')) {
                $('.btn-top .list').fadeOut();
            }
            //top 버튼 위치 수정 2024.04
            let $btnDebug = $("#debug-icon");
            if ($btnDebug.length > 0) { 
                let $btnDebugH = $btnDebug.outerHeight() + 3 ; 
                let $btnTop = $(".btn-top"); 
                $btnTop.css("bottom", $btnDebugH + "px");
            }
            
        },1000);

        var clipboard = new ClipboardJS('.copy', {
            text: function(trigger) {
                return $(trigger).data('clipboard-text');
            }
        });
        clipboard.on('success', function(e) {
            e.clearSelection();
            showTooltip(e.trigger, '복사완료!');
        });
        clipboard.on('error', function(e) {
            showTooltip(e.trigger, fallbackMessage(e.action));
        });
        $(".modal").on("shown.bs.modal", function() {
            var modalClipboard = new ClipboardJS('.copy', {
                container: document.querySelector('.modal'),
                text: function(trigger) {
                    return $(trigger).data('clipboard-text');
                }
            });
            modalClipboard.on('success', function(e) {
                e.clearSelection();
                showTooltip(e.trigger, '복사완료!');
            });
            modalClipboard.on('error', function(e) {
                showTooltip(e.trigger, fallbackMessage(e.action));
            });
        });
    });

    $('.toggle').on('click', function(){
        $(this).toggleClass('folded');
    });

    $('.dataTables_info > i').on('click',function(){
        $('.txt-info').toggle();
    });

    $(document).ajaxComplete(function(event, jqxhr, settings) {
        // 401 응답 처리
        if (jqxhr.status === 401) {
            window.location.href = '/login';
        }
    });
});