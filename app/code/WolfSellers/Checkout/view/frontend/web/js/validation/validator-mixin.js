define([
    'mage/translate',
    'Magento_Ui/js/lib/validation/utils'
], function($t, utils) {
    'use strict';
    return function(rules) {
        /**
         * Overrides a validate-alphanum-with-spaces to allow accents
         * @type {{handler: (function(*): boolean), message}}
         */
        rules['validate-alphanum-with-spaces'] = {
            handler: function (v) {
                return /^[a-zA-Z0-9áéíóúüñ\s./-]+$/.test(v);
            },
            message: $t('Solo se permiten letras, números y espacios.')
        };
        /**
         * Overrides a validate-number to avoid spaces
         * @type {{handler: (function(*): *), message}}
         */
        rules['validate-number'] = {
            handler: function (v) {
                return utils.isEmptyNoTrim(v) ||
                    !isNaN(utils.parseNumber(v)) &&
                    /^\d{8,12}$/.test(v);
            },
            message: $t('Please enter a valid number in this field.')
        };
        return rules;
    };
});


