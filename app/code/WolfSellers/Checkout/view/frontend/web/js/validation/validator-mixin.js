define(['mage/translate'], function($t) {
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
        return rules;
    };
});
