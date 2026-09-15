(function ($) {
    'use strict';

    $(function () {
        var $input = $('#hta_seo_og_image');
        var $preview = $('.hta-seo-og-preview');
        var $remove = $('#hta_seo_og_image_remove');
        var frame;

        if (!$input.length) {
            return;
        }

        $('#hta_seo_og_image_select').on('click', function (event) {
            event.preventDefault();

            if (frame) {
                frame.open();
                return;
            }

            frame = wp.media({
                title: 'בחירת OG Image',
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
                $preview.html('<img src="' + url + '" alt="" style="max-width:100%;height:auto;display:block;">');
                $remove.prop('disabled', false);
            });

            frame.open();
        });

        $remove.on('click', function (event) {
            event.preventDefault();
            $input.val('0');
            $preview.html('<span style="color:#646970;font-size:12px;">אין תמונה</span>');
            $remove.prop('disabled', true);
        });
    });
}(jQuery));
