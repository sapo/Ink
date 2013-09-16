/**
 * @author inkdev AT sapo.pt
 */

Ink.createModule('Ink.Dom.Event', 1, [], function() {

    'use strict';

    /**
     * @module Ink.Dom.Event_1
     */

    /**
     * @class Ink.Dom.Event
     */

    var Event = {

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
     *  // BEFORE
     *  InkEvent.observe(window, 'scroll', function () {
     *      ...
     *  }); // When scrolling on mobile devices or on firefox's smooth scroll
     *      // this is expensive because onscroll is called many times
     *
     *  // AFTER
     *  InkEvent.observe(window, 'scroll', InkEvent.throttle(function () {
     *      ...
     *  }, 100)); // The event handler is called only every 100ms. Problem solved.
     *
     * @example
     *  var handler = InkEvent.throttle(function () {
     *      ...
     *  }, 100);
     *
     *  InkEvent.observe(window, 'scroll', handler);
     *  InkEvent.observe(window, 'resize', handler);
     *
     *  // on resize, both the "scroll" and the "resize" events are triggered
     *  // a LOT of times. This prevents both of them being called a lot of
     *  // times when the window is being resized by a user.
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
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    return throttled.apply(that, args);
                });
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
     * @param {DOMElement|String}  element    element id or element
     * @param {String}             eventName  event name
     * @param {Object}             [memo]     metadata for the event
     */
    fire: function(element, eventName, memo)
    {
        element = Ink.i(element);
        var ev, nativeEvents;
        if(document.createEvent){
            nativeEvents = {
                "DOMActivate": true, "DOMFocusIn": true, "DOMFocusOut": true,
                "focus": true, "focusin": true, "focusout": true,
                "blur": true, "load": true, "unload": true, "abort": true,
                "error": true, "select": true, "change": true, "submit": true,
                "reset": true, "resize": true, "scroll": true,
                "click": true, "dblclick": true, "mousedown": true,
                "mouseenter": true, "mouseleave": true, "mousemove": true, "mouseover": true,
                "mouseout": true, "mouseup": true, "mousewheel": true, "wheel": true,
                "textInput": true, "keydown": true, "keypress": true, "keyup": true,
                "compositionstart": true, "compositionupdate": true, "compositionend": true,
                "DOMSubtreeModified": true, "DOMNodeInserted": true, "DOMNodeRemoved": true,
                "DOMNodeInsertedIntoDocument": true, "DOMNodeRemovedFromDocument": true,
                "DOMAttrModified": true, "DOMCharacterDataModified": true,
                "DOMAttributeNameChanged": true, "DOMElementNameChanged": true,
                "hashchange": true
            };
        } else {
            nativeEvents = {
                "onabort": true, "onactivate": true, "onafterprint": true, "onafterupdate": true,
                "onbeforeactivate": true, "onbeforecopy": true, "onbeforecut": true,
                "onbeforedeactivate": true, "onbeforeeditfocus": true, "onbeforepaste": true,
                "onbeforeprint": true, "onbeforeunload": true, "onbeforeupdate": true, "onblur": true,
                "onbounce": true, "oncellchange": true, "onchange": true, "onclick": true,
                "oncontextmenu": true, "oncontrolselect": true, "oncopy": true, "oncut": true,
                "ondataavailable": true, "ondatasetchanged": true, "ondatasetcomplete": true,
                "ondblclick": true, "ondeactivate": true, "ondrag": true, "ondragend": true,
                "ondragenter": true, "ondragleave": true, "ondragover": true, "ondragstart": true,
                "ondrop": true, "onerror": true, "onerrorupdate": true,
                "onfilterchange": true, "onfinish": true, "onfocus": true, "onfocusin": true,
                "onfocusout": true, "onhashchange": true, "onhelp": true, "onkeydown": true,
                "onkeypress": true, "onkeyup": true, "onlayoutcomplete": true,
                "onload": true, "onlosecapture": true, "onmessage": true, "onmousedown": true,
                "onmouseenter": true, "onmouseleave": true, "onmousemove": true, "onmouseout": true,
                "onmouseover": true, "onmouseup": true, "onmousewheel": true, "onmove": true,
                "onmoveend": true, "onmovestart": true, "onoffline": true, "ononline": true,
                "onpage": true, "onpaste": true, "onprogress": true, "onpropertychange": true,
                "onreadystatechange": true, "onreset": true, "onresize": true,
                "onresizeend": true, "onresizestart": true, "onrowenter": true, "onrowexit": true,
                "onrowsdelete": true, "onrowsinserted": true, "onscroll": true, "onselect": true,
                "onselectionchange": true, "onselectstart": true, "onstart": true,
                "onstop": true, "onstorage": true, "onstoragecommit": true, "onsubmit": true,
                "ontimeout": true, "onunload": true
            };
        }


        if(element !== null && element !== undefined){
            if (element === document && document.createEvent && !element.dispatchEvent) {
                element = document.documentElement;
            }

            if (document.createEvent) {
                ev = document.createEvent("HTMLEvents");
                if(typeof nativeEvents[eventName] === "undefined"){
                    ev.initEvent("dataavailable", true, true);
                } else {
                    ev.initEvent(eventName, true, true);
                }

            } else {
                ev = document.createEventObject();
                if(typeof nativeEvents["on"+eventName] === "undefined"){
                    ev.eventType = "ondataavailable";
                } else {
                    ev.eventType = "on"+eventName;
                }
            }

            ev.eventName = eventName;
            ev.memo = memo || { };

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
        }
    },

    _callbackForCustomEvents: function (element, eventName, callBack) {
        var isHashChangeInIE = eventName === "hashchange" && element.attachEvent && !window.onhashchange;
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
        if(element !== null && element !== undefined) {
            /* rare corner case: some events need a different callback to be generated */
            var callbackForCustomEvents = this._callbackForCustomEvents(element, eventName, callBack);
            if (callbackForCustomEvents) {
                callBack = callbackForCustomEvents;
                eventName = 'dataavailable';
            }

            if(element.addEventListener) {
                element.addEventListener(eventName, callBack, !!useCapture);
            } else {
                element.attachEvent('on' + eventName, callBack);
            }
            return callBack;
        }
    },

    /**
     * Attaches an event to a selector or array of elements.
     *
     * Requires Ink.Dom.Selector or a browser with Element.querySelectorAll.
     *
     * Ink.Dom.Event.observe
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
     * Remove an event attached to an element
     *
     * @method stopObserving
     * @param {DOMElement|String}  element       element id or element
     * @param {String}             eventName     event name
     * @param {Function}           callBack      callback function
     * @param {Boolean}            [useCapture]  set to true if the event was being observed with useCapture set to true as well.
     */
    stopObserving: function(element, eventName, callBack, useCapture)
    {
        element = Ink.i(element);

        if(element !== null && element !== undefined) {
            if(element.removeEventListener) {
                element.removeEventListener(eventName, callBack, !!useCapture);
            } else {
                element.detachEvent('on' + eventName, callBack);
            }
        }
    },

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
            x: ev.pageX || (ev.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft)),
            y: ev.pageY || (ev.clientY + (document.documentElement.scrollTop || document.body.scrollTop))
        };
    },

    /**
     * @method pointerX
     * @param {Object} ev event object
     * @return {Number} mouse X position
     */
    pointerX: function(ev)
    {
        return ev.pageX || (ev.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft));
    },

    /**
     * @method pointerY
     * @param {Object} ev event object
     * @return {Number} mouse Y position
     */
    pointerY: function(ev)
    {
        return ev.pageY || (ev.clientY + (document.documentElement.scrollTop || document.body.scrollTop));
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

return Event;

});
