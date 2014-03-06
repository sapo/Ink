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
        _init: function () {
            // TODO take only one drawer. It will be "left" or "right". There can't be a drawer instance for both sides.

            // TODO use Common.options
            this._options = Ink.extendObj({
                parentSelector:     ['String', '.ink-drawer'],
                leftDrawer:         ['String', '.left-drawer'],
                leftTrigger:        ['String', '.left-drawer-trigger'],
                rightDrawer:        ['String', '.right-drawer'],
                rightTrigger:       ['String', '.right-drawer-trigger'],
                contentDrawer:      ['String', '.content-drawer'],
                closeOnContentClick: ['Boolean', true],
                mode:               ['String', 'push'],
                sides:              ['String', 'both']
            }, arguments[0] || {});

            // make sure we have the required elements acording to the config options

            this._contentDrawers = Ink.ss(this._options.contentDrawer[1]);

            this._leftDrawer = Ink.s(this._options.leftDrawer[1]);
            this._leftTriggers = Ink.ss(this._options.leftTrigger[1]);

            this._rightDrawer = Ink.s(this._options.rightDrawer[1]);
            this._rightTriggers = Ink.ss(this._options.rightTrigger[1]);


            if(this._contentDrawers.length === 0) {
                Ink.warn( 'Ink.UI.Drawer_1: Could not find any "' +
                    this._options.contentDrawer[1] + '" elements on this page. ' +
                    'Please make sure you have at least one.' );
            }

            switch (this._options.sides) {

                case 'both':
                if( !this._leftDrawer ){
                    elNotFound(this._options.leftDrawer[1]);
                }

                if(this._leftTriggers.length === 0){
                    elNotFound(this._options.leftTrigger[1]);
                }

                if( !this._rightDrawer ){
                    elNotFound(this._options.rightDrawer[1]);
                }

                if( this._rightTriggers.length === 0 ){
                    elNotFound(this._options.rightTrigger[1]);
                }
                this._triggers =    this._options.leftTrigger[1] + ', ' + this._options.rightTrigger[1] + ', ' + this._options.contentDrawer[1];
                break;

                case 'left':
                if( !this._leftDrawer ){
                    elNotFound(this._options.leftDrawer[1]);
                }

                if(this._leftTriggers.length === 0){
                    elNotFound(this._options.leftTrigger[1]);
                }
                this._triggers = this._options.leftTrigger[1] + ', ' + this._options.contentDrawer[1];
                break;

                case 'right':
                if( !this._rightDrawer ){
                    elNotFound(this._options.rightDrawer[1]);
                }

                if( this._rightTriggers.length === 0 ){
                    elNotFound(this._options.rightTrigger[1]);
                }
                this._triggers = this._options.rightTrigger[1] + ', ' + this._options.contentDrawer[1];
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

        _onClick: function(ev){
            if(Selector.matchesSelector(ev.currentTarget,this._options.leftTrigger[1])){
                if(this._isOpen) {
                    this.close();
                } else {
                    this.open('left');
                }
            } else if(Selector.matchesSelector(ev.currentTarget,this._options.rightTrigger[1])){
                if(this._isOpen) {
                    this.close();
                } else {
                    this.open('right');
                }
            } else if(Selector.matchesSelector(ev.currentTarget,this._options.contentDrawer[1])){
                if(this._options.closeOnContentClick && this._isOpen) {
                    this.close();
                }
            }

            // TODO if clicked on a link, close it before the event goes default
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
