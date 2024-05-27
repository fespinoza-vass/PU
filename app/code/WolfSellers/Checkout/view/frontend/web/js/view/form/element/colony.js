/**
 * @api
 */
define([
    'underscore',
    'Magento_Ui/js/form/element/select',
    'WolfSellers_Checkout/js/model/address/ubigeo',
    'uiRegistry',
    'jquery'
], function (_, Select, ubigeo, registry, $) {
    'use strict';

    return Select.extend({
        distritosUno: [
            "Lima",
            "Ate",
            "Barranco",
            "Breña",
            "Carabaylio",
            "Chorrillos",
            "Comas",
            "El Augustino",
            "Independencia",
            "Jesus María",
            "La Molina",
            "La Victoria",
            "Lince",
            "Los Olivos",
            "Magdalena del Mar",
            "Pueblo Libre",
            "Miraflores",
            "Rimac",
            "San Borja",
            "SMP",
            "San Miguel",
            "Santa Anita",
            "Santiago de Surco",
            "Surquillo",
            "VES",
            "VMT",
            "Callao"
        ],
        distritosDos: [
            "Ancón",
            "Chaclacayo",
            "Cieneguilla",
            "Lurigancho",
            "Lurín",
            "Pachacamac",
            "Pucusana",
            "Puente Piedra",
            "Punta Hermosa",
            "Punta Negra",
            "San Bartolo",
            "Santa María del Mar",
            "Santa Rosa"
        ],

        defaults: {
            skipValidation: false
        },

        /**
         * @returns {*}
         */
        initialize: function () {
            this._super();

            var self = this;

            ubigeo.listUbigeo.subscribe(function (listUbigeo) {
                self.setOptions(listUbigeo);
            });
            this.onUpdate();
            this.options.subscribe(function (value) {
                value = _.map(value, function (item) {
                    if(!_.isUndefined(item)){
                        if(item.label.length >= 3){
                            item.label = item.label.charAt(0).toUpperCase() + item.label.slice(1).toLowerCase();
                        }
                        return item;
                    }
                    return item;
                });
            }, this);
            return this;
        },

        /**
         * @param value
         *
         * @returns {*}
         */
        onUpdate: function (value) {
            var ubigeo = ubigeo;
            var optSelected;
            if(_.isUndefined(ubigeo) && _.isEmpty(value)){
                return;
            }
            if (typeof this.getOption !== 'function') {
                return;
            }

            if (value && (optSelected = this.getOption(value))) {
                ubigeo = optSelected.postcode;
            }

            registry.get(this.parentName + '.' + 'postcode', function (postcodeField) {
                postcodeField.value(ubigeo);
            }.bind(this));

            this._showShippingTimeLabel(value);

            return this._super();
        },

        _showShippingTimeLabel: function (distrito) {
            var distritoNumber = this._getDistritoNumber(distrito);
            $(".shipping-time-label-uno").hide();
            $(".shipping-time-label-dos").hide();
            $(".shipping-time-label-tres").hide();
            switch (distritoNumber) {
                case 1: {
                    $(".shipping-time-label-uno").show();
                    break;
                }
                case 2: {
                    $(".shipping-time-label-dos").show();
                    break;
                }
                default: {
                    $(".shipping-time-label-tres").show();
                    break;
                }
            }
        },

        _getDistritoNumber: function(distrito) {
            var i;
            for(i = 0; i < this.distritosUno.length; i++) {
                if (distrito.toUpperCase() === this.distritosUno[i].toUpperCase()) {
                    return 1;
                }
            }
            for(i = 0; i < this.distritosDos.length; i++) {
                if (distrito.toUpperCase() === this.distritosDos[i].toUpperCase()) {
                    return 2;
                }
            }
            return 3;
        },
    });
});

