define([
    'jquery',
    'domReady!',
    'Magento_PageBuilder/js/resource/slick/slick'
], function ($) {
    'use strict';

    if (getComputedStyle(document.body).getPropertyValue('--header-panel-slideout')) {
        $('.panel.header')
            .clone()
            .removeClass('header panel')
            .addClass('mobile-header-panel')
            .data('breeze-temporary', true)
            .appendTo($('.navigation-wrapper'));
    }
    $('.homepage-slider-slick').slick({
           dots: true,
           infinite: true,
           arrows: false,
           speed: 500,
           slidesToShow: 1,
           adaptiveHeight: true,
           autoplay: true,
           autoplaySpeed: 4000
       });
});
