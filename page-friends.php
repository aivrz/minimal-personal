<?php
/**
 * Template Name: 朋友
 * 显示friend_link类型的友情链接
 * @package Minimal_Personal
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <?php
        while (have_posts()) :
            the_post();
            ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                </header><!-- .entry-header -->

                <div class="entry-content">
                    <?php
                    // 显示页面正文内容（可写友情链接说明）
                    the_content();

                    // 查询所有已发布的friend_link
                    $args = array(
                        'post_type' => 'friend_link',
                        'posts_per_page' => -1, // 显示全部
                        'post_status' => 'publish',
                        'orderby' => 'date', // 按添加时间排序
                        'order' => 'DESC'
                    );
                    $friend_links = new WP_Query($args);

                    if ($friend_links->have_posts()) {
                        echo '<div class="friends-section" style="margin-top: 30px;">';
                        echo '<h3 style="margin-bottom: 20px;">友情链接</h3>';
                        echo '<ul class="friends-list" style="display: flex; flex-wrap: wrap; gap: 15px; list-style: none; padding: 0;">';

                        // 循环输出每个友情链接
                        while ($friend_links->have_posts()) {
                            $friend_links->the_post();
                            $link_url = get_post_meta(get_the_ID(), '_friend_link_url', true);
                            $link_target = get_post_meta(get_the_ID(), '_friend_link_target', true) ?: '_blank';

                            // 只显示有链接的项
                            if ($link_url) {
                                echo '<li class="friend-link-item">';
                                echo '<a href="' . esc_url($link_url) . '" ';
                                echo 'target="' . esc_attr($link_target) . '" ';
                                echo 'rel="' . ($link_target === '_blank' ? 'noopener noreferrer' : 'nofollow') . '" ';
                                echo 'style="display: inline-block; padding: 8px 15px; background: #f8f9fa; border-radius: 4px; color: #333; text-decoration: none; transition: all 0.3s ease; border: 1px solid #eee;">';
                                echo esc_html(get_the_title()); // 站点标题（使用文章标题）
                                echo '</a>';
                                echo '</li>';
                            }
                        }

                        echo '</ul>';
                        echo '</div>';
                        wp_reset_postdata(); // 重置查询
                    } else {
                        // 没有链接时显示提示
                        echo '<div class="no-friends" style="margin-top: 20px; color: #666; padding: 20px; background: #f9f9f9; border-radius: 4px;">';
                        echo '暂无友情链接，欢迎交换链接~';
                        echo '</div>';
                    }
                    ?>
                </div><!-- .entry-content -->
            </article><!-- #post-<?php the_ID(); ?> -->

            <?php
            // 如果允许评论，显示评论区
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;

        endwhile; // 结束循环
        ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();