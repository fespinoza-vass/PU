define([
    "jquery",
    'Magento_Ui/js/form/element/single-checkbox',
    'mage/translate'
], function ($, AbstractField, $t) {
    'use strict';

    return AbstractField.extend({
        defaults: {
            streetLabels: [$t('Empresa'), $t('RUC')],
            modules: {
                company: '${ $.parentName }.company',
                dni: '${ $.parentName }.dni',
            },
            valueMap: {
                'true': true,
                'false': false
            }
        },

        updateDniCompany: function (flag) {
            if (flag) {
                this.company().visible(true);
                this.dni().visible(true);
            } else {
                this.company().visible(false);
                this.dni().visible(false);
            }
        },

        onCheckedChanged: function () {
            this._super();

            this.updateDniCompany(this.value());
        }
    });
});
