(function(){
    'use strict';


    var
        dependencies = ['SAPO.Dom.Selector', 'SAPO.Dom.Event', 'SAPO.Dom.Element', 'SAPO.Dom.Css', 'Aux'],
        dependency, i, j,
        checking
    ;

    for( i = 0; i < dependencies.length; i+=1 ){
        dependency = dependencies[i].split(".");
        checking = window;
        for( j = 0; j < dependency.length; j+=1 ){
            if( !(dependency[j] in checking ) ){
                throw '[SAPO.Ink.TreeView] :: Missing dependency - ' . dependency.join(".");
            }

            checking = checking[dependency[j]];
        }
    }

    var
        Aux = SAPO.Ink.Aux,
        Selector = SAPO.Dom.Selector,
        Element = SAPO.Dom.Element,
        Event = SAPO.Dom.Event,
        Css = SAPO.Dom.Css
    ;

    SAPO.namespace('Ink');

    var TreeView = function(selector, options){

        if( !Aux.isDOMElement(selector) && (typeof selector !== 'string') ){
            throw '[SAPO.Ink.TreeView] :: Invalid selector';
        } else if( typeof 'selector' === 'string' ){
            this._element = Selector.select( selector );
            if( this._element.length < 1 ){
                throw '[SAPO.Ink.TreeView] :: Selector has returned no elements';
            }
            this._element = this._element[0];
        } else {
            this._element = selector;
        }

        this._options = SAPO.extendObj({
            node:   'li',
            child:  'ul'
        },Element.data(this._element));

        this._options = SAPO.extendObj(this._options, options || {});

        this._init();
    };

    TreeView.prototype = {

        _init: function(){

            this._handlers = {
                click: this._onClick.bindObjEvent(this)
            };

            Event.observe(this._element, 'click', this._handlers.click);

        },

        _onClick: function(event){

            var tgtEl = Event.element(event);

            if( this._options.node[0] === '.' ) {
                if( !Css.hasClassName(tgtEl,this._options.node.substr(1)) ){
                    while( (!Css.hasClassName(tgtEl,this._options.node.substr(1))) && (tgtEl.nodeName.toLowerCase() !== 'body') ){
                        tgtEl = tgtEl.parentNode;
                    }
                }
            } else if( this._options.node[0] === '#' ){
                if( tgtEl.id !== this._options.node.substr(1) ){
                    while( (tgtEl.id !== this._options.node.substr(1)) && (tgtEl.nodeName.toLowerCase() !== 'body') ){
                        tgtEl = tgtEl.parentNode;
                    }
                }
            } else {
                if( tgtEl.nodeName.toLowerCase() !== this._options.node ){
                    while( (tgtEl.nodeName.toLowerCase() !== this._options.node) && (tgtEl.nodeName.toLowerCase() !== 'body') ){
                        tgtEl = tgtEl.parentNode;
                    }
                }
            }

            if(tgtEl.nodeName.toLowerCase() === 'body'){ return; }

            var child = Selector.select(this._options.child,tgtEl);
            if( child.length > 0 ){
                Event.stop(event);
                child = child[0];
                if( Css.hasClassName(child,'hide') ){ Css.removeClassName(child,'hide'); Css.addClassName(tgtEl,'open'); Css.removeClassName(tgtEl,'closed'); }
                else { Css.addClassName(child,'hide'); Css.removeClassName(tgtEl,'open'); Css.addClassName(tgtEl,'closed'); }
            }

        }

    };

    SAPO.Ink.TreeView = TreeView;

})();