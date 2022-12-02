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


        updateStreetLabels: function () {

            if (this.value()) {
                this.company().elems.each(function (street, key) {
                    this.company().elems()[key].set('label', this.streetLabels[key]);
                }.bind(this));
            } else {
                this.company().elems.each(function (street, key) {
                    this.company().elems()[key].set('label', '');
                }.bind(this));
            }
        },


        updateCompany: function () {
            if (this.value()) {
                this.company().disabled(true);
            } else {
                this.company().disabled(false);
            }
        },


        updateDni: function () {
            if (this.value()) {
                this.dni().disabled(true);
            } else {
                this.dni().disabled(false);
            }
        },


        onCheckedChanged: function () {
            this._super();
            this.updateStreetLabels();
            this.updateCompany();
            this.updateDni();
        }

    });

});
