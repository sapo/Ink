/**
 * This is the Spy component. Allows users to set an element to spy a nav element, to highlight places
 * when the spy reaches the top of the viewport.
 */

(function(undefined){

    'use strict';

    /**
     * Dependencies
     */
    
    if( typeof SAPO === 'undefined' ){
        throw '[Spy] :: Missing dependency "SAPO"';
    }

    SAPO.namespace('Ink');

    if( typeof SAPO.Dom.Selector === 'undefined' ){
        throw '[Spy] :: Missing dependency "SAPO.Dom.Selector"';
    }

    if( typeof SAPO.Dom.Css === 'undefined' ){
        throw '[Spy] :: Missing dependency "SAPO.Dom.Css"';
    }

    if( typeof SAPO.Dom.Element === 'undefined' ){
        throw '[Spy] :: Missing dependency "SAPO.Dom.Element"';
    }

    if( typeof SAPO.Dom.Event === 'undefined' ){
        throw '[Spy] :: Missing dependency "SAPO.Dom.Event"';
    }

    if( typeof SAPO.Ink.Aux === 'undefined' ){
        throw '[Spy] :: Missing dependency "SAPO.Ink.Aux"';
    }

    if( typeof SAPO.Utility.Array === 'undefined' ){
        throw '[Spy] :: Missing dependency "SAPO.Utility.Array"';
    }

    var
        Selector = SAPO.Dom.Selector,
        Css = SAPO.Dom.Css,
        Element = SAPO.Dom.Element,
        Event = SAPO.Dom.Event,
        Aux = SAPO.Ink.Aux,
        UtilArray = SAPO.Utility.Array
    ;

    var Spy = function( selector, options ){

        this._rootElement = Aux.elOrSelector(selector,'1st argument');

        /**
         * Setting default options and - if needed - overriding it with the data attributes
         */
        this._options = SAPO.extendObj({
            target: undefined,
            offset: '20px'
        }, SAPO.Dom.Element.data( this._rootElement ) );

        /**
         * In case options have been defined when creating the instance, they've precedence
         */
        this._options = SAPO.extendObj(this._options,options || {});

        this._options.target = Aux.elOrSelector( this._options.target, 'Target' );

        this._scrollTimeout = null;
        this._init();
    };

    Spy.prototype = {

        _elements: [],
        _init: function(){
            SAPO.Dom.Event.observe( document, 'scroll', this._onScroll.bindObjEvent(this) );
            this._elements.push(this._rootElement);
        },

        _onScroll: function(){

            if(
                (window.scrollY <= this._rootElement.offsetTop)
            ){
                return;
            } else {
                for( var i = 0; i < this._elements.length; i++ ){
                    if( (this._elements[i].offsetTop <= window.scrollY) && (this._elements[i] !== this._rootElement) && (this._elements[i].offsetTop > this._rootElement.offsetTop) ){
                        return;
                    }
                }
            }


            // if( this._scrollTimeout ){
            //     clearTimeout(this._scrollTimeout);
            // }
            // this._scrollTimeout = setTimeout(function(){

                var targetAnchor = null;

                UtilArray.each(
                    Selector.select(
                        'a',
                        this._options.target
                    ),function(item){

                        var comparisonValue = ( ("href" in this._rootElement) && this._rootElement.href ?
                            this._rootElement.href.substr(this._rootElement.href.indexOf('#') )
                            : '#' + this._rootElement.id
                        );

                        if( item.href.substr(item.href.indexOf('#')) === comparisonValue ){
                            Css.addClassName(Element.findUpwardsByTag(item,'li'),'active');
                        } else {
                            Css.removeClassName(Element.findUpwardsByTag(item,'li'),'active');
                        }
                    }.bindObj(this)
                );

            //     this._scrollTimeout = undefined;
            // }.bindObj(this),100);
        },

        _destroy: function(){

        }

    };

    SAPO.Ink.Spy = Spy;
})();


























