<?php
/**
 * 关于我页面模板
 */
get_header();
?>

<div class="about-page">
    <div class="about-content">
        <?php
        if (have_posts()) :
            while (have_posts()) : the_post();
                the_content();
            endwhile;
        endif;
        ?>
    </div>
</div>

<?php get_footer(); ?>