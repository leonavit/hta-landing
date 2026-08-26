<?php
/**
 * Shared helpers.
 *
 * @package HTA_Landing
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Request origin for local LAN preview (iPhone etc.).
 * Lets the site work via http://192.168.x.x:8081 without rewriting DB URLs.
 */
function hta_request_origin(): string
{
    if (is_admin() || wp_doing_cron() || (defined('WP_CLI') && WP_CLI)) {
        return '';
    }

    $host = isset($_SERVER['HTTP_HOST']) ? strtolower((string) wp_unslash($_SERVER['HTTP_HOST'])) : '';
    if ('' === $host) {
        return '';
    }

    $hostname = preg_replace('/:\d+$/', '', $host);
    $is_local = (
        'localhost' === $hostname
        || '127.0.0.1' === $hostname
        || (bool) preg_match('/^(10\.|192\.168\.|172\.(1[6-9]|2\d|3[0-1])\.)/', $hostname)
    );

    if (! $is_local) {
        return '';
    }

    $https = (! empty($_SERVER['HTTPS']) && 'off' !== $_SERVER['HTTPS'])
        || (isset($_SERVER['SERVER_PORT']) && '443' === (string) $_SERVER['SERVER_PORT']);

    return ($https ? 'https://' : 'http://') . $host;
}

function hta_filter_site_url($url)
{
    $origin = hta_request_origin();
    if ('' === $origin) {
        return $url;
    }

    return $origin;
}
add_filter('option_home', 'hta_filter_site_url');
add_filter('option_siteurl', 'hta_filter_site_url');

function hta_filter_upload_dir(array $uploads): array
{
    $origin = hta_request_origin();
    if ('' === $origin) {
        return $uploads;
    }

    foreach (['url', 'baseurl'] as $key) {
        if (! empty($uploads[$key])) {
            $uploads[$key] = preg_replace('#^https?://[^/]+#', $origin, $uploads[$key]);
        }
    }

    return $uploads;
}
add_filter('upload_dir', 'hta_filter_upload_dir');

function hta_rewrite_local_url(string $url): string
{
    $origin = hta_request_origin();
    if ('' === $origin || '' === $url) {
        return $url;
    }

    return (string) preg_replace('#^https?://(?:localhost|127\.0\.0\.1)(?::\d+)?#i', $origin, $url);
}

function hta_filter_attachment_url(string $url): string
{
    return hta_rewrite_local_url($url);
}
add_filter('wp_get_attachment_url', 'hta_filter_attachment_url');
add_filter('content_url', 'hta_filter_attachment_url');
add_filter('script_loader_src', 'hta_filter_attachment_url');
add_filter('style_loader_src', 'hta_filter_attachment_url');
add_filter('theme_file_uri', 'hta_filter_attachment_url');
add_filter('plugins_url', 'hta_filter_attachment_url');

function hta_mod(string $key, $default = '')
{
    return get_theme_mod($key, $default);
}

function hta_hero_default_video_url(): string
{
    return HTA_URI . '/assets/video/hero-bg.mp4';
}

/**
 * @return array{type:string,src:string,youtube:string}
 */
function hta_hero_video(): array
{
    $stored = get_theme_mod('hta_hero_video_source', null);
    if (null === $stored || '' === $stored) {
        // Backward compatible: previous upload without source setting.
        $source = absint(get_theme_mod('hta_hero_video', 0)) ? 'upload' : 'default';
    } else {
        $source = sanitize_key((string) $stored);
        if (! in_array($source, ['default', 'upload', 'url'], true)) {
            $source = 'default';
        }
    }

    $out = [
        'type'    => 'file',
        'src'     => hta_hero_default_video_url(),
        'youtube' => '',
    ];

    if ('upload' === $source) {
        $id = absint(hta_mod('hta_hero_video', 0));
        if ($id) {
            $url = wp_get_attachment_url($id);
            if ($url) {
                $out['src'] = $url;
                return $out;
            }
        }
        return $out;
    }

    if ('url' === $source) {
        $url = trim((string) hta_mod('hta_hero_video_url', ''));
        if ('' === $url) {
            return $out;
        }

        $yt = hta_youtube_id($url);
        if ($yt) {
            $out['type']    = 'youtube';
            $out['youtube'] = $yt;
            $out['src']     = '';
            return $out;
        }

        $clean = esc_url_raw($url);
        if ($clean) {
            $out['src'] = $clean;
        }
        return $out;
    }

    return $out;
}

function hta_hero_video_url(): string
{
    $video = hta_hero_video();
    return 'file' === $video['type'] ? $video['src'] : '';
}

function hta_color(string $key, string $default): string
{
    $value = (string) get_theme_mod($key, $default);
    return sanitize_hex_color($value) ?: $default;
}

/**
 * Allowed linear-gradient directions for hero text fills.
 *
 * @return array<string, string>
 */
function hta_text_fill_directions(): array
{
    return [
        'to left'         => __('ימין ← שמאל', 'hta-landing'),
        'to right'        => __('שמאל ← ימין', 'hta-landing'),
        'to bottom'       => __('למטה', 'hta-landing'),
        'to top'          => __('למעלה', 'hta-landing'),
        '135deg'          => __('אלכסון 135°', 'hta-landing'),
        '45deg'           => __('אלכסון 45°', 'hta-landing'),
        'to bottom left'  => __('אלכסון למטה-שמאל', 'hta-landing'),
        'to bottom right' => __('אלכסון למטה-ימין', 'hta-landing'),
    ];
}

/**
 * Solid or 3-stop gradient fill settings for a text element.
 *
 * @return array{mode: string, color: string, gradient: string, direction: string}
 */
function hta_text_fill(string $prefix, string $default = '#ffffff'): array
{
    $mode = sanitize_key((string) get_theme_mod("{$prefix}_color_mode", 'solid'));
    if (! in_array($mode, ['solid', 'gradient'], true)) {
        $mode = 'solid';
    }

    $directions = hta_text_fill_directions();
    $direction  = (string) get_theme_mod("{$prefix}_gradient_dir", 'to left');
    if (! isset($directions[$direction])) {
        $direction = 'to left';
    }

    $c1 = hta_color("{$prefix}_color_1", $default);
    $c2 = hta_color("{$prefix}_color_2", $default);
    $c3 = hta_color("{$prefix}_color_3", $default);

    return [
        'mode'      => $mode,
        'color'     => $c1,
        'direction' => $direction,
        'gradient'  => sprintf('linear-gradient(%s, %s 0%%, %s 50%%, %s 100%%)', $direction, $c1, $c2, $c3),
    ];
}

/**
 * CSS class for text fill mode.
 */
function hta_text_fill_class(string $prefix): string
{
    $fill = hta_text_fill($prefix);
    return 'gradient' === $fill['mode'] ? 'is-fill-gradient' : 'is-fill-solid';
}

/**
 * Shadow values from Customizer (offset-x, offset-y, blur, color).
 *
 * @return array{shadow: string, filter: string}
 */
function hta_text_shadow(string $prefix, array $defaults = []): array
{
    $defaults = array_merge(
        [
            'enabled' => true,
            'x'       => -1,
            'y'       => 2,
            'blur'    => 3,
            'color'   => '#000000',
        ],
        $defaults
    );

    $enabled = (bool) get_theme_mod("{$prefix}_shadow_enabled", $defaults['enabled']);
    if (! $enabled) {
        return [
            'shadow' => 'none',
            'filter' => 'none',
        ];
    }

    $x     = (int) get_theme_mod("{$prefix}_shadow_x", $defaults['x']);
    $y     = (int) get_theme_mod("{$prefix}_shadow_y", $defaults['y']);
    $blur  = max(0, (int) get_theme_mod("{$prefix}_shadow_blur", $defaults['blur']));
    $color = hta_color("{$prefix}_shadow_color", $defaults['color']);
    $value = sprintf('%dpx %dpx %dpx %s', $x, $y, $blur, $color);

    return [
        'shadow' => $value,
        // drop-shadow follows glyph alpha — required when background-clip:text makes fill transparent.
        'filter' => sprintf('drop-shadow(%s)', $value),
    ];
}

function hta_meta(int $post_id, string $key, $default = ''): string
{
    $value = get_post_meta($post_id, $key, true);
    return ('' === $value || false === $value) ? (string) $default : (string) $value;
}

function hta_youtube_id(string $url): string
{
    if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/))([A-Za-z0-9_-]{11})~', $url, $matches)) {
        return $matches[1];
    }
    return '';
}

function hta_event_search_blob(WP_Post $post): string
{
    $location = hta_meta($post->ID, '_hta_location');
    $terms    = wp_get_post_terms($post->ID, 'hta_event_cat', ['fields' => 'names']);
    $parts    = [
        $post->post_title,
        wp_strip_all_tags($post->post_content),
        $location,
        is_wp_error($terms) ? '' : implode(' ', $terms),
    ];
    return mb_strtolower(implode(' ', $parts));
}

/**
 * Enabled footer social links for Customizer-managed networks.
 *
 * @return array<int, array{slug:string,label:string,url:string}>
 */
function hta_footer_social_links(): array
{
    $defaults = [
        'linkedin'  => [
            'label' => 'LinkedIn',
            'url'   => 'https://www.linkedin.com/company/israeli-hi-tech-association?originalSubdomain=il',
        ],
        'facebook'  => [
            'label' => 'Facebook',
            'url'   => 'https://www.facebook.com/IsraeliHighTechAssociation/',
        ],
        'instagram' => [
            'label' => 'Instagram',
            'url'   => 'https://www.instagram.com/israeli_high_tech_association/',
        ],
    ];

    $links = [];
    foreach ($defaults as $slug => $network) {
        if (! hta_mod("hta_social_{$slug}_enable", true)) {
            continue;
        }
        $url = esc_url_raw((string) hta_mod("hta_social_{$slug}_url", $network['url']));
        if ('' === $url) {
            continue;
        }
        $links[] = [
            'slug'  => $slug,
            'label' => $network['label'],
            'url'   => $url,
        ];
    }

    return $links;
}

function hta_social_icon_svg(string $slug): string
{
    $icons = [
        'linkedin'  => '<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12" rx="0"/><circle cx="4" cy="4" r="2"/></svg>',
        'facebook'  => '<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>',
        'instagram' => '<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>',
    ];

    return $icons[$slug] ?? '';
}

/**
 * Allow "#" (and other relative/hash links) that esc_url_raw would strip.
 */
function hta_sanitize_link(string $url): string
{
    $url = trim($url);
    if ('' === $url) {
        return '';
    }
    if ('#' === $url || 0 === strpos($url, '#')) {
        return sanitize_text_field($url);
    }

    $clean = esc_url_raw($url);
    return $clean ?: sanitize_text_field($url);
}

function hta_esc_link(string $url): string
{
    $url = trim($url);
    if ('' === $url) {
        return '';
    }
    if ('#' === $url || 0 === strpos($url, '#')) {
        return esc_attr($url);
    }

    $clean = esc_url($url);
    return $clean ?: esc_attr($url);
}

/**
 * Permalink for a theme page by slug.
 */
function hta_page_url(string $slug): string
{
    $page = get_page_by_path($slug);
    if ($page instanceof WP_Post) {
        return get_permalink($page);
    }

    return home_url('/' . trim($slug, '/') . '/');
}

/**
 * Animated hamburger / close icon (Uiverse tall-swan-6).
 */
function hta_render_hamburger_icon(string $uid = 'nav'): void
{
    $grad_id = 'hta-hamburger-grad-' . sanitize_html_class($uid);
    ?>
    <span class="hta-hamburger" aria-hidden="true">
        <svg viewBox="0 0 32 32" focusable="false" aria-hidden="true">
            <defs>
                <linearGradient id="<?php echo esc_attr($grad_id); ?>" gradientUnits="userSpaceOnUse" x1="16" y1="0" x2="16" y2="32">
                    <stop offset="0%" stop-color="#22d3ee"></stop>
                    <stop offset="100%" stop-color="#c295e9"></stop>
                </linearGradient>
            </defs>
            <path class="line line-top-bottom" fill="none" stroke="url(#<?php echo esc_attr($grad_id); ?>)" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M27 10 13 10C10.8 10 9 8.2 9 6 9 3.5 10.8 2 13 2 15.2 2 17 3.8 17 6L17 26C17 28.2 18.8 30 21 30 23.2 30 25 28.2 25 26 25 23.8 23.2 22 21 22L7 22"></path>
            <path class="line" fill="none" stroke="url(#<?php echo esc_attr($grad_id); ?>)" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M7 16 27 16"></path>
        </svg>
    </span>
    <?php
}
