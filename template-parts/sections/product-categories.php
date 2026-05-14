<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$section_title = trim((string) get_theme_mod('shop_theme_category_section_title', __('Shop by Category', 'shop-theme')));
$section_subtitle = trim((string) get_theme_mod('shop_theme_category_section_subtitle', __('Browse our curated collections and find exactly what you\'re looking for', 'shop-theme')));
$title_size_desktop = (int) get_theme_mod('shop_theme_category_title_size_desktop', 48);
$title_size_mobile = (int) get_theme_mod('shop_theme_category_title_size_mobile', 32);
$subtitle_size_desktop = (int) get_theme_mod('shop_theme_category_subtitle_size_desktop', 20);
$subtitle_size_mobile = (int) get_theme_mod('shop_theme_category_subtitle_size_mobile', 16);
$max_categories = (int) get_theme_mod('shop_theme_category_max_count', 8);
$show_empty = (bool) get_theme_mod('shop_theme_category_show_empty', false);
$padding_top = (int) get_theme_mod('shop_theme_category_padding_top', 80);
$padding_bottom = (int) get_theme_mod('shop_theme_category_padding_bottom', 80);

$terms = get_terms([
    'taxonomy'   => 'product_cat',
    'hide_empty' => !$show_empty,
    'number'     => max(1, $max_categories),
]);

if (is_wp_error($terms) || empty($terms)) {
    return;
}

$use_slider = count($terms) > 4;
$placeholder_image = function_exists('wc_placeholder_img_src') ? wc_placeholder_img_src('large') : '';
?>
<section
    class="product-categories section"
    aria-labelledby="product-categories-title"
    data-slider="<?php echo esc_attr($use_slider ? 'true' : 'false'); ?>"
    style="--pc-title-size-desktop: <?php echo esc_attr((string) max(24, min(90, $title_size_desktop))); ?>px; --pc-title-size-mobile: <?php echo esc_attr((string) max(20, min(64, $title_size_mobile))); ?>px; --pc-subtitle-size-desktop: <?php echo esc_attr((string) max(14, min(40, $subtitle_size_desktop))); ?>px; --pc-subtitle-size-mobile: <?php echo esc_attr((string) max(12, min(32, $subtitle_size_mobile))); ?>px; --pc-padding-top: <?php echo esc_attr((string) max(0, min(200, $padding_top))); ?>px; --pc-padding-bottom: <?php echo esc_attr((string) max(0, min(200, $padding_bottom))); ?>px;"
>
    <div class="container">
        <header class="product-categories__header">
            <?php if ($section_title !== '') : ?>
                <h2 class="product-categories__title" id="product-categories-title"><?php echo esc_html($section_title); ?></h2>
            <?php endif; ?>
            <?php if ($section_subtitle !== '') : ?>
                <p class="product-categories__subtitle"><?php echo esc_html($section_subtitle); ?></p>
            <?php endif; ?>
        </header>

        <div class="product-categories__viewport" tabindex="0" aria-label="<?php esc_attr_e('Product categories slider', 'shop-theme'); ?>">
            <?php if ($use_slider) : ?>
                <div class="product-categories__nav">
                    <button class="product-categories__button product-categories__button--prev" type="button" aria-label="<?php esc_attr_e('Previous categories', 'shop-theme'); ?>">
                        <svg class="product-categories__button-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M14.5 5.5L8 12l6.5 6.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <button class="product-categories__button product-categories__button--next" type="button" aria-label="<?php esc_attr_e('Next categories', 'shop-theme'); ?>">
                        <svg class="product-categories__button-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M9.5 5.5L16 12l-6.5 6.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            <?php endif; ?>

            <ul class="product-categories__track <?php echo esc_attr($use_slider ? 'is-slider' : 'is-grid'); ?>">
                <?php foreach ($terms as $term) : ?>
                    <?php
                    $thumbnail_id = (int) get_term_meta((int) $term->term_id, 'thumbnail_id', true);
                    $image_url = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'large') : '';
                    if (!$image_url) {
                        $image_url = $placeholder_image;
                    }
                    $term_url = get_term_link($term);
                    if (is_wp_error($term_url)) {
                        continue;
                    }
                    ?>
                    <li class="product-categories__item">
                        <a class="product-categories__card" href="<?php echo esc_url($term_url); ?>">
                            <img class="product-categories__image" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($term->name); ?>">
                            <div class="product-categories__overlay">
                                <h3 class="product-categories__card-title"><?php echo esc_html($term->name); ?></h3>
                                <p class="product-categories__count"><?php echo esc_html((string) $term->count); ?> <?php esc_html_e('Products', 'shop-theme'); ?></p>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>
