define([
    'jquery'
], function ($) {

    return function(config) {
        if ($.cookie('webp')) {
            return true;
        }

        var Tester = new Image();
        Tester.onload = function () {
            if (Tester.width > 0 && Tester.height > 0) {
                document.cookie = 'webp=1';
            }
        };

        Tester.src = config.image;
    };
});
