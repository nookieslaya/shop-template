<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>
</main>
<footer class="site-footer" role="contentinfo">
    <div class="container site-footer__inner">
        <div class="site-footer__brand">
            <strong><?php bloginfo('name'); ?></strong>
            <p><?php bloginfo('description'); ?></p>
        </div>

        <nav class="site-footer__nav" aria-label="<?php esc_attr_e('Footer menu', 'shop-theme'); ?>">
            <?php
            wp_nav_menu([
                'theme_location' => 'footer',
                'container'      => false,
                'menu_class'     => 'menu menu--footer',
                'fallback_cb'    => false,
            ]);
            ?>
        </nav>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
