<?php
/**
 * Custom post types and taxonomies — WordPress core only.
 *
 * @package HTA_Landing
 */

if (! defined('ABSPATH')) {
    exit;
}

function hta_register_cpts(): void
{
    register_post_type('hta_event', [
        'labels' => [
            'name'          => 'אירועים',
            'singular_name' => 'אירוע',
            'add_new_item'  => 'הוספת אירוע',
            'edit_item'     => 'עריכת אירוע',
            'search_items'  => 'חיפוש אירועים',
            'not_found'     => 'לא נמצאו אירועים',
            'menu_name'     => 'אירועים',
        ],
        'public'       => true,
        'has_archive'  => false,
        'show_in_rest' => false,
        'menu_icon'    => 'dashicons-calendar-alt',
        'supports'     => ['title', 'editor', 'thumbnail'],
        'rewrite'      => ['slug' => 'events'],
    ]);

    register_taxonomy('hta_event_cat', 'hta_event', [
        'labels' => [
            'name'          => 'קטגוריות אירוע',
            'singular_name' => 'קטגוריית אירוע',
            'menu_name'     => 'קטגוריות',
        ],
        'public'            => true,
        'hierarchical'      => true,
        'show_admin_column' => true,
        'show_in_rest'      => false,
        'meta_box_cb'       => false,
        'rewrite'           => ['slug' => 'event-category'],
    ]);

    register_post_type('hta_ambassador', [
        'labels' => [
            'name'          => 'שגרירי השבוע',
            'singular_name' => 'שגריר',
            'add_new_item'  => 'הוספת שגריר',
            'edit_item'     => 'עריכת שגריר',
            'menu_name'     => 'שגרירי השבוע',
        ],
        'public'       => true,
        'has_archive'  => false,
        'show_in_rest' => false,
        'menu_icon'    => 'dashicons-groups',
        'supports'     => ['title', 'thumbnail'],
        'rewrite'      => ['slug' => 'ambassadors'],
    ]);

    register_post_type('hta_partner', [
        'labels' => [
            'name'          => 'שותפים מובילים',
            'singular_name' => 'שותף',
            'add_new_item'  => 'הוספת שותף',
            'edit_item'     => 'עריכת שותף',
            'menu_name'     => 'שותפים מובילים',
        ],
        'public'       => true,
        'has_archive'  => false,
        'show_in_rest' => false,
        'menu_icon'    => 'dashicons-building',
        'supports'     => ['title', 'thumbnail'],
        'rewrite'      => ['slug' => 'partners'],
    ]);

    register_post_type('hta_media', [
        'labels' => [
            'name'          => 'מן התקשורת',
            'singular_name' => 'פריט תקשורת',
            'add_new_item'  => 'הוספת פריט תקשורת',
            'edit_item'     => 'עריכת פריט תקשורת',
            'menu_name'     => 'מן התקשורת',
        ],
        'public'       => true,
        'has_archive'  => false,
        'show_in_rest' => false,
        'menu_icon'    => 'dashicons-megaphone',
        'supports'     => ['title', 'excerpt', 'thumbnail'],
        'rewrite'      => ['slug' => 'press'],
    ]);
}
add_action('init', 'hta_register_cpts');

function hta_unregister_legacy_submission_cpt(): void
{
    if (post_type_exists('event_submission')) {
        unregister_post_type('event_submission');
    }
}
add_action('init', 'hta_unregister_legacy_submission_cpt', 20);

function hta_flush_rewrites_on_switch(): void
{
    hta_register_cpts();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'hta_flush_rewrites_on_switch');
