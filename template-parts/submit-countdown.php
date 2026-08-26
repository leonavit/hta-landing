<?php
/**
 * Submit section countdown.
 *
 * @package HTA_Landing
 */

$target = hta_mod('hta_submit_countdown', '2026-11-01T00:00:00');
$title  = hta_mod('hta_submit_countdown_title', 'האירועים יתחילו בעוד:');
?>
<div class="hta-submit-countdown-wrap">
    <p class="hta-submit-countdown-title"><?php echo esc_html($title); ?></p>
    <div
        class="hta-submit-countdown"
        data-target="<?php echo esc_attr($target); ?>"
        aria-live="polite"
        aria-label="<?php esc_attr_e('ספירה לאחור לפתיחת שבוע ההיי-טק', 'hta-landing'); ?>"
    >
    <div class="hta-countdown-unit">
        <span class="hta-countdown-value hta-bidi-ltr" data-unit="milliseconds">000</span>
        <span class="hta-countdown-label">מילישניות</span>
    </div>
    <div class="hta-countdown-unit">
        <span class="hta-countdown-value hta-bidi-ltr" data-unit="seconds">00</span>
        <span class="hta-countdown-label">שניות</span>
    </div>
    <div class="hta-countdown-unit">
        <span class="hta-countdown-value hta-bidi-ltr" data-unit="minutes">00</span>
        <span class="hta-countdown-label">דקות</span>
    </div>
    <div class="hta-countdown-unit">
        <span class="hta-countdown-value hta-bidi-ltr" data-unit="hours">00</span>
        <span class="hta-countdown-label">שעות</span>
    </div>
    <div class="hta-countdown-unit">
        <span class="hta-countdown-value hta-bidi-ltr" data-unit="days">0</span>
        <span class="hta-countdown-label">ימים</span>
    </div>
    </div>
</div>
