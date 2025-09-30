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

        
        <div class="article-content">
    <?php 
    // 获取文章中的所有图片
    $post_images = minimal_personal_get_post_images();
    $total_images = count($post_images);
    
    // 只在有图片时显示图片网格
    if ($total_images > 0) : 
        // 根据图片数量确定网格布局类
        if ($total_images == 1) {
            $grid_class = 'single-image';
        } elseif ($total_images <= 4) {
            $grid_class = 'four-grid';
        } elseif ($total_images <= 6) {
            $grid_class = 'six-grid';
        } else {
            $grid_class = 'nine-grid';
        }
    ?>
    
    <div class="post-image-grid <?php echo $grid_class; ?>">
        <?php
        // 确定要显示的图片数量（最多9张，超过时第9张显示查看更多）
        $display_count = min($total_images, 9);
        
        for ($i = 0; $i < $display_count; $i++) :
            $image_src = $post_images[$i];
            
            // 第九张且有更多图片时显示查看更多
            if ($i == 8 && $total_images > 9) :
        ?>
            <div class="grid-item more-images">
                <img src="<?php echo esc_url($image_src); ?>" alt="图片 <?php echo $i + 1; ?>" 
                     class="grid-image" data-image-src="<?php echo esc_url($image_src); ?>"
                     data-index="<?php echo $i; ?>">
                <div class="more-overlay">
                    <span class="more-text">+<?php echo $total_images - 9; ?> 查看更多</span>
                </div>
            </div>
        <?php else : ?>
            <div class="grid-item">
                <img src="<?php echo esc_url($image_src); ?>" alt="图片 <?php echo $i + 1; ?>" 
                     class="grid-image" data-image-src="<?php echo esc_url($image_src); ?>"
                     data-index="<?php echo $i; ?>">
            </div>
        <?php endif; endfor; ?>
    </div>
    
    <?php endif; ?>
    
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
                
                <button class="action-button share-button" onclick="openShareModal()">
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

<!-- 添加到single.php的文章内容底部 -->
<div class="lightbox-overlay" id="lightbox">
    <button class="lightbox-close" id="lightboxClose">×</button>
    <button class="lightbox-nav lightbox-prev" id="lightboxPrev">‹</button>
    <button class="lightbox-nav lightbox-next" id="lightboxNext">›</button>
    <div class="lightbox-content">
        <img src="" alt="" class="lightbox-image" id="lightboxImage">
        <div class="lightbox-caption" id="lightboxCaption"></div>
    </div>
</div>

    <!-- 分享弹窗 -->
    <div class="share-modal" id="shareModal">
        <div class="share-modal-content">
            <span class="share-modal-close">&times;</span>
            <h3>分享文章</h3>
            <div class="share-qrcode">
                <img src="" alt="文章二维码" id="qrcodeImage">
            </div>
            <p>扫描二维码分享</p>
            <div class="share-links">
                <button class="copy-link" onclick="copyLink()">复制链接</button>
            </div>
        </div>
    </div>

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
// 分享弹窗功能
function openShareModal() {
    // 加载二维码生成库(使用外部CDN)
    if (!window.QRCode) {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js';
        script.onload = () => generateQRCode();
        document.head.appendChild(script);
    } else {
        generateQRCode();
    }
    document.getElementById('shareModal').style.display = 'block';
}

function closeShareModal() {
    document.getElementById('shareModal').style.display = 'none';
}

function generateQRCode() {
    const url = '<?php echo esc_url(get_permalink()); ?>';
    const qrcodeElement = document.getElementById('qrcodeImage');
    QRCode.toCanvas(url, function (error, canvas) {
        if (error) console.error(error);
        qrcodeElement.src = canvas.toDataURL();
    });
}

function copyLink() {
    const url = '<?php echo esc_url(get_permalink()); ?>';
    navigator.clipboard.writeText(url).then(() => {
        alert('链接已复制到剪贴板');
    });
}

// 点击弹窗外部关闭
window.onclick = function(event) {
    const modal = document.getElementById('shareModal');
    if (event.target == modal) {
        closeShareModal();
    }
}

// 关闭按钮事件
document.querySelector('.share-modal-close').addEventListener('click', closeShareModal);

// 原生分享功能
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
        openShareModal(); // 不支持原生分享时显示弹窗
    }
}

// 滚动到评论区域
function scrollToComments() {
    document.getElementById('comments').scrollIntoView({
        behavior: 'smooth'
    });
}

// 点赞功能
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