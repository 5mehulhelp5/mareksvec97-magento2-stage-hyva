define(['jquery'], function ($) {
    'use strict';

    function parseCsv(value) {
        if (!value) {
            return [];
        }
        return value
            .split(',')
            .map(function (item) {
                return parseInt(item, 10);
            })
            .filter(function (item) {
                return !isNaN(item) && item > 0;
            });
    }

    function unique(values) {
        var seen = {};
        return values.filter(function (value) {
            if (seen[value]) {
                return false;
            }
            seen[value] = true;
            return true;
        });
    }

    function renderTree(nodes, selected, onChange) {
        var $list = $('<ul class="category-tree-chooser__list"></ul>');

        nodes.forEach(function (node) {
            var $item = $('<li class="category-tree-chooser__item"></li>');
            var $row = $('<div class="category-tree-chooser__row"></div>');
            var hasChildren = node.children && node.children.length > 0;
            var $children = null;

            if (hasChildren) {
                var $toggle = $('<button type="button" class="category-tree-chooser__toggle" aria-expanded="false">+</button>');
                $toggle.on('click', function () {
                    var expanded = $toggle.attr('aria-expanded') === 'true';
                    $toggle.attr('aria-expanded', (!expanded).toString());
                    $toggle.text(expanded ? '+' : '−');
                    $children.toggle(!expanded);
                });
                $row.append($toggle);
            } else {
                $row.append('<span class="category-tree-chooser__toggle-spacer"></span>');
            }

            var $checkbox = $('<input type="checkbox" class="category-tree-chooser__checkbox" />');
            $checkbox.val(node.id);
            if (selected.indexOf(node.id) !== -1) {
                $checkbox.prop('checked', true);
            }
            $checkbox.on('change', function () {
                var id = parseInt($checkbox.val(), 10);
                onChange(id, $checkbox.is(':checked'));
            });

            var $label = $('<label class="category-tree-chooser__label"></label>');
            $label.append($checkbox).append($('<span></span>').text(node.text));

            $row.append($label);
            $item.append($row);

            if (hasChildren) {
                $children = renderTree(node.children, selected, onChange).hide();
                $item.append($children);
            }

            $list.append($item);
        });

        return $list;
    }

    return function (config, element) {
        var $root = $(element);
        var $input = $(config.inputSelector);
        var $count = $(config.countSelector);
        var $clear = $(config.clearSelector);
        var $treeContainer = $root.find('.category-tree-chooser__tree');

        var selected = parseCsv($input.val());

        function updateSelected(id, isChecked) {
            if (isChecked) {
                if (selected.indexOf(id) === -1) {
                    selected.push(id);
                }
            } else {
                selected = selected.filter(function (value) {
                    return value !== id;
                });
            }
            selected = unique(selected);
            $input.val(selected.join(','));
            $count.text('Selected: ' + selected.length);
        }

        $count.text('Selected: ' + selected.length);

        $clear.on('click', function () {
            selected = [];
            $input.val('');
            $treeContainer.find('input[type="checkbox"]').prop('checked', false);
            $count.text('Selected: 0');
        });

        $.getJSON(config.endpointUrl)
            .done(function (data) {
                $treeContainer.empty();
                $treeContainer.append(renderTree(data, selected, updateSelected));
                $input.val(selected.join(','));
                $count.text('Selected: ' + selected.length);
            })
            .fail(function () {
                $treeContainer.html('<p>Unable to load categories.</p>');
            });
    };
});
