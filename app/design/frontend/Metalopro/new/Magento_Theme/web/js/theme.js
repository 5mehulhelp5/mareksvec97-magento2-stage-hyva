/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/smart-keyboard-handler',
    'mage/mage',
    'Magento_PageBuilder/js/resource/slick/slick',
    'domReady!'
], function ($, keyboardHandler) {
    'use strict';

    // Initialize Slick Slider for homepage banners
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

    $('.cart-summary').mage('sticky', {
        container: '#maincontent'
    });

    $('.panel.header > .header.links').clone().appendTo('#store\\.links');
    $('#store\\.links li a').each(function () {
        var id = $(this).attr('id');

        if (id !== undefined) {
            $(this).attr('id', id + '_mobile');
        }
    });
    keyboardHandler.apply();

    $(document).on('click', '.custom-close-btn', function() {
            $.magnificPopup.close();
        });
    
    $('.play-button').on('click', function() {
        var video = $('#youtube-video');
        var src = video.attr('src');

        // Skrytie obalového obrázku a tlačidla prehrávania
        $('.video-thumbnail').hide(); // Toto skryje obalový obrázok spolu s tlačidlom prehrávania

        // Zobrazenie a automatické prehrávanie videa
        video.show();
        if (src.indexOf('?') > -1) {
            video.attr('src', src + '&autoplay=1');
        } else {
            video.attr('src', src + '?autoplay=1');
        }
    });

    var addQtyButtons = function(el) {
        
        $(el).each(function(idx) {

            // var id;

            // if ($(el).data('qty-id')) {
                // id = $(el).data('qty-id');
            // } else {
                // id = Math.random().toString(16).slice(2);
                // $(el).attr('data-qty-id', id);
            // }
            
            // var $el = $('[data-qty-id="'+id+'"]');
            
            var $el = $(this),
                $input = $el.find('input');
            $el.prepend('<button class="quantity__button quantity__button_minus" type="button">-</button>');
            $el.append('<button class="quantity__button quantity__button_plus" type="button">+</button>');
            var $buttons = $el.find('.quantity__button');
            
            disable($el);

            $input.on('change', {el: $el}, disable);
            
            $buttons.on('click', function() {
                var qty = parseInt($input.val());
                if ($(this).hasClass('quantity__button_minus')) {
                    $input.val(qty - 1).trigger('input');
                } else {
                    $input.val(qty + 1).trigger('input');
                }
                
                if ( $('body').hasClass('checkout-cart-index') && $el.parents('form').is("#form-validate") ) {
                    
                    $el.parents('td.qty').find('button.update-cart-item').show();
                    /*
                    
                    require([
                        'Magento_Checkout/js/action/get-totals',
                        'Magento_Customer/js/customer-data'
                    ], function(getTotalsAction, customerData){
                        
                        var form = $('form#form-validate');
                        $.ajax({
                            url: form.attr('action'),
                            data: form.serialize(),
                            showLoader: true,
                            success: function (res) {
                                var parsedResponse = $.parseHTML(res);
                                var result = $(parsedResponse).find("#form-validate");
                                var sections = ['cart'];

                                $("#form-validate").replaceWith(result);

                                // The mini cart reloading
                                customerData.reload(sections, true);

                                // The totals summary block reloading
                                var deferred = $.Deferred();
                                getTotalsAction([], deferred);
                                
                                addQtyButtons(el);
                            },
                            error: function (xhr, status, error) {
                                var err = eval("(" + xhr.responseText + ")");
                                console.log(err.Message);
                            }
                        });
                        
                    });
                    
                    */

                }
                
                disable($el);
            });
        });

        function disable($el) {         
            if ($el.data.el) {
                $el = $el.data.el;
            }
            
            if ($el.find('input').val() < 2) {
                $el.find('.quantity__button.quantity__button_minus').attr('disabled', 'disabled');
                $el.find('input').val(1).change();
            } else {
                $el.find('.quantity__button.quantity__button_minus').attr('disabled', false);
            }
        }
    }
    
    // $('input[name="qty"]').
    addQtyButtons('.field.qty .control');
    // addQtyButtons('.product-item-pricing .details-qty');
    
    $(document.body).on('click', '.product-item-pricing .quantity__button', function (e) {  
        var $input = $(this).siblings('.cart-item-qty'),
            $update = $(this).siblings('.update-cart-item');
        
        var qty = parseInt($input.val());
        // console.log(qty);
        
        if ($(this).hasClass('quantity__button_minus')) {
            $input.val(qty - 1).change();
        } else {
            $input.val(qty + 1).change();
        }
        
        if ($input.val() < 2) {
            $input.parents('.product-item-pricing').find('.quantity__button.quantity__button_minus').attr('disabled', 'disabled');
            $input.val(1).change();
        } else {
            $input.parents('.product-item-pricing').find('.quantity__button.quantity__button_minus').attr('disabled', false);
        }
        
        // $update.trigger('click').hide();
    });

    keyboardHandler.apply();   

}


);
