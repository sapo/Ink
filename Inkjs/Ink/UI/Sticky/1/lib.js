/**
 * @module Ink.UI.Sticky_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Sticky', '1', ['Ink.UI.Common_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1'], function(Common, Event, Css, Element, Selector ) {
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
     * @example
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.Sticky_1'], function( Selector, Sticky ){
     *              var menuElement = Ink.s('#menu');
     *              var stickyObj = new Sticky( menuElement );
     *          });
     *      </script>
     */
    var Sticky = function( selector, options ){

        if( typeof selector !== 'object' && typeof selector !== 'string'){
            throw '[Sticky] :: Invalid selector defined';
        }

        this._rootElement = Common.elOrSelector(selector,
            "[Sticky] :: Can't find any element with the specified selector");

        /**
         * Setting default options and - if needed - overriding it with the data attributes and given options
         */
        this._options = Ink.extendObj({
            offsetBottom: 0,
            offsetTop: 0,
            topElement: null,
            bottomElement: null
        }, options || {},  InkElement.data( this._rootElement ) );

        if( this._options.topElement ){
            this._topElement = Common.elOrSelector( this._options.topElement, 'Top Element');
        }

        if( this._options.bottomElement ){
            this._bottomElement = Common.elOrSelector( this._options.bottomElement, 'Bottom Element');
        }

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
            InkEvent.observe( document, 'scroll', Ink.bindEvent(this._onScroll,this) );
            InkEvent.observe( window, 'resize', Ink.bindEvent(this._onResize,this) );
        },

        /**
         * Scroll handler.
         *
         * @method _onScroll
         * @private
         */
        _onScroll: InkEvent.throttle(function(){
            var viewport = (document.compatMode === "CSS1Compat") ?  document.documentElement : document.body;
            var elm = this._rootElement;

            if(
                ( ( (InkElement.elementWidth(this._rootElement)*100)/viewport.clientWidth ) > 90 ) ||
                ( viewport.clientWidth<=649 )
            ){
                if( InkElement.hasAttribute(elm,'style') ){
                    elm.removeAttribute('style');
                }
                return;  // Do not do anything for mobile
            }


            var elementRect = elm.getBoundingClientRect();
            var topRect = this._topElement && this._topElement.getBoundingClientRect();
            var bottomRect = this._bottomElement && this._bottomElement.getBoundingClientRect();

            var offsetTop = this._options.offsetTop ? parseInt(this._options.offsetTop, 10) : 0;
            var offsetBottom = this._options.offsetBottom ? parseInt(this._options.offsetBottom, 10) : 0;

            var elementHeight = elementRect.bottom - elementRect.top;

            var elMargins =
                (parseInt(Css.getStyle(elm, 'margin-top'), 10) || 0) +
                (parseInt(Css.getStyle(elm, 'margin-bottom'), 10) || 0);

            var stickingTo = this._lastStickingTo;

            if (bottomRect && bottomRect.top < elementHeight + offsetTop + offsetBottom + elMargins) {
                stickingTo = 'bottom';
                elm.style.position = 'fixed';
                elm.style.top = bottomRect.top - elementHeight - offsetBottom - elMargins + 'px';
            } else if (!topRect || topRect.bottom > offsetTop) {
                stickingTo = '[normal]';
                elm.style.position = 'static';
                elm.style.top = 'auto';
            } else if (topRect && topRect.bottom <= offsetTop) {
                stickingTo = 'top';
                elm.style.position = 'fixed';
                elm.style.top = offsetTop + 'px';
            }

            if (stickingTo !== this._lastStickingTo) {
                Css.addRemoveClassName(elm, 'ink-sticky-top', stickingTo === 'top');
                Css.addRemoveClassName(elm, 'ink-sticky-bottom', stickingTo === 'bottom');
            }
        }, 80),

        /**
         * Resize handler
         *
         * @method _onResize
         * @private
         */
        _onResize: InkEvent.throttle(function(){
            this._onScroll();
        }, 80),

    };

    return Sticky;

});
