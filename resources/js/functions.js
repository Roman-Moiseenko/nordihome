import jQuery from 'jquery';
window.$ = jQuery;
const dayjs = require('dayjs');
/*
function _updateTomSelect(id_tom_select) {
    window.$("#" + id_tom_select).each(function () {
        let options = {
            plugins: {
                dropdown_input: {},
            },
        };

        if (window.$(this).data("placeholder")) {
            options.placeholder = window.$(this).data("placeholder");
        }

        if (window.$(this).attr("multiple") !== undefined) {
            options = {
                ...options,
                plugins: {
                    ...options.plugins,
                    remove_button: {
                        title: "Удалить элемент",
                    },
                },
                persist: false,
                create: true,
                onDelete: function (values) {
                    return confirm(
                        values.length > 1
                            ? "Вы уверены, что хотите удалить эти " +
                            values.length +
                            " элементы?"
                            : 'Будет удален элемент под id="' +
                            values[0] +
                            '"?'
                    );
                },
            };
        }

        if (window.$(this).data("header")) {
            options = {
                ...options,
                plugins: {
                    ...options.plugins,
                    dropdown_header: {
                        title: window.$(this).data("header"),
                    },
                },
            };
        }
        new window.TomSelect(this, options);
    });
}

window._updateTomSelect = _updateTomSelect;*/
