define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'mage/url'
], function ($,Customer,urlBuilder) {
    'use strict';

    $.widget('mage.UpdateQty', {
        qtyField: null,

        options: {
            decreaseSelector: '.action-less',
            increaseSelector: '.action-more',
            qtySelector: 'input[name="qty"]',
            defaultValue: 1
        },

        /**
         * @private
         */
        _create: function () {
            this.qtyField = this.element.find(this.options.qtySelector);
            this._qtyInicial();
            this._bind();
        },

        _bind: function () {
            var handlers = {};

            handlers['click ' + this.options.decreaseSelector] = '_onDecreaseClicked';
            handlers['click ' + this.options.increaseSelector] = '_onIncreaseClicked';
            this._on(handlers);
        },

        _onDecreaseClicked: function (e) {
            e.stopPropagation();
            var qty=this._getQty();
            var result= qty-1;

            if (result == 0){
                result=this.options.defaultValue;
            }
            this.qtyField.val(result);
            this._requestUpdateCart(result);

        },

        _onIncreaseClicked: function (e) {
            e.stopPropagation();
            var qty=this._getQty()+1;
            this.qtyField.val(qty);
            this._requestUpdateCart(qty);

        },

        _getQty: function () {

            var qty = parseInt(this.qtyField.val()) || this.options.defaultValue;
                qty >= this.options.defaultValue ? qty : this.options.defaultValue;
             
            return  qty;

        },
        _requestUpdateCart: function (qty)
        {
            var product=$("input[name='product']").val();
            var cart=Customer.get('cart')()["items"];
            var item_id=cart.find( Items => Items.product_id == product )["item_id"];

            $('[data-block="minicart"]').trigger('contentLoading');
            var formData = {
                itemId: item_id,
                itemQty: qty,
                form_key: $("input[name='form_key']").val()
            };

            $.ajax({
                url: urlBuilder.build("flow/index/updateitem"),
                data: formData,
                dataType: 'json',
                type: 'post',
                cache: false
            }).success(function (data) {
                $("#cart-item-"+item_id+"-qty").val(qty);
            }).fail(function() {
                /* do nothing */
            }).always(function() {
                $('[data-block="minicart"]').trigger('contentUpdated');
            });
        },

        _qtyInicial: function(){

            var product=$("input[name='product']").val();
            var cart=Customer.get('cart')()["items"];

            if (cart.length > 0){
                var qty=cart.find( Items => Items.product_id == product )["qty"];
                console.log($(this.qtyField));
                $(this.qtyField).val(qty);
            }
        }
    });

    return $.mage.UpdateQty;
});