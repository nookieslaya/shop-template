<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$section_title = trim((string) get_theme_mod('shop_theme_featured_section_title', __('Featured Products', 'shop-theme')));
$section_subtitle = trim((string) get_theme_mod('shop_theme_featured_section_subtitle', __('Handpicked favorites that our customers love', 'shop-theme')));
$title_size_desktop = (int) get_theme_mod('shop_theme_featured_title_size_desktop', 48);
$title_size_mobile = (int) get_theme_mod('shop_theme_featured_title_size_mobile', 32);
$subtitle_size_desktop = (int) get_theme_mod('shop_theme_featured_subtitle_size_desktop', 20);
$subtitle_size_mobile = (int) get_theme_mod('shop_theme_featured_subtitle_size_mobile', 16);
$max_products = (int) get_theme_mod('shop_theme_featured_max_count', 8);
$show_out_of_stock = (bool) get_theme_mod('shop_theme_featured_show_out_of_stock', false);
$padding_top = (int) get_theme_mod('shop_theme_featured_padding_top', 80);
$padding_bottom = (int) get_theme_mod('shop_theme_featured_padding_bottom', 80);
$background_color = trim((string) get_theme_mod('shop_theme_featured_bg_color', '#ececf0'));

$query_args = [
    'status'  => 'publish',
    'limit'   => max(1, $max_products),
    'featured'=> true,
    'orderby' => 'date',
    'order'   => 'DESC',
    'return'  => 'objects',
];

if (!$show_out_of_stock) {
    $query_args['stock_status'] = 'instock';
}

$products = wc_get_products($query_args);
if (empty($products)) {
    return;
}

$use_slider = count($products) > 4;
$favorites_url = trim((string) get_theme_mod('shop_theme_favorites_url', home_url('/favorites/')));
?>
<section
    class="featured-products section"
    aria-labelledby="featured-products-title"
    data-slider="<?php echo esc_attr($use_slider ? 'true' : 'false'); ?>"
    style="--fp-title-size-desktop: <?php echo esc_attr((string) max(24, min(90, $title_size_desktop))); ?>px; --fp-title-size-mobile: <?php echo esc_attr((string) max(20, min(64, $title_size_mobile))); ?>px; --fp-subtitle-size-desktop: <?php echo esc_attr((string) max(14, min(40, $subtitle_size_desktop))); ?>px; --fp-subtitle-size-mobile: <?php echo esc_attr((string) max(12, min(32, $subtitle_size_mobile))); ?>px; --fp-padding-top: <?php echo esc_attr((string) max(0, min(200, $padding_top))); ?>px; --fp-padding-bottom: <?php echo esc_attr((string) max(0, min(200, $padding_bottom))); ?>px; --fp-bg-color: <?php echo esc_attr($background_color); ?>;"
>
    <div class="container">
        <header class="featured-products__header">
            <?php if ($section_title !== '') : ?>
                <h2 class="featured-products__title" id="featured-products-title"><?php echo esc_html($section_title); ?></h2>
            <?php endif; ?>
            <?php if ($section_subtitle !== '') : ?>
                <p class="featured-products__subtitle"><?php echo esc_html($section_subtitle); ?></p>
            <?php endif; ?>
        </header>

        <div class="featured-products__viewport" tabindex="0" aria-label="<?php esc_attr_e('Featured products slider', 'shop-theme'); ?>">
            <?php if ($use_slider) : ?>
                <div class="featured-products__nav">
                    <button class="featured-products__button featured-products__button--prev" type="button" aria-label="<?php esc_attr_e('Previous products', 'shop-theme'); ?>">
                        <svg class="featured-products__button-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M14.5 5.5L8 12l6.5 6.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <button class="featured-products__button featured-products__button--next" type="button" aria-label="<?php esc_attr_e('Next products', 'shop-theme'); ?>">
                        <svg class="featured-products__button-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9.5 5.5L16 12l-6.5 6.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            <?php endif; ?>

            <ul class="featured-products__track <?php echo esc_attr($use_slider ? 'is-slider' : 'is-grid'); ?>">
                <?php foreach ($products as $product) : ?>
                    <?php
                    if (!$product instanceof WC_Product) {
                        continue;
                    }

                    $product_id = $product->get_id();
                    $product_link = get_permalink($product_id);
                    $image_url = get_the_post_thumbnail_url($product_id, 'large');
                    if (!$image_url) {
                        $image_url = wc_placeholder_img_src('large');
                    }

                    $created_ts = strtotime((string) $product->get_date_created());
                    $is_new = $created_ts && $created_ts >= strtotime('-30 days');

                    $sale_badge = '';
                    if ($product->is_on_sale() && $product->get_regular_price() !== '') {
                        $regular = (float) $product->get_regular_price();
                        $sale = (float) $product->get_sale_price();
                        if ($regular > 0 && $sale > 0 && $sale < $regular) {
                            $discount = (int) round((($regular - $sale) / $regular) * 100);
                            $sale_badge = '-' . $discount . '%';
                        }
                    }

                    $GLOBALS['product'] = $product;
                    $post_object = get_post($product_id);
                    if ($post_object) {
                        setup_postdata($post_object);
                    }
                    ?>
                    <li class="featured-products__item">
                        <article class="featured-products__card">
                            <div class="featured-products__media">
                                <a class="featured-products__image-link" href="<?php echo esc_url($product_link); ?>">
                                    <img class="featured-products__image" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($product->get_name()); ?>">
                                </a>

                                <?php if ($sale_badge !== '') : ?>
                                    <span class="featured-products__badge featured-products__badge--sale"><?php echo esc_html($sale_badge); ?></span>
                                <?php elseif ($is_new) : ?>
                                    <span class="featured-products__badge featured-products__badge--new"><?php esc_html_e('New', 'shop-theme'); ?></span>
                                <?php endif; ?>

                                <a class="featured-products__favorite" href="<?php echo esc_url($favorites_url !== '' ? $favorites_url : home_url('/favorites/')); ?>" aria-label="<?php esc_attr_e('Add to favorites', 'shop-theme'); ?>">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 20.8l-1.1-1C6.1 15.6 3 12.8 3 9.3A4.3 4.3 0 0 1 7.4 5a4.7 4.7 0 0 1 4.6 2.7A4.7 4.7 0 0 1 16.6 5 4.3 4.3 0 0 1 21 9.3c0 3.5-3.1 6.3-7.9 10.5l-1.1 1Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                                </a>

                                <div class="featured-products__cart-wrap">
                                    <?php woocommerce_template_loop_add_to_cart(); ?>
                                </div>
                            </div>

                            <div class="featured-products__content">
                                <h3 class="featured-products__product-title"><a href="<?php echo esc_url($product_link); ?>"><?php echo esc_html($product->get_name()); ?></a></h3>

                                <div class="featured-products__rating">
                                    <?php echo wp_kses_post(wc_get_rating_html((float) $product->get_average_rating(), (int) $product->get_review_count())); ?>
                                </div>

                                <div class="featured-products__price"><?php echo wp_kses_post($product->get_price_html()); ?></div>
                            </div>
                        </article>
                    </li>
                <?php endforeach; ?>
                <?php wp_reset_postdata(); ?>
            </ul>
        </div>
    </div>
</section>
