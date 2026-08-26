(function ($) {
    'use strict';

    function setVar(name, value) {
        document.documentElement.style.setProperty(name, value);
    }

    function bindColor(setting, cssVar) {
        wp.customize(setting, function (value) {
            value.bind(function (to) {
                setVar(cssVar, to);
            });
        });
    }

    function bindPx(setting, cssVar) {
        wp.customize(setting, function (value) {
            value.bind(function (to) {
                setVar(cssVar, parseInt(to, 10) + 'px');
            });
        });
    }

    function bindNum(setting, cssVar) {
        wp.customize(setting, function (value) {
            value.bind(function (to) {
                setVar(cssVar, String(parseInt(to, 10)));
            });
        });
    }

    bindColor('hta_color_primary_1', '--hta-primary-1');
    bindColor('hta_color_primary_2', '--hta-primary-2');
    bindColor('hta_color_primary_3', '--hta-primary-3');
    bindColor('hta_color_btn_bg', '--hta-btn-bg');
    bindColor('hta_color_btn_text', '--hta-btn-text');
    bindColor('hta_color_hero_kicker', '--hta-hero-kicker');
    bindColor('hta_hero_overlay_color', '--hta-hero-overlay-color');

    wp.customize('hta_hero_overlay_opacity', function (value) {
        value.bind(function (to) {
            var n = Math.max(0, Math.min(100, parseInt(to, 10) || 0));
            setVar('--hta-hero-overlay-opacity', String(n / 100));
        });
    });

    bindPx('hta_font_hero', '--hta-font-hero-desktop');
    bindPx('hta_font_hero_kicker', '--hta-font-hero-kicker-desktop');
    bindPx('hta_font_hero_tagline', '--hta-font-hero-tagline-desktop');
    bindPx('hta_font_hero_text', '--hta-font-hero-text-desktop');
    bindPx('hta_font_section', '--hta-font-section-desktop');
    bindPx('hta_font_body', '--hta-font-body-desktop');
    bindPx('hta_font_paragraph', '--hta-font-paragraph-desktop');
    bindPx('hta_font_list', '--hta-font-list-desktop');
    bindPx('hta_font_nav', '--hta-font-nav-desktop');

    bindPx('hta_font_hero_tablet', '--hta-font-hero-tablet');
    bindPx('hta_font_hero_kicker_tablet', '--hta-font-hero-kicker-tablet');
    bindPx('hta_font_hero_tagline_tablet', '--hta-font-hero-tagline-tablet');
    bindPx('hta_font_hero_text_tablet', '--hta-font-hero-text-tablet');
    bindPx('hta_font_section_tablet', '--hta-font-section-tablet');
    bindPx('hta_font_body_tablet', '--hta-font-body-tablet');
    bindPx('hta_font_paragraph_tablet', '--hta-font-paragraph-tablet');
    bindPx('hta_font_list_tablet', '--hta-font-list-tablet');
    bindPx('hta_font_nav_tablet', '--hta-font-nav-tablet');

    bindPx('hta_font_hero_mobile', '--hta-font-hero-mobile');
    bindPx('hta_font_hero_kicker_mobile', '--hta-font-hero-kicker-mobile');
    bindPx('hta_font_hero_tagline_mobile', '--hta-font-hero-tagline-mobile');
    bindPx('hta_font_hero_text_mobile', '--hta-font-hero-text-mobile');
    bindPx('hta_font_section_mobile', '--hta-font-section-mobile');
    bindPx('hta_font_body_mobile', '--hta-font-body-mobile');
    bindPx('hta_font_paragraph_mobile', '--hta-font-paragraph-mobile');
    bindPx('hta_font_list_mobile', '--hta-font-list-mobile');
    bindPx('hta_font_nav_mobile', '--hta-font-nav-mobile');

    bindNum('hta_weight_hero_main', '--hta-weight-hero-main');
    bindNum('hta_weight_hero_kicker', '--hta-weight-hero-kicker');
    bindNum('hta_weight_hero', '--hta-weight-hero');
    bindNum('hta_weight_hero_text', '--hta-weight-hero-text');
    bindNum('hta_weight_section', '--hta-weight-section');
    bindNum('hta_weight_paragraph', '--hta-weight-paragraph');
    bindNum('hta_weight_list', '--hta-weight-list');

    function updateGradient() {
        var c1 = wp.customize('hta_color_primary_1')();
        var c2 = wp.customize('hta_color_primary_2')();
        var c3 = wp.customize('hta_color_primary_3')();
        setVar('--hta-gradient', 'linear-gradient(135deg,' + c1 + ' 0%,' + c2 + ' 50%,' + c3 + ' 100%)');
    }

    ['hta_color_primary_1', 'hta_color_primary_2', 'hta_color_primary_3'].forEach(function (id) {
        wp.customize(id, function (value) {
            value.bind(updateGradient);
        });
    });

    function bindTextFill(prefix, colorVar, gradientVar) {
        function apply() {
            var dirSetting = wp.customize(prefix + '_gradient_dir');
            var dir = (dirSetting && dirSetting()) || 'to left';
            var c1 = (wp.customize(prefix + '_color_1') && wp.customize(prefix + '_color_1')()) || '#ffffff';
            var c2 = (wp.customize(prefix + '_color_2') && wp.customize(prefix + '_color_2')()) || '#ffffff';
            var c3 = (wp.customize(prefix + '_color_3') && wp.customize(prefix + '_color_3')()) || '#ffffff';
            setVar(colorVar, c1);
            setVar(gradientVar, 'linear-gradient(' + dir + ', ' + c1 + ' 0%, ' + c2 + ' 50%, ' + c3 + ' 100%)');
        }

        [prefix + '_gradient_dir', prefix + '_color_1', prefix + '_color_2', prefix + '_color_3'].forEach(function (id) {
            wp.customize(id, function (value) {
                value.bind(apply);
            });
        });
    }

    bindTextFill('hta_hero_title', '--hta-hero-title-color', '--hta-hero-title-gradient');
    bindTextFill('hta_hero_tagline', '--hta-hero-tagline-color', '--hta-hero-tagline-gradient');

    function bindTextShadow(prefix, shadowVar, dropVar, defaults) {
        defaults = defaults || { enabled: true, x: -1, y: 2, blur: 3, color: '#000000' };

        function apply() {
            var enabled = !!(wp.customize(prefix + '_shadow_enabled') && wp.customize(prefix + '_shadow_enabled')());
            if (!enabled) {
                setVar(shadowVar, 'none');
                setVar(dropVar, 'none');
                return;
            }
            var x = parseInt((wp.customize(prefix + '_shadow_x') && wp.customize(prefix + '_shadow_x')()) || defaults.x, 10);
            var y = parseInt((wp.customize(prefix + '_shadow_y') && wp.customize(prefix + '_shadow_y')()) || defaults.y, 10);
            var blur = Math.max(0, parseInt((wp.customize(prefix + '_shadow_blur') && wp.customize(prefix + '_shadow_blur')()) || defaults.blur, 10));
            var color = (wp.customize(prefix + '_shadow_color') && wp.customize(prefix + '_shadow_color')()) || defaults.color;
            var value = x + 'px ' + y + 'px ' + blur + 'px ' + color;
            setVar(shadowVar, value);
            setVar(dropVar, 'drop-shadow(' + value + ')');
        }

        [prefix + '_shadow_enabled', prefix + '_shadow_x', prefix + '_shadow_y', prefix + '_shadow_blur', prefix + '_shadow_color'].forEach(function (id) {
            wp.customize(id, function (value) {
                value.bind(apply);
            });
        });
    }

    bindTextShadow('hta_hero_title', '--hta-hero-title-shadow', '--hta-hero-title-drop-shadow', { enabled: true, x: -1, y: 2, blur: 3, color: '#000000' });
    bindTextShadow('hta_hero_tagline', '--hta-hero-tagline-shadow', '--hta-hero-tagline-drop-shadow', { enabled: false, x: 0, y: 0, blur: 0, color: '#000000' });
})(jQuery);
