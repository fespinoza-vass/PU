define([
    'Magento_Ui/js/form/element/single-checkbox',
    'mage/translate'
], function (AbstractField, $t) {
    'use strict';

    return AbstractField.extend({
        defaults: {
            streetLabels: [$t('Empresa'), $t('RUC')],
            modules: {
                company: '${ $.parentName }.company',
                dni: '${ $.parentName }.dni',
            }
        },

        updateCompany: function () {
            var value= this.value() === "" ? 1 : 0;

            if (value == 0) {
                console.log("ENTRE A FALSE");
                this.company().visible(false);
            }

            if(value == 1) {
                console.log("ENTRE A TRUE")
                this.company().visible(true);
            }
        },

        updateDni: function () {
            var value= this.value() === "" ? 1 : 0;

            if (value == 0) {
                this.dni().visible(false);
            }
            if(value == 1)
            {
                this.dni().visible(true);
            }
        },

        onCheckedChanged: function () {
            this._super();
            this.updateCompany();
            this.updateDni();
        }
    });
});
