/**
 * @module Ink.UI.Toggle_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Toggle', '1', ['Ink.UI.Common_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1'], function(Common, InkEvent, Css, InkElement, Selector, InkArray ) {
    'use strict';

    /**
     * Toggle component
     * 
     * @class Ink.UI.Toggle
     * @constructor
     * @version 1
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *     @param {String}       options.target                    CSS Selector that specifies the elements that this component will toggle
     *     @param {String}       [options.triggerEvent='click']    Event that will trigger the toggling.
     *     @param {Boolean}      [options.closeOnClick=true]       When clicking outside of the toggled content, it should be hidden.
     *     @param {Selector}     [options.closeOnInsideClick='a[href]']      Toggle closes if a click occurs inside the toggle and the element matches the selector. Set to null or false to deactivate. Default: links
     *     @param {Boolean}      [options.initialState=null]       Whether to start hidden, shown, or as found in the markup. (false: hidden, true: shown, null: markup)
     *     @param {Function}     [options.onChangeState=null]       Whether to start hidden, shown, or as found in the markup. (false: hidden, true: shown, null: markup)
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
        this._rootElement = Common.elOrSelector(selector, '[Ink.UI.Toggle]: Failed to find root element');

        this._options = Ink.extendObj({
            target : undefined,
            triggerEvent: 'click',
            closeOnClick: true,
            isAccordion: false,
            initialState: null,
            closeOnInsideClick: 'a[href]',  // closes the toggle when a target is clicked and it is a link
            onChangeState: null
        }, options || {}, InkElement.data(this._rootElement));

        this._targets = Common.elsOrSelector(this._options.target);

        if (!this._targets.length) {
            throw '[Ink.UI.Toggle] Toggle target was not found! Supply a valid selector, array, or element through the `target` option.';
        }

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
            this._accordion = ( Css.hasClassName(this._rootElement.parentNode,'accordion') || Css.hasClassName(this._targets[0].parentNode,'accordion') );

            this._firstTime = true;

            if (this._options.triggerEvent) {
                InkEvent.observe(this._rootElement, this._options.triggerEvent, Ink.bindEvent(this._onTriggerEvent,this));
            }

            if( this._options.closeOnClick.toString() === 'true' ){
                InkEvent.observe( document, 'click', Ink.bindEvent(this._onOutsideClick,this));
            }
            if( this._options.closeOnInsideClick ) {
                InkEvent.observeMulti(this._targets, 'click', Ink.bindEvent(function (e) {
                    if ( InkElement.findUpwardsBySelector(InkEvent.element(e), this._options.closeOnInsideClick) ) {
                        this.setState(false, true);
                    }
                }, this));
            }

            if (this._options.initialState !== null) {
                this.setState(this._options.initialState.toString() === 'true', true);
            } else {
                // Add initial classes matching the current "display" of the object.
                var state = Css.getStyle(this._targets[0], 'display') !== 'none';
                this.setState(state, true);
            }
            // Aditionally, remove any inline "display" style.
            for (var i = 0, len = this._targets.length; i < len; i++) {
                if (this._targets[i].style.display) {
                    this._targets[i].style.display = '';
                }
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
                if( Css.hasClassName(this._targets[0].parentNode,'accordion') ){
                    accordionElement = this._targets[0].parentNode;
                } else {
                    accordionElement = this._targets[0].parentNode.parentNode;
                }
                elms = Selector.select('.toggle',accordionElement);
                for( i=0; i<elms.length; i+=1 ){
                    var dataset = InkElement.data( elms[i] ),
                        targetElm = Selector.select( dataset.target,accordionElement );

                    if( (targetElm.length > 0) && (targetElm[0] !== this._targets[0]) ){
                        targetElm[0].style.display = 'none';
                    }
                }
            }
            
            var has = this.getState();
            this.setState(!has, true);
            if (!has && this._firstTime) {
                this._firstTime = false;
            }

            InkEvent.stop(event);
        },

        /**
         * Click handler. Will handle clicks outside the toggle component.
         * 
         * @method _onOutsideClick
         * @param {Event} event
         * @private
         */
        _onOutsideClick: function( event ){
            var tgtEl = InkEvent.element(event),
                shades;

            var ancestorOfTargets = InkArray.some(this._targets, function (target) {
                return InkElement.isAncestorOf(target, tgtEl);
            });

            if( (this._rootElement === tgtEl) || InkElement.isAncestorOf(this._rootElement, tgtEl) || ancestorOfTargets || this._firstTime ) {
                return;
            } else if( (shades = Ink.ss('.ink-shade')).length ) {
                var shadesLength = shades.length;

                for( var i = 0; i < shadesLength; i++ ){
                    if( InkElement.isAncestorOf(shades[i],tgtEl) && InkElement.isAncestorOf(shades[i],this._rootElement) ){
                        return;
                    }
                }
            }

            this.setState(false, true);  // dismiss
        },

        /**
         * Sets the state of the toggle. (Shown/Hidden)
         *
         * @param shown {Boolean} New state (Shown/Hidden)
         * 
         * @method setState
         */
        setState: function (shown, callHandler) {
            if (callHandler && typeof this._options.onChangeState === 'function') {
                this._options.onChangeState(!!shown);
            }
            for (var i = 0, len = this._targets.length; i < len; i++) {
                Css.addRemoveClassName(this._targets[i], 'show-all', shown);
                Css.addRemoveClassName(this._targets[i], 'hide-all', !shown);
            }
            Css.addRemoveClassName(this._rootElement, 'active', shown);
        },

        /**
         * Gets the state of the toggle. (Shown/Hidden)
         *
         * @method setState
         *
         * @return {Boolean} whether the toggle is active.
         */
        getState: function () {
            return Css.hasClassName(this._rootElement, 'active');
        }
    };

    return Toggle;
});

