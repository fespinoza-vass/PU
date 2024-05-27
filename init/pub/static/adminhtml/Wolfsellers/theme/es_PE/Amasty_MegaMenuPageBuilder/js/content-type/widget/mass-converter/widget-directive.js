define(["Magento_PageBuilder/js/mass-converter/widget-directive-abstract"], function (WidgetDirectiveAbstract) {
    var AmMenuDirective = function () {
        WidgetDirectiveAbstract.apply(this, arguments);
    };

    AmMenuDirective.prototype = Object.create(WidgetDirectiveAbstract.prototype);
    AmMenuDirective.prototype.constructor = AmMenuDirective;

    AmMenuDirective.prototype.fromDom = function (data, config) {
        var attributes = WidgetDirectiveAbstract.prototype.fromDom.call(this, data, config);
        data.title = attributes.title;
        data.reviews_count = attributes.reviews_count;
        data.higher_than = attributes.higher_than;
        data.review_type = attributes.review_type;
        data.template = attributes.template;
        data.current_category = attributes.current_category;
        data.enable_slider = attributes.enable_slider;

        return data;
    };

    AmMenuDirective.prototype.toDom = function (data, config) {
        data['html'] = "{{child_categories_content}}";

        return data;
    };

    return AmMenuDirective;
});
