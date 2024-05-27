(function(require){
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            directoryRegionUpdater: 'Magento_Directory/js/region-updater'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    waitSeconds: 0,
    map: {
        '*': {
            'ko': 'knockoutjs/knockout',
            'knockout': 'knockoutjs/knockout',
            'mageUtils': 'mage/utils/main',
            'rjsResolver': 'mage/requirejs/resolver',
            'jquery-ui-modules/core': 'jquery/ui-modules/core',
            'jquery-ui-modules/accordion': 'jquery/ui-modules/widgets/accordion',
            'jquery-ui-modules/autocomplete': 'jquery/ui-modules/widgets/autocomplete',
            'jquery-ui-modules/button': 'jquery/ui-modules/widgets/button',
            'jquery-ui-modules/datepicker': 'jquery/ui-modules/widgets/datepicker',
            'jquery-ui-modules/dialog': 'jquery/ui-modules/widgets/dialog',
            'jquery-ui-modules/draggable': 'jquery/ui-modules/widgets/draggable',
            'jquery-ui-modules/droppable': 'jquery/ui-modules/widgets/droppable',
            'jquery-ui-modules/effect-blind': 'jquery/ui-modules/effects/effect-blind',
            'jquery-ui-modules/effect-bounce': 'jquery/ui-modules/effects/effect-bounce',
            'jquery-ui-modules/effect-clip': 'jquery/ui-modules/effects/effect-clip',
            'jquery-ui-modules/effect-drop': 'jquery/ui-modules/effects/effect-drop',
            'jquery-ui-modules/effect-explode': 'jquery/ui-modules/effects/effect-explode',
            'jquery-ui-modules/effect-fade': 'jquery/ui-modules/effects/effect-fade',
            'jquery-ui-modules/effect-fold': 'jquery/ui-modules/effects/effect-fold',
            'jquery-ui-modules/effect-highlight': 'jquery/ui-modules/effects/effect-highlight',
            'jquery-ui-modules/effect-scale': 'jquery/ui-modules/effects/effect-scale',
            'jquery-ui-modules/effect-pulsate': 'jquery/ui-modules/effects/effect-pulsate',
            'jquery-ui-modules/effect-shake': 'jquery/ui-modules/effects/effect-shake',
            'jquery-ui-modules/effect-slide': 'jquery/ui-modules/effects/effect-slide',
            'jquery-ui-modules/effect-transfer': 'jquery/ui-modules/effects/effect-transfer',
            'jquery-ui-modules/effect': 'jquery/ui-modules/effect',
            'jquery-ui-modules/menu': 'jquery/ui-modules/widgets/menu',
            'jquery-ui-modules/mouse': 'jquery/ui-modules/widgets/mouse',
            'jquery-ui-modules/position': 'jquery/ui-modules/position',
            'jquery-ui-modules/progressbar': 'jquery/ui-modules/widgets/progressbar',
            'jquery-ui-modules/resizable': 'jquery/ui-modules/widgets/resizable',
            'jquery-ui-modules/selectable': 'jquery/ui-modules/widgets/selectable',
            'jquery-ui-modules/slider': 'jquery/ui-modules/widgets/selectmenu',
            'jquery-ui-modules/sortable': 'jquery/ui-modules/widgets/sortable',
            'jquery-ui-modules/spinner': 'jquery/ui-modules/widgets/spinner',
            'jquery-ui-modules/tabs': 'jquery/ui-modules/widgets/tabs',
            'jquery-ui-modules/tooltip': 'jquery/ui-modules/widgets/tooltip',
            'jquery-ui-modules/widget': 'jquery/ui-modules/widget',
            'jquery-ui-modules/timepicker': 'jquery/timepicker',
            'vimeo': 'vimeo/player',
            'vimeoWrapper': 'vimeo/vimeo-wrapper'
        }
    },
    shim: {
        'jquery/jquery-migrate': ['jquery'],
        'mage/adminhtml/backup': ['prototype'],
        'mage/captcha': ['prototype'],
        'mage/new-gallery': ['jquery'],
        'jquery/ui': ['jquery'],
        'matchMedia': {
            'exports': 'mediaCheck'
        },
        'magnifier/magnifier': ['jquery'],
        'vimeo/player': {
            'exports': 'Player'
        }
    },
    paths: {
        'jquery/validate': 'jquery/jquery.validate',
        'jquery/file-uploader': 'jquery/fileUploader/jquery.fileuploader',
        'prototype': 'legacy-build.min',
        'jquery/jquery.cookie': 'js-cookie/cookie-wrapper',
        'jquery/jquery-storageapi': 'js-storage/storage-wrapper',
        'text': 'mage/requirejs/text',
        'domReady': 'requirejs/domReady',
        'spectrum': 'jquery/spectrum/spectrum',
        'tinycolor': 'jquery/spectrum/tinycolor',
        'jquery-ui-modules': 'jquery/ui-modules'
    },
    deps: [
        'jquery/jquery-migrate'
    ],
    config: {
        text: {
            'headers': {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }
    }
};

require(['jquery'], function ($) {
    'use strict';

    $.noConflict();
});

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            'rowBuilder':             'Magento_Theme/js/row-builder',
            'toggleAdvanced':         'mage/toggle',
            'translateInline':        'mage/translate-inline',
            'sticky':                 'mage/sticky',
            'tabs':                   'mage/tabs',
            'collapsible':            'mage/collapsible',
            'dropdownDialog':         'mage/dropdown',
            'dropdown':               'mage/dropdowns',
            'accordion':              'mage/accordion',
            'loader':                 'mage/loader',
            'tooltip':                'mage/tooltip',
            'deletableItem':          'mage/deletable-item',
            'itemTable':              'mage/item-table',
            'fieldsetControls':       'mage/fieldset-controls',
            'fieldsetResetControl':   'mage/fieldset-controls',
            'redirectUrl':            'mage/redirect-url',
            'loaderAjax':             'mage/loader',
            'menu':                   'mage/menu',
            'popupWindow':            'mage/popup-window',
            'validation':             'mage/validation/validation',
            'breadcrumbs':            'Magento_Theme/js/view/breadcrumbs',
            'jquery/ui':              'jquery/compat',
            'cookieStatus':           'Magento_Theme/js/cookie-status'
        }
    },
    deps: [
        'mage/common',
        'mage/dataPost',
        'mage/bootstrap'
    ],
    config: {
        mixins: {
            'Magento_Theme/js/view/breadcrumbs': {
                'Magento_Theme/js/view/add-home-breadcrumb': true
            }
        }
    }
};

/* eslint-disable max-depth */
/**
 * Adds polyfills only for browser contexts which prevents bundlers from including them.
 */
if (typeof window !== 'undefined' && window.document) {
    /**
     * Polyfill localStorage and sessionStorage for browsers that do not support them.
     */
    try {
        if (!window.localStorage || !window.sessionStorage) {
            throw new Error();
        }

        localStorage.setItem('storage_test', 1);
        localStorage.removeItem('storage_test');
    } catch (e) {
        config.deps.push('mage/polyfill');
    }
}
/* eslint-enable max-depth */

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            checkoutBalance:    'Magento_Customer/js/checkout-balance',
            address:            'Magento_Customer/js/address',
            changeEmailPassword: 'Magento_Customer/js/change-email-password',
            passwordStrengthIndicator: 'Magento_Customer/js/password-strength-indicator',
            zxcvbn: 'Magento_Customer/js/zxcvbn',
            addressValidation: 'Magento_Customer/js/addressValidation',
            showPassword: 'Magento_Customer/js/show-password',
            'Magento_Customer/address': 'Magento_Customer/js/address',
            'Magento_Customer/change-email-password': 'Magento_Customer/js/change-email-password',
            globalSessionLoader:    'Magento_Customer/js/customer-global-session-loader.js'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            priceBox:             'Magento_Catalog/js/price-box',
            priceOptionDate:      'Magento_Catalog/js/price-option-date',
            priceOptionFile:      'Magento_Catalog/js/price-option-file',
            priceOptions:         'Magento_Catalog/js/price-options',
            priceUtils:           'Magento_Catalog/js/price-utils'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            compareList:            'Magento_Catalog/js/list',
            relatedProducts:        'Magento_Catalog/js/related-products',
            upsellProducts:         'Magento_Catalog/js/upsell-products',
            productListToolbarForm: 'Magento_Catalog/js/product/list/toolbar',
            catalogGallery:         'Magento_Catalog/js/gallery',
            catalogAddToCart:       'Magento_Catalog/js/catalog-add-to-cart'
        }
    },
    config: {
        mixins: {
            'Magento_Theme/js/view/breadcrumbs': {
                'Magento_Catalog/js/product/breadcrumbs': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            creditCardType: 'Magento_Payment/js/cc-type',
            'Magento_Payment/cc-type': 'Magento_Payment/js/cc-type'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            giftMessage:    'Magento_Sales/js/gift-message',
            ordersReturns:  'Magento_Sales/js/orders-returns',
            'Magento_Sales/gift-message':    'Magento_Sales/js/gift-message',
            'Magento_Sales/orders-returns':  'Magento_Sales/js/orders-returns'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            bundleOption:   'Magento_Bundle/bundle',
            priceBundle:    'Magento_Bundle/js/price-bundle',
            slide:          'Magento_Bundle/js/slide',
            productSummary: 'Magento_Bundle/js/product-summary'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            discountCode:           'Magento_Checkout/js/discount-codes',
            shoppingCart:           'Magento_Checkout/js/shopping-cart',
            regionUpdater:          'Magento_Checkout/js/region-updater',
            sidebar:                'Magento_Checkout/js/sidebar',
            checkoutLoader:         'Magento_Checkout/js/checkout-loader',
            checkoutData:           'Magento_Checkout/js/checkout-data',
            proceedToCheckout:      'Magento_Checkout/js/proceed-to-checkout',
            catalogAddToCart:       'Magento_Catalog/js/catalog-add-to-cart'
        }
    },
    shim: {
        'Magento_Checkout/js/model/totals' : {
            deps: ['Magento_Customer/js/customer-data']
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    shim: {
        cardinaljs: {
            exports: 'Cardinal'
        },
        cardinaljsSandbox: {
            exports: 'Cardinal'
        }
    },
    paths: {
        cardinaljsSandbox: 'https://includestest.ccdc02.com/cardinalcruise/v1/songbird',
        cardinaljs: 'https://songbird.cardinalcommerce.com/edge/v1/songbird'
    }
};


require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            escaper: 'Magento_Security/js/escaper'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            quickSearch: 'Magento_Search/js/form-mini',
            'Magento_Search/form-mini': 'Magento_Search/js/form-mini'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            catalogSearch: 'Magento_CatalogSearch/form-mini'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    deps: [],
    shim: {
        'chartjs/chartjs-adapter-moment': ['moment'],
        'tiny_mce_5/tinymce.min': {
            exports: 'tinyMCE'
        }
    },
    paths: {
        'ui/template': 'Magento_Ui/templates'
    },
    map: {
        '*': {
            uiElement:      'Magento_Ui/js/lib/core/element/element',
            uiCollection:   'Magento_Ui/js/lib/core/collection',
            uiComponent:    'Magento_Ui/js/lib/core/collection',
            uiClass:        'Magento_Ui/js/lib/core/class',
            uiEvents:       'Magento_Ui/js/lib/core/events',
            uiRegistry:     'Magento_Ui/js/lib/registry/registry',
            consoleLogger:  'Magento_Ui/js/lib/logger/console-logger',
            uiLayout:       'Magento_Ui/js/core/renderer/layout',
            buttonAdapter:  'Magento_Ui/js/form/button-adapter',
            chartJs:        'chartjs/Chart.min',
            'chart.js':     'chartjs/Chart.min',
            tinymce:        'tiny_mce_5/tinymce.min',
            wysiwygAdapter: 'mage/adminhtml/wysiwyg/tiny_mce/tinymce5Adapter'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    deps: [
        'Magento_Ui/js/core/app'
    ]
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            fileElement: 'Magento_CustomerCustomAttributes/file-element'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            addToCart: 'Magento_Msrp/js/msrp'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/select-payment-method': {
                'Magento_SalesRule/js/action/select-payment-method-mixin': true
            },
            'Magento_Checkout/js/model/shipping-save-processor': {
                'Magento_SalesRule/js/model/shipping-save-processor-mixin': true
            },
            'Magento_Checkout/js/action/place-order': {
                'Magento_SalesRule/js/model/place-order-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            captcha: 'Magento_Captcha/js/captcha',
            'Magento_Captcha/captcha': 'Magento_Captcha/js/captcha'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            downloadable: 'Magento_Downloadable/js/downloadable',
            'Magento_Downloadable/downloadable': 'Magento_Downloadable/js/downloadable'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            toggleGiftCard: 'Magento_GiftCard/toggle-gift-card'
        }
    },
    'config': {
        'mixins': {
            'Magento_Paypal/js/view/amountProviders/product': {
                'Magento_GiftCard/product-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            wishlist:       'Magento_Wishlist/js/wishlist',
            addToWishlist:  'Magento_Wishlist/js/add-to-wishlist',
            wishlistSearch: 'Magento_Wishlist/js/search'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            configurable: 'Magento_ConfigurableProduct/js/configurable'
        }
    },
    config: {
        mixins: {
            'Magento_Catalog/js/catalog-add-to-cart': {
                'Magento_ConfigurableProduct/js/catalog-add-to-cart-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            advancedSearch: 'Magento_GiftRegistry/advanced-search',
            giftRegistry: 'Magento_GiftRegistry/gift-registry',
            addressOption: 'Magento_GiftRegistry/address-option',
            searchByChanged: 'Magento_GiftRegistry/js/search-by-changed'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'Magento_CheckoutAgreements/js/model/place-order-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'Magento_CheckoutAgreements/js/model/set-payment-information-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            pageCache:  'Magento_PageCache/js/page-cache'
        }
    },
    deps: ['Magento_PageCache/js/form-key-provider']
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            requireCookie: 'Magento_Cookie/js/require-cookie',
            cookieNotices: 'Magento_Cookie/js/notices'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            orderBySkuFailure:  'Magento_AdvancedCheckout/js/order-by-sku-failure',
            fileChooser:        'Magento_AdvancedCheckout/js/file-chooser'
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    paths: {
        dataServicesBase: ['https://commerce.adobedtm.com/v6/ds.min', 'Magento_DataServices/js/noopDs'],
        dataServicesDiscount: ['https://commerce.adobedtm.com/v6/discount.min', 'Magento_DataServices/js/noopDiscount'],
        magentoStorefrontEvents: ['https://unpkg.com/@adobe/magento-storefront-events-sdk@^1/dist/index', 'Magento_DataServices/js/noopSdk'],
        magentoStorefrontEventCollector: ['https://unpkg.com/@adobe/magento-storefront-event-collector@^1/dist/index', 'Magento_DataServices/js/noopCollector'],
    }
}
require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            multiShipping: 'Magento_Multishipping/js/multi-shipping',
            orderOverview: 'Magento_Multishipping/js/overview',
            payment: 'Magento_Multishipping/js/payment',
            billingLoader: 'Magento_Checkout/js/checkout-loader',
            cartUpdate: 'Magento_Checkout/js/action/update-shopping-cart',
            multiShippingBalance: 'Magento_Multishipping/js/multi-shipping-balance'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            giftCard:       'Magento_GiftCardAccount/js/gift-card',
            paymentMethod:  'Magento_GiftCardAccount/js/payment-method'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            ticker:     'Magento_CatalogEvent/js/ticker',
            carousel:   'Magento_CatalogEvent/js/carousel'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            giftOptions:    'Magento_GiftMessage/js/gift-options',
            'Magento_GiftMessage/gift-options':    'Magento_GiftMessage/js/gift-options'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            giftWrapping: 'Magento_GiftWrapping/gift-wrapping'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            'taxToggle': 'Magento_Weee/js/tax-toggle',
            'Magento_Weee/tax-toggle': 'Magento_Weee/js/tax-toggle'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            configurableVariationQty: 'Magento_InventoryConfigurableProductFrontendUi/js/configurable-variation-qty'
        }
    },
    config: {
        mixins: {
            'Magento_ConfigurableProduct/js/configurable': {
                'Magento_InventoryConfigurableProductFrontendUi/js/configurable': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            recentlyViewedProducts: 'Magento_Reports/js/recently-viewed'
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/quote': {
                'Magento_InventoryInStorePickupFrontend/js/model/quote-ext': true
            },
            'Magento_Checkout/js/view/shipping-information': {
                'Magento_InventoryInStorePickupFrontend/js/view/shipping-information-ext': true
            },
            'Magento_Checkout/js/model/checkout-data-resolver': {
                'Magento_InventoryInStorePickupFrontend/js/model/checkout-data-resolver-ext': true
            },
            'Magento_Checkout/js/checkout-data': {
                'Magento_InventoryInStorePickupFrontend/js/checkout-data-ext': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'Magento_InventorySwatchesFrontendUi/js/swatch-renderer': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            multipleWishlist: 'Magento_MultipleWishlist/js/multiple-wishlist'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            subscriptionStatusResolver: 'Magento_Newsletter/js/subscription-status-resolver',
            newsletterSignUp:  'Magento_Newsletter/js/newsletter-sign-up'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            'slick': 'Magento_PageBuilder/js/resource/slick/slick',
            'jarallax': 'Magento_PageBuilder/js/resource/jarallax/jarallax',
            'jarallaxVideo': 'Magento_PageBuilder/js/resource/jarallax/jarallax-video',
            'Magento_PageBuilder/js/resource/vimeo/player': 'vimeo/player',
            'Magento_PageBuilder/js/resource/vimeo/vimeo-wrapper': 'vimeo/vimeo-wrapper',
            'jarallax-wrapper': 'Magento_PageBuilder/js/resource/jarallax/jarallax-wrapper'
        }
    },
    shim: {
        'Magento_PageBuilder/js/resource/slick/slick': {
            deps: ['jquery']
        },
        'Magento_PageBuilder/js/resource/jarallax/jarallax-video': {
            deps: ['jarallax-wrapper', 'vimeoWrapper']
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            transparent: 'Magento_Payment/js/transparent',
            'Magento_Payment/transparent': 'Magento_Payment/js/transparent'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            orderReview: 'Magento_Paypal/js/order-review',
            'Magento_Paypal/order-review': 'Magento_Paypal/js/order-review',
            paypalCheckout: 'Magento_Paypal/js/paypal-checkout'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Customer/js/customer-data': {
                'Magento_Persistent/js/view/customer-data-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    shim: {
        recommendationsSDK: {
            exports: "recsSDK",
        },
    },
    paths: {
        recommendationsSDK: "https://magento-recs-sdk.adobe.net/v2/index",
        recommendationsEvents: ['https://commerce.adobedtm.com/recommendations/events/v1/recommendationsEvents.min', 'Magento_ProductRecommendationsLayout/js/noopRecommendationsEvents'],
    },
}

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            loadPlayer: 'Magento_ProductVideo/js/load-player',
            fotoramaVideoEvents: 'Magento_ProductVideo/js/fotorama-add-video-events',
            'vimeoWrapper': 'vimeo/vimeo-wrapper'
        }
    },
    shim: {
        vimeoAPI: {},
        'Magento_ProductVideo/js/load-player': {
            deps: ['vimeoWrapper']
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// eslint-disable-next-line no-unused-vars
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/place-order': {
                'Magento_ReCaptchaCheckout/js/model/place-order-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/*eslint strict: ["error", "global"]*/

'use strict';

var config = {
    config: {
        mixins: {
            'Magento_Ui/js/view/messages': {
                'Magento_ReCaptchaFrontendUi/js/ui-messages-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// eslint-disable-next-line no-unused-vars
var config = {
    config: {
        mixins: {
            'Magento_GiftCardAccount/js/action/set-gift-card-information': {
                'Magento_ReCaptchaGiftCard/js/action/set-gift-card-information-mixin': true
            },
            'Magento_GiftCardAccount/js/action/get-gift-card-information': {
                'Magento_ReCaptchaGiftCard/js/action/get-gift-card-information-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// eslint-disable-next-line no-unused-vars
var config = {
    config: {
        mixins: {
            'Magento_Paypal/js/view/payment/method-renderer/payflowpro-method': {
                'Magento_ReCaptchaPaypal/js/payflowpro-method-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// eslint-disable-next-line no-unused-vars
var config = {
    config: {
        mixins: {
            'jquery': {
                'Magento_ReCaptchaWebapiUi/js/jquery-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            removePoints: 'Magento_Reward/js/action/remove-points'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            rmaTrackInfo:   'Magento_Rma/rma-track-info',
            rmaCreate:      'Magento_Rma/rma-create'
        }
    },
    shim: {
        'Magento_Rma/rma-track-info': {
            deps: ['Magento_Rma/set-options']
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            mageTranslationDictionary: 'Magento_Translation/js/mage-translation-dictionary'
        }
    },
    deps: [
        'mageTranslationDictionary'
    ]
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            editTrigger: 'mage/edit-trigger',
            addClass: 'Magento_Translation/js/add-class',
            'Magento_Translation/add-class': 'Magento_Translation/js/add-class'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/payment/list': {
                'Magento_PaypalCaptcha/js/view/payment/list-mixin': true
            },
            'Magento_Paypal/js/view/payment/method-renderer/payflowpro-method': {
                'Magento_PaypalCaptcha/js/view/payment/method-renderer/payflowpro-method-mixin': true
            },
            'Magento_Captcha/js/view/checkout/defaultCaptcha': {
                'Magento_PaypalCaptcha/js/view/checkout/defaultCaptcha-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/shipping-rates-validation-rules': {
                'Amasty_Conditions/js/model/shipping-rates-validation-rules-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    map: {
        '*': {
            'amScrollToTabs': 'Amasty_CustomTabs/js/scroll-to-tabs'
        }
    },
    config: {
        mixins: {
            "Magento_Review/js/process-reviews": {
                'Amasty_CustomTabs/js/process-reviews': true
            },
            'mage/collapsible': {
                'Amasty_CustomTabs/js/collapsible-mixin': true
            },
            'mage/tabs': {
                'Amasty_CustomTabs/js/tabs-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    map: {
        '*': {
            'amcformPrompt': 'Amasty_Customform/js/form-prompt',
            'amcformMessageList': 'Magento_Ui/js/model/messageList'
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    map: {
        '*': {
            amsearchSlick: 'Amasty_Base/vendor/slick/slick.min',
            amsearch_helpers: 'Amasty_Xsearch/js/utils/helpers',
            amsearch_color_helper: 'Amasty_Xsearch/js/utils/color',
            amsearchProductLinksStorage: 'Amasty_Xsearch/js/utils/links-storage',
            amsearchProductSlider: 'Amasty_Xsearch/js/widgets/product-slider',
            amsearchAnalyticsCollector: 'Amasty_Xsearch/js/widgets/analytics-data-collector',
            amsearchCollapsible: 'Amasty_Xsearch/js/widgets/collapsible',
            amsearchFullWidth: 'Amasty_Xsearch/js/widgets/full-width',
            amsearchCollectProductView: 'Amasty_Xsearch/js/widgets/handle-product-view',
            amsearchProductItemInit: 'Amasty_Xsearch/js/widgets/product-item-init',
            amsearchWidgetOverride: 'Amasty_Xsearch/js/content-type/products/appearance/carousel/widget-override.js'
        }
    },
    paths: {
        catalogAddToCart: 'Magento_Catalog/js/catalog-add-to-cart'
    },
    shim: {
        amsearchWidgetOverride: {
            deps: ['Amasty_Base/vendor/slick/slick.min']
        },
        amsearchSlick: {
            deps: ['jquery']
        }
    },
    config: {
        mixins: {
            'Magento_MultipleWishlist/js/multiple-wishlist': {
                'Amasty_Xsearch/js/mixins/multiple-wishlist': true
            },
            'Magento_Theme/js/view/breadcrumbs': {
                'Amasty_Xsearch/js/mixins/breadcrumbs': true
            }
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    config: {
        mixins: {
            'Magento_Paypal/js/view/payment/method-renderer/in-context/checkout-express': {
                'Amasty_InvisibleCaptcha/js/view/paypal/in-context/checkout-express-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    config: {
        mixins: {
            'Amasty_Conf/js/swatch-renderer': {
                'Amasty_Label/js/configurable/swatch-renderer': true
            },
            'Magento_Swatches/js/swatch-renderer': {
                'Amasty_Label/js/configurable/swatch-renderer': true
            },
            'Amasty_Conf/js/configurable': {
                'Amasty_Label/js/configurable/configurable': true
            },
            'Magento_ConfigurableProduct/js/configurable': {
                'Amasty_Label/js/configurable/configurable': true
            }
        }
    },

    map: {
        '*': {
            amInitLabelUi: 'Amasty_Label/js/uiWidget/amLoadUiLabel'
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    map: {
        '*': {
            'ammenu_helpers': 'Amasty_MegaMenuLite/js/utils/helpers',
            'ammenu_color_helper': 'Amasty_MegaMenuLite/js/utils/color'
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    map: {
        "*": {
            ammenuProductSlider: "Amasty_MegaMenu/js/widgets/product-slider",
            ammenuPager: "Amasty_MegaMenu/js/widgets/pager"
        }
    },
    config: {
        mixins: {
            'Amasty_MegaMenuLite/js/wrapper': {
                'Amasty_MegaMenu/js/wrapper': true
            }
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/summary/item/details/thumbnail': {  // Target module
                'Amasty_Promo/js/checkout/sidebar-image-update': true  // Extender module
            },
            'Magento_Checkout/js/view/summary/cart-items': {
                'Amasty_Promo/js/checkout/cart-items-counter-update': true
            },
            'Magento_Theme/js/view/messages' : {
                'Amasty_Promo/js/view/messages' : true
            },
            'Amasty_Coupons/js/model/cart/apply-response-processor': {
                'Amasty_Promo/js/model/coupon-apply-response-processor-mixin' : true
            }
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    map: {
        '*': {
            amShopbyTooltipInit: 'Amasty_ShopbyBase/js/components/am-tooltip-init'
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    map: {
        '*': {
            'amshopby_color': 'Amasty_Shopby/js/utils/color'
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    map: {
        '*': {
            amShopbyFilterAbstract: 'Amasty_Shopby/js/amShopby',
            amShopbyResponsive: 'Amasty_Shopby/js/amShopbyResponsive',
            amShopbyFilterItemDefault: 'Amasty_Shopby/js/components/amShopbyFilterItemDefault',
            amShopbyFilterDropdown: 'Amasty_Shopby/js/components/amShopbyFilterDropdown',
            amShopbyFilterFromTo: 'Amasty_Shopby/js/components/amShopbyFilterFromTo',
            amShopbyFilterHideMoreOptions: 'Amasty_Shopby/js/components/amShopbyFilterHideMoreOptions',
            amShopbyFilterAddTooltip: 'Amasty_Shopby/js/components/amShopbyFilterAddTooltip',
            amShopbyFilterCategoryDropdown: 'Amasty_Shopby/js/components/amShopbyFilterCategoryDropdown',
            amShopbyFilterCategory: 'Amasty_Shopby/js/components/amShopbyFilterCategory',
            amShopbyFilterContainer: 'Amasty_Shopby/js/components/amShopbyFilterContainer',
            amShopbyFilterSearch: 'Amasty_Shopby/js/components/amShopbyFilterSearch',
            amShopbyFilterMultiselect: 'Amasty_Shopby/js/components/amShopbyFilterMultiselect',
            amShopbyFilterSwatch: 'Amasty_Shopby/js/components/amShopbyFilterSwatch',
            amShopbyFilterSlider: 'Amasty_Shopby/js/components/amShopbyFilterSlider',
            amShopbyFilterFlyout: 'Amasty_Shopby/js/components/amShopbyFilterFlyout',
            amShopbySwiperSlider: 'Amasty_Shopby/js/components/amShopbySwiperSlider',
            amShopbySwatchTooltip: 'Amasty_Shopby/js/components/amShopbySwatchTooltip',
            amShopbyStickySidebar: 'Amasty_Shopby/js/components/amShopbyStickySidebar',
            amShopbyFilterCollapse: 'Amasty_Shopby/js/components/amShopbyFilterCollapse',
            amShopbySwatchesChoose: 'Amasty_Shopby/js/amShopbySwatchesChoose',
            amShopbyFiltersSync: 'Amasty_Shopby/js/amShopbyFiltersSync',
            amShopbyApplyFilters: 'Amasty_Shopby/js/amShopbyApplyFilters',
            amShopbyTopFilters: 'Amasty_Shopby/js/amShopbyTopFilters',
            amShopbyAjax: 'Amasty_Shopby/js/amShopbyAjax',
            amShopbyHelpers: 'Amasty_Shopby/js/utils/helpers'
        }
    },
    config: {
        mixins: {
            'jquery/ui-modules/widgets/slider': {
                'Amasty_Shopby/js/mixins/slider': true
            }
        }
    },
    paths: {
        'swiper': 'Amasty_ShopbyBase/js/swiper.min'
    },
    shim: {
        'swiper': {
            deps: [ 'jquery' ]
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    map: {
        '*': {
            amBrandsSearch: 'Amasty_ShopbyBrand/js/components/ambrands-search',
            amBrandsFilterInit: 'Amasty_ShopbyBrand/js/components/ambrands-filter-init',
            amBrandsFilter: 'Amasty_ShopbyBrand/js/brand-filter'
        }
    },
    paths: {
        'swiper': 'Amasty_ShopbyBase/js/swiper.min',
    },
    shim: {
        'swiper': {
            deps: ['jquery']
        }
    },
    config: {
        mixins: {
            'mage/menu': {
                'Amasty_ShopbyBrand/js/lib/mage/ambrands-menu-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    map: {
        "*": {
            amfancybox: 'Amasty_Storelocator/vendor/fancybox/jquery.fancybox.min',
            amLocator: 'Amasty_Storelocator/js/main',
            chosen: 'Amasty_Storelocator/vendor/chosen/chosen.min'
        }
    },
    shim: {
        'Amasty_Storelocator/vendor/fancybox/jquery.fancybox.min': ['jquery'],
        'Amasty_Storelocator/vendor/chosen/chosen.min': [ 'jquery' ],
        'Amasty_Storelocator/js/main': [ 'jquery-ui-modules/slider' ]
    }
};

require.config(config);
})();
(function() {
var config = {
    config: {
        mixins: {
            'mage/gallery/gallery': {
                'Fastly_Cdn/js/gallery/gallery-mixin': true
            },
            'Magento_Swatches/js/swatch-renderer': {
                'Fastly_Cdn/js/swatch-renderer-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Config to pull in all the relevant Braintree JS SDKs
 * @type {{paths: {braintreePayPalInContextCheckout: string, braintreePayPalCheckout: string, braintreeVenmo: string, braintreeHostedFields: string, braintreeDataCollector: string, braintreeThreeDSecure: string, braintreeGooglePay: string, braintreeApplePay: string, braintreeAch: string, braintreeLpm: string, googlePayLibrary: string}, map: {"*": {braintree: string}}}}
 */
var config = {
    map: {
        '*': {
            braintree: 'https://js.braintreegateway.com/web/3.79.1/js/client.min.js',
        }
    },

    paths: {
        "braintreePayPalCheckout": "https://js.braintreegateway.com/web/3.79.1/js/paypal-checkout.min",
        "braintreeHostedFields": "https://js.braintreegateway.com/web/3.79.1/js/hosted-fields.min",
        "braintreeDataCollector": "https://js.braintreegateway.com/web/3.79.1/js/data-collector.min",
        "braintreeThreeDSecure": "https://js.braintreegateway.com/web/3.79.1/js/three-d-secure.min",
        "braintreeApplePay": 'https://js.braintreegateway.com/web/3.79.1/js/apple-pay.min',
        "braintreeGooglePay": 'https://js.braintreegateway.com/web/3.79.1/js/google-payment.min',
        "braintreeVenmo": 'https://js.braintreegateway.com/web/3.79.1/js/venmo.min',
        "braintreeAch": "https://js.braintreegateway.com/web/3.79.1/js/us-bank-account.min",
        "braintreeLpm": "https://js.braintreegateway.com/web/3.79.1/js/local-payment.min",
        "googlePayLibrary": "https://pay.google.com/gp/p/js/pay",
        "braintreePayPalInContextCheckout": "https://www.paypalobjects.com/api/checkout"
    }
};

require.config(config);
})();
(function() {
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/step-navigator': {
                'PayPal_Braintree/js/model/step-navigator-mixin': true
            },
            'PayPal_Braintree/js/view/payment/method-renderer/cc-form': {
                'PayPal_Braintree/js/reCaptcha/braintree-cc-method-mixin': true
            }
        }
    },
    map: {
        '*': {
            braintreeCheckoutPayPalAdapter: 'PayPal_Braintree/js/view/payment/adapter'
        }
    },
};

require.config(config);
})();
(function() {
var config = {
    config: {
        mixins: {
            'Amasty_Label/js/initLabel': {
                'WolfSellers_AmastyLabel/js/initLabel-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
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

require.config(config);
})();
(function() {
var config = {
    map:{
      '*':{
          'Magento_Checkout/js/model/step-navigator':'WolfSellers_Checkout/js/model/step-navigator',
          'Magento_Checkout/js/view/form/element/email':'WolfSellers_Checkout/js/view/form/element/email',
          'Magento_Checkout/js/model/shipping-save-processor/default':'WolfSellers_Checkout/js/model/shipping-save-processor/default',
          'Magento_Checkout/js/model/shipping-save-processor/payload-extender':'WolfSellers_Checkout/js/model/shipping-save-processor/payload-extender'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/summary/shipping': {
                'WolfSellers_Checkout/js/view/summary/shipping-mixin': true
            },
            'Magento_Checkout/js/view/payment' : {
                'WolfSellers_Checkout/js/view/payment-mixin': true
            },
            'Magento_Checkout/js/view/shipping': {
                'WolfSellers_Checkout/js/view/shipping-mixin': true
            },
            'Amasty_Label/js/label': {
                'WolfSellers_Checkout/js/label': true
            },
            'Magento_Ui/js/form/element/abstract': {
                'WolfSellers_Checkout/js/view/form/element/abstract': true
            },
            'Magento_Checkout/js/view/progress-bar': {
                'WolfSellers_Checkout/js/view/progress-bar-mixin': true
            },
            'Magento_InventoryInStorePickupFrontend/js/view/store-selector': {
                'WolfSellers_Checkout/js/view/store-selector-mixin': true
            },
            'Magento_InventoryInStorePickupFrontend/js/view/store-pickup':{
                'WolfSellers_Checkout/js/view/store-pickup-mixin': true
            },
            'Magento_Checkout/js/view/payment/list': {
                'WolfSellers_Checkout/js/view/payment/list-mixin': true
            },
            'Magento_Checkout/js/view/summary/abstract-total': {
                'WolfSellers_Checkout/js/view/summary/abstract-total-mixins': true
            },
            "Magento_Tax/js/view/checkout/summary/shipping": {
                "WolfSellers_Checkout/js/view/checkout/summary/shipping-mixin" : true
            },
            'Magento_Ui/js/lib/validation/rules': {
                'WolfSellers_Checkout/js/validation/validator-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'WolfSellers_CustomSwatch/js/swatch-renderer-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    config: {
        mixins: {
            ///vendor/magento/module-checkout/view/frontend/web/js/model/shipping-save-processor/payload-extender.js
            //app/code/WolfSellers/DireccionesTiendas/view/frontend/web/js/payload-extender-mixin.js
            'Magento_Checkout/js/model/shipping-save-processor/payload-extender': {
                'WolfSellers_DireccionesTiendas/js/payload-extender-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    config: {
        mixins: {
            'Amasty_Label/js/label': {
                'WolfSellers_SkinCare/js/label-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    deps: [
        'Magento_Theme/js/theme'
    ]
};

require.config(config);
})();



})(require);