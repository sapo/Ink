/**
 * @module Ink.UI.Sticky_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Sticky', '1', ['Ink.UI.Common_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1'], function(Common, Event, Css, Element) {
    'use strict';

    /* jshint maxcomplexity: 3 */
    function arrayContains(haystack, needle) {
        if (haystack.indexOf) {
            return haystack.indexOf(needle) !== -1;
        } else {
            for (var i = 0, len = haystack.length; i < len; i++) {
                if (haystack[i] === needle) {
                    return true;
                }
            }
        }
        return false;
    }

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
     *     @param {Array|String} [options.activateInLayouts='medium,large'] Layouts in which the sticky behaviour is present.
     * @example
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.Sticky_1'], function( Selector, Sticky ){
     *              var menuElement = Ink.s('#menu');
     *              var stickyObj = new Sticky( menuElement );
     *          });
     *      </script>
     */
    var Sticky = function( selector, options ){
        this._rootElement = Common.elOrSelector(selector, 'Ink.UI.Sticky_1');

        /**
         * Setting default options and - if needed - overriding it with the data attributes
         */
        this._options = Common.options({
            offsetBottom: ['Integer', 0],
            offsetTop: ['Integer', 0],
            topElement: ['Element', document.body /* TODO weird */],
            bottomElement: ['Element', document.body /* TODO weird */],
            activateInLayouts: ['String', 'medium,large']
        }, options || {}, this._rootElement );


        this._options.activateInLayouts = this._options.activateInLayouts.toString().split(/[, ]+/g);

        // Save a reference to getComputedStyle
        this._computedStyle = window.getComputedStyle ? window.getComputedStyle(this._rootElement, null) : this._rootElement.currentStyle;
        this._dims = {
            height: this._computedStyle.height,
            width: this._computedStyle.width
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
            return !arrayContains(this._options.activateInLayouts, Common.currentLayout());
        },

        /**
         * Scroll handler.
         *
         * @method _onScroll
         * @private
         */
        _onScroll: function(){
            if( this._isDisabledInLayout() ){
                this._unstick();
                return;
            }

            clearTimeout(this._scrollTimeout);

            this._scrollTimeout = setTimeout(Ink.bind(function(){
                var scrollHeight = Element.scrollHeight();

                if( scrollHeight <= (this._options.originalTop-this._options.originalOffsetTop)){
                    // We're on top, no sticking. position:static is the "normal" position.
                    this._unstick();
                } else if((document.body.scrollHeight-(scrollHeight+parseInt(this._dims.height,10))) >= this._options.offsetBottom ){
                    // Stick to screen!
                    this._stickTo('screen');
                } else {
                    // Stick to bottom
                    this._stickTo('bottom');
                }
            },this), 0);
        },

        /**
         * Have the sticky stick nowhere, to the screen, or to the bottom.
         *
         * @method _stickTo
         * @private
         */
        _stickTo: function (where) {
            var scrollHeight = Element.scrollHeight();

            var props = this._rootElement.style;

            if (where === 'screen' || where === 'bottom') {
                props.position = 'fixed';
                props.left = this._options.originalLeft + 'px';
                props.width = this._options.originalWidth + 'px';
            }

            if (where === 'screen') {
                props.bottom = 'auto';
                props.top = this._options.originalOffsetTop + 'px';
            }

            if (where === 'bottom') {
                props.top = 'auto';
                // was: var distanceFromBottomOfScreenToBottomOfDocument
                var toBottom = document.body.scrollHeight - (document.documentElement.clientHeight + scrollHeight);
                props.bottom = this._options.offsetBottom - toBottom + 'px';
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
            if( this._options.topElement.nodeName.toLowerCase() !== 'body' ){
                var topElementHeight = Element.elementHeight( this._options.topElement ),
                    topElementTop = Element.elementTop( this._options.topElement );

                this._options.offsetTop = ( parseInt(topElementHeight,10) + parseInt(topElementTop,10) ) + parseInt(this._options.originalOffsetTop,10);
            } else {
                this._options.offsetTop = parseInt(this._options.originalOffsetTop,10);
            }

            /**
             * Calculating the offset bottom
             */
            if( this._options.bottomElement.nodeName.toLowerCase() !== 'body' ){
                var bottomElementHeight = Element.elementHeight(this._options.bottomElement);
                this._options.offsetBottom = parseInt(bottomElementHeight,10) + parseInt(this._options.originalOffsetBottom,10);
            } else {
                this._options.offsetBottom = parseInt(this._options.originalOffsetBottom,10);
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
