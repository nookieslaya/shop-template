<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

global $product;

if (!$product || !$product->is_visible()) {
    return;
}
?>
<article <?php wc_product_class('product-card', $product); ?>>
    <a class="product-card__link" href="<?php the_permalink(); ?>">
        <div class="product-card__thumb">
            <?php
            if (has_post_thumbnail()) {
                the_post_thumbnail('shop-theme-product-card', ['class' => 'product-card__image']);
            }
            ?>
        </div>
        <h2 class="product-card__title"><?php the_title(); ?></h2>
        <p class="product-card__price"><?php echo wp_kses_post($product->get_price_html()); ?></p>
    </a>
    <div class="product-card__actions">
        <?php woocommerce_template_loop_add_to_cart(); ?>
    </div>
</article>
