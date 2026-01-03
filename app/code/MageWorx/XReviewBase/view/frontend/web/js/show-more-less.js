define(
    ["jquery"],
    function ($) {
        'use strict';

        return function(config, element) {
            let button = {
                el: $("<a>", {
                    href: "#"
                }),
                show_less_label: config.show_less_label,
                show_more_label: config.show_more_label
            };

            let text = $(element).text().trim();

            let targetElement = {
                el: $(element),
                length: text.length,
                characters_limit: config.characters_limit,
                origin_text: text,
                cropped_text: text.slice(0, config.characters_limit) + '...',
                collapsedClassName: "collapsed"
            };

            if (targetElement.length > targetElement.characters_limit) {
                button.el.text(button.show_more_label);

                let container = $("<div>", {
                    class: "mw-xreview-toggle-more-less"
                });
                container.append(button.el);

                targetElement.el
                    .text(targetElement.cropped_text)
                    .addClass(targetElement.collapsedClassName)
                    .after(container);
            }

            button.el.on("click", function (e) {
                e.preventDefault();

                if (targetElement.el.hasClass(targetElement.collapsedClassName)) {
                    button.el.text(button.show_less_label);
                    targetElement.el.removeClass(targetElement.collapsedClassName);
                    targetElement.el.text(targetElement.origin_text);
                } else {
                    button.el.text(button.show_more_label);
                    targetElement.el.addClass(targetElement.collapsedClassName);
                    targetElement.el.text(targetElement.cropped_text);
                }
            });
        };
    }
);
