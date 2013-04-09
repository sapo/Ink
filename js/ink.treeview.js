(function(){
    'use strict';

    /**
     * Checking dependencies
     */
    var
        dependencies = ['SAPO.Dom.Selector', 'SAPO.Dom.Event', 'SAPO.Dom.Element', 'SAPO.Dom.Css', 'SAPO.Ink.Aux'],
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

    /**
     * Making variables to ease the porting to InkJS
     */
    var
        Aux = SAPO.Ink.Aux,
        Selector = SAPO.Dom.Selector,
        Element = SAPO.Dom.Element,
        Event = SAPO.Dom.Event,
        Css = SAPO.Dom.Css
    ;

    SAPO.namespace('Ink');

    /**
     * TreeView is an Ink's component responsible for presenting a defined set of elements in a tree-like hierarchical structure
     * 
     * @param {string|DOMElement} selector CSS Selector or DOMElement
     * @param {object} options  Options' object for configuring the instance. These options can also be set through
     * data-attributes
     */
    var TreeView = function(selector, options){

        /**
         * Gets the element
         */
        if( !Aux.isDOMElement(selector) && (typeof selector !== 'string') ){
            throw '[SAPO.Ink.TreeView] :: Invalid selector';
        } else if( typeof selector === 'string' ){
            this._element = Selector.select( selector );
            if( this._element.length < 1 ){
                throw '[SAPO.Ink.TreeView] :: Selector has returned no elements';
            }
            this._element = this._element[0];
        } else {
            this._element = selector;
        }

        /**
         * Default options and they're overrided by data-attributes if any.
         * The parameters are:
         * @param {string} node Selector to define which elements are seen as nodes. Default: li
         * @param {string} child Selector to define which elements are represented as childs. Default: ul
         */
        this._options = SAPO.extendObj({
            node:   'li',
            child:  'ul'
        },Element.data(this._element));

        this._options = SAPO.extendObj(this._options, options || {});

        this._init();
    };

    TreeView.prototype = {

        /**
         * @function {void} ? Sets the necessary event handlers.
         * @return {void}
         */
        _init: function(){

            this._handlers = {
                click: this._onClick.bindObjEvent(this)
            };

            Event.observe(this._element, 'click', this._handlers.click);

            var
                nodes = Selector.select(this._options.node,this._element),
                children
            ;
            nodes.forEach(function(item){
                console.log(item);
                if( Css.hasClassName(item,'open') )
                {
                    return;
                }

                if( !Css.hasClassName(item, 'closed') ){
                    Css.addClassName(item,'closed');
                }

                children = Selector.select(this._options.child,item);
                children.forEach(function( inner_item ){
                    if( !Css.hasClassName(inner_item, 'hide-all') )
                        Css.addClassName(inner_item,'hide-all');
                }.bindObj(this));
            }.bindObj(this));

        },

        /**
         * @function {void} ? Handles the click event (as specified in the _init function)
         * @return {void}
         */
        _onClick: function(event){

            /**
             * Summary:
             * If the clicked element is a "node" as defined in the options, will check if it has any "child".
             * If so, will show it or hide it, depending on its current state. And will stop the event's default behavior.
             * If not, will execute the event's default behavior.
             * 
             */
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
                if( Css.hasClassName(child,'hide-all') ){ Css.removeClassName(child,'hide-all'); Css.addClassName(tgtEl,'open'); Css.removeClassName(tgtEl,'closed'); }
                else { Css.addClassName(child,'hide-all'); Css.removeClassName(tgtEl,'open'); Css.addClassName(tgtEl,'closed'); }
            }

        }

    };

    SAPO.Ink.TreeView = TreeView;

})();