<?php
/**
 * Partners logos — GSAP infinite loop carousel.
 *
 * @package HTA_Landing
 */

$partners = new WP_Query([
    'post_type'      => 'hta_partner',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
]);
if (! $partners->have_posts()) {
    return;
}
?>
<section class="py-16 px-4 bg-slate-900/30 hta-rule-y scroll-mt-20" id="partners">
    <div class="max-w-6xl mx-auto">
        <div class="text-center max-w-2xl mx-auto mb-12" data-reveal>
            <h2 class="hta-section-title font-extrabold mb-3 text-white"><?php echo esc_html(hta_mod('hta_partners_title', 'שותפים')); ?></h2>
            <p class="text-slate-400 font-light"><?php echo esc_html(hta_mod('hta_partners_subtitle', 'השותפים המובילים של שבוע ההיי-טק')); ?></p>
        </div>
    </div>
    <div class="hta-partners-loop" dir="ltr">
        <div class="hta-partners-track">
            <?php while ($partners->have_posts()) : $partners->the_post(); ?>
                <?php $link = hta_meta(get_the_ID(), '_hta_partner_link'); ?>
                <div class="hta-partners-item">
                    <?php if ($link) : ?><a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener noreferrer"><?php endif; ?>
                        <div class="hta-partner-logo">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php
                                $thumb_id = (int) get_post_thumbnail_id();
                                the_post_thumbnail('hta-partner', [
                                    'class' => 'hta-partner-logo-img',
                                    'alt'   => hta_attachment_alt($thumb_id, get_the_title()),
                                ]);
                                ?>
                            <?php else : ?>
                                <?php the_title(); ?>
                            <?php endif; ?>
                        </div>
                    <?php if ($link) : ?></a><?php endif; ?>
                </div>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        </div>
    </div>
</section>
