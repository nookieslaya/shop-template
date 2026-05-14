<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<section class="section">
    <div class="container">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article <?php post_class('entry'); ?> id="post-<?php the_ID(); ?>">
                    <h2 class="entry__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <div class="entry__content"><?php the_excerpt(); ?></div>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <p><?php esc_html_e('No posts found.', 'shop-theme'); ?></p>
        <?php endif; ?>
    </div>
</section>
<?php
get_footer();
