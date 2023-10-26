var config = {
    map: {
        '*': {
            opentab: 'WolfSellers_Bopis/js/opentab'
        }
    },
    config: {
        mixins: {
            'Magento_Catalog/js/catalog-add-to-cart': {
                'WolfSellers_Bopis/js/catalog-add-to-cart-mixin': true
            },
            'Magento_Customer/js/model/customer-addresses': {
                'WolfSellers_Bopis/js/model/customer-addresses-mixin': true
            },
            'mage/collapsible': {
                'WolfSellers_Bopis/js/mage/collapsible-mixin': true
            },
            'Magento_Checkout/js/view/minicart': {
                'WolfSellers_Bopis/js/minicart': true
            },
        },
    },
};
