Ink.createModule('Ink.UI.LazyLoad', '1', ['Ink.UI.Common_1', 'Ink.Dom.Event_1', 'Ink.Dom.Element_1'], function(UICommon, InkEvent, InkElement) {


var LazyLoad = function(selector, options) {
    this._init(selector, options);
};

LazyLoad.prototype = {
    _init: function(selector) 
    {
        this._rootElm = UICommon.elsOrSelector(selector, 'Ink.UI.LazyLoad root element')[0] || null;;

        this._options = UICommon.options({
                lazyloadOnLoad: ['Boolean', true],
                lazyloadItem: ['String', '.lazyload-item'],
                lazyloadSource: ['String', 'data-src'], 
                lazyloadDestination: ['String', 'src'], 
                lazyloadDelay: ['Number', 100],
                lazyloadDelta: ['Number', 0], // distance in px from viewport  
                lazyloadImage: ['Boolean', true], // default is for images but can be used to infinit scroll  
                lazyloadOnTouch: ['Boolean', false], // default is for images but can be used to infinit scroll  
                beforeLoadCallBack: ['Function', false],
                lazyLoadCallBack: ['Function', false], // to run when lasyloadImage is false 
                afterLoadCallBack: ['Function', false]
            }, arguments[1] || {}, this._rootElm);

        this._sto = false;
        this._aData = [];
        this._hasEvents = false;
   
        if(this._options.lazyloadOnLoad) {
            this._activate();
        } 
    },

    _activate: function() 
    {
        this._getData();
        if(!this._hasEvents) {
            this._addEvents(); 
        }
        this._runOnScroll();
    },

    _getData: function()
    {
        var aElms = Ink.ss(this._options.lazyloadItem);
        var attr = null;
        for(var i=0, t=aElms.length; i < t; i++) {
            attr = aElms[i].getAttribute(this._options.lazyloadSource);
            if(attr !== null || !this._options.lazyloadImage) {
                this._aData.push({elm: aElms[i], original: attr});
            }
        }
    },

    _addEvents: function() 
    {
        this._onScrollBinded = Ink.bindEvent(this._onScroll, this);
        if('ontouchmove' in document.documentElement && this._options.lazyloadOnTouch) {
            InkEvent.observe(document.documentElement, 'touchmove', this._onScrollBinded);
        } else {
            InkEvent.observe(window, 'scroll', this._onScrollBinded);
        }
        this._hasEvents = true;
    },

    _removeEvents: function()
    {
        if('ontouchmove' in document.documentElement && this._options.lazyloadOnTouch) {
            InkEvent.stopObserving(document.documentElement, 'touchmove', this._onScrollBinded);
        } else {
            InkEvent.stopObserving(window, 'scroll', this._onScrollBinded);
        }
        this._hasEvents = false;
    }, 

    _onScroll: function(event)
    {
                    //Ink.i('debug').innerHTML = this._sto;
        if(this._sto) {
            return; 
        }
        this._sto = setTimeout(Ink.bind(function() {
                    //Ink.i('debug').innerHTML = Math.random();
                    this._runOnScroll(); 
                    this._sto = false;
                }, this), this._options.lazyloadDelay);
    },

    _runOnScroll: function()
    {
        var viewPortY = InkElement.viewportHeight();
        var scrollY = InkElement.scrollHeight();
        //console.log(viewPortY, scrollY);
        var total = this._aData.length; 
        //console.log(total);
        var curElm = false;
        var curOffset = false;
        if(total > 0) {
            for(var i=0; i < total; i++) {
                curElm = this._aData[i];
                curOffset = InkElement.offset(curElm.elm)[1];

                //console.log(curOffset, ((viewPortY + scrollY + this._options.lazyloadDelta)));

                if(curOffset <= (viewPortY + scrollY + this._options.lazyloadDelta)) {
                    if(typeof(this._options.beforeLoadCallBack) === 'function') {
                        this._options.beforeLoadCallBack(curElm.elm);
                    }

                    if(this._options.lazyloadImage) {
                        curElm.elm.setAttribute(this._options.lazyloadDestination, curElm.original);
                        curElm.elm.removeAttribute(this._options.lazyloadSource);
                        this._aData.splice(i, 1);
                        i -= 1;
                        total = this._aData.length;
                        //console.log(curElm);
                    } else {
                        if(typeof(this._options.lazyLoadCallBack) === 'function') {
                            this._options.lazyLoadCallBack(curElm.elm);
                        }
                    }
                    
                    if(typeof(this._options.afterLoadCallBack) === 'function') {
                        this._options.afterLoadCallBack(curElm.elm);
                    }
                } else {
                    return;
                }
            }
        } else {
            this._removeEvents();
        }
    },


    // API 
    reload: function()
    {
        this._activate(); 
    },

    destroy: function()
    {
        if(this._hasEvents) {
            this._removeEvents();
        }
    },

    _debug:function(){}
};

return LazyLoad;

});
