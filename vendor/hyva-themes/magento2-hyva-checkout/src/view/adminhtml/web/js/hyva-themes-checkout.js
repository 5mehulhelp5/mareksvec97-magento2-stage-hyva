define([
    'jquery',
    'Magento_Ui/js/lib/core/class',
    'underscore',
    'jquery/ui'
], function (
    $,
    Class,
    _
) {
    'use strict';

    return Class.extend({
        initialize: function (config) {
            this.getFields(config, 'shipping_address', 'eav_attribute_form_fields');
            this.getFields(config, 'billing_address', 'eav_attribute_form_fields');
        },

        /**
         * Get the fields and call actions and set observers.
         *
         * @param object config
         * @param string name
         */
        getFields: function (config, group, field) {
            var fieldString = '#row_hyva_themes_checkout_component_' + group + '_' + field;

            this.addRow(
                fieldString + ' table td:nth-child(2) input',
                name,
                config.fields.merged,
                this,
                fieldString + ' table td:first-child input',
                fieldString + ' table tbody'
            );

            this.setReadonly(fieldString + ' table td:first-child input');

            this.setChecboxes(fieldString + ' table td:nth-child(3) input:checkbox');
            this.setChecboxes(fieldString + ' table td:nth-child(4) input:checkbox');
            this.setChecboxes(fieldString + ' table td:nth-child(5) input:checkbox');

            this.removeButton(
                fieldString + ' table td:nth-child(7) button',
                'td:nth-child(2) input',
                config.fields.merged,
                this,
                fieldString + ' table td:first-child input'
            );

            this.addObserver(
                fieldString + ' table tfoot button',
                fieldString + ' table td:first-child input',
                this
            );

            this.addSortable(
                fieldString + ' table tbody',
                fieldString + ' table td:first-child input',
                this
            );
        },

        /**
         * Based on selector set readonly on element.
         *
         * @param string selector
         */
        setReadonly: function (selector) {
            $(selector).each(function () {
                $(this).prop('readonly', true);
            });
        },

        /**
         * Based on selector set hidden elements to match real checboxes.
         *
         * @param string selector
         */
        setChecboxes: function (selector) {
            $(selector).each(function (index) {
                var el = $(this),
                    tmp = el.clone().attr('type', 'hidden').attr('value', '0');

                if (el.val() === "1") {
                    el.prop('checked', true);
                }

                el.val("1");
                tmp.insertBefore(el);
            });

        },

        /**
         * Based on selector update sort value on element.
         *
         * @param string selector
         */
        setOrder: function (selector) {
            $(selector).each(function (index) {
                $(this).val(index + 1);
            });
        },

        /**
         * Remove a button element from the table.
         *
         * @param string selector
         * @param string search
         * @param object mergedFields
         */
        removeButton: function (
            selector,
            search,
            mergedFields,
            that,
            inputSelector
        ) {
            $(selector).each(
                function () {
                    var inputElem = this.up('tr').select(search).first();

                    var visible = this.up('tr').select("td:nth-child(4) input").first();
                    if (inputElem) {
                        if (typeof mergedFields[inputElem.value.split(/[:]+/).pop()] !== 'undefined') {
                            inputElem.readOnly = true;

                            var required = this.up('tr').select("td:nth-child(3) input").each(
                                function(requiredElem) {
                                    requiredElem.readOnly = true;
                                }
                            );

                            var visible = this.up('tr').select("td:nth-child(4) input").each(
                                function(visible) {
                                    visible.readOnly = true;
                                }
                            );

                            this.remove();
                        } else {
                            $(this).bind('click', function () {
                                that.setOrder(inputSelector);
                            });
                        }
                    }
                }
            );
        },

        /**
         * attach observer functionality to "add button"
         *
         * @param string selector
         * @param string inputSelector
         * @param object that
         */
        addObserver: function (selector, inputSelector, that) {
            $(selector).first().click(function () {
                that.setReadonly(inputSelector);
                that.setOrder(inputSelector);
            });
        },

        /**
         * attach sortable functionality to table
         *
         * @param string selector
         * @param string inputSelector
         * @param object that
         */
        addSortable: function (selector, inputSelector, that) {
            $(selector).sortable({
                placeholder : 'ui-state-highlight',
                axis : 'y',
                cursor : 'move',
                cursorAt : {
                    left : 5
                },
                update : function (event, ui) {
                    that.setOrder(inputSelector);
                }

            });
        },

        /**
         * Add a row to table via in page method window[containerElemId].add.
         *
         * @param string selector
         * @param string name
         * @param object mergedFields
         */
        addRow: function (selector, name, mergedFields, that, inputSelector, rowSelector) {
            var data = $(selector),
                rowId = $(rowSelector),
                existing = {},
                containerElemId = false;

            if (!containerElemId && rowId) {
                containerElemId = rowId.attr('id');
            }

            data.each(function () {
                var value = this.value.split(/[:]+/).pop();

                if (typeof mergedFields[value] !== 'undefined') {
                    existing[value] = value;
                }
            });

            if (containerElemId) {
                containerElemId = containerElemId.replace('addRow', 'arrayRow');

                var fieldsLength   = Object.keys(mergedFields).length,
                    existingLength = Object.keys(existing).length;

                if (fieldsLength > existingLength) {
                    var i = existingLength;

                    $.each(mergedFields, function (key) {
                        var d  = new Date(),
                            id = '_' + d.getTime() + '_' + d.getMilliseconds() + '_' + i;

                        if (typeof existing[key] === 'undefined') {
                            i++;

                            window[containerElemId].add({
                                'sort_order': '' + i + '',
                                'attribute_code': '' + key + '',
                                "enabled": "1",
                                "required": "0",
                                'length': '2',
                                'auto_save': '0',
                                // 'default_value': '',
                                'tool_tip': '',
                                '_id': '' + id + '',
                                'column_values': {
                                    "' + id + '_sort_order": +i,
                                    "' + id + '_attribute_code": '' + key + '',
                                    "' + id + '_enabled": '1',
                                    "' + id + '_required": '' + mergedFields[key].required + '',
                                    "' + id + '_auto_save": '0',
                                    "' + id + '_length": '' + i + '',
                                    "' + id + '_css_class": '',
                                    // "' + id + '_default_value": ''
                                    "' + id + '_tool_tip": ''
                                }
                            });
                        }
                    });
                }

                that.setOrder(inputSelector);
            }
        }
    })
});
