/**
 * @module Ink.UI.LazyLoad_1
 */

Ink.createModule('Ink.UI.LazyLoad', '1', ['Ink.UI.Common_1', 'Ink.Dom.Event_1', 'Ink.Dom.Element_1'], function(UICommon, InkEvent, InkElement) {
'use strict';

var LazyLoad = function(selector, options) {
    this._init(selector, options);
};

LazyLoad.prototype = {
    /**
     * @class Ink.UI.LazyLoad_1
     * @constructor
     */
    _init: function(selector) {
        this._rootElm = UICommon.elsOrSelector(selector, 'Ink.UI.LazyLoad root element')[0] || null;

        this._options = UICommon.options({
            onLoad: ['Boolean', true],
            item: ['String', '.lazyload-item'],
            destination: ['String', 'src'], 
            delay: ['Number', 100],
            delta: ['Number', 0], // distance in px from viewport  
            image: ['Boolean', true], // default is for images but can be used to infinit scroll  
            onTouch: ['Boolean', false], // default is for images but can be used to infinit scroll  
            onBeforeLoad: ['Function', false],
            onLazyLoad: ['Function', false], // to run when image is false 
            onAfterLoad: ['Function', false],
            source: ['String', 'data-src']
        }, arguments[1] || {}, this._rootElm);

        this._sto = false;
        this._aData = [];
        this._hasEvents = false;
   
        if(this._options.onLoad) {
            this._activate();
        } 
    },

    _activate: function() 
    {
        this._getData();
        if(!this._hasEvents) {
            this._addEvents(); 
        }
        this._onScroll();
    },

    _getData: function()
    {
        var aElms = Ink.ss(this._options.item);
        var attr = null;
        for(var i=0, t=aElms.length; i < t; i++) {
            attr = aElms[i].getAttribute(this._options.source);
            if(attr !== null || !this._options.image) {
                this._aData.push({elm: aElms[i], original: attr});
            }
        }
    },

    _addEvents: function() 
    {
        this._onScrollBinded = InkEvent.throttle(Ink.bindEvent(this._onScroll, this), 400);
        if('ontouchmove' in document.documentElement && this._options.onTouch) {
            InkEvent.observe(document.documentElement, 'touchmove', this._onScrollBinded);
        } else {
            InkEvent.observe(window, 'scroll', this._onScrollBinded);
        }
        this._hasEvents = true;
    },

    _removeEvents: function() {
        if('ontouchmove' in document.documentElement && this._options.onTouch) {
            InkEvent.stopObserving(document.documentElement, 'touchmove', this._onScrollBinded);
        } else {
            InkEvent.stopObserving(window, 'scroll', this._onScrollBinded);
        }
        this._hasEvents = false;
    }, 

    _onScroll: function() {
        var total = this._aData.length; 
        var curElm = false;
        var curOffset = false;

        if(total > 0) {
            for(var i=0; i < total; i++) {
                curElm = this._aData[i];
                curOffset = InkElement.offset(curElm.elm)[1];

                if(InkElement.inViewport(curElm, { partial: true, margin: this._options.delta })) {
                    this._userCallback('onBeforeLoad', { element: curElm.elm });

                    if(this._options.image) {
                        curElm.elm.setAttribute(this._options.destination, curElm.original);
                        curElm.elm.removeAttribute(this._options.source);
                        this._aData.splice(i, 1);
                        i -= 1;
                        total = this._aData.length;
                    } else {
                        this._userCallback('onLazyLoad', { element: curElm.elm });
                    }
                    
                    this._userCallback('onAfterLoad', { element: curElm.elm });
                } else {
                    return;
                }
            }
        } else {
            this._removeEvents();
        }
    },

    /**
     * Call a callback if it exists and its `typeof` is `"function"`.
     * @method _userCallback
     * @param name {String} Callback name in this._options.
     * @private
     **/
    _userCallback: function (name) {
        if (typeof this._options[name] === 'function') {
            this._options[name].apply(this, [].slice.call(arguments, 1));
        }
    },

    // API 
    reload: function() {
        this._activate(); 
    },

    destroy: function() {
        if(this._hasEvents) {
            this._removeEvents();
        }
        UICommon.destroyComponent.call(this);
    }
};

return LazyLoad;

});
