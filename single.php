<?php
/**
 * 文章详情页模板
 */
get_header();

// 更新浏览计数
minimal_personal_update_views(get_the_ID());
?>

<div class="single-post">
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <header class="article-header">
            <h1 class="article-title"><?php the_title(); ?></h1>
            
            <div class="article-meta">
                <div class="meta-item">
                    <span class="meta-icon">📅</span>
                    <time datetime="<?php echo get_the_date('c'); ?>">
                        <?php echo get_the_date(); ?>
                    </time>
                </div>
                
                <div class="meta-item">
                    <span class="meta-icon">👁️</span>
                    <span class="view-count">
                        <?php 
                        $views = get_post_meta(get_the_ID(), '_minimal_personal_views', true) ?: 0;
                        echo $views; 
                        ?> 浏览
                    </span>
                </div>
                
                <div class="meta-item">
                    <span class="meta-icon">💬</span>
                    <span class="comment-count">
                        <?php 
                        $comments_count = get_comments_number();
                        echo $comments_count; 
                        ?> 评论
                    </span>
                </div>
                
                <div class="meta-item">
                    <span class="meta-icon">❤️</span>
                    <span class="like-count">
                        <?php 
                        $likes = get_post_meta(get_the_ID(), '_minimal_personal_likes', true) ?: 0;
                        echo $likes; 
                        ?> 点赞
                    </span>
                </div>
            </div>
        </header>

        <?php if (has_post_thumbnail()) : ?>
            <div class="article-featured-image">
                <?php the_post_thumbnail('large', array('class' => 'featured-image')); ?>
            </div>
        <?php endif; ?>

        <div class="article-content">
            <?php the_content(); ?>
            
            <?php
            wp_link_pages(array(
                'before' => '<div class="page-links">页码: ',
                'after'  => '</div>',
            ));
            ?>
        </div>

        <footer class="article-footer">
            <div class="article-actions">
                <button class="action-button like-button" data-post-id="<?php the_ID(); ?>">
                    <span class="action-icon">❤️</span>
                    <span class="action-text">点赞</span>
                </button>
                
                <button class="action-button share-button" onclick="shareArticle()">
                    <span class="action-icon">🔗</span>
                    <span class="action-text">分享</span>
                </button>
                
                <button class="action-button comment-button" onclick="scrollToComments()">
                    <span class="action-icon">💬</span>
                    <span class="action-text">评论</span>
                </button>
            </div>

            <?php if (has_tag()) : ?>
                <div class="article-tags">
                    <h4>标签:</h4>
                    <ul class="tag-list">
                        <?php
                        $tags = get_the_tags();
                        if ($tags) {
                            foreach ($tags as $tag) {
                                echo '<li class="tag-item"><a href="' . get_tag_link($tag->term_id) . '">' . $tag->name . '</a></li>';
                            }
                        }
                        ?>
                    </ul>
                </div>
            <?php endif; ?>
        </footer>
    </article>

    <!-- 评论区域 -->
    <div class="comments-area" id="comments">
        <?php
        // 如果评论是开放的或者有评论
        if (comments_open() || get_comments_number()) :
            comments_template();
        endif;
        ?>
    </div>
</div>

<script>
// 分享文章
function shareArticle() {
    if (navigator.share) {
        navigator.share({
            title: '<?php echo esc_js(get_the_title()); ?>',
            text: '<?php echo esc_js(wp_trim_words(get_the_excerpt(), 20)); ?>',
            url: '<?php echo esc_url(get_permalink()); ?>'
        })
        .then(() => console.log('分享成功'))
        .catch((error) => console.log('分享失败', error));
    } else {
        // 备用分享方式：复制链接到剪贴板
        const url = '<?php echo esc_url(get_permalink()); ?>';
        navigator.clipboard.writeText(url).then(() => {
            alert('链接已复制到剪贴板');
        });
    }
}

// 滚动到评论区域
function scrollToComments() {
    document.getElementById('comments').scrollIntoView({
        behavior: 'smooth'
    });
}

// 点赞功能（与列表页保持一致）
jQuery(document).ready(function($) {
    $('.like-button').on('click', function(e) {
        e.preventDefault();
        
        const $button = $(this);
        const postId = $button.data('post-id');
        
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
                    $('.like-count').text(response.data.likes + ' 点赞');
                    
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
</script>

<?php get_footer(); ?>