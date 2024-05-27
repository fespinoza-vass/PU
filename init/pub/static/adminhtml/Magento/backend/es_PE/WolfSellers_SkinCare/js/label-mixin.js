define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    var labelWidgetMixin = {
        setLabelPosition: function () {
            var className = 'amlabel-position-' + this.options.position
                    + '-' + this.options.product+ '-' + this.options.mode + this.getWidgetLabelCode(),
                wrapper = this.parent.find('.' + className);

            if (wrapper.length) {
                var labelOrderMatch = false;

                $.each(wrapper.find('.amasty-label-container'), function (index, prevLabel) {
                    var nextLabel = $(prevLabel).next(),
                        currentOrder = parseInt(this.options.order);
                    if (typeof $(prevLabel).data('mageAmShowLabel') !== "undefined"){
                        var prevOrder = parseInt($(prevLabel).data('mageAmShowLabel').options.order),
                            nextOrder = null;

                        if (nextLabel.length) {
                            nextOrder = parseInt(nextLabel.data('mageAmShowLabel').options.order);
                        }

                        if (currentOrder >= prevOrder && (!nextOrder || currentOrder <= nextOrder)) {
                            labelOrderMatch = true;
                            $(prevLabel).after(this.element);
                            return false;
                        }
                    }
                }.bind(this));

                if (!labelOrderMatch) {
                    wrapper.prepend(this.element);
                }
            } else {
                var parent = this.element.parent();
                if (parent.hasClass(this.positionWrapper)) {
                    parent.parent().append(this.element);
                }

                this.element.wrap('<div class="' + className + ' ' + this.positionWrapper + '"></div>');
                wrapper = this.element.parent();
            }

            if (this.options.alignment === 1) {
                wrapper.children(':not(:first-child)').each(function (index, element) {
                    this.setStyleIfNotExist(
                        $(element),
                        {
                            'marginLeft': this.options.margin + 'px'
                        }
                    );
                }.bind(this));

            } else {
                wrapper.children(':not(:first-child)').each(function (index, element) {
                    this.setStyleIfNotExist(
                        $(element),
                        {
                            'marginTop': this.options.margin + 'px'
                        }
                    );
                }.bind(this));
            }

            //clear styles before changing
            wrapper.css({
                'top'  : "",
                'left' : "",
                'right' : "",
                'bottom' : "",
                'margin-top' : "",
                'margin-bottom' : "",
                'margin-left' : "",
                'margin-right' : ""
            });

            switch (this.options.position) {
                case 'top-left':
                    wrapper.css({
                        'top'  : 0,
                        'left' : 0
                    });
                    break;
                case 'top-center':
                    wrapper.css({
                        'top': 0,
                        'left': 0,
                        'right': 0,
                        'margin-left': 'auto',
                        'margin-right': 'auto'
                    });
                    break;
                case 'top-right':
                    wrapper.css({
                        'top'   : 0,
                        'right' : 0,
                        'text-align' : 'right'
                    });
                    break;

                case 'middle-left':
                    wrapper.css({
                        'left' : 0,
                        'top'   : 0,
                        'bottom'  : 0,
                        'margin-top': 'auto',
                        'margin-bottom': 'auto'
                    });
                    break;
                case 'middle-center':
                    wrapper.css({
                        'top'   : 0,
                        'bottom'  : 0,
                        'margin-top': 'auto',
                        'margin-bottom': 'auto',
                        'left': 0,
                        'right': 0,
                        'margin-left': 'auto',
                        'margin-right': 'auto'
                    });
                    break;
                case 'middle-right':
                    wrapper.css({
                        'top'   : 0,
                        'bottom'  : 0,
                        'margin-top': 'auto',
                        'margin-bottom': 'auto',
                        'right' : 0,
                        'text-align' : 'right'
                    });
                    break;

                case 'bottom-left':
                    wrapper.css({
                        'bottom'  : 0,
                        'left'    : 0
                    });
                    break;
                case 'bottom-center':
                    wrapper.css({
                        'bottom': 0,
                        'left': 0,
                        'right': 0,
                        'margin-left': 'auto',
                        'margin-right': 'auto'
                    });
                    break;
                case 'bottom-right':
                    wrapper.css({
                        'bottom'   : 0,
                        'right'    : 0,
                        'text-align' : 'right'
                    });
                    break;
            }
        },
    };
    return function (labelWidget) {
        $.widget('mage.amShowLabel', labelWidget, labelWidgetMixin);
        return $.mage.amShowLabel;
    };
});
