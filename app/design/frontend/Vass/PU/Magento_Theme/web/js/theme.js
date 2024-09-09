/**
 * @copyright Copyright (c) 2024 VASS
 * @author VASS Team
 */

define([
    "jquery",
], function ($) {
    'use strict';

    $(document).ready(function () {
        $(document).on('click', '.footer__accordion-column', function (e) {
            $(this).toggleClass('active');
        });
    });
});
