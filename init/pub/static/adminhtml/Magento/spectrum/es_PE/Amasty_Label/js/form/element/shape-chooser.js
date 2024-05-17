define([
    'Magento_Ui/js/form/element/abstract',
    'Amasty_Label/js/utils/svg-to-data-url-converter',
    'ko'
], function (Abstract, convertToDataUrl) {
    'use strict';

    return Abstract.extend({
        defaults: {
            elementTmpl: 'Amasty_Label/form/element/shape-chooser',

            /**
             * List of shape data.
             *
             * @example {
             *     'shape_name': {
             *         shapeType: 'rquarter',
             *         shapeContent: '<svg height="100" width="100"><circle fill="blue"></circle></svg>',
             *         shapeDescription: 'Description'
             *     }
             * }
             */
            shapeList: {},
            shapeIdPostfix: '',
            shapeIdPrefix: 'amlabel_shape_type_',
            currentShapeContent: ''
        },

        /**
         * @inheritDoc
         */
        initObservable: function () {
            this._super()
                .observe('currentShapeContent');

            return this;
        },

        /**
         * @param {string} type
         */
        generateShapeId: function (type) {
            return this.shapeIdPrefix + type + this.shapeIdPostfix;
        },

        /**
         * @param {string} svgContent,
         * @returns {string}
         */
        generateDataUrl: function (svgContent) {
            return convertToDataUrl(svgContent);
        },

        /**
         * @param {object} data
         * @return {boolean}
         */
        isShapeSelected: function (data) {
            return this.value() === data.shapeType;
        },

        /**
         *
         * @param {object} data
         */
        chooseShape: function (data) {
            this.currentShapeContent(data.shapeContent);
            this.value(data.shapeType);
        }
    });
});
