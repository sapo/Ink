/**
 * @author inkdev AT sapo.pt
 */

Ink.createModule('Ink.Net.JsonP', '1', [], function() {

    'use strict';

    /**
     * @module Ink.Net.JsonP_1
     */

    /**
     * This class takes care of the nitty-gritty details of doing jsonp requests: Storing
     * a callback in a globally accessible manner, waiting for the timeout or completion
     * of the request, and passing extra GET parameters to the server, is not so complicated
     * but it's boring and repetitive to code and tricky to get right.
     *
     * @class Ink.Net.JsonP
     * @constructor
     * @param {String} uri
     * @param {Object} options
     * @param {Function}  options.onSuccess         success callback
     * @param {Function}  [options.onFailure]       failure callback
     * @param {Object}    [options.failureObj]      object to be passed as argument to failure callback
     * @param {Number}    [options.timeout]         timeout for request fail, in seconds. defaults to 10
     * @param {Object}    [options.params]          object with the parameters and respective values to unfold
     * @param {String}    [options.callbackParam]   parameter to use as callback. defaults to 'jsoncallback'
     * @param {String}    [options.internalCallback] *Advanced*: name of the callback function stored in the Ink.Net.JsonP object.
     *
     * @example
     *      Ink.requireModules(['Ink.Net.JsonP_1'], function (JsonP) {
     *          var jsonp = new JsonP('http://path.to.jsonp/endpoint', {
     *              // When the JSONP response arrives, this callback is called:
     *              onSuccess: function (gameData) {
     *                  game.startGame(gameData);
     *              },
     *              // after options.timeout seconds, this callback gets called:
     *              onFailure: function () {
     *                  game.error('Could not load game data!');
     *              },
     *              timeout: 5
     *          });
     *      });
     */
    var JsonP = function(uri, options) {
        this.init(uri, options);
    };

    JsonP.prototype = {

        init: function(uri, options) {
            this.options = Ink.extendObj( {
                onSuccess:         undefined,
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
                throw 'Please define an URI';
            }

            if (typeof this.options.onSuccess !== 'function') {
                throw 'please define a callback function on option onSuccess!';
            }

            Ink.Net.JsonP[this.options.internalCallback] = Ink.bind(function() {
                window.clearTimeout(this.timeout);
                delete window.Ink.Net.JsonP[this.options.internalCallback];
                this._removeScriptTag();
                this.options.onSuccess(arguments[0]);
            }, this);

            this._addScriptTag();
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
            var headEls = document.getElementsByTagName('head');
            if (headEls.length === 0) {
                var scriptEls = document.getElementsByTagName('script');
                return scriptEls[0];
            }
            return headEls[0];
        },

        _addScriptTag: function() {
            // enrich options will callback and random seed
            this.options.params[this.options.callbackParam] = 'Ink.Net.JsonP.' + this.options.internalCallback;
            this.options.params.rnd_seed = this.randVar;
            this.uri = this._addParamsToGet(this.uri, this.options.params);
            // create script tag
            var scriptEl = document.createElement('script');
            scriptEl.type = 'text/javascript';
            scriptEl.src = this.uri;
            var scriptCtn = this._getScriptContainer();
            scriptCtn.appendChild(scriptEl);
            this.timeout = setTimeout(Ink.bind(this._requestFailed, this), (this.options.timeout * 1000));
        },

        _requestFailed : function () {
            delete Ink.Net.JsonP[this.options.internalCallback];
            this._removeScriptTag();
            if(typeof this.options.onFailure === 'function'){
                this.options.onFailure(this.options.failureObj);
            }
        },

        _removeScriptTag: function() {
            var scriptEl;
            var scriptEls = document.getElementsByTagName('script');
            var scriptUri;
            for (var i = 0, f = scriptEls.length; i < f; ++i) {
                scriptEl = scriptEls[i];
                scriptUri = scriptEl.getAttribute('src') || scriptEl.src;
                if (scriptUri !== null && scriptUri === this.uri) {
                    scriptEl.parentNode.removeChild(scriptEl);
                    return;
                }
            }
        }

    };

    return JsonP;

});
