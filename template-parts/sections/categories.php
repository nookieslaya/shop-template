<section class="section categories" aria-labelledby="categories-title">
    <div class="container">
        <header class="section__header">
            <h2 class="section__title" id="categories-title"><?php esc_html_e('Categories', 'shop-theme'); ?></h2>
        </header>
        <div class="categories__grid">
            <?php
            $terms = get_terms([
                'taxonomy'   => 'product_cat',
                'hide_empty' => true,
                'number'     => 6,
            ]);

            if (!is_wp_error($terms) && !empty($terms)) :
                foreach ($terms as $term) :
                    $thumb_id = (int) get_term_meta($term->term_id, 'thumbnail_id', true);
                    $thumb_url = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'medium') : '';
                    ?>
                    <a class="category-card" href="<?php echo esc_url(get_term_link($term)); ?>">
                        <span class="category-card__media">
                            <?php if ($thumb_url) : ?>
                                <img class="category-card__image" src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($term->name); ?>">
                            <?php endif; ?>
                        </span>
                        <span class="category-card__name"><?php echo esc_html($term->name); ?></span>
                    </a>
                    <?php
                endforeach;
            endif;
            ?>
        </div>
    </div>
</section>
