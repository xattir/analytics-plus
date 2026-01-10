// Hide loading image after 2 seconds with animation
(function() {
    'use strict';
    
    function hideLoadingImage() {
        var loadingContainer = document.getElementById('loading-image-container');
        if (loadingContainer) {
            // Wait 2 seconds, then fade out
            setTimeout(function() {
                if (loadingContainer) {
                    loadingContainer.style.transition = 'opacity 0.5s ease-out';
                    loadingContainer.style.opacity = '0';
                    setTimeout(function() {
                        if (loadingContainer) {
                            loadingContainer.style.display = 'none';
                        }
                    }, 500); // Wait for transition to complete
                }
            }, 2000); // 2 seconds delay
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', hideLoadingImage);
    } else {
        hideLoadingImage();
    }
})();

// Wait for jQuery to be available
(function() {
    'use strict';
    
    function initDashboard() {
        var $ = window.jQuery || window.$;
        if (!$ || typeof $ !== 'function') {
            setTimeout(initDashboard, 50);
            return;
        }
        
        $(function() {
            $('.asideToggle').on('click', function() {
                $('.aside').toggleClass('active');
                $('.aside').toggleClass('in-active');
                $('.main-content').toggleClass('active');
                $('.main-content').toggleClass('in-active');
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

            // Removed automatic red asterisk for required fields
            // $('input[required],select[required],textarea[required]').parent().parent().find('>div:nth-of-type(1)').append('<span style="color:red;font-size:16px">*</span>');
            $("[name='title']:not(.not-countable),[name='slug']:not(.not-countable),[name='meta_description']:not(.not-countable)").on('keyup',function(){
                var length = $(this).val().length;
                var maxLength = $(this).attr('maxlength');
                if(maxLength){
                    $(this).parent().find('.char-count').remove();
                    $(this).parent().append('<span class="char-count" style="font-size:12px;color:#999">'+length+'/'+maxLength+'</span>');
                }
            });
            
            // Hide loading image immediately when page loads
            var loadingContainer = document.getElementById('loading-image-container');
            if (loadingContainer) {
                loadingContainer.style.display = 'none';
            }
        });
    }
    
    // Start initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashboard);
    } else {
        initDashboard();
    }
})();
