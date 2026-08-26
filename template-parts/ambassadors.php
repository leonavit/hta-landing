<?php
/**
 * Ambassadors — portrait cards.
 *
 * @package HTA_Landing
 */

$ambassadors = new WP_Query([
    'post_type'      => 'hta_ambassador',
    'posts_per_page' => 20,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
]);
if (! $ambassadors->have_posts()) {
    return;
}
?>
<section id="ambassadors" class="py-20 px-4 bg-slate-950 hta-rule-t scroll-mt-20">
    <div class="max-w-6xl mx-auto">
        <div class="text-center max-w-2xl mx-auto mb-12" data-reveal>
            <h2 class="hta-section-title font-extrabold mb-3 text-white"><?php echo esc_html(hta_mod('hta_ambassadors_title', 'שגרירי השבוע')); ?></h2>
            <p class="text-slate-400 font-light"><?php echo esc_html(hta_mod('hta_ambassadors_subtitle', 'מובילי דעת הקהל וראשי קהילות המלווים את שבוע ההיי-טק הישראלי.')); ?></p>
        </div>
        <div class="swiper hta-ambassadors-swiper">
            <div class="swiper-wrapper">
                <?php while ($ambassadors->have_posts()) : $ambassadors->the_post(); ?>
                    <?php
                    $name = get_the_title();
                    $role = hta_meta(get_the_ID(), '_hta_role');
                    $initial = $name ? mb_substr($name, 0, 1) : '';
                    ?>
                    <div class="swiper-slide h-auto">
                        <article class="hta-ambassador-card">
                            <span class="hta-draw-frame" aria-hidden="true">
                                <span class="is-bl"></span>
                                <span class="is-br"></span>
                                <span class="is-l"></span>
                                <span class="is-r"></span>
                                <span class="is-tl"></span>
                                <span class="is-tr"></span>
                            </span>
                            <div class="hta-ambassador-photo">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php
                                    the_post_thumbnail('hta-ambassador', [
                                        'class' => 'hta-ambassador-img',
                                        'sizes' => '(min-width: 768px) 25vw, (min-width: 640px) 50vw, 100vw',
                                    ]);
                                    ?>
                                <?php else : ?>
                                    <span class="hta-ambassador-fallback"><?php echo esc_html($initial); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="hta-ambassador-meta">
                                <h4 class="hta-ambassador-name"><?php echo esc_html($name); ?></h4>
                                <?php if ($role) : ?>
                                    <span class="hta-ambassador-role"><?php echo esc_html($role); ?></span>
                                <?php endif; ?>
                            </div>
                        </article>
                    </div>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>
