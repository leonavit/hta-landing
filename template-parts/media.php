<?php
/**
 * Media / press.
 *
 * @package HTA_Landing
 */

$media = new WP_Query([
    'post_type'      => 'hta_media',
    'posts_per_page' => 9,
    'post_status'    => 'publish',
]);
if (! $media->have_posts()) {
    return;
}
?>
<section id="media" class="py-20 px-4 bg-slate-950 scroll-mt-20">
    <div class="max-w-6xl mx-auto">
        <div class="text-center max-w-2xl mx-auto mb-12" data-reveal>
            <h2 class="hta-section-title font-extrabold mb-3 text-white"><?php echo esc_html(hta_mod('hta_media_title', 'מן התקשורת')); ?></h2>
            <p class="text-slate-400 font-light"><?php echo esc_html(hta_mod('hta_media_subtitle', 'כתבות, סרטונים וראיונות על שבוע ההיי-טק הישראלי.')); ?></p>
        </div>
        <div class="swiper hta-media-swiper">
            <div class="swiper-wrapper">
                <?php while ($media->have_posts()) : $media->the_post(); ?>
                    <?php
                    $id       = get_the_ID();
                    $type     = hta_meta($id, '_hta_media_type', 'article');
                    $is_video = ('video' === $type);
                    $link     = hta_meta($id, '_hta_media_link');
                    $yt       = ($is_video && $link) ? hta_youtube_id($link) : '';
                    $excerpt  = get_the_excerpt();
                    ?>
                    <div class="swiper-slide h-auto">
                        <article class="hta-media-card bg-slate-900 border border-slate-800 p-6 flex flex-col justify-between h-full relative">
                            <span class="hta-draw-frame" aria-hidden="true">
                                <span class="is-bl"></span>
                                <span class="is-br"></span>
                                <span class="is-l"></span>
                                <span class="is-r"></span>
                                <span class="is-tl"></span>
                                <span class="is-tr"></span>
                            </span>
                            <div>
                                <?php if ($is_video) : ?>
                                    <div class="hta-media-visual">
                                        <?php if ($yt) : ?>
                                            <iframe src="<?php echo esc_url('https://www.youtube-nocookie.com/embed/' . $yt); ?>" title="<?php the_title_attribute(); ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                        <?php elseif (has_post_thumbnail()) : ?>
                                            <?php the_post_thumbnail('hta-media'); ?>
                                        <?php endif; ?>
                                    </div>
                                <?php else : ?>
                                    <div class="hta-media-visual<?php echo has_post_thumbnail() ? '' : ' is-placeholder'; ?>">
                                        <?php
                                        if (has_post_thumbnail()) {
                                            the_post_thumbnail('hta-media');
                                        } else {
                                            echo '<img src="' . esc_url(HTA_URI . '/assets/images/logo-hta.png') . '" alt="">';
                                        }
                                        ?>
                                    </div>
                                <?php endif; ?>
                                <h4 class="font-bold text-white text-lg mb-2"><?php the_title(); ?></h4>
                                <?php if ($excerpt) : ?>
                                    <p class="text-slate-400 text-sm font-light mb-6"><?php echo esc_html($excerpt); ?></p>
                                <?php endif; ?>
                            </div>
                            <?php if ($link) : ?>
                                <a href="<?php echo esc_url($link); ?>" class="text-hta-1 text-sm font-semibold hover:underline" <?php echo (0 === strpos($link, 'http')) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                    <?php echo $is_video ? 'לסרטון ←' : 'לכתבה ←'; ?>
                                </a>
                            <?php endif; ?>
                        </article>
                    </div>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            </div>
            <div class="swiper-pagination mt-8"></div>
        </div>
    </div>
</section>
