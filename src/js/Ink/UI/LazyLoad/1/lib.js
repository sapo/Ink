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
            item: ['String', '.lazyload-item'], // Use this to select and define what is to be considered an `item`.
            placeholder: ['String', null], // Placeholder value for items which are still outside the screen (in case they don't already have a value set)
            source: ['String', 'data-src'], // When an `item` is within the viewport, take the value it has in this attribute then set its `destination` attribute to it.
            destination: ['String', 'src'], // attribute which gets the value in `source` when the element is visible.
            delay: ['Number', 100], // Wait a few milliseconds before trying to load.
            delta: ['Number', 0], // Distance in px from the outside of the viewport. Elements touching within this "margin", items are considered to be inside even if they are outside the viewport limits. Can be negative if you want an element to be considered inside only when it is a certain distance into the viewport.
            image: ['Boolean', true], // Set to false to make this component do nothing to any elements and just give you the onInsideViewport callback.
            onTouch: ['Boolean', true],  // Subscribe to touch events in addition to scroll events. Useful in mobile safari because 'scroll' events aren't frequent enough.
            onInsideViewport: ['Function', false], // Called when an `item` is within the viewport. Receives `{ element }`
            onAfterAttributeChange: ['Function', false],  // (advanced) Called after `source` is copied over to `destination`. Receives `{ element }`
            autoInit: ['Boolean', true]  // Set to false if you want LazyLoad to do nothing until you call `reload()`
        }, arguments[1] || {}, this._rootElm);

        this._aData = [];
        this._hasEvents = false;
   
        if(this._options.autoInit) {
            this._activate();
        } 
    },

    _activate: function() 
    {
        this._getData();
        if(!this._hasEvents) {
            this._addEvents(); 
        }
        this._onScrollThrottled();
    },

    _getData: function()
    {
        var aElms = Ink.ss(this._options.item);
        var attr = null;
        for(var i=0, t=aElms.length; i < t; i++) {
            if (this._options.placeholder != null && !InkElement.hasAttribute(aElms[i], this._options.destination)) {
                aElms[i].setAttribute(this._options.destination, this._options.placeholder);
            }
            attr = aElms[i].getAttribute(this._options.source);
            if(attr !== null || !this._options.image) {
                this._aData.push({elm: aElms[i], original: attr});
            }
        }
    },

    _addEvents: function() 
    {
        this._onScrollThrottled = InkEvent.throttle(Ink.bindEvent(this._onScroll, this), 400);
        if('ontouchmove' in document.documentElement && this._options.onTouch) {
            InkEvent.observe(document.documentElement, 'touchmove', this._onScrollThrottled);
        } else {
            InkEvent.observe(window, 'scroll', this._onScrollThrottled);
        }
        this._hasEvents = true;
    },

    _removeEvents: function() {
        if('ontouchmove' in document.documentElement && this._options.onTouch) {
            InkEvent.stopObserving(document.documentElement, 'touchmove', this._onScrollThrottled);
        } else {
            InkEvent.stopObserving(window, 'scroll', this._onScrollThrottled);
        }
        this._hasEvents = false;
    }, 

    _onScroll: function() {
        var curElm;

        for(var i=0; i < this._aData.length; i++) {
            curElm = this._aData[i];

            if(InkElement.inViewport(curElm.elm, { partial: true, margin: this._options.delta })) {
                this._elInViewport(curElm);
                if (this._options.image) {
                    /* [todo] a seemingly unrelated option creates a branch? Some of this belongs in another module. */
                    this._aData.splice(i, 1);
                    i -= 1;
                }
            }
        }

        if (this._aData.length === 0) {
            this._removeEvents();
        }
    },

    /**
     * Called when an element is detected inside the viewport
     *
     * @method _elInViewport
     * @param {LazyLoadInternalElementData} curElm
     * @private
     **/
    _elInViewport: function (curElm) {
        this._userCallback('onInsideViewport', { element: curElm.elm });

        if(this._options.image) {
            curElm.elm.setAttribute(this._options.destination, curElm.original);
            curElm.elm.removeAttribute(this._options.source);
        }

        this._userCallback('onAfterAttributeChange', { element: curElm.elm });
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
