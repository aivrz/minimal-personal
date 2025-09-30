jQuery(document).ready(function($) {
    $('.like-button').on('click', function(e) {
        e.preventDefault();
        
        const $button = $(this);
        const postId = $button.data('post-id');
        
        // 防止重复点击
        if ($button.hasClass('loading') || $button.hasClass('liked')) {
            return;
        }
        
        $button.addClass('loading').prop('disabled', true);
        
        $.ajax({
            url: minimal_personal_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'minimal_personal_like',
                post_id: postId,
                nonce: minimal_personal_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $button.removeClass('loading').addClass('liked');
                    $button.find('.like-count').text(response.data.likes);
                    
                    // 3秒后恢复可再次点击
                    setTimeout(() => {
                        $button.removeClass('liked').prop('disabled', false);
                    }, 3000);
                }
            },
            error: function() {
                $button.removeClass('loading').prop('disabled', false);
                alert('点赞失败，请重试');
            }
        });
    });
});