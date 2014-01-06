Ink.createModule('Ink.UI.Animate', 1, ['Ink.UI.Common_1', 'Ink.Dom.Css_1', 'Ink.Dom.Event_1'], function (Common, Css, InkEvent) {
    var Animate = {
        animate: function (element, animation, options) {
            element = Common.elOrSelector(element);

            if (typeof options === 'number') {
                options = { duration: options };
            }

            if (typeof arguments[3] === 'function') {
                options.onEnd = arguments[3];
            }

            if (typeof options.duration !== 'number') {
                options.duration = 400;
            }

            Css.addClassName(element, ['animated', animation]);
            // element.style.

            /*
            setTimeout(function () {
                if (options.onEnd) {
                    if (options.onEnd() === false) {
                        return;
                    }
                }
                Css.removeClassName(element, ['animated', animation]);
            }, options.duration);
            */

            element.addEventListener('animationend', function () {
                alert(arguments[0])
            })
        }
    }

    return Animate;
});
