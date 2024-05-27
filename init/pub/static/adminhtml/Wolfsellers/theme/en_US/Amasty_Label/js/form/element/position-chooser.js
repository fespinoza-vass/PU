define([
    'Magento_Ui/js/form/element/abstract',
    'ko'
], function (Abstract, ko) {
    'use strict';

    return Abstract.extend({
        defaults: {
            elementTmpl: 'Amasty_Label/form/element/position-chooser',

            /**
             * Certain items may be prohibited from selection.
             * To do this, you need to transfer them in Cartesian form, where the highest left cell is 0;0
             *
             * @example
             * disabledPositions = ['1;1', '0;1']
             */
            disabledPositions: [],

            /**
             * The dimension of the square. For example 3 means chooser 3x3
             */
            dimensionSize: 3,
            visible: true,
            additionalClasses: {'amlabel-position-chooser': true},
            cellsMap: []
        },

        /**
         * @inheritDoc
         * @return {object}
         */
        initialize: function () {
            this._super();
            this.initCellsMap();

            return this;
        },

        /**
         * Creates matrix with cells state models
         */
        initCellsMap: function () {
            // eslint-disable-next-line vars-on-top
            for (var i = 0; i < this.dimensionSize; ++i) {
                // eslint-disable-next-line
                var verticalDimension = [];

                this.cellsMap.push(verticalDimension);

                // eslint-disable-next-line
                for (var j = 0; j < this.dimensionSize; ++j) {
                    verticalDimension.push({
                        enabled: ko.observable(1),
                        value: i * this.dimensionSize + j
                    });
                }
            }

            this.disabledPositions.forEach(this.disablePosition.bind(this));
        },

        /**
         * @param {string} position
         */
        disablePosition: function (position) {
            var xyCoords;

            if (/\d+;\d+/.test(position)) {
                xyCoords = position.split(';');

                if (this.cellsMap[xyCoords[0]] && this.cellsMap[xyCoords[0]][xyCoords[1]]) {
                    this.cellsMap[xyCoords[0]][xyCoords[1]].enabled(0);
                }
            }
        },

        isCellEnabled: function (data) {
          return this.value() === data.value ? 1 : 0;
        },

        /**
         * @param {object} data
         */
        selectPositionProcess: function (data) {
            if (data.enabled()) {
                if (this.value() === data.value) {
                    this.value(null);
                } else {
                    this.value(data.value);
                }
            }
        },

        /**
         * @param {any} value
         * @return {object}
         */
        normalizeData: function (value) {
            value = value === null ? null : +value;

            return this._super(value);
        }
    });
});
