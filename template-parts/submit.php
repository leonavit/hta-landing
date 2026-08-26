<?php
/**
 * Event submission form.
 *
 * @package HTA_Landing
 */

$status = isset($_GET['hta_submit']) ? sanitize_key(wp_unslash($_GET['hta_submit'])) : '';
?>
<section id="submit-event" class="hta-submit py-24 px-4 bg-slate-950 hta-rule-t scroll-mt-20">
    <div class="hta-particle-field" aria-hidden="true"></div>
    <div class="hta-submit-inner max-w-6xl mx-auto">
        <?php get_template_part('template-parts/submit', 'countdown'); ?>
        <div class="hta-submit-grid grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
        <div data-reveal>
            <h2 class="hta-section-title font-extrabold text-white mb-4"><?php echo esc_html(hta_mod('hta_submit_title', 'למה להגיש אירוע?')); ?></h2>
            <p class="text-slate-300 font-light mb-8 leading-relaxed"><?php echo esc_html(hta_mod('hta_submit_intro', 'החברות המשתתפות יקבלו חשיפה כחלק מהלך ארצי רחב היקף, ייראות באתר המרכזי של שבוע ההיי-טק הישראלי, ויהיו חלק מהלך לאומי שיוצר השפעה אמיתית.')); ?></p>
            <ul class="hta-submit-list">
                <?php
                $benefit_defaults = [
                    1 => 'חשיפה רחבה באתר המרכזי הארצי',
                    2 => 'חיבור לשותפים מובילים וקהילות',
                    3 => 'ליווי מקצועי של צוות האיגוד',
                    4 => 'כלים לניהול הרשמות ומשתתפים',
                    5 => 'קישור לאקוסיסטם הישראלי והגלובלי',
                ];
                for ($i = 1; $i <= 5; $i++) :
                    $benefit = hta_mod("hta_submit_benefit_{$i}", $benefit_defaults[$i]);
                    if ($benefit) :
                        ?>
                        <li><?php echo esc_html($benefit); ?></li>
                        <?php
                    endif;
                endfor;
                ?>
            </ul>
            <div class="hta-submit-note">
                <h4 class="hta-submit-note-title"><?php echo esc_html(hta_mod('hta_submit_note_title', 'ניהול אירועים דיגיטלי')); ?></h4>
                <p class="hta-submit-note-text"><?php echo esc_html(hta_mod('hta_submit_note_text', 'האירוע שלכם יכלול דף ייעודי, כלי הרשמה ומעקב, ועדכונים ישירים בפלטפורמה.')); ?></p>
            </div>
        </div>

        <div class="hta-submit-form-panel p-8 md:p-10 shadow-2xl border border-slate-800" data-reveal>
            <span class="hta-draw-frame" aria-hidden="true">
                <span class="is-bl"></span>
                <span class="is-br"></span>
                <span class="is-l"></span>
                <span class="is-r"></span>
                <span class="is-tl"></span>
                <span class="is-tr"></span>
            </span>
            <h3 class="text-2xl font-bold text-center mb-2 text-white"><?php echo esc_html(hta_mod('hta_submit_form_title', 'הגשת אירוע משלכם')); ?></h3>
            <p class="text-slate-400 text-center text-sm mb-8 font-light"><?php echo esc_html(hta_mod('hta_submit_form_text', 'מלאו את הפרטים ושלחו להגשה.')); ?></p>

            <?php if ('ok' === $status) : ?>
                <p class="mb-6 border border-hta-1/40 bg-hta-1/10 text-hta-1 px-4 py-3 text-sm">ההגשה התקבלה ותעבור לסקירה.</p>
            <?php elseif ('error' === $status) : ?>
                <p class="mb-6 border border-rose-500/40 bg-rose-500/10 text-rose-300 px-4 py-3 text-sm">אירעה שגיאה. בדקו את השדות ונסו שוב.</p>
            <?php endif; ?>

            <form id="hta-submit-form" class="space-y-4" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" novalidate>
                <input type="hidden" name="action" value="hta_submit_event">
                <input type="hidden" name="hta_nonce" value="<?php echo esc_attr(wp_create_nonce('hta_submit_event')); ?>">
                <div class="hta-hp" aria-hidden="true">
                    <label>אתר</label>
                    <input type="text" name="hta_website" tabindex="-1" autocomplete="off">
                </div>

                <div>
                    <label class="block text-sm mb-1 text-hta-1" for="event_name">שם האירוע *</label>
                    <input type="text" id="event_name" name="event_name" required maxlength="180" class="w-full bg-slate-950 border border-slate-800 px-4 py-3 text-white focus:outline-none focus:border-hta-1" placeholder="הקלד את שם האירוע">
                </div>
                <div>
                    <label class="block text-sm mb-1 text-hta-1" for="host_company">שם החברה המארחת *</label>
                    <input type="text" id="host_company" name="host_company" required maxlength="180" class="w-full bg-slate-950 border border-slate-800 px-4 py-3 text-white focus:outline-none focus:border-hta-1" placeholder="שם החברה או הארגון">
                </div>
                <div class="w-full min-w-0 overflow-hidden">
                    <label class="block text-sm mb-1 text-hta-1" for="event_datetime">תאריך ושעה *</label>
                    <input type="datetime-local" id="event_datetime" name="event_datetime" required dir="ltr" class="w-full min-w-0 bg-slate-950 border border-slate-800 px-4 py-3 text-white focus:outline-none focus:border-hta-1">
                </div>
                <div>
                    <label class="block text-sm mb-1 text-hta-1" for="event_location">מיקום האירוע *</label>
                    <input type="text" id="event_location" name="event_location" required maxlength="180" class="w-full bg-slate-950 border border-slate-800 px-4 py-3 text-white focus:outline-none focus:border-hta-1" placeholder="למשל: תל אביב / אונליין">
                </div>
                <div>
                    <label class="block text-sm mb-1 text-hta-1" for="event_description">תיאור קצר</label>
                    <textarea id="event_description" name="event_description" rows="3" maxlength="4000" class="w-full bg-slate-950 border border-slate-800 px-4 py-3 text-white focus:outline-none focus:border-hta-1 resize-none" placeholder="תאר בקצרה את האירוע..."></textarea>
                </div>
                <p id="hta-form-status" class="text-sm min-h-5" role="status" aria-live="polite"></p>
                <button type="submit" class="hta-btn w-full py-3.5 mt-2">שלחו להגשה</button>
            </form>
        </div>
        </div>
    </div>
</section>
