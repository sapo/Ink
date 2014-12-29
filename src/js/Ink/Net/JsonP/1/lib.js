/**
 * Cross Browser JsonP requests
 * @module Ink.Net.JsonP_1
 * @version 1
 */

Ink.createModule('Ink.Net.JsonP', '1', [], function() {

    'use strict';

    /**
     * Executes a JSONP request
     *
     * @class Ink.Net.JsonP
     * @constructor
     *
     * @param {String}      uri                         Request URL
     * @param {Object}      options                     Request options
     * @param {Function}    options.onSuccess           Success callback. Called with the JSONP response.
     * @param {Function}    [options.onFailure]         Failure callback. Called when there is a timeout.
     * @param {Object}      [options.failureObj]        Object to be passed as argument to failure callback
     * @param {Number}      [options.timeout]           Timeout for the request, in seconds. defaults to 10.
     * @param {Object}      [options.params]            Object with URL parameters.
     * @param {String}      [options.callbackParam]     URL parameter which gets the name of the JSONP function to call. defaults to 'jsoncallback'.
     * @param {String}      [options.randVar]           (Advanced, not recommended unless you know what you're doing) A string to append to the callback name. By default, generate a random number. Use an empty string if you already passed the correct name in the internalCallback option.
     * @param {String}      [options.internalCallback]  (Advanced) Name of the callback function stored in the Ink.Net.JsonP object (before it's prefixed).
     *
     * @sample Ink_Net_JsonP_1.html 
     */
    var JsonP = function(uri, options) {
        this.init(uri, options);
    };

    JsonP.prototype = {

        init: function(uri, options) {
            this.options = Ink.extendObj( {
                onSuccess:          undefined,
                onFailure:          undefined,
                failureObj:         {},
                timeout:            10,
                params:             {},
                callbackParam:      'jsoncallback',
                internalCallback:   '_cb',
                randVar:            false
            }, options || {});

            if(this.options.randVar !== false) {
                this.randVar = this.options.randVar;
            } else {
                this.randVar = parseInt(Math.random() * 100000, 10);
            }

            this.options.internalCallback += this.randVar;

            this.uri = uri;

            // prevent SAPO legacy onComplete - make it onSuccess
            if(typeof(this.options.onComplete) === 'function') {
                this.options.onSuccess = this.options.onComplete;
            }

            if (typeof this.uri !== 'string') {
                throw new Error('Ink.Net.JsonP: Please define an URI');
            }

            if (typeof this.options.onSuccess !== 'function') {
                throw new Error('Ink.Net.JsonP: please define a callback function on option onSuccess!');
            }

            Ink.Net.JsonP[this.options.internalCallback] = Ink.bind(function() {
                this.options.onSuccess(arguments[0]);
                this._cleanUp();
            }, this);

            this.timeout = setTimeout(Ink.bind(function () {
                this.abort();
                if(typeof this.options.onFailure === 'function'){
                    this.options.onFailure(this.options.failureObj);
                }
            }, this),
            this.options.timeout * 1000);

            this._addScriptTag();
        },

        /**
         * Abort the request, avoiding onSuccess or onFailure being called.
         * @method abort
         * @return {void}
         **/
        abort: function () {
            Ink.Net.JsonP[this.options.internalCallback] = Ink.bindMethod(this, '_cleanUp');
        },

        _addParamsToGet: function(uri, params) {
            var hasQuestionMark = uri.indexOf('?') !== -1;
            var sep, pKey, pValue, parts = [uri];

            for (pKey in params) {
                if (params.hasOwnProperty(pKey)) {
                    if (!hasQuestionMark) { sep = '?';  hasQuestionMark = true; }
                    else {                  sep = '&';                          }
                    pValue = params[pKey];
                    if (typeof pValue !== 'number' && !pValue) {    pValue = '';    }
                    parts = parts.concat([sep, pKey, '=', encodeURIComponent(pValue)]);
                }
            }

            return parts.join('');
        },

        _getScriptContainer: function() {
            return document.body ||
                document.getElementsByTagName('body')[0] ||
                document.getElementsByTagName('head')[0] ||
                document.documentElement;
        },

        _addScriptTag: function() {
            // enrich options will callback and random seed
            this.options.params[this.options.callbackParam] = 'Ink.Net.JsonP.' + this.options.internalCallback;
            this.options.params.rnd_seed = this.randVar;
            this.uri = this._addParamsToGet(this.uri, this.options.params);
            // create script tag
            this._scriptEl = document.createElement('script');
            this._scriptEl.type = 'text/javascript';
            this._scriptEl.src = this.uri;
            var scriptCtn = this._getScriptContainer();
            scriptCtn.appendChild(this._scriptEl);
        },

        _cleanUp: function () {
            if (this.timeout) {
                window.clearTimeout(this.timeout);
            }
            delete this.options.onSuccess;
            delete this.options.onFailure;
            delete Ink.Net.JsonP[this.options.internalCallback];
            this._removeScriptTag();
        },

        _removeScriptTag: function() {
            if (!this._scriptEl) { return; /* already removed */ }
            this._scriptEl.parentNode.removeChild(this._scriptEl);
            delete this._scriptEl;
        }
    };

    return JsonP;

});
