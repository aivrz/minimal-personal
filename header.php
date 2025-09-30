<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <header class="site-header">
        <div class="header-content">
            <?php
            $avatar = get_theme_mod('personal_avatar');
            $nickname = get_theme_mod('personal_nickname', get_bloginfo('name'));
            $signature = get_theme_mod('personal_signature', get_bloginfo('description'));
            
            if ($avatar) {
                // 添加链接到首页
                echo '<a href="' . esc_url(home_url('/')) . '" class="avatar-link">';
                echo '<img src="' . esc_url($avatar) . '" alt="' . esc_attr($nickname) . '" class="header-avatar">';
                echo '</a>';
            }
            ?>
            <div class="header-text">
                <div class="header-nickname"><?php echo esc_html($nickname); ?></div>
                <?php if ($signature) : ?>
                    <div class="header-signature"><?php echo esc_html($signature); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="site-main">