    </main>

    <footer class="site-footer">
        <nav class="nav-menu">
            <?php
            $discovery_page_id = minimal_personal_get_page_id('发现');
            $friends_page_id = minimal_personal_get_page_id('朋友');
            $about_page_id = minimal_personal_get_page_id('我的');
            ?>
            
            <a href="<?php echo $discovery_page_id ? get_permalink($discovery_page_id) : home_url(); ?>" 
               class="nav-button <?php echo (is_home() || is_page('发现')) ? 'active' : ''; ?>">
                <span class="nav-icon">🔍</span>
                <span>发现</span>
            </a>
            
            <a href="<?php echo $friends_page_id ? get_permalink($friends_page_id) : '#'; ?>" 
               class="nav-button <?php echo is_page('朋友') ? 'active' : ''; ?>">
                <span class="nav-icon">👥</span>
                <span>朋友</span>
            </a>
            
            <a href="<?php echo $about_page_id ? get_permalink($about_page_id) : '#'; ?>" 
               class="nav-button <?php echo is_page('我的') ? 'active' : ''; ?>">
                <span class="nav-icon">👤</span>
                <span>我的</span>
            </a>
        </nav>
    </footer>

    <?php wp_footer(); ?>
</body>
</html>