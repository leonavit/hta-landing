(function ($) {
    'use strict';

    function isRtl($el) {
        return $el.css('direction') === 'rtl' || $('html').attr('dir') === 'rtl' || $('body').hasClass('rtl');
    }

    function bindRange(control) {
        if (!control || !control.params || control.params.type !== 'range') {
            return;
        }

        function attach() {
            var $input = control.container.find('input[type="range"]');
            if (!$input.length || $input.data('htaBound')) {
                return;
            }

            $input.data('htaBound', true);
            $input.wrap('<div class="hta-range-track"></div>');
            var $track = $input.parent();
            var $bubble = $('<span class="hta-range-bubble" aria-hidden="true"></span>');
            $track.prepend($bubble);

            function update() {
                var min = parseFloat($input.attr('min')) || 0;
                var max = parseFloat($input.attr('max')) || 100;
                var val = parseFloat($input.val());
                if (isNaN(val)) {
                    val = min;
                }
                var pct = max === min ? 0 : (val - min) / (max - min);
                var width = $input.outerWidth() || $track.width();
                var thumb = 16;
                var x = pct * (width - thumb) + thumb / 2;
                if (isRtl($input)) {
                    x = (1 - pct) * (width - thumb) + thumb / 2;
                }
                $bubble.text(val + (control.id.indexOf('weight') !== -1 ? '' : 'px')).css('left', Math.round(x) + 'px');
            }

            $input.on('input change', update);
            $(window).on('resize', update);
            update();
            setTimeout(update, 80);
            setTimeout(update, 300);
        }

        if (control.deferred && control.deferred.embedded) {
            control.deferred.embedded.done(attach);
        } else {
            attach();
        }
    }

    wp.customize.bind('ready', function () {
        wp.customize.control.each(bindRange);

        function bindFillModeVisibility(prefix) {
            wp.customize(prefix + '_color_mode', function (setting) {
                setting.bind(function (mode) {
                    var isGradient = mode === 'gradient';
                    var dirControl = wp.customize.control(prefix + '_gradient_dir');
                    if (dirControl && dirControl.active) {
                        dirControl.active.set(isGradient);
                    }
                    [2, 3].forEach(function (n) {
                        var control = wp.customize.control(prefix + '_color_' + n);
                        if (control && control.active) {
                            control.active.set(isGradient);
                        }
                    });
                });
            });
        }

        bindFillModeVisibility('hta_hero_title');
        bindFillModeVisibility('hta_hero_tagline');

        function bindShadowVisibility(prefix) {
            wp.customize(prefix + '_shadow_enabled', function (setting) {
                setting.bind(function (on) {
                    ['x', 'y', 'blur', 'color'].forEach(function (key) {
                        var control = wp.customize.control(prefix + '_shadow_' + key);
                        if (control && control.active) {
                            control.active.set(!!on);
                        }
                    });
                });
            });
        }

        bindShadowVisibility('hta_hero_title');
        bindShadowVisibility('hta_hero_tagline');
    });
    wp.customize.control.bind('add', bindRange);
})(jQuery);
