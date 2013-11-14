/**
 * @author inkdev AT sapo.pt
 */

Ink.createModule('Ink.Dom.Event', 1, [], function() {

    'use strict';

    /**
     * Instantiate browser native events array
     */

    var nativeEvents;

    if (document.createEvent) {
        nativeEvents = 'DOMActivate DOMFocusIn DOMFocusOut focus focusin focusout blur load unload abort error select change submit reset resize scroll click dblclick mousedown mouseenter mouseleave mousemove mouseover mouseout mouseup mousewheel wheel textInput keydown keypress keyup compositionstart compositionupdate compositionend DOMSubtreeModified DOMNodeInserted DOMNodeRemoved DOMNodeInsertedIntoDocument DOMNodeRemovedFromDocument DOMAttrModified DOMCharacterDataModified DOMAttributeNameChanged DOMElementNameChanged hashchange'.split(' ');
    } else {
        nativeEvents = 'onabort onactivate onafterprint onafterupdate onbeforeactivate onbeforecopy onbeforecut onbeforedeactivate onbeforeeditfocus onbeforepaste onbeforeprint onbeforeunload onbeforeupdate onblur onbounce oncellchange onchange onclick oncontextmenu oncontrolselect oncopy oncut ondataavailable ondatasetchanged ondatasetcomplete ondblclick ondeactivate ondrag ondragend ondragenter ondragleave ondragover ondragstart ondrop onerror onerrorupdate onfilterchange onfinish onfocus onfocusin onfocusout onhashchange onhelp onkeydown onkeypress onkeyup onlayoutcomplete onload onlosecapture onmessage onmousedown onmouseenter onmouseleave onmousemove onmouseout onmouseover onmouseup onmousewheel onmove onmoveend onmovestart onoffline ononline onpage onpaste onprogress onpropertychange onreadystatechange onreset onresize onresizeend onresizestart onrowenter onrowexit onrowsdelete onrowsinserted onscroll onselect onselectionchange onselectstart onstart onstop onstorage onstoragecommit onsubmit ontimeout onunload'.split(' ');
    }

    function isNative(eventName) {
        if ([].indexOf && 0) {
            return nativeEvents.indexOf(eventName !== -1);
        } else {
            for (var i = 0, len = nativeEvents.length; i < len; i++) {
                if (nativeEvents[i] === eventName) {
                    return true;
                }
            }
            return false;
        }
    }

    function i(func) {
        return function (elem/*, ...*/) {
            elem = Ink.i(elem);
            if (elem) {
                var args = [].slice.call(arguments);
                args[0] = elem;
                return func.apply(InkEvent, args);
            } else {
                return null;
            }
        };
    }

    /**
     * @module Ink.Dom.Event_1
     */

    /**
     * @class Ink.Dom.Event
     */

    var InkEvent = {

    KEY_BACKSPACE: 8,
    KEY_TAB:       9,
    KEY_RETURN:   13,
    KEY_ESC:      27,
    KEY_LEFT:     37,
    KEY_UP:       38,
    KEY_RIGHT:    39,
    KEY_DOWN:     40,
    KEY_DELETE:   46,
    KEY_HOME:     36,
    KEY_END:      35,
    KEY_PAGEUP:   33,
    KEY_PAGEDOWN: 34,
    KEY_INSERT:   45,
    
    /**
     * Returns a function which calls `func`, waiting at least `wait`
     * milliseconds between calls. This is useful for events such as `scroll`
     * or `resize`, which can be triggered too many times per second, slowing
     * down the browser with needless function calls.
     *
     * *note:* This does not delay the first function call to the function.
     *
     * @method throttle
     * @param {Function} func   Function to call. Arguments and context are both passed.
     * @param {Number} [wait=0] Milliseconds to wait between calls.
     *
     * @example
     *  
     * Suppose you are observing the `scroll` event, but your application is lagging because `scroll` is triggered too many times.
     *
     *     // BEFORE
     *     InkEvent.observe(window, 'scroll', function () {
     *         ...
     *     }); // When scrolling on mobile devices or on firefox's smooth scroll
     *         // this is expensive because onscroll is called many times
     *
     *     // AFTER
     *     InkEvent.observe(window, 'scroll', InkEvent.throttle(function () {
     *         ...
     *     }, 100)); // The event handler is called only every 100ms. Problem solved.
     *
     * @example
     *     var handler = InkEvent.throttle(function () {
     *         ...
     *     }, 100);
     *
     *     InkEvent.observe(window, 'scroll', handler);
     *     InkEvent.observe(window, 'resize', handler);
     *
     *     // on resize, both the 'scroll' and the 'resize' events are triggered
     *     // a LOT of times. This prevents both of them being called a lot of
     *     // times when the window is being resized by a user.
     *
     **/
    throttle: function (func, wait) {
        wait = wait || 0;
        var lastCall = 0;  // Warning: This breaks on Jan 1st 1970 0:00
        var timeout;
        var throttled = function () {
            var now = +new Date();
            var timeDiff = now - lastCall;
            if (timeDiff >= wait) {
                lastCall = now;
                return func.apply(this, [].slice.call(arguments));
            } else {
                var that = this;
                var args = [].slice.call(arguments);
                if (!timeout) {
                    timeout = setTimeout(function () {
                        timeout = null;
                        return throttled.apply(that, args);
                    }, wait - timeDiff);
                }
            }
        };
        return throttled;
    },

    /**
     * Returns the target of the event object
     *
     * @method element
     * @param {Object} ev  event object
     * @return {Node} The target
     */
    element: function(ev)
    {
        var node = ev.target ||
            // IE stuff
            (ev.type === 'mouseout'   && ev.fromElement) ||
            (ev.type === 'mouseleave' && ev.fromElement) ||
            (ev.type === 'mouseover'  && ev.toElement) ||
            (ev.type === 'mouseenter' && ev.toElement) ||
            ev.srcElement ||
            null;
        return node && (node.nodeType === 3 || node.nodeType === 4) ? node.parentNode : node;
    },

    /**
     * Returns the related target of the event object
     *
     * @method relatedTarget
     * @param {Object} ev event object
     * @return {Node} The related target
     */
    relatedTarget: function(ev){
        var node = ev.relatedTarget ||
            // IE stuff
            (ev.type === 'mouseout'   && ev.toElement) ||
            (ev.type === 'mouseleave' && ev.toElement) ||
            (ev.type === 'mouseover'  && ev.fromElement) ||
            (ev.type === 'mouseenter' && ev.fromElement) ||
            null;
        return node && (node.nodeType === 3 || node.nodeType === 4) ? node.parentNode : node;
    },

    /**
     * Navigate up the DOM tree, looking for a tag with the name `elmTagName`.
     *
     * If such tag is not found, `document` is returned.
     *
     * @method findElement
     * @param {Object}  ev              event object
     * @param {String}  elmTagName      tag name to find
     * @param {Boolean} [force=false]   If this is true, never return `document`, and returns `false` instead.
     * @return {DOMElement} the first element which matches given tag name or the document element if the wanted tag is not found
     */
    findElement: function(ev, elmTagName, force)
    {
        var node = this.element(ev);
        while(true) {
            if(node.nodeName.toLowerCase() === elmTagName.toLowerCase()) {
                return node;
            } else {
                node = node.parentNode;
                if(!node) {
                    if(force) {
                        return false;
                    }
                    return document;
                }
                if(!node.parentNode){
                    if(force){ return false; }
                    return document;
                }
            }
        }
    },


    /**
     * Dispatches an event to element
     *
     * @method fire
     * @param {DOMElement|String}  element       element id or element
     * @param {String}             eventName     event name
     * @param {Object}             [eventData]        metadata for the event
     * @param {Boolean}            [_extendEventData] have the eventData argument extend the event properties. Used for testing.
     */
    fire: i(function(element, eventName, eventData, _extendEventData) {
        var ev;

        if (element === document && document.createEvent && !element.dispatchEvent) {
            element = document.documentElement;
        }

        if (document.createEvent) {
            ev = document.createEvent('HTMLEvents');
            ev.initEvent(eventName, true, true);

        } else {
            ev = document.createEventObject();
            if (!isNative('on' + eventName)) {
                ev.eventType = 'ondataavailable';
            } else {
                ev.eventType = 'on'+eventName;
            }
        }

        ev.eventName = eventName;
        if (!_extendEventData) {
            ev.eventData = eventData || { };
        } else {
            Ink.extendObj(ev, eventData);
        }

        try {
            if (document.createEvent) {
                element.dispatchEvent(ev);
            } else if(element.fireEvent){
                element.fireEvent(ev.eventType, ev);
            } else {
                return;
            }
        } catch(ex) {}

        return ev;
    }),

    _callbackForCustomEvents: function (element, eventName, callBack) {
        var isHashChangeInIE = eventName === 'hashchange' && element.attachEvent && !('onhashchange' in window);
        var isCustomEvent = eventName.indexOf(':') !== -1;
        if (isHashChangeInIE || isCustomEvent) {
            /**
             *
             * prevent that each custom event fire without any test
             * This prevents that if you have multiple custom events
             * on dataavailable to trigger the callback event if it
             * is a different custom event
             *
             */
            var argCallback = callBack;
            return Ink.bindEvent(function(ev, eventName, cb){

              //tests if it is our event and if not
              //check if it is IE and our dom:loaded was overrided (IE only supports one ondatavailable)
              //- fix /opera also supports attachEvent and was firing two events
              // if(ev.eventName === eventName || (Ink.Browser.IE && eventName === 'dom:loaded')){
              if(ev.eventName === eventName){
                //fix for FF since it loses the event in case of using a second binObjEvent
                if(window.addEventListener){
                  window.event = ev;
                }
                cb();
              }

            }, this, eventName, argCallback);
        } else {
            return null;
        }
    },

    /**
     * Attaches an event to element
     *
     * @method observe
     * @param {DOMElement|String}  element      Element id or element
     * @param {String}             eventName    Event name
     * @param {Function}           callBack     Receives event object as a
     * parameter. If you're manually firing custom events, check the
     * eventName property of the event object to make sure you're handling
     * the right event.
     * @param {Boolean}            [useCapture] Set to true to change event listening from bubbling to capture.
     * @return {Function} The event handler used. Hang on to this if you want to `stopObserving` later.
     */
    observe: function(element, eventName, callBack, useCapture)
    {
        element = Ink.i(element);
        if(element) {
            /* rare corner case: some events need a different callback to be generated */
            var callbackForCustomEvents = this._callbackForCustomEvents(element, eventName, callBack);
            if (callbackForCustomEvents) {
                callBack = callbackForCustomEvents;
                eventName = 'dataavailable';
            }

            if(element.addEventListener) {
                element.addEventListener(eventName, callBack, !!useCapture);
            } else {
                element.attachEvent('on' + eventName, (callBack = Ink.bind(callBack, element)));
            }
            return callBack;
        }
    },

    /**
     * Like observe, but listen to the event only once.
     *
     * @method observeOnce
     * @param {DOMElement|String}  element      Element id or element
     * @param {String}             eventName    Event name
     * @param {Function}           callBack     Receives event object as a
     * parameter. If you're manually firing custom events, check the
     * eventName property of the event object to make sure you're handling
     * the right event.
     * @param {Boolean}            [useCapture] Set to true to change event listening from bubbling to capture.
     * @return {Function} The event handler used. Hang on to this if you want to `stopObserving` later.
     */
    observeOnce: function (element, eventName, callBack, useCapture) {
        var onceBack = function () {
            InkEvent.stopObserving(element, eventName, onceBack);
            return callBack();
        };
        return InkEvent.observe(element, eventName, onceBack, useCapture);
    },

    /**
     * Attaches an event to a selector or array of elements.
     *
     * Requires Ink.Dom.Selector
     *
     * @method observeMulti
     * @param {Array|String} elements
     * @param ... See the `observe` function.
     * @return {Function} The used callback.
     */
    observeMulti: function (elements, eventName, callBack, useCapture) {
        if (typeof elements === 'string') {
            elements = Ink.ss(elements);
        } else if (elements instanceof Element) {
            elements = [elements];
        }
        if (!elements[0]) { return false; }

        var callbackForCustomEvents = this._callbackForCustomEvents(elements[0], eventName, callBack);
        if (callbackForCustomEvents) {
            callBack = callbackForCustomEvents;
            eventName = 'dataavailable';
        }

        for (var i = 0, len = elements.length; i < len; i++) {
            this.observe(elements[i], eventName, callBack, useCapture);
        }
        return callBack;
    },

    /**
     * Observe an event on the given element and every children which matches the selector string (if provided).
     *
     * Requires Ink.Dom.Selector if you need to use a selector.
     *
     * @method observeDelegated
     * @param {DOMElement|String} element   Element to observe.
     * @param {String}            eventName Event name to observe.
     * @param {String}            selector  Child element selector. When null, finds any element.
     * @param {Function}          callback  Callback to be called when the event is fired
     * @return {Function} The used callback, for ceasing to listen to the event later.
     **/
    observeDelegated: function (element, eventName, selector, callback) {
        return InkEvent.observe(element, eventName, function (event) {
            var fromElement = InkEvent.element(event);
            if (!fromElement || fromElement === element) { return; }

            var cursor = fromElement;

            while (cursor !== element && cursor) {
                if (Ink.Dom.Selector_1.matchesSelector(cursor, selector)) {
                    return callback.call(cursor, event);
                }
                cursor = cursor.parentNode;
            }
        });
    },

    /**
     * Remove an event attached to an element
     *
     * @method stopObserving
     * @param {DOMElement|String}  element       element id or element
     * @param {String}             eventName     event name
     * @param {Function}           callBack      callback function
     * @param {Boolean}            [useCapture]  set to true if the event was being observed with useCapture set to true as well.
     */
    stopObserving: function(element, eventName, callBack, useCapture) {
        element = Ink.i(element);

        if(element) {
            if(element.removeEventListener) {
                element.removeEventListener(eventName, callBack, !!useCapture);
            } else {
                element.detachEvent('on' + eventName, callBack);
            }
        }
    },

    /**
     * Returns whether this browser has touch events
     * @property touchEnabled
     */
    touchEnabled: 'ontouchstart' in window && window.DocumentTouch &&
        document instanceof window.DocumentTouch,

    /**
     * Subscribe to both click and touch events.
     *
     * Like just subscribing to 'click', but without the 300ms delay mobile browsers put in.
     *
     * @method pointerTap
     * @param element  {DOMElement|String}
     * @param callback {Function}
     **/
    pointerTap: i(function (element, callback) {
        if (InkEvent.touchEnabled) {
            (function () {
                var startTime;  // When did it start?
                var startPos;  // Where did it start?
                var nevermind; // Did the user move his finger too much and we should release this to be another event?
                var cb = function (evType, ev) {
                    if (ev === 'start') {
                        startTime = +new Date();
                        startPos = InkEvent.pointer(ev);
                        nevermind = false;
                    } else if (ev === 'move' && !nevermind) {
                        if (+new Date() - startTime > 1000) {
                            nevermind = true;
                        }
                        var posNow = InkEvent.pointer(ev);
                        var dist = Math.sqrt(
                            Math.abs(posNow[0] - startPos[0]) *
                            Math.abs(posNow[1] - startPos[1]));
                        if (dist > 50) {
                            nevermind = true;
                        }
                    } else if (ev === 'end' && !nevermind) {
                        callback();
                    }
                    if (!nevermind) { InkEvent.stopDefault(ev); }
                };
                InkEvent.observe(element, 'touchstart', Ink.bind(cb, false, 'start'));
                InkEvent.observe(element, 'touchmove', Ink.bind(cb, false, 'move'));
                InkEvent.observe(element, 'touchend', Ink.bind(cb, false, 'end'));
            }());
        }
        return InkEvent.observe(element, 'click', callback);
    }),

    /**
     * Stops event propagation and bubbling
     *
     * @method stop
     * @param {Object} event  event handle
     */
    stop: function(event)
    {
        if(event.cancelBubble !== null) {
            event.cancelBubble = true;
        }
        if(event.stopPropagation) {
            event.stopPropagation();
        }
        if(event.preventDefault) {
            event.preventDefault();
        }
        if(window.attachEvent) {
            event.returnValue = false;
        }
        if(event.cancel !== null) {
            event.cancel = true;
        }
    },

    /**
     * Stops event propagation
     *
     * @method stopPropagation
     * @param {Object} event  event handle
     */
    stopPropagation: function(event) {
        if(event.cancelBubble !== null) {
            event.cancelBubble = true;
        }
        if(event.stopPropagation) {
            event.stopPropagation();
        }
    },

    /**
     * Stops event default behaviour
     *
     * @method stopDefault
     * @param {Object} event  event handle
     */
    stopDefault: function(event)
    {
        if(event.preventDefault) {
            event.preventDefault();
        }
        if(window.attachEvent) {
            event.returnValue = false;
        }
        if(event.cancel !== null) {
            event.cancel = true;
        }
    },

    /**
     * @method pointer
     * @param {Object} ev event object
     * @return {Object} an object with the mouse X and Y position
     */
    pointer: function(ev)
    {
        return {
            x: this.pointerX(ev),
            y: this.pointerY(ev)
        };
    },

    /**
     * @method pointerX
     * @param {Object} ev event object
     * @return {Number} mouse X position
     */
    pointerX: function(ev)
    {
        return (ev.touches && ev.touches[0] && ev.touches[0].pageX) ||
            (ev.pageX) ||
            (ev.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft));
    },

    /**
     * @method pointerY
     * @param {Object} ev event object
     * @return {Number} mouse Y position
     */
    pointerY: function(ev)
    {
        return (ev.touches && ev.touches[0] && ev.touches[0].pageY) ||
            (ev.pageY) ||
            (ev.clientY + (document.documentElement.scrollTop || document.body.scrollTop));
    },

    /**
     * @method isLeftClick
     * @param {Object} ev  event object
     * @return {Boolean} True if the event is a left mouse click
     */
    isLeftClick: function(ev) {
        if (window.addEventListener) {
            if(ev.button === 0){
                return true;
            }
            else if(ev.type.substring(0,5) === 'touch' && ev.button === null){
                return true;
            }
        }
        else {
            if(ev.button === 1){ return true; }
        }
        return false;
    },

    /**
     * @method isRightClick
     * @param {Object} ev  event object
     * @return {Boolean} True if there is a right click on the event
     */
    isRightClick: function(ev) {
        return (ev.button === 2);
    },

    /**
     * @method isMiddleClick
     * @param {Object} ev  event object
     * @return {Boolean} True if there is a middle click on the event
     */
    isMiddleClick: function(ev) {
        if (window.addEventListener) {
            return (ev.button === 1);
        }
        else {
            return (ev.button === 4);
        }
        return false;
    },

    /**
     * Work in Progress.
     * Used in SAPO.Component.MaskedInput
     *
     * @method getCharFromKeyboardEvent
     * @param {KeyboardEvent}     event           keyboard event
     * @param {optional Boolean}  [changeCasing]  if true uppercases, if false lowercases, otherwise keeps casing
     * @return {String} character representation of pressed key combination
     */
    getCharFromKeyboardEvent: function(event, changeCasing) {
        var k = event.keyCode;
        var c = String.fromCharCode(k);

        var shiftOn = event.shiftKey;
        if (k >= 65 && k <= 90) {   // A-Z
            if (typeof changeCasing === 'boolean') {
                shiftOn = changeCasing;
            }
            return (shiftOn) ? c : c.toLowerCase();
        }
        else if (k >= 96 && k <= 105) { // numpad digits
            return String.fromCharCode( 48 + (k-96) );
        }
        switch (k) {
            case 109:   case 189:   return '-';
            case 107:   case 187:   return '+';
        }
        return c;
    },

    debug: function(){}
};

return InkEvent;

});
