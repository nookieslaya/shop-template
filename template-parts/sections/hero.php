<section class="hero section" aria-labelledby="hero-title">
    <div class="container hero__inner">
        <p class="hero__eyebrow">New Collection</p>
        <h1 class="hero__title" id="hero-title">Simple products. Fast checkout.</h1>
        <p class="hero__text">Lightweight WooCommerce theme optimized for conversion and speed.</p>
        <a class="button button--primary" href="<?php echo esc_url(get_permalink((int) wc_get_page_id('shop'))); ?>">
            <?php esc_html_e('Shop now', 'shop-theme'); ?>
        </a>
    </div>
</section>
