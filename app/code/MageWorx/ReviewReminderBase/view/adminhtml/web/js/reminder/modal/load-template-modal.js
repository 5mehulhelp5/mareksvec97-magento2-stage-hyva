/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'uiRegistry',
    'underscore',
    'Magento_Ui/js/modal/modal-component',
    'wysiwygAdapter',
    'jquery'
], function (registry, _, ModalComponent, wysiwyg, $) {
    'use strict';

    return ModalComponent.extend({
        defaults: {
            getTemplateHtmlUrl: ''
        },

        /**
         * Load Template
         */
        loadTemplate: function () {

            var templateObj = registry.get('index = ' + this.indexies.template);
            var contentObj  = registry.get('index = ' + this.indexies.content);
            var identifier  = templateObj.value();

            if (identifier) {
                var self = this;

                $.ajax({
                    url: this.getTemplateHtmlUrl,
                    showLoader: true,
                    data: {form_key: window.FORM_KEY, 'identifier': identifier},
                    type: "POST",
                    dataType : 'json',
                    success: function(result) {
                        var templateHtml = result.content;

                        if (!_.isUndefined(templateHtml)) {
                            var wysiwygElement = wysiwyg.get(contentObj.wysiwygId);

                            if (wysiwygElement) {
                                wysiwyg.id = contentObj.wysiwygId;

                                if (_.isUndefined(wysiwyg.config)) {
                                    wysiwyg.config = {
                                        add_directives: contentObj.wysiwygConfigData.add_directives
                                    }
                                } else {
                                    wysiwyg.config.add_directives = contentObj.wysiwygConfigData.add_directives
                                }

                                wysiwyg.setContent(templateHtml);
                                wysiwyg.onChangeContent();
                            } else {
                                contentObj.value(templateHtml);
                            }

                            self.closeModal();
                        } else if (!_.isUndefined(result.error)) {
                            alert(result.error);
                        }
                    }
                });
            }
        },
    });
});
