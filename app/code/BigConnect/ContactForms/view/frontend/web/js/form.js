define([
    "jquery",
    "domReady!",
    "magnificpopup"
], function($){
    "use strict";

    $.widget('custom.ContactFormPopup', {
        options: {
            idPopup: '.contact-form-popup-wrap',
            popupForm: '.contact-form-popup',
            showButton: '.contact-form-popup-show',
            submitButton: '.contact-form-popup .actions-toolbar button'
        },

        _create: function() {
            var self = this;

			$(document.body).on('click', this.options.showButton, function (e) {
				e.stopImmediatePropagation();
				e.preventDefault();
				e.stopPropagation();

				var button = $(this);
				button.off('click');
				button.prop('disabled', true);
				
				if ($.magnificPopup.instance.isOpen) {
					$.magnificPopup.close();
				}
				
				console.log($(self.options.idPopup).attr('id'));
				
				$.magnificPopup.open({
					items: {
						src: self.options.idPopup,
						type: 'inline'
					},
					overflowY: 'auto',
					fixedContentPos: false,
					removalDelay: 300,
					mainClass: 'mfp-zoom-in',
					callbacks: {
						open: function () {
							if (this.fixedContentPos) {
								if (this._hasScrollBar(this.wH)) {
									var s = this._getScrollbarSize();
									if (s) {
										$('.sticky-menu.active').css('padding-right', s);
										$('#go-top').css('margin-right', s);
									}
								}
							}
						},
						close: function () {
							$('.sticky-menu.active').css('padding-right', '');
							$('#go-top').css('margin-right', '');
							button.prop('disabled', false);
						}
					}
				});
				
            });

			$(document.body).on('submit', this.options.popupForm, function (e) {
				e.stopImmediatePropagation();
				e.preventDefault();
				e.stopPropagation();
				
                var form = $(this);
                var data = new FormData(this);
				// for(var pair of data.entries()) {
					// console.log(pair[0]+ ', '+ pair[1]);
				// }

				$.ajax({
					timeout: 5000,
					type: "POST",
					url: form.data('action'),
					data: data,
					contentType: false,
					processData: false,
					success: function (res) {
						// console.log(res);
						form.replaceWith('<h2 class="contact-title">&nbsp;</h2><h2 class="newsletter-content text-center">'+res.message+'</h2>');
						setTimeout(function () {
							if ($.magnificPopup.instance.isOpen) {
								$.magnificPopup.close();
							}
						}, 5000);
					},
					error: function(jqXHR, textStatus, errorThrown) {

					}
				});
            });

        }
    });

    return $.custom.ContactFormPopup;
});
