/**
 * @module Ink.UI.Drawer_1
 * @version 1
 */
Ink.createModule('Ink.UI.Drawer', '1', ['Ink.UI.Common_1', 'Ink.Dom.Loaded_1', 'Ink.Dom.Selector_1', 'Ink.Dom.Element_1', 'Ink.Dom.Event_1', 'Ink.Dom.Css_1'], function(Common, Loaded, Selector, Element, Event, Css) {
    'use strict';

    function elNotFound(el) {
        Ink.warn( 'Ink.UI.Drawer_1: Could not find the "' +
            el + '" element on this page. Please make sure it exists.' );
    }

    function Drawer(options) {
        this._init(options);
    }

    Drawer.prototype = {
        /**
         * @class Ink.UI.Drawer_1
         * @constructor
         *
         * @param [options] {Object} object containing the following options:
         * @param [options.parentSelector]='.ink-drawer'         {String}
         * @param [options.leftDrawer]='.left-drawer'            {String}
         * @param [options.leftTrigger]='.left-drawer-trigger'   {String}
         * @param [options.rightDrawer]='.right-drawer'          {String}
         * @param [options.rightTrigger]='.right-drawer-trigger' {String}
         * @param [options.contentDrawer]='.content-drawer'      {String}
         * @param [options.closeOnContentClick]=true             {Boolean}
         * @param [options.mode]='push'                          {String}
         * @param [options.sides]='both'                         {String}
         */
        _init: function (options) {
            this._options = Common.options({
                parentSelector:     ['String', '.ink-drawer'],
                leftDrawer:         ['String', '.left-drawer'],
                leftTrigger:        ['String', '.left-drawer-trigger'],
                rightDrawer:        ['String', '.right-drawer'],
                rightTrigger:       ['String', '.right-drawer-trigger'],
                contentDrawer:      ['String', '.content-drawer'],
                closeOnContentClick: ['Boolean', true],
                mode:               ['String', 'push'],
                sides:              ['String', 'both']
            }, null, options || {});

            // make sure we have the required elements acording to the config options

            this._contentDrawers = Ink.ss(this._options.contentDrawer);

            this._leftDrawer = Ink.s(this._options.leftDrawer);
            this._leftTriggers = Ink.ss(this._options.leftTrigger);

            this._rightDrawer = Ink.s(this._options.rightDrawer);
            this._rightTriggers = Ink.ss(this._options.rightTrigger);

            // The body might not have it
            Css.addClassName(document.body, 'ink-drawer');

            if(this._contentDrawers.length === 0) {
                Ink.warn( 'Ink.UI.Drawer_1: Could not find any "' +
                    this._options.contentDrawer + '" elements on this page. ' +
                    'Please make sure you have at least one.' );
            }

            switch (this._options.sides) {

                case 'both':
                if( !this._leftDrawer ){
                    elNotFound(this._options.leftDrawer);
                }

                if(this._leftTriggers.length === 0){
                    elNotFound(this._options.leftTrigger);
                }

                if( !this._rightDrawer ){
                    elNotFound(this._options.rightDrawer);
                }

                if( this._rightTriggers.length === 0 ){
                    elNotFound(this._options.rightTrigger);
                }
                this._triggers = this._options.leftTrigger + ', ' + this._options.rightTrigger + ', ' + this._options.contentDrawer;
                break;

                case 'left':
                if( !this._leftDrawer ){
                    elNotFound(this._options.leftDrawer);
                }

                if(this._leftTriggers.length === 0){
                    elNotFound(this._options.leftTrigger);
                }
                this._triggers = this._options.leftTrigger + ', ' + this._options.contentDrawer;
                break;

                case 'right':
                if( !this._rightDrawer ){
                    elNotFound(this._options.rightDrawer);
                }

                if( this._rightTriggers.length === 0 ){
                    elNotFound(this._options.rightTrigger);
                }
                this._triggers = this._options.rightTrigger + ', ' + this._options.contentDrawer;
                break;
            }


            this._isOpen = false;
            this._direction = undefined;

            this._handlers = {
                click:     Ink.bindEvent(this._onClick, this),
                afterTransition: Ink.bindEvent(this._afterTransition, this)
            };
            this._delay = 10;
            this._addEvents();
        },

        /**
         * Click event handler. Listens to the body's click event
         *
         * @method _onClick
         * @private
         **/
        _onClick: function(ev){
            var triggerClicked = Ink.bind(function (side) {
                if (this._isOpen) {
                    this.close();
                } else {
                    this.open(side);
                }
            }, this);

            if(Selector.matchesSelector(ev.currentTarget,this._options.leftTrigger)){
                triggerClicked('left');
            } else if(Selector.matchesSelector(ev.currentTarget,this._options.rightTrigger)){
                triggerClicked('right');
            } else if(Selector.matchesSelector(ev.currentTarget,this._options.contentDrawer)){
                if(this._options.closeOnContentClick && this._isOpen) {
                    this.close();
                }
            }

            if (Element.isLink(ev.currentTarget)) {
                this.close();
            }
        },

        _afterTransition: function(){
            if(!this._isOpen){
                if(this._direction === 'left') {
                    Css.removeClassName(this._leftDrawer,'show');
                } else {
                    Css.removeClassName(this._rightDrawer,'show');
                }
            }
        },

        _addEvents: function(){
            Event.on(document.body, 'click', this._triggers, this._handlers.click);
        },

        open: function(direction) {
            this._isOpen = true;
            this._direction = direction;

            var open = direction === 'left' ?
                this._leftDrawer :
                this._rightDrawer;

            Css.addClassName(open,'show');
            setTimeout(Ink.bind(function(){
                Css.addClassName(document.body, this._options.mode + ' '    + direction);
            },this), this._delay);
        },

        close: function() {
            this._isOpen = false;
            // TODO detect transitionEnd exists, otherwise don't rely on it
            Event.one(document.body, 'transitionend oTransitionEnd transitionend webkitTransitionEnd', this._handlers.afterTransition);
            Css.removeClassName(document.body, this._options.mode + ' ' + this._direction);
        }

    };

    return Drawer;
});
