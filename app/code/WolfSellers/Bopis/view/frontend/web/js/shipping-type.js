define([
    'Magento_Customer/js/customer-data',
    'jquery',
    'underscore',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/modal',
    'mage/url',
    'mage/translate',
    'tabs',
    'domReady!'
], function (
    customerData,
    $,
    _,
    alert,
    confirm,
    modal,
    mageUrl,
    $t
) {
    var options = {
        modalSelector: '.bopis-modal',
        addressFormSelector: '.modal-address-form',
        addressGridSelector: '.modal-addresses-grid',
        typeStorePickup: 'store-pickup',
        typeDelivery: 'delivery',
        typeAddressSelected: 'address-selected',
        typeSourceSelected: 'source-selected',
        tabDeliverySelector: '#tab-delivery',
        tabStorePickupSelector: '#tab-store-pickup',
        radioAddressSelector: 'input[type="radio"][name="address_id"]',
        radioSourceSelector: 'input[type="radio"][name="source_code"]',
        messagesSelector: '[data-placeholder="messages"]',
        addButton: '#product-addtocart-button',
        errorMessage: "Agrega una dirección de entrega o una Sucursal para recoger tu producto",
    };

    $(options.addButton).on('click', function (){
        /* remove this for to avoid the this.options is undefined error */
        var bopis = customerData.get('bopis')();

        if (bopis.is_active){
            if (bopis.error){
                $(options.messagesSelector).html("<div class='message-error error message'>" + options.errorMessage + "</div>");
                return ;
            }
            if (bopis.cart_have_bundle){
            }
        }
    });

    jQuery('body').on('click', '.tab-store-pickup', function(e) {
        $('.tab-delivery').removeClass("active");
        $('.tab-store-pickup').addClass("active");

        if(typeof e.originalEvent !== 'undefined' && e.originalEvent.isTrusted)
        {
            $('body').addClass('noscroll');
        }

        /*
         * INICIO - IS-382 - mejoramiento de bopis
         */
        var lat = null;
        var lng = null;
        var sku = jQuery("#product_addtocart_form").data("product-sku");
        var opts = {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        }
        navigator.geolocation.getCurrentPosition(
            function success(pos) {
                var crd = pos.coords;
                lat = crd.latitude;
                lng = crd.longitude;
            },
            function error(err) {
                console.warn('ERROR(' + err.code + '): ' + err.message);
            }, opts
        );
        $.ajax({
            url: mageUrl.build('bopis/modal/sources'),
            data: {sku:sku, lat:lat, lng:lng},
            type: 'GET',
            dataType: 'json',
            cache: false
        }).success(function (response) {
            if (response.count <= 0){
                $('a.select-source').hide();
                var content = $('<span>').append('Disponible sólo para envio a Domicilio.');
                $('#tab-store-pickup').empty().append(content);
            }
        }).fail(function(response) {
        }).always(function() {
        });
        /*
         * FIN - IS-382 - mejoramiento de bopis
         */
    });

    validateBopisData();

    jQuery('body').on('click', '.tab-delivery', function() {
        //$('.tab-store-pickup').removeClass("active");
        //$('.tab-delivery').addClass("active");
        saveDelivery();
        console.log('click delivery');
    });



    function saveDelivery(){
        var bopis = customerData.get(['bopis'], false);
        if (bopis().type === options.typeDelivery ) return;

        /*validation is removed since the msg for both stores are the same*/

        if (bopis().type === options.typeStorePickup){
            confirm({
                content: $t('No seleccionaste Recoger en una Tienda. Si continúas, tu método de entrega va a cambiar a Envío a Domicilio. Deseas continuar?.'),
                actions:{
                    confirm:function (){
                        customerData.invalidate(['bopis']);
                        ajaxCall(
                            mageUrl.build('bopis/modal/saveAddress'),
                            options.typeAddressSelected,
                            {'address_data': "", "address_formatted" : "", "type" : options.typeDelivery, "store" : false},
                            "POST"
                        );
                    },
                    cancel:function (){
                        $('.tab-store-pickup').trigger("click")
                    }
                }
            });
        }else{
            ajaxCall(
                mageUrl.build('bopis/modal/saveAddress'),
                options.typeAddressSelected,
                {'address_data': "", "address_formatted" : "", "type" : options.typeDelivery, "store" : false},
                "POST"
            );
        }
    }

    jQuery('body').on('click', 'a.shipping-address', function() {

        var bopis = customerData.get("bopis");
        var customer = customerData.get("customer");

        if(bopis().type && bopis().type == 'delivery' && !customer().firstname){
            ajaxCall(mageUrl.build('bopis/modal/addresses'), options.typeDelivery, {new_address:true});
        }else{
            ajaxCall(mageUrl.build('bopis/modal/addresses'), options.typeDelivery, null);
        }
    });

    jQuery('body').on('click', 'a.select-source', function(e) {

        if(typeof e.originalEvent !== 'undefined' && e.originalEvent.isTrusted)
        {
            $('body').addClass('noscroll');
        }


        var lat = null;
        var lng = null;

        var opts = {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        }

        navigator.geolocation.getCurrentPosition(
            function success(pos) {
                var crd = pos.coords;
                lat = crd.latitude;
                lng = crd.longitude;
            },
            function error(err) {
                console.warn('ERROR(' + err.code + '): ' + err.message);
            }, opts
        );

        var cart = customerData.get('cart');
        var sku = jQuery("#product_addtocart_form").data("product-sku");
        customerData.reload(['bopis'], false);
        var bopis = customerData.get('bopis');

        if (cart().items.length > 1 && bopis()){
            confirm({
                content: $t('Se revisará la disponibilidad de los productos ingresados previamente en el carrito.'),
                actions:{
                    confirm:function (){
                        ajaxCall(mageUrl.build('bopis/modal/sources'), options.typeStorePickup, {sku:sku, lat:lat, lng:lng});
                    }
                }
            });

            return;
        }
        ajaxCall(mageUrl.build('bopis/modal/sources'), options.typeStorePickup, {sku:sku, lat:lat, lng:lng});

    });

    $('body').on('click', 'button.select-address', function() {
        validateAddressSelected();
    });

    $('body').on('click', 'button.add-another-address', function() {
        showAddressForm();
    });

    $('body').on('click', 'button.add-address', function() {
        if ($(this).parents("form").valid()){
            saveAddress();
        }
    });

    $('body').on('click', 'button.select-source', function(e) {
        if(typeof e.originalEvent !== 'undefined' && e.originalEvent.isTrusted)
        {
            $('body').addClass('noscroll');
        }

        validateSourceSelected();
    });

    function ajaxCall(url, type, data, method = 'GET') {
        console.log(url);
        console.log(type);
        console.log(data);
        $.ajax({
            url: url,
            data: data,
            type: method,
            dataType: 'json',
            cache: false
        }).success(function (response) {
            console.log(response);
            if (type === options.typeDelivery) {
                $(options.modalSelector).html(response.content.form);

                if (typeof response.content.grid !== typeof undefined) {
                    $(options.modalSelector).append(response.content.grid);
                    $(options.addressFormSelector).hide();

                    customerData.reload(['bopis'], true);
                    var address = customerData.get('bopis');

                    var addressData = address().object || null;

                    addressData = JSON.parse(addressData);

                    if (addressData !== null && addressData['address_id'] !== null) {
                        $('#address-' + addressData['address_id']).prop('checked', true);
                    }
                }

                showModal('', $(options.modalSelector))
            } else if (type === options.typeAddressSelected) {
                customerData.reload(['bopis'], true);
                $(options.messagesSelector).html("");
                $(options.addButton).attr("type", "submit");
                $(options.addButton).children("span").text("Agregar al carrito");
            } else if (type === options.typeStorePickup) {
                $(options.modalSelector).html(response.sources);
                customerData.reload(['bopis'], true);
                var source = customerData.get('bopis');

                var sourceData = source().object || null
                sourceData = JSON.parse(sourceData);

                if (sourceData !== null && sourceData['source_code'] !== null) {
                    $('#source-' + sourceData['source_code']).prop('checked', true);
                }

                if (response.count <= 0){
                    $(options.addButton).attr("type", "button");
                    $(options.addButton).children("span").text("No disponible en la tienda seleccionada");
                }else{
                    $(options.addButton).attr("type", "submit");
                    $(options.addButton).children("span").text("Agregar al carrito");
                }

                showModal('', $(options.modalSelector))
            } else if (type === options.typeSourceSelected) {
                customerData.reload(['bopis'], false);
                $(options.messagesSelector).html("");
                $(options.addButton).attr("type", "submit");
                localStorage.setItem('bopis', 1);
                console.log('source selected');
            }
        }).fail(function(response) {
        }).always(function() {
        });
    }

    function showModal(title, selector) {
        var modalOptions = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            modalClass: 'see-detail-modal',
            title: title,
            buttons: []
        };

        modal(modalOptions, selector);

        selector.modal('openModal');
    }

    function showAddressForm() {
        $(options.addressFormSelector).show();
        $(options.addressGridSelector).hide();
    }

    function validateAddressSelected() {
        if (!$(options.radioAddressSelector).is(':checked')) {
            alert({
                title: $t('Notification'),
                content: $t('To continue you must select an address'),
            });

            return;
        }

        var addressSelected = $(options.radioAddressSelector + ':checked').val();

        $(options.modalSelector).modal('closeModal');

        ajaxCall(mageUrl.build('bopis/modal/addressData'), options.typeAddressSelected, {'address_id': addressSelected})
    }

    function saveAddress() {
        var addressData = {},
            street = 1,
            formData = $('#form-validate').serialize(),
            region = $('#region_id option:selected').text(),
            province = $('#province_id option:selected').text(),
            country = $('#country option:selected').text(),
            formattedAddress;

        formData = formData.split('&');

        for (var i = 0; i < formData.length; i++) {
            var data = formData[i].split('=');

            if (data[0] !== 'form_key') {
                var key = decodeURIComponent(data[0]);

                if (key === 'street[]') {
                    key = key.replace('[]', '');
                    key += '_' + street;

                    street++;
                }

                addressData[key] = decodeURIComponent(data[1]);
            }
        }

        addressData.region = region;
        addressData.province = province;
        addressData.country = country;
        addressData.address_id = null;
        addressData.product_id = jQuery("#prodId").val();

        formattedAddress = (addressData['street_1'] + ' ' + (addressData['street_2'] ?? '') + ' ' + (addressData['street_3'] ?? '')).trim() + ', ' +
            addressData['province'] + ' ' + addressData['region'] + ' ' + addressData['country'];

        addressData = JSON.stringify(addressData);

        customerData.invalidate(['bopis']);
        ajaxCall(
            mageUrl.build('bopis/modal/saveAddress'),
            options.typeAddressSelected,
            {'address_data': addressData.replaceAll('+',' '), "address_formatted" : formattedAddress.replaceAll('+',' '), "type" : options.typeDelivery, "store" : false},
            "POST"
        );
        $(options.modalSelector).modal('closeModal');
    }

    function validateSourceSelected() {
        if (!$(options.radioSourceSelector).is(':checked')) {
            alert({
                title: $t('Notification'),
                content: $t('To continue you must select a store'),
            });

            return;
        }

        var sourceSelected = $(options.radioSourceSelector + ':checked').val();

        $(options.modalSelector).modal('closeModal');

        ajaxCall(mageUrl.build('bopis/modal/sourceData'), options.typeSourceSelected, {'source_code': sourceSelected})
    }

    /**
     * @function The click to home delivery is added by default
     * @params type
     * @return
     */
    function validateBopisData() {
        var bopisValidate = customerData.get('bopis')();
        if(_.isUndefined(bopisValidate.type)){
            $('.tab-delivery').trigger('click');
        }
    }

});
