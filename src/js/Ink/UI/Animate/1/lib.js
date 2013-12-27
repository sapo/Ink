Ink.createModule('Ink.UI.Animate', 1, ['Ink.Dom.Css_1'], function (Css) {
    /* TODO maybe?
    var animationDurationSupport = (function (el) {
        var options = [
            'animation-duration',
            'o-animation-duration',
            'ms-animation-duration',
            'webkit-animation-duration'
        ];
        for (var i = 0; i < options.length; i++) {
            if (typeof Css.getStyle(el, options[i]) === 'string') {
                return options[i];
            }
        }
    }(document.createElement('div')));
    */

    function cssDurationToMs(duration) {
        duration = duration
            .replace(/ms$/, '')
            .replace(/s$/, '000');

        duration = +duration;

        return duration;
    }

    var Animate = {
        animate: function (element, animation, options) {
            if (arguments.length === 2) {
                options = animation;
                animation = options.animation
            }

            Css.addClassName(element, ['animate', animation || 'fadeOut']);

            options = Ink.extendObj({
                duration: 400
            }, options || {});

            /* TODO maybe ?
            if (animationDurationSupport) {
                duration = cssDurationToMs(Css.getStyle(element, 'animation-duration'));
            }
            */

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
