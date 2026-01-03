require([
    'jquery',
    'mage/validation',
    'mage/translate',
    'Magento_Catalog/js/price-utils',
    'jquery/ui',
    'noUiSlider',
], function($, validation, $t, priceUtils, ui, noUiSlider){
    $(document).ready(function() {

        if ($('.bigconnect-configurator-main').length === 0) {
                    return; // nechaj natívny Magento behavior
                }
        /*slider set valua*/
        $('.range-slider input.product-custom-option').change(function() {
            var slider_id = $(this).attr('name');
            var value = $(this).val();
            slider_id = slider_id.replace("options[", "");
            slider_id = slider_id.replace("]", "");
            // if(value.length > 0){

            /*prvotna kontrola ci zadal sprany formac*/
            var slider_element = $('.range-slider-id-' + slider_id);
            var minVal = parseInt($('.range-slider-id-' + slider_id ).attr('min'));
            var maxVal = parseInt($('.range-slider-id-' + slider_id ).attr('max'));
            var rate   = parseFloat(slider_element.data('option-rate'));
            var $input = $('#options_' + slider_id + '_text')
            var inputValue = $input.val().trim();
            var isNumeric = /^-?\d+(\.\d+)?$/.test(inputValue); // Regular expression to check if the input is a valid number
            inputValue = parseFloat(inputValue);

            if (!isNumeric) {
                $input.val(minVal);
                $input.next('.mage-error').remove();
                $input.after('<div class="mage-error" generated="true">' + $t('Only numbers are allowed') + '</div>');
                $('.range-slider-id-' + slider_id + ' .mage-error').delay(3000).hide('slow');
            } else {
                if (inputValue < parseFloat(minVal)) {
                    $input.val(minVal);
                    var slider = document.getElementById('slider-'+slider_id);
                    slider.noUiSlider.set(minVal);
                    $input.next('.mage-error').remove();
                    $input.after('<div class="mage-error" generated="true">' + $t('Min. value is %1').replace('%1', minVal) + '</div>');
                    $('.range-slider-id-' + slider_id + ' .mage-error').delay(3000).hide('slow');

                } else if (inputValue > parseFloat(maxVal)) {
                    $input.val(maxVal);
                    var slider = document.getElementById('slider-'+slider_id);
                    slider.noUiSlider.set(maxVal);
                    $input.next('.mage-error').remove();
                    $input.after('<div class="mage-error" generated="true">' + $t('Max. value is %1').replace('%1', maxVal) + '</div>');
                    $('.range-slider-id-' + slider_id + ' .mage-error').delay(3000).hide('slow');
                    // return false;
                } else {
                    // $input.next('.mage-error').remove();
                }
            }
            /*koniec prvotna kontrola ci zadal sprany formac*/

            var slider = document.getElementById('slider-'+slider_id);
            slider.noUiSlider.set(value);

            setTimeout(function() {
                updatePrice();
            }, 20);
        });

       // function validateInput($input, minVal, maxVal) {
       //     var inputValue = $input.val().trim();
       //     var isNumeric = /^-?\d+(\.\d+)?$/.test(inputValue); // Regular expression to check if the input is a valid number
       //     inputValue = parseFloat(inputValue);
       //
       //     setTimeout(function() {
       //         if (!isNumeric) {
       //             $input.val(minVal);
       //             $input.next('.mage-error').remove();
       //             $input.after('<div class="mage-error" generated="true">' + $t('Povolené sú iba čísla') + '</div>');
       //         } else {
       //
       //             if (inputValue < parseFloat(minVal)) {
       //                 $input.val(minVal);
       //                 $input.next('.mage-error').remove();
       //                 $input.after('<div class="mage-error" generated="true">' + $t('Minimálna hodnota je %1').replace('%1', minVal) + '</div>');
       //                 // return false;
       //             } else if (inputValue > parseFloat(maxVal)) {
       //                 $input.val(maxVal);
       //                 $input.next('.mage-error').remove();
       //                 $input.after('<div class="mage-error" generated="true">' + $t('Maximálna hodnota je %1').replace('%1', maxVal) + '</div>');
       //                 // return false;
       //             } else {
       //                 $input.next('.mage-error').remove();
       //             }
       //         }
       //     }, 20);
       // }


       function initCustomOptions() {
           $('.range-slider input.product-custom-option').off('change').on('change', function () {
               // tvoja pôvodná logika zo slider change
               updatePrice();
           });

           $('.product-custom-option').off('click').on('click', function () {
               updatePrice();
           });

           // Inicializuj noUiSlider opäť – najprv ho znič (ak existuje)
           $('.product-price-slider').each(function () {
               const el = document.getElementById('slider-' + $(this).attr('option_id'));
               if (el && el.noUiSlider) {
                   el.noUiSlider.destroy();
               }

               // opäť vytvor noUiSlider
               // ... tvoje nastavenia ako predtým ...
           });
       }

        function updatePrice() {
            var $priceBox = $('[data-role="priceBox"]');
            var basePrice = parseFloat($priceBox.find('.price-final_price .price-wrapper').attr('data-price-amount'));

            var optionsPrice = $('.product-options-wrapper .field .control .field.choice input.product-custom-option:checked').toArray().reduce(function(acc, elem) {
                return acc + parseFloat($(elem).attr('price'));
            }, 0);

            basePrice += optionsPrice;

            var calculatePriceForSlider = function(sliderClass) {
                var inputValue = parseFloat($('.' + sliderClass).find('input.input-text.product-custom-option').val()) - parseFloat($('.' + sliderClass).attr('min'));
                var priceFactor = parseFloat($('.' + sliderClass).data('option-price'));
                return inputValue * priceFactor;
            };

            var newPrice = basePrice
            var sliders = ['slider-vyska', 'slider-sirka', 'slider-dlzka'];
            var sliders_price = 0;
            $.each(sliders, function(index, sliderClass) {
                var sliderElement = $('.catalog-product-view .range-slider.' + sliderClass);
                if(sliderElement.length > 0){
                    var localRate = parseFloat(sliderElement.data('option-rate'));
                    sliders_price += calculatePriceForSlider(sliderClass) * localRate;
                }
            });


            newPrice = newPrice + sliders_price;

            var formattedPrice = priceUtils.formatPrice(newPrice);
            if($('.product-info-main .price-final_price .special-price').length > 0){
                $('.product-info-main .price-final_price .special-price .price-wrapper .price').text(formattedPrice);
            } else {
                $('.product-info-main .price-final_price .price-wrapper .price').text(formattedPrice);
            }

            if($('.product-info-main .old-price span[data-price-type="oldPrice"]').length){
                var baseOldPrice = parseFloat($('.old-price span[data-price-type="oldPrice"]').attr('data-price-amount'));
                baseOldPrice = baseOldPrice + optionsPrice + sliders_price;
                var baseOldPrice = priceUtils.formatPrice(baseOldPrice);
                $('.product-info-main .old-price span[data-price-type="oldPrice"] span.price').text(baseOldPrice);
            }

        }

        // var sliderAttributes = [
        //     { class: 'slider-vyska', dataAttrMin: 'option-vyska-min', dataAttrMax: 'option-vyska-max', dataAttrPrice: 'option-price' },
        //     { class: 'slider-sirka', dataAttrMin: 'option-sirka-min', dataAttrMax: 'option-sirka-max', dataAttrPrice: 'option-price' },
        //     { class: 'slider-dlzka', dataAttrMin: 'option-dlzka-min', dataAttrMax: 'option-dlzka-max', dataAttrPrice: 'option-price' }
        // ];
        //
        // $.each(sliderAttributes, function(index, item) {
        //     $('.' + item.class).each(function() {
        //         var minVal = parseFloat($(this).data(item.dataAttrMin));
        //         var maxVal = parseFloat($(this).data(item.dataAttrMax));
        //         var $input = $(this).find('input.input-text.product-custom-option');
        //         $input.val(minVal);
        //         $input.on('change', function() {
        //             validateInput($(this), minVal, maxVal);
        //             // updatePrice();
        //         });
        //
        //     });
        // });

        // var observer = new MutationObserver(updatePrice);
        // observer.observe(document.querySelector('.price-final_price .price-wrapper .price'), { childList: true, subtree: true });

        // $('.custom-product-configurator.catalog-product-view .product-price-slider').each(function(i) {
        $('.catalog-product-view .range-slider .product-price-slider').each(function(i) {
            var slider_id = $(this).attr('option_id');
            var slider_min = parseInt($('.range-slider-id-' + slider_id ).attr('min'));
            var slider_max = parseInt($('.range-slider-id-' + slider_id ).attr('max'));

            var slider = document.getElementById('slider-'+slider_id);

            decimals = 0;
            numberFormat = {
                to: function (value) {
                    return value.toFixed(decimals);
                },
                from: function (value) {
                    return Number(value);
                }
            };

            noUiSlider.create(slider, {
                start: slider_min,
                value: slider_min,
                // connect: true,
                connect: "lower",
                step: 1,
                range: {
                    'min': slider_min,
                    'max': slider_max
                },
                format: numberFormat
            });

            slider.noUiSlider.on('update', function( values, handle ) {

                var option_id = this.target.getAttribute('option_id');
                $('#options_' + option_id + '_text').val(values[handle]);
                // $('.range-slider .mage-error').remove();

                // var minVal = parseInt($('.range-slider-id-' + option_id ).attr('min'));
                // var maxVal = parseInt($('.range-slider-id-' + option_id ).attr('max'));
                // validateInput($('#options_' + option_id + '_text'), minVal, maxVal);

                /*prvotna kontrola*/
                // var minVal = parseInt($('.range-slider-id-' + slider_id ).attr('min'));
                // var maxVal = parseInt($('.range-slider-id-' + slider_id ).attr('max'));
                // var $input = $('#options_' + slider_id + '_text')
                // var inputValue = $input.val().trim();
                // var isNumeric = /^-?\d+(\.\d+)?$/.test(inputValue); // Regular expression to check if the input is a valid number
                // inputValue = parseFloat(inputValue);
                //
                // if (inputValue < parseFloat(minVal)) {
                //     $input.val(minVal);
                //     $input.next('.mage-error').remove();
                //     $input.after('<div class="mage-error" generated="true">' + $t('Minimálna hodnota je %1').replace('%1', minVal) + '</div>');
                //     return false;
                // } else if (inputValue > parseFloat(maxVal)) {
                //     $input.val(maxVal);
                //     $input.next('.mage-error').remove();
                //     $input.after('<div class="mage-error" generated="true">' + $t('Maximálna hodnota je %1').replace('%1', maxVal) + '</div>');
                //     console.log('max 2');
                //     // updatePrqice();
                //     return false;
                // } else {
                //     $input.next('.mage-error').remove();
                // }
                /*koniec prvotna kontrola ci zadal sprany formac*/

                updatePrice();

            });
        });


        // $('.custom-product-configurator.catalog-product-view input.product-custom-option').click(function() {
        $('.catalog-product-view input.product-custom-option').click(function() {
            if ($('.catalog-product-view .range-slider').length > 0) {

                setTimeout(function() {
                    updatePrice();
                }, 20);
            }
        });

        /*fix pre textove inputy*/
        $('.catalog-product-view input.product-custom-option').change(function() {
            if(parseFloat($(this).attr('price')) == 0) {
                setTimeout(function () {
                    updatePrice();
                }, 20);
            }
        });

        $('body').on('click', '.swatch-option', function () {
                    setTimeout(function () {
                        if ($('.bigconnect-configurator-main').length > 0) {
                            initCustomOptions(); // znova napoj všetko
                            updatePrice();
                        }
                    }, 300);
                });


    });
});