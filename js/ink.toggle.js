(function(window,undefined){

    /**
     * Dependencies checking
     */
    if( typeof SAPO.Dom.Selector === undefined ){
        throw '[SAPO.Ink.Toggle] Missing one dependency: SAPO.Dom.Selector';
    }

    if( typeof SAPO.Dom.Css === undefined ){
        throw '[SAPO.Ink.Toggle] Missing one dependency: SAPO.Dom.Css';
    }

    if( typeof SAPO.Dom.Event === undefined ){
        throw '[SAPO.Ink.Toggle] Missing one dependency: SAPO.Dom.Event';
    }

    if( typeof SAPO.Utility.Array === undefined ){
        throw '[SAPO.Ink.Toggle] Missing one dependency: SAPO.Utility.Array';
    }
    /* --------------------------------------------------- */

    var Toggle = function( selector, options ){

        if( typeof selector !== 'string' && typeof selector !== 'object' ){
            throw '[SAPO.Ink.Toggle] Invalid CSS selector to determine the root element';
        }

        if( typeof selector === 'string' ){
            this._rootElement = SAPO.Dom.Selector.select( selector );
            if( this._rootElement.length <= 0 ){
                throw '[SAPO.Ink.Toggle] Root element not found';
            }

            this._rootElement = this._rootElement[0];
        } else {
            this._rootElement = selector;
        }

        this._options = SAPO.extendObj({
            target : undefined,
            triggerEvent: 'click',
            closeOnClick: true
        },SAPO.Dom.Element.data(this._rootElement));

        this._options = SAPO.extendObj(this._options,options || {});

        if( typeof this._options.target === 'undefined' ){
            throw '[SAPO.Ink.Toggle] Target option not defined';
        }

        this._childElement = SAPO.Dom.Selector.select( this._options.target, this._rootElement );
        if( this._childElement.length <= 0 ){
            if( this._childElement.length <= 0 ){
                this._childElement = SAPO.Dom.Selector.select( this._options.target, this._rootElement.parentNode );
            }

            if( this._childElement.length <= 0 ){
                this._childElement = SAPO.Dom.Selector.select( this._options.target );
            }

            if( this._childElement.length <= 0 ){
                return;
            }
        }
        this._childElement = this._childElement[0];

        this._init();

    };


    Toggle.prototype = {
        _init: function(){

            this._accordion = ( SAPO.Dom.Css.hasClassName(this._rootElement.parentNode,'accordion') || SAPO.Dom.Css.hasClassName(this._childElement.parentNode,'accordion') );

            SAPO.Dom.Event.observe( this._rootElement, this._options.triggerEvent, this._onTriggerEvent.bindObjEvent(this) );
            if( this._options.closeOnClick.toString() === 'true' ){
                SAPO.Dom.Event.observe( document, 'click', this._onClick.bindObjEvent(this));
            }
        },

        _onTriggerEvent: function( event ){
            SAPO.Dom.Event.stop( event );

            if( this._accordion ){
                var elms, i, accordionElement;
                if( SAPO.Dom.Css.hasClassName(this._childElement.parentNode,'accordion') ){
                    accordionElement = this._childElement.parentNode;
                } else {
                    accordionElement = this._childElement.parentNode.parentNode;
                }
                elms = SAPO.Dom.Selector.select('.toggle',accordionElement);
                for( i=0; i<elms.length; i+=1 ){
                    var
                        dataset = SAPO.Dom.Element.data( elms[i] ),
                        targetElm = SAPO.Dom.Selector.select( dataset.target,accordionElement )
                    ;
                    if( (targetElm.length > 0) && (targetElm[0] !== this._childElement) ){
                            targetElm[0].style.display = 'none';
                    }
                }
            }

            var finalClass = ( SAPO.Dom.Css.getStyle(this._childElement,'display') === 'none') ? 'show-all' : 'hide-all';
            var finalDisplay = ( SAPO.Dom.Css.getStyle(this._childElement,'display') === 'none') ? 'block' : 'none';
            SAPO.Dom.Css.removeClassName(this._childElement,'show-all');
            SAPO.Dom.Css.removeClassName(this._childElement, 'hide-all');
            SAPO.Dom.Css.addClassName(this._childElement, finalClass);
            this._childElement.style.display = finalDisplay;
        },

        _onClick: function( event ){
            var tgtEl = SAPO.Dom.Event.element(event);

            if( SAPO.Dom.Element.isAncestorOf( this._rootElement, tgtEl ) || SAPO.Dom.Element.isAncestorOf( this._childElement, tgtEl ) ){
                return;
            }

            this._dismiss( this._rootElement );
        },

        _dismiss: function( ){
            if( ( SAPO.Dom.Css.getStyle(this._childElement,'display') === 'none') ){
                return;
            }
            SAPO.Dom.Css.removeClassName(this._childElement, 'show-all');
            SAPO.Dom.Css.addClassName(this._childElement, 'hide-all');
            this._childElement.style.display = 'none';
        }
    };

    window.SAPO.Ink.Toggle = Toggle;

})(window);