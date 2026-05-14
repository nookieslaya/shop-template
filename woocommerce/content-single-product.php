<?php

declare(strict_types=1);

defined('ABSPATH') || exit;
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class('single-product-view'); ?>>
    <div class="single-product-view__gallery">
        <?php do_action('woocommerce_before_single_product_summary'); ?>
    </div>

    <div class="single-product-view__summary">
        <?php do_action('woocommerce_single_product_summary'); ?>
    </div>

    <div class="single-product-view__meta">
        <?php do_action('woocommerce_after_single_product_summary'); ?>
    </div>
</div>
