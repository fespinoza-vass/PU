function _inheritsLoose(subClass, superClass) {
    subClass.prototype = Object.create(superClass.prototype);
    subClass.prototype.constructor = subClass;
    subClass.__proto__ = superClass;
}

define([
    "jquery",
    "knockout",
    "mage/translate",
    "Magento_PageBuilder/js/widget-initializer",
    "Magento_PageBuilder/js/config",
    "Magento_PageBuilder/js/content-type-menu/hide-show-option",
    "Magento_PageBuilder/js/utils/object",
    "Magento_PageBuilder/js/content-type/preview"
], function (_jquery, _knockout, _translate, _widgetInitializer, _config, _hideShowOption, _object, _preview) {

    var Preview =
        function (_preview2) {
            "use strict";

            _inheritsLoose(Preview, _preview2);

            function Preview(parent, config, observableUpdater) {
                var self;

                self = _preview2.call(this, parent, config, observableUpdater) || this;
                self.displayingWidgetPreview = _knockout.observable(false);
                self.displayingWidgetPreview(true);
                var data = this.contentType.dataStore.getState(); // Only load if something changed
                data.html = true;

                self.loading = _knockout.observable(false);
                self.messages = {
                    NOT_SELECTED: (0, _translate)("Empty Child Categories"),
                    UNKNOWN_ERROR: (0, _translate)("An unknown error occurred. Please try again.")
                };
                self.placeholderText = _knockout.observable(self.messages.NOT_SELECTED);
                this.processWidgetData(data);
                return self;
            }

            var _proto = Preview.prototype;

            _proto.retrieveOptions = function retrieveOptions() {
                var options = _preview2.prototype.retrieveOptions.call(this);

                options.hideShow = new _hideShowOption({
                    preview: this,
                    icon: _hideShowOption.showIcon,
                    title: _hideShowOption.showText,
                    action: this.onOptionVisibilityToggle,
                    classes: ["hide-show-content-type"],
                    sort: 40
                });
                return options;
            };

            _proto.processWidgetData = function processWidgetData(data) {
                this.displayPreviewPlaceholder(data);

                if (data.html || data.template) {
                    this.processRequest(data);
                }
            };

            _proto.afterObservablesUpdated = function afterObservablesUpdated() {
                _preview2.prototype.afterObservablesUpdated.call(this);

                var data = this.contentType.dataStore.getState(); // Only load if something changed

                this.processWidgetData(data);
            };

            _proto.displayPreviewPlaceholder = function displayPreviewPlaceholder(data, identifierName) {
                this.showBlockPreview(false);
            };

            _proto.processRequest = function processRequest(data) {
                var self = this,
                    url = _config.getConfig("preview_url"),
                    identifier = (0, _object.get)(data, "reviews_count"),
                    reg = require('uiRegistry'),
                    field = reg.get('category_form.category_form.general.path'),
                    categoryPath = field.value(),
                    requestConfig = {
                        method: "POST",
                        data: {
                            role: this.config.name,
                            category_id: categoryPath.split('/').slice(-1)[0]
                        }
                    };

                this.loading(true);

                _jquery.ajax(url, requestConfig)
                    .done(function (response) {
                        if (!response.data.content) {
                            self.showBlockPreview(false);
                            self.placeholderText(self.messages.UNKNOWN_ERROR);

                            return;
                        }

                        self.displayLabel(self.config.label);

                        if (response.data.content) {
                            self.showBlockPreview(true);

                            self.data.main.html(response.data.content);
                        }
                        self.lastWidget = identifier;
                        self.lastRenderedHtml = response.data.content;
                    }).fail(function () {
                    self.showBlockPreview(false);

                    self.placeholderText(self.messages.UNKNOWN_ERROR);
                }).always(function () {
                    self.loading(false);
                });
            };

            _proto.showBlockPreview = function showBlockPreview(isShow) {
                this.displayingWidgetPreview(isShow);
            };

            return Preview;
        }(_preview);

    return Preview;
});
