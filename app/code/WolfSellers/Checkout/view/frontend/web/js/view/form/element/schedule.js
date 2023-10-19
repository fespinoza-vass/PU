define([
    'ko',
    'Magento_Ui/js/form/element/select'
], function (
    ko,
    Select
) {
    'use strict';
    return Select.extend({
        options: [],
        value: ko.observable(),
        scheduleDates: ko.observable(""),
        initialize: function () {
            this._super();
            var ahora = new Date();
            this.options = this.getAvailableDates(ahora);
        },
        /**
         * get timeSensitive by option radio with his own data
         * @param radioOption
         * @param data
         * @returns {*|string}
         */
        setValueFromTimer: function (radioOption, data) {
            var ahora = new Date();
            var timeSensitive = this.getAvailableDates(ahora);
            this.value(timeSensitive[radioOption]['value']);
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
            var timeSensitive = this.getAvailableDates(ahora);
            var fechaEntrega = this.formatDate(ahora);
            var diaEntrega = timeSensitive[radioOptionSelected].dia;
            var horarioEntrega = timeSensitive[radioOptionSelected].label;
            this.scheduleDates("<p>Tu pedido llegará " + diaEntrega + " <span> "+ fechaEntrega + " </span> en un rango de " + horarioEntrega + "</p>");
        },
        /**
         * Get available Dates with the actual date
         * Taste this function setting the var ahora like:
         *      ahora = new Date('2023-10-18T14:00:00');
         * @param ahora
         * @returns {[{label: string, value: string, dia: string}]|[{label: string, value: string, dia: string},{label: string, value: string, dia: string}]}
         */
        getAvailableDates: function (ahora) {
            var hora = ahora.getHours();
            var finRango1 = new Date(ahora.getFullYear(), ahora.getMonth(), ahora.getDate(), 16, 0);
            var minutosRestantesRango1 = Math.ceil((finRango1 - ahora) / (1000 * 60));
            var finRango2 = new Date(ahora.getFullYear(), ahora.getMonth(), ahora.getDate(), 20, 0);
            var minutosRestantesRango2 = Math.ceil((finRango2 - ahora) / (1000 * 60));
            var manana = "mañana";
            var hoy = "hoy";
            if (hora >= 0 && hora < 16) {
                if(minutosRestantesRango1 > 60){
                    return [
                        { value: '12_4_hoy',"label": "12 a 4pm", "dia": hoy },
                        { value: '4_8_hoy',"label": "4 a 8pm", "dia": hoy }
                    ];
                }
                if (minutosRestantesRango1 <= 60) {
                    return [
                        { value: '12_4_manana',"label": "12 a 4pm", "dia": manana },
                        { value: '4_8_hoy',"label": "4 a 8pm", "dia": hoy }
                    ];
                }
            }else if (hora >= 16 && hora < 20) {
                if(minutosRestantesRango2 > 60){
                    return [
                        { value: '12_4_manana',"label": "12 a 4pm", "dia": manana },
                        { value: '4_8_hoy',"label": "4 a 8pm", "dia": hoy }
                    ];
                }
                if (minutosRestantesRango2 <= 60) {
                    return [
                        { value: '12_4_manana',"label": "12 a 4pm", "dia": manana },
                        { value: '4_8__manana',"label": "4 a 8pm", "dia": manana }
                    ];
                }
            }else if (hora >= 20 && hora <=23 ){
                return [
                    { value: '12_4_manana',"label": "12 a 4pm", "dia": manana },
                    { value: '4_8__manana',"label": "4 a 8pm", "dia": manana }
                ];
            }else {
                return [
                    {value: 'noSet',"label": "no disponible", "dia": "no disponible"}
                ]
            }
        },
        /**
         * get format date by a date
         * @param date
         * @returns {string}
         */
        formatDate: function(date) {
            var daysOfWeek = ["domingo", "lunes", "martes", "miércoles", "jueves", "viernes", "sábado"];
            var months = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"];
            var dayOfWeek = daysOfWeek[date.getDay()];
            var dayOfMonth = date.getDate();
            var month = months[date.getMonth()]
            return dayOfWeek + dayOfMonth + " de " + month;
        }
    });
})
