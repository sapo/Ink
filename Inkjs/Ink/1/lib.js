(function(window, document) {
    /*jshint eqnull:false*/

    'use strict';

    /**
     * @module Ink_1
     */

    /**
     * global object
     *
     * @class Ink
     */


    // skip redefinition of Ink core
    if ('Ink' in window) { return; }


    // internal data

    /*
     * NOTE:
     * invoke Ink.setPath('Ink', '/Ink/'); before requiring local modules
     */
    var paths = {};
    var staticMode = ('INK_STATICMODE' in window) ? window.INK_STATICMODE : false;
    var modules = {};
    var modulePromises = {};


    window.Ink = {

        _checkPendingRequireModules: function() {
            var I, F, o, dep, mod, cb, pRMs = [];
            for (I = 0, F = pendingRMs.length; I < F; ++I) {
                o = pendingRMs[I];

                if (!o) { continue; }

                for (dep in o.left) {
                    if (o.left.hasOwnProperty(dep)) {
                        mod = modules[dep];
                        if (mod) {
                            o.args[o.left[dep] ] = mod;
                            delete o.left[dep];
                            --o.remaining;
                        }
                    }
                }

                if (o.remaining > 0) {
                    pRMs.push(o);
                }
                else {
                    cb = o.cb;
                    if (!cb) { continue; }
                    delete o.cb; // to make sure I won't call this more than once!
                    cb.apply(false, o.args);
                }
            }

            pendingRMs = pRMs;

            if (pendingRMs.length > 0) {
                setTimeout( function() { this._checkPendingRequireModules(); }, 0 );
            }
        },

        /**
         * Sets or unsets the static mode.
         *
         * Enable static mode to disable dynamic loading of modules and throw an exception.
         *
         * @method setStaticMode
         *
         * @param {Boolean} staticMode
         */
        setStaticMode: function(newStatus) {
            staticMode = newStatus;
        },
        
        /**
         * Get the path of a certain module by looking up the paths given in setPath (and ultimately the default Ink path)
         *
         * @method getPath
         * @param modName   Name of the module you want the path of.
         * @param noLib     Exclude the 'lib.js' filename
         */
        getPath: function(key, noLib) {
            var split = key.split(/[._]/g);
            var curKey;
            var i;
            var root;
            var path;
            // Look for Ink.Dom.Element.1, Ink.Dom.Element, Ink.Dom, Ink in this order.
            for (i = split.length; i >= 0; i -= 1) {
                curKey = split.slice(0, i + 1).join('.');  // See comment in setPath
                if (paths[curKey]) {
                    root = curKey;
                    break;
                }
            }
            path = paths[root || 'Ink'];
            if (path[path.length - 1] !== '/') {
                path += '/';
            }
            if (i < split.length) {
                path += split.slice(i + 1).join('/') + '/';
            }
            if (!noLib) {
                path += 'lib.js';
            }
            return path;
        },
        
        /**
         * Sets the URL path for a namespace. Use this to customize where
         * requireModules (and createModule) will load dependencies from.
         *
         * @method setPath
         *
         * @param key
         * @param rootURI
         *
         * @example
         *      Ink.setPath('Ink', 'http://my-cdn/Ink/');
         *      Ink.setPath('Lol', 'http://my-cdn/Lol/');
         *
         *      // Loads from http://my-cdn/Ink/Dom/Whatever/lib.js
         *      Ink.requireModules(['Ink.Dom.Whatever'], function () { ... });
         *      // Loads from http://my-cdn/Lol/Whatever/lib.js
         *      Ink.requireModules(['Lol.Whatever'], function () { ... });
         */
        setPath: function(key, rootURI) {
            // Replacing version separator with dot because the difference
            // between a submodule and a version doesn't matter here.
            // It would also overcomplicate the implementation of getPath
            paths[key.replace(/_/, '.')] = rootURI;
        },

        /**
         * loads a javascript script in the head.
         *
         * @method loadScript
         * @param  {String}   uri  can be an http URI or a module name
         */
        loadScript: function(uri) {
            /*jshint evil:true */

            if (staticMode) {
                throw new Error('Requiring a module to be loaded dynamically while in static mode');
            }

            if (uri.indexOf('/') === -1) {
                uri = this.getPath(uri);
            }

            var scriptEl = document.createElement('script');
            scriptEl.setAttribute('type', 'text/javascript');
            scriptEl.setAttribute('src', uri);

            // CHECK ON ALL BROWSERS
            /*if (document.readyState !== 'complete' && !document.body) {
                document.write( scriptEl.outerHTML );
            }
            else {*/
                var aHead = document.getElementsByTagName('head');
                if(aHead.length > 0) {
                    aHead[0].appendChild(scriptEl);
                }
            //}
        },

        /**
         * defines a namespace.
         *
         * @method namespace
         * @param  {String}   ns
         * @param  {Boolean}  [returnParentAndKey]
         * @return {Array|Object} if returnParentAndKey, returns [parent, lastPart], otherwise return the namespace directly
         */
        namespace: function(ns, returnParentAndKey) {
            if (!ns || !ns.length) { return null; }

            var levels = ns.split('.');
            var nsobj = window;
            var parent;

            for (var i = 0, f = levels.length; i < f; ++i) {
                nsobj[ levels[i] ] = nsobj[ levels[i] ] || {};
                parent = nsobj;
                nsobj = nsobj[ levels[i] ];
            }

            if (returnParentAndKey) {
                return [
                    parent,
                    levels[i-1]
                ];
            }

            return nsobj;
        },

        /**
         * synchronous. assumes module is loaded already!
         *
         * @method getModule
         * @param  {String}  mod
         * @param  {Number}  [version]
         * @return {Object|Function} module object / function
         */
        getModule: function(mod, version) {
            var key = version ? [mod, version].join('_') : mod;
            return modules[key];
        },

        /**
         * Return a promises/A+ object
         *
         * @method promise
         * @return A pinkySwear promise object.
         */
        promise: function () { return pinkySwear(); },
        
        /**
         * must be the wrapper around each Ink lib module for require resolution
         *
         * @method createModule
         * @param  {String}    mod      module name. parts are split with dots
         * @param  {Number}    version
         * @param  {Array}     deps     array of module names which are dependencies for the module being created
         * @param  {Function}  modFn    its arguments are the resolved dependecies, once all of them are fetched. the body of this function should return the module.
         */
        createModule: function(mod, ver, deps, modFn) { // define
            if (typeof mod !== 'string') {
                throw new Error('module name must be a string!');
            }

            // validate version correctness
            if (typeof ver === 'number' || (typeof ver === 'string' && ver.length > 0)) {
            } else {
                throw new Error('version number missing!');
            }

            var modAll = [mod, '_', ver].join('');  // 'Ink_Dom_Element_1'

            if (modules[modAll]) {
                return;  // already defined
            }

            this.requireModules(deps, function (/*..*/) {
                var module = modFn.apply(this, [].slice.call(arguments));
                modules[modAll] = module;
                this._addToNamespaceObjects(mod, ver, module);
                this._getModulePromiseFor(modAll)(true, [module]);
            });
        },

        /**
         * use this to get depencies, even if they're not loaded yet
         *
         * @method requireModules
         * @param  {Array}     deps  array of module names which are dependencies for the require function body
         * @param  {Function}  cbFn  its arguments are the resolved dependecies, once all of them are fetched
         */
        requireModules: function(deps, cbFn) { // require
            //console.log(['requireModules([', deps.join(', '), '], ', !!cbFn, ')'].join(''));

            var whenDone = this.promise();

            if (!(typeof deps === 'object' && deps.length !== undefined)) {
                throw new Error('Dependency list should be an array!');
            }
            if (typeof cbFn !== 'function') {
                throw new Error('Callback should be a function!');
            }

            var promisesForModules = new Array(deps.length);
            
            for (var m = 0, len = deps.length; m < len; m++) {
                promisesForModules[m] = this._getModulePromiseFor(deps[m]);
                if (!modules[deps[m]]) {
                    Ink.loadScript(deps[m]);
                }
            }
            
            var that = this;
            whenDone.all(promisesForModules).then(function (modules) {
                cbFn.apply(that, modules);
            });
        },

        _addToNamespaceObjects: function (mod, ver, module) {
            // TODO this is namespace();
            var modPath = mod.split('.');           // ['Ink', 'Dom', 'Element']
            var root = modPath[0];                  // 'Ink'
            var leaf = modPath[modPath.length - 1]; // 'Element'
            var leafWithVersion = leaf + '_' + ver; // 'Element_1'
            if (root === 'Ink') {
                var current = Ink
                for (var i = 1, len = modPath.length - 1; i < len; i++) {
                    if (typeof current[modPath[i]] !== 'object') {
                        current[modPath[i]] = {};
                    }
                    current = current[modPath[i]];
                }
                current[leaf] = module;
                if (!current[leafWithVersion]) {
                    current[leafWithVersion] = module;
                }
            }  
        },

        _getModulePromiseFor: function (modName) {
            if ( !(modName in modulePromises) ) {
                modulePromises[modName] = Ink.promise();
            }
            return modulePromises[modName];
        },

        /**
         * list or module names, ordered by loaded time
         *
         * @method getModulesLoadOrder
         * @return {Array} returns the order in which modules were resolved and correctly loaded
         */
        getModulesLoadOrder: function() {
            return modulesLoadOrder.slice();
        },

        /**
         * returns the markup you should have to bundle your JS resources yourself
         *
         * @return {String} scripts markup
         */
        getModuleScripts: function() {
            var mlo = this.getModulesLoadOrder();
            mlo.unshift('Ink_1');
            mlo = mlo.map(function(m) {
                return ['<script type="text/javascript" src="', Ink.getModuleURL(m), '"></script>'].join('');
            });

            return mlo.join('\n');
        },
        
        /**
         * Creates an Ink.Ext module
         *
         * Does exactly the same as createModule but creates the module in the Ink.Ext namespace
         *
         * @method createExt
         * @param {String} moduleName   Extension name
         * @param {String} version  Extension version
         * @param {Array}  dependencies Extension dependencies
         * @param {Function} modFn  Function returning the extension
         */
        createExt: function (moduleName, version, dependencies, modFn) {
            Ink.createModule('Ink.Ext.' + moduleName, version, dependencies, modFn);
        },

        /**
         * Function.prototype.bind alternative.
         * Additional arguments will be sent to the original function as prefix arguments.
         *
         * @method bind
         * @param {Function}  fn
         * @param {Object}    context
         * @return {Function}
         */
        bind: function(fn, context) {
            var args = Array.prototype.slice.call(arguments, 2);
            return function() {
                var innerArgs = Array.prototype.slice.call(arguments);
                var finalArgs = args.concat(innerArgs);
                return fn.apply(context, finalArgs);
            };
        },

        /**
         * Function.prototype.bind alternative for binding class methods
         *
         * @method bindMethod
         * @param {Object}  object
         * @param {String}  methodName
         * @return {Function}
         *  
         * @example
         *  // Build a function which calls Ink.Dom.Element.remove on an element.
         *  var removeMyElem = Ink.bindMethod(Ink.Dom.Element, 'remove', someElement);
         *
         *  removeMyElem();  // no arguments, nor Ink.Dom.Element, needed
         * @example
         *  // (comparison with using Ink.bind to the same effect).
         *  // The following two calls are equivalent
         *
         *  Ink.bind(this.remove, this, myElem);
         *  Ink.bindMethod(this, 'remove', myElem);
         */
        bindMethod: function (object, methodName) {
            return this.bind.apply(this,
                [object[methodName], object].concat([].slice.call(arguments, 2)));
        },

        /**
         * Function.prototype.bind alternative for event handlers.
         * Same as bind but keeps first argument of the call the original event.
         * Additional arguments will be sent to the original function as prefix arguments.
         *
         * @method bindEvent
         * @param {Function}  fn
         * @param {Object}    context
         * @return {Function}
         */
        bindEvent: function(fn, context) {
            var args = Array.prototype.slice.call(arguments, 2);
            return function(event) {
                var finalArgs = args.slice();
                finalArgs.unshift(event || window.event);
                return fn.apply(context, finalArgs);
            };
        },

        /**
         * alias to document.getElementById
         *
         * @method i
         * @param {String} id
         */
        i: function(id) {
            if(!id) {
                throw new Error('Ink.i => id or element must be passed');
            }
            if(typeof(id) === 'string') {
                return document.getElementById(id);
            }
            return id;
        },

        /**
         * alias to sizzle or querySelector
         *
         * @method s
         * @param {String}     rule
         * @param {DOMElement} [from]
         * @return {DOMElement}
         */
        s: function(rule, from)
        {
            if(typeof(Ink.Dom) === 'undefined' || typeof(Ink.Dom.Selector) === 'undefined') {
                throw new Error('This method requires Ink.Dom.Selector');
            }
            return Ink.Dom.Selector.select(rule, (from || document))[0] || null;
        },

        /**
         * alias to sizzle or querySelectorAll
         *
         * @method ss
         * @param {String}     rule
         * @param {DOMElement} [from]
         * @return {Array} array of DOMElements
         */
        ss: function(rule, from)
        {
            if(typeof(Ink.Dom) === 'undefined' || typeof(Ink.Dom.Selector) === 'undefined') {
                throw new Error('This method requires Ink.Dom.Selector');
            }
            return Ink.Dom.Selector.select(rule, (from || document));
        },

        /**
         * Enriches the destination object with values from source object whenever the key is missing in destination.
         *
         * More than one object can be passed as source, in which case the rightmost objects have precedence.
         *
         * @method extendObj
         * @param {Object} destination
         * @param {Object...} sources
         * @return destination object, enriched with defaults from the sources
         */
        extendObj: function(destination, source)
        {
            if (arguments.length > 2) {
                source = Ink.extendObj.apply(this, [].slice.call(arguments, 1));
            }
            if (source) {
                for (var property in source) {
                    if(Object.prototype.hasOwnProperty.call(source, property)) {
                        destination[property] = source[property];
                    }
                }
            }
            return destination;
        }

    };

    Ink.setPath('Ink',
        ('INK_PATH' in window) ? window.INK_PATH : window.location.protocol + '//js.ink.sapo.pt/Ink/');



    // TODO for debug - to detect pending stuff
    /*
    var failCount = {};   // fail count per module name
    var maxFails = 3;     // times
    var checkDelta = 0.5; //seconds

    var tmpTmr = setInterval(function() {
        var mk = Object.keys(modulesRequested);
        var l = mk.length;

        if (l > 0) {
            // console.log('** waiting for modules: ' + mk.join(', ') + ' **');

            for (var i = 0, f = mk.length, k, v; i < f; ++i) {
                k = mk[i];
                v = failCount[k];
                failCount[k] = (v === undefined) ? 1 : ++v;

                if (v >= maxFails) {
                    console.error('** Loading of module ' + k + ' failed! **');
                    delete modulesRequested[k];
                }
            }
        }
        else {
            // console.log('** Module loads complete. **');
            clearInterval(tmpTmr);
        }
    }, checkDelta*1000);
    */

/*
* PinkySwear.js - Minimalistic implementation of the Promises/A+ spec
*
* Public Domain. Use, modify and distribute it any way you like. No attribution required.
*
* NO WARRANTY EXPRESSED OR IMPLIED. USE AT YOUR OWN RISK.
*
* PinkySwear is a very small implementation of the Promises/A+ specification. After compilation with the
* Google Closure Compiler and gzipping it weighs less than 350 bytes. It is based on the implementation for
* my upcoming library Minified.js and should be perfect for embedding.
*
*
* PinkySwear has just four functions.
*
* To create a new promise in pending state, call pinkySwear():
* var promise = pinkySwear();
*
* The returned object has a Promises/A+ compatible then() implementation:
* promise.then(function(value) { alert("Success!"); }, function(value) { alert("Failure!"); });
*
*
* The promise returned by pinkySwear() is a function. To fulfill the promise, call the function with true as first argument and
* an optional array of values to pass to the then() handler. By putting more than one value in the array, you can pass more than one
* value to the then() handlers. Here an example to fulfill a promsise, this time with only one argument:
* promise(true, [42]);
*
* When the promise has been rejected, call it with false. Again, there may be more than one argument for the then() handler:
* promise(true, [6, 6, 6]);
*
* PinkySwear has two convenience functions. always(func) is the same as then(func, func) and thus will always be called, no matter what the
* promises final state is:
* promise.always(function(value) { alert("Done!"); });
*
* error(func) is the same as then(0, func), and thus the handler will only be called on error:
* promise.error(function(value) { alert("Failure!"); });
*
*
* https://github.com/timjansen/PinkySwear.js
*/


// (function(target) {  // Ink: unnecessary
    function isFunction(f,o) {
        return typeof f == 'function';
    }
    function defer(callback) {
        // Ink: unnecessary  if (typeof process != 'undefined' && process['nextTick'])
        // Ink: unnecessary     process['nextTick'](callback);
        // Ink: unnecessary  else
            window.setTimeout(callback, 0);
    }
    
    /* target[0][target[1]] = Ink: unnecessary */
    function pinkySwear() {
        var state;           // undefined/null = pending, true = fulfilled, false = rejected
        var values = [];     // an array of values as arguments for the then() handlers
        var deferred = [];   // functions to call when set() is invoked

        var set = function promise(newState, newValues) {
            if (state == null) {
                state = newState;
                values = newValues;
                defer(function() {
                    for (var i = 0; i < deferred.length; i++)
                        deferred[i]();
                });
            }
        };
        set['then'] = function(onFulfilled, onRejected) {
            var newPromise = pinkySwear();
            var callCallbacks = function() {
                try {
                    var f = (state ? onFulfilled : onRejected);
                    if (isFunction(f)) {
                        var r = f.apply(null, values);
                        if (r && isFunction(r['then']))
                            r['then'](function(value){newPromise(true,[value]);}, function(value){newPromise(false,[value]);});
                        else
                            newPromise(true, [r]);
                    }
                    else
                        newPromise(state, values);
                }
                catch (e) {
                    newPromise(false, [e]);
                }
            };
            if (state != null)
                defer(callCallbacks);
            else
                deferred.push(callCallbacks);            
            return newPromise;
        };

        // always(func) is the same as then(func, func)
        set['always'] = function(func) { return set['then'](func, func); };
        
        // Ink: A little extension for waiting for many promises at the same time
        set['all'] = function (promises) {
            if (promises.length === 0) {
                set(true, [[]]);
                return set;
            }
            var remaining = promises.length;
            var values = new Array(promises.length);
            for (var i = 0, len = promises.length; i < len; i++) {
                (function (i) {
                    promises[i].then(function (value) {
                        remaining -= 1;
                        values[i] = value
                        if (remaining === 0) {
                            set(true, [values]);
                        }
                    });
                }(i))
            }
            return set;  // Chainable, but mutable
        };

        // error(func) is the same as then(0, func)
        set['error'] = function(func) { return set['then'](0, func); };
        return set;
    }
// })(typeof module === 'undefined' ? [window, 'pinkySwear'] : [module, 'exports']);  // Ink: unnecessary here
})(window, document);
