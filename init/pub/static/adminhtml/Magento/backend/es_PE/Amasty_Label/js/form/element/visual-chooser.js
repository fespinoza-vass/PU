define([
    'Amasty_Label/js/form/element/position-chooser',
    'ko'
], function (PositionChooser, ko) {
    'use strict';

    return PositionChooser.extend({
        defaults: {
            optionTitles: []
        },

        /**
         * Creates matrix with cells state models
         */
        initCellsMap: function () {
            var chooserRow = [];

            this.optionTitles.forEach(function (label, i) {
                chooserRow.push({
                    enabled: ko.observable(1),
                    value: i,
                    label: ko.observable(label)
                });
            });

            this.cellsMap.push(chooserRow);
            this.disabledPositions.forEach(this.disablePosition.bind(this));
        }
    });
});
