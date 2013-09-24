(function() {

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
    var paths = {
	    Ink: ( ('INK_PATH' in window) ? window.INK_PATH : window.location.protocol + '//js.ink.sapo.pt/Ink/' )
    };
    var modules = {};
    var modulesLoadOrder = [];
    var modulesRequested = {};
    var pendingRMs = [];



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
                setTimeout( function() { Ink._checkPendingRequireModules(); }, 0 );
            }
        },

        _modNameToUri: function(modName) {
            if (modName.indexOf('/') !== -1) {
                return modName;
            }
            var parts = modName.replace(/_/g, '.').split('.');
            var root = parts.shift();
            var uriPrefix = paths[root];
            if (!uriPrefix) {
                uriPrefix = './' + root + '/';
                // console.warn('Not sure where to fetch ' + root + ' modules from! Attempting ' + uriPrefix + '...');
            }
            return [uriPrefix, parts.join('/'), '/lib.js'].join('');
        },

        getPath: function(key) {
            return paths[key || 'Ink'];
        },

        setPath: function(key, rootURI) {
            paths[key] = rootURI;
        },

        /**
         * loads a javascript script in the head.
         *
         * @method loadScript
         * @param  {String}   uri  can be an http URI or a module name
         */
        loadScript: function(uri) {
            /*jshint evil:true */

            var scriptEl = document.createElement('script');
            scriptEl.setAttribute('type', 'text/javascript');
            scriptEl.setAttribute('src', this._modNameToUri(uri));

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
            var key = version ? [mod, '_', version].join('') : mod;
            return modules[key];
        },

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
            var cb = function() {
                //console.log(['createModule(', mod, ', ', ver, ', [', deps.join(', '), '], ', !!modFn, ')'].join(''));

                if (typeof mod !== 'string') {
                    throw new Error('module name must be a string!');
                }

                // validate version correctness
                if (typeof ver === 'number' || (typeof ver === 'string' && ver.length > 0)) {
                } else {
                    throw new Error('version number missing!');
                }

                var modAll = [mod, '_', ver].join('');


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
                    moduleContent._version = ver;
                }
                else if (typeof moduleContent === 'function') {
                    moduleContent.prototype._version = ver; // if constructor
                    moduleContent._version = ver;           // if regular function
                }


                // add to global namespace...
                var isInkModule = mod.indexOf('Ink.') === 0;
                var t;
                if (isInkModule) {
                    t = Ink.namespace(mod, true); // for mod 'Ink.Dom.Css', t[0] gets 'Ink.Dom' object and t[1] 'Css'
                }


                // versioned
                modules[ modAll ] = moduleContent; // in modules

                if (isInkModule) {
                    t[0][ t[1] + '_' + ver ] = moduleContent; // in namespace
                }


                // unversioned
                modules[ mod ] = moduleContent; // in modules

                if (isInkModule) {
                    if (isEmptyObject( t[0][ t[1] ] )) {
                        t[0][ t[1] ] = moduleContent; // in namespace
                    }
                    else {
                        // console.warn(['Ink.createModule ', modAll, ': module has been defined already with a different version!'].join(''));
                    }
                }


                if (this) { // there may be pending requires expecting this module, check...
                    Ink._checkPendingRequireModules();
                }
            };

            this.requireModules(deps, cb);
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
            var i, f, o, dep, mod;
            f = deps.length;
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
                dep = deps[i];
                mod = modules[dep];
                if (mod) {
                    o.args[i] = mod;
                    --o.remaining;
                    continue;
                }
                else if (modulesRequested[dep]) {
                }
                else {
                    modulesRequested[dep] = true;
                    Ink.loadScript(dep);
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
            // console.log(mlo);
            mlo = mlo.map(function(m) {
                var cutAt = m.indexOf('.');
                if (cutAt === -1) { cutAt = m.indexOf('_'); }
                var root = m.substring(0, cutAt);
                m = m.substring(cutAt + 1);
                var rootPath = Ink.getPath(root);
                return ['<script type="text/javascript" src="', rootPath, m.replace(/\./g, '/'), '/"></script>'].join('');
            });

            return mlo.join('\n');
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

})();
