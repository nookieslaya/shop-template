<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$banner_title = trim((string) get_theme_mod('shop_theme_promo_title', __('Summer Sale', 'shop-theme')));
$banner_subtitle = trim((string) get_theme_mod('shop_theme_promo_subtitle', __('Up to 50% off on selected items. Limited time offer!', 'shop-theme')));
$button_text = trim((string) get_theme_mod('shop_theme_promo_button_text', __('Shop Sale', 'shop-theme')));
$button_url = trim((string) get_theme_mod('shop_theme_promo_button_url', function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/')));
$bg_image = trim((string) get_theme_mod('shop_theme_promo_bg_image', 'https://images.unsplash.com/photo-1607082349566-187342175e2f?auto=format&fit=crop&w=1600&q=80'));
$overlay_color = trim((string) get_theme_mod('shop_theme_promo_overlay_color', '#0d1028'));
$overlay_opacity = (int) get_theme_mod('shop_theme_promo_overlay_opacity', 58);
$padding_top = (int) get_theme_mod('shop_theme_promo_padding_top', 64);
$padding_bottom = (int) get_theme_mod('shop_theme_promo_padding_bottom', 64);
$radius = (int) get_theme_mod('shop_theme_promo_radius', 18);
$title_size = (int) get_theme_mod('shop_theme_promo_title_size', 56);
$subtitle_size = (int) get_theme_mod('shop_theme_promo_subtitle_size', 36);
$button_text_size = (int) get_theme_mod('shop_theme_promo_button_text_size', 30);

?>
<section class="promo-banner section" aria-labelledby="promo-banner-title" style="--promo-overlay-color: <?php echo esc_attr($overlay_color); ?>; --promo-overlay-opacity: <?php echo esc_attr((string) max(0, min(100, $overlay_opacity))); ?>; --promo-padding-top: <?php echo esc_attr((string) max(0, min(240, $padding_top))); ?>px; --promo-padding-bottom: <?php echo esc_attr((string) max(0, min(240, $padding_bottom))); ?>px; --promo-radius: <?php echo esc_attr((string) max(0, min(48, $radius))); ?>px; --promo-title-size: <?php echo esc_attr((string) max(24, min(100, $title_size))); ?>px; --promo-subtitle-size: <?php echo esc_attr((string) max(14, min(64, $subtitle_size))); ?>px; --promo-button-text-size: <?php echo esc_attr((string) max(12, min(48, $button_text_size))); ?>px;">
    <div class="container">
        <div class="promo-banner__inner" style="--promo-bg-image: url('<?php echo esc_url($bg_image); ?>');">
            <div class="promo-banner__content">
                <?php if ($banner_title !== '') : ?>
                    <h2 class="promo-banner__title" id="promo-banner-title"><?php echo esc_html($banner_title); ?></h2>
                <?php endif; ?>

                <?php if ($banner_subtitle !== '') : ?>
                    <p class="promo-banner__subtitle"><?php echo esc_html($banner_subtitle); ?></p>
                <?php endif; ?>

                <?php if ($button_text !== '') : ?>
                    <a class="promo-banner__button" href="<?php echo esc_url($button_url !== '' ? $button_url : '#'); ?>">
                        <span><?php echo esc_html($button_text); ?></span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
