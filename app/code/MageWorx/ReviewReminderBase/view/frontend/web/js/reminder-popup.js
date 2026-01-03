/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'mage/cookies'
], function ($, _,) {
    'use strict';

    /**
     * CampaignPopup constructor.
     *
     * @param config
     * @param element
     */
    var ReminderPopup = function (config, element) {
        this.config = config;
        this.element = element;
        this.stopProcess = false;

        this.init = function () {

            if (!$.cookie('mageworx_review_reminder_popup')) {
                this.updateContent();
            }

            return this;
        };

        this.updateContent = function () {
            var self = this;

            $.ajax({
                url: this.config.ajaxUrl,
                method: 'POST',
                data: {
                    email: $.cookie('mageworx_review_reminder_previous_email')
                },
                success: function (result) {
                    self._processSuccess(result);
                },
            });
        };

        this._processSuccess = function (result) {
            if (result.success) {
                $(this.element).html(result.content).show();
                $.cookie('mageworx_review_reminder_popup', Math.floor(Date.now() / 1000), {path: '/'});
            }
        };

        this.show = function () {
            $(this.element).show();
        };

        this.hide = function () {
            $(this.element).hide();
        };

        this.init();
    };

    return {
        'reminder-popup': ReminderPopup
    }
});
