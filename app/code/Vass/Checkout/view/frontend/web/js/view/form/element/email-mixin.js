/**
 * @copyright Copyright (c) 2024 Vass
 * @package Vass_Checkout
 * @author Vass Team
 */

define([
    'jquery'
], function ($) {
    'use strict';

    return function (Component) {
        return Component.extend({

            showPassword: function (element) {
                let input = $('#customer-password');

                if (input.attr('type') === 'text') input.attr('type', 'password');
                else input.attr('type', 'text');
            },
        });
    };
});
