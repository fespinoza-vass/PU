define([
    'ko',
    'Magento_Ui/js/form/element/select'
], function (
    ko,
    Select
) {
    'use strict';

    return Select.extend({
       initialize:function () {
           this._super();
           this.value.subscribe(function (value) {
               console.log("Actualizar lista de distritos");
           });
       }
    });
})
