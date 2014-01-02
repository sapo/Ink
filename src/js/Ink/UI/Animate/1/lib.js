Ink.createModule('Ink.UI.Animate', 1, ['Ink.Dom.Css_1'], function (Css) {
    function cssDurationToMs(duration) {
        duration = duration
            .replace(/ms$/, '');

        if (/s$/.test(duration)) {
            return parseFloat(duration) * 1000;
        }

        return parseFloat(duration);
    }

    function isNumber(n) {
        return typeof n === 'number' && !isNaN(n);
    }

    var Animate = {
        animate: function (element, animation, options) {
            if (arguments.length === 2) {
                options = animation;
                animation = options.animation
            }

            Css.addClassName(element, ['animate', animation || 'fadeOut']);

            if (typeof options.duration === 'string') {
                options.duration = cssDurationToMs(options.duration);
            }

            if (!isNumber(options.duration)) {
                options.duration = 400;
            }

            setTimeout(function () {
                if (options.onEnd) {
                    if (options.onEnd() === false) {
                        return;
                    }
                }
                if (options.revert) {
                    Css.removeClassName(element, ['animate', animation]);
                }
            }, options.duration);
        }
    }

    return Animate;
});
