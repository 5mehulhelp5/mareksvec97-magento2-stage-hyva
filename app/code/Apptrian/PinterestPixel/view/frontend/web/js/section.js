/**
 * @category  Apptrian
 * @package   Apptrian_PinterestPixel
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */

define([
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function (Component, customerData) {
    'use strict';

    return Component.extend({
        initialize: function () {
            this._super();
            this.apptrian_pinterest_pixel_matching_section = customerData.get('apptrian_pinterest_pixel_matching_section');
        }
    });
});
