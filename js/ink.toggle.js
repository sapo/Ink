/**
00 * @module Ink.UI.Toggle_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Toggle', '1', ['Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1'], function(Aux, Event, Css, Element, Selector ) {
    'use strict';

    /**
     * Toggle component
     * 
     * @class Ink.UI.Toggle
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *     @param {String}       options.target                    CSS Selector that specifies the elements that will toggle
     *     @param {String}       [options.triggerEvent]            Event that will trigger the toggling. Default is 'click'
     *     @param {Boolean}      [options.closeOnClick]            Flag that determines if, when clicking outside of the toggled content, it should hide it. Default: true.
     * @example
     *      <div class="ink-dropdown">
     *          <button class="ink-button toggle" data-target="#dropdown">Dropdown <span class="icon-caret-down"></span></button>
     *          <ul id="dropdown" class="dropdown-menu">
     *              <li class="heading">Heading</li>
     *              <li class="separator-above"><a href="#">Option</a></li>
     *              <li><a href="#">Option</a></li>
     *              <li class="separator-above disabled"><a href="#">Disabled option</a></li>
     *              <li class="submenu">
     *                  <a href="#" class="toggle" data-target="#submenu1">A longer option name</a>
     *                  <ul id="submenu1" class="dropdown-menu">
     *                      <li class="submenu">
     *                          <a href="#" class="toggle" data-target="#ultrasubmenu">Sub option</a>
     *                          <ul id="ultrasubmenu" class="dropdown-menu">
     *                              <li><a href="#">Sub option</a></li>
     *                              <li><a href="#" data-target="ultrasubmenu">Sub option</a></li>
     *                              <li><a href="#">Sub option</a></li>
     *                          </ul>
     *                      </li>
     *                      <li><a href="#">Sub option</a></li>
     *                      <li><a href="#">Sub option</a></li>
     *                  </ul>
     *              </li>
     *              <li><a href="#">Option</a></li>
     *          </ul>
     *      </div>
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.Toggle_1'], function( Selector, Toggle ){
     *              var toggleElement = Ink.s('.toggle');
     *              var toggleObj = new Toggle( toggleElement );
     *          });
     *      </script>
     */
    var Toggle = function( selector, options ){

        if( typeof selector !== 'string' && typeof selector !== 'object' ){
            throw '[Ink.UI.Toggle] Invalid CSS selector to determine the root element';
        }

        if( typeof selector === 'string' ){
            this._rootElement = Selector.select( selector );
            if( this._rootElement.length <= 0 ){
                throw '[Ink.UI.Toggle] Root element not found';
            }

            this._rootElement = this._rootElement[0];
        } else {
            this._rootElement = selector;
        }

        this._options = Ink.extendObj({
            target : undefined,
            triggerEvent: 'click',
            closeOnClick: true
        },Element.data(this._rootElement));

        this._options = Ink.extendObj(this._options,options || {});

        if( typeof this._options.target === 'undefined' ){
            throw '[Ink.UI.Toggle] Target option not defined';
        }

        this._childElement = Aux.elOrSelector( this._options.target, 'Target' );
        // this._childElement = Selector.select( this._options.target, this._rootElement );
        // if( this._childElement.length <= 0 ){
        //     if( this._childElement.length <= 0 ){
        //         this._childElement = Selector.select( this._options.target, this._rootElement.parentNode );
        //     }

        //     if( this._childElement.length <= 0 ){
        //         this._childElement = Selector.select( this._options.target );
        //     }

        //     if( this._childElement.length <= 0 ){
        //         return;
        //     }
        // }
        // this._childElement = this._childElement[0];

        this._init();

    };

    Toggle.prototype = {

        /**
         * Init function called by the constructor
         * 
         * @method _init
         * @private
         */
        _init: function(){

            this._accordion = ( Css.hasClassName(this._rootElement.parentNode,'accordion') || Css.hasClassName(this._childElement.parentNode,'accordion') );

            Event.observe( this._rootElement, this._options.triggerEvent, Ink.bindEvent(this._onTriggerEvent,this) );
            if( this._options.closeOnClick.toString() === 'true' ){
                Event.observe( document, 'click', Ink.bindEvent(this._onClick,this));
            }
        },

        /**
         * Event handler. It's responsible for handling the <triggerEvent> defined in the options.
         * This will trigger the toggle.
         * 
         * @method _onTriggerEvent
         * @param {Event} event
         * @private
         */
        _onTriggerEvent: function( event ){

            if( this._accordion ){
                var elms, i, accordionElement;
                if( Css.hasClassName(this._childElement.parentNode,'accordion') ){
                    accordionElement = this._childElement.parentNode;
                } else {
                    accordionElement = this._childElement.parentNode.parentNode;
                }
                elms = Selector.select('.toggle',accordionElement);
                for( i=0; i<elms.length; i+=1 ){
                    var
                        dataset = Element.data( elms[i] ),
                        targetElm = Selector.select( dataset.target,accordionElement )
                    ;
                    if( (targetElm.length > 0) && (targetElm[0] !== this._childElement) ){
                            targetElm[0].style.display = 'none';
                    }
                }
            }

            var finalClass = ( Css.getStyle(this._childElement,'display') === 'none') ? 'show-all' : 'hide-all';
            var finalDisplay = ( Css.getStyle(this._childElement,'display') === 'none') ? 'block' : 'none';
            Css.removeClassName(this._childElement,'show-all');
            Css.removeClassName(this._childElement, 'hide-all');
            Css.addClassName(this._childElement, finalClass);
            this._childElement.style.display = finalDisplay;

            if( finalClass === 'show-all' ){
                Css.addClassName(this._rootElement,'active');
            } else {
                Css.removeClassName(this._rootElement,'active');
            }
        },

        /**
         * Click handler. Will handle clicks outside the toggle component.
         * 
         * @method _onClick
         * @param {Event} event
         * @private
         */
        _onClick: function( event ){
            var
                tgtEl = Event.element(event),
                shades
            ;

            if( (this._rootElement === tgtEl) || Element.isAncestorOf( this._rootElement, tgtEl ) || Element.isAncestorOf( this._childElement, tgtEl ) ){
                return;
            } else if( (shades = Ink.ss('.ink-shade')).length ) {
                var
                    shadesLength = shades.length
                ;

                for( var i = 0; i < shadesLength; i++ ){
                    if( Element.isAncestorOf(shades[i],tgtEl) && Element.isAncestorOf(shades[i],this._rootElement) ){
                        return;
                    }
                }
            }
            
            this._dismiss( this._rootElement );
        },

        /**
         * Dismisses the toggling.
         * 
         * @method _dismiss
         * @private
         */
        _dismiss: function( ){
            if( ( Css.getStyle(this._childElement,'display') === 'none') ){
                return;
            }
            Css.removeClassName(this._childElement, 'show-all');
            Css.removeClassName(this._rootElement,'active');
            Css.addClassName(this._childElement, 'hide-all');
            this._childElement.style.display = 'none';
        }
    };

    return Toggle;

});
