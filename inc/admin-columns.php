<?php
/**
 * Admin list columns for native CPTs.
 *
 * @package HTA_Landing
 */

if (! defined('ABSPATH')) {
    exit;
}

function hta_event_columns(array $columns): array
{
    $columns['hta_date']         = 'תאריך';
    $columns['hta_location']     = 'מיקום';
    $columns['hta_host_company'] = 'חברה מארחת';
    return $columns;
}
add_filter('manage_hta_event_posts_columns', 'hta_event_columns');

function hta_ambassador_columns(array $columns): array
{
    $columns['hta_role'] = 'תפקיד';
    return $columns;
}
add_filter('manage_hta_ambassador_posts_columns', 'hta_ambassador_columns');

function hta_render_custom_columns(string $column, int $post_id): void
{
    $map = [
        'hta_date'         => '_hta_date',
        'hta_location'     => '_hta_location',
        'hta_role'         => '_hta_role',
        'hta_host_company' => '_hta_host_company',
    ];

    if (! isset($map[$column])) {
        return;
    }

    echo esc_html(hta_meta($post_id, $map[$column]));
}
add_action('manage_hta_event_posts_custom_column', 'hta_render_custom_columns', 10, 2);
add_action('manage_hta_ambassador_posts_custom_column', 'hta_render_custom_columns', 10, 2);
