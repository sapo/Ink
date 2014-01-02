Ink.createModule('Ink.UI.Animate', 1, ['Ink.Dom.Css_1'], function (Css) {
    var Animate = {
        animate: function (element, animation, options) {
            if (typeof options === 'number') {
                options = { duration: options };
            }

            if (typeof arguments[3] === 'function') {
                options.onEnd = arguments[3];
            }

            if (typeof options.duration !== 'number') {
                options.duration = 400;
            }

            Css.addClassName(element, ['animate', animation]);

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
