define([
    'uiComponent',
    'domReady!'
    ],
    function(Component) {
        'use strict';
        return Component.extend({
            onRenderComplete: function () {
                console.log('onRenderComplete');
            },
            openBenefits: function () {
                console.log('openBenefits');
            }
        });
    }
);
