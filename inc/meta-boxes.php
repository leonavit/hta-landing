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

function hta_metabox_nonce(): void
{
    wp_nonce_field('hta_save_meta', 'hta_meta_nonce');
}

function hta_render_event_metabox(WP_Post $post): void
{
    hta_metabox_nonce();
    $date     = hta_meta($post->ID, '_hta_date');
    $location = hta_meta($post->ID, '_hta_location');
    $link     = hta_meta($post->ID, '_hta_external_link');
    $cat      = hta_meta($post->ID, '_hta_category');
    if ('' === $cat) {
        $assigned = wp_get_object_terms($post->ID, 'hta_event_cat', ['fields' => 'slugs']);
        if (! is_wp_error($assigned) && ! empty($assigned)) {
            $cat = (string) $assigned[0];
        }
    }
    $company  = hta_meta($post->ID, '_hta_host_company');
    $terms    = get_terms(['taxonomy' => 'hta_event_cat', 'hide_empty' => false]);
    ?>
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
                    <option value="<?php echo esc_attr($term->slug); ?>" <?php selected($cat, $term->slug); ?>>
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
        <label for="hta_location"><strong>מיקום</strong></label><br>
        <input type="text" id="hta_location" name="hta_location" value="<?php echo esc_attr($location); ?>" class="regular-text">
    </p>
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

function hta_get_posted_event_category_slug(): string
{
    if (isset($_POST['hta_category'])) {
        $slug = sanitize_key(wp_unslash($_POST['hta_category']));
        if ('' !== $slug) {
            return $slug;
        }
    }

    $term_ids = hta_get_posted_taxonomy_category_term_ids();
    if ([] !== $term_ids) {
        $term = get_term((int) $term_ids[0], 'hta_event_cat');
        if ($term && ! is_wp_error($term)) {
            return $term->slug;
        }
    }

    return '';
}

function hta_resolve_event_category_slug(int $post_id): string
{
    $posted_slug = hta_get_posted_event_category_slug();
    if ('' !== $posted_slug) {
        return $posted_slug;
    }

    if (isset($_POST['hta_meta_nonce']) && isset($_POST['hta_category']) && '' === sanitize_key(wp_unslash($_POST['hta_category']))) {
        $term_ids = hta_get_posted_taxonomy_category_term_ids();
        if ([] === $term_ids) {
            return '';
        }
    }

    $terms = wp_get_object_terms($post_id, 'hta_event_cat', ['fields' => 'slugs']);
    if (! is_wp_error($terms) && ! empty($terms)) {
        return (string) $terms[0];
    }

    $meta = hta_meta($post_id, '_hta_category');
    return is_string($meta) ? $meta : '';
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

    $slug = hta_resolve_event_category_slug($post_id);
    update_post_meta($post_id, '_hta_category', $slug);

    if ('' !== $slug) {
        wp_set_object_terms($post_id, [$slug], 'hta_event_cat', false);
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

        update_post_meta($post_id, '_hta_date', $date);
        update_post_meta($post_id, '_hta_location', $location);
        update_post_meta($post_id, '_hta_external_link', $link);
        update_post_meta($post_id, '_hta_host_company', $company);
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
