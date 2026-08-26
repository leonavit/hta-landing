<?php
/**
 * Week topics.
 *
 * @package HTA_Landing
 */
?>
<section id="topics" class="hta-topics py-20 px-4 bg-slate-950 scroll-mt-20">
    <?php if (hta_mod('hta_topics_shader_enable', true)) : ?>
        <div class="hta-topics-shader" aria-hidden="true"></div>
    <?php endif; ?>
    <div class="hta-topics-inner max-w-6xl mx-auto">
        <div class="text-center max-w-3xl mx-auto mb-16" data-reveal>
            <h2 class="hta-section-title font-extrabold text-white mb-3"><?php echo esc_html(hta_mod('hta_topics_title', 'נושאי השבוע')); ?></h2>
            <p class="text-hta-1 font-medium text-lg"><?php echo esc_html(hta_mod('hta_topics_subtitle', 'תחומים שמעצבים את עתיד ההיי-טק הישראלי')); ?></p>
        </div>
        <div class="hta-topics-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php for ($i = 1; $i <= 6; $i++) : ?>
                <?php
                $badge    = hta_mod("hta_topic_{$i}_badge", '');
                $title    = hta_mod("hta_topic_{$i}_title", '');
                $text     = hta_mod("hta_topic_{$i}_text", '');
                $image_id = absint(hta_mod("hta_topic_{$i}_image", 0));
                $image_src = $image_id ? wp_get_attachment_image_url($image_id, 'hta-topic') : HTA_URI . '/assets/images/logo-hta.png';
                if (! $title) {
                    continue;
                }
                ?>
                <article class="hta-topic-card bg-slate-900 border border-slate-800 flex flex-col hover:border-hta-1/50 shadow-xl">
                    <div class="hta-topic-photo<?php echo $image_id ? '' : ' is-placeholder'; ?>">
                        <img src="<?php echo esc_url($image_src); ?>" alt="<?php echo esc_attr($title); ?>">
                    </div>
                    <div class="hta-topic-body">
                        <?php
                        $date = $badge;
                        if ($date && false !== strpos($date, '|')) {
                            $date = trim(explode('|', $date, 2)[0]);
                        }
                        if ($date) :
                            ?>
                            <span class="hta-topic-date"><?php echo esc_html($date); ?></span>
                        <?php endif; ?>
                        <h3 class="text-xl font-bold text-white mb-3"><?php echo esc_html($title); ?></h3>
                        <p class="text-slate-300 font-light text-sm leading-relaxed"><?php echo esc_html($text); ?></p>
                    </div>
                </article>
            <?php endfor; ?>
        </div>
    </div>
</section>
