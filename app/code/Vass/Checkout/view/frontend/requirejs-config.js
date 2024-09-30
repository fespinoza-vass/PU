var config = {
    map: {
        '*': {
            'general-validation': 'Vass_Checkout/js/general-validation',
            'validation-documento': 'Vass_Checkout/js/validation-documento',
            'Magento_SalesRule/template/payment/discount.html': 'Vass_Checkout/template/payment/discount.html',
            'popups': 'Vass_Checkout/js/popups',
            'texto-checkbox': 'Vass_Checkout/js/texto-checkbox'
        }
    },
    paths: {
        'general-validation': 'Vass_Checkout/js/general-validation',
        'validation-documento': 'Vass_Checkout/js/validation-documento',
        'popups': 'Vass_Checkout/js/popups',
        'texto-checkbox': 'Vass_Checkout/js/texto-checkbox'
    },
    shim: {
        'general-validation': {
            deps: ['jquery'],
            exports: 'general-validation'
        },
        'validation-documento': {
            deps: ['jquery'],
            exports: 'validation-documento'
        },
        'popups': {
            deps: ['jquery'],
            exports: 'popups'
        },
        'texto-checkbox': {
            deps: ['jquery'],
            exports: 'popups'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/billing-address': {
                'Vass_Checkout/js/view/billing-address-mixin-update': true
            }
        }
    }
};
