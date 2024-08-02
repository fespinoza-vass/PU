var config = {
    map: {
        '*': {
            'vass-validation': 'Vass_Checkout/js/vass-validation'
        }
    },
    paths: {
        'vass-validation': 'Vass_Checkout/js/vass-validation'
    },
    shim: {
        'vass-validation': {
            deps: ['jquery'],
            exports: 'vass-validation'
        }
    }
};
