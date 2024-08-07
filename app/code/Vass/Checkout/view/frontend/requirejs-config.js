var config = {
    map: {
        '*': {
            'general-validation': 'Vass_Checkout/js/general-validation',
            'validation-documento': 'Vass_Checkout/js/validation-documento',
        }
    },
    paths: {
        'general-validation': 'Vass_Checkout/js/general-validation',
        'validation-documento': 'Vass_Checkout/js/validation-documento'
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
    }
};
