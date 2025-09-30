<?php get_header(); ?>

<?php if (have_posts()) : ?>
    <div class="article-list">
        <?php while (have_posts()) : the_post(); ?>
            <article class="article-item">
                <h2 class="article-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h2>
                
                <div class="article-meta">
                    <time datetime="<?php echo get_the_date('c'); ?>">
                        <?php echo get_the_date(); ?>
                    </time>
                </div>
                
                <?php if (has_excerpt()) : ?>
                    <div class="article-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                <?php endif; ?>
            </article>
        <?php endwhile; ?>
    </div>
    
    <?php the_posts_pagination(); ?>
    
<?php else : ?>
    <p>暂无内容</p>
<?php endif; ?>

<?php get_footer(); ?>