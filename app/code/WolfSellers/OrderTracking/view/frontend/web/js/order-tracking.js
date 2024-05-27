/**
 * Order tracking.
 */
define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('wolfsellers.orderTracking', {
        form: null,
        result: null,

        options: {
            formSelector: '#order-tracking-form',
            resultSelector: '.tracking-result'
        },

        /**
         * @private
         */
        _create: function () {
            this.form = this.element.find(this.options.formSelector);
            this.result = this.element.find(this.options.resultSelector);

            this._bind();
        },

        _bind: function () {
            var self = this;

            this.form.on('submit', function (e) {
                e.preventDefault();
                self.submitForm();
            });
        },

        submitForm: function () {
            this.result.empty().hide();
            this.form.validation();

            if (!this.form.validation('isValid')) {
                return false;
            }

            this.getTrackingInfo();
        },

        getTrackingInfo: function () {
            var self = this;

            $('body').trigger('processStart');

            $.ajax({
                type: 'POST',
                url: this.form.attr('action'),
                dataType: 'json',
                data: this.form.serialize(),
                global: false,
                cache: false
            }).done(function (response) {
                self.updateResult(response);
            }).always(function () {
                $('body').trigger('processStop');
            });
        },

        updateResult: function (response) {
            if (!response.success) {
                this.result
                    .html(response.message)
                    .addClass('error')
                    .show()
                ;

                return;
            }

            this.result
                .html(response.html)
                .removeClass('error')
                .show()
            ;
        }
    });

    return $.wolfsellers.orderTracking;
});
