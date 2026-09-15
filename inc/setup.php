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

/**
 * Use Classic Editor (TinyMCE) for pages — not the block editor.
 */
function hta_disable_block_editor_for_pages(bool $use_block_editor, string $post_type): bool
{
    if ('page' === $post_type) {
        return false;
    }
    return $use_block_editor;
}
add_filter('use_block_editor_for_post_type', 'hta_disable_block_editor_for_pages', 10, 2);

/**
 * Keep classic editor when editing an individual page post as well.
 */
function hta_disable_block_editor_for_page_post(bool $use_block_editor, WP_Post $post): bool
{
    if ('page' === $post->post_type) {
        return false;
    }
    return $use_block_editor;
}
add_filter('use_block_editor_for_post', 'hta_disable_block_editor_for_page_post', 10, 2);

/**
 * Ensure pages keep the classic content editor.
 */
function hta_ensure_page_editor_support(): void
{
    add_post_type_support('page', 'editor');
    add_post_type_support('page', 'revisions');
    add_post_type_support('page', 'thumbnail');
}
add_action('init', 'hta_ensure_page_editor_support', 20);

/**
 * Clarify that the static front page template ignores post content.
 */
function hta_front_page_editor_notice(): void
{
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (! $screen || 'page' !== $screen->id) {
        return;
    }

    $post_id = isset($_GET['post']) ? absint($_GET['post']) : 0;
    if ($post_id <= 0) {
        return;
    }

    $front_id = (int) get_option('page_on_front');
    if ($front_id <= 0 || $front_id !== $post_id) {
        return;
    }

    echo '<div class="notice notice-info"><p>';
    echo esc_html__('עמוד זה מוגדר כעמוד הבית. התוכן באתר נשלט מתבנית הנחיתה (Customizer / חלקי תבנית) ולא מתוכן העורך שלמטה.', 'hta-landing');
    echo '</p></div>';
}
add_action('admin_notices', 'hta_front_page_editor_notice');

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
