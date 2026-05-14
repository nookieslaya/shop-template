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
<header class="site-header" role="banner">
    <div class="container site-header__inner">
        <a class="site-header__brand" href="<?php echo esc_url(home_url('/')); ?>">
            <?php bloginfo('name'); ?>
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
            <?php if (function_exists('WC')) : ?>
                <a class="site-header__cart-link" href="<?php echo esc_url(wc_get_cart_url()); ?>">
                    <?php esc_html_e('Cart', 'shop-theme'); ?>
                    <span class="site-header__cart-count"><?php echo esc_html((string) WC()->cart->get_cart_contents_count()); ?></span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
<main class="site-main" role="main">
