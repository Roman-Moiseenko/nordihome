//const path = require('path')

module.exports = {
    // другие параметры конфигурации Webpack
    resolve: {
        alias: {
            '@': '/resources/js',
            '@Page': '/resources/js/Pages',
            '@Comp': '/resources/js/VueComponents',
            // при необходимости можно добавить больше алиасов для других модулей
        },
    },
};
