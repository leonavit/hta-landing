(function ($) {
    'use strict';

    $(function () {
        var $box = $('#hta_promoted');
        var $wrap = $('#hta-admin-banner-wrap');
        var $input = $('#hta_banner_id');
        var $preview = $('#hta-admin-banner-preview');
        var $remove = $('#hta_banner_remove');
        var frame;

        function syncPromoted() {
            if (!$box.length || !$wrap.length) {
                return;
            }
            $wrap.prop('hidden', !$box.prop('checked'));
        }

        $box.on('change', syncPromoted);
        syncPromoted();

        $('#hta_banner_select').on('click', function (event) {
            event.preventDefault();

            if (frame) {
                frame.open();
                return;
            }

            frame = wp.media({
                title: 'בחירת באנר',
                button: { text: 'שימוש בתמונה' },
                library: { type: 'image' },
                multiple: false
            });

            frame.on('select', function () {
                var attachment = frame.state().get('selection').first().toJSON();
                var url = (attachment.sizes && attachment.sizes.medium && attachment.sizes.medium.url)
                    ? attachment.sizes.medium.url
                    : attachment.url;

                $input.val(attachment.id);
                $preview.html('<img src="' + url + '" alt="">');
                $remove.prop('disabled', false);
            });

            frame.open();
        });

        $remove.on('click', function (event) {
            event.preventDefault();
            $input.val('0');
            $preview.html('<span class="description">אין באנר</span>');
            $remove.prop('disabled', true);
        });
    });
}(jQuery));
