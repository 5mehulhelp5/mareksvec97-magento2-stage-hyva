require([
	'jquery',
	'jquery-ui-modules/tooltip',
	'domReady!'
], function ($) {
	'use strict';

	$('.timer-inner').tooltip({
		// classes: {
			// "ui-tooltip": "cart-summary"
		// },
		tooltipClass: "timer-tooltip",
		position: { my: "left+15 center+15", at: "right center" }
	});
});