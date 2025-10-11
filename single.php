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
    // 使用新的渲染函数输出九宫格（不改变目录结构，仅替换行为）
    minimal_personal_render_gallery( get_the_ID() );

    // 输出文章内容（the_content 的 filter 会移除 <img>，避免重复）
    the_content();
    ?>
        </div>

        <footer class="article-footer">
            <div class="article-actions">
                <button class="action-button like-button" data-post-id="<?php echo get_the_ID(); ?>">
                    <span class="action-icon">❤️</span>
                    <span class="action-text">点赞</span>
                </button>
                
                <button class="action-button share-button" onclick="openShareModal()">
                    <span class="action-icon">🔗</span>
                    <span class="action-text">分享</span>
                </button>
                
                <!-- 改为带 href 的锚点，onclick 调用 scrollToComments 以提供平滑滚动（并保留锚点作为备份） -->
                <a href="#comments" class="action-button comment-button" onclick="event.preventDefault(); scrollToComments();">
                    <span class="action-icon">💬</span>
                    <span class="action-text">评论</span>
                </a>
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

<!-- 保留主题原有灯箱结构（如有），但新 lightbox.js 也会创建自己的 lightbox DOM -->
<div class="lightbox-overlay" id="lightbox" aria-hidden="true">
    <button class="lightbox-close" id="lightboxClose" aria-label="关闭">×</button>
    <button class="lightbox-nav lightbox-prev" id="lightboxPrev" aria-label="上一张">‹</button>
    <button class="lightbox-nav lightbox-next" id="lightboxNext" aria-label="下一张">›</button>
    <div class="lightbox-content">
        <img src="" alt="" class="lightbox-image" id="lightboxImage">
        <div class="lightbox-caption" id="lightboxCaption"></div>
    </div>
</div>

    <!-- 分享弹窗 -->
    <div class="share-modal" id="shareModal" style="display:none;">
        <div class="share-modal-content">
            <span class="share-modal-close" role="button" aria-label="关闭">&times;</span>
            <h3>分享文章</h3>
            <div class="share-qrcode">
                <img src="" alt="文章二维码" id="qrcodeImage">
            </div>
            <p>扫描二维码分享</p>
            <div class="share-links">
                <button onclick="copyLink()">复制链接</button>
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
    const modal = document.getElementById('shareModal');
    if (modal) modal.style.display = 'block';
}

function closeShareModal() {
    const modal = document.getElementById('shareModal');
    if (modal) modal.style.display = 'none';
}

function generateQRCode() {
    const url = '<?php echo esc_url(get_permalink()); ?>';
    const qrcodeElement = document.getElementById('qrcodeImage');
    if (!window.QRCode || !qrcodeElement) return;
    QRCode.toCanvas(url, function (error, canvas) {
        if (error) console.error(error);
        qrcodeElement.src = canvas.toDataURL();
    });
}

function copyLink() {
    const url = '<?php echo esc_url(get_permalink()); ?>';
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(() => {
            alert('链接已复制到剪贴板');
        }).catch(function(){ alert('复制失败，请手动复制链接'); });
    } else {
        // 退回方案
        const el = document.createElement('textarea');
        el.value = url;
        document.body.appendChild(el);
        el.select();
        try { document.execCommand('copy'); alert('链接已复制到剪贴板'); } catch (e) { alert('复制失败，请手动复制链接'); }
        document.body.removeChild(el);
    }
}

// 点击弹窗外部关闭
window.onclick = function(event) {
    const modal = document.getElementById('shareModal');
    if (event.target == modal) {
        closeShareModal();
    }
}

// 绑定关闭按钮（先检查元素是否存在，避免抛错）
const shareClose = document.querySelector('.share-modal-close');
if (shareClose) {
    shareClose.addEventListener('click', closeShareModal);
}

// 滚动到评论区域（更鲁棒的实现）
function scrollToComments() {
    try {
        var el = document.getElementById('comments');
        if (el) {
            // 若 el 不是可聚焦元素，临时给 tabindex 以便聚焦（可访问性优化）
            if (!el.hasAttribute('tabindex')) {
                el.setAttribute('tabindex', '-1');
            }
            el.scrollIntoView({ behavior: 'smooth' });
            // 在滚动之后设置焦点，提升无障碍体验
            setTimeout(function() {
                try { el.focus({ preventScroll: true }); } catch (e) { el.focus(); }
            }, 400);
            return;
        }
        // 回退：使用 hash 触发跳转
        window.location.hash = '#comments';
    } catch (e) {
        // 最后保险回退
        window.location.hash = '#comments';
    }
}

// 点赞功能（保留原逻辑）
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
                if (response.success && response.data && response.data.likes !== undefined) {
                    $button.removeClass('loading').addClass('liked').prop('disabled', false);
                    $button.find('.action-text').text('已点赞');
                } else {
                    $button.removeClass('loading').prop('disabled', false);
                    alert('点赞失败，请重试');
                }
            },
            error: function() {
                $button.removeClass('loading').prop('disabled', false);
                alert('网络错误，请重试');
            }
        });
    });
});
</script>

<?php get_footer(); ?>