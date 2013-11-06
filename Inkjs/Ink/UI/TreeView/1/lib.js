/**
 * @module Ink.UI.TreeView_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.TreeView', '1', ['Ink.UI.Common_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1'], function(Aux, Event, Css, Element, Selector, InkArray ) {
    'use strict';

    /**
     * TreeView is an Ink's component responsible for presenting a defined set of elements in a tree-like hierarchical structure
     * 
     * @class Ink.UI.TreeView
     * @constructor
     * @version 1
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *     @param {String} options.node        CSS selector that identifies the elements that are considered nodes.
     *     @param {String} options.child       CSS selector that identifies the elements that are children of those nodes.
     * @example
     *      <ul class="ink-tree-view">
     *        <li class="open"><span></span><a href="#">root</a>
     *          <ul>
     *            <li><a href="">child 1</a></li>
     *            <li><span></span><a href="">child 2</a>
     *              <ul>
     *                <li><a href="">grandchild 2a</a></li>
     *                <li><span></span><a href="">grandchild 2b</a>
     *                  <ul>
     *                    <li><a href="">grandgrandchild 1bA</a></li>
     *                    <li><a href="">grandgrandchild 1bB</a></li>
     *                  </ul>
     *                </li>
     *              </ul>
     *            </li>
     *            <li><a href="">child 3</a></li>
     *          </ul>
     *        </li>
     *      </ul>
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.TreeView_1'], function( Selector, TreeView ){
     *              var treeViewElement = Ink.s('.ink-tree-view');
     *              var treeViewObj = new TreeView( treeViewElement );
     *          });
     *      </script>
     */
    var TreeView = function(selector, options){

        /**
         * Gets the element
         */
        if( !Aux.isDOMElement(selector) && (typeof selector !== 'string') ){
            throw '[Ink.UI.TreeView] :: Invalid selector';
        } else if( typeof selector === 'string' ){
            this._element = Selector.select( selector );
            if( this._element.length < 1 ){
                throw '[Ink.UI.TreeView] :: Selector has returned no elements';
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
         * @param {string} closedClass Class to be added when a parent is closed. Default: closed
         * @param {string} openClass Class to be added when a parent is open. Default: open
         * @param {string} parentClass Class to be added to the parent element. Default: parent
         * @param {string} hideClass Class to toggle visibility. Default: hide-all
         */
        this._options = Ink.extendObj({
            node:   'li',
            child:  'ul',
            closedClass: 'closed',
            openClass: 'open',
            parentClass: 'parent',
            hideClass: 'hide-all'

        },Element.data(this._element));

        this._options = Ink.extendObj(this._options, options || {});

        this._init();
    };

    TreeView.prototype = {

        /**
         * Init function called by the constructor. Sets the necessary event handlers.
         * 
         * @method _init
         * @private
         */
        _init: function(){

            this._handlers = {
                click: Ink.bindEvent(this._onClick,this)
            };

            Event.observe(this._element, 'click', this._handlers.click);

            var
                nodes = Selector.select(this._options.node,this._element),
                children,
                is_open,
                anchor
            ;
            InkArray.each(nodes, Ink.bind(function(item){

                children = Selector.select(this._options.child,item);

                if( children.length > 0 ) {
                    is_open = Element.data(item)['open'] === 'true';
                    this._addClassNames(item, this._options.parentClass);

                    if( is_open ) {
                        this._addClassNames(item, this._options.openClass);
                    }

                    if ( this._hasClassNames(item, this._options.openClass) ) {
                        item.setAttribute('data-open', true);
                    } else {
                        this._addClassNames(item, this._options.closedClass);
                        item.setAttribute('data-open', false);
                    }
                }

                if( this._hasClassNames(item, this._options.openClass)) {
                    return;
                }

                InkArray.each(children,Ink.bind(function( inner_item ){
                    this._addClassNames(inner_item, this._options.hideClass);
                },this));
            },this));
        },

        /**
         * Helper method to support adding an array of classes to an element
         * 
         * @method _addClassNames
         * @param {Element} elm
         * @param {Array|String} classes
         * @private
         */
        _addClassNames: function(elm, classes){
            classes = ('' + classes).split(/[ ,]+/);
            InkArray.each(classes, function( current_class ){
                Css.addClassName(elm, current_class);
            });
        },

        /**
         * Helper method to check if an element has all the class names
         * 
         * @method _hasClassNames
         * @param {Element} elm
         * @param {Array|String} classes
         * @private
         */
        _hasClassNames: function(elm, classes){
            var ret = true;
            classes = ('' + classes).split(/[ ,]+/);
            InkArray.each(classes, function( current_class ){
                ret = ret && Css.hasClassName(elm, current_class);
            });
            return ret;
        },

        /**
         * Helper method to toggle every class name
         * 
         * @method _toggleClassNames
         * @param {Element} elm
         * @param {Array|String} classes
         */
        _toggleClassNames: function(elm, classes){
            classes = ('' + classes).split(/[ ,]+/);
            InkArray.each(classes, function( current_class ){
                if( Css.hasClassName(elm, current_class) ) {
                    Css.removeClassName(elm, current_class);
                } else {
                    Css.addClassName(elm, current_class);
                }
            });
        },

        /**
         * Handles the click event (as specified in the _init function).
         * 
         * @method _onClick
         * @param {Event} event
         * @private
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

            tgtEl = Element.findUpwardsBySelector(tgtEl, this._options.node);

            if(tgtEl === false){ return; }

            var child = Selector.select(this._options.child, tgtEl),
                is_open;

            if( child.length > 0 ){
                Event.stop(event);
                child = child[0];
                this._toggleClassNames(child, this._options.hideClass);
                is_open = Element.data(tgtEl)['open'] === 'true';

                if(is_open){
                    tgtEl.setAttribute('data-open', false);
                } else {
                    tgtEl.setAttribute('data-open', true);
                }
                this._toggleClassNames(tgtEl, this._options.openClass); 
                this._toggleClassNames(tgtEl, this._options.closedClass); 
            }

        }

    };

    return TreeView;

});
