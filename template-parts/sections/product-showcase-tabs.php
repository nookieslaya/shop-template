<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$tab_new_label = trim((string) get_theme_mod('shop_theme_showcase_tab_new_label', __('New Arrivals', 'shop-theme')));
$tab_popular_label = trim((string) get_theme_mod('shop_theme_showcase_tab_popular_label', __('Popular', 'shop-theme')));
$tab_sale_label = trim((string) get_theme_mod('shop_theme_showcase_tab_sale_label', __('On Sale', 'shop-theme')));
$bg_color = trim((string) get_theme_mod('shop_theme_showcase_bg_color', '#ececf0'));
$max_items = (int) get_theme_mod('shop_theme_showcase_max_items', 8);
$padding_top = (int) get_theme_mod('shop_theme_showcase_padding_top', 80);
$padding_bottom = (int) get_theme_mod('shop_theme_showcase_padding_bottom', 80);

$limit = max(1, $max_items);

// New Arrivals: newest products by publish date DESC.
$new_products = wc_get_products([
    'status' => 'publish',
    'limit' => $limit,
    'orderby' => 'date',
    'order' => 'DESC',
    'return' => 'objects',
]);

// Popular: products sorted by total sales DESC.
$popular_query = new WP_Query([
    'post_type'      => 'product',
    'post_status'    => 'publish',
    'posts_per_page' => $limit,
    'orderby'        => 'meta_value_num',
    'meta_key'       => 'total_sales',
    'order'          => 'DESC',
    'fields'         => 'ids',
]);

$popular_ids = array_map('intval', $popular_query->posts);
$popular_products = [];
if (!empty($popular_ids)) {
    foreach ($popular_ids as $product_id) {
        $product = wc_get_product($product_id);
        if ($product instanceof WC_Product) {
            $popular_products[] = $product;
        }
    }
}
wp_reset_postdata();

// On Sale: products from WooCommerce on-sale IDs helper.
$sale_ids = array_map('intval', wc_get_product_ids_on_sale());
$sale_ids = array_values(array_filter($sale_ids));
$sale_products = [];

if (!empty($sale_ids)) {
    $sale_products = wc_get_products([
        'status'  => 'publish',
        'limit'   => $limit,
        'include' => $sale_ids,
        'orderby' => 'include',
        'return'  => 'objects',
    ]);
}

if (empty($new_products) && empty($popular_products) && empty($sale_products)) {
    return;
}

$favorites_url = trim((string) get_theme_mod('shop_theme_favorites_url', home_url('/favorites/')));
?>
<section class="showcase-tabs section" aria-label="<?php esc_attr_e('Product tabs section', 'shop-theme'); ?>" style="--showcase-bg-color: <?php echo esc_attr($bg_color); ?>; --showcase-padding-top: <?php echo esc_attr((string) max(0, min(240, $padding_top))); ?>px; --showcase-padding-bottom: <?php echo esc_attr((string) max(0, min(240, $padding_bottom))); ?>px;">
    <div class="container">
        <div class="showcase-tabs__tabs" role="tablist" aria-label="<?php esc_attr_e('Product filters', 'shop-theme'); ?>">
            <button id="showcase-tab-new" class="showcase-tabs__tab is-active" type="button" role="tab" aria-controls="showcase-panel-new" aria-selected="true" data-tab="new"><?php echo esc_html($tab_new_label); ?></button>
            <button id="showcase-tab-popular" class="showcase-tabs__tab" type="button" role="tab" aria-controls="showcase-panel-popular" aria-selected="false" data-tab="popular"><?php echo esc_html($tab_popular_label); ?></button>
            <button id="showcase-tab-sale" class="showcase-tabs__tab" type="button" role="tab" aria-controls="showcase-panel-sale" aria-selected="false" data-tab="sale"><?php echo esc_html($tab_sale_label); ?></button>
        </div>

        <?php
        $tabs_data = [
            'new'     => $new_products,
            'popular' => $popular_products,
            'sale'    => $sale_products,
        ];
        ?>
        <?php foreach ($tabs_data as $tab_key => $products_list) : ?>
            <div id="showcase-panel-<?php echo esc_attr($tab_key); ?>" class="showcase-tabs__panel<?php echo $tab_key === 'new' ? ' is-active' : ''; ?>" role="tabpanel" aria-labelledby="showcase-tab-<?php echo esc_attr($tab_key); ?>" data-showcase-panel="<?php echo esc_attr($tab_key); ?>"<?php echo $tab_key === 'new' ? '' : ' hidden'; ?>>
                <div class="showcase-tabs__grid" data-showcase-grid>
                    <?php if (empty($products_list)) : ?>
                        <p class="showcase-tabs__empty"><?php esc_html_e('No products found in this tab.', 'shop-theme'); ?></p>
                    <?php endif; ?>
                    <?php foreach ($products_list as $product) : ?>
                        <?php
                        if (!$product instanceof WC_Product) {
                            continue;
                        }

                        $product_link = get_permalink($product->get_id());
                        $image_url = get_the_post_thumbnail_url($product->get_id(), 'large');
                        if (!$image_url) {
                            $image_url = wc_placeholder_img_src('large');
                        }

                        $created_ts = strtotime((string) $product->get_date_created());
                        $is_new_badge = $created_ts && $created_ts >= strtotime('-30 days');
                        $sale_badge = '';
                        if ($product->is_on_sale() && $product->get_regular_price() !== '') {
                            $regular = (float) $product->get_regular_price();
                            $sale = (float) $product->get_sale_price();
                            if ($regular > 0 && $sale > 0 && $sale < $regular) {
                                $discount = (int) round((($regular - $sale) / $regular) * 100);
                                $sale_badge = '-' . $discount . '%';
                            }
                        }
                        ?>
                        <article class="showcase-tabs__card">
                            <div class="showcase-tabs__media">
                                <a href="<?php echo esc_url($product_link); ?>" class="showcase-tabs__image-link">
                                    <img class="showcase-tabs__image" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($product->get_name()); ?>">
                                </a>

                                <?php if ($sale_badge !== '') : ?>
                                    <span class="showcase-tabs__badge showcase-tabs__badge--sale"><?php echo esc_html($sale_badge); ?></span>
                                <?php elseif ($is_new_badge) : ?>
                                    <span class="showcase-tabs__badge showcase-tabs__badge--new"><?php esc_html_e('New', 'shop-theme'); ?></span>
                                <?php endif; ?>

                                <a class="showcase-tabs__favorite" href="<?php echo esc_url($favorites_url !== '' ? $favorites_url : home_url('/favorites/')); ?>" aria-label="<?php esc_attr_e('Add to favorites', 'shop-theme'); ?>">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 20.8l-1.1-1C6.1 15.6 3 12.8 3 9.3A4.3 4.3 0 0 1 7.4 5a4.7 4.7 0 0 1 4.6 2.7A4.7 4.7 0 0 1 16.6 5 4.3 4.3 0 0 1 21 9.3c0 3.5-3.1 6.3-7.9 10.5l-1.1 1Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                                </a>

                                <div class="showcase-tabs__cart-wrap">
                                    <?php
                                    $GLOBALS['product'] = $product;
                                    woocommerce_template_loop_add_to_cart();
                                    ?>
                                </div>
                            </div>

                            <div class="showcase-tabs__content">
                                <h3 class="showcase-tabs__title"><a href="<?php echo esc_url($product_link); ?>"><?php echo esc_html($product->get_name()); ?></a></h3>
                                <div class="showcase-tabs__rating"><?php echo wp_kses_post(shop_theme_render_product_rating($product)); ?></div>
                                <div class="showcase-tabs__price"><?php echo wp_kses_post($product->get_price_html()); ?></div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
