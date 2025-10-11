<?php
/**
 * 发现页面模板 - 文章九宫格展示
 */
get_header();
?>

<div class="discovery-page">
    <div class="grid-container">
        <?php
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => 12,
            'paged' => $paged,
            'post_status' => 'publish'
        );
        
        $discovery_query = new WP_Query($args);
        
        if ($discovery_query->have_posts()) :
            while ($discovery_query->have_posts()) : $discovery_query->the_post();
                $post_id = get_the_ID();
                $likes = get_post_meta($post_id, '_minimal_personal_likes', true) ?: 0;
                $thumbnail_url = get_the_post_thumbnail_url($post_id, 'grid-thumb');
                ?>
                
                <div class="grid-item">
                    <?php if ($thumbnail_url) : ?>
                        <img src="<?php echo esc_url($thumbnail_url); ?>" 
                             alt="<?php echo esc_attr(sprintf('%s - %s', get_the_title(), get_bloginfo('name'))); ?>" 
                            class="grid-image"
                            data-post-id="<?php echo $post_id; ?>"
                             data-image-src="<?php echo esc_url(get_the_post_thumbnail_url($post_id, 'large')); ?>">
                    <?php else : ?>
                        <div class="grid-image no-image" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #999;">
                            暂无图片
                        </div>
                    <?php endif; ?>
                    
                    <div class="grid-info">
                        <h2 class="grid-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        
                        <div class="grid-meta">
                            <span class="post-date"><?php echo get_the_date(); ?></span>
                            <button class="like-button" data-post-id="<?php echo $post_id; ?>">
                                <span class="like-icon">❤</span>
                                <span class="like-count"><?php echo $likes; ?></span>
                            </button>
                        </div>
                    </div>
                </div>
                
            <?php endwhile; ?>
        <?php else : ?>
            <p style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #666;">
                暂无文章内容
            </p>
        <?php endif; ?>
        
        <?php wp_reset_postdata(); ?>
    </div>
    
    <!-- 分页 -->
    <div class="pagination">
        <?php
        $big = 999999999;
        echo paginate_links(array(
            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format' => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total' => $discovery_query->max_num_pages,
            'prev_text' => '« 上一页',
            'next_text' => '下一页 »'
        ));
        ?>
    </div>
</div>

<!-- 灯箱结构 -->
<div class="lightbox-overlay" id="lightbox">
    <button class="lightbox-close" id="lightboxClose">×</button>
    <button class="lightbox-nav lightbox-prev" id="lightboxPrev">‹</button>
    <button class="lightbox-nav lightbox-next" id="lightboxNext">›</button>
    <div class="lightbox-content">
        <img src="" alt="" class="lightbox-image" id="lightboxImage">
        <div class="lightbox-caption" id="lightboxCaption"></div>
    </div>
</div>

<?php get_footer(); ?>