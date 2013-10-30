Ink.createModule('Ink.UI.Animation', '1', ['Ink.Dom.Css_1', 'Ink.Dom.Element_1'], function (Css, InkElement) {
    var Animation = {
        transitionScrubber: function (elem, animClass, styleProps) {
            var startProps = {};
            var endProps = {};
            elem = Ink.i(elem)

            for (var i = 0, len = styleProps.length; i < len; i++) {
                startProps[styleProps[i]] = Css.getStyle(elem, styleProps[i]);
            }

            var oldDuration = Css.getStyle(elem, 'transitionDuration')
            elem.style.transitionDuration = '0s';
            elem.classList.add(animClass);

            for (var i = 0, len = styleProps.length; i < len; i++) {
                endProps[styleProps[i]] = Css.getStyle(elem, styleProps[i]);
            }

            elem.classList.remove(animClass);
            
            return {
                update: function (ratio) {
                    if (ratio > 1) ratio = 1;
                    if (ratio < 0) ratio = 0;
                    var prop;
                    var start;
                    var end;
                    for (var i = 0, len = styleProps.length; i < len; i++) {
                        prop = styleProps[i];
                        start = parseInt(startProps[prop], 10);
                        end = parseInt(endProps[prop], 10);

                        elem.style[prop] = (
                            start + (end * ratio)
                        ) + 'px';
                    }
                },
                detach: function () {
                    for (var i = 0, len = styleProps.length; i < len; i++) {
                        elem.style[styleProps[i]] = null;
                    }
                    elem.style.transitionDuration = oldDuration;
                },
            }
        },
    };
    return Animation;
});
