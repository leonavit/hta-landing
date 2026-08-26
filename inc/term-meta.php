<?php
/**
 * Event category term meta — background color.
 *
 * @package HTA_Landing
 */

if (! defined('ABSPATH')) {
    exit;
}

function hta_event_cat_bg_meta_key(): string
{
    return '_hta_cat_bg';
}

/**
 * Background color for an event category term (hex or empty).
 */
function hta_event_cat_bg($term): string
{
    $term_id = 0;
    if ($term instanceof WP_Term) {
        $term_id = (int) $term->term_id;
    } elseif (is_numeric($term)) {
        $term_id = (int) $term;
    } elseif (is_string($term) && '' !== $term) {
        $found = get_term_by('slug', $term, 'hta_event_cat');
        if ($found && ! is_wp_error($found)) {
            $term_id = (int) $found->term_id;
        }
    }

    if ($term_id <= 0) {
        return '';
    }

    $color = (string) get_term_meta($term_id, hta_event_cat_bg_meta_key(), true);
    return sanitize_hex_color($color) ?: '';
}

/**
 * Contrasting text color for a hex background.
 */
function hta_contrast_text_color(string $hex): string
{
    $hex = ltrim($hex, '#');
    if (3 === strlen($hex)) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    if (6 !== strlen($hex)) {
        return '#fff';
    }

    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

    return $yiq >= 160 ? '#080c1f' : '#fff';
}

function hta_event_cat_add_color_field(): void
{
    ?>
    <div class="form-field term-hta-cat-bg-wrap">
        <label for="hta_cat_bg"><?php esc_html_e('צבע רקע', 'hta-landing'); ?></label>
        <input type="text" name="hta_cat_bg" id="hta_cat_bg" value="" class="hta-term-color-field" data-default-color="#2563eb">
        <p class="description"><?php esc_html_e('צבע רקע לתג הקטגוריה באתר.', 'hta-landing'); ?></p>
    </div>
    <?php
}
add_action('hta_event_cat_add_form_fields', 'hta_event_cat_add_color_field');

function hta_event_cat_edit_color_field(WP_Term $term): void
{
    $color = hta_event_cat_bg($term);
    ?>
    <tr class="form-field term-hta-cat-bg-wrap">
        <th scope="row">
            <label for="hta_cat_bg"><?php esc_html_e('צבע רקע', 'hta-landing'); ?></label>
        </th>
        <td>
            <input type="text" name="hta_cat_bg" id="hta_cat_bg" value="<?php echo esc_attr($color); ?>" class="hta-term-color-field" data-default-color="#2563eb">
            <p class="description"><?php esc_html_e('צבע רקע לתג הקטגוריה באתר.', 'hta-landing'); ?></p>
        </td>
    </tr>
    <?php
}
add_action('hta_event_cat_edit_form_fields', 'hta_event_cat_edit_color_field');

function hta_event_cat_save_color(int $term_id): void
{
    if (! current_user_can('manage_categories')) {
        return;
    }

    if (! isset($_POST['hta_cat_bg'])) {
        return;
    }

    $color = sanitize_hex_color(wp_unslash((string) $_POST['hta_cat_bg']));
    if ($color) {
        update_term_meta($term_id, hta_event_cat_bg_meta_key(), $color);
    } else {
        delete_term_meta($term_id, hta_event_cat_bg_meta_key());
    }
}
add_action('created_hta_event_cat', 'hta_event_cat_save_color');
add_action('edited_hta_event_cat', 'hta_event_cat_save_color');

function hta_event_cat_color_column(array $columns): array
{
    $columns['hta_cat_bg'] = __('צבע רקע', 'hta-landing');
    return $columns;
}
add_filter('manage_edit-hta_event_cat_columns', 'hta_event_cat_color_column');

function hta_event_cat_color_column_content(string $content, string $column, int $term_id): string
{
    if ('hta_cat_bg' !== $column) {
        return $content;
    }

    $color = hta_event_cat_bg($term_id);
    if ('' === $color) {
        return '—';
    }

    return sprintf(
        '<span style="display:inline-block;width:1.25rem;height:1.25rem;border-radius:3px;border:1px solid #c3c4c7;background:%1$s;vertical-align:middle;" title="%1$s"></span> <code>%1$s</code>',
        esc_attr($color)
    );
}
add_filter('manage_hta_event_cat_custom_column', 'hta_event_cat_color_column_content', 10, 3);

function hta_event_cat_admin_assets(string $hook): void
{
    if (! in_array($hook, ['edit-tags.php', 'term.php'], true)) {
        return;
    }

    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (! $screen || 'hta_event_cat' !== $screen->taxonomy) {
        return;
    }

    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    wp_add_inline_script(
        'wp-color-picker',
        "jQuery(function($){ $('.hta-term-color-field').wpColorPicker(); });"
    );
}
add_action('admin_enqueue_scripts', 'hta_event_cat_admin_assets');
