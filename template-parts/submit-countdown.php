<?php
/**
 * Submit section countdown, replaced by the week's daily status once the week starts.
 *
 * @package HTA_Landing
 */

$target = hta_mod('hta_submit_countdown', '2026-11-01T00:00:00');
$title  = hta_mod('hta_submit_countdown_title', 'האירועים יתחילו בעוד:');

try {
    $target_time = new DateTimeImmutable((string) $target, wp_timezone());
} catch (Exception $e) {
    $target_time = new DateTimeImmutable('now', wp_timezone());
}

$started  = $target_time->getTimestamp() <= time();
$today    = wp_date('Y-m-d');
$days     = hta_week_days($target_time);
$statuses = [
    'ended' => __('הסתיים', 'hta-landing'),
    'live'  => __('מתקיים', 'hta-landing'),
    'soon'  => __('בקרוב', 'hta-landing'),
];
$title_live  = __('שבוע ההיי-טק מתקיים עכשיו', 'hta-landing');
$title_ended = __('שבוע ההיי-טק הסתיים', 'hta-landing');
$all_ended   = end($days)['date'] < $today;
?>
<?php if (! $started) : ?>
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
<?php endif; ?>
<div
    class="hta-week-status"
    data-tz="<?php echo esc_attr(wp_timezone_string()); ?>"
    data-title-live="<?php echo esc_attr($title_live); ?>"
    data-title-ended="<?php echo esc_attr($title_ended); ?>"
    data-label-ended="<?php echo esc_attr($statuses['ended']); ?>"
    data-label-live="<?php echo esc_attr($statuses['live']); ?>"
    data-label-soon="<?php echo esc_attr($statuses['soon']); ?>"
    <?php echo $started ? '' : 'hidden'; ?>
>
    <p class="hta-submit-countdown-title hta-week-status-title"><?php echo esc_html($all_ended ? $title_ended : $title_live); ?></p>
    <ol class="hta-week-days">
        <?php foreach ($days as $day) : ?>
            <?php $status = hta_week_day_status($day['date'], $today); ?>
            <li class="hta-week-day is-<?php echo esc_attr($status); ?>" data-date="<?php echo esc_attr($day['date']); ?>"<?php echo 'live' === $status ? ' aria-current="date"' : ''; ?>>
                <span class="hta-week-day-date hta-bidi-ltr" dir="ltr"><?php echo esc_html($day['label']); ?></span>
                <?php if ($day['title']) : ?>
                    <span class="hta-week-day-title"><?php echo esc_html($day['title']); ?></span>
                <?php endif; ?>
                <span class="hta-week-day-status"><?php echo esc_html($statuses[ $status ]); ?></span>
            </li>
        <?php endforeach; ?>
    </ol>
</div>
