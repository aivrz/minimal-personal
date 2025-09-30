<?php
/**
 * 朋友页面模板 - 友情链接
 */
get_header();

// 获取所有友情链接
$friend_links = get_posts(array(
    'post_type' => 'friend_link',
    'numberposts' => -1,
    'orderby' => 'title',
    'order' => 'ASC'
));
?>

<div class="friends-page">
    <h1>友情链接</h1>
    
    <?php if ($friend_links) : ?>
        <ul class="links-list">
            <?php foreach ($friend_links as $link) : ?>
                <li class="link-item">
                    <a href="<?php echo get_permalink($link->ID); ?>" target="_blank">
                        <?php echo esc_html($link->post_title); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>暂无友情链接</p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>