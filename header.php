<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class('site'); ?>>
<?php wp_body_open(); ?>
<?php
$nav_bg_color = trim((string) get_theme_mod('shop_theme_nav_bg_color', '#ffffff'));
$logo_width = (int) get_theme_mod('shop_theme_logo_width', 32);
$nav_is_sticky = (bool) get_theme_mod('shop_theme_nav_sticky', false);
$show_search_icon = (bool) get_theme_mod('shop_theme_show_search_icon', true);
$show_favorites_icon = (bool) get_theme_mod('shop_theme_show_favorites_icon', true);
$show_account_icon = (bool) get_theme_mod('shop_theme_show_account_icon', true);
$favorites_url = trim((string) get_theme_mod('shop_theme_favorites_url', home_url('/favorites/')));
$header_classes = 'site-header';
if ($nav_is_sticky) {
    $header_classes .= ' site-header--sticky';
}
?>
<header class="<?php echo esc_attr($header_classes); ?>" role="banner" style="--nav-bg-color: <?php echo esc_attr($nav_bg_color); ?>; --logo-size: <?php echo esc_attr((string) max(16, min(240, $logo_width))); ?>px;">
    <div class="container site-header__inner">
        <a class="site-header__brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php esc_attr_e('Homepage', 'shop-theme'); ?>">
            <span class="site-header__logo">
                <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <span class="site-header__logo-fallback"><?php echo esc_html(strtoupper(substr((string) get_bloginfo('name'), 0, 1))); ?></span>
                <?php endif; ?>
            </span>
            <span class="site-header__brand-text"><?php bloginfo('name'); ?></span>
        </a>

        <button class="site-header__menu-toggle" type="button" aria-expanded="false" aria-controls="primary-menu">
            <span class="site-header__menu-label"><?php esc_html_e('Menu', 'shop-theme'); ?></span>
        </button>

        <nav class="site-header__nav" id="primary-menu" aria-label="<?php esc_attr_e('Primary menu', 'shop-theme'); ?>">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'menu menu--primary',
                'fallback_cb'    => false,
            ]);
            ?>
        </nav>

        <div class="site-header__actions">
            <?php if ($show_search_icon) : ?>
                <a class="site-header__icon-link" href="<?php echo esc_url(home_url('/?s=')); ?>" aria-label="<?php esc_attr_e('Search', 'shop-theme'); ?>">
                    <svg class="site-header__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="11" cy="11" r="6.6" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M16 16l4.2 4.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </a>
            <?php endif; ?>

            <?php if ($show_favorites_icon) : ?>
                <a class="site-header__icon-link" href="<?php echo esc_url($favorites_url !== '' ? $favorites_url : home_url('/favorites/')); ?>" aria-label="<?php esc_attr_e('Favorites', 'shop-theme'); ?>">
                    <svg class="site-header__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 20.8l-1.1-1C6.1 15.6 3 12.8 3 9.3A4.3 4.3 0 0 1 7.4 5a4.7 4.7 0 0 1 4.6 2.7A4.7 4.7 0 0 1 16.6 5 4.3 4.3 0 0 1 21 9.3c0 3.5-3.1 6.3-7.9 10.5l-1.1 1Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    </svg>
                </a>
            <?php endif; ?>

            <?php if ($show_account_icon) : ?>
                <a class="site-header__icon-link" href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/my-account/')); ?>" aria-label="<?php esc_attr_e('Account', 'shop-theme'); ?>">
                    <svg class="site-header__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M5 20c1.2-3.2 3.8-4.8 7-4.8s5.8 1.6 7 4.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </a>
            <?php endif; ?>

            <?php if (function_exists('WC')) : ?>
                <a class="site-header__icon-link site-header__icon-link--cart" href="<?php echo esc_url(wc_get_cart_url()); ?>" aria-label="<?php esc_attr_e('Cart', 'shop-theme'); ?>">
                    <svg class="site-header__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M3 5h2l2.2 10.4a1 1 0 0 0 1 .8h8.8a1 1 0 0 0 1-.8L20 8H7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="10" cy="20" r="1.4" fill="currentColor"/>
                        <circle cx="17" cy="20" r="1.4" fill="currentColor"/>
                    </svg>
                    <span class="site-header__cart-count"><?php echo esc_html((string) WC()->cart->get_cart_contents_count()); ?></span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
<main class="site-main" role="main">
