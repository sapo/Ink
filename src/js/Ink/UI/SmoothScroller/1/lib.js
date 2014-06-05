/**
 * Scroll to content
 * @module Ink.UI.SmoothScroller_1
 * @version 1
 */
Ink.createModule('Ink.UI.SmoothScroller', '1', ['Ink.UI.Common_1', 'Ink.Dom.Event_1', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1','Ink.Dom.Css_1'], function(Common, Event, InkElement, Selector, Css) {
    'use strict';

    var requestAnimationFrame =
        window.requestAnimationFrame ||
        function (cb) { return setTimeout(cb, 10); };

    var cancelAnimationFrame =
        window.cancelAnimationFrame ||
        function (id) { clearTimeout(id); };

    /**
     * @namespace Ink.UI.SmoothScroller
     * @version 1
     * @static
     *
     * SmoothScroller is a component which replaces the default scroll-to behaviour of `<a>` tags which refer to IDs on the page.
     *
     * For example, when you have this:
     *
     *          <a href="#todo">Todo</a>
     *              [...]
     *          <section id="todo">
     *              [...]
     *
     * You can click the `<a>` and the page will scroll until the section you pointed to.
     *
     * When you use SmoothScroller, instead of immediately scrolling to the element, you get a smooth motion.
     *
     * Also, you can define the data-margin option if you have a `position:fixed` top menu ruining the behaviour.
     *
     * @example
     *
     *      <a href="#part1" class="ink-smooth-scroll" data-margin="10">go to Part 1</a>
     *
     *      [lots and lots of content...]
     *
     *      <h1 id="part1">Part 1</h1>
     *
     *      <script>
     *          // ...Although you don't need to do this if you have autoload.js
     *          Ink.requireModules(['Ink.UI.SmoothScroller_1'], function (SmoothScroller) {
     *              SmoothScroller.init('.ink-smooth-scroll');
     *          })
     *      </script>
     */
    var SmoothScroller = {

        /**
         * Sets the speed of the scrolling
         *
         * @property speed
         * @type {Number}
         * @readOnly
         * @static
         */
        speed: 10,


        /**
         * Moves the scrollbar to the target element. This is the function
         * which animates the scroll position bit by bit. It calls itself in
         * the end through requestAnimationFrame
         *
         * @method scroll
         * @param  {Number} d Y coordinate value to stop
         * @public
         * @static
         */
        scroll: function(d, options) {
            var a = Math.round(InkElement.scrollHeight());
            var margin = options.margin || 0;

            var endPos = Math.round(d - margin);

            if (endPos > a) {
                a += Math.ceil((endPos - a) / SmoothScroller.speed);
            } else {
                a = a + (endPos - a) / SmoothScroller.speed;
            }

            cancelAnimationFrame(SmoothScroller.interval);

            if (!((a) === endPos || SmoothScroller.offsetTop === a)) {
                SmoothScroller.interval = requestAnimationFrame(
                    Ink.bindMethod(SmoothScroller, 'scroll', d, options), document.body);
            } else {
                SmoothScroller.onDone(options);
            }

            window.scrollTo(0, a);
            SmoothScroller.offsetTop = a;
        },


        /**
         * Has smooth scrolling applied to relevant elements upon page load.
         *
         * @method init
         * @param [selector='a.scrollableLink,a.ink-smooth-scroll'] Selector string for finding links with smooth scrolling enabled.
         * @public
         * @static
         */
        init: function(selector) {
            Event.on(document, 'click', selector, SmoothScroller.onClick);
        },

        // Deprecated. Kept around just in case someone is still calling this.
        render: function() {},

        /**
         * Handles clicks on link elements
         *
         * @method onClick
         * @public
         * @static
         */
        onClick: function(event) {
            var link = event.currentTarget;

            var hash = link.getAttribute('data-hash') || (link.getAttribute('href') || '')
                .replace(/^.*?#/, '');

            if(hash) {
                event.preventDefault();
                var activeLiSelector = 'ul > li.active > ' + selector;

                var selector = 'a[name="' + hash + '"],#' + hash;
                var elm = Ink.s(selector);
                var activeLi = Ink.s(activeLiSelector);
                activeLi = activeLi && activeLi.parentNode;

                if (elm) {
                    if (!Css.hasClassName(link.parentNode, 'active')) {
                        if (activeLi) {
                            Css.removeClassName(activeLi, 'active');
                        }
                        Css.addClassName(link.parentNode, 'active');
                    }

                    var options = Common.options('SmoothScroller link options', {
                        margin: ['Number', 0],
                        noHashChange: ['Boolean', false]
                    }, {}, elm);

                    SmoothScroller.hash = hash;
                    
                    SmoothScroller.scroll(InkElement.offsetTop(elm), options);
                }
            }
        },

        /**
         * Called when the scroll movement is done. Updates browser address.
         */
        onDone: function (options) {
            if (!options.noHashChange) {
                window.location.hash = SmoothScroller.hash;
            }

            SmoothScroller.hash = SmoothScroller.offsetTop = null;
        }
    };

    return SmoothScroller;

});
