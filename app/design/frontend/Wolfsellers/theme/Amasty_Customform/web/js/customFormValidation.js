define([
    "jquery",
    'Magento_Ui/js/modal/alert'
], function($, alert){
    "use strict";
    return function custom(element) {
        if (element.val() === '' || element.val() === 'undefined'){
            return false;
        }

        const adulto = 18;
        var birthday = new Date(element.val()).getTime();
        var today = new Date().getTime();

        var diff = today - birthday;
        var edad = Math.round(Math.round(diff/(1000 * 60 * 60 * 24)) / 360);

        console.log(edad);

        if (edad<=0){
            alert({
                title: $.mage.__('La fecha de nacimiento seleccionada no es válida'),
                content: $.mage.__('La fecha de nacimiento seleccionada no es válida.'),
                actions: {
                    always: function(){}
                }
            });

            element.val('');
            $('#dropdown-apoderado-obligatorio').val('Si').trigger('change');
            return false;
        }

        if ( edad < adulto ){
            $('#dropdown-apoderado-obligatorio').val('Si').trigger('change');
            return true;
        }

        if (edad >= adulto){
            $('#dropdown-apoderado-obligatorio').val('No').trigger('change');
            return true;
        }

    }
});