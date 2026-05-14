<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

function shop_theme_setup(): void
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('woocommerce');

    register_nav_menus([
        'primary' => __('Primary Menu', 'shop-theme'),
        'footer'  => __('Footer Menu', 'shop-theme'),
    ]);

    add_image_size('shop-theme-product-card', 600, 760, true);
}
add_action('after_setup_theme', 'shop_theme_setup');

function shop_theme_asset_path(string $file): string
{
    $theme_uri = get_template_directory_uri();
    $manifest_path = get_template_directory() . '/public/build/manifest.json';

    if (file_exists($manifest_path)) {
        $manifest = json_decode((string) file_get_contents($manifest_path), true);

        if (is_array($manifest) && isset($manifest[$file]['file'])) {
            return $theme_uri . '/public/build/' . ltrim($manifest[$file]['file'], '/');
        }
    }

    return $theme_uri . '/public/build/' . $file;
}

function shop_theme_enqueue_assets(): void
{
    wp_enqueue_style(
        'shop-theme-main',
        shop_theme_asset_path('main.css'),
        [],
        null
    );

    wp_enqueue_script(
        'shop-theme-main',
        shop_theme_asset_path('main.js'),
        [],
        null,
        true
    );
}
add_action('wp_enqueue_scripts', 'shop_theme_enqueue_assets');

function shop_theme_register_sidebars(): void
{
    register_sidebar([
        'name'          => __('Shop Sidebar', 'shop-theme'),
        'id'            => 'shop-sidebar',
        'description'   => __('Sidebar for shop archive and product pages.', 'shop-theme'),
        'before_widget' => '<section class="widget widget--shop">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget__title">',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'shop_theme_register_sidebars');

function shop_theme_products_per_page(int $cols): int
{
    return 12;
}
add_filter('loop_shop_per_page', 'shop_theme_products_per_page', 20);

function shop_theme_loop_columns(): int
{
    return 3;
}
add_filter('loop_shop_columns', 'shop_theme_loop_columns');

function shop_theme_related_products_args(array $args): array
{
    $args['posts_per_page'] = 4;
    $args['columns'] = 4;

    return $args;
}
add_filter('woocommerce_output_related_products_args', 'shop_theme_related_products_args');

function shop_theme_cart_link_fragment(array $fragments): array
{
    ob_start();
    ?>
    <a class="site-header__cart-link" href="<?php echo esc_url(wc_get_cart_url()); ?>">
        <?php esc_html_e('Cart', 'shop-theme'); ?>
        <span class="site-header__cart-count"><?php echo esc_html((string) WC()->cart->get_cart_contents_count()); ?></span>
    </a>
    <?php
    $fragments['a.site-header__cart-link'] = (string) ob_get_clean();

    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'shop_theme_cart_link_fragment');

remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
add_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 5);
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
