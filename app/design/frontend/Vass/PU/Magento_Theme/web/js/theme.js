define([
    "jquery",
    ], function ($) {
        'use strict';

        $('.footer__accordion-column').click(function(e){
            e.preventDefault();
            $(this).toggleClass('footer__accordion');
        });
        
    });