jQuery(document).ready(function($) {
    $('#loadMore').on('click', function() {
        const button = $(this);
        const page = parseInt(button.data('page')) + 1;
        const container = $('.article-list');
        
        // 显示加载状态
        button.text('加载中...').prop('disabled', true);
        
        $.ajax({
            url: minimal_personal_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'minimal_personal_load_more',
                page: page,
                nonce: minimal_personal_ajax.nonce
            },
            success: function(response) {
                if (response.success && response.data.html) {
                    // 添加新文章
                    container.append(response.data.html);
                    button.data('page', page).text('加载更多').prop('disabled', false);
                    
                    // 如果没有更多文章，隐藏按钮
                    if (!response.data.has_more) {
                        button.text('没有更多文章了').prop('disabled', true);
                    }
                } else {
                    button.text('加载失败，请重试').prop('disabled', false);
                }
            },
            error: function() {
                button.text('加载失败，请重试').prop('disabled', false);
            }
        });
    });
});