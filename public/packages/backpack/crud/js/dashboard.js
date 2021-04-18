/*
* Dashboard Crud
*/

jQuery(function ($) {
    'use strict';

    $('.chart-interval-switcher > button').click(function () {
        window.location.replace($(this).data('url'))
    });

    $('.menu-tour-switcher button.dropdown-item').click(function () {
        $('.menu-tour-switcher button.dropdown-toggle').text($(this).text());

        window.location.replace($(this).data('url'))
    });
});
