define([
    'Magento_Ui/js/form/element/abstract',
    'ko',
    'Amasty_Label/js/utils/svg-to-data-url-converter',
    'Amasty_Label/js/utils/replace-svg-color',
    'underscore'
], function (Abstract, ko, convertToDataUrl, replaceSvgColor, _) {
    'use strict';

    return Abstract.extend({
        defaults: {
            elementTmpl: 'Amasty_Label/form/element/label-preview',
            mainImageSelector: '[data-am-label-js="preview-image"]',
            previewImageUrl: null,
            labelImage: null,
            previewType: '',
            labelText: null,
            labelStyle: '',
            wrapperStyle: '',
            transparentShapes: [],
            externalPreviewLinks: {
                labelType: null,
                shapeContent: null,
                shapeColor: null,
                shapeType: null,
                labelImage: null,
                position: null,
                labelSize: null,
                labelTextColor: null,
                labelTextSize: null,
                externalLabelStyle: null
            }
        },

        /**
         * @inheritDoc
         */
        initialize: function () {
          _.bindAll(
              this,
              'onLabelTypeChange',
              'replaceImageWithShape',
              'replaceImageWithPicture',
              'renderLabelStyles',
              'renderLabelWrapperStyles',
              'createOnloadListener'
          );

          this._super();
        },

        /**
         * @inheritDoc
         */
        initObservable: function () {
            this._super()
                .observe('labelImage labelStyle labelText wrapperStyle');

            // eslint-disable-next-line vars-on-top
            for (var key in this.externalPreviewLinks) {
                if (this.externalPreviewLinks.hasOwnProperty(key)) {
                    this.externalPreviewLinks[key] = ko.observable(this.externalPreviewLinks[key]);
                }
            }

            this.externalPreviewLinks.labelType.subscribe(this.onLabelTypeChange);
            this.externalPreviewLinks.shapeColor.subscribe(this.replaceImageWithShape);
            this.externalPreviewLinks.shapeContent.subscribe(this.replaceImageWithShape);
            this.externalPreviewLinks.labelImage.subscribe(this.replaceImageWithPicture);
            this.externalPreviewLinks.labelTextColor.subscribe(this.renderLabelStyles);
            this.externalPreviewLinks.labelTextSize.subscribe(this.renderLabelStyles);
            this.externalPreviewLinks.externalLabelStyle.subscribe(this.renderLabelStyles);
            this.externalPreviewLinks.position.subscribe(this.renderLabelWrapperStyles);
            this.externalPreviewLinks.labelSize.subscribe(this.renderLabelWrapperStyles);

            return this;
        },

        /**
         * @param option
         * @return {void}
         */
        renderLabelWrapperStyles: function (option) {
            var labelSize = this.getNewLabelSize(),
                position = this.externalPreviewLinks.position(),
                resultCss = '';

            if (labelSize !== null) {
                resultCss += option === 'clear' ? 'width: auto;' : 'width: ' + labelSize + 'px;';
            }

            if (position !== null) {
                resultCss += this.getPositionCss(position);
            }

            if (resultCss !== '') {
                this.wrapperStyle(resultCss);
            }
        },

        /**
         * @param {number} position
         * @return {object}
         */
        getPositionCss(position) {
            var cssValues = {},
                resultCss = '';

            switch (position) {
                case 0:
                    cssValues = {
                        'top': 0,
                        'left': 0
                    };
                    break;
                case 1:
                    cssValues = {
                        'top': 0,
                        'left': 0,
                        'right': 0,
                        'margin-left': 'auto',
                        'margin-right': 'auto'
                    };
                    break;
                case 2:
                    cssValues = {
                        'top': 0,
                        'right': 0,
                        'text-align': 'right'
                    };
                    break;
                case 3:
                    cssValues = {
                        'left': 0,
                        'top': 0,
                        'bottom': 0,
                        'margin-top': 'auto',
                        'margin-bottom': 'auto'
                    };
                    break;
                case 4:
                    cssValues = {
                        'top': 0,
                        'bottom': 0,
                        'margin-top': 'auto',
                        'margin-bottom': 'auto',
                        'left': 0,
                        'right': 0,
                        'margin-left': 'auto',
                        'margin-right': 'auto'
                    };
                    break;
                case 5:
                    cssValues = {
                        'top': 0,
                        'bottom': 0,
                        'margin-top': 'auto',
                        'margin-bottom': 'auto',
                        'right': 0,
                        'text-align': 'right'
                    };
                    break;
                case 6:
                    cssValues = {
                        'bottom': 0,
                        'left': 0
                    };
                    break;
                case 7:
                    cssValues = {
                        'bottom': 0,
                        'left': 0,
                        'right': 0,
                        'margin-left': 'auto',
                        'margin-right': 'auto'
                    };
                    break;
                case 8:
                    cssValues = {
                        'bottom': 0,
                        'right': 0,
                        'text-align': 'right'
                    };
                    break;
            }

            resultCss = Object.entries(cssValues).reduce(function (carry, currentCss) {
                    var cssSetting = currentCss[0],
                        cssSettingValue = currentCss[1];

                    // eslint-disable-next-line no-return-assign
                    return carry += cssSetting + ': ' + cssSettingValue + '; ';
                }, '');

            return resultCss;
        },

        /**
         * @return {void}
         */
        renderLabelStyles: function () {
            var labelTextColor = this.externalPreviewLinks.labelTextColor(),
                labelTextSize = this.externalPreviewLinks.labelTextSize(),
                additionalStyles = this.externalPreviewLinks.externalLabelStyle(),
                styles = '';

            if (this.isHexColor(labelTextColor)) {
                styles += 'color: ' + labelTextColor + ';';
            }

            if (labelTextSize) {
                labelTextSize = /^\d+$/gm.test(labelTextSize) ? labelTextSize + 'px' : labelTextSize;
                styles += 'font-size: ' + labelTextSize + ';';
            }

            if (additionalStyles) {
                styles += ' ' + additionalStyles;
            }

            if (styles !== '') {
                this.labelStyle(styles);
            }
        },

        /**
         * @return {string | number}
         */
        getNewLabelSize: function () {
            var newLabelSize = this.externalPreviewLinks.labelSize(),
                rawSize = /\d+/.exec(newLabelSize),
                result = null,
                size;

            if (rawSize) {
                size = parseFloat(rawSize[0]);

                result = size * this.getMainImageWidth() / 100;
            }

            return result;
        },

        /**
         * @param {string} shapeType
         * @return {boolean}
         */
        isShapeTransparent: function (shapeType) {
          return this.transparentShapes.indexOf(shapeType) !== -1;
        },

        /**
         * @param colorValue
         * @return {boolean}
         */
        isHexColor: function (colorValue) {
            return /^(#[A-Fa-f0-9]{6}|#[A-Fa-f0-9]{3})$/i.test(colorValue);
        },

        /**
         * @return {void}
         */
        replaceImageWithShape: function () {
            var shapeContent = this.externalPreviewLinks.shapeContent(),
                shapeColor = this.externalPreviewLinks.shapeColor(),
                shapeType = this.externalPreviewLinks.shapeType(),
                isTransparent = this.isShapeTransparent(shapeType);

            if (shapeContent) {
                shapeContent = this.isHexColor(shapeColor)
                    ? replaceSvgColor(shapeContent, shapeColor, isTransparent)
                    : shapeContent;

                if (shapeContent !== null) {
                    this.labelImage(convertToDataUrl(shapeContent));
                }
            }
        },

        /**
         * @return {void}
         */
        replaceImageWithPicture: function () {
            var labelImage = this.externalPreviewLinks.labelImage();

            if (labelImage && labelImage[0] && labelImage[0].url) {
                this.labelImage(labelImage[0].url);
            }
        },

        /**
         * @param labelType
         * @return {void}
         */
        onLabelTypeChange: function (labelType) {
            switch (labelType) {
                case 0:
                    this.labelImage(null);
                    this.renderLabelWrapperStyles('clear');
                    break;
                case 1:
                    this.replaceImageWithShape();
                    this.renderLabelWrapperStyles();
                    break;
                case 2:
                    this.replaceImageWithPicture();
                    this.renderLabelWrapperStyles();
                    break;
            }
        },

        /**
         * @return {HTMLElement}
         */
        getMainImage: function () {
            return document.querySelector(
                this.mainImageSelector + '[data-am-label-preview-type="' + this.previewType + '"]'
            );
        },

        /**
         * @return {number}
         */
        getMainImageWidth: function () {
            var image = this.getMainImage(),
                result = 0;

            if (image) {
                result = image.clientWidth;
            }

            return result;
        },

        /**
         * @return {void}
         */
        createOnloadListener: function () {
            this.getMainImage().onload = this.renderLabelWrapperStyles;
        }
    });
});
