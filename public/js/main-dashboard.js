document.addEventListener("DOMContentLoaded", () => {
    function isMobile() {
        return window.innerWidth <= 991.98;
    }
    
    // Initialize aside state based on screen size
    function initAsideState() {
        if (isMobile()) {
            // Mobile: Start with aside hidden
            $('.aside').removeClass('active').addClass('in-active');
            $('.main-content').removeClass('active').addClass('in-active');
            $('#body-overlay').removeClass('active');
        } else {
            // Desktop: Start with aside visible
            $('.aside').addClass('active').removeClass('in-active');
            $('.main-content').addClass('active').removeClass('in-active');
        }
    }
    
    // Initialize on load
    initAsideState();
    
    // Reinitialize on resize
    var resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            initAsideState();
        }, 250);
    });
    
    $('.asideToggle').on('click', function() {
        if (isMobile()) {
            // Mobile: Toggle aside visibility
            if ($('.aside').hasClass('active') && !$('.aside').hasClass('in-active')) {
                $('.aside').addClass('in-active').removeClass('active');
                $('.main-content').removeClass('active').addClass('in-active');
                $('#body-overlay').removeClass('active');
            } else {
                $('.aside').removeClass('in-active').addClass('active');
                $('.main-content').removeClass('in-active').addClass('active');
                $('#body-overlay').addClass('active');
            }
        } else {
            // Desktop: Toggle aside visibility (original behavior)
            $('.aside').toggleClass('active');
            $('.aside').toggleClass('in-active');
            $('.main-content').toggleClass('active');
            $('.main-content').toggleClass('in-active');
        }
    });
$('.settings-tab-opener').on('click',function(){
    $('.settings-tab-opener').removeClass('active');
    $(this).addClass('active');
    var open_id = $(this).attr('data-opentab');
    $('.taber').removeClass('active');
    $('.taber#'+open_id).addClass('active');
});
var getUrl = window.location;
$(".aside a[href='" + getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1] + "/" +getUrl.pathname.split('/')[2] + "']").closest('.item').find('.item-container').addClass('active');
$(".aside a[href='" + getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1] + "/" +getUrl.pathname.split('/')[2] + "']").closest('.item').find('.sub-item').addClass('active');
$(".aside a[href='" + getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1] + "/" +getUrl.pathname.split('/')[2] + "']").closest('.item').find('.sub-item').find("a[href='" + getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1] + "/" +getUrl.pathname.split('/')[2] + "']").addClass('active');
$(".aside a[href='" + getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1] + "/" +getUrl.pathname.split('/')[2] + "'] >div").addClass('active');
$(".aside a[href='" + window.location.href + "'] >div").addClass('active');
$(".aside a[href='" + window.location.href + "']").addClass('active');

$('input[required],select[required],textarea[required]').parent().parent().find('>div:nth-of-type(1)').append('<span style="color:red;font-size:16px">*</span>');
$("[name='title']:not(.not-countable),[name='slug']:not(.not-countable),[name='meta_description']:not(.not-countable)").on('keyup',function(){
    var length = $(this).val().length;
    var maxLength = $(this).attr('maxlength');
    if(maxLength){
        $(this).parent().find('.char-count').remove();
        $(this).parent().append('<span class="char-count" style="font-size:12px;color:#999">'+length+'/'+maxLength+'</span>');
    }
});
});
