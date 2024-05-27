define([
    'jquery',
    'jquery/ui'
], function (
    $
) {
   'use strict';
   var initLabelWidgetMixin = {
       /**
        * Exec Amasty Label widget
        * @public
        * @returns {void}
        */
       execLabel: function () {
           if (this._isFromAmastySearch()) {
               return; // No renderizamos el label si viene de Amasty_Xsearch
           }
           this.element.amShowLabel(this.options.config);
       },

       /**
        * Verificar si el elemento está dentro de un bloque de Amasty_Xsearch
        * @private
        * @returns {Boolean}
        */
       _isFromAmastySearch: function () {
           return this.element.closest('.amsearch-products-section').length > 0;
       }
   };

    return function (initLabelWidget) {
        $.widget('mage.amInitLabel', initLabelWidget, initLabelWidgetMixin);
        return $.mage.amInitLabel;
    };

});
