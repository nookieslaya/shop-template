<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<section class="shop-archive section">
    <div class="container shop-archive__inner">
        <header class="shop-archive__header">
            <h1 class="shop-archive__title"><?php woocommerce_page_title(); ?></h1>
            <?php do_action('woocommerce_archive_description'); ?>
        </header>

        <?php if (woocommerce_product_loop()) : ?>
            <?php do_action('woocommerce_before_shop_loop'); ?>
            <div class="products-grid">
                <?php
                while (have_posts()) :
                    the_post();
                    wc_get_template_part('content', 'product');
                endwhile;
                ?>
            </div>
            <?php do_action('woocommerce_after_shop_loop'); ?>
        <?php else : ?>
            <?php do_action('woocommerce_no_products_found'); ?>
        <?php endif; ?>
    </div>
</section>
<?php
get_footer();
