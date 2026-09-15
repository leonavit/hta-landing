<?php
/**
 * About.
 *
 * @package HTA_Landing
 */

$image_id    = absint(hta_mod('hta_about_image', 0));
$about_title = hta_mod('hta_about_title', 'אודות שבוע ההיי-טק');
?>
<section id="about" class="pt-0 pb-20 md:py-20 px-4 bg-slate-950 scroll-mt-20" tabindex="-1">
    <div class="hta-about-layout max-w-6xl mx-auto">
        <h2 class="hta-split-title hta-section-title font-extrabold text-white" data-reveal><?php echo esc_html($about_title); ?></h2>
        <div class="hta-split-media hta-tilt-wrap w-full h-full" data-reveal>
            <div class="hta-tilt-card hta-tilt-orbit hta-fill-photo bg-slate-900 border border-slate-800 overflow-hidden shadow-xl">
                <?php
                if ($image_id) {
                    echo hta_attachment_image($image_id, 'large', [], $about_title); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                } else {
                    ?>
                    <img src="<?php echo esc_url(HTA_URI . '/assets/images/logo-hta.png'); ?>" alt="<?php echo esc_attr($about_title); ?>">
                    <?php
                }
                ?>
            </div>
        </div>
        <div class="hta-split-copy" data-reveal>
            <p class="text-slate-300 leading-relaxed font-light mb-4 text-lg"><?php echo esc_html(hta_mod('hta_about_p1', 'מהלך חדש של איגוד ההיי-טק הישראלי, שמטרתו לרכז בשבוע אחד את כלל העשייה, החדשנות והעוצמה של ההיי-טק הישראלי, בישראל ובעולם.')); ?></p>
            <p class="text-slate-300 leading-relaxed font-light mb-4 text-lg"><?php echo esc_html(hta_mod('hta_about_p2', 'השבוע נועד לחזק את המיצוב של ההיי-טק הישראלי, ליצור חיבורים חדשים בין כלל שחקני האקו-סיסטם, ולהציג את התרומה המשמעותית של התעשייה לכלכלה ולחברה.')); ?></p>
            <p class="text-slate-300 leading-relaxed font-light text-lg"><?php echo esc_html(hta_mod('hta_about_p3', 'במהלך השבוע חברות היי-טק, ארגונים, גופי ממשל, משקיעים, אקדמיה ומערכת החינוך יקיימו אירועים, פעילויות ומפגשים סביב שישה עולמות תוכן.')); ?></p>
        </div>
    </div>
</section>
