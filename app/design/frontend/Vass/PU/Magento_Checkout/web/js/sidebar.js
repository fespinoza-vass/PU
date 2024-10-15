/**
 * @copyright Copyright (c) 2024 VASS
 * @author VASS Team
 */

define([
    'jquery',
    'mage/url',
    'Magento_Customer/js/model/authentication-popup',
    'Magento_Customer/js/customer-data',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/confirm',
    'underscore',
    'jquery-ui-modules/widget',
    'mage/decorate',
    'mage/collapsible',
    'mage/cookies',
    'jquery-ui-modules/effect-fade'
], function ($, urlBuilder, authenticationPopup, customerData, alert, confirm, _) {
    'use strict';

    $.widget('mage.sidebar', {
        options: {
            isRecursive: true,
            minicart: {
                maxItemsVisible: 3
            },
            buttons: {
                qty_order_mas: '.action-more',
                qty_order_menos: '.action-less',
                qty_p_selector: '.item-show-text-qty'
            },
            qty_inputs_selector: '.item-qty.cart-item-qty'
        },
        scrollHeight: 0,
        shoppingCartUrl: window.checkout.shoppingCartUrl,

        /**
         * Create sidebar.
         * @private
         */
        _create: function () {
            this._initContent();
            this._initContentMixin();
        },

        /**
         * Update sidebar block.
         */
        update: function () {
            $(this.options.targetElement).trigger('contentUpdated');
            this._calcHeight();
            this._isOverflowed();
        },

        /**
         * @private
         */
        _initContent: function () {
            var self = this,
                events = {};

            this.element.decorate('list', this.options.isRecursive);

            /**
             * @param {jQuery.Event} event
             */
            events['click ' + this.options.button.close] = function (event) {
                event.stopPropagation();
                $(self.options.targetElement).dropdownDialog('close');
            };
            events['click ' + this.options.button.checkout] = $.proxy(function () {
                var cart = customerData.get('cart'),
                    customer = customerData.get('customer'),
                    element = $(this.options.button.checkout);

                if (!customer().firstname && cart().isGuestCheckoutAllowed === false) {
                    // set URL for redirect on successful login/registration. It's postprocessed on backend.
                    $.cookie('login_redirect', this.options.url.checkout);
                    if (this.options.url.isRedirectRequired) {
                        element.prop('disabled', true);
                        location.href = this.options.url.loginUrl;
                    } else {
                        authenticationPopup.showModal();
                    }

                    return false;
                }
                element.prop('disabled', true);
                location.href = this.options.url.checkout;
            }, this);

            /**
             * @param {jQuery.Event} event
             */
            events['click ' + this.options.button.remove] =  function (event) {
                event.stopPropagation();

                confirm({
                    content: self.options.confirmMessage,
                    actions: {
                        /** @inheritdoc */
                        confirm: function () {

                            self._removeItem($(event.currentTarget));
                        },

                        /** @inheritdoc */
                        always: function (e) {
                            e.stopImmediatePropagation();
                        }
                    }
                });
            };

            /**
             * @param {jQuery.Event} event
             */
            events['keyup ' + this.options.item.qty] = function (event) {
                self._showItemButton($(event.target));
            };

            /**
             * @param {jQuery.Event} event
             */
            events['change ' + this.options.item.qty] = function (event) {
                self._showItemButton($(event.target));
            };

            /**
             * @param {jQuery.Event} event
             */
            events['click ' + this.options.item.button] = function (event) {
                event.stopPropagation();
                self._updateItemQty($(event.currentTarget));
            };

            /**
             * @param {jQuery.Event} event
             */
            events['focusout ' + this.options.item.qty] = function (event) {
                self._validateQty($(event.currentTarget));
            };

            this._on(this.element, events);
            this._calcHeight();
            this._isOverflowed();
        },

        /**
         * Add 'overflowed' class to minicart items wrapper element
         *
         * @private
         */
        _isOverflowed: function () {
            var list = $(this.options.minicart.list),
                cssOverflowClass = 'overflowed';

            if (this.scrollHeight > list.innerHeight()) {
                list.parent().addClass(cssOverflowClass);
            } else {
                list.parent().removeClass(cssOverflowClass);
            }
        },

        /**
         * @param {HTMLElement} elem
         * @private
         */
        _showItemButton: function (elem) {
            var itemId = elem.data('cart-item'),
                itemQty = elem.data('item-qty');

            if (this._isValidQty(itemQty, elem.val())) {
                $('#update-cart-item-' + itemId).show('fade', 300);
            } else if (elem.val() == 0) { //eslint-disable-line eqeqeq
                this._hideItemButton(elem);
            } else {
                this._hideItemButton(elem);
            }
        },

        /**
         * @param {*} origin - origin qty. 'data-item-qty' attribute.
         * @param {*} changed - new qty.
         * @returns {Boolean}
         * @private
         */
        _isValidQty: function (origin, changed) {
            return origin != changed && //eslint-disable-line eqeqeq
                changed.length > 0 &&
                changed - 0 == changed && //eslint-disable-line eqeqeq
                changed - 0 > 0;
        },

        /**
         * @param {Object} elem
         * @private
         */
        _validateQty: function (elem) {
            var itemQty = elem.data('item-qty');

            if (!this._isValidQty(itemQty, elem.val())) {
                elem.val(itemQty);
            }
        },

        /**
         * @param {HTMLElement} elem
         * @private
         */
        _hideItemButton: function (elem) {
            var itemId = elem.data('cart-item');

            $('#update-cart-item-' + itemId).hide('fade', 300);
        },

        /**
         * @param {HTMLElement} elem
         * @private
         */
        _updateItemQty: function (elem) {
            var itemId = elem.data('cart-item');

            this._ajax(this.options.url.update, {
                'item_id': itemId,
                'item_qty': $('#cart-item-' + itemId + '-qty').val()
            }, elem, this._updateItemQtyAfter);
        },

        /**
         * Update content after update qty
         *
         * @param {HTMLElement} elem
         */
        _updateItemQtyAfter: function (elem) {
            var productData = this._getProductById(Number(elem.data('cart-item')));

            if (!_.isUndefined(productData)) {
                $(document).trigger('ajax:updateCartItemQty');

                if (window.location.href === this.shoppingCartUrl) {
                    window.location.reload(false);
                }
            }
            this._hideItemButton(elem);
        },

        /**
         * @param {HTMLElement} elem
         * @private
         */
        _removeItem: function (elem) {
            var itemId = elem.data('cart-item');

            this._ajax(this.options.url.remove, {
                'item_id': itemId
            }, elem, this._removeItemAfter);
        },

        /**
         * Update content after item remove
         *
         * @param {Object} elem
         * @private
         */
        _removeItemAfter: function (elem) {
            var productData = this._getProductById(Number(elem.data('cart-item')));

            if (!_.isUndefined(productData)) {
                $(document).trigger('ajax:removeFromCart', {
                    productIds: [productData['product_id']]
                });

                if (window.location.href.indexOf(this.shoppingCartUrl) === 0) {
                    window.location.reload();
                }
            }
        },

        /**
         * Retrieves product data by Id.
         *
         * @param {Number} productId - product Id
         * @returns {Object|undefined}
         * @private
         */
        _getProductById: function (productId) {
            return _.find(customerData.get('cart')().items, function (item) {
                return productId === Number(item['item_id']);
            });
        },

        /**
         * @param {String} url - ajax url
         * @param {Object} data - post data for ajax call
         * @param {Object} elem - element that initiated the event
         * @param {Function} callback - callback method to execute after AJAX success
         */
        _ajax: function (url, data, elem, callback) {
            $.extend(data, {
                'form_key': $.mage.cookies.get('form_key')
            });

            $.ajax({
                url: url,
                data: data,
                type: 'post',
                dataType: 'json',
                context: this,

                /** @inheritdoc */
                beforeSend: function () {
                    elem.attr('disabled', 'disabled');
                },

                /** @inheritdoc */
                complete: function () {
                    elem.attr('disabled', null);
                }
            })
                .done(function (response) {
                    var msg;

                    if (response.success) {
                        callback.call(this, elem, response);
                    } else {
                        msg = response['error_message'];

                        if (msg) {
                            alert({
                                content: msg
                            });
                        }
                    }
                })
                .fail(function (error) { });
        },
        /**
         * Calculate height of minicart list
         *
         * @private
         */
        _calcHeight: function () {
            var self = this,
                height = 0,
                counter = this.options.minicart.maxItemsVisible,
                target = $(this.options.minicart.list),
                outerHeight;

            self.scrollHeight = 0;
            target.children().each(function () {

                if ($(this).find('.options').length > 0) {
                    $(this).collapsible();
                }
                outerHeight = $(this).outerHeight(true);

                if (counter-- > 0) {
                    height += outerHeight;
                }
                self.scrollHeight += outerHeight;
            });

            target.parent().height(height);
        },

        /**
         * @private
         */
        _initContentMixin: function () {
            var self = this,
                events = {};

            $(this.options.qty_inputs_selector).each(function () {
                $(this).data("val", $(this).val());
            });

            /**
             * @param {jQuery.Event} event
             */
            events['click ' + this.options.buttons.qty_order_menos] = function (event) {
                if (self._isDoubleClicked($(event.currentTarget))) return;
                self._updateItemQtyLessMixin($(event.currentTarget));
            }

            /**
             * @param {jQuery.Event} event
             */
            events['click ' + this.options.buttons.qty_order_mas] = function (event) {
                if (self._isDoubleClicked($(event.currentTarget))) return;
                self._updateItemQtyMoreMixin($(event.currentTarget));
            }

            //pending for inputs
            /**
             * @param {jQuery.Event} event
             */
            events['keyup ' + this.options.qty_inputs_selector] = function (event) {
                self._updateItemQtyKeyup(event.currentTarget);
            };

            /**
             * @param {jQuery.Event} event
             */
            events['change ' + this.options.qty_inputs_selector] = function (event) {
                self._updateItemQtyChange($(event.currentTarget));
            };

            /**
             * @param {jQuery.Event} event
             */
            events['focusout ' + this.options.qty_inputs_selector] = function (event) {
                self._updateItemQtyFocusOut($(event.currentTarget));
            };

            /**
             * @param {jQuery.Event} event
             */
            events['click' + this.options.buttons.qty_p_selector] = function (event) {
                $(event.currentTarget).addClass('custom_attributes');
                $(event.currentTarget).closest('div.parent-qty').find('.cart-item-qty').removeClass('custom_attributes');
            };

            this._on(this.element, events);
        },

        _updateItemQtyLessMixin: function(elem){
            var caja = elem.closest("div.details-qty").find(".cart-item-qty");
            var num = parseFloat(caja.val()) - 1;
            var min = 1;
            if(num >= min){
                caja.val(num);
                this._requestUpdateCart(caja);
            }
        },

        _updateItemQtyMoreMixin: function(elem){
            var caja = elem.closest("div.details-qty").find(".cart-item-qty");
            var num = parseFloat(caja.val()) + 1;

            caja.val(num);
            this._requestUpdateCart(caja);
        },

        _updateItemQtyChange: function (elem) {
            var caja = elem.closest("div.details-qty").find(".cart-item-qty");
            var num = parseFloat(caja.val());
            var min = 1;

            if (num >= min){
                caja.val(num);
                this._requestUpdateCart(caja);
            }
        },

        _updateItemQtyKeyup: function (elem) {
            elem.value = elem.value.replace(/\D/g, '');
        },

        _updateItemQtyFocusOut: function(elem){
            var a = elem.val();
            if (a == '') {
                elem.val(elem.data("val"));
            }
        },

        _requestUpdateCart: function (caja)
        {
            if(caja.val() == caja.data('val'))
            {
                return false;
            }
            $('body').trigger('processStart');
            $('[data-block="minicart"]').trigger('contentLoading');
            var formData = {
                itemId: caja.attr("data-cart-item"),
                itemQty: caja.val(),
                form_key: $("input[name='form_key']").val()
            };

            $.ajax({
                url: urlBuilder.build("flow/index/updateitem"),
                data: formData,
                dataType: 'json',
                type: 'post',
                cache: false
            }).success(function (data) {

                caja.data('val',parseInt(caja.val()));
                var id=caja.attr("id").replace('-item','');
                var input=document.getElementById(id);
                $(input).val(caja.val());
                var div=document.getElementsByClassName("cart-container");
                $(div).trigger('contentUpdated');

            }).fail(function() {
                /* do nothing */
            }).always(function() {
                $('[data-block="minicart"]').trigger('contentUpdated');
            }).complete(function () {
                $('body').trigger('processStop');
            });
        },

        _isDoubleClicked: function (elem) {
            if (elem.data("isclicked")) return true;

            //mark as clicked for few seconds
            elem.data("isclicked", true);
            setTimeout(function () {
                elem.removeData("isclicked");
            }, 2500);

            //return false to indicate this click was allowed
            return false;
        }
    });

    return $.mage.sidebar;
});
