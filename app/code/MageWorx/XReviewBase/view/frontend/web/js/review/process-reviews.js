/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery'
], function ($) {
    'use strict';

    /**
     * @param {String} url
     * @param {*} fromPages
     */
    function processReviews(url, fromPages) {

        $.ajax({
            url: url,
            cache: true,
            dataType: 'html',
            showLoader: false,
            loaderContext: $('.product.data.items')
        }).done(function (data) {
            $('#product-review-container').html(data).trigger('contentUpdated');

            setTimeout(function () {
                $('[data-role="product-review"] .pages a').each(function (index, element) {
                    $(element).click(function (event) { //eslint-disable-line max-nested-callbacks
                        processReviews($(element).attr('href'), true);
                        event.preventDefault();
                    });
                });

                $('[data-role="product-review"] .toolbar-sorter [data-role="direction-switcher"]').each(function (index, element) {
                    $(element).click(function (event) { //eslint-disable-line max-nested-callbacks
                        processReviews($(this).data('ajax_url'), true);
                        event.preventDefault();
                    });
                });

                $('[data-role="product-review"] .toolbar-sorter [data-role="sorter"]').each(function (index, element) {
                    $(element).change(function (event) { //eslint-disable-line max-nested-callbacks
                        processReviews($(this).data('ajax_url'), true);
                        event.preventDefault();
                    });
                });

                $('[data-role="product-review"] .toolbar-filter [data-role="filter"]').each(function (index, element) {
                    $(element).click(function (event) { //eslint-disable-line max-nested-callbacks
                        processReviews($(this).data('ajax_url'), true);
                        event.preventDefault();
                    });
                });

                $('[data-role="product-review"] .toolbar-filter [data-role="media-filter"]').each(function (index, element) {
                    $(element).click(function (event) { //eslint-disable-line max-nested-callbacks
                        processReviews($(this).data('ajax_url'), true);
                        event.preventDefault();
                    });
                });

                $('[data-role="product-review"] .toolbar-filter [data-role="location-filter"]').each(function (index, element) {
                    $(element).click(function (event) { //eslint-disable-line max-nested-callbacks
                        processReviews($(this).data('ajax_url'), true);
                        event.preventDefault();
                    });
                });

            }, 200);
        }).always(function () {
            if (fromPages == true) { //eslint-disable-line eqeqeq

                let container = $('#reviews');

                if (container.length) {
                    $('html, body').animate({
                        scrollTop: container.offset().top - 50
                    }, 300);
                }
            }

            if (typeof (refreshFsLightbox) == 'function') {
                refreshFsLightbox();
            }
        });
    }

    return function (config) {

        if (config.isForceAjax) {
            processReviews(config.productReviewUrl);
        } else {
            var reviewTab = $(config.reviewsTabSelector),
                requiredReviewTabRole = 'tab';
            if (reviewTab.attr('role') === requiredReviewTabRole && reviewTab.hasClass('active')) {
                processReviews(config.productReviewUrl, location.hash === '#reviews');
            } else {
                reviewTab.one('beforeOpen', function () {
                    processReviews(config.productReviewUrl);
                });
            }
        }

        $(function () {
            $('.product-info-main .reviews-actions a').click(function (event) {
                var anchor, addReviewBlock;

                event.preventDefault();
                anchor = $(this).attr('href').replace(/^.*?(#|$)/, '');
                addReviewBlock = $('#' + anchor);

                if (addReviewBlock.length) {
                    $('.product.data.items [data-role="content"]').each(function (index) { //eslint-disable-line
                        if (this.id == 'reviews') { //eslint-disable-line eqeqeq
                            $('.product.data.items').tabs('activate', index);
                        }
                    });
                    $('html, body').animate({
                        scrollTop: addReviewBlock.offset().top - 50
                    }, 300);
                }
            });
        });
    };
});
