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
                <?php
                $submit_phone = hta_mod('hta_submit_note_phone', '+972 (0) 3-5198863');
                $submit_phone = trim((string) $submit_phone);
                if ('' !== $submit_phone) :
                    $tel_href = hta_phone_tel_href($submit_phone);
                    ?>
                    <?php if ('' !== $tel_href) : ?>
                        <a class="hta-submit-note-phone hta-bidi-ltr" dir="ltr" href="<?php echo esc_attr('tel:' . $tel_href); ?>"><?php echo esc_html($submit_phone); ?></a>
                    <?php else : ?>
                        <span class="hta-submit-note-phone hta-bidi-ltr" dir="ltr"><?php echo esc_html($submit_phone); ?></span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <div id="submit-event-form" class="hta-submit-form-panel p-8 md:p-10 shadow-2xl border border-slate-800 scroll-mt-28" data-reveal>
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
                    <p class="hta-field-error" id="event_name_error" hidden></p>
                </div>
                <div>
                    <label class="block text-sm mb-1 text-hta-1" for="host_company">שם החברה המארחת *</label>
                    <input type="text" id="host_company" name="host_company" required maxlength="180" class="w-full bg-slate-950 border border-slate-800 px-4 py-3 text-white focus:outline-none focus:border-hta-1" placeholder="שם החברה או הארגון">
                    <p class="hta-field-error" id="host_company_error" hidden></p>
                </div>
                <div class="hta-datetime-field">
                    <label class="block text-sm mb-1 text-hta-1" for="event_datetime">תאריך ושעה *</label>
                    <div class="hta-datetime-wrap" dir="ltr">
                        <span
                            class="hta-datetime-display is-empty"
                            id="event_datetime_display"
                            aria-hidden="true"
                            data-placeholder="YYYY-MM-DD HH:MM"
                        ></span>
                        <input
                            type="datetime-local"
                            id="event_datetime"
                            name="event_datetime"
                            required
                            dir="ltr"
                            lang="en"
                            class="hta-datetime-native w-full min-w-0 bg-transparent border border-slate-800 px-4 py-3 text-white focus:outline-none focus:border-hta-1"
                        >
                    </div>
                    <p class="hta-field-error" id="event_datetime_error" hidden></p>
                </div>
                <div class="hta-online-field">
                    <input type="checkbox" id="event_online" name="event_online" value="1">
                    <label class="text-sm text-hta-1" for="event_online">האירוע יתקיים אונליין (וובינר)</label>
                </div>
                <div class="hta-city-field">
                    <label class="block text-sm mb-1 text-hta-1" for="event_location">עיר *</label>
                    <div class="hta-city-combobox">
                        <input
                            type="text"
                            id="event_location"
                            name="event_location"
                            required
                            maxlength="180"
                            autocomplete="off"
                            spellcheck="false"
                            role="combobox"
                            aria-autocomplete="list"
                            aria-expanded="false"
                            aria-controls="event_location_listbox"
                            aria-haspopup="listbox"
                            aria-required="true"
                            aria-describedby="event_location_hint"
                            class="w-full bg-slate-950 border border-slate-800 px-4 py-3 text-white focus:outline-none focus:border-hta-1"
                            placeholder="הקלידו שם עיר או יישוב"
                        >
                        <ul id="event_location_listbox" class="hta-city-listbox" role="listbox" hidden></ul>
                    </div>
                    <p class="hta-field-error" id="event_location_error" hidden></p>
                    <p id="event_location_hint" class="hta-city-hint">בחרו יישוב מתוך ההצעות לאחר הקלדת לפחות 2 תווים</p>
                </div>
                <script>
                (function () {
                    var box = document.getElementById('event_online');
                    var wrap = document.querySelector('#hta-submit-form .hta-city-field');
                    var input = document.getElementById('event_location');
                    if (!box || !wrap) {
                        return;
                    }
                    function sync() {
                        var on = !!box.checked;
                        wrap.hidden = on;
                        wrap.classList.toggle('is-online-hidden', on);
                        if (input) {
                            input.required = !on;
                            input.setAttribute('aria-required', on ? 'false' : 'true');
                        }
                    }
                    box.addEventListener('change', sync);
                    box.addEventListener('click', sync);
                    sync();
                })();
                </script>
                <div>
                    <label class="block text-sm mb-1 text-hta-1" for="event_description">תיאור קצר</label>
                    <textarea id="event_description" name="event_description" rows="3" maxlength="4000" class="w-full bg-slate-950 border border-slate-800 px-4 py-3 text-white focus:outline-none focus:border-hta-1 resize-none" placeholder="תאר בקצרה את האירוע..."></textarea>
                </div>
                <div class="hta-submit-agree">
                    <input type="checkbox" id="event_agree" name="event_agree" value="1" required>
                    <label for="event_agree">
                        <?php
                        echo wp_kses(
                            sprintf(
                                /* translators: 1: terms URL, 2: privacy URL */
                                __('אני מאשר/ת שקראתי ומסכים/ה ל<a href="%1$s" target="_blank" rel="noopener noreferrer">תקנון האתר</a> ול<a href="%2$s" target="_blank" rel="noopener noreferrer">מדיניות הפרטיות</a>, ושהמידע שמסרתי נכון ומדויק.', 'hta-landing'),
                                esc_url(hta_page_url('takkanon')),
                                esc_url(hta_page_url('mediniut-pratiut'))
                            ),
                            [
                                'a' => [
                                    'href'   => true,
                                    'target' => true,
                                    'rel'    => true,
                                ],
                            ]
                        );
                        ?>
                    </label>
                    <p class="hta-field-error" id="event_agree_error" hidden></p>
                </div>
                <p id="hta-form-status" class="text-sm min-h-5" role="status" aria-live="polite" aria-atomic="true"></p>
                <button type="submit" class="hta-btn w-full py-3.5 mt-2" formnovalidate>שלחו להגשה</button>
            </form>
        </div>
        </div>
    </div>
</section>
