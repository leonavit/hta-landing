<?php
/**
 * About.
 *
 * @package HTA_Landing
 */

$image_id  = absint(hta_mod('hta_about_image', 0));
$image_src = $image_id ? wp_get_attachment_image_url($image_id, 'large') : HTA_URI . '/assets/images/logo-hta.png';
?>
<section id="about" class="pt-0 pb-20 md:py-20 px-4 bg-slate-950 scroll-mt-20">
    <div class="hta-about-layout max-w-6xl mx-auto">
        <h2 class="hta-split-title hta-section-title font-extrabold text-white" data-reveal><?php echo esc_html(hta_mod('hta_about_title', 'אודות שבוע ההיי-טק')); ?></h2>
        <div class="hta-split-media hta-tilt-wrap w-full h-full" data-reveal>
            <div class="hta-tilt-card hta-tilt-orbit hta-fill-photo bg-slate-900 border border-slate-800 overflow-hidden shadow-xl">
                <img src="<?php echo esc_url($image_src); ?>" alt="">
            </div>
        </div>
        <div class="hta-split-copy" data-reveal>
            <p class="text-slate-300 leading-relaxed font-light mb-4 text-lg"><?php echo esc_html(hta_mod('hta_about_p1', 'מהלך חדש של איגוד ההיי-טק הישראלי, שמטרתו לרכז בשבוע אחד את כלל העשייה, החדשנות והעוצמה של ההיי-טק הישראלי, בישראל ובעולם.')); ?></p>
            <p class="text-slate-300 leading-relaxed font-light mb-4 text-lg"><?php echo esc_html(hta_mod('hta_about_p2', 'השבוע נועד לחזק את המיצוב של ההיי-טק הישראלי, ליצור חיבורים חדשים בין כלל שחקני האקו-סיסטם, ולהציג את התרומה המשמעותית של התעשייה לכלכלה ולחברה.')); ?></p>
            <p class="text-slate-300 leading-relaxed font-light text-lg"><?php echo esc_html(hta_mod('hta_about_p3', 'במהלך השבוע חברות היי-טק, ארגונים, גופי ממשל, משקיעים, אקדמיה ומערכת החינוך יקיימו אירועים, פעילויות ומפגשים סביב שישה עולמות תוכן.')); ?></p>
        </div>
    </div>
</section>
