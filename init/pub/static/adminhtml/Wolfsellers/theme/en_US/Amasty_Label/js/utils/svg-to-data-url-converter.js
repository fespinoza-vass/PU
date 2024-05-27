define([], function () {
    'use strict';

    /**
     * @param {string} svgContent,
     * @returns {string}
     */
    function escapeSvg(svgContent) {
        // eslint-disable-next-line
        svgContent = svgContent.replace(/"/g, '\'');
        svgContent = svgContent.replace(/>\s{1,}</g, '><');
        svgContent = svgContent.replace(/\s{2,}/g, ' ');

        return svgContent.replace(/[\r\n%#()<>?[\\\]^`{|}]/g, encodeURIComponent);
    }

    return function (svgContent) {
        return 'data:image/svg+xml,' + escapeSvg(svgContent);
    };
});
