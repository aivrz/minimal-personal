<?php
/**
 * 评论模板
 */
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">
    <?php if (have_comments()) : ?>
        <h3 class="comments-title">
            <?php
            $comments_number = get_comments_number();
            if ($comments_number === 1) {
                printf('1 条评论');
            } else {
                printf('%s 条评论', number_format_i18n($comments_number));
            }
            ?>
        </h3>

        <ol class="comment-list">
            <?php
            wp_list_comments(array(
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 50,
                'callback'    => 'minimal_personal_comment_callback'
            ));
            ?>
        </ol>

        <?php
        // 评论分页
        the_comments_pagination(array(
            'prev_text' => '&larr; 上一页',
            'next_text' => '下一页 &rarr;',
        ));
        ?>

    <?php endif; ?>

    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
        <p class="no-comments">评论已关闭</p>
    <?php endif; ?>

    <?php comment_form(); ?>
</div>