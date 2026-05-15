<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

// Theme setup: supports, menus and image sizes.
function shop_theme_setup(): void
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('woocommerce');
    add_theme_support('custom-logo', [
        'height'      => 48,
        'width'       => 48,
        'flex-height' => true,
        'flex-width'  => true,
    ]);

    register_nav_menus([
        'primary' => __('Primary Menu', 'shop-theme'),
        'footer'  => __('Footer Menu', 'shop-theme'),
    ]);

    add_image_size('shop-theme-product-card', 600, 760, true);
}
add_action('after_setup_theme', 'shop_theme_setup');

// Resolve compiled asset metadata (url + version) from Vite manifest with fallback.
function shop_theme_asset_meta(string $file): array
{
    $theme_uri = get_template_directory_uri();
    $theme_dir = get_template_directory();
    $manifest_candidates = [
        $theme_dir . '/public/build/manifest.json',
        $theme_dir . '/public/build/.vite/manifest.json',
    ];

    foreach ($manifest_candidates as $manifest_path) {
        if (!file_exists($manifest_path)) {
            continue;
        }

        $manifest = json_decode((string) file_get_contents($manifest_path), true);

        if (is_array($manifest) && isset($manifest[$file]['file'])) {
            $built_file = ltrim((string) $manifest[$file]['file'], '/');
            $asset_path = $theme_dir . '/public/build/' . $built_file;

            return [
                'url'     => $theme_uri . '/public/build/' . $built_file,
                'version' => file_exists($asset_path) ? (string) filemtime($asset_path) : null,
            ];
        }
    }

    $fallback_path = $theme_dir . '/public/build/' . $file;

    return [
        'url'     => $theme_uri . '/public/build/' . $file,
        'version' => file_exists($fallback_path) ? (string) filemtime($fallback_path) : null,
    ];
}

// Enqueue main CSS and JS app bundle built by Vite.
function shop_theme_enqueue_assets(): void
{
    $main_css = shop_theme_asset_meta('main.css');
    $app_js = shop_theme_asset_meta('app.js');

    wp_enqueue_style(
        'shop-theme-main',
        (string) $main_css['url'],
        [],
        $main_css['version']
    );

    wp_enqueue_script(
        'shop-theme-app',
        (string) $app_js['url'],
        [],
        $app_js['version'],
        true
    );
}
add_action('wp_enqueue_scripts', 'shop_theme_enqueue_assets');

// Register sidebar used by WooCommerce archive/single templates.
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

// WooCommerce: set products per page in shop loop.
function shop_theme_products_per_page(int $cols): int
{
    return 12;
}
add_filter('loop_shop_per_page', 'shop_theme_products_per_page', 20);

// WooCommerce: set number of columns in shop loop.
function shop_theme_loop_columns(): int
{
    return 3;
}
add_filter('loop_shop_columns', 'shop_theme_loop_columns');

// WooCommerce: control related products layout.
function shop_theme_related_products_args(array $args): array
{
    $args['posts_per_page'] = 4;
    $args['columns'] = 4;

    return $args;
}
add_filter('woocommerce_output_related_products_args', 'shop_theme_related_products_args');

// WooCommerce: refresh cart icon fragment after AJAX add-to-cart.
function shop_theme_cart_link_fragment(array $fragments): array
{
    ob_start();
    ?>
    <a class="site-header__icon-link site-header__icon-link--cart" href="<?php echo esc_url(wc_get_cart_url()); ?>" aria-label="<?php esc_attr_e('Cart', 'shop-theme'); ?>">
        <svg class="site-header__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M3 5h2l2.2 10.4a1 1 0 0 0 1 .8h8.8a1 1 0 0 0 1-.8L20 8H7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="10" cy="20" r="1.4" fill="currentColor"/>
            <circle cx="17" cy="20" r="1.4" fill="currentColor"/>
        </svg>
        <span class="site-header__cart-count"><?php echo esc_html((string) WC()->cart->get_cart_contents_count()); ?></span>
    </a>
    <?php
    $fragments['a.site-header__icon-link--cart'] = (string) ob_get_clean();

    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'shop_theme_cart_link_fragment');

remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
add_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 5);
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);

// Sanitize plain text input.
function shop_theme_sanitize_text(string $value): string
{
    return sanitize_text_field($value);
}

// Sanitize Hero text allowing only <br>.
function shop_theme_sanitize_hero_html(string $value): string
{
    return wp_kses($value, [
        'br' => [],
    ]);
}

// Sanitize URL input.
function shop_theme_sanitize_url(string $value): string
{
    return esc_url_raw($value);
}

// Sanitize color picker HEX value with fallback.
function shop_theme_sanitize_hex_color(string $value): string
{
    $sanitized = sanitize_hex_color($value);
    return $sanitized ?: '#ececf0';
}

// Sanitize generic font size.
function shop_theme_sanitize_font_size(int $value): int
{
    $value = absint($value);
    if ($value < 12) {
        return 12;
    }
    if ($value > 120) {
        return 120;
    }
    return $value;
}

// Sanitize logo size control.
function shop_theme_sanitize_logo_width(int $value): int
{
    $value = absint($value);
    if ($value < 16) {
        return 16;
    }
    if ($value > 240) {
        return 240;
    }
    return $value;
}

// Sanitize checkbox controls to boolean.
function shop_theme_sanitize_checkbox($value): bool
{
    return (bool) $value;
}

// Sanitize ranged integer controls.
function shop_theme_sanitize_range_int(int $value, int $min, int $max): int
{
    $value = absint($value);
    if ($value < $min) {
        return $min;
    }
    if ($value > $max) {
        return $max;
    }
    return $value;
}

// Render custom modern rating component used across WooCommerce views.
function shop_theme_render_product_rating(?WC_Product $product = null): string
{
    if (!$product instanceof WC_Product) {
        return '';
    }

    $average_rating = (float) $product->get_average_rating();
    $review_count = (int) $product->get_review_count();
    $fill_percent = max(0, min(100, ($average_rating / 5) * 100));

    $label = sprintf(
        /* translators: 1: average rating value */
        __('Rated %s out of 5', 'shop-theme'),
        number_format_i18n($average_rating, 1)
    );

    ob_start();
    ?>
    <div class="product-rating" aria-label="<?php echo esc_attr($label); ?>">
        <span class="product-rating__stars" aria-hidden="true">
            <span class="product-rating__star product-rating--empty">★★★★★</span>
            <span class="product-rating__star product-rating--filled" style="width: <?php echo esc_attr((string) $fill_percent); ?>%;">★★★★★</span>
        </span>
        <span class="product-rating__count">(<?php echo esc_html((string) $review_count); ?>)</span>
    </div>
    <?php
    return (string) ob_get_clean();
}

// Replace WooCommerce default rating HTML with custom BEM component.
function shop_theme_filter_product_rating_html(string $html, float $rating, int $count): string
{
    global $product;
    if (!$product instanceof WC_Product) {
        return $html;
    }

    return shop_theme_render_product_rating($product);
}
add_filter('woocommerce_product_get_rating_html', 'shop_theme_filter_product_rating_html', 10, 3);

// Register all Customizer controls for navigation, hero and categories.
function shop_theme_customize_register(WP_Customize_Manager $wp_customize): void
{
    // Navigation section settings.
    $wp_customize->add_section('shop_theme_navigation', [
        'title'       => __('Navigation', 'shop-theme'),
        'priority'    => 25,
        'description' => __('Header and navigation settings.', 'shop-theme'),
    ]);

    $wp_customize->add_setting('shop_theme_nav_bg_color', [
        'default'           => '#ffffff',
        'sanitize_callback' => 'shop_theme_sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'shop_theme_nav_bg_color', [
        'label'    => __('Navigation background color', 'shop-theme'),
        'section'  => 'shop_theme_navigation',
        'settings' => 'shop_theme_nav_bg_color',
    ]));

    $wp_customize->add_setting('shop_theme_logo_width', [
        'default'           => 32,
        'sanitize_callback' => 'shop_theme_sanitize_logo_width',
    ]);
    $wp_customize->add_control('shop_theme_logo_width', [
        'type'        => 'number',
        'label'       => __('Logo width (px)', 'shop-theme'),
        'description' => __('Controls width and height of logo in header.', 'shop-theme'),
        'section'     => 'shop_theme_navigation',
        'active_callback' => static function (): bool {
            return has_custom_logo();
        },
        'input_attrs' => [
            'min'  => 16,
            'max'  => 240,
            'step' => 1,
        ],
    ]);

    $wp_customize->add_setting('shop_theme_nav_sticky', [
        'default'           => false,
        'sanitize_callback' => 'shop_theme_sanitize_checkbox',
    ]);
    $wp_customize->add_control('shop_theme_nav_sticky', [
        'type'    => 'checkbox',
        'label'   => __('Sticky navigation', 'shop-theme'),
        'section' => 'shop_theme_navigation',
    ]);

    $wp_customize->add_setting('shop_theme_show_search_icon', [
        'default'           => true,
        'sanitize_callback' => 'shop_theme_sanitize_checkbox',
    ]);
    $wp_customize->add_control('shop_theme_show_search_icon', [
        'type'    => 'checkbox',
        'label'   => __('Show search icon', 'shop-theme'),
        'section' => 'shop_theme_navigation',
    ]);

    $wp_customize->add_setting('shop_theme_show_favorites_icon', [
        'default'           => true,
        'sanitize_callback' => 'shop_theme_sanitize_checkbox',
    ]);
    $wp_customize->add_control('shop_theme_show_favorites_icon', [
        'type'    => 'checkbox',
        'label'   => __('Show favorites icon', 'shop-theme'),
        'section' => 'shop_theme_navigation',
    ]);

    $wp_customize->add_setting('shop_theme_favorites_url', [
        'default'           => '',
        'sanitize_callback' => 'shop_theme_sanitize_url',
    ]);
    $wp_customize->add_control('shop_theme_favorites_url', [
        'type'    => 'url',
        'label'   => __('Favorites URL', 'shop-theme'),
        'section' => 'shop_theme_navigation',
    ]);

    $wp_customize->add_setting('shop_theme_show_account_icon', [
        'default'           => true,
        'sanitize_callback' => 'shop_theme_sanitize_checkbox',
    ]);
    $wp_customize->add_control('shop_theme_show_account_icon', [
        'type'    => 'checkbox',
        'label'   => __('Show account icon', 'shop-theme'),
        'section' => 'shop_theme_navigation',
    ]);

    // Hero section settings.
    $wp_customize->add_section('shop_theme_hero', [
        'title'       => __('Hero Section', 'shop-theme'),
        'priority'    => 30,
        'description' => __('Hero content for the front page.', 'shop-theme'),
    ]);

    $wp_customize->add_setting('shop_theme_hero_image', [
        'default'           => '',
        'sanitize_callback' => 'shop_theme_sanitize_url',
    ]);

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'shop_theme_hero_image', [
        'label'    => __('Hero image', 'shop-theme'),
        'section'  => 'shop_theme_hero',
        'settings' => 'shop_theme_hero_image',
    ]));

    $wp_customize->add_setting('shop_theme_hero_bg_color', [
        'default'           => '#ececf0',
        'sanitize_callback' => 'shop_theme_sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'shop_theme_hero_bg_color', [
        'label'    => __('Hero background color', 'shop-theme'),
        'section'  => 'shop_theme_hero',
        'settings' => 'shop_theme_hero_bg_color',
    ]));

    $wp_customize->add_setting('shop_theme_hero_title', [
        'default'           => __('Discover Your Perfect Style', 'shop-theme'),
        'sanitize_callback' => 'shop_theme_sanitize_hero_html',
    ]);
    $wp_customize->add_control('shop_theme_hero_title', [
        'type'    => 'text',
        'label'   => __('Hero title', 'shop-theme'),
        'section' => 'shop_theme_hero',
    ]);

    $wp_customize->add_setting('shop_theme_hero_title_size', [
        'default'           => 64,
        'sanitize_callback' => 'shop_theme_sanitize_font_size',
    ]);
    $wp_customize->add_control('shop_theme_hero_title_size', [
        'type'        => 'number',
        'label'       => __('Hero title font size (px)', 'shop-theme'),
        'description' => __('Enter exact value in pixels, e.g. 64.', 'shop-theme'),
        'section'     => 'shop_theme_hero',
        'input_attrs' => [
            'min'  => 24,
            'max'  => 100,
            'step' => 1,
        ],
    ]);

    $wp_customize->add_setting('shop_theme_hero_subtitle', [
        'default'           => __('Shop the latest trends in fashion, accessories, and lifestyle products. Quality guaranteed with free shipping on orders over $50.', 'shop-theme'),
        'sanitize_callback' => 'shop_theme_sanitize_hero_html',
    ]);
    $wp_customize->add_control('shop_theme_hero_subtitle', [
        'type'    => 'textarea',
        'label'   => __('Hero subtitle', 'shop-theme'),
        'section' => 'shop_theme_hero',
    ]);

    $wp_customize->add_setting('shop_theme_hero_subtitle_size', [
        'default'           => 32,
        'sanitize_callback' => 'shop_theme_sanitize_font_size',
    ]);
    $wp_customize->add_control('shop_theme_hero_subtitle_size', [
        'type'        => 'number',
        'label'       => __('Hero subtitle font size (px)', 'shop-theme'),
        'description' => __('Enter exact value in pixels, e.g. 32.', 'shop-theme'),
        'section'     => 'shop_theme_hero',
        'input_attrs' => [
            'min'  => 14,
            'max'  => 64,
            'step' => 1,
        ],
    ]);

    $wp_customize->add_setting('shop_theme_hero_primary_text', [
        'default'           => __('Shop Now', 'shop-theme'),
        'sanitize_callback' => 'shop_theme_sanitize_text',
    ]);
    $wp_customize->add_control('shop_theme_hero_primary_text', [
        'type'    => 'text',
        'label'   => __('Primary button text', 'shop-theme'),
        'section' => 'shop_theme_hero',
    ]);

    $wp_customize->add_setting('shop_theme_hero_primary_url', [
        'default'           => '',
        'sanitize_callback' => 'shop_theme_sanitize_url',
    ]);
    $wp_customize->add_control('shop_theme_hero_primary_url', [
        'type'    => 'url',
        'label'   => __('Primary button URL', 'shop-theme'),
        'section' => 'shop_theme_hero',
    ]);

    $wp_customize->add_setting('shop_theme_hero_secondary_text', [
        'default'           => __('View Sale', 'shop-theme'),
        'sanitize_callback' => 'shop_theme_sanitize_text',
    ]);
    $wp_customize->add_control('shop_theme_hero_secondary_text', [
        'type'    => 'text',
        'label'   => __('Secondary button text', 'shop-theme'),
        'section' => 'shop_theme_hero',
    ]);

    $wp_customize->add_setting('shop_theme_hero_secondary_url', [
        'default'           => '',
        'sanitize_callback' => 'shop_theme_sanitize_url',
    ]);
    $wp_customize->add_control('shop_theme_hero_secondary_url', [
        'type'    => 'url',
        'label'   => __('Secondary button URL', 'shop-theme'),
        'section' => 'shop_theme_hero',
    ]);

    // Product categories section settings.
    $wp_customize->add_section('shop_theme_product_categories', [
        'title'       => __('Product Categories Section', 'shop-theme'),
        'priority'    => 35,
        'description' => __('Settings for Shop by Category section.', 'shop-theme'),
    ]);

    $wp_customize->add_setting('shop_theme_category_section_title', [
        'default'           => __('Shop by Category', 'shop-theme'),
        'sanitize_callback' => 'shop_theme_sanitize_text',
    ]);
    $wp_customize->add_control('shop_theme_category_section_title', [
        'type'    => 'text',
        'label'   => __('Category section title', 'shop-theme'),
        'section' => 'shop_theme_product_categories',
    ]);

    $wp_customize->add_setting('shop_theme_category_section_subtitle', [
        'default'           => __('Browse our curated collections and find exactly what you\'re looking for', 'shop-theme'),
        'sanitize_callback' => 'shop_theme_sanitize_text',
    ]);
    $wp_customize->add_control('shop_theme_category_section_subtitle', [
        'type'    => 'text',
        'label'   => __('Category section subtitle', 'shop-theme'),
        'section' => 'shop_theme_product_categories',
    ]);

    $wp_customize->add_setting('shop_theme_category_title_size_desktop', [
        'default'           => 48,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 20, 120),
    ]);
    $wp_customize->add_control('shop_theme_category_title_size_desktop', [
        'type'        => 'number',
        'label'       => __('Title font size desktop (px)', 'shop-theme'),
        'section'     => 'shop_theme_product_categories',
        'input_attrs' => ['min' => 20, 'max' => 120, 'step' => 1],
    ]);

    $wp_customize->add_setting('shop_theme_category_title_size_mobile', [
        'default'           => 32,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 16, 80),
    ]);
    $wp_customize->add_control('shop_theme_category_title_size_mobile', [
        'type'        => 'number',
        'label'       => __('Title font size mobile (px)', 'shop-theme'),
        'section'     => 'shop_theme_product_categories',
        'input_attrs' => ['min' => 16, 'max' => 80, 'step' => 1],
    ]);

    $wp_customize->add_setting('shop_theme_category_subtitle_size_desktop', [
        'default'           => 20,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 12, 60),
    ]);
    $wp_customize->add_control('shop_theme_category_subtitle_size_desktop', [
        'type'        => 'number',
        'label'       => __('Subtitle font size desktop (px)', 'shop-theme'),
        'section'     => 'shop_theme_product_categories',
        'input_attrs' => ['min' => 12, 'max' => 60, 'step' => 1],
    ]);

    $wp_customize->add_setting('shop_theme_category_subtitle_size_mobile', [
        'default'           => 16,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 10, 48),
    ]);
    $wp_customize->add_control('shop_theme_category_subtitle_size_mobile', [
        'type'        => 'number',
        'label'       => __('Subtitle font size mobile (px)', 'shop-theme'),
        'section'     => 'shop_theme_product_categories',
        'input_attrs' => ['min' => 10, 'max' => 48, 'step' => 1],
    ]);

    $wp_customize->add_setting('shop_theme_category_max_count', [
        'default'           => 8,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 1, 30),
    ]);
    $wp_customize->add_control('shop_theme_category_max_count', [
        'type'        => 'number',
        'label'       => __('Max number of categories to display', 'shop-theme'),
        'section'     => 'shop_theme_product_categories',
        'input_attrs' => ['min' => 1, 'max' => 30, 'step' => 1],
    ]);

    $wp_customize->add_setting('shop_theme_category_show_empty', [
        'default'           => false,
        'sanitize_callback' => 'shop_theme_sanitize_checkbox',
    ]);
    $wp_customize->add_control('shop_theme_category_show_empty', [
        'type'    => 'checkbox',
        'label'   => __('Show empty categories', 'shop-theme'),
        'section' => 'shop_theme_product_categories',
    ]);

    $wp_customize->add_setting('shop_theme_category_padding_top', [
        'default'           => 80,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 0, 240),
    ]);
    $wp_customize->add_control('shop_theme_category_padding_top', [
        'type'        => 'number',
        'label'       => __('Section top padding (px)', 'shop-theme'),
        'section'     => 'shop_theme_product_categories',
        'input_attrs' => ['min' => 0, 'max' => 240, 'step' => 1],
    ]);

    $wp_customize->add_setting('shop_theme_category_padding_bottom', [
        'default'           => 80,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 0, 240),
    ]);
    $wp_customize->add_control('shop_theme_category_padding_bottom', [
        'type'        => 'number',
        'label'       => __('Section bottom padding (px)', 'shop-theme'),
        'section'     => 'shop_theme_product_categories',
        'input_attrs' => ['min' => 0, 'max' => 240, 'step' => 1],
    ]);

    // Featured products section settings.
    $wp_customize->add_section('shop_theme_featured_products', [
        'title'       => __('Featured Products Section', 'shop-theme'),
        'priority'    => 36,
        'description' => __('Settings for Featured Products section.', 'shop-theme'),
    ]);

    $wp_customize->add_setting('shop_theme_featured_section_title', [
        'default'           => __('Featured Products', 'shop-theme'),
        'sanitize_callback' => 'shop_theme_sanitize_text',
    ]);
    $wp_customize->add_control('shop_theme_featured_section_title', [
        'type'    => 'text',
        'label'   => __('Featured section title', 'shop-theme'),
        'section' => 'shop_theme_featured_products',
    ]);

    $wp_customize->add_setting('shop_theme_featured_section_subtitle', [
        'default'           => __('Handpicked favorites that our customers love', 'shop-theme'),
        'sanitize_callback' => 'shop_theme_sanitize_text',
    ]);
    $wp_customize->add_control('shop_theme_featured_section_subtitle', [
        'type'    => 'text',
        'label'   => __('Featured section subtitle', 'shop-theme'),
        'section' => 'shop_theme_featured_products',
    ]);

    $wp_customize->add_setting('shop_theme_featured_bg_color', [
        'default'           => '#ececf0',
        'sanitize_callback' => 'shop_theme_sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'shop_theme_featured_bg_color', [
        'label'    => __('Featured section background color', 'shop-theme'),
        'section'  => 'shop_theme_featured_products',
        'settings' => 'shop_theme_featured_bg_color',
    ]));

    $wp_customize->add_setting('shop_theme_featured_title_size_desktop', [
        'default'           => 48,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 20, 120),
    ]);
    $wp_customize->add_control('shop_theme_featured_title_size_desktop', [
        'type'        => 'number',
        'label'       => __('Title font size desktop (px)', 'shop-theme'),
        'section'     => 'shop_theme_featured_products',
        'input_attrs' => ['min' => 20, 'max' => 120, 'step' => 1],
    ]);

    $wp_customize->add_setting('shop_theme_featured_title_size_mobile', [
        'default'           => 32,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 16, 80),
    ]);
    $wp_customize->add_control('shop_theme_featured_title_size_mobile', [
        'type'        => 'number',
        'label'       => __('Title font size mobile (px)', 'shop-theme'),
        'section'     => 'shop_theme_featured_products',
        'input_attrs' => ['min' => 16, 'max' => 80, 'step' => 1],
    ]);

    $wp_customize->add_setting('shop_theme_featured_subtitle_size_desktop', [
        'default'           => 20,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 12, 60),
    ]);
    $wp_customize->add_control('shop_theme_featured_subtitle_size_desktop', [
        'type'        => 'number',
        'label'       => __('Subtitle font size desktop (px)', 'shop-theme'),
        'section'     => 'shop_theme_featured_products',
        'input_attrs' => ['min' => 12, 'max' => 60, 'step' => 1],
    ]);

    $wp_customize->add_setting('shop_theme_featured_subtitle_size_mobile', [
        'default'           => 16,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 10, 48),
    ]);
    $wp_customize->add_control('shop_theme_featured_subtitle_size_mobile', [
        'type'        => 'number',
        'label'       => __('Subtitle font size mobile (px)', 'shop-theme'),
        'section'     => 'shop_theme_featured_products',
        'input_attrs' => ['min' => 10, 'max' => 48, 'step' => 1],
    ]);

    $wp_customize->add_setting('shop_theme_featured_max_count', [
        'default'           => 8,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 1, 30),
    ]);
    $wp_customize->add_control('shop_theme_featured_max_count', [
        'type'        => 'number',
        'label'       => __('Max number of products to display', 'shop-theme'),
        'section'     => 'shop_theme_featured_products',
        'input_attrs' => ['min' => 1, 'max' => 30, 'step' => 1],
    ]);

    $wp_customize->add_setting('shop_theme_featured_show_out_of_stock', [
        'default'           => false,
        'sanitize_callback' => 'shop_theme_sanitize_checkbox',
    ]);
    $wp_customize->add_control('shop_theme_featured_show_out_of_stock', [
        'type'    => 'checkbox',
        'label'   => __('Show out of stock products', 'shop-theme'),
        'section' => 'shop_theme_featured_products',
    ]);

    $wp_customize->add_setting('shop_theme_featured_padding_top', [
        'default'           => 80,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 0, 240),
    ]);
    $wp_customize->add_control('shop_theme_featured_padding_top', [
        'type'        => 'number',
        'label'       => __('Section top padding (px)', 'shop-theme'),
        'section'     => 'shop_theme_featured_products',
        'input_attrs' => ['min' => 0, 'max' => 240, 'step' => 1],
    ]);

    $wp_customize->add_setting('shop_theme_featured_padding_bottom', [
        'default'           => 80,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 0, 240),
    ]);
    $wp_customize->add_control('shop_theme_featured_padding_bottom', [
        'type'        => 'number',
        'label'       => __('Section bottom padding (px)', 'shop-theme'),
        'section'     => 'shop_theme_featured_products',
        'input_attrs' => ['min' => 0, 'max' => 240, 'step' => 1],
    ]);

    // Promo banner section settings.
    $wp_customize->add_section('shop_theme_promo_banner', [
        'title'       => __('Promo Banner Section', 'shop-theme'),
        'priority'    => 37,
        'description' => __('Settings for promo/banner section.', 'shop-theme'),
    ]);

    $wp_customize->add_setting('shop_theme_promo_title', [
        'default'           => __('Summer Sale', 'shop-theme'),
        'sanitize_callback' => 'shop_theme_sanitize_text',
    ]);
    $wp_customize->add_control('shop_theme_promo_title', [
        'type'    => 'text',
        'label'   => __('Banner title', 'shop-theme'),
        'section' => 'shop_theme_promo_banner',
    ]);

    $wp_customize->add_setting('shop_theme_promo_subtitle', [
        'default'           => __('Up to 50% off on selected items. Limited time offer!', 'shop-theme'),
        'sanitize_callback' => 'shop_theme_sanitize_text',
    ]);
    $wp_customize->add_control('shop_theme_promo_subtitle', [
        'type'    => 'text',
        'label'   => __('Banner subtitle', 'shop-theme'),
        'section' => 'shop_theme_promo_banner',
    ]);

    $wp_customize->add_setting('shop_theme_promo_button_text', [
        'default'           => __('Shop Sale', 'shop-theme'),
        'sanitize_callback' => 'shop_theme_sanitize_text',
    ]);
    $wp_customize->add_control('shop_theme_promo_button_text', [
        'type'    => 'text',
        'label'   => __('Button text', 'shop-theme'),
        'section' => 'shop_theme_promo_banner',
    ]);

    $wp_customize->add_setting('shop_theme_promo_button_url', [
        'default'           => '',
        'sanitize_callback' => 'shop_theme_sanitize_url',
    ]);
    $wp_customize->add_control('shop_theme_promo_button_url', [
        'type'    => 'url',
        'label'   => __('Button URL', 'shop-theme'),
        'section' => 'shop_theme_promo_banner',
    ]);

    $wp_customize->add_setting('shop_theme_promo_button_text_size', [
        'default'           => 30,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 12, 48),
    ]);
    $wp_customize->add_control('shop_theme_promo_button_text_size', [
        'type'        => 'number',
        'label'       => __('Button text size (px)', 'shop-theme'),
        'section'     => 'shop_theme_promo_banner',
        'input_attrs' => ['min' => 12, 'max' => 48, 'step' => 1],
    ]);

    $wp_customize->add_setting('shop_theme_promo_bg_image', [
        'default'           => '',
        'sanitize_callback' => 'shop_theme_sanitize_url',
    ]);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'shop_theme_promo_bg_image', [
        'label'    => __('Banner background image', 'shop-theme'),
        'section'  => 'shop_theme_promo_banner',
        'settings' => 'shop_theme_promo_bg_image',
    ]));

    $wp_customize->add_setting('shop_theme_promo_overlay_color', [
        'default'           => '#0d1028',
        'sanitize_callback' => 'shop_theme_sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'shop_theme_promo_overlay_color', [
        'label'    => __('Overlay color', 'shop-theme'),
        'section'  => 'shop_theme_promo_banner',
        'settings' => 'shop_theme_promo_overlay_color',
    ]));

    $wp_customize->add_setting('shop_theme_promo_overlay_opacity', [
        'default'           => 58,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 0, 100),
    ]);
    $wp_customize->add_control('shop_theme_promo_overlay_opacity', [
        'type'        => 'number',
        'label'       => __('Overlay opacity (0-100)', 'shop-theme'),
        'section'     => 'shop_theme_promo_banner',
        'input_attrs' => ['min' => 0, 'max' => 100, 'step' => 1],
    ]);

    $wp_customize->add_setting('shop_theme_promo_title_size', [
        'default'           => 56,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 24, 100),
    ]);
    $wp_customize->add_control('shop_theme_promo_title_size', [
        'type'        => 'number',
        'label'       => __('Title size (px)', 'shop-theme'),
        'section'     => 'shop_theme_promo_banner',
        'input_attrs' => ['min' => 24, 'max' => 100, 'step' => 1],
    ]);

    $wp_customize->add_setting('shop_theme_promo_subtitle_size', [
        'default'           => 36,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 14, 64),
    ]);
    $wp_customize->add_control('shop_theme_promo_subtitle_size', [
        'type'        => 'number',
        'label'       => __('Subtitle size (px)', 'shop-theme'),
        'section'     => 'shop_theme_promo_banner',
        'input_attrs' => ['min' => 14, 'max' => 64, 'step' => 1],
    ]);

    $wp_customize->add_setting('shop_theme_promo_radius', [
        'default'           => 18,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 0, 48),
    ]);
    $wp_customize->add_control('shop_theme_promo_radius', [
        'type'        => 'number',
        'label'       => __('Banner border radius (px)', 'shop-theme'),
        'section'     => 'shop_theme_promo_banner',
        'input_attrs' => ['min' => 0, 'max' => 48, 'step' => 1],
    ]);

    $wp_customize->add_setting('shop_theme_promo_padding_top', [
        'default'           => 64,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 0, 240),
    ]);
    $wp_customize->add_control('shop_theme_promo_padding_top', [
        'type'        => 'number',
        'label'       => __('Section top padding (px)', 'shop-theme'),
        'section'     => 'shop_theme_promo_banner',
        'input_attrs' => ['min' => 0, 'max' => 240, 'step' => 1],
    ]);

    $wp_customize->add_setting('shop_theme_promo_padding_bottom', [
        'default'           => 64,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 0, 240),
    ]);
    $wp_customize->add_control('shop_theme_promo_padding_bottom', [
        'type'        => 'number',
        'label'       => __('Section bottom padding (px)', 'shop-theme'),
        'section'     => 'shop_theme_promo_banner',
        'input_attrs' => ['min' => 0, 'max' => 240, 'step' => 1],
    ]);

    // Showcase tabs section settings.
    $wp_customize->add_section('shop_theme_showcase_tabs', [
        'title'       => __('Showcase Tabs Section', 'shop-theme'),
        'priority'    => 38,
        'description' => __('Settings for tabs products section.', 'shop-theme'),
    ]);

    $wp_customize->add_setting('shop_theme_showcase_tab_new_label', [
        'default'           => __('New Arrivals', 'shop-theme'),
        'sanitize_callback' => 'shop_theme_sanitize_text',
    ]);
    $wp_customize->add_control('shop_theme_showcase_tab_new_label', [
        'type'    => 'text',
        'label'   => __('New tab label', 'shop-theme'),
        'section' => 'shop_theme_showcase_tabs',
    ]);

    $wp_customize->add_setting('shop_theme_showcase_tab_popular_label', [
        'default'           => __('Popular', 'shop-theme'),
        'sanitize_callback' => 'shop_theme_sanitize_text',
    ]);
    $wp_customize->add_control('shop_theme_showcase_tab_popular_label', [
        'type'    => 'text',
        'label'   => __('Popular tab label', 'shop-theme'),
        'section' => 'shop_theme_showcase_tabs',
    ]);

    $wp_customize->add_setting('shop_theme_showcase_tab_sale_label', [
        'default'           => __('On Sale', 'shop-theme'),
        'sanitize_callback' => 'shop_theme_sanitize_text',
    ]);
    $wp_customize->add_control('shop_theme_showcase_tab_sale_label', [
        'type'    => 'text',
        'label'   => __('Sale tab label', 'shop-theme'),
        'section' => 'shop_theme_showcase_tabs',
    ]);

    $wp_customize->add_setting('shop_theme_showcase_bg_color', [
        'default'           => '#ececf0',
        'sanitize_callback' => 'shop_theme_sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'shop_theme_showcase_bg_color', [
        'label'    => __('Section background color', 'shop-theme'),
        'section'  => 'shop_theme_showcase_tabs',
        'settings' => 'shop_theme_showcase_bg_color',
    ]));

    $wp_customize->add_setting('shop_theme_showcase_max_items', [
        'default'           => 8,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 1, 30),
    ]);
    $wp_customize->add_control('shop_theme_showcase_max_items', [
        'type'        => 'number',
        'label'       => __('Max products per tab', 'shop-theme'),
        'section'     => 'shop_theme_showcase_tabs',
        'input_attrs' => ['min' => 1, 'max' => 30, 'step' => 1],
    ]);

    $wp_customize->add_setting('shop_theme_showcase_padding_top', [
        'default'           => 80,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 0, 240),
    ]);
    $wp_customize->add_control('shop_theme_showcase_padding_top', [
        'type'        => 'number',
        'label'       => __('Section top padding (px)', 'shop-theme'),
        'section'     => 'shop_theme_showcase_tabs',
        'input_attrs' => ['min' => 0, 'max' => 240, 'step' => 1],
    ]);

    $wp_customize->add_setting('shop_theme_showcase_padding_bottom', [
        'default'           => 80,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 0, 240),
    ]);
    $wp_customize->add_control('shop_theme_showcase_padding_bottom', [
        'type'        => 'number',
        'label'       => __('Section bottom padding (px)', 'shop-theme'),
        'section'     => 'shop_theme_showcase_tabs',
        'input_attrs' => ['min' => 0, 'max' => 240, 'step' => 1],
    ]);

    // Benefits strip section settings.
    $wp_customize->add_section('shop_theme_benefits_strip', [
        'title'       => __('Benefits Strip Section', 'shop-theme'),
        'priority'    => 39,
        'description' => __('Settings for benefits/icons strip.', 'shop-theme'),
    ]);

    $wp_customize->add_setting('shop_theme_benefits_bg_color', [
        'default'           => '#ececf0',
        'sanitize_callback' => 'shop_theme_sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'shop_theme_benefits_bg_color', [
        'label'    => __('Section background color', 'shop-theme'),
        'section'  => 'shop_theme_benefits_strip',
        'settings' => 'shop_theme_benefits_bg_color',
    ]));

    $wp_customize->add_setting('shop_theme_benefits_padding_top', [
        'default'           => 56,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 0, 240),
    ]);
    $wp_customize->add_control('shop_theme_benefits_padding_top', [
        'type'        => 'number',
        'label'       => __('Section top padding (px)', 'shop-theme'),
        'section'     => 'shop_theme_benefits_strip',
        'input_attrs' => ['min' => 0, 'max' => 240, 'step' => 1],
    ]);

    $wp_customize->add_setting('shop_theme_benefits_padding_bottom', [
        'default'           => 56,
        'sanitize_callback' => static fn(int $value): int => shop_theme_sanitize_range_int($value, 0, 240),
    ]);
    $wp_customize->add_control('shop_theme_benefits_padding_bottom', [
        'type'        => 'number',
        'label'       => __('Section bottom padding (px)', 'shop-theme'),
        'section'     => 'shop_theme_benefits_strip',
        'input_attrs' => ['min' => 0, 'max' => 240, 'step' => 1],
    ]);

    for ($i = 1; $i <= 4; $i++) {
        $wp_customize->add_setting("shop_theme_benefit_{$i}_title", [
            'default'           => '',
            'sanitize_callback' => 'shop_theme_sanitize_text',
        ]);
        $wp_customize->add_control("shop_theme_benefit_{$i}_title", [
            'type'    => 'text',
            'label'   => sprintf(__('Benefit %d title', 'shop-theme'), $i),
            'section' => 'shop_theme_benefits_strip',
        ]);

        $wp_customize->add_setting("shop_theme_benefit_{$i}_text", [
            'default'           => '',
            'sanitize_callback' => 'shop_theme_sanitize_text',
        ]);
        $wp_customize->add_control("shop_theme_benefit_{$i}_text", [
            'type'    => 'text',
            'label'   => sprintf(__('Benefit %d text', 'shop-theme'), $i),
            'section' => 'shop_theme_benefits_strip',
        ]);

        $wp_customize->add_setting("shop_theme_benefit_{$i}_icon", [
            'default'           => '',
            'sanitize_callback' => 'shop_theme_sanitize_url',
        ]);
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "shop_theme_benefit_{$i}_icon", [
            'label'    => sprintf(__('Benefit %d custom icon', 'shop-theme'), $i),
            'section'  => 'shop_theme_benefits_strip',
            'settings' => "shop_theme_benefit_{$i}_icon",
        ]));
    }
}
add_action('customize_register', 'shop_theme_customize_register');

// Return allowed front-page section slugs used by template-parts/sections.
function shop_theme_get_front_page_sections(): array
{
    return [
        'hero'               => __('Hero', 'shop-theme'),
        'featured-products'  => __('Featured Products', 'shop-theme'),
        'product-categories' => __('Product Categories', 'shop-theme'),
        'promo-banner'       => __('Promo Banner', 'shop-theme'),
        'product-showcase-tabs' => __('Showcase Tabs', 'shop-theme'),
        'benefits-strip'     => __('Benefits Strip', 'shop-theme'),
    ];
}

// Register front-page section order metabox in page editor.
function shop_theme_add_front_page_sections_metabox(): void
{
    add_meta_box(
        'shop-theme-front-page-sections',
        __('Front Page Sections Order', 'shop-theme'),
        'shop_theme_render_front_page_sections_metabox',
        'page',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'shop_theme_add_front_page_sections_metabox');

// Render numeric order inputs for each available front-page section.
function shop_theme_render_front_page_sections_metabox(WP_Post $post): void
{
    if ($post->post_name !== 'home' && get_page_template_slug($post->ID) !== 'front-page.php') {
        echo '<p>' . esc_html__('Use this on your front page. Save once after assigning this page as homepage.', 'shop-theme') . '</p>';
    }

    wp_nonce_field('shop_theme_save_front_page_sections_order', 'shop_theme_front_page_sections_nonce');

    $sections = shop_theme_get_front_page_sections();
    $saved_order = get_post_meta($post->ID, '_shop_theme_front_page_sections_order', true);
    if (!is_array($saved_order)) {
        $saved_order = array_keys($sections);
    }

    $position_map = [];
    foreach (array_values($saved_order) as $index => $slug) {
        $position_map[$slug] = $index + 1;
    }

    echo '<p>' . esc_html__('Set section position (1 = first).', 'shop-theme') . '</p>';

    foreach ($sections as $slug => $label) {
        $value = isset($position_map[$slug]) ? (int) $position_map[$slug] : 99;
        echo '<p>';
        echo '<label for="shop-theme-section-order-' . esc_attr($slug) . '">' . esc_html($label) . '</label><br>';
        echo '<input id="shop-theme-section-order-' . esc_attr($slug) . '" name="shop_theme_section_order[' . esc_attr($slug) . ']" type="number" min="1" step="1" value="' . esc_attr((string) $value) . '" style="width: 100%;">';
        echo '</p>';
    }
}

// Save section order from page editor and persist it in post meta.
function shop_theme_save_front_page_sections_order(int $post_id): void
{
    if (!isset($_POST['shop_theme_front_page_sections_nonce'])) {
        return;
    }

    if (!wp_verify_nonce((string) $_POST['shop_theme_front_page_sections_nonce'], 'shop_theme_save_front_page_sections_order')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_page', $post_id)) {
        return;
    }

    if (!isset($_POST['shop_theme_section_order']) || !is_array($_POST['shop_theme_section_order'])) {
        return;
    }

    $sections = shop_theme_get_front_page_sections();
    $raw = $_POST['shop_theme_section_order'];
    $sortable = [];

    foreach ($sections as $slug => $label) {
        $position = isset($raw[$slug]) ? absint((string) $raw[$slug]) : 999;
        if ($position < 1) {
            $position = 999;
        }
        $sortable[] = [
            'slug'     => $slug,
            'position' => $position,
        ];
    }

    usort($sortable, static function (array $a, array $b): int {
        if ($a['position'] === $b['position']) {
            return strcmp((string) $a['slug'], (string) $b['slug']);
        }
        return $a['position'] <=> $b['position'];
    });

    $ordered_slugs = array_map(static fn(array $item): string => (string) $item['slug'], $sortable);
    update_post_meta($post_id, '_shop_theme_front_page_sections_order', $ordered_slugs);
}
add_action('save_post_page', 'shop_theme_save_front_page_sections_order');
