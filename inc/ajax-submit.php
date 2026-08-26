<?php
/**
 * Secure frontend event submission — AJAX + POST.
 * Creates a draft hta_event for editorial review. No ACF.
 *
 * @package HTA_Landing
 */

if (! defined('ABSPATH')) {
    exit;
}

function hta_register_submission_hooks(): void
{
    add_action('wp_ajax_hta_submit_event', 'hta_handle_event_submission');
    add_action('wp_ajax_nopriv_hta_submit_event', 'hta_handle_event_submission');
    add_action('admin_post_hta_submit_event', 'hta_handle_event_submission');
    add_action('admin_post_nopriv_hta_submit_event', 'hta_handle_event_submission');
}
add_action('init', 'hta_register_submission_hooks');

function hta_is_ajax_request(): bool
{
    return defined('DOING_AJAX') && DOING_AJAX;
}

function hta_submission_error(string $message, int $code = 400): void
{
    if (hta_is_ajax_request()) {
        wp_send_json_error(['message' => $message], $code);
    }

    wp_safe_redirect(add_query_arg('hta_submit', 'error', wp_get_referer() ?: home_url('/#submit-event')));
    exit;
}

function hta_submission_success(): void
{
    if (hta_is_ajax_request()) {
        wp_send_json_success(['message' => __('ההגשה התקבלה ותעבור לסקירה.', 'hta-landing')]);
    }

    wp_safe_redirect(add_query_arg('hta_submit', 'ok', wp_get_referer() ?: home_url('/#submit-event')));
    exit;
}

function hta_handle_event_submission(): void
{
    if ('POST' !== strtoupper($_SERVER['REQUEST_METHOD'] ?? '')) {
        hta_submission_error(__('בקשה לא תקינה.', 'hta-landing'), 405);
    }

    $nonce = isset($_POST['hta_nonce']) ? sanitize_text_field(wp_unslash($_POST['hta_nonce'])) : '';
    if (! wp_verify_nonce($nonce, 'hta_submit_event')) {
        hta_submission_error(__('פג תוקף הטופס. רעננו את העמוד.', 'hta-landing'), 403);
    }

    $honeypot = isset($_POST['hta_website']) ? trim((string) wp_unslash($_POST['hta_website'])) : '';
    if ('' !== $honeypot) {
        hta_submission_success();
    }

    $ip    = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : 'unknown';
    $key   = 'hta_submit_' . md5($ip);
    $count = (int) get_transient($key);
    if ($count >= 8) {
        hta_submission_error(__('נשלחו יותר מדי הגשות. נסו שוב מאוחר יותר.', 'hta-landing'), 429);
    }
    set_transient($key, $count + 1, HOUR_IN_SECONDS);

    $name     = isset($_POST['event_name']) ? sanitize_text_field(wp_unslash($_POST['event_name'])) : '';
    $company  = isset($_POST['host_company']) ? sanitize_text_field(wp_unslash($_POST['host_company'])) : '';
    $datetime = isset($_POST['event_datetime']) ? sanitize_text_field(wp_unslash($_POST['event_datetime'])) : '';
    $location = isset($_POST['event_location']) ? sanitize_text_field(wp_unslash($_POST['event_location'])) : '';
    $desc     = isset($_POST['event_description']) ? sanitize_textarea_field(wp_unslash($_POST['event_description'])) : '';

    $name     = mb_substr($name, 0, 180);
    $company  = mb_substr($company, 0, 180);
    $location = mb_substr($location, 0, 180);
    $desc     = mb_substr($desc, 0, 4000);

    if ('' === $name || '' === $company || '' === $datetime || '' === $location) {
        hta_submission_error(__('יש למלא את כל השדות החובה.', 'hta-landing'));
    }

    if (! hta_is_valid_israel_city($location)) {
        hta_submission_error(__('יש לבחור עיר או יישוב מתוך הרשימה.', 'hta-landing'));
    }

    $agree = ! empty($_POST['event_agree']);
    if (! $agree) {
        hta_submission_error(__('יש לאשר את התקנון, מדיניות הפרטיות ואת נכונות המידע.', 'hta-landing'));
    }

    if (! preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}/', $datetime)) {
        hta_submission_error(__('תאריך או שעה לא תקינים.', 'hta-landing'));
    }

    $date = substr($datetime, 0, 10);

    $post_id = wp_insert_post([
        'post_type'    => 'hta_event',
        'post_status'  => 'draft',
        'post_title'   => $name,
        'post_content' => $desc,
    ], true);

    if (is_wp_error($post_id) || ! $post_id) {
        hta_submission_error(__('לא ניתן לשמור את ההגשה.', 'hta-landing'), 500);
    }

    update_post_meta($post_id, '_hta_date', $date);
    update_post_meta($post_id, '_hta_datetime', $datetime);
    update_post_meta($post_id, '_hta_location', $location);
    update_post_meta($post_id, '_hta_host_company', $company);
    update_post_meta($post_id, '_hta_from_frontend', '1');

    hta_submission_success();
}
