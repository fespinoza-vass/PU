define([
    'Magento_Ui/js/form/element/image-uploader',
    'underscore'
], function (ImageUploader, _) {
    'use strict';

    return ImageUploader.extend({
        defaults: {
            initialFileId: null
        },

        /**
         * @inheritDoc
         * @return {object}
         */
        initialize: function () {
            var result = this._super(),
                originalValue = this.value();

            if (_.isArray(originalValue) && originalValue.length) {
                this.initialFileId = originalValue[0].id;
            }

            return result;
        },

        onUpdate: function () {
            var currentValue = this.value(),
                currentFileId = _.isArray(currentValue) && currentValue.length ? currentValue[0].id : null;

            if (currentFileId !== null) {
                this.observe.call(currentValue[0], true, [
                    'previewWidth',
                    'previewHeight'
                ]);
            }

            if (!_.isEqual(currentFileId, this.initialFileId)) {
                this.bubble('update', this.hasChanged());
            }

            this.validate();
        }
    });
});
