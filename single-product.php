<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<section class="product-single section">
    <div class="container">
        <?php while (have_posts()) : ?>
            <?php the_post(); ?>
            <?php wc_get_template_part('content', 'single-product'); ?>
        <?php endwhile; ?>
    </div>
</section>
<?php
get_footer();
