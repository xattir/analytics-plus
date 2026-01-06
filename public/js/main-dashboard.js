$(document).ready(function() {
    $('.asideToggle').on('click', function() {
        $('.aside').toggleClass('active');
        $('.aside').toggleClass('in-active');
        $('.main-content').toggleClass('active');
        $('.main-content').toggleClass('in-active');
    });

    // Handle mobile aside toggle button using event delegation
    $(document).on('click', '.aside-toggle-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        $('.aside').toggleClass('active');
        $('.aside').toggleClass('in-active');
    });
    
    // Also handle touch events for mobile
    $(document).on('touchend', '.aside-toggle-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        $('.aside').toggleClass('active');
        $('.aside').toggleClass('in-active');
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
