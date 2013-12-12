/**
 * @module Ink.UI.Spy_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Spy', '1', ['Ink.UI.Common_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1'], function(Common, Event, Css, Element, Selector ) {
    'use strict';

    /**
     * Spy is a component that 'spies' an element (or a group of elements) and when they leave the viewport (through the top),
     * highlight an option - related to that element being spied - that resides in a menu, initially identified as target.
     * 
     * @class Ink.UI.Spy
     * @constructor
     * @version 1
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *     @param {DOMElement|String}     options.target          Target menu on where the spy will highlight the right option.
     *     @param {String}                [options.activeClass='active'] Class which marks the "li" as active.
     * @example
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.Spy_1'], function( Selector, Spy ){
     *              var menuElement = Ink.s('#menu');
     *              var specialAnchorToSpy = Ink.s('#specialAnchor');
     *              var spyObj = new Spy( specialAnchorToSpy, {
     *                  target: menuElement
     *              });
     *          });
     *      </script>
     */
    var Spy = function( selector, options ){

        this._rootElement = Common.elOrSelector(selector,'1st argument');

        /**
         * Setting default options and - if needed - overriding it with the data attributes
         */
        this._options = Ink.extendObj({
            target: undefined,
            activeClass: 'active'
        }, Element.data( this._rootElement ) );

        /**
         * In case options have been defined when creating the instance, they've precedence
         */
        this._options = Ink.extendObj(this._options,options || {});

        this._options.target = Common.elOrSelector( this._options.target, 'Target' );

        this._init();
    };

    Spy.prototype = {

        /**
         * Stores the spy elements
         *
         * @property _elements
         * @type {Array}
         * @readOnly
         * 
         */
        _elements: [],  // [wtf] since arrays are immutable, _elements is the same for every instance. Very weird logic going on here

        /**
         * Init function called by the constructor
         * 
         * @method _init
         * @private
         */
        _init: function(){
            // Rate limited scroll function
            var throttledScroll = Event.throttle(Ink.bind(this._onScroll, this), 300);
            throttledScroll();
            Event.observe( document, 'scroll', throttledScroll );
            this._elements.push(this._rootElement);
        },

        /**
         * Scroll handler. Responsible for highlighting the right options of the target menu.
         * 
         * @method _onScroll
         * @private
         */
        _onScroll: function(){
            var bbox = this._rootElement.getBoundingClientRect();

            // To be the active element, its top must be above the viewport
            // (so that the content is inside the viewport)
            if( bbox.top > 0 ) {
                return;
            }

            // Find other elements, check if any other element could be the active element
            var otherBbox;
            for( var i = 0, total = this._elements.length; i < total; i++ ){
                if (this._elements[i] !== this._rootElement) {
                    otherBbox = this._elements[i].getBoundingClientRect();
                    if (otherBbox.top <= 0 && otherBbox.top > bbox.top) {
                        return;
                    }
                }
            }

            // This selector finds li's to deactivate
            var activeLinkSelector = 'li.active';  // [todo] configurable active class

            // The link which should be activated has a "href" ending with "#" + name or id of the element
            var menuLinkSelector = 'a[href$="#' + (this._rootElement.name || this._rootElement.id) + '"]';

            var toDeactivate = Selector.select(activeLinkSelector, this._options.target);
            for (i = 0, total = toDeactivate.length; i < total; i++) {
                Css.removeClassName(toDeactivate[i], this._options.activeClass);
            }

            var toActivate = Selector.select(menuLinkSelector, this._options.target);
            for (i = 0, total = toActivate.length; i < total; i++) {
                Css.addClassName(Element.findUpwardsByTag(toActivate[i], 'li'), this._options.activeClass);
            }
        }
    };

    return Spy;

});
