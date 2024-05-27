define([
    'underscore',
    'uiRegistry'
], function (
    _,
    registry
) {
    'use strict';
    /**
     * TODO all string passed as parameters turn into vars or observables
     */
    return {
        /**
         * get uiComponents array by path + [uiComponent names]
         * @param path
         * @param uiComponentsRequired
         * @returns {*}
         */
        getUiComponentsArray: function (path, uiComponentsRequired) {
            return _.chain(uiComponentsRequired)
                .map(function(componentName) {
                    var component = registry.get(path + componentName);
                    return component ? [componentName, component] : null;
                })
                .compact()
                .object()
                .value();
        },
        /**
         * set new validation to uiComponent by path
         * @param path
         * @param uiComponents
         * @param validationConfig
         * @returns {*}
         */
        setUiComponentsArrayValidation: function (path,uiComponents, validationConfig) {
            return _.chain(uiComponents)
                .map(function(componentName) {
                    var component = registry.get(path + componentName);
                    if (component){
                        component.validation = Object.assign({}, component.validation, validationConfig);
                    }
                    return true;
                })
                .compact()
                .object()
                .value();
        },
        /**
         *
         * @param ahora
         * @returns {[{label: string, value: string, dia: string}]|[{label: string, value: string, dia: string},{label: string, value: string, dia: string}]}
         */
        getAvailableDatesWithRange: function (ahora) {
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
                        { value: '12_4_hoy',"label": "12 a 4 pm", "dia": hoy },
                        { value: '4_8_hoy',"label": "4 a 8 pm", "dia": hoy }
                    ];
                }
                if (minutosRestantesRango1 <= 60) {
                    return [
                        { value: '12_4_manana',"label": "12 a 4 pm", "dia": manana },
                        { value: '4_8_hoy',"label": "4 a 8 pm", "dia": hoy }
                    ];
                }
            }else if (hora >= 16 && hora < 20) {
                if(minutosRestantesRango2 > 60){
                    return [
                        { value: '12_4_manana',"label": "12 a 4 pm", "dia": manana },
                        { value: '4_8_hoy',"label": "4 a 8 pm", "dia": hoy }
                    ];
                }
                if (minutosRestantesRango2 <= 60) {
                    return [
                        { value: '12_4_manana',"label": "12 a 4 pm", "dia": manana },
                        { value: '4_8_manana',"label": "4 a 8 pm", "dia": manana }
                    ];
                }
            }else if (hora >= 20 && hora <=23 ){
                return [
                    { value: '12_4_manana',"label": "12 a 4 pm", "dia": manana },
                    { value: '4_8_manana',"label": "4 a 8 pm", "dia": manana }
                ];
            }else {
                return [
                    {value: 'noSet',"label": "no disponible", "dia": "no disponible"}
                ]
            }
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
            if (hora >= 11 && hora < 15) {

                return [
                    { value: '4_8_hoy',"label": "4 a 8pm", "dia": hoy }
                ];

            }else if (hora >= 0 && hora < 11) {

                return [
                    { value: '12_4_hoy',"label": "12 a 4 pm", "dia": hoy },
                    { value: '4_8_hoy',"label": "4 a 8 pm", "dia": hoy }
                ];

            }else if (hora >= 15 && hora <= 24 ){

                return [
                    { value: '12_4_manana',"label": "12 a 4 pm", "dia": manana },
                    { value: '4_8_manana',"label": "4 a 8 pm", "dia": manana }
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
            var months = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"];
            var dayOfWeek = this.formatDayOfTheWeek(date);
            var dayOfMonth = date.getDate();
            var month = months[date.getMonth()]
            return dayOfWeek + " " +  dayOfMonth + " de " + month;
        },
        /**
         * Set format date to day of the week
         * @param date
         * @returns {string}
         */
        formatDayOfTheWeek: function (date) {
            var daysOfWeek = ["domingo", "lunes", "martes", "miércoles", "jueves", "viernes", "sábado"];
            return daysOfWeek[date.getDay()];
        },
        /**
         * convertirHoraFormato24
         * @param date
         * @return {string}
         */
        convertirHoraFormato24: function (hora12) {
            var partesHora = hora12.split(':');
            var horas = parseInt(partesHora[0], 10);
            var minutos = parseInt(partesHora[1], 10);

            var esPM = hora12.toLowerCase().indexOf('pm') !== -1;

            if (esPM && horas < 12) {
                horas += 12;
            } else if (!esPM && horas === 12) {
                horas = 0;
            }

            var hora24 = horas.toString().padStart(2, '0') + ':' + minutos.toString().padStart(2, '0');

            return hora24;
        },
        /**
         * add day in date
         * @param fecha
         * @param dias
         * @return {string|*}
         */
        addDays:function (fecha, dias) {
            fecha.setDate(fecha.getDate() + dias);
            fecha = this.formatDate(fecha);
            return fecha;
        },
        /**
         * return hora Navegador
         * @param date
         * @return {string}
         */
        localTimes: function (date) {
            var hora = date.getHours() + " : " + date.getMinutes();
            return hora;
        },
        /**
         * return day Delivery
         * @param fecha
         * @param dias
         * @return {string}
         */
        getDaysDelivery:function (fecha, dias) {
            fecha.setDate(fecha.getDate() + dias);
            fecha = this.formatDateDay(fecha);
            return fecha;
        },
        /**
         * return format day
         * @param date
         * @return {string}
         */
        formatDateDay: function(date) {
            var dayOfWeek = this.formatDayOfTheWeek(date);
            return dayOfWeek;
        },
        /**
         * return rest day date
         * @param fecha
         * @param dias
         * @return {string}
         */
        restDays:function (fecha, dias) {
            fecha.setDate(fecha.getDate() - dias);
            fecha = this.formatDate(fecha);
            return fecha;
        }
    }
});


