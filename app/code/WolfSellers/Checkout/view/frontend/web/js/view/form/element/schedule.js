define([
    'ko',
    'Magento_Ui/js/form/element/select',
    'WolfSellers_Checkout/js/model/shipping-payment',
    'WolfSellers_Checkout/js/utils-wolf-uicomponents'
], function (
    ko,
    Select,
    shippingPayment,
    wolfUtils
) {
    'use strict';
    return Select.extend({
        options: [],
        value: ko.observable(),
        scheduleDates: ko.observable(""),
        updateOptions: ko.observable(),
        optionSelected: ko.observable(),

        initialize: function () {
            this._super();
            var ahora = new Date();
            this.options = wolfUtils.getAvailableDates(ahora);
            this.updateOptions.subscribe(function (value) {
                for (let i = 0; i < this.options.length; i++) {
                    this.options[i].value = value[i].option_value;
                }
            },this);
            this.value.subscribe(function (value) {
                if(value){
                    this.optionSelected(this.setValueFromTimer(value, 'value'));
                }
            },this);
        },
        /**
         * get timeSensitive by option radio with his own data
         * @param radioOption
         * @param data
         * @returns {*|string}
         */
        setValueFromTimer: function (radioOption, data) {
            var ahora = new Date();
            var timeSensitive = wolfUtils.getAvailableDates(ahora);
            if(data.includes('lab')){
                return "Horario de " +
                    timeSensitive[radioOption].label +
                    ' de ' +
                    timeSensitive[radioOption].dia;
            }
            this.getHorarioDisponibles(radioOption);
            return timeSensitive[radioOption][data];
        },
        /**
         * Get html label for time sensitive with the actual date
         * @param radioOptionSelected
         */
        getHorarioDisponibles: function (radioOptionSelected) {
            var ahora = new Date();
            var timeSensitive = wolfUtils.getAvailableDates(ahora);
            var fechaEntrega = wolfUtils.formatDate(ahora);
            var diaEntrega = timeSensitive[radioOptionSelected].dia;
            var horarioEntrega = timeSensitive[radioOptionSelected].label;
            shippingPayment.fechaEnvioRapido({
                dia: diaEntrega,
                fecha:fechaEntrega,
                horario: horarioEntrega
            });
            this.scheduleDates("<p>Tu pedido llegará " + diaEntrega + " <span> "+ fechaEntrega + " </span> en un rango de " + horarioEntrega + "</p>");
        }
    });
})
