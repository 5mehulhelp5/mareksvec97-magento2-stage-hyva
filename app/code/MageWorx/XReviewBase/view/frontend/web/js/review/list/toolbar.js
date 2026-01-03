/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'jquery-ui-modules/widget',
], function ($) {
    'use strict';

    /**
     * ReviewListToolbarForm Widget - this widget is setting cookie and submitting form according to toolbar controls
     */
    $.widget('mageworx.reviewListToolbarForm', {

        options: {
            directionControl: '[data-role="direction-switcher"]',
            orderControl: '[data-role="sorter"]',
            limitControl: '[data-role="limiter"]',
            filterControl: '[data-role="filter"]',
            mediaFilterControl: '[data-role="media-filter"]',
            locationFilterControl: '[data-role="location-filter"]',
            direction: 'review_list_dir',
            order: 'review_list_order',
            limit: 'review_list_limit',
            filter: 'review_list_filter',
            mediaFilter: 'review_list_filter_media',
            locationFilter: 'review_list_filter_location',
            directionDefault: 'asc',
            orderDefault: 'position',
            limitDefault: '9',
            filterDefault: 'off',
            mediaFilterDefault: 'off',
            locationFilterDefault: 'off',
            url: '',
            formKey: '',
            post: false,
            ajax:true
        },

        /** @inheritdoc */
        _create: function () {

        },

        _init: function () {
            this._bind($(this.options.directionControl), this.options.direction, this.options.directionDefault);
            this._bind($(this.options.orderControl), this.options.order, this.options.orderDefault);
            this._bind($(this.options.filterControl), this.options.filter, this.options.filterDefault);
            this._bind($(this.options.mediaFilterControl), this.options.mediaFilter, this.options.mediaFilterDefault);
            this._bind($(this.options.locationFilterControl), this.options.locationFilter, this.options.locationFilterDefault);
        },

        /** @inheritdoc */
        _bind: function (element, paramName, defaultValue) {
            if (element.is('select')) {
                element.on('change', {
                    paramName: paramName,
                    'default': defaultValue
                }, $.proxy(this._processSelect, this));
            } else if (element.is(':checkbox')) {
                element.on('click', {
                    paramName: paramName,
                    'default': defaultValue
                }, $.proxy(this._processCheckbox, this));
            } else {
                element.on('click', {
                    paramName: paramName,
                    'default': defaultValue
                }, $.proxy(this._processLink, this));
            }
        },

        _processCheckbox: function (event) {
            this.changeUrl(
                event.data.paramName,
                $(event.currentTarget).data('value'),
                event.data.default,
                $(event.currentTarget)
            );
        },

        /**
         * @param {jQuery.Event} event
         * @private
         */
        _processLink: function (event) {
            event.preventDefault();
            this.changeUrl(
                event.data.paramName,
                $(event.currentTarget).data('value'),
                event.data.default,
                $(event.currentTarget)
            );
        },

        /**
         * @param {jQuery.Event} event
         * @private
         */
        _processSelect: function (event) {
            this.changeUrl(
                event.data.paramName,
                event.currentTarget.options[event.currentTarget.selectedIndex].value,
                event.data.default,
                $(event.currentTarget)
            );
        },

        /**
         * @param {String} paramName
         * @param {*} paramValue
         * @param {*} defaultValue
         */
        changeUrl: function (paramName, paramValue, defaultValue, element) {

            var decode = window.decodeURIComponent,
                urlPaths = this.options.url.split('?'),
                baseUrl = urlPaths[0],
                urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                paramData = {},
                parameters, i, form, params, key, input, formKey;

            for (i = 0; i < urlParams.length; i++) {
                parameters = urlParams[i].split('=');
                paramData[decode(parameters[0])] = parameters[1] !== undefined ?
                    decode(parameters[1].replace(/\+/g, '%20')) :
                    '';
            }
            paramData[paramName] = paramValue;

            if (this.options.post) {
                form = document.createElement('form');
                params = [this.options.direction, this.options.order, this.options.limit];

                for (key in paramData) {
                    if (params.indexOf(key) !== -1) { //eslint-disable-line max-depth
                        input = document.createElement('input');
                        input.name = key;
                        input.value = paramData[key];
                        form.appendChild(input);
                        delete paramData[key];
                    }
                }
                formKey = document.createElement('input');
                formKey.name = 'form_key';
                formKey.value = this.options.formKey;
                form.appendChild(formKey);

                paramData = $.param(paramData);
                baseUrl += paramData.length ? '?' + paramData : '';

                form.action = baseUrl;
                form.method = 'POST';
                document.body.appendChild(form);
                form.submit();
            } else {
                if (paramValue == defaultValue) { //eslint-disable-line eqeqeq
                    delete paramData[paramName];
                }
                paramData = $.param(paramData);

                let url = baseUrl + (paramData.length ? '?' + paramData : '');

                if (this.options.ajax) {
                    element.data('ajax_url', url);
                    //For debug:
                    //console.log('toolbar ajax URL: ' + element.data('ajax_url'));
                } else {
                    location.href = baseUrl + (paramData.length ? '?' + paramData : '');
                }
            }
        }
    });

    return $.mageworx.reviewListToolbarForm;
});
