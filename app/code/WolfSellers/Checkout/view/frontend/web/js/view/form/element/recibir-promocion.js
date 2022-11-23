define([
    'ko',
    'uiComponent',
    'jquery'
], function (ko, Component,$) {

    "use strict";

    return Component.extend({

        defaults: {
            template: 'WolfSellers_Checkout/form/element/recibir-promocion'
        },
            
        initObservable: function () {
        this._super();
        this.observe({isSubscribed: ko.observable(false)});

        self=this;

            this.isSubscribed.subscribe(function(valueCHX){
                if(valueCHX){
                    $("#receive-promotion").val(1);
                }
                else{
                    $("#receive-promotion").val(0);
                }
            });

            return this;
        }

    });
});




