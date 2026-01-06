$(document).ready(function() {
    function isMobile() {
        return window.innerWidth <= 991.98;
    }
    
    // Initialize aside state based on screen size
    function initAsideState() {
        if (isMobile()) {
            // Mobile: Start with aside hidden
            $('.aside').removeClass('active').addClass('in-active');
            $('.main-content').removeClass('active');
            $('#body-overlay').removeClass('active');
        } else {
            // Desktop: Start with aside visible (default state)
            $('.aside').addClass('active').removeClass('in-active');
            $('.main-content').removeClass('active');
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
    
    // Handle aside toggle button
    $(document).on('click', '.asideToggle', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (isMobile()) {
            // Mobile: Toggle aside visibility
            var aside = $('.aside');
            // Check if aside is currently visible (has active class and doesn't have in-active)
            var isVisible = aside.hasClass('active') && !aside.hasClass('in-active');
            
            if (isVisible) {
                // Hide aside
                aside.removeClass('active').addClass('in-active');
                $('.main-content').removeClass('active');
                $('#body-overlay').removeClass('active');
            } else {
                // Show aside
                aside.removeClass('in-active').addClass('active');
                $('.main-content').removeClass('active');
                $('#body-overlay').addClass('active');
            }
        } else {
            // Desktop: Toggle aside visibility (original behavior)
            // When aside is active (visible), toggle to in-active (hidden) and make main-content active (full width)
            // When aside is in-active (hidden), toggle to active (visible) and make main-content inactive (normal width)
            $('.aside').toggleClass('active');
            $('.aside').toggleClass('in-active');
            $('.main-content').toggleClass('active');
        }
    });
    
    // Handle close button click
    $(document).on('click', '.aside-close-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Simply change active to in-active
        $('.aside').removeClass('active').addClass('in-active');
        $('#body-overlay').removeClass('active');
    });
    
    // Handle overlay click
    $(document).on('click', '#body-overlay', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        $('.aside').removeClass('active').addClass('in-active');
        $('.main-content').removeClass('active');
        $(this).removeClass('active');
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
