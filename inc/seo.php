<?php
/**
 * SEO meta (pages) + tracking pixels output.
 *
 * @package HTA_Landing
 */

if (! defined('ABSPATH')) {
    exit;
}

function hta_seo_title_meta_key(): string
{
    return '_hta_seo_title';
}

function hta_seo_description_meta_key(): string
{
    return '_hta_seo_description';
}

function hta_seo_og_image_meta_key(): string
{
    return '_hta_seo_og_image';
}

/**
 * Allow storing tracking snippets (scripts / meta) for admins.
 */
function hta_sanitize_tracking_code($value): string
{
    $value = (string) $value;
    $value = str_replace("\0", '', $value);
    $value = preg_replace('#<\?(?:php)?|\?>#i', '', $value) ?? $value;
    return trim($value);
}

function hta_add_seo_meta_box(): void
{
    add_meta_box(
        'hta_seo',
        __('SEO — כותרת, תיאור ו־OG Image', 'hta-landing'),
        'hta_render_seo_metabox',
        'page',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'hta_add_seo_meta_box');

function hta_enqueue_seo_metabox_assets(string $hook): void
{
    if (! in_array($hook, ['post.php', 'post-new.php'], true)) {
        return;
    }

    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (! $screen || 'page' !== $screen->post_type) {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_script(
        'hta-seo-metabox',
        HTA_URI . '/assets/js/seo-metabox.js',
        ['jquery'],
        HTA_VERSION,
        true
    );
}
add_action('admin_enqueue_scripts', 'hta_enqueue_seo_metabox_assets');

function hta_render_seo_metabox(WP_Post $post): void
{
    wp_nonce_field('hta_save_seo', 'hta_seo_nonce');
    $title     = hta_meta($post->ID, hta_seo_title_meta_key());
    $desc      = hta_meta($post->ID, hta_seo_description_meta_key());
    $og_id     = (int) get_post_meta($post->ID, hta_seo_og_image_meta_key(), true);
    $og_url    = $og_id > 0 ? (string) wp_get_attachment_image_url($og_id, 'medium') : '';
    ?>
    <p>
        <label for="hta_seo_title"><strong><?php esc_html_e('Title (כותרת לדפדפן / SEO)', 'hta-landing'); ?></strong></label><br>
        <input type="text" class="widefat" id="hta_seo_title" name="hta_seo_title" value="<?php echo esc_attr($title); ?>" maxlength="70" placeholder="<?php echo esc_attr(get_the_title($post)); ?>">
    </p>
    <p>
        <label for="hta_seo_description"><strong><?php esc_html_e('Description (תיאור מטא)', 'hta-landing'); ?></strong></label><br>
        <textarea class="widefat" id="hta_seo_description" name="hta_seo_description" rows="3" maxlength="320"><?php echo esc_textarea($desc); ?></textarea>
    </p>
    <p class="description"><?php esc_html_e('אם ריק — ייעשה שימוש בכותרת העמוד / תיאור האתר.', 'hta-landing'); ?></p>

    <hr style="margin:1.25rem 0;">

    <p>
        <strong><?php esc_html_e('OG Image (תמונת שיתוף)', 'hta-landing'); ?></strong><br>
        <span class="description"><?php esc_html_e('תמונה לשיתוף ברשתות (Open Graph / Twitter). מומלץ 1200×630.', 'hta-landing'); ?></span>
    </p>
    <div class="hta-seo-og-image" style="display:flex;gap:12px;align-items:flex-start;flex-wrap:wrap;">
        <div class="hta-seo-og-preview" style="width:180px;min-height:94px;background:#f0f0f1;border:1px solid #c3c4c7;display:flex;align-items:center;justify-content:center;overflow:hidden;">
            <?php if ($og_url) : ?>
                <img src="<?php echo esc_url($og_url); ?>" alt="" style="max-width:100%;height:auto;display:block;">
            <?php else : ?>
                <span style="color:#646970;font-size:12px;"><?php esc_html_e('אין תמונה', 'hta-landing'); ?></span>
            <?php endif; ?>
        </div>
        <div>
            <input type="hidden" id="hta_seo_og_image" name="hta_seo_og_image" value="<?php echo esc_attr((string) $og_id); ?>">
            <p style="margin:0 0 8px;">
                <button type="button" class="button" id="hta_seo_og_image_select"><?php esc_html_e('בחירת תמונה', 'hta-landing'); ?></button>
                <button type="button" class="button" id="hta_seo_og_image_remove" <?php disabled($og_id <= 0); ?>><?php esc_html_e('הסרה', 'hta-landing'); ?></button>
            </p>
        </div>
    </div>
    <?php
}

function hta_save_seo_meta(int $post_id): void
{
    if (! isset($_POST['hta_seo_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['hta_seo_nonce'])), 'hta_save_seo')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if ('page' !== get_post_type($post_id) || ! current_user_can('edit_post', $post_id)) {
        return;
    }

    $title = isset($_POST['hta_seo_title']) ? sanitize_text_field(wp_unslash($_POST['hta_seo_title'])) : '';
    $desc  = isset($_POST['hta_seo_description']) ? sanitize_textarea_field(wp_unslash($_POST['hta_seo_description'])) : '';
    $og_id = isset($_POST['hta_seo_og_image']) ? absint($_POST['hta_seo_og_image']) : 0;

    update_post_meta($post_id, hta_seo_title_meta_key(), $title);
    update_post_meta($post_id, hta_seo_description_meta_key(), $desc);

    if ($og_id > 0 && wp_attachment_is_image($og_id)) {
        update_post_meta($post_id, hta_seo_og_image_meta_key(), $og_id);
    } else {
        delete_post_meta($post_id, hta_seo_og_image_meta_key());
    }
}
add_action('save_post_page', 'hta_save_seo_meta');

/**
 * Current queried page ID for front page / singular page SEO.
 */
function hta_seo_context_post_id(): int
{
    if (is_front_page()) {
        $page_id = (int) get_option('page_on_front');
        if ($page_id > 0) {
            return $page_id;
        }
    }

    if (is_singular('page')) {
        return (int) get_queried_object_id();
    }

    return 0;
}

/**
 * Homepage SEO from Customizer (when front is not a WP page, or as fallback).
 *
 * @return array{title: string, description: string, og_image: int}
 */
function hta_home_seo(): array
{
    return [
        'title'       => trim((string) hta_mod('hta_home_seo_title', '')),
        'description' => trim((string) hta_mod('hta_home_seo_description', '')),
        'og_image'    => absint(hta_mod('hta_home_seo_og_image', 0)),
    ];
}

/**
 * Absolute image URL for an attachment ID (full size).
 */
function hta_seo_attachment_url(int $attachment_id): string
{
    if ($attachment_id <= 0 || ! wp_attachment_is_image($attachment_id)) {
        return '';
    }

    $url = wp_get_attachment_image_url($attachment_id, 'full');
    return is_string($url) ? $url : '';
}

/**
 * Resolve OG image attachment ID for the current view.
 */
function hta_resolved_seo_og_image_id(): int
{
    $home_og = absint(hta_mod('hta_home_seo_og_image', 0));

    if (is_front_page()) {
        $page_id = (int) get_option('page_on_front');
        if ($page_id > 0) {
            $from_page = absint(get_post_meta($page_id, hta_seo_og_image_meta_key(), true));
            if ($from_page > 0 && wp_attachment_is_image($from_page)) {
                return $from_page;
            }
        }

        if ($home_og > 0 && wp_attachment_is_image($home_og)) {
            return $home_og;
        }

        if ($page_id > 0 && has_post_thumbnail($page_id)) {
            $thumb = (int) get_post_thumbnail_id($page_id);
            if ($thumb > 0 && wp_attachment_is_image($thumb)) {
                return $thumb;
            }
        }

        return 0;
    }

    $post_id = hta_seo_context_post_id();
    if ($post_id > 0) {
        $from_page = absint(get_post_meta($post_id, hta_seo_og_image_meta_key(), true));
        if ($from_page > 0 && wp_attachment_is_image($from_page)) {
            return $from_page;
        }

        if (has_post_thumbnail($post_id)) {
            $thumb = (int) get_post_thumbnail_id($post_id);
            if ($thumb > 0 && wp_attachment_is_image($thumb)) {
                return $thumb;
            }
        }
    }

    if ($home_og > 0 && wp_attachment_is_image($home_og)) {
        return $home_og;
    }

    return 0;
}

/**
 * Public ASCII OG image URL (safe for WhatsApp / crawlers).
 * Avoids Hebrew filenames and localhost uploads URLs.
 */
function hta_seo_og_public_url(int $attachment_id): string
{
    if ($attachment_id <= 0 || ! wp_attachment_is_image($attachment_id)) {
        return '';
    }

    $base = hta_seo_canonical_base();
    if ('' === $base) {
        $base = untrailingslashit(home_url('/'));
    }

    return add_query_arg('hta_og', $attachment_id, trailingslashit($base));
}

/**
 * Resolve OG image URL for the current view.
 */
function hta_resolved_seo_og_image_url(): string
{
    return hta_seo_og_public_url(hta_resolved_seo_og_image_id());
}

/**
 * Stream OG image by attachment ID via ?hta_og={id}.
 */
function hta_serve_og_image_request(): void
{
    if (! isset($_GET['hta_og'])) {
        return;
    }

    $attachment_id = absint(wp_unslash($_GET['hta_og']));
    if ($attachment_id <= 0 || ! wp_attachment_is_image($attachment_id)) {
        status_header(404);
        exit;
    }

    $path = get_attached_file($attachment_id);
    if (! is_string($path) || '' === $path || ! is_readable($path)) {
        status_header(404);
        exit;
    }

    $mime = get_post_mime_type($attachment_id);
    if (! is_string($mime) || '' === $mime) {
        $mime = 'image/jpeg';
    }

    $size = filesize($path);
    $mtime = filemtime($path);

    status_header(200);
    header('Content-Type: ' . $mime);
    header('Content-Disposition: inline; filename="og-' . $attachment_id . '"');
    header('Cache-Control: public, max-age=604800');
    header('X-Content-Type-Options: nosniff');
    if (false !== $size) {
        header('Content-Length: ' . (string) $size);
    }
    if (false !== $mtime) {
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mtime) . ' GMT');
    }

    // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile -- binary image stream.
    readfile($path);
    exit;
}
add_action('template_redirect', 'hta_serve_og_image_request', 0);

/**
 * Resolve SEO title for the current view.
 */
function hta_resolved_seo_title(): string
{
    if (is_front_page()) {
        $page_id = (int) get_option('page_on_front');
        if ($page_id > 0) {
            $from_page = hta_meta($page_id, hta_seo_title_meta_key());
            if ('' !== $from_page) {
                return $from_page;
            }
        }
        $home = hta_home_seo();
        if ('' !== $home['title']) {
            return $home['title'];
        }
        return '';
    }

    $post_id = hta_seo_context_post_id();
    if ($post_id > 0) {
        return hta_meta($post_id, hta_seo_title_meta_key());
    }

    return '';
}

/**
 * Resolve SEO description for the current view.
 */
function hta_resolved_seo_description(): string
{
    if (is_front_page()) {
        $page_id = (int) get_option('page_on_front');
        if ($page_id > 0) {
            $from_page = hta_meta($page_id, hta_seo_description_meta_key());
            if ('' !== $from_page) {
                return $from_page;
            }
        }
        $home = hta_home_seo();
        if ('' !== $home['description']) {
            return $home['description'];
        }
        return '';
    }

    $post_id = hta_seo_context_post_id();
    if ($post_id <= 0) {
        return '';
    }

    $description = hta_meta($post_id, hta_seo_description_meta_key());
    if ('' !== $description) {
        return $description;
    }

    $excerpt = get_the_excerpt($post_id);
    if (is_string($excerpt) && '' !== trim($excerpt)) {
        return wp_strip_all_tags($excerpt);
    }

    return '';
}

/**
 * @param array<string, string> $parts
 * @return array<string, string>
 */
function hta_filter_document_title_parts(array $parts): array
{
    $custom = hta_resolved_seo_title();
    if ('' === $custom) {
        return $parts;
    }

    $parts['title'] = $custom;
    if (isset($parts['tagline'])) {
        unset($parts['tagline']);
    }
    if (isset($parts['site'])) {
        unset($parts['site']);
    }

    return $parts;
}
add_filter('document_title_parts', 'hta_filter_document_title_parts', 20);

function hta_output_meta_description(): void
{
    $description = hta_resolved_seo_description();

    if ('' === $description) {
        $description = (string) get_bloginfo('description', 'display');
    }

    $description = trim(preg_replace('/\s+/u', ' ', $description) ?? '');
    if ('' === $description) {
        return;
    }

    echo '<meta name="description" content="' . esc_attr(mb_substr($description, 0, 320)) . '">' . "\n";
}
add_action('wp_head', 'hta_output_meta_description', 1);

/**
 * Open Graph + Twitter image (and related social tags).
 */
function hta_output_open_graph_tags(): void
{
    $url = hta_seo_canonical_url();
    if ('' === $url) {
        $url = home_url('/');
    }

    $title = hta_resolved_seo_title();
    if ('' === $title) {
        $title = wp_get_document_title();
    }

    $description = hta_resolved_seo_description();
    if ('' === $description) {
        $description = (string) get_bloginfo('description', 'display');
    }
    $description = trim(preg_replace('/\s+/u', ' ', $description) ?? '');

    $image_id = hta_resolved_seo_og_image_id();
    $image    = hta_seo_og_public_url($image_id);
    $type     = is_front_page() ? 'website' : 'article';

    echo '<meta property="og:type" content="' . esc_attr($type) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    if ('' !== $title) {
        echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    }
    if ('' !== $description) {
        echo '<meta property="og:description" content="' . esc_attr(mb_substr($description, 0, 320)) . '">' . "\n";
    }
    if ('' !== $image) {
        echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
        echo '<meta property="og:image:secure_url" content="' . esc_url($image) . '">' . "\n";

        $meta = wp_get_attachment_metadata($image_id);
        if (is_array($meta)) {
            $width  = isset($meta['width']) ? absint($meta['width']) : 0;
            $height = isset($meta['height']) ? absint($meta['height']) : 0;
            if ($width > 0) {
                echo '<meta property="og:image:width" content="' . esc_attr((string) $width) . '">' . "\n";
            }
            if ($height > 0) {
                echo '<meta property="og:image:height" content="' . esc_attr((string) $height) . '">' . "\n";
            }
        }

        $mime = get_post_mime_type($image_id);
        if (is_string($mime) && '' !== $mime) {
            echo '<meta property="og:image:type" content="' . esc_attr($mime) . '">' . "\n";
        }
    }
    echo '<meta property="og:locale" content="he_IL">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name', 'display')) . '">' . "\n";

    echo '<meta name="twitter:card" content="' . esc_attr('' !== $image ? 'summary_large_image' : 'summary') . '">' . "\n";
    if ('' !== $title) {
        echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    }
    if ('' !== $description) {
        echo '<meta name="twitter:description" content="' . esc_attr(mb_substr($description, 0, 320)) . '">' . "\n";
    }
    if ('' !== $image) {
        echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";
    }
}
add_action('wp_head', 'hta_output_open_graph_tags', 1);

/**
 * Public site URL used for canonical (production subdomain).
 */
function hta_seo_canonical_base(): string
{
    $base = trim((string) hta_mod('hta_seo_canonical_base', 'https://hiweek.hta.org.il'));
    if ('' === $base) {
        $base = 'https://hiweek.hta.org.il';
    }
    return untrailingslashit(esc_url_raw($base));
}

/**
 * Canonical URL for the current request on the public domain.
 */
function hta_seo_canonical_url(): string
{
    $base = hta_seo_canonical_base();

    if (is_front_page()) {
        return trailingslashit($base);
    }

    $path = '';
    if (is_singular()) {
        $permalink = get_permalink();
        if (is_string($permalink) && '' !== $permalink) {
            $path = (string) wp_make_link_relative($permalink);
        }
    } else {
        $request = isset($_SERVER['REQUEST_URI']) ? (string) wp_unslash($_SERVER['REQUEST_URI']) : '/';
        $path    = (string) wp_parse_url($request, PHP_URL_PATH);
    }

    $path = '/' . ltrim((string) $path, '/');
    if ('/' === $path) {
        return trailingslashit($base);
    }

    return $base . $path;
}

function hta_output_seo_head_tags(): void
{
    $canonical = hta_seo_canonical_url();
    if ('' !== $canonical) {
        echo '<!-- SEO Canonical Tag for Subdomain -->' . "\n";
        echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "\n";
    }

    $author = trim((string) hta_mod('hta_seo_author', 'איגוד ההיי-טק הישראלי'));
    if ('' !== $author) {
        echo '<meta name="author" content="' . esc_attr($author) . '">' . "\n";
    }

    $publisher = trim((string) hta_mod('hta_seo_publisher', 'איגוד ההיי-טק הישראלי'));
    if ('' !== $publisher) {
        echo '<meta name="publisher" content="' . esc_attr($publisher) . '">' . "\n";
    }

    $keywords = trim((string) hta_mod(
        'hta_seo_keywords',
        'שבוע ההייטק הישראלי, איגוד ההייטק, כנס הייטק 2026, אירועי טכנולוגיה'
    ));
    if ('' !== $keywords) {
        echo '<meta name="keywords" content="' . esc_attr($keywords) . '">' . "\n";
    }
}
add_action('wp_head', 'hta_output_seo_head_tags', 1);

/**
 * Prefer theme canonical over core default.
 */
function hta_filter_canonical_url(string $canonical_url, WP_Post $post): string
{
    unset($post);
    $custom = hta_seo_canonical_url();
    return '' !== $custom ? $custom : $canonical_url;
}
add_filter('get_canonical_url', 'hta_filter_canonical_url', 20, 2);

remove_action('wp_head', 'rel_canonical');

function hta_output_search_console_verification(): void
{
    $code = trim((string) hta_mod('hta_tracking_gsc', ''));
    if ('' === $code) {
        return;
    }

    // Full meta tag pasted, or bare content token.
    if (false !== stripos($code, '<meta')) {
        echo wp_kses(
            $code,
            [
                'meta' => [
                    'name'    => true,
                    'content' => true,
                ],
            ]
        ) . "\n";
        return;
    }

    echo '<meta name="google-site-verification" content="' . esc_attr($code) . '">' . "\n";
}
add_action('wp_head', 'hta_output_search_console_verification', 1);

function hta_output_gtm_head(): void
{
    $raw = trim((string) hta_mod('hta_tracking_gtm', ''));
    if ('' === $raw) {
        return;
    }

    if (preg_match('/^GTM-[A-Z0-9]+$/i', $raw)) {
        $id = esc_js($raw);
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted admin snippet built from ID.
        echo "<!-- Google Tag Manager -->\n<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{$id}');</script>\n<!-- End Google Tag Manager -->\n";
        return;
    }

    echo $raw . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- admin-managed tracking snippet.
}
add_action('wp_head', 'hta_output_gtm_head', 2);

function hta_output_ga_head(): void
{
    $raw = trim((string) hta_mod('hta_tracking_ga', ''));
    if ('' === $raw) {
        return;
    }

    if (preg_match('/^(G|UA|GT)-[A-Z0-9-]+$/i', $raw)) {
        $id = esc_js($raw);
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo "<!-- Google Analytics -->\n<script async src=\"https://www.googletagmanager.com/gtag/js?id={$id}\"></script>\n<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{$id}');</script>\n";
        return;
    }

    echo $raw . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action('wp_head', 'hta_output_ga_head', 3);

function hta_output_facebook_pixel_head(): void
{
    $raw = trim((string) hta_mod('hta_tracking_facebook', ''));
    if ('' === $raw) {
        return;
    }

    if (preg_match('/^\d{5,20}$/', $raw)) {
        $id = esc_js($raw);
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo "<!-- Facebook Pixel -->\n<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','{$id}');fbq('track','PageView');</script>\n<noscript><img height=\"1\" width=\"1\" style=\"display:none\" src=\"https://www.facebook.com/tr?id={$id}&ev=PageView&noscript=1\" alt=\"\"/></noscript>\n";
        return;
    }

    echo $raw . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action('wp_head', 'hta_output_facebook_pixel_head', 4);

function hta_output_gtm_body(): void
{
    $raw = trim((string) hta_mod('hta_tracking_gtm', ''));
    if ('' === $raw || ! preg_match('/^GTM-[A-Z0-9]+$/i', $raw)) {
        return;
    }

    $id = esc_attr($raw);
    echo '<!-- Google Tag Manager (noscript) -->' . "\n";
    echo '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . $id . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>' . "\n";
    echo '<!-- End Google Tag Manager (noscript) -->' . "\n";
}
add_action('wp_body_open', 'hta_output_gtm_body', 1);
