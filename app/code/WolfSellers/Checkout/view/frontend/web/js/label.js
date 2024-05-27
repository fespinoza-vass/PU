define([
    'jquery'
], function ($) {
    return function (widget) {
        $.widget('mage.amShowLabel', widget, {
            _refreshSLickSlider: function () {
                //nothing to do
            }
        });
        return $.mage.amShowLabel;
    };
});
