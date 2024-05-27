define([
    'jquery'
], function ($) {
    "use strict";
    return function () {
        $("#dob").addClass("validate-age")
        $.validator.addMethod(
            'validate-age',
            function (value) {

                const AGE_MIN = 18;
                var date = value.split('/').reverse().join('/');
                var today = new Date();
                var birthday = new Date(date);
                var age = today.getFullYear() - birthday.getFullYear();
                var m = today.getMonth() - birthday.getMonth();

                if (m < 0 || (m === 0 && today.getDate() < birthday.getDate())) {
                    age--;
                }

                if(age >= AGE_MIN){
                    return true
                }else{
                    return false;
                }
            },
            $.mage.__('You must be at least 18 years old')
        );
        /**
         * Overrides a validate-alpha to allow accents
         * @type {{handler: (function(*): boolean), message}}
         */
        $.validator.addMethod(
            'validate-alpha',
            function (value) {
                return /^[a-zA-Z0-9áéíóúüñ\s./-]+$/.test(value);
            },
            $.mage.__('Only letters, numbers and spaces are allowed.')
        );
        /**
         * Add a custom validation method to allow text, whitespaces and accents
         * @type { Object}
         */
        $.validator.addMethod(
            'validate-text-with-spaces',
            function (value) {
                return /^[a-zA-ZáéíóúüñÁÉÍÓÚÜÑ\s./-]+$/.test(value);
            },
            $.mage.__('Only letters and spaces are allowed.')
        );
    }
});
