<section class="section featured-products" aria-labelledby="featured-products-title">
    <div class="container">
        <header class="section__header">
            <h2 class="section__title" id="featured-products-title"><?php esc_html_e('Featured Products', 'shop-theme'); ?></h2>
        </header>

        <?php
        echo do_shortcode('[products limit="8" columns="4" visibility="featured"]');
        ?>
    </div>
</section>
