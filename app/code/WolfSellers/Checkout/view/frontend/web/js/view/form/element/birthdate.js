/**
 * Birthdate field.
 */
define([
    'Magento_Ui/js/form/element/abstract',
    'moment'
], function (Element, moment) {
    'use strict';

    return Element.extend({
        pickerDefaultDateFormat: 'MM/DD/YYYY',
        pickerViewDateFormat: 'DD/MM/YYYY',

        defaults: {
            listens: {
                'valueDate': 'onValueDateChange'
            },
            valueDate: ''
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            return this._super().observe(['valueDate']);
        },

        /**
         * Format date.
         *
         * @param {String} valueDate
         */
        onValueDateChange: function (valueDate) {
            var momentValue,
                value
            ;

            if (!valueDate) {
                return;
            }

            momentValue = moment(valueDate, this.pickerViewDateFormat);

            if (!momentValue.isValid()) {
                return;
            }

            value = momentValue.format(this.pickerDefaultDateFormat);

            if (value !== this.value()) {
                this.value(value);
            }
        }
    });
});
