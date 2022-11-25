define([
    'jquery',
    'domReady!'
], function($) {
    'use strict';

    $(function() {
        //$(".action.primary.checkout.amasty").prop("disabled","disabled");

        console.log($('button'));
    });

    $(function(){
        $(document).on('change',"input[name*='agreement']",function (event){
            var selection=event.currentTarget;
            if(selection.checked){
                $(".action.primary.checkout.amasty").removeProp("disabled");
            }else{
                $(".action.primary.checkout.amasty").prop("disabled","disabled");
            }
        })
    });













    /*
    $.widget('wolfsellers.validacionEnvioEspecial',{

        options:{
            placeOrder:".payment-method .action.primary.checkout.amasty"
        },
        _create(){

            var $placeOrder=$(this.options.placeOrder);

            console.log($placeOrder);

            console.log($("input[name='agreement[1]']"));


            





            console.log("**************************");

            
            /*
            var $inputRadio=$(this.options.radioFacturar);
            if(this.options.status == 1){

                $inputRadio.each(function(indice, elemento) {
                    $(elemento).removeProp('checked');
                })
                $inputRadio.prop("disabled","disabled");
            }
            else{
                $inputRadio.removeProp("disabled");
            }
            
        }
    });
    return $.wolfsellers.validacionEnvioEspecial;


        $(document).ready(function(){
        var $placeOrder=".payment-method .action.primary.checkout";

        console.log($($placeOrder));
        console.log("##");


    });
    */
});