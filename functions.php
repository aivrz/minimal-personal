<?php
// 主题设置
function minimal_personal_setup() {
    // 支持自定义Logo
    add_theme_support('custom-logo');
    // 启用WordPress自动生成标题标签
    add_theme_support('title-tag');
    // 支持文章特色图片
    add_theme_support('post-thumbnails');
    
    // 支持自动Feed链接
    add_theme_support('automatic-feed-links');
    
    // 支持HTML5
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    
    // 注册图片尺寸
    add_image_size('grid-thumb', 300, 300, true);
    
    // 创建必要的页面
    minimal_personal_create_pages();
    
    // 启用文章点赞功能
    minimal_personal_setup_likes();
    
    // 启用文章浏览计数
    minimal_personal_setup_views();
}
add_action('after_setup_theme', 'minimal_personal_setup');

// 创建默认页面
function minimal_personal_create_pages() {
    $pages = array(
        '发现' => array(
            'content' => '',
            'template' => 'page-discovery.php'
        ),
        '朋友' => array(
            'content' => '<!-- wp:html --><div class="links-list"></div><!-- /wp:html -->',
            'template' => 'page-friends.php'
        ),
        '我的' => array(
            'content' => '欢迎编辑您的个人介绍...',
            'template' => 'page-about.php'
        )
    );
    
    foreach ($pages as $title => $data) {
        $page = get_page_by_title($title);
        if (!$page) {
            $page_id = wp_insert_post(array(
                'post_title' => $title,
                'post_content' => $data['content'],
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => sanitize_title($title)
            ));
            
            if ($page_id && isset($data['template'])) {
                update_post_meta($page_id, '_wp_page_template', $data['template']);
            }
        }
    }
}

// 注册菜单
function minimal_personal_menus() {
    register_nav_menus(array(
        'bottom-nav' => '底部导航菜单'
    ));
}
add_action('init', 'minimal_personal_menus');

    // 自定义头像字段
    function minimal_personal_customize_register($wp_customize) {
    // 个人头像
    $wp_customize->add_setting('personal_avatar', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'personal_avatar', array(
        'label' => '个人头像',
        'section' => 'title_tagline',
        'settings' => 'personal_avatar'
    )));
    
    // 个人昵称
    $wp_customize->add_setting('personal_nickname', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    
    $wp_customize->add_control('personal_nickname', array(
        'label' => '个人昵称',
        'section' => 'title_tagline',
        'type' => 'text'
    ));
    // 首页元描述设置
    $wp_customize->add_setting('home_meta_description', array(
        'default' => get_bloginfo('description'), // 默认使用站点描述
        'sanitize_callback' => 'sanitize_textarea_field'
    ));
    
    $wp_customize->add_control('home_meta_description', array(
        'label' => '首页元描述',
        'description' => '用于搜索引擎展示的摘要信息，建议120-160个字符',
        'section' => 'title_tagline',
        'type' => 'textarea'
    ));
    // 个性签名
    $wp_customize->add_setting('personal_signature', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    
    $wp_customize->add_control('personal_signature', array(
        'label' => '个性签名',
        'section' => 'title_tagline',
        'type' => 'text'
    ));
}
add_action('customize_register', 'minimal_personal_customize_register');

// 友情链接自定义文章类型
function minimal_personal_links_post_type() {
    register_post_type('friend_link',
        array(
            'labels' => array(
                'name' => '友情链接',
                'singular_name' => '友情链接'
            ),
            'public' => true,
            'has_archive' => false,
            'supports' => array('title', 'thumbnail'),
            'menu_icon' => 'dashicons-admin-links'
        )
    );
}
add_action('init', 'minimal_personal_links_post_type');

// 获取页面ID函数
function minimal_personal_get_page_id($title) {
    $page = get_page_by_title($title);
    return $page ? $page->ID : 0;
}

// 设置点赞功能
function minimal_personal_setup_likes() {
    // 为文章添加点赞meta字段
    register_meta('post', '_minimal_personal_likes', array(
        'type' => 'integer',
        'single' => true,
        'default' => 0,
        'show_in_rest' => true,
    ));
}

// 设置浏览计数功能
function minimal_personal_setup_views() {
    // 为文章添加浏览计数meta字段
    register_meta('post', '_minimal_personal_views', array(
        'type' => 'integer',
        'single' => true,
        'default' => 0,
        'show_in_rest' => true,
    ));
}

// 更新文章浏览计数
function minimal_personal_update_views($post_id) {
    if (!is_single() || !$post_id) return;
    
    $current_views = get_post_meta($post_id, '_minimal_personal_views', true) ?: 0;
    $new_views = $current_views + 1;
    update_post_meta($post_id, '_minimal_personal_views', $new_views);
}
add_action('wp_head', 'minimal_personal_update_views');

// 处理点赞AJAX请求
function minimal_personal_handle_like() {
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    
    if ($post_id > 0) {
        $current_likes = get_post_meta($post_id, '_minimal_personal_likes', true) ?: 0;
        $new_likes = $current_likes + 1;
        update_post_meta($post_id, '_minimal_personal_likes', $new_likes);
        
        wp_send_json_success(array(
            'likes' => $new_likes,
            'message' => '点赞成功'
        ));
    }
    
    wp_send_json_error('点赞失败');
}

add_action('wp_ajax_minimal_personal_like', 'minimal_personal_handle_like');
add_action('wp_ajax_nopriv_minimal_personal_like', 'minimal_personal_handle_like');

// 添加CSS和JS
function minimal_personal_enqueue_scripts() {
    wp_enqueue_style('minimal-personal-style', get_stylesheet_uri());
    
    // 仅在文章详情页加载灯箱样式和脚本
    if (is_single()) { // 关键：只在文章页面加载
        wp_enqueue_script('minimal-personal-lightbox', get_template_directory_uri() . '/js/lightbox.js', array(), '1.0', true);
        wp_enqueue_style('minimal-personal-lightbox-style', get_template_directory_uri() . '/css/lightbox.css');
    }
    
    // 点赞功能脚本（所有页面都需要）
    wp_enqueue_script('minimal-personal-likes', get_template_directory_uri() . '/js/likes.js', array('jquery'), '1.0', true);
    wp_localize_script('minimal-personal-likes', 'minimal_personal_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('minimal_personal_nonce')
    ));

    // 主题切换脚本（用于自动按时区和手动切换）
    wp_enqueue_script('minimal-personal-theme', get_template_directory_uri() . '/js/theme-toggle.js', array(), '1.0', true);

    // 评论回复脚本（如果支持评论）
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'minimal_personal_enqueue_scripts');

// 自定义评论表单字段
function minimal_personal_comment_form_fields($fields) {
    $commenter = wp_get_current_commenter();
    $req = get_option('require_name_email');
    $aria_req = ($req ? " required" : '');
    
    $fields['author'] = '
    <div class="comment-form-author">
        <input id="author" name="author" type="text" placeholder="姓名' . ($req ? ' *' : '') . '" 
               value="' . esc_attr($commenter['comment_author']) . '" size="30"' . $aria_req . ' />
    </div>';
    
    $fields['email'] = '
    <div class="comment-form-email">
        <input id="email" name="email" type="email" placeholder="邮箱' . ($req ? ' *' : '') . '" 
               value="' . esc_attr($commenter['comment_author_email']) . '" size="30"' . $aria_req . ' />
    </div>';
    
    $fields['url'] = '
    <div class="comment-form-url">
        <input id="url" name="url" type="url" placeholder="网站" 
               value="' . esc_attr($commenter['comment_author_url']) . '" size="30" />
    </div>';
    
    return $fields;
}
add_filter('comment_form_fields', 'minimal_personal_comment_form_fields');

// 自定义评论表单
function minimal_personal_comment_form($args) {
    $args['comment_field'] = '
    <div class="comment-form-comment">
        <textarea id="comment" name="comment" placeholder="写下您的评论..." rows="4" required></textarea>
    </div>';
    
    $args['submit_button'] = '
    <button type="submit" class="comment-submit">发表评论</button>';
    
    $args['title_reply'] = '发表评论';
    $args['title_reply_to'] = '回复 %s';
    $args['cancel_reply_link'] = '取消回复';
    $args['label_submit'] = '发表评论';
    
    return $args;
}
add_filter('comment_form_defaults', 'minimal_personal_comment_form');

// 自定义评论显示回调函数
function minimal_personal_comment_callback($comment, $args, $depth) {
    $tag = ($args['style'] == 'div') ? 'div' : 'li';
    ?>
    
    <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class(empty($args['has_children']) ? '' : 'parent'); ?>>
        <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
            <div class="comment-avatar">
                <?php if ($args['avatar_size'] != 0) : ?>
                    <?php echo get_avatar($comment, $args['avatar_size']); ?>
                <?php endif; ?>
            </div>
            
            <div class="comment-content">
                <div class="comment-meta">
                    <div class="comment-author">
                        <?php printf('%s', get_comment_author_link()); ?>
                    </div>
                    
                    <div class="comment-date">
                        <time datetime="<?php comment_time('c'); ?>">
                            <?php printf('%1$s %2$s', get_comment_date(), get_comment_time()); ?>
                        </time>
                    </div>
                </div>
                
                <div class="comment-text">
                    <?php comment_text(); ?>
                    
                    <?php if ($comment->comment_approved == '0') : ?>
                        <p class="comment-awaiting-moderation">您的评论正在等待审核</p>
                    <?php endif; ?>
                </div>
                
                <div class="comment-reply">
                    <?php
                    comment_reply_link(array_merge($args, array(
                        'depth'     => $depth,
                        'max_depth' => $args['max_depth'],
                        'reply_text' => '回复'
                    )));
                    ?>
                </div>
            </div>
        </article>
    <?php
}
// 添加加载更多文章的AJAX处理
function minimal_personal_load_more() {
    check_ajax_referer('minimal_personal_nonce', 'nonce');
    
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 10,
        'paged' => $page,
        'post_status' => 'publish'
    );
    
    $query = new WP_Query($args);
    
    ob_start();
    
    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
            ?>
            <article class="article-item">
                <h2 class="article-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h2>
                
                <div class="article-meta">
                    <time datetime="<?php echo get_the_date('c'); ?>">
                        <?php echo get_the_date(); ?>
                    </time>
                    <span class="view-count">
                        <?php 
                        $views = get_post_meta(get_the_ID(), '_minimal_personal_views', true) ?: 0;
                        echo $views . ' 浏览'; 
                        ?>
                    </span>
                </div>
                
                <div class="article-excerpt">
                    <?php 
                    if (has_excerpt()) {
                        the_excerpt();
                    } else {
                        $content = wp_strip_all_tags(get_the_content());
                        echo mb_substr($content, 0, 150) . '...';
                    }
                    ?>
                </div>
                
                <div class="read-more">
                    <a href="<?php the_permalink(); ?>">阅读全文 →</a>
                </div>
            </article>
            <?php
        endwhile;
        wp_reset_postdata();
    endif;
    
    $html = ob_get_clean();
    
    wp_send_json_success(array(
        'html' => $html,
        'has_more' => $query->max_num_pages > $page
    ));
}


add_action('wp_ajax_minimal_personal_load_more', 'minimal_personal_load_more');
add_action('wp_ajax_nopriv_minimal_personal_load_more', 'minimal_personal_load_more');

/**
 * 为friend_link添加自定义字段（链接地址和跳转方式）
 */
function friend_link_add_meta_box() {
    // 仅在friend_link类型的文章编辑页显示
    add_meta_box(
        'friend_link_meta_box',
        '友情链接信息',
        'friend_link_meta_box_callback',
        'friend_link', // 对应你的自定义文章类型
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'friend_link_add_meta_box');

/**
 * 自定义字段的HTML内容
 */
function friend_link_meta_box_callback($post) {
    // 加载已保存的数据
    $link_url = get_post_meta($post->ID, '_friend_link_url', true);
    $link_target = get_post_meta($post->ID, '_friend_link_target', true) ?: '_blank'; // 默认新窗口打开

    // 安全验证字段
    wp_nonce_field('save_friend_link_meta', 'friend_link_meta_nonce');

    // 链接地址输入框
    echo '<p>';
    echo '<label for="friend_link_url" style="display: block; margin-bottom: 5px; font-weight: bold;">站点链接（必须包含http://或https://）：</label>';
    echo '<input type="url" id="friend_link_url" name="friend_link_url" value="' . esc_attr($link_url) . '" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required';
    echo '</p>';

    // 跳转方式选择框
    echo '<p>';
    echo '<label for="friend_link_target" style="display: block; margin-bottom: 5px; font-weight: bold;">跳转方式：</label>';
    echo '<select id="friend_link_target" name="friend_link_target" style="width: 200px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">';
    echo '<option value="_blank"' . selected($link_target, '_blank', false) . '>新窗口打开（推荐）</option>';
    echo '<option value="_self"' . selected($link_target, '_self', false) . '>当前窗口打开</option>';
    echo '</select>';
    echo '</p>';
}

/**
 * 保存自定义字段数据
 */
function save_friend_link_meta($post_id) {
    // 验证安全字段
    if (!isset($_POST['friend_link_meta_nonce']) || !wp_verify_nonce($_POST['friend_link_meta_nonce'], 'save_friend_link_meta')) {
        return;
    }
    // 禁止自动保存时执行
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    // 验证权限（确保是friend_link类型且有编辑权限）
    if (isset($_POST['post_type']) && $_POST['post_type'] === 'friend_link' && !current_user_can('edit_post', $post_id)) {
        return;
    }

    // 保存链接地址
    if (isset($_POST['friend_link_url'])) {
        $url = esc_url_raw($_POST['friend_link_url']); // 验证并净化URL
        update_post_meta($post_id, '_friend_link_url', $url);
    }

    // 保存跳转方式
    if (isset($_POST['friend_link_target']) && in_array($_POST['friend_link_target'], ['_blank', '_self'])) {
        update_post_meta($post_id, '_friend_link_target', sanitize_text_field($_POST['friend_link_target']));
    }
}
add_action('save_post', 'save_friend_link_meta');

/**
 * 在friend_link列表页添加自定义列
 */
function friend_link_add_list_columns($columns) {
    // 添加“链接地址”和“跳转方式”列
    $columns['link_url'] = '站点链接';
    $columns['link_target'] = '跳转方式';
    return $columns;
}
add_filter('manage_friend_link_posts_columns', 'friend_link_add_list_columns');

/**
 * 填充自定义列的内容
 */
function friend_link_fill_list_columns($column, $post_id) {
    switch ($column) {
        case 'link_url':
            $url = get_post_meta($post_id, '_friend_link_url', true);
            if ($url) {
                echo '<a href="' . esc_url($url) . '" target="_blank">' . esc_html($url) . '</a>';
            } else {
                echo '<span style="color: #dc3232;">未设置</span>';
            }
            break;
        case 'link_target':
            $target = get_post_meta($post_id, '_friend_link_target', true) ?: '_blank';
            echo $target === '_blank' ? '新窗口' : '当前窗口';
            break;
    }
}
add_action('manage_friend_link_posts_custom_column', 'friend_link_fill_list_columns', 10, 2);

/**
 * 图片处理与九宫格渲染（修改/替换点）
 *
 * 说明：
 * - minimal_personal_get_post_image_ids(): 解析 content 中的 img src -> attachment ID（优先）并补充 article attachments
 * - minimal_personal_render_gallery(): 在文章页输出九宫格 DOM（最多9张，超出在第9张显示 +N）
 * - minimal_personal_get_post_images(): 向后兼容接口（返回 full url 列表）
 * - 保留对 the_content 的过滤：移除原始 <img>，并为残留图片补 data-image-src
 */

/**
 * 返回文章中图片的附件ID数组（优先解析 content 中的图片 URL -> attachment ID，补充附加的 attachment）
 */
function minimal_personal_get_post_image_ids( $post_id = 0 ) {
    $post_id = $post_id ? intval( $post_id ) : get_the_ID();
    if ( ! $post_id ) {
        return array();
    }

    $post = get_post( $post_id );
    if ( ! $post ) {
        return array();
    }

    $ids = array();

    // 1) 从文章内容解析出所有 img 的 src，并尝试把 URL 转为附件 ID
    if ( preg_match_all( '/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', $post->post_content, $matches ) ) {
        foreach ( $matches[1] as $src ) {
            $src = esc_url_raw( $src );
            $aid = attachment_url_to_postid( $src );
            if ( $aid && ! in_array( $aid, $ids, true ) ) {
                $ids[] = $aid;
            }
        }
    }

    // 2) 补上所有已上传并附着到当前文章的图片（保持上传顺序）
    $attached = get_children( array(
        'post_parent'    => $post_id,
        'post_status'    => 'inherit',
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'orderby'        => 'menu_order ID',
        'order'          => 'ASC',
    ) );
    if ( $attached ) {
        foreach ( $attached as $att ) {
            if ( ! in_array( $att->ID, $ids, true ) ) {
                $ids[] = $att->ID;
            }
        }
    }

    return $ids;
}

/**
 * 在单篇文章页面渲染九宫格画廊（最多显示前9张，超出在第9张显示“查看更多”）
 */
function minimal_personal_render_gallery( $post_id = 0 ) {
    $post_id = $post_id ? intval( $post_id ) : get_the_ID();
    $ids    = minimal_personal_get_post_image_ids( $post_id );

    if ( empty( $ids ) ) {
        return;
    }

    $total = count( $ids );
    $max_visible = 9;
    $visible = array_slice( $ids, 0, $max_visible );
    $remaining = max( 0, $total - count( $visible ) );

    // 准备 full 大图 URL 数组，供 JS lightbox 使用
    $full_urls = array();
    foreach ( $ids as $aid ) {
        $url = wp_get_attachment_image_url( $aid, 'full' );
        if ( $url ) {
            $full_urls[] = $url;
        }
    }

    // 输出九宫格结构
    ?>
    <div class="mp-gallery" aria-label="<?php echo esc_attr__( '文章画廊', 'minimal-personal' ); ?>" data-full-urls="<?php echo esc_attr( wp_json_encode( $full_urls ) ); ?>">
        <div class="mp-gallery__grid" role="list">
            <?php foreach ( $visible as $i => $aid ) : 
                $alt = get_post_meta( $aid, '_wp_attachment_image_alt', true );
                // 使用主题已注册的 grid-thumb（或 grid-thumb 可替换为更大尺寸）
                $img = wp_get_attachment_image( $aid, 'grid-thumb', false, array(
                    'class' => 'mp-gallery__img',
                    'loading' => 'lazy',
                    'data-index' => esc_attr( $i ),
                    'alt' => esc_attr( $alt ),
                ) );
            ?>
                <figure class="mp-gallery__item" role="listitem">
                    <button class="mp-gallery__btn" type="button" data-index="<?php echo esc_attr( $i ); ?>" aria-label="<?php echo esc_attr__( '打开图片', 'minimal-personal' ); ?>">
                        <?php echo $img; ?>
                        <?php if ( $i === $max_visible - 1 && $remaining > 0 ) : ?>
                            <div class="mp-gallery__more" aria-hidden="true">
                                <span class="mp-gallery__more-count"><?php echo '+' . intval( $remaining ); ?></span>
                                <span class="mp-gallery__more-label"><?php echo esc_html__( '查看更多', 'minimal-personal' ); ?></span>
                            </div>
                        <?php endif; ?>
                    </button>
                </figure>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

/**
 * 兼容之前的函数：保留 minimal_personal_get_post_images() 的调用点（向后兼容）
 * 旧函数改为返回图片URL列表（仅在需要时）
 */
function minimal_personal_get_post_images() {
    $ids = minimal_personal_get_post_image_ids();
    $urls = array();
    foreach ( $ids as $id ) {
        $url = wp_get_attachment_image_url( $id, 'full' );
        if ( $url ) $urls[] = $url;
    }
    return $urls;
}

/**
 * 保持移除原始 content 中 img 的行为（单篇页面）
 * 保留，确保内容区不会重复显示图片（我们用 render_gallery 来输出）
 */
function minimal_personal_remove_images_from_content( $content ) {
    if ( is_single() ) {
        $content = preg_replace( '/<img[^>]+>/i', '', $content );
    }
    return $content;
}
add_filter( 'the_content', 'minimal_personal_remove_images_from_content', 100 );

/**
 * 为 content 中的图片（若仍存在）补充 data-image-src 属性（尽量不依赖）
 * 仅作兼容（不会影响我们渲染的九宫格）
 */
function add_lightbox_attr_to_content_images( $content ) {
    if ( is_single() && $content ) {
        $content = preg_replace_callback( '/<img(.*?)src=[\'"](.*?)[\'"](.*?)>/i', function( $matches ) {
            $full_src = $matches[2];
            return '<img' . $matches[1] . 'src="' . esc_url( $matches[2] ) . '" data-image-src="' . esc_attr( $full_src ) . '"' . $matches[3] . '>';
        }, $content );
    }
    return $content;
}
add_filter( 'the_content', 'add_lightbox_attr_to_content_images' );