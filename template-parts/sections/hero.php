<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$default_shop_url = function_exists('wc_get_page_id') ? get_permalink((int) wc_get_page_id('shop')) : home_url('/');
$hero_image_url = trim((string) get_theme_mod('shop_theme_hero_image', ''));
$hero_bg_color = trim((string) get_theme_mod('shop_theme_hero_bg_color', '#ececf0'));
$hero_title_size = (int) get_theme_mod('shop_theme_hero_title_size', 64);
$hero_subtitle_size = (int) get_theme_mod('shop_theme_hero_subtitle_size', 32);
$hero_title = trim((string) get_theme_mod('shop_theme_hero_title', __('Discover Your Perfect Style', 'shop-theme')));
$hero_subtitle = trim((string) get_theme_mod('shop_theme_hero_subtitle', __('Shop the latest trends in fashion, accessories, and lifestyle products. Quality guaranteed with free shipping on orders over $50.', 'shop-theme')));
$primary_text = trim((string) get_theme_mod('shop_theme_hero_primary_text', __('Shop Now', 'shop-theme')));
$primary_url = trim((string) get_theme_mod('shop_theme_hero_primary_url', (string) $default_shop_url));
$secondary_text = trim((string) get_theme_mod('shop_theme_hero_secondary_text', __('View Sale', 'shop-theme')));
$secondary_url = trim((string) get_theme_mod('shop_theme_hero_secondary_url', (string) $default_shop_url));
?>
<section class="hero section" aria-labelledby="hero-title" style="--hero-bg-color: <?php echo esc_attr($hero_bg_color); ?>; --hero-title-size: <?php echo esc_attr((string) max(24, min(100, $hero_title_size))); ?>px; --hero-subtitle-size: <?php echo esc_attr((string) max(14, min(64, $hero_subtitle_size))); ?>px;">
    <div class="container">
        <div class="hero__inner">
            <div class="hero__content">
                <?php if ($hero_title !== '') : ?>
                    <h1 class="hero__title" id="hero-title"><?php echo wp_kses($hero_title, ['br' => []]); ?></h1>
                <?php endif; ?>

                <?php if ($hero_subtitle !== '') : ?>
                    <p class="hero__text"><?php echo wp_kses($hero_subtitle, ['br' => []]); ?></p>
                <?php endif; ?>

                <div class="hero__actions">
                    <?php if ($primary_text !== '') : ?>
                        <a class="button button--primary hero__button hero__button--primary" href="<?php echo esc_url($primary_url !== '' ? $primary_url : '#'); ?>">
                            <?php echo esc_html($primary_text); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($secondary_text !== '') : ?>
                        <a class="button button--secondary hero__button hero__button--secondary" href="<?php echo esc_url($secondary_url !== '' ? $secondary_url : '#'); ?>">
                            <?php echo esc_html($secondary_text); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="hero__media">
                <?php if ($hero_image_url) : ?>
                    <img class="hero__image" src="<?php echo esc_url($hero_image_url); ?>" alt="<?php echo esc_attr($hero_title !== '' ? $hero_title : get_bloginfo('name')); ?>">
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
