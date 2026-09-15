<?php
/**
 * Why now.
 *
 * @package HTA_Landing
 */

$image_id  = absint(hta_mod('hta_why_image', 0));
$why_title = hta_mod('hta_why_title', 'למה זה חשוב עכשיו?');
?>
<section id="why" class="py-20 px-4 bg-slate-900/50 hta-rule-y scroll-mt-20">
    <div class="hta-why-layout max-w-6xl mx-auto">
        <h2 class="hta-split-title hta-section-title font-extrabold text-white" data-reveal><?php echo esc_html($why_title); ?></h2>
        <div class="hta-split-media hta-tilt-wrap w-full h-full" data-reveal>
            <div class="hta-tilt-card hta-tilt-orbit hta-fill-photo bg-slate-900 border border-slate-800 overflow-hidden shadow-xl">
                <?php
                if ($image_id) {
                    echo hta_attachment_image($image_id, 'large', [], $why_title); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                } else {
                    ?>
                    <img src="<?php echo esc_url(HTA_URI . '/assets/images/logo-hta.png'); ?>" alt="<?php echo esc_attr($why_title); ?>">
                    <?php
                }
                ?>
            </div>
        </div>
        <div class="hta-split-copy" data-reveal>
            <p class="text-slate-300 leading-relaxed font-light mb-6 text-lg"><?php echo esc_html(hta_mod('hta_why_intro', 'ההיי-טק הישראלי נמצא בנקודת זמן קריטית, מול אתגרים גלובליים, תחרות גוברת וצורך בחיזוק המיצוב, התדמית וההשפעה הבינלאומית של התעשייה. שבוע ההיי-טק הישראלי מאפשר:')); ?></p>
            <ul class="hta-why-list">
                <?php for ($i = 1; $i <= 6; $i++) : ?>
                    <?php $item = hta_mod("hta_why_item_{$i}", ''); ?>
                    <?php if ($item) : ?>
                        <li><?php echo esc_html($item); ?></li>
                    <?php endif; ?>
                <?php endfor; ?>
            </ul>
        </div>
    </div>
</section>
