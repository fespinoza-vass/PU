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
    }
});
