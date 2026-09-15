<?php
/**
 * Scripts and styles.
 *
 * @package HTA_Landing
 */

if (! defined('ABSPATH')) {
    exit;
}

function hta_enqueue_assets(): void
{
    wp_enqueue_style(
        'hta-fonts',
        'https://fonts.googleapis.com/css2?family=Assistant:wght@300;400;500;600;700;800&family=Electrolize&display=swap',
        [],
        null
    );

    wp_enqueue_style(
        'swiper',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
        [],
        '11.0.0'
    );

    wp_enqueue_style(
        'hta-theme',
        HTA_URI . '/assets/css/theme.css',
        ['hta-fonts', 'swiper'],
        HTA_VERSION
    );

    wp_add_inline_style('hta-theme', hta_css_variables());

    wp_enqueue_script(
        'tailwindcss',
        'https://cdn.tailwindcss.com',
        [],
        null,
        false
    );

    wp_add_inline_script('tailwindcss', hta_tailwind_config(), 'before');

    wp_enqueue_script(
        'gsap',
        'https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/gsap.min.js',
        [],
        '3.15.0',
        true
    );

    wp_enqueue_script(
        'gsap-scrolltrigger',
        'https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/ScrollTrigger.min.js',
        ['gsap'],
        '3.15.0',
        true
    );

    wp_enqueue_script(
        'gsap-splittext',
        'https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/SplitText.min.js',
        ['gsap'],
        '3.15.0',
        true
    );

    wp_enqueue_script(
        'swiper',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
        [],
        '11.0.0',
        true
    );

    if (is_front_page()) {
        wp_enqueue_script(
            'lordicon',
            'https://cdn.lordicon.com/lordicon.js',
            [],
            null,
            true
        );
    }

    wp_enqueue_script(
        'hta-city-autocomplete',
        HTA_URI . '/assets/js/city-autocomplete.js',
        [],
        HTA_VERSION,
        true
    );

    wp_enqueue_script(
        'hta-theme',
        HTA_URI . '/assets/js/theme.js',
        ['gsap', 'gsap-scrolltrigger', 'gsap-splittext', 'swiper', 'hta-city-autocomplete'],
        HTA_VERSION,
        true
    );

    wp_localize_script('hta-theme', 'htaLanding', [
        'ajaxUrl'   => admin_url('admin-ajax.php'),
        'nonce'     => wp_create_nonce('hta_submit_event'),
        'citiesUrl' => HTA_URI . '/assets/data/israel-cities.json',
        'i18n'      => [
            'sending'          => __('שולח...', 'hta-landing'),
            'success'          => __('ההגשה התקבלה ותעבור לסקירה.', 'hta-landing'),
            'error'            => __('אירעה שגיאה. נסו שוב.', 'hta-landing'),
            'agree'            => __('יש לאשר את התקנון, מדיניות הפרטיות ואת נכונות המידע.', 'hta-landing'),
            'city'             => __('יש לבחור עיר או יישוב מתוך הרשימה.', 'hta-landing'),
            'cityRequired'     => __('יש לבחור עיר או יישוב.', 'hta-landing'),
            'name'             => __('יש להזין שם אירוע.', 'hta-landing'),
            'company'          => __('יש להזין שם חברה מארחת.', 'hta-landing'),
            'datetime'         => __('יש לבחור תאריך ושעה.', 'hta-landing'),
            'datetimeInvalid'  => __('תאריך או שעה לא תקינים.', 'hta-landing'),
            'fixFields'        => __('יש לתקן את השדות המסומנים.', 'hta-landing'),
        ],
    ]);

    $shader_src = HTA_URI . '/assets/js/topics-shader.js';
    if (hta_mod('hta_topics_shader_enable', true)) {
        if (function_exists('wp_enqueue_script_module')) {
            wp_enqueue_script_module('hta-topics-shader', $shader_src, [], HTA_VERSION);
        } else {
            wp_enqueue_script('hta-topics-shader', $shader_src, [], HTA_VERSION, true);
            wp_script_add_data('hta-topics-shader', 'type', 'module');
        }
    }
}
add_action('wp_enqueue_scripts', 'hta_enqueue_assets');

function hta_css_variables(): string
{
    $c1 = hta_color('hta_color_primary_1', '#22d3ee');
    $c2 = hta_color('hta_color_primary_2', '#2563eb');
    $c3 = hta_color('hta_color_primary_3', '#8b5cf6');
    $btn_bg      = hta_color('hta_color_btn_bg', $c1);
    $btn_text    = hta_color('hta_color_btn_text', '#020617');
    $kicker_color = hta_color('hta_color_hero_kicker', $c1);
    $overlay_color = hta_color('hta_hero_overlay_color', '#020617');
    $overlay_opacity = max(0, min(100, absint(hta_mod('hta_hero_overlay_opacity', 70))));
    $overlay_alpha = number_format($overlay_opacity / 100, 2, '.', '');

    $hero         = absint(hta_mod('hta_font_hero', 56));
    $hero_kicker  = absint(hta_mod('hta_font_hero_kicker', 14));
    $hero_tagline = absint(hta_mod('hta_font_hero_tagline', 50));
    $hero_text    = absint(hta_mod('hta_font_hero_text', 18));
    $section      = absint(hta_mod('hta_font_section', 36));
    $body         = absint(hta_mod('hta_font_body', 16));
    $paragraph    = absint(hta_mod('hta_font_paragraph', 16));
    $list         = absint(hta_mod('hta_font_list', 16));
    $nav          = absint(hta_mod('hta_font_nav', 22));

    $fonts = [
        'hero'         => $hero,
        'hero-kicker'  => $hero_kicker,
        'hero-tagline' => $hero_tagline,
        'hero-text'    => $hero_text,
        'section'      => $section,
        'body'         => $body,
        'paragraph'    => $paragraph,
        'list'         => $list,
        'nav'          => $nav,
    ];
    $font_ids = [
        'hero'         => 'hta_font_hero',
        'hero-kicker'  => 'hta_font_hero_kicker',
        'hero-tagline' => 'hta_font_hero_tagline',
        'hero-text'    => 'hta_font_hero_text',
        'section'      => 'hta_font_section',
        'body'         => 'hta_font_body',
        'paragraph'    => 'hta_font_paragraph',
        'list'         => 'hta_font_list',
        'nav'          => 'hta_font_nav',
    ];

    $weight_hero_main   = absint(hta_mod('hta_weight_hero_main', 800));
    $weight_hero_kicker = absint(hta_mod('hta_weight_hero_kicker', 600));
    $weight_hero        = absint(hta_mod('hta_weight_hero', 700));
    $weight_hero_text = absint(hta_mod('hta_weight_hero_text', 300));
    $weight_section   = absint(hta_mod('hta_weight_section', 800));
    $weight_paragraph = absint(hta_mod('hta_weight_paragraph', 300));
    $weight_list      = absint(hta_mod('hta_weight_list', 300));

    $title_fill     = hta_text_fill('hta_hero_title');
    $tagline_fill   = hta_text_fill('hta_hero_tagline');
    $title_shadow   = hta_text_shadow('hta_hero_title', ['enabled' => true, 'x' => -1, 'y' => 2, 'blur' => 3, 'color' => '#000000']);
    $tagline_shadow = hta_text_shadow('hta_hero_tagline', ['enabled' => false, 'x' => 0, 'y' => 0, 'blur' => 0, 'color' => '#000000']);

    $vars = [
        '--hta-primary-1'          => $c1,
        '--hta-primary-2'          => $c2,
        '--hta-primary-3'          => $c3,
        '--hta-btn-bg'             => $btn_bg,
        '--hta-btn-text'           => $btn_text,
        '--hta-hero-kicker'        => $kicker_color,
        '--hta-hero-overlay-color' => $overlay_color,
        '--hta-hero-overlay-opacity' => $overlay_alpha,
        '--hta-hero-title-color'   => $title_fill['color'],
        '--hta-hero-title-gradient' => $title_fill['gradient'],
        '--hta-hero-title-shadow'  => $title_shadow['shadow'],
        '--hta-hero-title-drop-shadow' => $title_shadow['filter'],
        '--hta-hero-tagline-color' => $tagline_fill['color'],
        '--hta-hero-tagline-gradient' => $tagline_fill['gradient'],
        '--hta-hero-tagline-shadow' => $tagline_shadow['shadow'],
        '--hta-hero-tagline-drop-shadow' => $tagline_shadow['filter'],
        '--hta-weight-hero-main'   => (string) $weight_hero_main,
        '--hta-weight-hero-kicker' => (string) $weight_hero_kicker,
        '--hta-weight-hero'        => (string) $weight_hero,
        '--hta-weight-hero-text'   => (string) $weight_hero_text,
        '--hta-weight-section'     => (string) $weight_section,
        '--hta-weight-paragraph'   => (string) $weight_paragraph,
        '--hta-weight-list'        => (string) $weight_list,
        '--hta-gradient'           => sprintf('linear-gradient(135deg,%s 0%%,%s 50%%,%s 100%%)', $c1, $c2, $c3),
    ];

    $tablet_vars = [];
    $mobile_vars = [];
    foreach ($fonts as $slug => $desktop) {
        $id     = $font_ids[$slug];
        $tablet = absint(hta_mod($id . '_tablet', $desktop));
        $mobile = absint(hta_mod($id . '_mobile', $desktop));
        $vars['--hta-font-' . $slug . '-desktop'] = $desktop . 'px';
        $vars['--hta-font-' . $slug . '-tablet']  = $tablet . 'px';
        $vars['--hta-font-' . $slug . '-mobile']  = $mobile . 'px';
        $vars['--hta-font-' . $slug]              = 'var(--hta-font-' . $slug . '-desktop)';
        $tablet_vars[] = '--hta-font-' . $slug . ':var(--hta-font-' . $slug . '-tablet)';
        $mobile_vars[] = '--hta-font-' . $slug . ':var(--hta-font-' . $slug . '-mobile)';
    }

    $pairs = [];
    foreach ($vars as $name => $value) {
        $pairs[] = $name . ':' . $value;
    }

    return ':root{' . implode(';', $pairs) . ';}'
        . '@media (max-width:1023px){:root{' . implode(';', $tablet_vars) . ';}}'
        . '@media (max-width:767px){:root{' . implode(';', $mobile_vars) . ';}}';
}

function hta_tailwind_config(): string
{
    return <<<'JS'
window.tailwind = window.tailwind || {};
tailwind.config = {
  theme: {
    borderRadius: {
      none: '0px',
      sm: '0px',
      DEFAULT: '0px',
      md: '0px',
      lg: '0px',
      xl: '0px',
      '2xl': '0px',
      '3xl': '0px',
      full: '0px'
    },
    extend: {
      colors: {
        hta: {
          1: 'var(--hta-primary-1)',
          2: 'var(--hta-primary-2)',
          3: 'var(--hta-primary-3)'
        }
      },
      fontFamily: {
        sans: ['Assistant', 'sans-serif']
      },
      fontSize: {
        sm: ['0.875rem', { lineHeight: '1.55rem' }]
      }
    }
  },
  corePlugins: {
    borderRadius: true
  }
};
JS;
}

function hta_resource_hints(array $urls, string $relation_type): array
{
    if ('preconnect' === $relation_type) {
        $urls[] = ['href' => 'https://fonts.googleapis.com', 'crossorigin' => false];
        $urls[] = ['href' => 'https://fonts.gstatic.com', 'crossorigin' => true];
        $urls[] = 'https://cdn.jsdelivr.net';
        $urls[] = 'https://cdn.tailwindcss.com';
        $urls[] = 'https://esm.sh';
        $urls[] = 'https://ruucm.github.io';
    }
    return $urls;
}
add_filter('wp_resource_hints', 'hta_resource_hints', 10, 2);

function hta_dequeue_block_chrome(): void
{
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('global-styles');
    wp_dequeue_style('classic-theme-styles');
}
add_action('wp_enqueue_scripts', 'hta_dequeue_block_chrome', 100);

function hta_customize_controls_assets(): void
{
    wp_enqueue_style(
        'hta-customizer-controls',
        HTA_URI . '/assets/css/customizer-controls.css',
        [],
        HTA_VERSION
    );
    wp_enqueue_script(
        'hta-customizer-controls',
        HTA_URI . '/assets/js/customizer-controls.js',
        ['jquery', 'customize-controls'],
        HTA_VERSION,
        true
    );
}
add_action('customize_controls_enqueue_scripts', 'hta_customize_controls_assets');

function hta_customize_preview_assets(): void
{
    wp_enqueue_script(
        'hta-customizer-preview',
        HTA_URI . '/assets/js/customizer-preview.js',
        ['customize-preview'],
        HTA_VERSION,
        true
    );
}
add_action('customize_preview_init', 'hta_customize_preview_assets');
