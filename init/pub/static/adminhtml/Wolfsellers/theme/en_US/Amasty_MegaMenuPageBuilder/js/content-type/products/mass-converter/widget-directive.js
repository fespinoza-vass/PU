/*eslint-disable */

function _inheritsLoose(subClass, superClass) { subClass.prototype = Object.create(superClass.prototype); subClass.prototype.constructor = subClass; subClass.__proto__ = superClass; }

define(["Magento_PageBuilder/js/mass-converter/widget-directive-abstract", "Magento_PageBuilder/js/utils/object"], function (_widgetDirectiveAbstract, _object) {
  /**
   * Copyright © Magento, Inc. All rights reserved.
   * See COPYING.txt for license details.
   */

  /**
   * @api
   */
  var WidgetDirective =
  /*#__PURE__*/
  function (_widgetDirectiveAbstr) {
    "use strict";

    _inheritsLoose(WidgetDirective, _widgetDirectiveAbstr);

    function WidgetDirective() {
      return _widgetDirectiveAbstr.apply(this, arguments) || this;
    }

    var _proto = WidgetDirective.prototype;

    /**
     * Convert value to internal format
     *
     * @param {object} data
     * @param {object} config
     * @returns {object}
     */
    _proto.fromDom = function fromDom(data, config) {
      var attributes = _widgetDirectiveAbstr.prototype.fromDom.call(this, data, config);

      data.conditions_encoded = this.decodeWysiwygCharacters(attributes.conditions_encoded || "");
      data.products_count = attributes.products_count;
      data.title = attributes.title;
      data.block_layout = attributes.block_layout;
      data.slider_items_show = attributes.slider_items_show;
      data.slider_width = attributes.slider_width;
      data.slider_autoplay = attributes.slider_autoplay;
      data.slider_autoplay_speed = attributes.slider_autoplay_speed
      data.display_options = attributes.display_options;
      data.show_pager = attributes.show_pager;
      data.products_per_page = attributes.products_per_page;

      return data;
    }
    /**
     * Convert value to knockout format
     *
     * @param {object} data
     * @param {object} config
     * @returns {object}
     */
    ;

    _proto.toDom = function toDom(data, config) {
      var attributes = {
        type: "Amasty\\MegaMenu\\Block\\Product\\ProductsSlider",
        template: "Amasty_MegaMenuPageBuilder::product/widget/content/grid.phtml",
        anchor_text: "",
        id_path: "",
        products_count: data.products_count,
        title: data.title,
        block_layout: data.block_layout,
        slider_items_show: data.slider_items_show,
        slider_width: data.slider_width,
        slider_autoplay: data.slider_autoplay,
        slider_autoplay_speed: data.slider_autoplay_speed,
        display_options: data.display_options,
        show_pager: data.show_pager,
        products_per_page: data.products_per_page,
        page_var_name: 'psp',
        type_name: "Product Slider",
        conditions_encoded: this.encodeWysiwygCharacters(data.conditions_encoded || "")
      };

      if (attributes.conditions_encoded.length === 0) {
        return data;
      }

      (0, _object.set)(data, config.html_variable, this.buildDirective(attributes));
      return data;
    }
    /**
     * @param {string} content
     * @returns {string}
     */
    ;

    _proto.encodeWysiwygCharacters = function encodeWysiwygCharacters(content) {
      return content.replace(/\{/g, "^[").replace(/\}/g, "^]").replace(/"/g, "`").replace(/\\/g, "|").replace(/</g, "&lt;").replace(/>/g, "&gt;");
    }
    /**
     * @param {string} content
     * @returns {string}
     */
    ;

    _proto.decodeWysiwygCharacters = function decodeWysiwygCharacters(content) {
      return content.replace(/\^\[/g, "{").replace(/\^\]/g, "}").replace(/`/g, "\"").replace(/\|/g, "\\").replace(/&lt;/g, "<").replace(/&gt;/g, ">");
    };

    return WidgetDirective;
  }(_widgetDirectiveAbstract);

  return WidgetDirective;
});
//# sourceMappingURL=widget-directive.js.map