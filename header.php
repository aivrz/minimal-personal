<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php 
    // 仅在首页添加的SEO标签
    if (is_front_page()) : 
        // 元描述
        $home_desc = get_theme_mod('home_meta_description', get_bloginfo('description'));
        if (!empty($home_desc)) : ?>
            <meta name="description" content="<?php echo esc_attr($home_desc); ?>">
        <?php endif;
        
        // 规范链接（避免重复内容）
        echo '<link rel="canonical" href="' . esc_url(home_url('/')) . '">';
        
        // 搜索引擎抓取规则
        echo '<meta name="robots" content="index, follow">';
    endif; 
    ?>
    <!-- 站点地图链接（如果有） -->
        <link rel="sitemap" type="application/xml" title="Sitemap" href="<?php echo esc_url(home_url('/sitemap_index.xml')); ?>">
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
                <h1 class="header-nickname"><?php echo esc_html($nickname); ?></h1>
                <?php if ($signature) : ?>
                    <div class="header-signature"><?php echo esc_html($signature); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="site-main">