define([
    "jquery",
    "Magento_Ui/js/modal/modal",
    "mage/cookies",
    "domReady!"
], function ($, modal) {
    "use strict";

    $('.row-popup-home').attr('id', 'modal');
    if (! $.cookie('cookiemodal')) { //check if cookiemodal doesn't exist
        $.cookie('cookiemodal', 'ok'); //we set a cookie name="cookiemodal" value="ok"
        var options = {
            type: 'popup',
            responsive: true,
            buttons: [{
                text: $.mage.__('Ok'),
                class: '',
                click: function () {
                    this.closeModal();
                }
            }]
        };

        var popup = modal(options, $('#modal'));
        $('#modal').modal('openModal');
    }
});