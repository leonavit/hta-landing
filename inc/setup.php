<?php
/**
 * Theme supports, menus, image sizes.
 *
 * @package HTA_Landing
 */

if (! defined('ABSPATH')) {
    exit;
}

function hta_setup(): void
{
    load_theme_textdomain('hta-landing', HTA_DIR . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('custom-logo', [
        'height'      => 120,
        'width'       => 320,
        'flex-height' => true,
        'flex-width'  => true,
    ]);

    register_nav_menus([
        'primary' => __('תפריט ראשי', 'hta-landing'),
        'footer'  => __('תפריט פוטר', 'hta-landing'),
    ]);

    add_image_size('hta-topic', 800, 500, true);
    add_image_size('hta-event', 800, 450, true);
    add_image_size('hta-ambassador', 480, 560, true);
    add_image_size('hta-partner', 320, 120, false);
    add_image_size('hta-media', 800, 450, true);
}
add_action('after_setup_theme', 'hta_setup');

function hta_content_width(): void
{
    $GLOBALS['content_width'] = 1152;
}
add_action('after_setup_theme', 'hta_content_width', 0);

function hta_force_rtl_language_attributes(string $output): string
{
    if (false === stripos($output, 'dir=')) {
        $output .= ' dir="rtl"';
    }
    return $output;
}
add_filter('language_attributes', 'hta_force_rtl_language_attributes');
