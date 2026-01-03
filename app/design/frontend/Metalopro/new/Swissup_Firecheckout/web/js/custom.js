define([
    'jquery',
    'Swissup_Firecheckout/js/utils/move',
    'Swissup_Firecheckout/js/utils/form-field/manager',
    'mage/translate'
], function(
    $,
    move,
    manager,
    $t
) {
    'use strict';

    /**
     * Move custom fields (like Order Comment etc.) before Place Order button
     */
    move('.um-ordercomment').before('.opc-block-summary > .place-order', 0);

    /**
     * Rename 'Street Address: Line 1' to 'Street Address'
     */
    manager('[name="street[0]"]', {
        label: $t('Street Address')
    });


});
