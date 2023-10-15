define([
    'underscore',
    'uiRegistry'
], function (
    _,
    registry
) {
    'use strict';
    /**
     * TODO all string passed as parameters turn into vars or observables
     */
    return {
        /**
         * get uiComponents array by path + [uiComponent names]
         * @param path
         * @param uiComponentsRequired
         * @returns {*}
         */
        getUiComponentsArray: function (path, uiComponentsRequired) {
            return _.chain(uiComponentsRequired)
                .map(function(componentName) {
                    var component = registry.get(path + componentName);
                    return component ? [componentName, component] : null;
                })
                .compact()
                .object()
                .value();
        }
    }
});


