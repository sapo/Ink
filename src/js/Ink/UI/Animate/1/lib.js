Ink.createModule('Ink.UI.Animate', 1, ['Ink.UI.Common_1', 'Ink.Dom.Css_1'], function (Common, Css) {
    'use strict';

    var animationPrefix = (function (el) {
        return ('animationName' in el.style) ? 'animation' :
               ('oAnimationName' in el.style) ? 'oAnimation' :
               ('msAnimationName' in el.style) ? 'msAnimation' :
               ('webkitAnimationName' in el.style) ? 'webkitAnimation' : null;
    }(document.createElement('div')));

    var animationEndEventName = {
        animation: 'animationend',
        oAnimation: 'oanimationend',
        msAnimation: 'MSAnimationEnd',
        webkitAnimation: 'webkitAnimationEnd'
    }[animationPrefix];

    var Animate = {
        /**
         * Prefix for CSS animation-related properties in this browser.
         *
         * @property _animationPrefix
         * @private
         **/
        _animationPrefix: animationPrefix,

        /**
         * Whether CSS3 animation is supported in this browser.
         *
         * @property {Boolean} animationSupported
         **/
        animationSupported: !!animationPrefix,

        /**
         * The prefix for animation{start,iteration,end} events
         *
         * @property {String} animationEndEventName
         **/
        animationEndEventName: animationEndEventName,

        /**
         * Animate a div using one of the animate.css classes
         *
         * @method animate
         * @param element {DOMElement} animated element
         * @param animation {String} animation string
         * @param [options] {Object}
         *     @param [options.onEnd=null] {Function} callback for animation end
         *     @param [options.duration=medium] {String|Number} duration name (fast|medium|slow) or duration in ms
         **/
        animate: function (element, animation, options) {
            element = Common.elOrSelector(element);

            if (typeof options === 'number' || typeof options === 'string') {
                options = { duration: options };
            }

            if (typeof arguments[3] === 'function') {
                options.onEnd = arguments[3];
            }

            if (typeof options.duration !== 'number' && typeof options.duration !== 'string') {
                options.duration = 400;
            }

            if (typeof options.duration === 'number') {
                element.style[animationPrefix + 'Duration'] = options.duration + 'ms';
            } else if (typeof options.duration === 'string') {
                Css.addClassName(element, options.duration);
            }

            if (!Animate.animationSupported) {
                if (options.onEnd) {
                    setTimeout(function () {
                        options.onEnd(null);
                    }, 0);
                }
                return;
            }

            Css.addClassName(element, ['animated', animation]);

            element.addEventListener(animationEndEventName, function onAnimationEnd(event) {
                if (event.target !== element) { return; }
                if (event.animationName !== animation) { return; }
                if (options.onEnd) { options.onEnd(event); }
                Css.removeClassName(element, ['animated', animation]);
                element.removeEventListener(animationEndEventName, onAnimationEnd, false);
            }, false);
        }
    };

    return Animate;
});

