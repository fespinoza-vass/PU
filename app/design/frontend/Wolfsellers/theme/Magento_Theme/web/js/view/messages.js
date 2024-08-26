define([
    'jquery',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'underscore',
    'escaper',
    'jquery/jquery-storageapi',
    'jquery-ui-modules/effect-blind'
], function ($, Component, customerData, _, escaper) {
    'use strict';

    return Component.extend({
        defaults: {
            cookieMessages: [],
            messages: [],
            allowedTags: ['div', 'span', 'b', 'strong', 'i', 'em', 'u', 'a']
        },

        /**
         * Extends Component object by storage observable messages.
         */
        initialize: function () {
            this._super();

            this.cookieMessages = _.unique($.cookieStorage.get('mage-messages'), 'text');
            this.messages = customerData.get('messages').extend({
                disposableCustomerData: 'messages'
            });

            // Force to clean obsolete messages
            if (!_.isEmpty(this.messages().messages)) {
                customerData.set('messages', {});
            }

            $.cookieStorage.set('mage-messages', '');

            var shoppingCartUrl = window.checkout.shoppingCartUrl;
            $('body').on('click', "div.checkout-redirect-cart", function (event) {
                if(!_.isEmpty(shoppingCartUrl)){
                    window.location.href = shoppingCartUrl;
                }
            });
        },

        /**
         * Prepare the given message to be rendered as HTML
         *
         * @param {String} message
         * @return {String}
         */
        prepareMessageForHtml: function (message) {
            if(message.indexOf('msg-add-success') >= 0){
                $(".messages").addClass('main-msg-add-success');
            }
            $(".messages").show();
            setTimeout(function() {
                $(".messages").hide('blind', {}, 500);
                // $(".messages").removeClass('main-msg-add-success');
            }, 3000);
            return message;
            //return escaper.escapeHtml(message, this.allowedTags);
        }

    });
});
