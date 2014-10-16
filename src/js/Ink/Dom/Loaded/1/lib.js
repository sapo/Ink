/**
 * Execute code only when the DOM is loaded.
 * @module Ink.Dom.Loaded_1
 * @version 1
 */
 
Ink.createModule('Ink.Dom.Loaded', 1, [], function() {

    'use strict';

    /**
     * @namespace Ink.Dom.Loaded_1
     **/
    var Loaded = {

        /**
         * Callbacks and their contexts. Array of 2-arrays.
         *
         * []
         *
         * @attribute _contexts Array
         * @private
         * 
         */
        _contexts: [], // Callbacks' queue

        /**
         * Specify a function to execute when the DOM is fully loaded.
         *
         * @method run
         * @param {Object}   [win]=window   Window object to attach/add the event
         * @param {Function} fn             Callback function to be executed after the DOM is ready
         * @return {void}
         * @public
         * @sample Ink_Dom_Loaded_run.html 
         */
        run: function(win, fn) {
            if (!fn) {
                fn  = win;
                win = window;
            }

            var context;

            for (var i = 0, len = this._contexts.length; i < len; i++) {
                if (this._contexts[i][0] === win) {
                    context = this._contexts[i][1];
                    break;
                }
            }
            if (!context) {
                context = {
                    cbQueue: [],
                    win: win,
                    doc: win.document,
                    root: win.document.documentElement,
                    done: false,
                    top: true
                };
                context.handlers = {
                    checkState: Ink.bindEvent(this._checkState, this, context),
                    poll: Ink.bind(this._poll, this, context)
                };
                this._contexts.push(
                    [win, context]  // Javascript Objects cannot map different windows to
                                    // different values.
                );
            }

            var   ael = context.doc.addEventListener;
            context.add = ael ? 'addEventListener' : 'attachEvent';
            context.rem = ael ? 'removeEventListener' : 'detachEvent';
            context.pre = ael ? '' : 'on';
            context.det = ael ? 'DOMContentLoaded' : 'onreadystatechange';
            context.wet = context.pre + 'load';

            var csf = context.handlers.checkState;
            var alreadyLoaded = (
                /complete|loaded/.test(context.doc.readyState) &&
                context.win.location.toString() !== 'about:blank');  // https://code.google.com/p/chromium/issues/detail?id=32357

            if (alreadyLoaded){
                setTimeout(Ink.bind(function () {
                    fn.call(context.win, 'lazy');
                }, this), 0);
            } else {
                context.cbQueue.push(fn);

                context.doc[context.add]( context.det , csf );
                context.win[context.add]( context.wet , csf );

                var frameElement = 1;
                try{
                    frameElement = context.win.frameElement;
                } catch(e) {}
                if ( !ael && context.root && context.root.doScroll ) { // IE HACK
                    try {
                        context.top = !frameElement;
                    } catch(e) { }
                    if (context.top) {
                        this._poll(context);
                    }
                }
            }
        },

        /**
         * Function that will be running the callbacks after the page is loaded
         *
         * @method _checkState
         * @param {Event} event Triggered event
         * @private
         */
        _checkState: function(event, context) {
            if ( !event || (event.type === 'readystatechange' && !/complete|loaded/.test(context.doc.readyState))) {
                return;
            }
            var where = (event.type === 'load') ? context.win : context.doc;
            where[context.rem](context.pre+event.type, context.handlers.checkState, false);
            this._ready(context);
        },

        /**
         * Polls the load progress of the page to see if it has already loaded or not
         *
         * @method _poll
         * @private
         */

        /**
         * (old IE only) wait until a doScroll() call does not throw an error
         *
         * @method _poll
         * @private
         */
        _poll: function(context) {
            try {
                context.root.doScroll('left');
            } catch(e) {
                return setTimeout(context.handlers.poll, 50);
            }
            this._ready(context);
        },

        /**
         * Function that runs the callbacks from the queue when the document is ready.
         *
         * @method _ready
         * @private
         */
        _ready: function(context) {
            if (!context.done) {
                context.done = true;
                for (var i = 0; i < context.cbQueue.length; ++i) {
                    context.cbQueue[i].call(context.win);
                }
                context.cbQueue = [];
            }
        }
    };

    return Loaded;

});
