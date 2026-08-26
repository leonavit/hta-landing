<?php
/**
 * Native Customizer — landing copy, typography, 3 primary colors.
 * No ACF.
 *
 * @package HTA_Landing
 */

if (! defined('ABSPATH')) {
    exit;
}

function hta_customize_register(WP_Customize_Manager $wp_customize): void
{
    $wp_customize->add_panel('hta_landing', [
        'title'       => __('HTA Landing', 'hta-landing'),
        'description' => __('טקסטים, טיפוגרפיה וצבעי שבוע ההיי-טק הישראלי.', 'hta-landing'),
        'priority'    => 30,
    ]);

    hta_register_color_section($wp_customize);
    hta_register_type_section($wp_customize);
    hta_register_type_breakpoint_section($wp_customize, 'tablet');
    hta_register_type_breakpoint_section($wp_customize, 'mobile');
    hta_register_hero_section($wp_customize);
    hta_register_about_section($wp_customize);
    hta_register_why_section($wp_customize);
    hta_register_topics_section($wp_customize);
    hta_register_events_section($wp_customize);
    hta_register_ambassadors_section($wp_customize);
    hta_register_partners_section($wp_customize);
    hta_register_media_section($wp_customize);
    hta_register_submit_section($wp_customize);
    hta_register_footer_section($wp_customize);
}
add_action('customize_register', 'hta_customize_register');

function hta_add_text_control(WP_Customize_Manager $wp_customize, string $id, string $section, string $label, string $default, string $type = 'text'): void
{
    $sanitize = 'textarea' === $type ? 'sanitize_textarea_field' : 'sanitize_text_field';
    $wp_customize->add_setting($id, [
        'default'           => $default,
        'sanitize_callback' => $sanitize,
        'transport'         => 'refresh',
    ]);
    $wp_customize->add_control($id, [
        'label'   => $label,
        'section' => $section,
        'type'    => $type,
    ]);
}

function hta_add_range_control(WP_Customize_Manager $wp_customize, string $id, string $section, string $label, int $default, int $min, int $max, int $step = 1): void
{
    $wp_customize->add_setting($id, [
        'default'           => $default,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control($id, [
        'label'       => $label,
        'section'     => $section,
        'type'        => 'range',
        'input_attrs' => ['min' => $min, 'max' => $max, 'step' => $step],
    ]);
}

function hta_add_number_control(WP_Customize_Manager $wp_customize, string $id, string $section, string $label, int $default, int $min, int $max, int $step = 1): void
{
    $wp_customize->add_setting($id, [
        'default'           => $default,
        'sanitize_callback' => static function ($value) use ($min, $max): int {
            $n = (int) $value;
            return max($min, min($max, $n));
        },
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control($id, [
        'label'       => $label,
        'section'     => $section,
        'type'        => 'number',
        'input_attrs' => ['min' => $min, 'max' => $max, 'step' => $step],
    ]);
}

function hta_register_color_section(WP_Customize_Manager $wp_customize): void
{
    $wp_customize->add_section('hta_colors', [
        'title' => __('צבעים ראשיים', 'hta-landing'),
        'panel' => 'hta_landing',
    ]);

    $colors = [
        'hta_color_primary_1' => ['ציאן / Primary 1', '#22d3ee'],
        'hta_color_primary_2' => ['כחול / Primary 2', '#2563eb'],
        'hta_color_primary_3' => ['סגול / Primary 3', '#8b5cf6'],
        'hta_color_btn_bg'       => ['רקע כפתורי הירו', '#22d3ee'],
        'hta_color_btn_text'     => ['טקסט על כפתורי הירו', '#020617'],
        'hta_color_hero_kicker'  => ['תאריך בהירו', '#22d3ee'],
    ];

    foreach ($colors as $id => $data) {
        $wp_customize->add_setting($id, [
            'default'           => $data[1],
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ]);
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $id, [
            'label'   => $data[0],
            'section' => 'hta_colors',
        ]));
    }

    hta_register_text_fill_controls($wp_customize, 'hta_hero_title', __('כותרת ראשית בהירו', 'hta-landing'));
    hta_register_text_shadow_controls(
        $wp_customize,
        'hta_hero_title',
        __('כותרת ראשית בהירו', 'hta-landing'),
        ['enabled' => true, 'x' => -1, 'y' => 2, 'blur' => 3, 'color' => '#000000']
    );
    hta_register_text_fill_controls($wp_customize, 'hta_hero_tagline', __('תת כותרת בהירו', 'hta-landing'));
    hta_register_text_shadow_controls(
        $wp_customize,
        'hta_hero_tagline',
        __('תת כותרת בהירו', 'hta-landing'),
        ['enabled' => false, 'x' => 0, 'y' => 0, 'blur' => 0, 'color' => '#000000']
    );
}

/**
 * Mode + 1/3 color controls for hero text fills.
 */
function hta_register_text_fill_controls(WP_Customize_Manager $wp_customize, string $prefix, string $label): void
{
    $wp_customize->add_setting("{$prefix}_color_mode", [
        'default'           => 'solid',
        'sanitize_callback' => static function ($value): string {
            $value = sanitize_key((string) $value);
            return in_array($value, ['solid', 'gradient'], true) ? $value : 'solid';
        },
        'transport'         => 'refresh',
    ]);
    $wp_customize->add_control("{$prefix}_color_mode", [
        'label'   => sprintf(__('%s — סוג צבע', 'hta-landing'), $label),
        'section' => 'hta_colors',
        'type'    => 'select',
        'choices' => [
            'solid'    => __('צבע אחד', 'hta-landing'),
            'gradient' => __('גרדיאנט 3 צבעים', 'hta-landing'),
        ],
    ]);

    $wp_customize->add_setting("{$prefix}_gradient_dir", [
        'default'           => 'to left',
        'sanitize_callback' => static function ($value): string {
            $value = sanitize_text_field((string) $value);
            return isset(hta_text_fill_directions()[$value]) ? $value : 'to left';
        },
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control("{$prefix}_gradient_dir", [
        'label'           => sprintf(__('%s — כיוון גרדיאנט', 'hta-landing'), $label),
        'section'         => 'hta_colors',
        'type'            => 'select',
        'choices'         => hta_text_fill_directions(),
        'active_callback' => static function () use ($wp_customize, $prefix): bool {
            return 'gradient' === $wp_customize->get_setting("{$prefix}_color_mode")->value();
        },
    ]);

    $stops = [
        1 => __('צבע 1', 'hta-landing'),
        2 => __('צבע 2', 'hta-landing'),
        3 => __('צבע 3', 'hta-landing'),
    ];

    foreach ($stops as $n => $stop_label) {
        $id = "{$prefix}_color_{$n}";
        $wp_customize->add_setting($id, [
            'default'           => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ]);
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $id, [
            'label'           => sprintf('%s — %s', $label, 1 === $n ? __('צבע', 'hta-landing') : $stop_label),
            'section'         => 'hta_colors',
            'active_callback' => static function () use ($wp_customize, $prefix, $n): bool {
                $mode = $wp_customize->get_setting("{$prefix}_color_mode")->value();
                if ('gradient' === $mode) {
                    return true;
                }
                return 1 === $n;
            },
        ]));
    }
}

/**
 * text-shadow controls matching DevTools fields: X, Y, blur, color + enable.
 *
 * @param array{enabled?: bool, x?: int, y?: int, blur?: int, color?: string} $defaults
 */
function hta_register_text_shadow_controls(WP_Customize_Manager $wp_customize, string $prefix, string $label, array $defaults = []): void
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

    $wp_customize->add_setting("{$prefix}_shadow_enabled", [
        'default'           => $defaults['enabled'],
        'sanitize_callback' => static function ($value): bool {
            return (bool) $value;
        },
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control("{$prefix}_shadow_enabled", [
        'label'   => sprintf(__('%s — הצללת טקסט (text-shadow)', 'hta-landing'), $label),
        'section' => 'hta_colors',
        'type'    => 'checkbox',
    ]);

    $is_enabled = static function () use ($wp_customize, $prefix): bool {
        return (bool) $wp_customize->get_setting("{$prefix}_shadow_enabled")->value();
    };

    hta_add_number_control(
        $wp_customize,
        "{$prefix}_shadow_x",
        'hta_colors',
        sprintf(__('%s — Offset X (px)', 'hta-landing'), $label),
        (int) $defaults['x'],
        -40,
        40
    );
    $wp_customize->get_control("{$prefix}_shadow_x")->active_callback = $is_enabled;

    hta_add_number_control(
        $wp_customize,
        "{$prefix}_shadow_y",
        'hta_colors',
        sprintf(__('%s — Offset Y (px)', 'hta-landing'), $label),
        (int) $defaults['y'],
        -40,
        40
    );
    $wp_customize->get_control("{$prefix}_shadow_y")->active_callback = $is_enabled;

    hta_add_number_control(
        $wp_customize,
        "{$prefix}_shadow_blur",
        'hta_colors',
        sprintf(__('%s — Blur (px)', 'hta-landing'), $label),
        (int) $defaults['blur'],
        0,
        80
    );
    $wp_customize->get_control("{$prefix}_shadow_blur")->active_callback = $is_enabled;

    $wp_customize->add_setting("{$prefix}_shadow_color", [
        'default'           => $defaults['color'],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, "{$prefix}_shadow_color", [
        'label'           => sprintf(__('%s — צבע הצללה', 'hta-landing'), $label),
        'section'         => 'hta_colors',
        'active_callback' => $is_enabled,
    ]));
}

function hta_register_type_section(WP_Customize_Manager $wp_customize): void
{
    $wp_customize->add_section('hta_typography', [
        'title' => __('טיפוגרפיה — דסקטופ', 'hta-landing'),
        'panel' => 'hta_landing',
    ]);

    hta_add_range_control($wp_customize, 'hta_font_hero', 'hta_typography', __('גודל כותרת הירו (px)', 'hta-landing'), 56, 32, 84);
    hta_add_range_control($wp_customize, 'hta_font_hero_kicker', 'hta_typography', __('גודל תאריך בהירו (px)', 'hta-landing'), 14, 10, 50);
    hta_add_range_control($wp_customize, 'hta_font_hero_tagline', 'hta_typography', __('גודל תת כותרת הירו (px)', 'hta-landing'), 24, 14, 50);
    hta_add_range_control($wp_customize, 'hta_font_hero_text', 'hta_typography', __('גודל פסקת הירו (px)', 'hta-landing'), 18, 12, 32);
    hta_add_range_control($wp_customize, 'hta_font_section', 'hta_typography', __('גודל כותרות סקשן (px)', 'hta-landing'), 36, 22, 56);
    hta_add_range_control($wp_customize, 'hta_font_body', 'hta_typography', __('גודל טקסט גוף (px)', 'hta-landing'), 16, 14, 22);
    hta_add_range_control($wp_customize, 'hta_font_paragraph', 'hta_typography', __('גודל פסקאות סקשנים (px)', 'hta-landing'), 16, 12, 24);
    hta_add_range_control($wp_customize, 'hta_font_list', 'hta_typography', __('גודל רשימות (px)', 'hta-landing'), 16, 12, 24);
    hta_add_range_control($wp_customize, 'hta_font_nav', 'hta_typography', __('גודל תפריט (px)', 'hta-landing'), 22, 14, 32);

    hta_add_range_control($wp_customize, 'hta_weight_hero_main', 'hta_typography', __('משקל כותרת ראשית בהירו', 'hta-landing'), 800, 300, 800, 100);
    hta_add_range_control($wp_customize, 'hta_weight_hero_kicker', 'hta_typography', __('משקל תאריך בהירו', 'hta-landing'), 600, 300, 800, 100);
    hta_add_range_control($wp_customize, 'hta_weight_hero', 'hta_typography', __('משקל תת כותרת הירו', 'hta-landing'), 700, 300, 800, 100);
    hta_add_range_control($wp_customize, 'hta_weight_hero_text', 'hta_typography', __('משקל פסקת הירו', 'hta-landing'), 300, 300, 800, 100);
    hta_add_range_control($wp_customize, 'hta_weight_section', 'hta_typography', __('משקל כותרות בסקשנים', 'hta-landing'), 800, 300, 800, 100);
    hta_add_range_control($wp_customize, 'hta_weight_paragraph', 'hta_typography', __('משקל פסקאות', 'hta-landing'), 300, 300, 800, 100);
    hta_add_range_control($wp_customize, 'hta_weight_list', 'hta_typography', __('משקל רשימות', 'hta-landing'), 300, 300, 800, 100);
}

function hta_font_size_fields(): array
{
    return [
        'hta_font_hero'         => [__('גודל כותרת הירו (px)', 'hta-landing'), 56, 32, 84],
        'hta_font_hero_kicker'  => [__('גודל תאריך בהירו (px)', 'hta-landing'), 14, 10, 50],
        'hta_font_hero_tagline' => [__('גודל תת כותרת הירו (px)', 'hta-landing'), 24, 14, 50],
        'hta_font_hero_text'    => [__('גודל פסקת הירו (px)', 'hta-landing'), 18, 12, 32],
        'hta_font_section'      => [__('גודל כותרות סקשן (px)', 'hta-landing'), 36, 22, 56],
        'hta_font_body'         => [__('גודל טקסט גוף (px)', 'hta-landing'), 16, 14, 22],
        'hta_font_paragraph'    => [__('גודל פסקאות סקשנים (px)', 'hta-landing'), 16, 12, 24],
        'hta_font_list'         => [__('גודל רשימות (px)', 'hta-landing'), 16, 12, 24],
        'hta_font_nav'          => [__('גודל תפריט (px)', 'hta-landing'), 22, 14, 32],
    ];
}

function hta_register_type_breakpoint_section(WP_Customize_Manager $wp_customize, string $bp): void
{
    $is_tablet = ('tablet' === $bp);
    $section   = $is_tablet ? 'hta_typography_tablet' : 'hta_typography_mobile';
    $wp_customize->add_section($section, [
        'title'       => $is_tablet ? __('טיפוגרפיה — טאבלט', 'hta-landing') : __('טיפוגרפיה — מובייל', 'hta-landing'),
        'description' => $is_tablet
            ? __('גדלים למסכים 768–1023px. אם לא שיניתם, נשמר גודל הדסקטופ.', 'hta-landing')
            : __('גדלים למסכים עד 767px. אם לא שיניתם, נשמר גודל הדסקטופ.', 'hta-landing'),
        'panel'       => 'hta_landing',
    ]);

    foreach (hta_font_size_fields() as $id => $field) {
        $desktop = absint(get_theme_mod($id, $field[1]));
        hta_add_range_control(
            $wp_customize,
            $id . '_' . $bp,
            $section,
            $field[0],
            $desktop,
            $field[2],
            $field[3]
        );
    }
}

function hta_register_hero_section(WP_Customize_Manager $wp_customize): void
{
    $wp_customize->add_section('hta_hero', [
        'title' => __('הירו', 'hta-landing'),
        'panel' => 'hta_landing',
    ]);

    $wp_customize->add_setting('hta_hero_video_source', [
        'default'           => 'default',
        'sanitize_callback' => static function ($value): string {
            $value = sanitize_key((string) $value);
            return in_array($value, ['default', 'upload', 'url'], true) ? $value : 'default';
        },
        'transport'         => 'refresh',
    ]);
    $wp_customize->add_control('hta_hero_video_source', [
        'label'       => __('מקור סרטון רקע', 'hta-landing'),
        'description' => __('בחרו ברירת מחדל, העלאה מהמדיה, או קישור לקובץ וידאו.', 'hta-landing'),
        'section'     => 'hta_hero',
        'type'        => 'select',
        'choices'     => [
            'default' => __('ברירת מחדל (סרטון התבנית)', 'hta-landing'),
            'upload'  => __('העלאת וידאו', 'hta-landing'),
            'url'     => __('קישור לווידאו', 'hta-landing'),
        ],
    ]);

    $wp_customize->add_setting('hta_hero_video', [
        'default'           => 0,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ]);
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'hta_hero_video', [
        'label'           => __('העלאת סרטון', 'hta-landing'),
        'description'     => __('העלו קובץ MP4 מהספרייה.', 'hta-landing'),
        'section'         => 'hta_hero',
        'mime_type'       => 'video',
        'active_callback' => static function () use ($wp_customize): bool {
            return 'upload' === $wp_customize->get_setting('hta_hero_video_source')->value();
        },
    ]));

    $wp_customize->add_setting('hta_hero_video_url', [
        'default'           => '',
        'sanitize_callback' => static function ($value): string {
            $value = trim((string) $value);
            if ('' === $value) {
                return '';
            }
            return esc_url_raw($value);
        },
        'transport'         => 'refresh',
    ]);
    $wp_customize->add_control('hta_hero_video_url', [
        'label'           => __('קישור לווידאו', 'hta-landing'),
        'description'     => __('הדביקו קישור ישיר לקובץ MP4/WebM (או קישור יוטיוב).', 'hta-landing'),
        'section'         => 'hta_hero',
        'type'            => 'url',
        'active_callback' => static function () use ($wp_customize): bool {
            return 'url' === $wp_customize->get_setting('hta_hero_video_source')->value();
        },
    ]);

    $wp_customize->add_setting('hta_hero_logo', [
        'default'           => 0,
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'hta_hero_logo', [
        'label'     => __('לוגו הירו (אופציונלי)', 'hta-landing'),
        'section'   => 'hta_hero',
        'mime_type' => 'image',
    ]));

    $wp_customize->add_setting('hta_hero_overlay_color', [
        'default'           => '#020617',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'hta_hero_overlay_color', [
        'label'       => __('צבע Overlay', 'hta-landing'),
        'description' => __('שכבת הצבע מעל סרטון הרקע.', 'hta-landing'),
        'section'     => 'hta_hero',
    ]));

    hta_add_range_control(
        $wp_customize,
        'hta_hero_overlay_opacity',
        'hta_hero',
        __('שקיפות Overlay (%)', 'hta-landing'),
        70,
        0,
        100,
        1
    );

    hta_add_text_control($wp_customize, 'hta_hero_kicker', 'hta_hero', __('שורה עליונה', 'hta-landing'), '1-6.11.2026 | איגוד ההיי-טק הישראלי');
    hta_add_text_control($wp_customize, 'hta_hero_title', 'hta_hero', __('כותרת ראשית', 'hta-landing'), 'שבוע ההיי-טק הישראלי 2026');
    hta_add_text_control($wp_customize, 'hta_hero_tagline', 'hta_hero', __('סלוגן', 'hta-landing'), 'מחברים אנשים. טכנולוגיה. עתיד.');
    hta_add_text_control($wp_customize, 'hta_hero_subtitle', 'hta_hero', __('תיאור', 'hta-landing'), 'שישה ימים . שישה עולמות תוכן . מאות אירועים. סיפור אחד של ההיי-טק הישראלי', 'textarea');
    hta_add_text_control($wp_customize, 'hta_hero_cta_primary', 'hta_hero', __('כפתור ראשי', 'hta-landing'), 'חיפוש אירועים בשבוע');
    hta_add_text_control($wp_customize, 'hta_hero_cta_secondary', 'hta_hero', __('כפתור משני', 'hta-landing'), 'הגשת אירוע משלכם');
}

function hta_register_about_section(WP_Customize_Manager $wp_customize): void
{
    $wp_customize->add_section('hta_about', [
        'title' => __('אודות', 'hta-landing'),
        'panel' => 'hta_landing',
    ]);

    $wp_customize->add_setting('hta_about_image', [
        'default'           => 0,
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'hta_about_image', [
        'label'     => __('תמונת אודות', 'hta-landing'),
        'section'   => 'hta_about',
        'mime_type' => 'image',
    ]));

    hta_add_text_control($wp_customize, 'hta_about_title', 'hta_about', __('כותרת', 'hta-landing'), 'אודות שבוע ההיי-טק');
    hta_add_text_control($wp_customize, 'hta_about_p1', 'hta_about', __('פסקה 1', 'hta-landing'), 'מהלך חדש של איגוד ההיי-טק הישראלי, שמטרתו לרכז בשבוע אחד את כלל העשייה, החדשנות והעוצמה של ההיי-טק הישראלי, בישראל ובעולם.', 'textarea');
    hta_add_text_control($wp_customize, 'hta_about_p2', 'hta_about', __('פסקה 2', 'hta-landing'), 'השבוע נועד לחזק את המיצוב של ההיי-טק הישראלי, ליצור חיבורים חדשים בין כלל שחקני האקו-סיסטם, ולהציג את התרומה המשמעותית של התעשייה לכלכלה ולחברה.', 'textarea');
    hta_add_text_control($wp_customize, 'hta_about_p3', 'hta_about', __('פסקה 3', 'hta-landing'), 'במהלך השבוע חברות היי-טק, ארגונים, גופי ממשל, משקיעים, אקדמיה ומערכת החינוך יקיימו אירועים, פעילויות ומפגשים סביב שישה עולמות תוכן.', 'textarea');
}

function hta_register_why_section(WP_Customize_Manager $wp_customize): void
{
    $wp_customize->add_section('hta_why', [
        'title' => __('למה זה חשוב', 'hta-landing'),
        'panel' => 'hta_landing',
    ]);

    $wp_customize->add_setting('hta_why_image', [
        'default'           => 0,
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'hta_why_image', [
        'label'     => __('תמונה (משמאל)', 'hta-landing'),
        'section'   => 'hta_why',
        'mime_type' => 'image',
    ]));

    hta_add_text_control($wp_customize, 'hta_why_title', 'hta_why', __('כותרת', 'hta-landing'), 'למה זה חשוב עכשיו?');
    hta_add_text_control($wp_customize, 'hta_why_intro', 'hta_why', __('מבוא', 'hta-landing'), 'ההיי-טק הישראלי נמצא בנקודת זמן קריטית, מול אתגרים גלובליים, תחרות גוברת וצורך בחיזוק המיצוב, התדמית וההשפעה הבינלאומית של התעשייה. שבוע ההיי-טק הישראלי מאפשר:', 'textarea');

    $defaults = [
        'יצירת נראות רחבה ואחידה לתעשיית ההיי-טק הישראלית',
        'חיזוק המותג הישראלי בזירה הבינלאומית',
        'חיבור בין חברות, משקיעים, ממשל, אקדמיה, חינוך ואקו-סיסטם',
        'יצירת שיח ציבורי וכלכלי סביב חדשנות, צמיחה וטכנולוגיה',
        'ריכוז כוח תקשורתי, מקצועי וציבורי במהלך אחד',
        'אדפטציה לעולמות הבינה המלאכותית',
    ];
    foreach ($defaults as $i => $text) {
        $n = $i + 1;
        hta_add_text_control($wp_customize, "hta_why_item_{$n}", 'hta_why', sprintf(__('סעיף %d', 'hta-landing'), $n), $text, 'textarea');
    }
}

function hta_register_topics_section(WP_Customize_Manager $wp_customize): void
{
    $wp_customize->add_section('hta_topics', [
        'title' => __('נושאי השבוע', 'hta-landing'),
        'panel' => 'hta_landing',
    ]);

    hta_add_text_control($wp_customize, 'hta_topics_title', 'hta_topics', __('כותרת', 'hta-landing'), 'נושאי השבוע');
    hta_add_text_control($wp_customize, 'hta_topics_subtitle', 'hta_topics', __('תת-כותרת', 'hta-landing'), 'תחומים שמעצבים את עתיד ההיי-טק הישראלי');

    $wp_customize->add_setting('hta_topics_shader_enable', [
        'default'           => true,
        'sanitize_callback' => static function ($value): bool {
            return (bool) $value;
        },
        'transport'         => 'refresh',
    ]);
    $wp_customize->add_control('hta_topics_shader_enable', [
        'label'       => __('רקע מונפש', 'hta-landing'),
        'description' => __('כבו כדי להציג רקע סטטי בלי האנימציה.', 'hta-landing'),
        'section'     => 'hta_topics',
        'type'        => 'checkbox',
    ]);

    $topics = [
        ['01.11.2026', 'יום הסייבר', 'ישראל היא מעצמת סייבר עולמית. יום הסייבר יציג את החברות, הטכנולוגיות והאנשים שמובילים את הגנת העולם הדיגיטלי ומעצבים את עתיד הביטחון בעידן החדש.'],
        ['02.11.2026', 'יום הבינה המלאכותית', 'המהפכה שמשנה את כללי המשחק. יום ה־AI יפגיש את מובילי החדשנות, התעשייה והמחקר כדי להציג כיצד ישראל מובילה את יישומי הבינה המלאכותית של העתיד.'],
        ['03.11.2026', 'יום השבבים והחומרה', 'מהנדסה ישראלית ועד טכנולוגיות פורצות דרך. יום השבבים והחומרה יציג את מקומה של ישראל בלב תעשיית ה־Deep Tech העולמית ואת הפיתוחים שישנו את עולם המחשוב.'],
        ['04.11.2026', 'יום יזמות, צמיחה והשקעות', 'המקום שבו חדשנות פוגשת הון והזדמנויות. יום המוקדש ליזמים, משקיעים וחברות בצמיחה המחפשים את הדבר הגדול הבא באקוסיסטם הישראלי.'],
        ['05.11.2026', 'יום הכישרונות העתידיים', 'עוסקים באנשים, בכישורים ובכלים שיבנו את הדור הבא של תעשיית הטכנולוגיה הישראלית.'],
        ['06.11.2026', 'יום ההשפעה הגלובלית', 'חוגגים את ההשפעה הגלובלית של החדשנות הישראלית ומחזקים את החיבורים בין ישראל לעולם.'],
    ];

    foreach ($topics as $i => $topic) {
        $n = $i + 1;
        hta_add_text_control($wp_customize, "hta_topic_{$n}_badge", 'hta_topics', sprintf(__('נושא %d — תאריך', 'hta-landing'), $n), $topic[0]);
        hta_add_text_control($wp_customize, "hta_topic_{$n}_title", 'hta_topics', sprintf(__('נושא %d — כותרת', 'hta-landing'), $n), $topic[1]);
        hta_add_text_control($wp_customize, "hta_topic_{$n}_text", 'hta_topics', sprintf(__('נושא %d — תיאור', 'hta-landing'), $n), $topic[2], 'textarea');

        $wp_customize->add_setting("hta_topic_{$n}_image", [
            'default'           => 0,
            'sanitize_callback' => 'absint',
        ]);
        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, "hta_topic_{$n}_image", [
            'label'     => sprintf(__('נושא %d — תמונה', 'hta-landing'), $n),
            'section'   => 'hta_topics',
            'mime_type' => 'image',
        ]));
    }
}

function hta_register_events_section(WP_Customize_Manager $wp_customize): void
{
    $wp_customize->add_section('hta_events', [
        'title' => __('חיפוש אירועים', 'hta-landing'),
        'panel' => 'hta_landing',
    ]);

    hta_add_text_control($wp_customize, 'hta_events_title', 'hta_events', __('כותרת', 'hta-landing'), 'מנוע חיפוש אירועים');
    hta_add_text_control($wp_customize, 'hta_events_subtitle', 'hta_events', __('תיאור', 'hta-landing'), 'הקלידו מילת מפתח או בחרו קטגוריה כדי לסנן את האירועים בזמן אמת.', 'textarea');

    $wp_customize->add_setting('hta_events_mobile_cols', [
        'default'           => '1',
        'sanitize_callback' => static function ($value): string {
            $value = sanitize_key((string) $value);
            return in_array($value, ['1', '2'], true) ? $value : '1';
        },
        'transport'         => 'refresh',
    ]);
    $wp_customize->add_control('hta_events_mobile_cols', [
        'label'       => __('עמודות במובייל', 'hta-landing'),
        'description' => __('מספר עמודות בגריד האירועים במסכים צרים.', 'hta-landing'),
        'section'     => 'hta_events',
        'type'        => 'select',
        'choices'     => [
            '1' => __('עמודה אחת (ברירת מחדל)', 'hta-landing'),
            '2' => __('שתי עמודות', 'hta-landing'),
        ],
    ]);

    $wp_customize->add_setting('hta_events_desktop_cols', [
        'default'           => '4',
        'sanitize_callback' => static function ($value): string {
            $value = sanitize_key((string) $value);
            return in_array($value, ['3', '4'], true) ? $value : '4';
        },
        'transport'         => 'refresh',
    ]);
    $wp_customize->add_control('hta_events_desktop_cols', [
        'label'       => __('עמודות במסכים רחבים', 'hta-landing'),
        'description' => __('מספר עמודות בגריד האירועים מטאבלט ומעלה.', 'hta-landing'),
        'section'     => 'hta_events',
        'type'        => 'select',
        'choices'     => [
            '3' => __('3 עמודות', 'hta-landing'),
            '4' => __('4 עמודות (ברירת מחדל)', 'hta-landing'),
        ],
    ]);

    $wp_customize->add_setting('hta_events_page_size', [
        'default'           => '4',
        'sanitize_callback' => static function ($value): string {
            $value = sanitize_key((string) $value);
            return in_array($value, ['4', '8', '12', 'all'], true) ? $value : '4';
        },
        'transport'         => 'refresh',
    ]);
    $wp_customize->add_control('hta_events_page_size', [
        'label'       => __('כמות אירועים בצפייה ראשונה', 'hta-landing'),
        'description' => __('כמה אירועים יוצגו בהתחלה. לחיצה על «טען עוד» תציג את אותה כמות נוספת.', 'hta-landing'),
        'section'     => 'hta_events',
        'type'        => 'select',
        'choices'     => [
            '4'   => __('4 אירועים (ברירת מחדל)', 'hta-landing'),
            '8'   => __('8 אירועים', 'hta-landing'),
            '12'  => __('12 אירועים', 'hta-landing'),
            'all' => __('הצג הכל', 'hta-landing'),
        ],
    ]);
}

function hta_register_ambassadors_section(WP_Customize_Manager $wp_customize): void
{
    $wp_customize->add_section('hta_ambassadors', [
        'title' => __('שגרירים', 'hta-landing'),
        'panel' => 'hta_landing',
    ]);

    hta_add_text_control($wp_customize, 'hta_ambassadors_title', 'hta_ambassadors', __('כותרת', 'hta-landing'), 'שגרירי השבוע');
    hta_add_text_control($wp_customize, 'hta_ambassadors_subtitle', 'hta_ambassadors', __('תיאור', 'hta-landing'), 'מובילי דעת הקהל וראשי קהילות המלווים את שבוע ההיי-טק הישראלי.', 'textarea');
}

function hta_register_partners_section(WP_Customize_Manager $wp_customize): void
{
    $wp_customize->add_section('hta_partners', [
        'title' => __('שותפים', 'hta-landing'),
        'panel' => 'hta_landing',
    ]);

    hta_add_text_control($wp_customize, 'hta_partners_title', 'hta_partners', __('כותרת', 'hta-landing'), 'שותפים');
    hta_add_text_control($wp_customize, 'hta_partners_subtitle', 'hta_partners', __('תיאור', 'hta-landing'), 'השותפים המובילים של שבוע ההיי-טק', 'textarea');
}

function hta_register_media_section(WP_Customize_Manager $wp_customize): void
{
    $wp_customize->add_section('hta_media', [
        'title' => __('מן התקשורת', 'hta-landing'),
        'panel' => 'hta_landing',
    ]);

    hta_add_text_control($wp_customize, 'hta_media_title', 'hta_media', __('כותרת', 'hta-landing'), 'מן התקשורת');
    hta_add_text_control($wp_customize, 'hta_media_subtitle', 'hta_media', __('תיאור', 'hta-landing'), 'כתבות, סרטונים וראיונות על שבוע ההיי-טק הישראלי.', 'textarea');
}

function hta_register_submit_section(WP_Customize_Manager $wp_customize): void
{
    $wp_customize->add_section('hta_submit', [
        'title' => __('הגשת אירוע', 'hta-landing'),
        'panel' => 'hta_landing',
    ]);

    hta_add_text_control($wp_customize, 'hta_submit_title', 'hta_submit', __('כותרת שמאל', 'hta-landing'), 'למה להגיש אירוע?');
    hta_add_text_control($wp_customize, 'hta_submit_countdown', 'hta_submit', __('תאריך יעד לספירה לאחור', 'hta-landing'), '2026-11-01T00:00:00');
    hta_add_text_control($wp_customize, 'hta_submit_countdown_title', 'hta_submit', __('כותרת ספירה לאחור', 'hta-landing'), 'האירועים יתחילו בעוד:');
    hta_add_text_control($wp_customize, 'hta_submit_intro', 'hta_submit', __('מבוא', 'hta-landing'), 'החברות המשתתפות יקבלו חשיפה כחלק מהלך ארצי רחב היקף, ייראות באתר המרכזי של שבוע ההיי-טק הישראלי, ויהיו חלק מהלך לאומי שיוצר השפעה אמיתית.', 'textarea');
    hta_add_text_control($wp_customize, 'hta_submit_form_title', 'hta_submit', __('כותרת טופס', 'hta-landing'), 'הגשת אירוע משלכם');
    hta_add_text_control($wp_customize, 'hta_submit_form_text', 'hta_submit', __('טקסט טופס', 'hta-landing'), 'מלאו את הפרטים ושלחו להגשה.');
    hta_add_text_control($wp_customize, 'hta_submit_note_title', 'hta_submit', __('כותרת הערה', 'hta-landing'), 'ניהול אירועים דיגיטלי');
    hta_add_text_control($wp_customize, 'hta_submit_note_text', 'hta_submit', __('טקסט הערה', 'hta-landing'), 'האירוע שלכם יכלול דף ייעודי, כלי הרשמה ומעקב, ועדכונים ישירים בפלטפורמה.', 'textarea');

    $benefits = [
        'חשיפה רחבה באתר המרכזי הארצי',
        'חיבור לשותפים מובילים וקהילות',
        'ליווי מקצועי של צוות האיגוד',
        'כלים לניהול הרשמות ומשתתפים',
        'קישור לאקוסיסטם הישראלי והגלובלי',
    ];
    foreach ($benefits as $i => $text) {
        $n = $i + 1;
        hta_add_text_control($wp_customize, "hta_submit_benefit_{$n}", 'hta_submit', sprintf(__('יתרון %d', 'hta-landing'), $n), $text);
    }
}

function hta_register_footer_section(WP_Customize_Manager $wp_customize): void
{
    $wp_customize->add_section('hta_footer', [
        'title' => __('פוטר', 'hta-landing'),
        'panel' => 'hta_landing',
    ]);

    hta_add_text_control($wp_customize, 'hta_footer_copy', 'hta_footer', __('קרדיט', 'hta-landing'), '© 2026 איגוד ההיי-טק הישראלי. כל הזכויות שמורות.');

    $networks = [
        'linkedin'  => [
            'label' => __('LinkedIn', 'hta-landing'),
            'url'   => 'https://www.linkedin.com/company/israeli-hi-tech-association?originalSubdomain=il',
        ],
        'facebook'  => [
            'label' => __('Facebook', 'hta-landing'),
            'url'   => 'https://www.facebook.com/IsraeliHighTechAssociation/',
        ],
        'instagram' => [
            'label' => __('Instagram', 'hta-landing'),
            'url'   => 'https://www.instagram.com/israeli_high_tech_association/',
        ],
    ];

    foreach ($networks as $slug => $network) {
        $enable_id = "hta_social_{$slug}_enable";
        $url_id    = "hta_social_{$slug}_url";

        $wp_customize->add_setting($enable_id, [
            'default'           => true,
            'sanitize_callback' => static function ($value): bool {
                return (bool) $value;
            },
            'transport'         => 'refresh',
        ]);
        $wp_customize->add_control($enable_id, [
            'label'   => sprintf(__('הצג %s', 'hta-landing'), $network['label']),
            'section' => 'hta_footer',
            'type'    => 'checkbox',
        ]);

        $wp_customize->add_setting($url_id, [
            'default'           => $network['url'],
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ]);
        $wp_customize->add_control($url_id, [
            'label'   => sprintf(__('קישור %s', 'hta-landing'), $network['label']),
            'section' => 'hta_footer',
            'type'    => 'url',
        ]);
    }
}
