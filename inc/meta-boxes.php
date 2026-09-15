<?php
/**
 * Native meta boxes. No ACF.
 *
 * @package HTA_Landing
 */

if (! defined('ABSPATH')) {
    exit;
}

function hta_add_meta_boxes(): void
{
    add_meta_box('hta_event_details', 'פרטי אירוע', 'hta_render_event_metabox', 'hta_event', 'normal', 'high');
    add_meta_box('hta_ambassador_details', 'פרטי שגריר', 'hta_render_ambassador_metabox', 'hta_ambassador', 'normal', 'high');
    add_meta_box('hta_partner_details', 'פרטי שותף', 'hta_render_partner_metabox', 'hta_partner', 'normal', 'high');
    add_meta_box('hta_media_details', 'פרטי תקשורת', 'hta_render_media_metabox', 'hta_media', 'normal', 'high');
}
add_action('add_meta_boxes', 'hta_add_meta_boxes');

function hta_event_admin_assets(string $hook): void
{
    if (! in_array($hook, ['post.php', 'post-new.php'], true)) {
        return;
    }

    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (! $screen || 'hta_event' !== $screen->post_type) {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_script(
        'hta-event-metabox',
        HTA_URI . '/assets/js/event-metabox.js',
        ['jquery'],
        HTA_VERSION,
        true
    );
    wp_add_inline_style(
        'wp-admin',
        '.hta-admin-city-select{min-width:min(100%,28rem);max-width:100%;}'
        . '.hta-admin-banner-preview{margin:0.5rem 0;max-width:16rem;}'
        . '.hta-admin-banner-preview img{display:block;max-width:100%;height:auto;border:1px solid #c3c4c7;background:#0f172a;}'
        . '.hta-admin-banner-actions{display:flex;gap:0.5rem;flex-wrap:wrap;}'
    );
}
add_action('admin_enqueue_scripts', 'hta_event_admin_assets');

function hta_metabox_nonce(): void
{
    wp_nonce_field('hta_save_meta', 'hta_meta_nonce');
}

function hta_render_event_metabox(WP_Post $post): void
{
    hta_metabox_nonce();
    $date     = hta_meta($post->ID, '_hta_date');
    $location = hta_meta($post->ID, '_hta_location');
    $online   = hta_event_is_online($post->ID);
    $link     = hta_meta($post->ID, '_hta_external_link');
    $cat_term = hta_get_event_category_term($post->ID);
    $cat_id   = $cat_term ? (int) $cat_term->term_id : 0;
    $company  = hta_meta($post->ID, '_hta_host_company');
    $promoted = hta_event_is_promoted($post->ID);
    $banner_id = hta_event_banner_id($post->ID);
    $banner_src = $banner_id ? wp_get_attachment_image_url($banner_id, 'medium') : '';
    $terms    = get_terms(['taxonomy' => 'hta_event_cat', 'hide_empty' => false]);
    $cities   = hta_israel_cities();
    $location_in_list = '' !== $location && in_array($location, $cities, true);
    ?>
    <p>
        <label for="hta_promoted">
            <input type="checkbox" id="hta_promoted" name="hta_promoted" value="1" <?php checked($promoted); ?>>
            ראשון מקודם
        </label><br>
        <span class="description">יוצג כבאנר תמונה בלבד, תמיד ראשון בשורה הראשונה של האירועים — בלי תאריך, קטגוריה, חברה מארחת או עיר.</span>
    </p>
    <div id="hta-admin-banner-wrap" <?php echo $promoted ? '' : 'hidden'; ?>>
        <p>
            <strong>באנר</strong><br>
            <span class="description">העלו תמונה שתוצג במלואה בכרטיס הראשון. מומלץ פוסטר אנכי.</span>
        </p>
        <input type="hidden" id="hta_banner_id" name="hta_banner_id" value="<?php echo esc_attr((string) $banner_id); ?>">
        <div class="hta-admin-banner-preview" id="hta-admin-banner-preview">
            <?php if ($banner_src) : ?>
                <img src="<?php echo esc_url($banner_src); ?>" alt="">
            <?php else : ?>
                <span class="description">אין באנר</span>
            <?php endif; ?>
        </div>
        <p class="hta-admin-banner-actions">
            <button type="button" class="button" id="hta_banner_select">העלאת באנר</button>
            <button type="button" class="button" id="hta_banner_remove" <?php disabled(! $banner_id); ?>>הסרת באנר</button>
        </p>
    </div>
    <p>
        <label for="hta_date"><strong>תאריך</strong></label><br>
        <input type="date" id="hta_date" name="hta_date" value="<?php echo esc_attr($date); ?>" class="regular-text">
    </p>
    <p>
        <label for="hta_category"><strong>קטגוריה *</strong></label><br>
        <select id="hta_category" name="hta_category" class="regular-text">
            <option value="">— בחירה (חובה לפני פרסום) —</option>
            <?php if (! is_wp_error($terms)) : ?>
                <?php foreach ($terms as $term) : ?>
                    <?php if (hta_is_mangled_event_cat_slug($term->slug)) : ?>
                        <?php continue; ?>
                    <?php endif; ?>
                    <option value="<?php echo esc_attr((string) $term->term_id); ?>" <?php selected($cat_id, (int) $term->term_id); ?>>
                        <?php echo esc_html($term->name); ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        <span class="description">יש לבחור קטגוריה לפני פרסום האירוע באתר.</span>
    </p>
    <p>
        <label for="hta_host_company"><strong>חברה מארחת</strong></label><br>
        <input type="text" id="hta_host_company" name="hta_host_company" value="<?php echo esc_attr($company); ?>" class="regular-text">
    </p>
    <p>
        <label for="hta_online">
            <input type="checkbox" id="hta_online" name="hta_online" value="1" <?php checked($online); ?>>
            האירוע יתקיים אונליין (וובינר)
        </label>
    </p>
    <p id="hta-admin-city-wrap">
        <label for="hta_location"><strong>עיר</strong></label><br>
        <select id="hta_location" name="hta_location" class="regular-text hta-admin-city-select">
            <option value="">— בחרו עיר / יישוב —</option>
            <?php if ('' !== $location && ! $location_in_list) : ?>
                <option value="<?php echo esc_attr($location); ?>" selected>
                    <?php echo esc_html($location . ' (לא ברשימה — בחרו יישוב תקין)'); ?>
                </option>
            <?php endif; ?>
            <?php foreach ($cities as $city) : ?>
                <option value="<?php echo esc_attr($city); ?>" <?php selected($location, $city); ?>>
                    <?php echo esc_html($city); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <span class="description">בחירה מרשימת היישובים של טופס ההגשה בלבד (ללא הקלדה חופשית).</span>
    </p>
    <script>
    (function () {
        var box = document.getElementById('hta_online');
        var wrap = document.getElementById('hta-admin-city-wrap');
        if (!box || !wrap) {
            return;
        }
        function sync() {
            wrap.hidden = box.checked;
            var sel = wrap.querySelector('#hta_location');
            if (sel) {
                sel.disabled = box.checked;
            }
        }
        box.addEventListener('change', sync);
        sync();
    })();
    </script>
    <p>
        <label for="hta_external_link"><strong>קישור חיצוני</strong></label><br>
        <input type="text" id="hta_external_link" name="hta_external_link" value="<?php echo esc_attr($link); ?>" class="regular-text ltr" placeholder="https:// או #">
        <span class="description">ניתן גם להזין # כקישור זמני.</span>
    </p>
    <?php
}

function hta_render_ambassador_metabox(WP_Post $post): void
{
    hta_metabox_nonce();
    $role = hta_meta($post->ID, '_hta_role');
    ?>
    <p>
        <label for="hta_role"><strong>תפקיד</strong></label><br>
        <input type="text" id="hta_role" name="hta_role" value="<?php echo esc_attr($role); ?>" class="regular-text">
    </p>
    <p class="description">תמונה ראשית: השתמשו בתיבת «תמונה ראשית» בצד שמאל.</p>
    <?php
}

function hta_render_partner_metabox(WP_Post $post): void
{
    hta_metabox_nonce();
    $link = hta_meta($post->ID, '_hta_partner_link');
    ?>
    <p>
        <label for="hta_partner_link"><strong>קישור</strong></label><br>
        <input type="url" id="hta_partner_link" name="hta_partner_link" value="<?php echo esc_attr($link); ?>" class="regular-text ltr" placeholder="https://">
    </p>
    <p class="description">לוגו: השתמשו בתיבת «תמונה ראשית».</p>
    <?php
}

function hta_render_media_metabox(WP_Post $post): void
{
    hta_metabox_nonce();
    $type = hta_meta($post->ID, '_hta_media_type', 'article');
    $link = hta_meta($post->ID, '_hta_media_link');
    ?>
    <p>
        <label for="hta_media_type"><strong>סוג</strong></label><br>
        <select id="hta_media_type" name="hta_media_type">
            <option value="article" <?php selected($type, 'article'); ?>>כתבה</option>
            <option value="video" <?php selected($type, 'video'); ?>>סרטון</option>
            <option value="news" <?php selected($type, 'news'); ?>>אתר חדשות</option>
        </select>
    </p>
    <p>
        <label for="hta_media_link"><strong>קישור</strong></label><br>
        <input type="url" id="hta_media_link" name="hta_media_link" value="<?php echo esc_attr($link); ?>" class="regular-text ltr" placeholder="https://">
    </p>
    <p class="description">תקציר: השתמשו בשדה «תקציר» של וורדפרס. לסרטון אפשר להדביק קישור יוטיוב.</p>
    <?php
}

function hta_is_mangled_event_cat_slug(string $slug): bool
{
    return (bool) preg_match('/(?:d7[0-9a-f]{2}){3,}/i', $slug);
}

function hta_unmangle_event_cat_slug(string $slug): string
{
    $stripped = str_replace('%', '', $slug);
    if (! preg_match('/^(.*?)((?:d7[0-9a-f]{2})+)$/i', $stripped, $matches)) {
        return $slug;
    }

    $prefix = $matches[1];
    $hex    = $matches[2];
    if (strlen($hex) % 2 !== 0) {
        return $slug;
    }

    $bytes = hex2bin($hex);
    if (false === $bytes || '' === $bytes) {
        return $slug;
    }

    return $prefix . $bytes;
}

function hta_event_category_slug_candidates(string $raw): array
{
    $raw = trim($raw);
    if ('' === $raw) {
        return [];
    }

    $decoded   = rawurldecode($raw);
    $unmangled = hta_unmangle_event_cat_slug($raw);
    $candidates = [$raw, $decoded, $unmangled, sanitize_key($raw)];

    if (function_exists('sanitize_title')) {
        $candidates[] = sanitize_title($unmangled);
        $candidates[] = sanitize_title($decoded);
    }

    $stripped = sanitize_key(str_replace('%', '', $raw));
    if (preg_match('/^(.*?)((?:d7[0-9a-f]{2})+)$/i', $stripped, $matches)) {
        $encoded = $matches[1];
        $hex     = strtolower($matches[2]);
        $length  = strlen($hex);
        for ($i = 0; $i < $length; $i += 2) {
            $encoded .= '%' . substr($hex, $i, 2);
        }
        $candidates[] = $encoded;
    }

    return array_values(array_unique(array_filter($candidates)));
}

function hta_find_event_category_term($value): ?WP_Term
{
    if (is_int($value) || (is_string($value) && ctype_digit($value))) {
        $term = get_term((int) $value, 'hta_event_cat');
        if ($term instanceof WP_Term && ! is_wp_error($term) && ! hta_is_mangled_event_cat_slug($term->slug)) {
            return $term;
        }
    }

    $raw = trim((string) $value);
    if ('' === $raw) {
        return null;
    }

    foreach (hta_event_category_slug_candidates($raw) as $slug) {
        $found = get_term_by('slug', $slug, 'hta_event_cat');
        if ($found instanceof WP_Term && ! hta_is_mangled_event_cat_slug($found->slug)) {
            return $found;
        }
    }

    $all = get_terms([
        'taxonomy'   => 'hta_event_cat',
        'hide_empty' => false,
    ]);
    if (is_wp_error($all) || empty($all)) {
        return null;
    }

    $normalized = array_map('rawurldecode', hta_event_category_slug_candidates($raw));
    foreach ($all as $term) {
        if (! $term instanceof WP_Term || hta_is_mangled_event_cat_slug($term->slug)) {
            continue;
        }
        $term_decoded = rawurldecode($term->slug);
        foreach ($normalized as $slug) {
            if ($term->slug === $slug || $term_decoded === $slug || $term_decoded === rawurldecode($slug)) {
                return $term;
            }
        }
    }

    return null;
}

function hta_get_event_category_term(int $post_id): ?WP_Term
{
    $assigned = wp_get_object_terms($post_id, 'hta_event_cat');
    if (! is_wp_error($assigned) && ! empty($assigned)) {
        foreach ($assigned as $term) {
            if ($term instanceof WP_Term && ! hta_is_mangled_event_cat_slug($term->slug)) {
                return $term;
            }
        }
        if ($assigned[0] instanceof WP_Term) {
            $fixed = hta_find_event_category_term(hta_unmangle_event_cat_slug($assigned[0]->slug));
            if ($fixed instanceof WP_Term) {
                return $fixed;
            }
        }
    }

    $meta = hta_meta($post_id, '_hta_category');
    if ('' !== $meta) {
        return hta_find_event_category_term($meta);
    }

    return null;
}

function hta_get_posted_taxonomy_category_term_ids(): array
{
    if (! isset($_POST['tax_input']['hta_event_cat'])) {
        return [];
    }

    $tax_input = wp_unslash($_POST['tax_input']['hta_event_cat']);
    if (is_array($tax_input)) {
        return array_values(array_filter(array_map('intval', $tax_input)));
    }

    $term_id = (int) $tax_input;
    return $term_id > 0 ? [$term_id] : [];
}

function hta_get_posted_event_category_term(): ?WP_Term
{
    if (isset($_POST['hta_category'])) {
        $posted = wp_unslash($_POST['hta_category']);
        $term   = hta_find_event_category_term($posted);
        if ($term instanceof WP_Term) {
            return $term;
        }
    }

    $term_ids = hta_get_posted_taxonomy_category_term_ids();
    if ([] !== $term_ids) {
        return hta_find_event_category_term((int) $term_ids[0]);
    }

    return null;
}

function hta_get_posted_event_category_slug(): string
{
    $term = hta_get_posted_event_category_term();
    return $term instanceof WP_Term ? $term->slug : '';
}

function hta_resolve_event_category_term(int $post_id): ?WP_Term
{
    $posted = hta_get_posted_event_category_term();
    if ($posted instanceof WP_Term) {
        return $posted;
    }

    if (isset($_POST['hta_meta_nonce']) && isset($_POST['hta_category'])) {
        $raw = trim((string) wp_unslash($_POST['hta_category']));
        if ('' === $raw) {
            $term_ids = hta_get_posted_taxonomy_category_term_ids();
            if ([] === $term_ids) {
                return null;
            }
        }
    }

    return hta_get_event_category_term($post_id);
}

function hta_sync_event_category(int $post_id): void
{
    static $syncing = [];

    if (! empty($syncing[$post_id])) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (wp_is_post_revision($post_id)) {
        return;
    }
    if ('hta_event' !== get_post_type($post_id)) {
        return;
    }

    $syncing[$post_id] = true;

    $term = hta_resolve_event_category_term($post_id);
    $slug = $term instanceof WP_Term ? $term->slug : '';

    update_post_meta($post_id, '_hta_category', $slug);

    if ($term instanceof WP_Term) {
        wp_set_object_terms($post_id, [(int) $term->term_id], 'hta_event_cat', false);
    } else {
        wp_set_object_terms($post_id, [], 'hta_event_cat', false);
    }

    unset($syncing[$post_id]);
}

function hta_schedule_event_category_sync(int $post_id): void
{
    if ('hta_event' !== get_post_type($post_id)) {
        return;
    }

    static $queued = [];
    static $registered = false;

    $queued[$post_id] = true;

    if ($registered) {
        return;
    }

    $registered = true;

    add_action(
        'shutdown',
        static function () use (&$queued): void {
            foreach (array_keys($queued) as $id) {
                hta_sync_event_category((int) $id);
            }
            $queued = [];
        },
        0
    );
}

function hta_event_has_category(int $post_id, string $posted_category = ''): bool
{
    if ('' !== $posted_category) {
        return true;
    }

    if ('' !== hta_get_posted_event_category_slug()) {
        return true;
    }

    $meta = hta_meta($post_id, '_hta_category');
    if ('' !== $meta) {
        return true;
    }

    $terms = wp_get_object_terms($post_id, 'hta_event_cat', ['fields' => 'ids']);
    return ! is_wp_error($terms) && ! empty($terms);
}

function hta_require_event_category_before_publish(array $data, array $postarr): array
{
    if (($data['post_type'] ?? '') !== 'hta_event') {
        return $data;
    }
    if (($data['post_status'] ?? '') !== 'publish') {
        return $data;
    }

    $post_id = isset($postarr['ID']) ? (int) $postarr['ID'] : 0;
    $posted  = hta_get_posted_event_category_slug();

    if (! hta_event_has_category($post_id, $posted)) {
        $data['post_status'] = 'draft';
        if (is_admin() && get_current_user_id()) {
            set_transient('hta_event_cat_required_' . get_current_user_id(), 1, 60);
        }
    }

    return $data;
}
add_filter('wp_insert_post_data', 'hta_require_event_category_before_publish', 20, 2);

function hta_event_category_admin_notice(): void
{
    if (! is_admin() || ! get_current_user_id()) {
        return;
    }
    $key = 'hta_event_cat_required_' . get_current_user_id();
    if (! get_transient($key)) {
        return;
    }
    delete_transient($key);
    echo '<div class="notice notice-error is-dismissible"><p>לא ניתן לפרסם אירוע בלי קטגוריה. האירוע נשמר כטיוטה — בחרו קטגוריה ונסו לפרסם שוב.</p></div>';
}
add_action('admin_notices', 'hta_event_category_admin_notice');

function hta_cleanup_mangled_event_categories(): void
{
    if ('1' === get_option('hta_cleaned_mangled_event_cats_v2')) {
        return;
    }
    if (! taxonomy_exists('hta_event_cat')) {
        return;
    }

    $terms = get_terms([
        'taxonomy'   => 'hta_event_cat',
        'hide_empty' => false,
    ]);
    if (is_wp_error($terms)) {
        return;
    }

    foreach ($terms as $term) {
        if (! $term instanceof WP_Term || ! hta_is_mangled_event_cat_slug($term->slug)) {
            continue;
        }

        $real        = hta_find_event_category_term(hta_unmangle_event_cat_slug($term->slug));
        $object_ids  = get_objects_in_term((int) $term->term_id, 'hta_event_cat');
        if (is_wp_error($object_ids)) {
            $object_ids = [];
        }

        if ($real instanceof WP_Term && (int) $real->term_id !== (int) $term->term_id) {
            foreach ($object_ids as $post_id) {
                wp_set_object_terms((int) $post_id, [(int) $real->term_id], 'hta_event_cat', false);
                update_post_meta((int) $post_id, '_hta_category', $real->slug);
            }
            wp_delete_term((int) $term->term_id, 'hta_event_cat');
            continue;
        }

        if (empty($object_ids)) {
            wp_delete_term((int) $term->term_id, 'hta_event_cat');
        }
    }

    $events = get_posts([
        'post_type'      => 'hta_event',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => [
            [
                'key'     => '_hta_category',
                'value'   => 'd7',
                'compare' => 'LIKE',
            ],
        ],
    ]);
    foreach ($events as $post_id) {
        $term = hta_get_event_category_term((int) $post_id);
        if ($term instanceof WP_Term) {
            update_post_meta((int) $post_id, '_hta_category', $term->slug);
            wp_set_object_terms((int) $post_id, [(int) $term->term_id], 'hta_event_cat', false);
        }
    }

    update_option('hta_cleaned_mangled_event_cats_v2', '1');
}
add_action('admin_init', 'hta_cleanup_mangled_event_categories');

function hta_save_meta_boxes(int $post_id): void
{
    if (! isset($_POST['hta_meta_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['hta_meta_nonce'])), 'hta_save_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (! current_user_can('edit_post', $post_id)) {
        return;
    }

    $type = get_post_type($post_id);

    if ('hta_event' === $type) {
        $date     = isset($_POST['hta_date']) ? sanitize_text_field(wp_unslash($_POST['hta_date'])) : '';
        $location = isset($_POST['hta_location']) ? sanitize_text_field(wp_unslash($_POST['hta_location'])) : '';
        $link     = isset($_POST['hta_external_link']) ? hta_sanitize_link((string) wp_unslash($_POST['hta_external_link'])) : '';
        $company  = isset($_POST['hta_host_company']) ? sanitize_text_field(wp_unslash($_POST['hta_host_company'])) : '';
        $online   = ! empty($_POST['hta_online']);
        $promoted = ! empty($_POST['hta_promoted']);
        $banner_id = isset($_POST['hta_banner_id']) ? absint($_POST['hta_banner_id']) : 0;

        if ($online) {
            $location = '';
        } elseif ('' !== $location && ! hta_is_valid_israel_city($location)) {
            $location = '';
        }

        if ($banner_id && 'attachment' !== get_post_type($banner_id)) {
            $banner_id = 0;
        }

        update_post_meta($post_id, '_hta_date', $date);
        update_post_meta($post_id, '_hta_location', $location);
        update_post_meta($post_id, '_hta_external_link', $link);
        update_post_meta($post_id, '_hta_host_company', $company);
        update_post_meta($post_id, '_hta_online', $online ? '1' : '');
        update_post_meta($post_id, '_hta_promoted', $promoted ? '1' : '');
        update_post_meta($post_id, '_hta_banner_id', $banner_id ? (string) $banner_id : '');
    }

    if ('hta_ambassador' === $type) {
        $role = isset($_POST['hta_role']) ? sanitize_text_field(wp_unslash($_POST['hta_role'])) : '';
        update_post_meta($post_id, '_hta_role', $role);
    }

    if ('hta_partner' === $type) {
        $link = isset($_POST['hta_partner_link']) ? esc_url_raw(wp_unslash($_POST['hta_partner_link'])) : '';
        update_post_meta($post_id, '_hta_partner_link', $link);
    }

    if ('hta_media' === $type) {
        $media_type = isset($_POST['hta_media_type']) ? sanitize_key(wp_unslash($_POST['hta_media_type'])) : 'article';
        if (! in_array($media_type, ['article', 'video', 'news'], true)) {
            $media_type = 'article';
        }
        $link = isset($_POST['hta_media_link']) ? esc_url_raw(wp_unslash($_POST['hta_media_link'])) : '';
        update_post_meta($post_id, '_hta_media_type', $media_type);
        update_post_meta($post_id, '_hta_media_link', $link);
    }
}
add_action('save_post_hta_event', 'hta_save_meta_boxes');
add_action('save_post_hta_event', 'hta_schedule_event_category_sync', 999);
add_action('save_post_hta_ambassador', 'hta_save_meta_boxes');
add_action('save_post_hta_partner', 'hta_save_meta_boxes');
add_action('save_post_hta_media', 'hta_save_meta_boxes');
