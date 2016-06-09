/**
 * Ink Core.
 * @module Ink_1
 * This module provides the necessary methods to create and load the modules using Ink.
 */

;(function(window, document) {

    'use strict';

    // skip redefinition of Ink core
    if ('Ink' in window && typeof Ink.requireModules === 'function') { return; }


    // internal data

    /*
     * NOTE:
     * invoke Ink.setPath('Ink', '/Ink/'); before requiring local modules
     */
    var paths = {};
    var modules = {};
    var modulesLoadOrder = [];
    var modulesRequested = {};
    var pendingRMs = [];
    var modulesWaitingForDeps = {};

    var apply = Function.prototype.apply;

    // auxiliary fns
    var isEmptyObject = function(o) {
        /*jshint unused:false */
        if (typeof o !== 'object') { return false; }
        for (var k in o) {
            if (o.hasOwnProperty(k)) {
                return false;
            }
        }
        return true;
    };

    /**
     * @namespace Ink_1
     */

    window.Ink = {
        /**
         * @property {String} VERSION
         **/
        VERSION: '3.1.10',
        _checkPendingRequireModules: function() {
            var I, F, o, dep, mod, cb, pRMs = [];
            var toApply = [];
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
                } else {
                    cb = o.cb;
                    if (!cb) { continue; }
                    delete o.cb; // to make sure I won't call this more than once!
                    toApply.push([cb, o.args]);
                }
            }

            pendingRMs = pRMs;

            for (var i = 0; i < toApply.length; i++) {
                toApply[i][0].apply(false, toApply[i][1]);
            }

            if (pendingRMs.length > 0) {
                setTimeout( function() { Ink._checkPendingRequireModules(); }, 0 );
            }
        },

        /**
         * Get the full path of a module.
         * This method looks up the paths given in setPath (and ultimately the default Ink's path).
         *
         * @method getPath
         * @param {String}  key      Name of the module you want to get the path
         * @param {Boolean} [noLib] Flag to skip appending 'lib.js' to the returned path.
         *
         * @return {String} The URI to the module, according to what you added in setPath for the given `key`.
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

            if (root in paths) {
                path = paths[root];
            } else {
                return null;
            }

            if (!/\/$/.test(path)) {
                path += '/';
            }
            if (i < split.length) {
                // Add the rest of the path. For example, if we found
                // paths['Ink.Dom'] to be 'http://example.com/Ink/Dom/',
                // we now add '/Element/' to get the full path.
                path += split.slice(i + 1).join('/') + '/';
            }
            if (!noLib) {
                path += 'lib.js';
            }
            return path;
        },

        /**
         * Sets the URL path for a namespace.
         * Use this to customize where requireModules and createModule will load dependencies from.
         * This can be useful to set your own CDN for dynamic module loading or simply to change your module folder structure
         *
         * @method setPath
         *
         * @param {String} key       Module or namespace
         * @param {String} rootURI   Base URL path and schema to be appended to the module or namespace
         * @return {void}
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
         * Loads a script URL.
         * This creates a `script` tag in the `head` of the document.
         * Reports errors by listening to 'error' and 'readystatechange' events.
         *
         * @method loadScript
         * @param {String}  uri  Can be an external URL or a module name
         * @param {String}  [contentType]='text/javascript' The `type` attribute of the new script tag.
         * @return {Element} The newly created script element.
         */
        loadScript: function(uri, contentType) {
            /*jshint evil:true */

            if (uri.indexOf('/') === -1) {
                var givenUri = uri;  // For the error message
                uri = this.getPath(uri);
                if (uri === null) {
                    throw new Error('Could not load script "' + givenUri + '". ' +
                        'Path not found in the registry. Did you misspell ' +
                        'the name, or forgot to call setPath()?');
                }
            }

            var scriptEl = document.createElement('script');
            scriptEl.setAttribute('type', contentType || 'text/javascript');
            scriptEl.setAttribute('src', uri);

            if ('onerror' in scriptEl) {
                scriptEl.onerror = function () {
                    Ink.error(['Failed to load script from ', uri, '.'].join(''));
                };
            }

            var head = document.head ||
                document.getElementsByTagName('head')[0];

            if (head) {
                return head.appendChild(scriptEl);
            }
        },

        _loadLater: function (dep) {
            setTimeout(function () {
                if (modules[dep] || modulesRequested[dep] ||
                        modulesWaitingForDeps[dep]) {
                    return;
                }
                modulesRequested[dep] = true;
                Ink.loadScript(dep);
            }, 0);
        },

        /**
         * Defines a module namespace.
         *
         * @method namespace
         * @param  {String}   ns                    Namespace to define.
         * @param  {Boolean}  [returnParentAndKey]  Flag to change the return value to an array containing the namespace parent and the namespace key
         * @return {Object|Array} Returns the created namespace object
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
         * Loads a module.
         * A synchronous method to get the module from the internal registry.
         * It assumes the module is defined and loaded already!
         *
         * @method getModule
         * @param  {String}  mod        Module name
         * @param  {Number}  [version]  Version number of the module
         * @return {Object|Function}    Module object or function, depending how the module is defined
         */
        getModule: function(mod, version) {
            var key = version ? [mod, '_', version].join('') : mod;
            return modules[key];
        },

        /**
         * Creates a new module.
         * Use this to wrap your code and benefit from the module loading used throughout the Ink library
         *
         * @method createModule
         * @param  {String}    mod      Module name, separated by dots. Like Ink.Dom.Selector, Ink.UI.Modal
         * @param  {Number}    version  Version number
         * @param  {Array}     deps     Array of module names which are dependencies of the module being created. The order in which they are passed here will define the order they will be passed to the callback function.
         * @param  {Function}  modFn    The callback function to be executed when all the dependencies are resolved. The dependencies are passed as arguments, in the same order they were declared. The function itself should return the module.
         * @return {void}
         * @sample Ink_1_createModule.html
         *
         */
        createModule: function(mod, version, deps, modFn) { // define
            if (typeof mod !== 'string') {
                throw new Error('module name must be a string!');
            }

            // validate version correctness
            if (!(typeof version === 'number' || (typeof version === 'string' && version.length > 0))) {
                throw new Error('version number missing!');
            }

            var modAll = [mod, '_', version].join('');

            modulesWaitingForDeps[modAll] = true;

            var cb = function() {
                //console.log(['createModule(', mod, ', ', version, ', [', deps.join(', '), '], ', !!modFn, ')'].join(''));

                // make sure module in not loaded twice
                if (modules[modAll]) {
                    //console.warn(['Ink.createModule ', modAll, ': module has been defined already.'].join(''));
                    return;
                }


                // delete related pending tasks
                delete modulesRequested[modAll];
                delete modulesRequested[mod];


                // run module's supplied factory
                var args = Array.prototype.slice.call(arguments);
                var moduleContent = modFn.apply(window, args);
                modulesLoadOrder.push(modAll);
                // console.log('** loaded module ' + modAll + '**');


                // set version
                if (typeof moduleContent === 'object') { // Dom.Css Dom.Event
                    moduleContent._version = version;
                }
                else if (typeof moduleContent === 'function') {
                    moduleContent.prototype._version = version; // if constructor
                    moduleContent._version = version;           // if regular function
                }


                // add to global namespace...
                var isInkModule = mod.indexOf('Ink.') === 0;
                var t;
                if (isInkModule) {
                    t = Ink.namespace(mod, true); // for mod 'Ink.Dom.Css', t[0] gets 'Ink.Dom' object and t[1] 'Css'
                }


                // versioned
                modules[ modAll ] = moduleContent; // in modules
                delete modulesWaitingForDeps[ modAll ];

                if (isInkModule) {
                    t[0][ t[1] + '_' + version ] = moduleContent; // in namespace
                }


                // unversioned
                modules[ mod ] = moduleContent; // in modules

                if (isInkModule) {
                    if (isEmptyObject( t[0][ t[1] ] )) {
                        t[0][ t[1] ] = moduleContent; // in namespace
                    }
                    // else {
                        // console.warn(['Ink.createModule ', modAll, ': module has been defined already with a different version!'].join(''));
                    // }
                }


                if (this) { // there may be pending requires expecting this module, check...
                    Ink._checkPendingRequireModules();
                }
            };

            this.requireModules(deps, cb);
        },

        /**
         * Requires modules asynchronously
         * Use this to get modules, even if they're not loaded yet
         *
         * @method requireModules
         * @param  {Array}     deps  Array of module names. The order in which they are passed here will define the order they will be passed to the callback function.
         * @param  {Function}  cbFn  The callback function to be executed when all the dependencies are resolved. The dependencies are passed as arguments, in the same order they were declared.
         * @return {void}
         * @sample Ink_1_requireModules.html
         */
        requireModules: function(deps, cbFn) { // require
            //console.log(['requireModules([', deps.join(', '), '], ', !!cbFn, ')'].join(''));
            var i, f, o, dep, mod;
            f = deps && deps.length;
            o = {
                args: new Array(f),
                left: {},
                remaining: f,
                cb: cbFn
            };

            if (!(typeof deps === 'object' && deps.length !== undefined)) {
                throw new Error('Dependency list should be an array!');
            }
            if (typeof cbFn !== 'function') {
                throw new Error('Callback should be a function!');
            }

            for (i = 0; i < f; ++i) {
                if (Ink._moduleRenames[deps[i]]) {
                    Ink.warn(deps[i] + ' was renamed to ' + Ink._moduleRenames[deps[i]]);
                    dep = Ink._moduleRenames[deps[i]];
                } else {
                    dep = deps[i];
                }

                // Because trailing commas in oldIE bring us undefined values here
                if (!dep) {
                    --o.remaining;
                    continue;
                }

                mod = modules[dep];
                if (mod) {
                    o.args[i] = mod;
                    --o.remaining;
                    continue;
                }
                else if (!modulesRequested[dep]) {
                    Ink._loadLater(dep);
                }
                o.left[dep] = i;
            }

            if (o.remaining > 0) {
                pendingRMs.push(o);
            }
            else {
                cbFn.apply(true, o.args);
            }
        },

        _moduleRenames: {
            'Ink.UI.Aux_1': 'Ink.UI.Common_1'
        },

        /**
         * Lists loaded module names.
         * The list is ordered by loaded time (oldest module comes first)
         *
         * @method getModulesLoadOrder
         * @return {Array} returns the order in which modules were resolved and correctly loaded
         */
        getModulesLoadOrder: function() {
            return modulesLoadOrder.slice();
        },

        /**
         * Builds the markup needed to load the modules.
         * This method builds the script tags needed to load the currently used modules
         *
         * @method getModuleScripts
         * @uses getModulesLoadOrder
         * @return {String} The script markup
         */
        getModuleScripts: function() {
            var mlo = this.getModulesLoadOrder();
            mlo.unshift('Ink_1');
            mlo = mlo.map(function(m) {
                return ['<scr', 'ipt type="text/javascript" src="', Ink.getModuleURL(m), '"></scr', 'ipt>'].join('');
            });

            return mlo.join('\n');
        },

        /**
         * Creates an Ink.Ext module
         *
         * Does exactly the same as createModule but creates the module in the Ink.Ext namespace
         *
         * @method createExt
         * @uses createModule
         * @param {String} moduleName   Extension name
         * @param {String} version  Extension version
         * @param {Array}  dependencies Extension dependencies
         * @param {Function} modFn  Function returning the extension
         * @return {void}
         * @sample Ink_1_createExt.html
         */
        createExt: function (moduleName, version, dependencies, modFn) {
            return Ink.createModule('Ink.Ext.' + moduleName, version, dependencies, modFn);
        },

        /**
         * Function.prototype.bind alternative/fallback.
         * Creates a new function that, when called, has its this keyword set to the provided value, with a given sequence of arguments preceding any provided when the new function is called.
         *
         * @method bind
         * @param {Function}  fn        The function
         * @param {Object}    context   The value to be passed as the this parameter to the target function when the bound function is called. If used as false, it preserves the original context and just binds the arguments.
         * @param {Mixed}       [more...] Additional arguments will be sent to the original function as prefix arguments.
         * @return {Function} A copy of `fn` bound to the given `context`. Calling this function causes a call to `fn` with the new `context` and any `more` arguments.
         * @sample Ink_1_bind.html
         */
        bind: function(fn, context) {
            var args = Array.prototype.slice.call(arguments, 2);
            return function() {
                var innerArgs = Array.prototype.slice.call(arguments);
                var finalArgs = args.concat(innerArgs);
                return fn.apply(context === false ? this : context, finalArgs);
            };
        },

        /**
         * Function.prototype.bind alternative for class methods
         * See Ink.bind. The difference between `bindMethod` and `bind` is that `bindMethod` fetches a method from an object. It can be useful, for instance, to bind a function which is a property of an object returned by another function.
         *
         * @method bindMethod
         * @uses bind
         * @param {Object}  object      The object that contains the method to bind
         * @param {String}  methodName  The name of the method that will be bound
         * @param {Mixed}       [more...] Additional arguments will be sent to the new method as prefix arguments.
         * @return {Function} See Ink.bind.
         * @sample Ink_1_bindMethod.html
         */
        bindMethod: function (object, methodName) {
            return Ink.bind.apply(Ink,
                [object[methodName], object].concat([].slice.call(arguments, 2)));
        },

        /**
         * Function.prototype.bind alternative for event handlers.
         * Same as bind but keeps first argument of the call the original event.
         * Set `context` to `false` to preserve the original context of the function and just bind the arguments.
         *
         * @method bindEvent
         * @param {Function}  fn        The function
         * @param {Object}    context   The value to be passed as the this parameter to the target
         * @param {Mixed}       [more...] Additional arguments will be sent to the original function as prefix arguments
         * @return {Function} A function which will always call `fn` with the given event (or window.event, in IE) as the first argument.
         * @sample Ink_1_bindEvent.html
         */
        bindEvent: function(fn, context) {
            var args = Array.prototype.slice.call(arguments, 2);
            return function(event) {
                var finalArgs = args.slice();
                finalArgs.unshift(event || window.event);
                return fn.apply(context === false ? this : context, finalArgs);
            };
        },

        /**
         * Shorter alias to document.getElementById.
         * Just calls `document.getElementById(id)`, unless `id` happens to be an element.
         * If `id` is an element, `Ink.i` just returns it.
         *
         * You can use this in situations where you want to accept an element id, but a raw element is also okay.
         *
         * @method i
         * @param {String} id Element ID
         * @return {DOMElement} The element returned by `document.getElementById(id)` if `id` was a string, and `id` otherwise.
         * @sample Ink_1_i.html
         */
        i: function(id) {
            if(typeof(id) === 'string') {
                return document.getElementById(id) || null;
            }
            return id;
        },

        /**
         * Alias for Ink.Dom.Selector
         *
         * Using sizzle-specific selectors is NOT encouraged!
         *
         * @method ss
         * @uses Ink.Dom.Selector.select
         * @param {String}     selector          CSS3 selector string
         * @param {DOMElement} [from=document]   Context element. If set to a DOM element, the `selector` will only look for descendants of this DOM Element.
         * @return {Array} array of DOMElements
         * @sample Ink_1_ss.html
         */
        ss: function(selector, from)
        {
            if(typeof(Ink.Dom) === 'undefined' || typeof(Ink.Dom.Selector) === 'undefined') {
                throw new Error('This method requires Ink.Dom.Selector');
            }
            return Ink.Dom.Selector.select(selector, (from || document));
        },

        /**
         * Selects elements like `Ink.ss`, but only returns the first element found.
         *
         * Using sizzle-specific selectors is NOT encouraged!
         *
         * @method s
         * @uses Ink.Dom.Selector.select
         * @param {String}     selector        CSS3 selector string
         * @param {DOMElement} [from=document] Context element. If set to a DOM element, the `selector` will only look for descendants of this DOM Element.
         * @return {DOMElement} The first element found which matches the `selector`, or `null` if nothing is found.
         * @sample Ink_1_s.html
         */
        s: function(selector, from)
        {
            if(typeof(Ink.Dom) === 'undefined' || typeof(Ink.Dom.Selector) === 'undefined') {
                throw new Error('This method requires Ink.Dom.Selector');
            }
            return Ink.Dom.Selector.select(selector, (from || document))[0] || null;
        },

        /**
         * Extends an object with another
         * Copy all of the properties in one or more source objects over to the destination object, and return the destination object. It's in-order, so the last source will override properties of the same name in previous arguments.
         *
         * @method extendObj
         * @param {Object} destination  The object that will receive the new/updated properties
         * @param {Object} source       The object whose properties will be copied over to the destination object
         * @param {Object} [more...]    Additional source objects. The last source will override properties of the same name in the previous defined sources
         * @return {Object} destination object, enriched with defaults from the sources
         * @sample Ink_1_extendObj.html
         */
        extendObj: function(destination/*, source... */) {
            var sources = [].slice.call(arguments, 1);

            for (var i = 0, len = sources.length; i < len; i++) {
                if (!sources[i]) { continue; }
                for (var property in sources[i]) {
                    if(Object.prototype.hasOwnProperty.call(sources[i], property)) {
                        destination[property] = sources[i][property];
                    }
                }
            }

            return destination;
        },

        /**
         * Calls native console.log if available.
         *
         * @method log
         * @param {Mixed} [more...] Arguments to be evaluated
         * @return {void}
         * @sample Ink_1_log.html
         **/
        log: function () {
            // IE does not have console.log.apply in IE10 emulated mode
            var console = window.console;
            if (console && console.log) {
                apply.call(console.log, console, arguments);
            }
        },

        /**
         * Calls native console.warn if available.
         *
         * @method warn
         * @param {Mixed} [more...] Arguments to be evaluated
         * @return {void}
         * @sample Ink_1_warn.html
         **/
        warn: function () {
            // IE does not have console.log.apply in IE10 emulated mode
            var console = window.console;
            if (console && console.warn) {
                apply.call(console.warn, console, arguments);
            }
        },

        /**
         * Calls native console.error if available.
         *
         * @method error
         * @param {Mixed} [more...] Arguments to be evaluated
         * @return {void}
         * @sample Ink_1_error.html
         **/
        error: function () {
            // IE does not have console.log.apply in IE10 emulated mode
            var console = window.console;
            if (console && console.error) {
                apply.call(console.error, console, arguments);
            }
        }
    };


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
}(window, document));
