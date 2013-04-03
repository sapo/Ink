/**
 * This is the Sticky component. Allows users to define a DOM Element to be sticky (position:fixed)
 */

(function(undefined){

    'use strict';

    /**
     * Dependencies
     */
    
    if( typeof SAPO === 'undefined' ){
        throw '[Sticky] :: Missing dependency "SAPO"';
    }

    SAPO.namespace('Ink');

    if( typeof SAPO.Dom.Selector === 'undefined' ){
        throw '[Sticky] :: Missing dependency "SAPO.Dom.Selector"';
    }

    if( typeof SAPO.Dom.Css === 'undefined' ){
        throw '[Sticky] :: Missing dependency "SAPO.Dom.Css"';
    }

    if( typeof SAPO.Dom.Element === 'undefined' ){
        throw '[Sticky] :: Missing dependency "SAPO.Dom.Element"';
    }

    if( typeof SAPO.Dom.Event === 'undefined' ){
        throw '[Sticky] :: Missing dependency "SAPO.Dom.Event"';
    }

    var Sticky = function( selector, options ){

        if( typeof selector !== 'object' && typeof selector !== 'string'){
            throw '[Sticky] :: Invalid selector defined';
        }

        if( typeof selector === 'object' ){
            this._rootElement = selector;
        } else {
            this._rootElement = SAPO.Dom.Selector.select( selector );
            if( this._rootElement.length <= 0) {
                throw "[Sticky] :: Can't find any element with the specified selector";
            }
            this._rootElement = this._rootElement[0];
        }

        /**
         * Setting default options and - if needed - overriding it with the data attributes
         */
        this._options = SAPO.extendObj({

        }, SAPO.Dom.Element.data( this._rootElement ) );

        /**
         * In case options have been defined when creating the instance, they've precedence
         */
        this._options = SAPO.extendObj(this._options,options || {});

        this._init();
    };

    Sticky.prototype = {


        _init: function(){
            SAPO.Dom.Event.observe( document, 'scroll', this._onScroll.bindObjEvent(this) );
            SAPO.Dom.Event.observe( window, 'resize', this._onResize.bindObjEvent(this) );

        },

        _onScroll: function(){


            var viewport = (document.compatMode === "CSS1Compat") ?  document.documentElement : document.body;

            if( ( (SAPO.Dom.Element.elementWidth(this._rootElement)*100)/viewport.clientWidth ) > 90 ){
                if( this._rootElement.style.position === 'fixed' ){
                    this._rootElement.style.top = this._options.originalTop + 'px';
                    this._rootElement.style.position = this._options.originalPosition;
                    this._rootElement.style.width = this._options.originalWidth;
                }
                return;
            }


            if( !this._scrollTimeout ){
                this._scrollTimeout = setTimeout(function(){

                    var computedStyle = window.getComputedStyle ? window.getComputedStyle(this._rootElement, null) : this._rootElement.currentStyle;

                    if( (this._rootElement.style.position !== 'fixed') && ( window.scrollY >= SAPO.Dom.Element.elementTop(this._rootElement) )  ){
                        /**
                         * Saving initial status
                         */
                        if( !isNaN(parseInt( computedStyle.top, 10 )) ){
                            this._options.originalTop = parseInt( computedStyle.top, 10 ) || 'auto';
                        } else {
                            this._options.originalTop = this._rootElement.offsetTop;
                        }
                        this._options.originalPosition = computedStyle.position;
                        this._options.originalWidth = parseInt(computedStyle.width,10);

                        /**
                         * Setting new values
                         */
                        this._rootElement.style.width = computedStyle.width;
                        this._rootElement.style.position = 'fixed';
                        this._rootElement.style.top = this._options.offsetTop;

                    } else if( ( window.scrollY < this._options.originalTop ) ){

                        this._rootElement.style.top = this._options.originalTop + 'px';
                        this._rootElement.style.position = this._options.originalPosition;
                        this._rootElement.style.width = this._options.originalWidth +'px';

                    } else if( ( window.scrollY+parseInt(computedStyle.height,10) ) >= (viewport.scrollHeight-parseInt(this._options.offsetBottom,10)) ){

                        this._rootElement.style.top = 'auto';
                        this._rootElement.style.bottom = this._options.offsetBottom;
                        // this._rootElement.style.position = this._options.originalPosition;
                        // this._rootElement.style.width = this._options.originalWidth;

                    } else if(
                        ( ( window.scrollY+parseInt(computedStyle.height,10) ) < (viewport.scrollHeight-parseInt(this._options.offsetBottom,10)) ) &&
                        ( this._rootElement.style.bottom === this._options.offsetBottom )
                    ){
                        this._rootElement.style.top = this._options.offsetTop;
                        this._rootElement.style.bottom = 'auto';
                    }

                    this._scrollTimeout = undefined;
                }.bindObj(this),250);
            }
        },

        _onResize: function(){

            if( !this._resizeTimeout ){
                this._resizeTimeout = setTimeout(function(){
                    if( (this._rootElement.style.position === 'fixed') ){
                        this._rootElement.style.position = this._options.originalPosition;

                        /**
                         * Saving initial status
                         */
                        this._options.originalPosition = this._rootElement.style.position || 'static';
                        this._options.originalTop = SAPO.Dom.Element.elementTop(this._rootElement);
                        this._rootElement.style.width = 'auto';  
                        // setTimeout(function(){
                            this._options.originalWidth = this._rootElement.style.width || 'auto';  
                            this._rootElement.style.width = SAPO.Dom.Element.elementWidth(this._rootElement)+'px';  
                            this._rootElement.style.position = 'fixed';
                            this._rootElement.style.top = '10px';
                        // }.bindObj(this),100);

                        this._onScroll();
                    }
                    this._resizeTimeout = undefined;
                }.bindObj(this),250);
            }

        },

        _destroy: function(){

        }

    };

    SAPO.Ink.Sticky = Sticky;
})();


























