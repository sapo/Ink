/**
 * @module Ink.UI.Sticky_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Sticky', '1', ['Ink.UI.Common_1','Ink.Dom.Event_1','Ink.Dom.Element_1'], function(Common, Event, Element) {
    'use strict';

    /**
     * The Sticky component takes an element and transforms it's behavior in order to, when the user scrolls he sets its position
     * to fixed and maintain it until the user scrolls back to the same place.
     *
     * @class Ink.UI.Sticky
     * @constructor
     * @version 1
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *     @param {Number}     options.offsetBottom       Number of pixels of distance from the bottomElement.
     *     @param {Number}     options.offsetTop          Number of pixels of distance from the topElement.
     *     @param {String}     options.topElement         CSS Selector that specifies a top element with which the component could collide.
     *     @param {String}     options.bottomElement      CSS Selector that specifies a bottom element with which the component could collide.
     *     @param {Array|String} [options.activateInLayouts='medium,large'] Layouts in which the sticky behaviour is present. Pass an array or comma-separated string.
     * @example
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.Sticky_1'], function( Selector, Sticky ) {
     *              var menuElement = Ink.s('#menu');
     *              var stickyObj = new Sticky( menuElement );
     *          });
     *      </script>
     */
    var Sticky = function( selector, options ){
        this._rootElement = Common.elOrSelector(selector, 'Ink.UI.Sticky_1');

        this._options = Common.options({
            offsetBottom: ['Integer', 0],
            offsetTop: ['Integer', 0],
            topElement: ['Element', null],
            bottomElement: ['Element', null],
            activateInLayouts: ['String', 'medium,large']
        }, options || {}, this._rootElement );

        // Because String#indexOf is compatible with lt IE8 but not Array#indexOf
        this._options.activateInLayouts = this._options.activateInLayouts.toString();

        // Save a reference to getComputedStyle
        var computedStyle = window.getComputedStyle ?
            window.getComputedStyle(this._rootElement, null) :
            this._rootElement.currentStyle;

        this._dims = {
            height: computedStyle.height,
            width: computedStyle.width
        };

        this._init();
    };

    Sticky.prototype = {

        /**
         * Init function called by the constructor
         *
         * @method _init
         * @private
         */
        _init: function(){
            Event.observe( document, 'scroll', Ink.bindEvent(Event.throttle(this._onScroll, 100), this) );
            Event.observe( window, 'resize', Ink.bindEvent(Event.throttle(this._onResize, 100), this) );

            this._calculateOriginalSizes();

            this._calculateOffsets();

        },

        /**
         * Returns whether the sticky is disabled in the current view
         *
         * @method isDisabledInLayout
         * @private
         */
        _isDisabledInLayout: function () {
            return this._options.activateInLayouts.indexOf(Common.currentLayout()) === -1;
        },

        /**
         * Scroll handler.
         *
         * @method _onScroll
         * @private
         */
        _onScroll: function(){
            var scrollHeight = Element.scrollHeight();

            var unstick = this._isDisabledInLayout() ||
                scrollHeight <= this._options.originalTop-this._options.originalOffsetTop;

            if( unstick ) {
                // We're on top, no sticking. position:static is the "normal" position.
                this._unstick();
            } else if(document.body.scrollHeight-(scrollHeight+parseInt(this._dims.height,10)) >= this._options.offsetBottom ){
                // Stick to screen!
                this._stickTo('screen');
            } else {
                // Stick to bottom
                this._stickTo('bottom');
            }
        },

        /**
         * Have the sticky stick nowhere, to the screen, or to the bottom.
         *
         * @method _stickTo
         * @private
         */
        _stickTo: function (where) {
            var scrollHeight = Element.scrollHeight();

            var style = this._rootElement.style;

            style.position = 'fixed';
            style.left = this._options.originalLeft + 'px';
            style.width = this._options.originalWidth + 'px';

            if (where === 'screen') {
                style.bottom = 'auto';
                style.top = this._options.originalOffsetTop + 'px';
            } else if (where === 'bottom') {
                // was: var distanceFromBottomOfScreenToBottomOfDocument
                var toBottom = document.body.scrollHeight - (document.documentElement.clientHeight + scrollHeight);
                style.bottom = this._options.offsetBottom - toBottom + 'px';
                style.top = 'auto';
            }
        },

        /**
         * "unstick" the sticky from the screen or bottom of the document
         * @method _unstick
         * @private
         */
        _unstick: function () {
            this._rootElement.style.position = 'static';
            this._rootElement.style.width = null;
        },

        /**
         * Resize handler
         *
         * @method _onResize
         * @private
         */
        _onResize: function(){
            this._rootElement.removeAttribute('style');
            this._calculateOriginalSizes();
            this._calculateOffsets();
        },

        /**
         * On each resizing (and in the beginning) the component recalculates the offsets, since
         * the top and bottom element heights might have changed.
         *
         * @method _calculateOffsets
         * @private
         */
        _calculateOffsets: function(){
            /**
             * Calculating the offset top
             */
            if( this._options.topElement ){
                var topElementHeight = Element.elementHeight( this._options.topElement );
                var topElementTop = Element.elementTop( this._options.topElement );

                this._options.offsetTop = topElementHeight + topElementTop + parseInt(this._options.originalOffsetTop,10);
            }

            /**
             * Calculating the offset bottom
             */
            if( this._options.bottomElement ){
                var bottomElementHeight = Element.elementHeight(this._options.bottomElement);

                this._options.offsetBottom = bottomElementHeight + this._options.originalOffsetBottom;
            }

            this._onScroll();

        },

        /**
         * Function to calculate the 'original size' of the element.
         * It's used in the begining (_init method) and when a scroll happens
         *
         * @method _calculateOriginalSizes
         * @private
         */
        _calculateOriginalSizes: function(){
            if( typeof this._options.originalOffsetTop === 'undefined' ){
                this._options.originalOffsetTop = parseInt(this._options.offsetTop,10);
                this._options.originalOffsetBottom = parseInt(this._options.offsetBottom,10);
            }
            this._options.originalTop = parseInt(this._rootElement.offsetTop,10);
            this._options.originalLeft = parseInt(this._rootElement.offsetLeft,10);
            this._options.originalWidth = parseInt(this._dims.width, 10) || 0;
        }

    };

    return Sticky;

});
