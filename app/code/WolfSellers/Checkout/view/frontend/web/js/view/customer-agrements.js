define([
    'ko',
    'jquery',
    'uiComponent',
    'Magento_CheckoutAgreements/js/model/agreements-modal'
], function (ko, $, Component, agreementsModal) {
    'use strict';

    var checkoutConfig = window.checkoutConfig,
        agreementManualMode = 1,
        agreementsConfig = checkoutConfig ? checkoutConfig.checkoutAgreements : {};

    return Component.extend({
        defaults: {
            template: 'WolfSellers_Checkout/customer_agreements'
        },
        isVisible: agreementsConfig.isEnabled,
        agreements: agreementsConfig.agreements,
        modalTitle: ko.observable(null),
        modalContent: ko.observable(null),
        contentHeight: ko.observable(null),
        modalWindow: null,

        initialize: function () {
            this._super();
            if(this.name.includes('customer-fieldsets')){
                this.isVisible = true;
            }
        },

        /**
         * Checks if agreement required
         *
         * @param {Object} element
         */
        isAgreementRequired: function (element) {
            return element.mode == agreementManualMode; //eslint-disable-line eqeqeq
        },

        /**
         * Show agreement content in modal
         *
         * @param {Object} element
         */
        showContentAgreement: function (element) {
            this.modalTitle(element.checkboxText);
            this.modalContent(element.content);
            this.contentHeight(element.contentHeight ? element.contentHeight : 'auto');
        },

        /**
         * build a unique id for the term checkbox
         *
         * @param {Object} context - the ko context
         * @param {Number} agreementId
         */
        getCheckboxId: function (context, agreementId) {
            var paymentMethodName = '',
                paymentMethodRenderer = context.$parents[1];

            // corresponding payment method fetched from parent context
            if (paymentMethodRenderer) {
                // item looks like this: {title: "Check / Money order", method: "checkmo"}
                paymentMethodName = paymentMethodRenderer.item ?
                    paymentMethodRenderer.item.method : '';
            }

            return 'agreement_' + paymentMethodName + '_' + agreementId;
        },

        click: function (context, agreementId) {
            var paymentMethodName = '',
                paymentMethodRenderer = context.$parents[1];
            // corresponding payment method fetched from parent context
            if (paymentMethodRenderer) {
                // item looks like this: {title: "Check / Money order", method: "checkmo"}
                paymentMethodName = paymentMethodRenderer.item ?
                    paymentMethodRenderer.item.method : '';
            }
            if (jQuery( ".actions-toolbar").find("#stepFormCheck")){
                if(jQuery('#agreement__' + agreementId).is(":checked")){
                    $( ".actions-toolbar").find("#stepFormContinue").removeAttr("disabled");
                } else {
                    $( ".actions-toolbar").find("#stepFormContinue").attr("disabled", "disabled");
                }
            }
        }
    });
});
