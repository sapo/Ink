/**
 * @module Ink.Util.Swipe_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.Util.Swipe', '1', ['Ink.Dom.Event_1'], function(Event) {

    'use strict';

    /**
     * Subscribe swipe gestures!
     * Supports filtering swipes be any combination of the criteria supported in the options.
     *
     * @class Ink.Util.Swipe
     * @constructor
     * @version 1
     *
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options for the Swipe detection
     *     @param {Function}  [options.callback]        Function to be called when a swipe is detected. Default is undefined.
     *     @param {Number}    [options.forceAxis]       Specify in which axis the swipe will be detected (x or y). Default is both.
     *     @param {Number}    [options.maxDist]         maximum allowed distance, in pixels
     *     @param {Number}    [options.maxDuration]     maximum allowed duration, in seconds
     *     @param {Number}    [options.minDist]         minimum allowed distance, in pixels
     *     @param {Number}    [options.minDuration]     minimum allowed duration, in seconds
     *     @param {Boolean}   [options.stopEvents]      Flag that specifies if it should stop events. Default is true.
     *     @param {Boolean}   [options.storeGesture]    Stores the gesture to be used for other purposes.
     */
    var Swipe = function(el, options) {

        this._options = Ink.extendObj({
            callback:       undefined,
            forceAxis:      undefined,       // x | y
            maxDist:        undefined,
            maxDuration:    undefined,
            minDist:        undefined,      // in pixels
            minDuration:    undefined,      // in seconds
            stopEvents:     true,
            storeGesture:   false
        }, options || {});

        this._handlers = {
            down: Ink.bindEvent(this._onDown, this),
            move: Ink.bindEvent(this._onMove, this),
            up:   Ink.bindEvent(this._onUp, this)
        };

        this._element = Ink.i(el);

        this._init();

    };

    Swipe._supported = ('ontouchstart' in document.documentElement);

    Swipe.prototype = {

        /**
         * Initialization function. Called by the constructor.
         *
         * @method _init
         * @private
         */
        _init: function() {
            var db = document.body;
            Event.observe(db, 'touchstart', this._handlers.down);
            if (this._options.storeGesture) {
                Event.observe(db, 'touchmove', this._handlers.move);
            }
            Event.observe(db, 'touchend', this._handlers.up);
            this._isOn = false;
        },

        /**
         * Function to compare/get the parent of an element.
         *
         * @method _isMeOrParent
         * @param {DOMElement} el Element to be compared with its parent
         * @param {DOMElement} parentEl Element to be compared used as reference
         * @return {DOMElement|Boolean} ParentElement of el or false in case it can't.
         * @private
         */
        _isMeOrParent: function(el, parentEl) {
            if (!el) {
                return;
            }
            do {
                if (el === parentEl) {
                    return true;
                }
                el = el.parentNode;
            } while (el);
            return false;
        },

        /**
         * MouseDown/TouchStart event handler
         *
         * @method _onDown
         * @param {EventObject} ev window.event object
         * @private
         */

        _onDown: function(ev) {
            if (event.changedTouches.length !== 1) { return; }
            if (!this._isMeOrParent(ev.target, this._element)) { return; }


            if( this._options.stopEvents === true ){
                Event.stop(ev);
            }
            ev = ev.changedTouches[0];
            this._isOn = true;
            this._target = ev.target;

            this._t0 = new Date().valueOf();
            this._p0 = [ev.pageX, ev.pageY];

            if (this._options.storeGesture) {
                this._gesture = [this._p0];
                this._time    = [0];
            }

        },

        /**
         * MouseMove/TouchMove event handler
         *
         * @method _onMove
         * @param {EventObject} ev window.event object
         * @private
         */
        _onMove: function(ev) {
            if (!this._isOn || event.changedTouches.length !== 1) { return; }
            if( this._options.stopEvents === true ){
                Event.stop(ev);
            }
            ev = ev.changedTouches[0];
            var t1 = new Date().valueOf();
            var dt = (t1 - this._t0) * 0.001;
            this._gesture.push([ev.pageX, ev.pageY]);
            this._time.push(dt);
        },

        /**
         * MouseUp/TouchEnd event handler
         *
         * @method _onUp
         * @param {EventObject} ev window.event object
         * @private
         */
        _onUp: function(ev) {
            if (!this._isOn || event.changedTouches.length !== 1) { return; }

            if (this._options.stopEvents) {
                Event.stop(ev);
            }
            ev = ev.changedTouches[0];   // TODO SHOULD CHECK IT IS THE SAME TOUCH
            this._isOn = false;

            var t1 = new Date().valueOf();
            var p1 = [ev.pageX, ev.pageY];
            var dt = (t1 - this._t0) * 0.001;
            var dr = [
                p1[0] - this._p0[0],
                p1[1] - this._p0[1]
            ];
            var dist = Math.sqrt(dr[0]*dr[0] + dr[1]*dr[1]);
            var axis = Math.abs(dr[0]) > Math.abs(dr[1]) ? 'x' : 'y';

            var o = this._options;
            if (o.minDist     && dist <   o.minDist) {     return; }
            if (o.maxDist     && dist >   o.maxDist) {     return; }
            if (o.minDuration && dt   <   o.minDuration) { return; }
            if (o.maxDuration && dt   >   o.maxDuration) { return; }
            if (o.forceAxis   && axis !== o.forceAxis) {   return; }

            var O = {
                upEvent:   ev,
                elementId: this._element.id,
                duration:  dt,
                dr:        dr,
                dist:      dist,
                axis:      axis,
                target:    this._target
            };

            if (this._options.storeGesture) {
                O.gesture = this._gesture;
                O.time    = this._time;
            }

            this._options.callback(this, O);
        }

    };

    return Swipe;

});

