/* global stlmsSubscribeObject */
(function ($) {
    'use strict';

    $(function () {
        if (typeof stlmsSubscribeObject === 'undefined') {
            return;
        }

        var $notice = $('#stlms-subscribe-notice');

        if (!$notice.length) {
            return;
        }

        var $form = $('#stlms-subscribe-form');
        var $emailInput = $('#stlms_subscribe_email');
        var $message = $('#stlms-subscribe-message');
        var $submitBtn = $('#stlms-subscribe-submit-btn');
        var $spinner = $form.find('.spinner');

        function resetSubmitButton() {
            $submitBtn.prop('disabled', false);
            $spinner.removeClass('is-active');
        }

        function showMessage(type, message) {
            $message
                .removeClass('stlms-notice-error stlms-notice-success')
                .addClass('stlms-notice-' + type)
                .text(message)
                .slideDown(200);
            $form.toggleClass('stlms-has-error', 'error' === type);
        }

        function hideNotice() {
            $notice.slideUp(300, function () {
                $notice.remove();
            });
        }

        function dismissNotice() {
            // Remember the dismissal so the notice stays hidden on later screens.
            $.post(stlmsSubscribeObject.ajaxurl, {
                action: 'stlms_dismiss_subscribe_notice',
                nonce: stlmsSubscribeObject.nonce
            });

            hideNotice();
        }

        $notice.on('click', '#stlms-subscribe-cancel-btn', function (e) {
            e.preventDefault();
            dismissNotice();
        });

        $form.on('submit', function (e) {
            e.preventDefault();

            var emailVal = $.trim($emailInput.val());

            if (!emailVal || !emailVal.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                showMessage('error', stlmsSubscribeObject.i18n.invalidEmail);
                $emailInput.trigger('focus');
                return;
            }

            $submitBtn.prop('disabled', true);
            $spinner.addClass('is-active');
            $message.slideUp(200).removeClass('stlms-notice-error stlms-notice-success');
            $form.removeClass('stlms-has-error');

            $.ajax({
                url: stlmsSubscribeObject.ajaxurl,
                type: 'POST',
                data: {
                    action: 'stlms_submit_subscribe_email',
                    nonce: stlmsSubscribeObject.nonce,
                    email: emailVal
                },
                success: function (response) {
                    if (response && response.success) {
                        $notice.addClass('is-success');
                        $notice.find('.stlms-subscribe-form-view').hide();
                        $notice.find('.stlms-subscribe-success-view').fadeIn(200);
                        setTimeout(hideNotice, 6000);
                        return;
                    }

                    var errorMsg = response && response.data && response.data.message
                        ? response.data.message
                        : stlmsSubscribeObject.i18n.serverError;
                    showMessage('error', errorMsg);
                    resetSubmitButton();
                },
                error: function (jqXHR) {
                    var errorMsg = jqXHR && jqXHR.responseJSON && jqXHR.responseJSON.data && jqXHR.responseJSON.data.message
                        ? jqXHR.responseJSON.data.message
                        : stlmsSubscribeObject.i18n.serverError;
                    showMessage('error', errorMsg);
                    resetSubmitButton();
                }
            });
        });
    });
})(jQuery);
