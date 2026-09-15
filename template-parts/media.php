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
                                            <iframe
                                                class="hta-media-video"
                                                src="<?php echo esc_url('https://www.youtube-nocookie.com/embed/' . $yt . '?enablejsapi=1&rel=0&playsinline=1'); ?>"
                                                title="<?php the_title_attribute(); ?>"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                allowfullscreen
                                            ></iframe>
                                        <?php elseif (has_post_thumbnail()) : ?>
                                            <?php
                                            $thumb_id = (int) get_post_thumbnail_id();
                                            the_post_thumbnail('hta-media', [
                                                'alt' => hta_attachment_alt($thumb_id, get_the_title()),
                                            ]);
                                            ?>
                                        <?php endif; ?>
                                    </div>
                                <?php else : ?>
                                    <div class="hta-media-visual<?php echo has_post_thumbnail() ? '' : ' is-placeholder'; ?>">
                                        <?php
                                        if (has_post_thumbnail()) {
                                            $thumb_id = (int) get_post_thumbnail_id();
                                            the_post_thumbnail('hta-media', [
                                                'alt' => hta_attachment_alt($thumb_id, get_the_title()),
                                            ]);
                                        } else {
                                            echo '<img src="' . esc_url(HTA_URI . '/assets/images/logo-hta.png') . '" alt="' . esc_attr(get_the_title()) . '">';
                                        }
                                        ?>
                                    </div>
                                <?php endif; ?>
                                <h4 class="font-bold text-white text-lg mb-2"><?php the_title(); ?></h4>
                                <?php if ($excerpt) : ?>
                                    <p class="text-slate-400 text-sm font-light mb-6"><?php echo esc_html($excerpt); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="hta-media-card-footer">
                                <span class="hta-media-countdown" aria-hidden="true">
                                    <span class="hta-media-countdown-value">5</span>
                                </span>
                                <?php if ($link) : ?>
                                    <a href="<?php echo esc_url($link); ?>" class="hta-media-card-link text-hta-1 text-sm font-semibold hover:underline" <?php echo (0 === strpos($link, 'http')) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                        <?php echo $is_video ? 'לסרטון ←' : 'לכתבה ←'; ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </article>
                    </div>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            </div>
            <div class="hta-media-controls mt-8">
                <div class="swiper-pagination"></div>
                <button type="button" class="hta-media-autoplay-toggle" aria-label="<?php esc_attr_e('עצור קרוסלה', 'hta-landing'); ?>" aria-pressed="false">
                    <span class="hta-media-autoplay-pause" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <rect x="6" y="5" width="4" height="14"></rect>
                            <rect x="14" y="5" width="4" height="14"></rect>
                        </svg>
                    </span>
                    <span class="hta-media-autoplay-play" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M8 5v14l11-7z"></path>
                        </svg>
                    </span>
                </button>
            </div>
        </div>
    </div>
</section>
