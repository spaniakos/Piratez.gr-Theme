/**
 * Customizer: Regenerate llms.txt & sitemap.xml button
 *
 * @package piratez_cyberpunk
 */
(function ($) {
    'use strict';

    $(function () {
        $(document).on('click', '#piratez-regenerate-ai-index-btn', function () {
            var $btn = $(this);
            var $status = $('#piratez-ai-index-status');

            $btn.prop('disabled', true);
            $status.removeClass('success error').text('');

            $.post(
                typeof piratezAiIndex !== 'undefined' ? piratezAiIndex.ajaxUrl : ajaxurl,
                {
                    action: 'piratez_regenerate_ai_index',
                    nonce: typeof piratezAiIndex !== 'undefined' ? piratezAiIndex.nonce : ''
                }
            )
                .done(function (r) {
                    if (r.success && r.data && r.data.message) {
                        $status.addClass('success').text(r.data.message);
                    } else {
                        $status.addClass('error').text(r.data && r.data.message ? r.data.message : 'Error');
                    }
                })
                .fail(function () {
                    $status.addClass('error').text('Request failed');
                })
                .always(function () {
                    $btn.prop('disabled', false);
                });
        });
    });
})(jQuery);
