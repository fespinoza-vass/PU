require([
    'jquery',
    'mage/translate',
    'domReady!'
], function($, $t) {
    $(document).ready(function() {
        $(document).ajaxComplete(function () {
            let $subscribeLabelCheck = $('input[name="additional[subscribe]"] + label');
            let subscribeText = $t('I accept Shipping Policy of')
                + ' <strong id="comunicaciones">' + $t('Advertising and Promotional Communications')  + '</strong>.'
            if ($subscribeLabelCheck.html() !== subscribeText) {
                $subscribeLabelCheck.html(subscribeText);
            }

            let $privacyLabelCheck = $('input[name="sidebar[additional][checkbox_privacidad]"] + label');
            let privacyText = $t('I have read and accept the')
                + ' <strong id="tyc">' + $t('Terms and Conditions') + ' </strong>' + $t('and')
                + ' <strong id="privacidad">' + $t('the Personal Data Protection Policy') + '</strong>.'
            if ($privacyLabelCheck.html() !== privacyText) {
                $privacyLabelCheck.html(privacyText);
            }

            let $checkRegister = $('input[name="additional[register]"]');
            if ($checkRegister.length) {
                $checkRegister.parent().hide();
                if (!$checkRegister.is(':checked')) {
                    $checkRegister.click();
                }
            }
        });
    });
});
