
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

    /**
     * NOTE:
     * invoke Ink.setPath('Ink', '/Ink/'); before requiring local modules
     */
    var paths = {
        Ink: window.location.protocol + '//js.ink.sapo.pt/Ink/' // TODO as soon as a production site exists, replace this default!
	//Ink: ( ('INK_PATH' in window) ? window.INK_PATH :'http://inkjs.gamblap/Ink/' )
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
            return false;
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
                    mod = modules[dep];
                    if (mod) {
                        o.args[o.left[dep] ] = mod;
                        delete o.left[dep];
                        --o.remaining;
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
                /*global console:false */

                //console.log(['createModule(', mod, ', ', ver, ', [', deps.join(', '), '], ', !!modFn, ')'].join(''));


                // validate version correctness
                if (typeof ver === 'number' || (typeof ver === 'string' && ver.length > 0)) {
                } else {
                    throw new Error('version must be passed!');
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
         * Function.prototype.bind alternative
         *
         * @function bind
         * @param {Function}  fn
         * @param {Object}    context
         * @param {any}       args*
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
         * Function.prototype.bind alternative
         * same as bind but keeps first argument of the call the original event
         *
         * @function bindEvent
         * @param {Function}  fn
         * @param {Object}    context
         * @param {any}       args*
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
         * @function i
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
         * @function s
         * @param {String}     rule
         * @param {DOMElement} [from]
         * @return {DOMElement}
         */
        s: function(rule, from)
        {
            if(typeof(Ink.Dom) === 'undefined' || typeof(Ink.Dom.Selector) === 'undefined') {
                throw new Error('This method requires Ink.Dom.Selector');
            }
            if(!document.querySelector) {
                var aRes = Ink.Dom.Selector.select(rule, (from || document));
                if(aRes.length > 0) {
                    return aRes[0];
                } else {
                    return null;
                }
            } else {
                return (from || document).querySelector(rule);
            }
        },

        /**
         * alias to sizzle or querySelectorAll
         *
         * @function ss
         * @param {String}     rule
         * @param {DOMElement} [from]
         * @return {Array} array of DOMElements
         */
        ss: function(rule, from)
        {
            if(typeof(Ink.Dom) === 'undefined' || typeof(Ink.Dom.Selector) === 'undefined') {
                throw new Error('This method requires Ink.Dom.Selector');
            }
            if(!document.querySelectorAll) {
                return Ink.Dom.Selector.select(rule, (from || document));
            } else {
                var nodeList = (from || document).querySelectorAll(rule);
                return Array.prototype.slice.call(nodeList); // to mimic selector, which returns an array
            }
        },

        /**
         * Enriches the destination object with values from source object whenever the key is missing in destination
         *
         * @function extendObj
         * @param {Object} destination
         * @param {Object} source
         * @return destination object, enriched with defaults from source
         */
        extendObj: function(destination, source)
        {
            if (source) {
                for (var property in source) {
                    if(source.hasOwnProperty(property)){
                        destination[property] = source[property];
                    }
                }
            }
            return destination;
        }

        /**
         * TODO EH?!
         */
        /*
        Browser: {
            IE: true,
            GECKO: true,
            SAFARI: true,
            OPERA: false,
            CHROME: true,
            KONQUEROR: true,
            model: '',
            version: '',
            userAgent: ''
        }
        */


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

/**
 * @author inkdev AT sapo.pt
 */

Ink.createModule('Ink.Net.Ajax', '1', [], function() {

    'use strict';

    /**
     * @module Ink.Net.Ajax_1
     */

    /**
     * Creates a new cross browser XMLHttpRequest object
     *
     * @class Ink.Net.Ajax
     * @constructor
     *
     * @param {String}  url      request url
     * @param {Object}  options  request options
     * @param {Boolean}        [options.asynchronous]    if the request should be asynchronous. true by default.
     * @param {String}         [options.method]          HTTP request method. POST by default.
     * @param {Object|String}  [options.parameters]      Request parameters which should be sent with the request
     * @param {Number}         [options.timeout]         Request timeout
     * @param {Number}         [options.delay]           Artificial delay. If request is completed in time lower than this, then wait a bit before calling the callbacks
     * @param {String}         [options.postBody]        POST request body. If not specified, it's filled with the contents from parameters
     * @param {String}         [options.contentType]     Content-type header to be sent. Defaults to 'application/x-www-form-urlencoded'
     * @param {Object}         [options.requestHeaders]  key-value pairs for additional request headers
     * @param {Function}       [options.onComplete]      Callback executed after the request is completed, no matter what happens during the request.
     * @param {Function}       [options.onSuccess]       Callback executed if the request is successful (requests with 2xx status codes)
     * @param {Function}       [options.onFailure]       Callback executed if the request fails (requests with status codes different from 2xx)
     * @param {Function}       [options.onException]     Callback executed if an exception  occurs. Receives the exception as a parameter.
     * @param {Function}       [options.onCreate]        Callback executed after object initialization but before the request is made
     * @param {Function}       [options.onInit]          Callback executed before any initialization
     * @param {Function}       [options.onTimeout]       Callback executed if the request times out
     * @param {Boolean|String} [options.evalJS]          If the request Content-type header is application/json, evaluates the response and populates responseJSON. Use 'force' if you want to force the response evaluation, no matter what Content-type it's using. Defaults to true.
     * @param {Boolean}        [options.sanitizeJSON]    Sanitize the content of responseText before evaluation
     * @param {String}         [options.xhrProxy]        URI for proxy service hosted on the same server as the web app, that can fetch documents from other domains.
     *                                             The service must pipe all input and output untouched (some input sanitization is allowed, like clearing cookies).
     *                                             e.g., requesting http://example.org/doc can become /proxy/http%3A%2F%2Fexample.org%2Fdoc The proxy service will
     *                                             be used for cross-domain requests, if set, else a network error is returned as exception.
     */
    var Ajax = function(url, options){

        // start of AjaxMock patch - uncomment to enable it
        /*var AM = SAPO.Communication.AjaxMock;
        if (AM && !options.inMock) {
            if (AM.autoRecordThisUrl && AM.autoRecordThisUrl(url)) {
                return new AM.Record(url, options);
            }
            if (AM.mockThisUrl && AM.mockThisUrl(url)) {
                return new AM.Play(url, options, true);
            }
        }*/
        // end of AjaxMock patch

        this.init(url, options);
    };

    /**
    * Options for all requests. These can then be
    * overriden for individual ones.
    */
    Ajax.globalOptions = {
        parameters: {},
        requestHeaders: {}
    };


    // IE10 does not need XDomainRequest
    var xMLHttpRequestWithCredentials = 'XMLHttpRequest' in window && 'withCredentials' in (new XMLHttpRequest());



    Ajax.prototype = {

        init: function(url, userOptions) {
            if (!url) {
                throw new Error("WRONG_ARGUMENTS_ERR");
            }
            var options = Ink.extendObj({
                asynchronous: true,
                method: 'POST',
                parameters: null,
                timeout: 0,
                delay: 0,
                postBody: '',
                contentType:  'application/x-www-form-urlencoded',
                requestHeaders: null,
                onComplete: null,
                onSuccess: null,
                onFailure: null,
                onException: null,
                onHeaders: null,
                onCreate: null,
                onInit: null,
                onTimeout: null,
                sanitizeJSON: false,
                evalJS: true,
                xhrProxy: '',
                cors: false,
                debug: false,
                useCredentials: false,
                signRequest: false
            }, Ajax.globalOptions);

            if (userOptions && typeof userOptions === 'object') {
                options = Ink.extendObj(options, userOptions);


                if (typeof userOptions.parameters === 'object') {
                    options.parameters = Ink.extendObj(Ink.extendObj({}, Ajax.globalOptions.parameters), userOptions.parameters);
                } else if (userOptions.parameters !== null) {
                    var globalParameters = this.paramsObjToStr(Ajax.globalOptions.parameters);
                    if (globalParameters) {
                        options.parameters = userOptions.parameters + '&' + globalParameters;
                    }
                }

                options.requestHeaders = Ink.extendObj({}, Ajax.globalOptions.requestHeaders);
                options.requestHeaders = Ink.extendObj(options.requestHeaders, userOptions.requestHeaders);
            }

            this.options = options;

            this.safeCall('onInit');

            var urlLocation =  document.createElementNS ?
                document.createElementNS('http://www.w3.org/1999/xhtml', 'a') :
                document.createElement('a');
            urlLocation.href = url;

            this.url = url;
            this.isHTTP = urlLocation.protocol.match(/^https?:$/i) && true;
            this.requestHasBody = options.method.search(/^get|head$/i) < 0;

            if (!this.isHTTP || location.protocol === 'widget:' || typeof window.widget === 'object') {
                this.isCrossDomain = false;
            } else {
                this.isCrossDomain = location.protocol !== urlLocation.protocol || location.host !== urlLocation.host.split(':')[0];
            }
            if(this.options.cors) {
                this.isCrossDomain = false;
            }

            this.transport = this.getTransport();

            this.request();
        },

        /**
         * Creates the appropriate XMLHttpRequest object
         *
         * @function getTransport
         * @return {Object} XMLHttpRequest object
         */
        getTransport: function()
        {
            /*global XDomainRequest:false, ActiveXObject:false */
            if (!xMLHttpRequestWithCredentials && this.options.cors && 'XDomainRequest' in window) {
                this.usingXDomainReq = true;
                return new XDomainRequest();
            }
            else if (typeof XMLHttpRequest !== 'undefined') {
                return new XMLHttpRequest();
            }
            else if (typeof ActiveXObject !== 'undefined') {
                try {
                    return new ActiveXObject('Msxml2.XMLHTTP');
                } catch (e) {
                    return new ActiveXObject('Microsoft.XMLHTTP');
                }
            } else {
                return null;
            }
        },

        /**
         * Set the necessary headers for an ajax request
         *
         * @function setHeaders
         * @param {String} url - url for the request
         */
        setHeaders: function()
        {
            if (this.transport) {
                try {
                    var headers = {
                        "Accept": "text/javascript,text/xml,application/xml,application/xhtml+xml,text/html,application/json;q=0.9,text/plain;q=0.8,video/x-mng,image/png,image/jpeg,image/gif;q=0.2,*/*;q=0.1",
                        "Accept-Language": navigator.language,
                        "X-Requested-With": "XMLHttpRequest",
                        "X-Ink-Version": "1"
                    };
                    if (this.options.cors) {
                        if (!this.options.signRequest) {
                            delete headers['X-Requested-With'];
                        }
                        delete headers['X-Ink-Version'];
                    }

                    if (this.options.requestHeaders && typeof this.options.requestHeaders === 'object') {
                        for(var headerReqName in this.options.requestHeaders) {
                            headers[headerReqName] = this.options.requestHeaders[headerReqName];
                        }
                    }

                    if (this.transport.overrideMimeType && (navigator.userAgent.match(/Gecko\/(\d{4})/) || [0,2005])[1] < 2005) {
                        headers['Connection'] = 'close';
                    }

                    for (var headerName in headers) {
                        if(headers.hasOwnProperty(headerName)) {
                            this.transport.setRequestHeader(headerName, headers[headerName]);
                        }
                    }
                } catch(e) {}
            }
        },

        /**
         * Converts an object with parameters to a querystring
         *
         * @function paramsObjToStr
         * @param {Object|String}  optParams  parameters object
         * @return {String} querystring
         */
        paramsObjToStr: function(optParams) {
            var k, m, p, a, params = [];
            if (typeof optParams === 'object') {
                for (p in optParams){
                    if (optParams.hasOwnProperty(p)) {
                        a = optParams[p];
                        if (Object.prototype.toString.call(a) === '[object Array]' && !isNaN(a.length)) {
                            for (k = 0, m = a.length; k < m; k++) {
                                params = params.concat([
                                    encodeURIComponent(p),    '=',
                                    encodeURIComponent(a[k]), '&'
                                ]);
                            }
                        }
                        else {
                            params = params.concat([
                                encodeURIComponent(p), '=',
                                encodeURIComponent(a), '&'
                            ]);
                        }
                    }
                }
                if (params.length > 0) {
                    params.pop();
                }
            }
            else
            {
                return optParams;
            }
            return params.join('');
        },

        /**
         * set the url parameters for a GET request
         *
         * @function setParams
         */
        setParams: function()
        {
            var params = null, optParams = this.options.parameters;

            if(typeof optParams === "object"){
                params = this.paramsObjToStr(optParams);
            } else {
                params = '' + optParams;
            }

            if(params){
                if(this.url.indexOf('?') > -1) {
                    this.url = this.url.split('#')[0] + '&' + params;
                } else {
                    this.url = this.url.split('#')[0] + '?' + params;
                }
            }
        },

        /**
         * Retrieves HTTP header from response
         *
         * @function getHeader
         * @param {String}  name  header name
         * @return {String} header content
         */
        getHeader: function(name)
        {
            if (this.usingXDomainReq && name === 'Content-Type') {
                return this.transport.contentType;
            }
            try{
                return this.transport.getResponseHeader(name);
            } catch(e) {
                return null;
            }
        },

        /**
         * Returns all http headers from the response
         *
         * @function getAllHeaders
         * @return {String} the headers, each separated by a newline
         */
        getAllHeaders: function()
        {
            try {
                return this.transport.getAllResponseHeaders();
            } catch(e) {
                return null;
            }
        },

        /**
         * Setup the response object
         *
         * @function getResponse
         * @return {Object} the response object
         */
        getResponse: function(){
            // setup our own stuff
            var t = this.transport,
                r = {
                    headerJSON: null,
                    responseJSON: null,
                    getHeader: this.getHeader,
                    getAllHeaders: this.getAllHeaders,
                    request: this,
                    transport: t,
                    timeTaken: new Date() - this.startTime,
                    requestedUrl: this.url
                };

            // setup things expected from the native object
            r.readyState = t.readyState;
            try { r.responseText = t.responseText; } catch(e) {}
            try { r.responseXML  = t.responseXML;  } catch(e) {}
            try { r.status       = t.status;       } catch(e) { r.status     = 0;  }
            try { r.statusText   = t.statusText;   } catch(e) { r.statusText = ''; }

            return r;
        },

        /**
         * Aborts the request if still running. No callbacks are called
         *
         * @function abort
         */
        abort: function(){
            if (this.transport) {
                clearTimeout(this.delayTimeout);
                clearTimeout(this.stoTimeout);
                try { this.transport.abort(); } catch(ex) {}
                this.finish();
            }
        },

        /**
         * Executes the state changing phase of an ajax request
         *
         * @function runStateChange
         */
        runStateChange: function()
        {
            var rs = this.transport.readyState;
            if (rs === 3) {
                if (this.isHTTP) {
                    this.safeCall('onHeaders');
                }
            } else if (rs === 4 || this.usingXDomainReq) {

                if (this.options.asynchronous && this.options.delay && (this.startTime + this.options.delay > new Date().getTime())) {
                    this.delayTimeout = setTimeout(Ink.bind(this.runStateChange, this), this.options.delay + this.startTime - new Date().getTime());
                    return;
                }

                var responseJSON,
                    responseContent = this.transport.responseText,
                    response = this.getResponse(),
                    curStatus = this.transport.status;

                if (this.isHTTP && !this.options.asynchronous) {
                    this.safeCall('onHeaders');
                }

                clearTimeout(this.stoTimeout);

                if (curStatus === 0) {
                    // Status 0 indicates network error for http requests.
                    // For http less requests, 0 is always returned.
                    if (this.isHTTP) {
                        this.safeCall('onException', this.makeError(18, 'NETWORK_ERR'));
                    } else {
                        curStatus = responseContent ? 200 : 404;
                    }
                }
                else if (curStatus === 304) {
                    curStatus = 200;
                }
                var isSuccess = this.usingXDomainReq || 200 <= curStatus && curStatus < 300;

                var headerContentType = this.getHeader('Content-Type') || '';
                if (this.options.evalJS &&
                    (headerContentType.indexOf("application/json") >= 0 || this.options.evalJS === 'force')){
                        try {
                            responseJSON = this.evalJSON(responseContent, this.sanitizeJSON);
                            if(responseJSON){
                                responseContent = response.responseJSON = responseJSON;
                            }
                        } catch(e){
                            if (isSuccess) {
                                // If the request failed, then this is perhaps an error page
                                // so don't notify error.
                                this.safeCall('onException', e);
                            }
                        }
                }

                if (this.usingXDomainReq && headerContentType.indexOf('xml') !== -1 && 'DOMParser' in window) {
                    // http://msdn.microsoft.com/en-us/library/ie/ff975278(v=vs.85).aspx
                    var mimeType;
                    switch (headerContentType) {
                        case 'application/xml':
                        case 'application/xhtml+xml':
                        case 'image/svg+xml':
                            mimeType = headerContentType;
                            break;
                        default:
                            mimeType = 'text/xml';
                    }
                    var xmlDoc = (new DOMParser()).parseFromString( this.transport.responseText, mimeType);
                    this.transport.responseXML = xmlDoc;
                    response.responseXML  = xmlDoc;
                }

                if (this.transport.responseXML !== null && response.responseJSON === null && this.transport.responseXML.xml !== ""){
                    responseContent = this.transport.responseXML;
                }

                if (curStatus || this.usingXDomainReq) {
                    if (isSuccess) {
                        this.safeCall('onSuccess', response, responseContent);
                    } else {
                        this.safeCall('onFailure', response, responseContent);
                    }
                    this.safeCall('on'+curStatus, response, responseContent);
                }
                this.finish(response, responseContent);
            }
        },

        /**
         * Last step after XHR is complete. Call onComplete and cleanup object
         *
         * @function finish
         * @param {} response
         * @param {} responseContent
         */
        finish: function(response, responseContent){
            if (response) {
                this.safeCall('onComplete', response, responseContent);
            }
            clearTimeout(this.stoTimeout);

            if (this.transport) {
                // IE6 sometimes barfs on this one
                try{ this.transport.onreadystatechange = null; } catch(e){}

                if (typeof this.transport.destroy === 'function') {
                    // Stuff for Samsung.
                    this.transport.destroy();
                }

                // Let XHR be collected.
                this.transport = null;
            }
        },

        /**
         * Safely calls a callback function.
         * Verifies that the callback is well defined and traps errors
         *
         * @function safeCall
         * @param {Function}  listener
         */
        safeCall: function(listener, first/*, second*/) {
            function rethrow(exception){
                setTimeout(function() {
                    // Rethrow exception so it'll land in
                    // the error console, firebug, whatever.
                    if (exception.message) {
                        exception.message += '\n'+(exception.stacktrace || exception.stack || '');
                    }
                    throw exception;
                }, 1);
            }
            if (typeof this.options[listener] === 'function') {
                //SAPO.safeCall(this, this.options[listener], first, second);
                //return object[listener].apply(object, [].slice.call(arguments, 2));
                try {
                    this.options[listener].apply(this, [].slice.call(arguments, 1));
                } catch(ex) {
                    rethrow(ex);
                }
            } else if (first && window.Error && (first instanceof Error)) {
                rethrow(first);
            }
        },

        /**
         * Sets new request header for the subsequent http request
         *
         * @function setRequestHeader
         * @param {String} name
         * @param {String} value
         */
        setRequestHeader: function(name, value){
            if (!this.options.requestHeaders) {
                this.options.requestHeaders = {};
            }
            this.options.requestHeaders[name] = value;
        },

        /**
         * Execute the request
         *
         * @function request
         */
        request: function()
        {
            if(this.transport) {
                var params = null;
                if(this.requestHasBody) {
                    if(this.options.postBody !== null && this.options.postBody !== '') {
                        params = this.options.postBody;
                        this.setParams();
                    } else if (this.options.parameters !== null && this.options.parameters !== ''){
                        params = this.options.parameters;
                    }

                    if (typeof params === "object" && !params.nodeType) {
                        params = this.paramsObjToStr(params);
                    } else if (typeof params !== "object" && params !== null){
                        params = '' + params;
                    }

                    if(this.options.contentType) {
                        this.setRequestHeader('Content-Type', this.options.contentType);
                    }
                } else {
                    this.setParams();
                }

                var url = this.url;
                var method = this.options.method;
                var crossDomain = this.isCrossDomain;

                if (crossDomain && this.options.xhrProxy) {
                    this.setRequestHeader('X-Url', url);
                    url = this.options.xhrProxy + encodeURIComponent(url);
                    crossDomain = false;
                }

                try {
                    this.transport.open(method, url, this.options.asynchronous);
                } catch(e) {
                    this.safeCall('onException', e);
                    return this.finish(this.getResponse(), null);
                }

                this.setHeaders();

                this.safeCall('onCreate');

                if(this.options.timeout && !isNaN(this.options.timeout)) {
                    this.stoTimeout = setTimeout(Ink.bind(function() {
                        if(this.options.onTimeout) {
                            this.safeCall('onTimeout');
                            this.abort();
                        }
                    }, this), (this.options.timeout * 1000));
                }

                if(this.options.useCredentials && !this.usingXDomainReq) {
                    this.transport.withCredentials = true;
                }

                if(this.options.asynchronous && !this.usingXDomainReq) {
                    this.transport.onreadystatechange = Ink.bind(this.runStateChange, this);
                }
                else if (this.usingXDomainReq) {
                    this.transport.onload = Ink.bind(this.runStateChange, this);
                }

                try {
                    if (crossDomain) {
                        // Need explicit handling because Mozila aborts
                        // the script and Chrome fails silently.per the spec
                        throw this.makeError(18, 'NETWORK_ERR');
                    } else {
                        this.startTime = new Date().getTime();
                        this.transport.send(params);
                    }
                } catch(e) {
                    this.safeCall('onException', e);
                    return this.finish(this.getResponse(), null);
                }

                if(!this.options.asynchronous) {
                    this.runStateChange();
                }
            }
        },

        /**
         * Returns new exception object that can be thrown
         *
         * @function makeError
         * @param code
         * @param message
         * @returns {Object}
         */
        makeError: function(code, message){
            if (typeof Error !== 'function') {
                return {code: code, message: message};
            }
            var e = new Error(message);
            e.code = code;
            return e;
        },

        /**
         * Checks if a given string is valid JSON
         *
         * @function isJSON
         * @param {String} str  String to be evaluated
         * @return {Boolean} True if the string is valid JSON
         */
        isJSON: function(str)
        {
            if (typeof str !== "string" || !str){ return false; }
            str = str.replace(/\\./g, '@').replace(/"[^"\\\n\r]*"/g, '');
            return (/^[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]*$/).test(str);
        },

        /**
         * Evaluates a given string as JSON
         *
         * @function evalJSON
         * @param {String}  str       String to be evaluated
         * @param {Boolean} sanitize  whether to sanitize the content or not
         * @return {Object} Json content as an object
         */
        evalJSON: function(strJSON, sanitize)
        {
            if (strJSON && (!sanitize || this.isJSON(strJSON))) {
                try {
                    if (typeof JSON  !== "undefined" && typeof JSON.parse !== 'undefined'){
                        return JSON.parse(strJSON);
                    }
                    return eval('(' + strJSON + ')');
                } catch(e) {
                    throw new Error('ERROR: Bad JSON string...');
                }
            }
            return null;
        }
    };

    /**
     * Loads content from a given url through a XMLHttpRequest.
     * Shortcut function for simple AJAX use cases.
     *
     * @function load
     * @param {String}   url       request url
     * @param {Function} callback  callback to be executed if the request is successful
     * @return {Object} XMLHttpRequest object
     */
    Ajax.load = function(url, callback){
        return new Ajax(url, {
            method: 'GET',
            onSuccess: function(response){
                callback(response.responseText, response);
            }
        });
    };

    /**
     * Loads content from a given url through a XMLHttpRequest.
     * Shortcut function for simple AJAX use cases.
     * 
     * @function ping
     * @param {String}   url       request url
     * @param {Function} callback  callback to be executed if the request is successful
     * @return {Object} XMLHttpRequest object
     */
    Ajax.ping = function(url, callback){
        return new Ajax(url, {
            method: 'HEAD',
            onSuccess: function(response){
                if (typeof callback === 'function'){
                    callback(response);
                }
            }
        });
    };


    return Ajax;

});

/**
 * @author inkdev AT sapo.pt
 */

Ink.createModule('Ink.Net.JsonP', '1', [], function() {

    'use strict';

    /**
     * @module Ink.Net.JsonP_1
     */

    /**
     * @class Ink.Net.JsonP
     * @constructor
     * @param {String} uri
     * @param {Object} options
     * @param {Function}  [options.onComplete]        success callback
     * @param {Function}  [options.onFailure]         failure callback
     * @param {Object}    [options.failureObj]        object to be passed as argument to failure callback
     * @param {Number}    [options.timeout]           timeout for request fail, in seconds. defaults to 10
     * @param {Object}    [options.params]            object with the parameters and respective values to unfold
     * @param {String}    [options.callbackParam]     parameter to use as callback. defaults to 'jsoncallback'
     * @param {String}    [options.internalCallback]  x
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
            for (var i = 0, f = scriptEls.length; i < f; ++i) {
                scriptEl = scriptEls[i];
                if (scriptEl.src === this.uri) {
                    scriptEl.parentNode.removeChild(scriptEl);
                    return;
                }
            }
        }

    };

    return JsonP;

});

/**
 * @author inkdev AT sapo.pt
 */

Ink.createModule( 'Ink.Dom.Css', 1, [], function() {

    'use strict';

    /**
     * @module Ink.Dom.Css_1
     */

    /**
     * @class Ink.Dom.Css
     * @static
     */

    var DomCss = {
        /**
         * adds or removes a class to the given element according to addRemState
         *
         * @function addRemoveClassName
         * @param {DOMElement|string}   elm          DOM element or element id
         * @param {string}              className    class name
         * @param {boolean}             addRemState  which method to apply
         */
        addRemoveClassName: function(elm, className, addRemState) {
            if (addRemState) {
                return this.addClassName(elm, className);
            }
            this.removeClassName(elm, className);
        },

        /**
         * add a class to a given element
         *
         * @function addClassName
         * @param {DOMElement|String}  elm        DOM element or element id
         * @param {String}             className
         */
        addClassName: function(elm, className) {
            elm = Ink.i(elm);
            if (elm && className) {
                if (typeof elm.classList !== "undefined"){
                    elm.classList.add(className);
                }
                else if (!this.hasClassName(elm, className)) {
                    elm.className += (elm.className ? ' ' : '') + className;
                }
            }
        },

        /**
         * removes a class from a given element
         *
         * @function removeClassName
         * @param {DOMElement|String} elm        DOM element or element id
         * @param {String}            className
         */
        removeClassName: function(elm, className) {
            elm = Ink.i(elm);
            if (elm && className) {
                if (typeof elm.classList !== "undefined"){
                    elm.classList.remove(className);
                } else {
                    if (typeof elm.className === "undefined") {
                        return false;
                    }
                    var elmClassName = elm.className,
                        re = new RegExp("(^|\\s+)" + className + "(\\s+|$)");
                    elmClassName = elmClassName.replace(re, ' ');
                    elmClassName = elmClassName.replace(/^\s+/, '').replace(/\s+$/, '');

                    elm.className = elmClassName;
                }
            }
        },

        /**
         * Alias to addRemoveClassName. Utility function, saves many if/elses.
         *
         * @function setClassName
         * @param {DOMElement|String}  elm        DOM element or element id
         * @param {String}             className
         * @param {Boolean}            add        true to add, false to remove
         */
        setClassName: function(elm, className, add) {
            this.addRemoveClassName(elm, className, add || false);
        },

        /**
         * @function {Boolean} hasClassName
         * @param {DOMElement|String}  elm        DOM element or element id
         * @param {String}             className
         * @return true if a given class is applied to a given element
         */
        hasClassName: function(elm, className) {
            elm = Ink.i(elm);
            if (elm && className) {
                if (typeof elm.classList !== "undefined"){
                    return elm.classList.contains(className);
                }
                else {
                    if (typeof elm.className === "undefined") {
                        return false;
                    }
                    var elmClassName = elm.className;

                    if (typeof elmClassName.length === "undefined") {
                        return false;
                    }

                    if (elmClassName.length > 0) {
                        if (elmClassName === className) {
                            return true;
                        }
                        else {
                            var re = new RegExp("(^|\\s)" + className + "(\\s|$)");
                            if (re.test(elmClassName)) {
                                return true;
                            }
                        }
                    }
                }
            }
            return false;
        },

        /**
         * Add and removes the class from the element with a timeout, so it blinks
         *
         * @function blinkClass
         * @param {DOMElement|String}  elm        DOM element or element id
         * @param {String}             className  class name
         * @param {Boolean}            timeout    timeout in ms between adding and removing, default 100 ms
         * @param {Boolean}            negate     is true, class is removed then added
         */
        blinkClass: function(element, className, timeout, negate){
            element = Ink.i(element);
            this.addRemoveClassName(element, className, !negate);
            setTimeout(Ink.bind(function() {
                this.addRemoveClassName(element, className, negate);
            }, this), Number(timeout) || 100);
            /*
            var _self = this;
            setTimeout(function() {
                    console.log(_self);
                _self.addRemoveClassName(element, className, negate);
            }, Number(timeout) || 100);
            */
        },

        /**
         * Add or remove a class name from a given element
         *
         * @function toggleClassName
         * @param {DOMElement|String}  elm        DOM element or element id
         * @param {String}             className  class name
         * @param {Boolean}            forceAdd   forces the addition of the class if it doesn't exists
         */
        toggleClassName: function(elm, className, forceAdd) {
            if (elm && className){
                if (typeof elm.classList !== "undefined"){
                    elm = Ink.i(elm);
                    if (elm !== null){
                        elm.classList.toggle(className);
                    }
                    return true;
                }
            }

            if (typeof forceAdd !== 'undefined') {
                if (forceAdd === true) {
                    this.addClassName(elm, className);
                }
                else if (forceAdd === false) {
                    this.removeClassName(elm, className);
                }
            } else {
                if (this.hasClassName(elm, className)) {
                    this.removeClassName(elm, className);
                }
                else {
                    this.addClassName(elm, className);
                }
            }
        },

        /**
         * sets the opacity of given client a given element
         *
         * @function setOpacity
         * @param {DOMElement|String}  elm    DOM element or element id
         * @param {Number}             value  allows 0 to 1(default mode decimal) or percentage (warning using 0 or 1 will reset to default mode)
         */
        setOpacity: function(elm, value) {
            elm = Ink.i(elm);
            if (elm !== null){
                var val = 1;

                if (!isNaN(Number(value))){
                    if      (value <= 0) {   val = 0;           }
                    else if (value <= 1) {   val = value;       }
                    else if (value <= 100) { val = value / 100; }
                    else {                   val = 1;           }
                }

                if (typeof elm.style.opacity !== 'undefined') {
                    elm.style.opacity = val;
                }
                else {
                    elm.style.filter = "alpha(opacity:"+(val*100|0)+")";
                }
            }
        },

        /**
         * Converts a css property name to a string in camelcase to be used with CSSStyleDeclaration.
         * @function _camelCase
         * @private
         * @param {String} str  String to convert
         * @return {String} Converted string
         */
        _camelCase: function(str) {
            return str ? str.replace(/-(\w)/g, function (_, $1){
                return $1.toUpperCase();
            }) : str;
        },


        /**
         * Gets the value for an element's style attribute
         *
         * @function getStyle
         * @param {DOMElement|String}  elm    DOM element or element id
         * @param {String}             style  Which css attribute to fetch
         * @return Style value
         */
         getStyle: function(elm, style) {
             elm = Ink.i(elm);
             if (elm !== null) {
                 style = style === 'float' ? 'cssFloat': this._camelCase(style);

                 var value = elm.style[style];

                 if (window.getComputedStyle && (!value || value === 'auto')) {
                     var css = window.getComputedStyle(elm, null);

                     value = css ? css[style] : null;
                 }
                 else if (!value && elm.currentStyle) {
                      value = elm.currentStyle[style];
                      if (value === 'auto' && (style === 'width' || style === 'height')) {
                        value = elm["offset" + style.charAt(0).toUpperCase() + style.slice(1)] + "px";
                      }
                 }

                 if (style === 'opacity') {
                     return value ? parseFloat(value, 10) : 1.0;
                 }
                 else if (style === 'borderTopWidth'   || style === 'borderBottomWidth' ||
                          style === 'borderRightWidth' || style === 'borderLeftWidth'       ) {
                      if      (value === 'thin') {      return '1px';   }
                      else if (value === 'medium') {    return '3px';   }
                      else if (value === 'thick') {     return '5px';   }
                 }

                 return value === 'auto' ? null : value;
             }
         },


        /**
         * Sets the value for an element's style attribute
         *
         * @function setStyle
         * @param {DOMElement|String}  elm    DOM element or element id
         * @param {String}             style  Which css attribute to set
         */
        setStyle: function(elm, style) {
            elm = Ink.i(elm);
            if (elm !== null) {
                if (typeof style === 'string') {
                    elm.style.cssText += '; '+style;

                    if (style.indexOf('opacity') !== -1) {
                        this.setOpacity(elm, style.match(/opacity:\s*(\d?\.?\d*)/)[1]);
                    }
                }
                else {
                    for (var prop in style) {
                        if (style.hasOwnProperty(prop)){
                            if (prop === 'opacity') {
                                this.setOpacity(elm, style[prop]);
                            }
                            else {
                                if (prop === 'float' || prop === 'cssFloat') {
                                    if (typeof elm.style.styleFloat === 'undefined') {
                                        elm.style.cssFloat = style[prop];
                                    }
                                    else {
                                        elm.style.styleFloat = style[prop];
                                    }
                                } else {
                                    elm.style[prop] = style[prop];
                                }
                            }
                        }
                    }
                }
            }
        },


        /**
         * Makes an element visible
         *
         * @function show
         * @param {DOMElement|String}  elm                   DOM element or element id
         * @param {String}             forceDisplayProperty  Css display property to apply on show
         */
        show: function(elm, forceDisplayProperty) {
            elm = Ink.i(elm);
            if (elm !== null) {
                elm.style.display = (forceDisplayProperty) ? forceDisplayProperty : '';
            }
        },

        /**
         * Hides an element
         *
         * @function hide
         * @param {DOMElement|String}  elm  DOM element or element id
         */
        hide: function(elm) {
            elm = Ink.i(elm);
            if (elm !== null) {
                elm.style.display = 'none';
            }
        },

        /**
         * shows or hides according to param show
         *
         * @function showHide
         * @param {DOMElement|String}  elm   DOM element or element id
         * @param {boolean}            show
         */
        showHide: function(elm, show) {
            elm = Ink.i(elm);
            if (elm) {
                elm.style.display = show ? '' : 'none';
            }
        },

        /**
         * Shows or hides an element depending on current state
         * @function toggle
         * @param {DOMElement|String}  elm        DOM element or element id
         * @param {Boolean}            forceShow  Forces showing if element is hidden
         */
        toggle: function(elm, forceShow) {
            elm = Ink.i(elm);
            if (elm !== null) {
                if (typeof forceShow !== 'undefined') {
                    if (forceShow === true) {
                        this.show(elm);
                    } else {
                        this.hide(elm);
                    }
                } else {
                    if (elm.style.display === 'none') {
                        this.show(elm);
                    }
                    else {
                        this.hide(elm);
                    }
                }
            }
        },

        _getRefTag: function(head){
            if (head.firstElementChild) {
                return head.firstElementChild;
            }

            for (var child = head.firstChild; child; child = child.nextSibling){
                if (child.nodeType === 1){
                    return child;
                }
            }
            return null;
        },

        /**
         * Adds css style tags to the head section of a page
         *
         * @function appendStyleTag
         * @param {String}  selector  The css selector for the rule
         * @param {String}  style     The content of the style rule
         * @param {Object}  options   Options for the tag
         *    @param {String}  [options.type]   file type
         *    @param {Boolean} [options.force]  if true, style tag will be appended to end of head
         */
        appendStyleTag: function(selector, style, options){
            options = Ink.extendObj({
                type: 'text/css',
                force: false
            }, options || {});

            var styles = document.getElementsByTagName("style"),
                oldStyle = false, setStyle = true, i, l;

            for (i=0, l=styles.length; i<l; i++) {
                oldStyle = styles[i].innerHTML;
                if (oldStyle.indexOf(selector) >= 0) {
                    setStyle = false;
                }
            }

            if (setStyle) {
                var defStyle = document.createElement("style"),
                    head = document.getElementsByTagName("head")[0],
                    refTag = false, styleStr = '';

                defStyle.type  = options.type;

                styleStr += selector +" {";
                styleStr += style;
                styleStr += "} ";

                if (typeof defStyle.styleSheet !== "undefined") {
                    defStyle.styleSheet.cssText = styleStr;
                } else {
                    defStyle.appendChild(document.createTextNode(styleStr));
                }

                if (options.force){
                    head.appendChild(defStyle);
                } else {
                    refTag = this._getRefTag(head);
                    if (refTag){
                        head.insertBefore(defStyle, refTag);
                    }
                }
            }
        },

        /**
         * Adds a link tag for a stylesheet to the head section of a page
         *
         * @function appendStylesheet
         * @param {String}  path     File path
         * @param {Object}  options  Options for the tag
         *    @param {String}   [options.media]  media type
         *    @param {String}   [options.type]   file type
         *    @param {Boolean}  [options.force]  if true, tag will be appended to end of head
         */
        appendStylesheet: function(path, options){
            options = Ink.extendObj({
                media: 'screen',
                type: 'text/css',
                force: false
            }, options || {});

            var refTag,
                style = document.createElement("link"),
                head = document.getElementsByTagName("head")[0];

            style.media = options.media;
            style.type = options.type;
            style.href = path;
            style.rel = "Stylesheet";

            if (options.force){
                head.appendChild(style);
            }
            else {
                refTag = this._getRefTag(head);
                if (refTag){
                    head.insertBefore(style, refTag);
                }
            }
        },

        /**
         * Loads CSS via LINK element inclusion in HEAD (skips append if already there)
         *
         * Works similarly to appendStylesheet but:
         *   a) supports all browsers;
         *   b) supports optional callback which gets invoked once the CSS has been applied
         *
         * @function appendStylesheetCb
         * @param {String}            cssURI      URI of the CSS to load, if empty ignores and just calls back directly
         * @param {Function(cssURI)}  [callback]  optional callback which will be called once the CSS is loaded
         */
        _loadingCSSFiles: {},
        _loadedCSSFiles:  {},
        appendStylesheetCb: function(url, callback) {
            if (!url) {
                return callback(url);
            }

            if (this._loadedCSSFiles[url]) {
                return callback(url);
            }

            var cbs = this._loadingCSSFiles[url];
            if (cbs) {
                return cbs.push(callback);
            }

            this._loadingCSSFiles[url] = [callback];

            var linkEl = document.createElement('link');
            linkEl.type = 'text/css';
            linkEl.rel  = 'stylesheet';
            linkEl.href = url;

            var headEl = document.getElementsByTagName('head')[0];
            headEl.appendChild(linkEl);

            var imgEl = document.createElement('img');
            /*
            var _self = this;
            (function(_url) {
                imgEl.onerror = function() {
                    //var url = this;
                    var url = _url;
                    _self._loadedCSSFiles[url] = true;
                    var callbacks = _self._loadingCSSFiles[url];
                    for (var i = 0, f = callbacks.length; i < f; ++i) {
                        callbacks[i](url);
                    }
                    delete _self._loadingCSSFiles[url];
                };
            })(url);
            */
            imgEl.onerror = Ink.bindEvent(function(event, _url) {
                //var url = this;
                var url = _url;
                this._loadedCSSFiles[url] = true;
                var callbacks = this._loadingCSSFiles[url];
                for (var i = 0, f = callbacks.length; i < f; ++i) {
                    callbacks[i](url);
                }
                delete this._loadingCSSFiles[url];
            }, this, url);
            imgEl.src = url;
        },

        /**
         * Converts decimal to hexadecimal values, for use with colors
         *
         * @function decToHex
         * @param {String} dec - Either a single decimal value , an rgb(r, g, b) string
         * or an Object with r, g and b properties
         * @return Hexadecimal value
         */
        decToHex: function(dec) {
            var normalizeTo2 = function(val) {
                if (val.length === 1) {
                    val = '0' + val;
                }
                val = val.toUpperCase();
                return val;
            };

            if (typeof dec === 'object') {
                var rDec = normalizeTo2(parseInt(dec.r, 10).toString(16));
                var gDec = normalizeTo2(parseInt(dec.g, 10).toString(16));
                var bDec = normalizeTo2(parseInt(dec.b, 10).toString(16));
                return rDec+gDec+bDec;
            }
            else {
                dec += '';
                var rgb = dec.match(/\((\d+),\s?(\d+),\s?(\d+)\)/);
                if (rgb !== null) {
                    return  normalizeTo2(parseInt(rgb[1], 10).toString(16)) +
                            normalizeTo2(parseInt(rgb[2], 10).toString(16)) +
                            normalizeTo2(parseInt(rgb[3], 10).toString(16));
                }
                else {
                    return normalizeTo2(parseInt(dec, 10).toString(16));
                }
            }
        },

        /**
         * Converts hexadecimal values to decimal, for use with colors
         *
         * @function hexToDec
         * @param {String}  hex  hexadecimal value with 6, 3, 2 or 1 characters
         * @return {Number} Object with properties r, g, b if length of number is >= 3 or decimal value instead.
         */
        hexToDec: function(hex){
            if (hex.indexOf('#') === 0) {
                hex = hex.substr(1);
            }
            if (hex.length === 6) { // will return object RGB
                return {
                    r: parseInt(hex.substr(0,2), 16),
                    g: parseInt(hex.substr(2,2), 16),
                    b: parseInt(hex.substr(4,2), 16)
                };
            }
            else if (hex.length === 3) { // will return object RGB
                return {
                    r: parseInt(hex.charAt(0) + hex.charAt(0), 16),
                    g: parseInt(hex.charAt(1) + hex.charAt(1), 16),
                    b: parseInt(hex.charAt(2) + hex.charAt(2), 16)
                };
            }
            else if (hex.length <= 2) { // will return int
                return parseInt(hex, 16);
            }
        },

        /**
         * use this to obtain the value of a CSS property (searched from loaded CSS documents)
         *
         * @function getPropertyFromStylesheet
         * @param {String}  selector  a CSS rule. must be an exact match
         * @param {String}  property  a CSS property
         * @return {String} value of the found property, or null if it wasn't matched
         */
        getPropertyFromStylesheet: function(selector, property) {
            var rule = this.getRuleFromStylesheet(selector);
            if (rule) {
                return rule.style[property];
            }
            return null;
        },

        getPropertyFromStylesheet2: function(selector, property) {
            var rules = this.getRulesFromStylesheet(selector);
            /*
            rules.forEach(function(rule) {
                var x = rule.style[property];
                if (x !== null && x !== undefined) {
                    return x;
                }
            });
            */
            var x;
            for(var i=0, t=rules.length; i < t; i++) {
                x = rules[i].style[property];
                if (x !== null && x !== undefined) {
                    return x;
                }
            }
            return null;
        },

        getRuleFromStylesheet: function(selector) {
            var sheet, rules, ri, rf, rule;
            var s = document.styleSheets;
            if (!s) {
                return null;
            }

            for (var si = 0, sf = document.styleSheets.length; si < sf; ++si) {
                sheet = document.styleSheets[si];
                rules = sheet.rules ? sheet.rules : sheet.cssRules;
                if (!rules) { return null; }

                for (ri = 0, rf = rules.length; ri < rf; ++ri) {
                    rule = rules[ri];
                    if (!rule.selectorText) { continue; }
                    if (rule.selectorText === selector) {
                        return rule;
                    }
                }
            }

            return null;
        },

        getRulesFromStylesheet: function(selector) {
            var res = [];
            var sheet, rules, ri, rf, rule;
            var s = document.styleSheets;
            if (!s) { return res; }

            for (var si = 0, sf = document.styleSheets.length; si < sf; ++si) {
                sheet = document.styleSheets[si];
                rules = sheet.rules ? sheet.rules : sheet.cssRules;
                if (!rules) {
                    return null;
                }

                for (ri = 0, rf = rules.length; ri < rf; ++ri) {
                    rule = rules[ri];
                    if (!rule.selectorText) { continue; }
                    if (rule.selectorText === selector) {
                        res.push(rule);
                    }
                }
            }

            return res;
        },

        getPropertiesFromRule: function(selector) {
            var rule = this.getRuleFromStylesheet(selector);
            var props = {};
            var prop, i, f;

            /*if (typeof rule.style.length === 'snumber') {
                for (i = 0, f = rule.style.length; i < f; ++i) {
                    prop = this._camelCase( rule.style[i]   );
                    props[prop] = rule.style[prop];
                }
            }
            else {  // HANDLES IE 8, FIREFOX RULE JOINING... */
                rule = rule.style.cssText;
                var parts = rule.split(';');
                var steps, val, pre, pos;
                for (i = 0, f = parts.length; i < f; ++i) {
                    if (parts[i].charAt(0) === ' ') {
                        parts[i] = parts[i].substring(1);
                    }
                    steps = parts[i].split(':');
                    prop = this._camelCase( steps[0].toLowerCase()  );
                    val = steps[1];
                    if (val) {
                        val = val.substring(1);

                        if (prop === 'padding' || prop === 'margin' || prop === 'borderWidth') {

                            if (prop === 'borderWidth') {   pre = 'border'; pos = 'Width';  }
                            else {                          pre = prop;     pos = '';       }

                            if (val.indexOf(' ') !== -1) {
                                val = val.split(' ');
                                props[pre + 'Top'   + pos]  = val[0];
                                props[pre + 'Bottom'+ pos]  = val[0];
                                props[pre + 'Left'  + pos]  = val[1];
                                props[pre + 'Right' + pos]  = val[1];
                            }
                            else {
                                props[pre + 'Top'   + pos]  = val;
                                props[pre + 'Bottom'+ pos]  = val;
                                props[pre + 'Left'  + pos]  = val;
                                props[pre + 'Right' + pos]  = val;
                            }
                        }
                        else if (prop === 'borderRadius') {
                            if (val.indexOf(' ') !== -1) {
                                val = val.split(' ');
                                props.borderTopLeftRadius       = val[0];
                                props.borderBottomRightRadius   = val[0];
                                props.borderTopRightRadius      = val[1];
                                props.borderBottomLeftRadius    = val[1];
                            }
                            else {
                                props.borderTopLeftRadius       = val;
                                props.borderTopRightRadius      = val;
                                props.borderBottomLeftRadius    = val;
                                props.borderBottomRightRadius   = val;
                            }
                        }
                        else {
                            props[prop] = val;
                        }
                    }
                }
            //}
            //console.log(props);

            return props;
        },

        /**
         * Changes the font size of the elements which match the given CSS rule
         * For this function to work, the CSS file must be in the same domain than the host page, otherwise JS can't access it.
         *
         * @function changeFontSize
         * @param {String}  selector  CSS selector rule
         * @param {Number}  delta     number of pixels to change on font-size
         * @param {String}  [op]      supported operations are '+' and '*'. defaults to '+'
         * @param {Number}  [minVal]  if result gets smaller than minVal, change does not occurr
         * @param {Number}  [maxVal]  if result gets bigger  than maxVal, change does not occurr
         */
        changeFontSize: function(selector, delta, op, minVal, maxVal) {
            var that = this;
            Ink.requireModules(['Ink.Dom.Selector_1'], function(Selector) {
                var e;
                if      (typeof selector !== 'string') { e = '1st argument must be a CSS selector rule.'; }
                else if (typeof delta    !== 'number') { e = '2nd argument must be a number.'; }
                else if (op !== undefined && op !== '+' && op !== '*') { e = '3rd argument must be one of "+", "*".'; }
                else if (minVal !== undefined && (typeof minVal !== 'number' || minVal <= 0)) { e = '4th argument must be a positive number.'; }
                else if (maxVal !== undefined && (typeof maxVal !== 'number' || maxVal < maxVal)) { e = '5th argument must be a positive number greater than minValue.'; }
                if (e) { throw new TypeError(e); }

                var val, el, els = Selector.select(selector);
                if (minVal === undefined) { minVal = 1; }
                op = (op === '*') ? function(a,b){return a*b;} : function(a,b){return a+b;};
                for (var i = 0, f = els.length; i < f; ++i) {
                    el = els[i];
                    val = parseFloat( that.getStyle(el, 'fontSize'));
                    val = op(val, delta);
                    if (val < minVal) { continue; }
                    if (typeof maxVal === 'number' && val > maxVal) { continue; }
                    el.style.fontSize = val + 'px';
                }
            });
        }

    };

    return DomCss;

});

/**
 * @author inkdev AT sapo.pt
 */

Ink.createModule('Ink.Dom.Element', 1, [], function() {

    'use strict';

    /**
     * @module Ink.Dom.Element_1
     */

    /**
     * @class Ink.Dom.Element
     */

    var Element = {

        /** 
         * Shortcut for document.getElementById
         *
         * @function get
         * @param {String|Array} elm  Receives either an id or an Array of ids
         * @return Either the DOM element for the given id or an array of elements for the given ids
         */
        get: function(elm) {
            if(typeof elm !== 'undefined') {
                if(typeof elm === 'string') {
                    return document.getElementById(elm);
                }
                return elm;
            }
            return null;
        },

        /**
         * Creates a DOM element
         *
         * @function create
         * @param {String} tag        tag name
         * @param {Object} properties  object with properties to be set on the element
         */
        create: function(tag, properties) {
            var el = document.createElement(tag);
            //Ink.extendObj(el, properties);
            for(var property in properties) {
                if(properties.hasOwnProperty(property)) {
                    if(property === 'className') {
                        property = 'class';
                    }
                    el.setAttribute(property, properties[property]);
                }
            }
            return el;
        },

        /**
         * Removes DOM Element from DOM
         *
         * @function remove
         * @param  {DOMElement} el
         */
        remove: function(el) {
            var parEl;
            if (el && (parEl = el.parentNode)) {
                parEl.removeChild(el);
            }
        },

        /**
         * Scrolls to an element
         *
         * @function scrollTo
         * @param {DOMElement|String} elm  Element where to scroll
         */
        scrollTo: function(elm) {
            elm = this.get(elm);
            if(elm) {
                if (elm.scrollIntoView) {
                    return elm.scrollIntoView();
                }

                var elmOffset = {},
                    elmTop = 0, elmLeft = 0;

                do {
                    elmTop += elm.offsetTop || 0;
                    elmLeft += elm.offsetLeft || 0;

                    elm = elm.offsetParent;
                } while(elm);

                elmOffset = {x: elmLeft, y: elmTop};

                window.scrollTo(elmOffset.x, elmOffset.y);
            }
        },

        /**
         * Gets the top cumulative offset for an element
         *
         * @function offsetTop
         * @param {DOMElement|String} elm  target element
         * @return {Number} Offset from the target element to the top of the document
         */
        offsetTop: function(elm) {
            elm = this.get(elm);

            var offset = elm.offsetTop;

            while(elm.offsetParent){
                if(elm.offsetParent.tagName.toLowerCase() !== "body"){
                    elm = elm.offsetParent;
                    offset += elm.offsetTop;
                } else {
                    break;
                }
            }

            return offset;
        },

        /**
         * Gets the left cumulative offset for an element
         *
         * @function offsetLeft
         * @param {DOMElement|String} elm  target element
         * @return {Number} Offset from the target element to the left of the document
         */
        offsetLeft: function(elm) {
            /*
                elm = this.get(elm);

                var offset = elm.offsetLeft;

                while(elm.offsetParent){
                    if(elm.offsetParent.tagName.toLowerCase() !== "body"){
                        elm = elm.offsetParent;
                        offset += elm.offsetLeft;
                    } else {
                        break;
                    }
                }

                return offset;
            */
           return this.offset2( elm );
        },

        /**
        * Gets the element offset relative to its closest positioned ancestor
        * 
        * @function positionedOffset
        * @param {DOMElement|String} elm  target element
        * @return {Array} Array with the element offsetleft and offsettop relative to the closest positioned ancestor
        */
        positionedOffset: function(element) {
            var valueTop = 0, valueLeft = 0;
            element = this.get(element);
            do {
                valueTop  += element.offsetTop  || 0;
                valueLeft += element.offsetLeft || 0;
                element = element.offsetParent;
                if (element) {
                    if (element.tagName.toLowerCase() === 'body') { break;  }

                    var value = element.style.position;
                    if (!value && element.currentStyle) {
                        value = element.currentStyle.position;
                    }
                    if ((!value || value === 'auto') && typeof getComputedStyle !== 'undefined') {
                        var css = getComputedStyle(element, null);
                        value = css ? css.position : null;
                    }
                    if (value === 'relative' || value === 'absolute') { break;  }
                }
            } while (element);
            return [valueLeft, valueTop];
        },

        /**
         * Gets the cumulative offset for an element
         *
         * @function offset
         * @param {DOMElement|String} elm  target element
         * @return {Array} Array with offset from the target element to the top/left of the document
         */
        offset: function(elm) {
            return [
                this.offsetLeft(elm),
                this.offsetTop(elm)
            ];
        },

        /**
         * Gets the scroll of the element
         *
         * @function scroll
         * @param {DOMElement|String} [elm] target element or document.body
         * @returns {Array} offset values for x and y scroll
         */
        scroll: function(elm) {
            elm = elm ? Ink.i(elm) : document.body;
            return [
                ( ( !window.pageXOffset ) ? elm.scrollLeft : window.pageXOffset ),
                ( ( !window.pageYOffset ) ? elm.scrollTop : window.pageYOffset )
            ];
        },

        _getPropPx: function(cs, prop) {
            var n, c;
            var val = cs.getPropertyValue ? cs.getPropertyValue(prop) : cs[prop];
            if (!val) { n = 0; }
            else {
                c = val.indexOf('px');
                if (c === -1) { n = 0; }
                else {
                    n = parseInt(val, 10);
                }
            }

            //console.log([prop, ' "', val, '" ', n].join(''));

            return n;
        },

        /**
         * Returns the top left position of the element on the page
         *
         * @function offset2
         * @param {String|DOMElement} el
         * @return {Number[2]}
         */
        offset2: function(el) {
            /*jshint boss:true */
            el = Ink.i(el);
            var bProp = ['border-left-width', 'border-top-width'];
            var res = [0, 0];
            var dRes, bRes, parent, cs;
            var getPropPx = this._getPropPx;

            var InkBrowser = Ink.getModule('Ink.Dom.Browser',1);

            do {
                cs = window.getComputedStyle ? window.getComputedStyle(el, null) : el.currentStyle;
                dRes = [el.offsetLeft | 0, el.offsetTop | 0];
                bRes = [getPropPx(cs, bProp[0]), getPropPx(cs, bProp[1])];
                if( InkBrowser.OPERA ){
                    res[0] += dRes[0];
                    res[1] += dRes[1];
                } else {
                    res[0] += dRes[0] + bRes[0];
                    res[1] += dRes[1] + bRes[1];
                }
                parent = el.offsetParent;
            } while (el = parent);

            bRes = [getPropPx(cs, bProp[0]), getPropPx(cs, bProp[1])];

            if (InkBrowser.GECKO) {
                res[0] += bRes[0];
                res[1] += bRes[1];
            }
            else if( !InkBrowser.OPERA ) {
                res[0] -= bRes[0];
                res[1] -= bRes[1];
            }
            
            return res;
        },

        /**
         * Verifies the existence of an attribute
         *
         * @function hasAttribute 
         * @param {Object} elm   target element
         * @param {String} attr  attribute name
         * @return {Boolean} Boolean based on existance of attribute
         */
        hasAttribute: function(elm, attr){
            return elm.hasAttribute ? elm.hasAttribute(attr) : !!elm.getAttribute(attr);
        },
        /**
         * Inserts a element immediately after a target element
         *
         * @function insertAfter
         * @param {DOMElement}         newElm     element to be inserted
         * @param {DOMElement|String}  targetElm  key element
         */
        insertAfter: function(newElm, targetElm) {
            /*jshint boss:true */
            if (targetElm = this.get(targetElm)) {
                targetElm.parentNode.insertBefore(newElm, targetElm.nextSibling);
            }
        },

        /**
         * Inserts a element at the top of the childNodes of a target element
         *
         * @function insertTop
         * @param {DOMElement}         newElm     element to be inserted
         * @param {DOMElement|String}  targetElm  key element
         */
        insertTop: function(newElm,targetElm) {
            /*jshint boss:true */
            if (targetElm = this.get(targetElm)) {
                targetElm.insertBefore(newElm, targetElm.firstChild);
            }
        },

        /**
         * Retreives textContent from node
         *
         * @function textContent
         * @param {DOMNode} node from which to retreive text from. Can be any node type.
         * @return {String} the text
         */
        textContent: function(node){
            node = Ink.i(node);
            var text, k, cs, m;

            switch(node && node.nodeType) {
            case 9: /*DOCUMENT_NODE*/
                // IE quirks mode does not have documentElement
                return this.textContent(node.documentElement || node.body && node.body.parentNode || node.body);

            case 1: /*ELEMENT_NODE*/
                text = node.innerText;
                if (typeof text !== 'undefined') {
                    return text;
                }
                /* falls through */
            case 11: /*DOCUMENT_FRAGMENT_NODE*/
                text = node.textContent;
                if (typeof text !== 'undefined') {
                    return text;
                }

                if (node.firstChild === node.lastChild) {
                    // Common case: 0 or 1 children
                    return this.textContent(node.firstChild);
                }

                text = [];
                cs = node.childNodes;
                for (k = 0, m = cs.length; k < m; ++k) {
                    text.push( this.textContent( cs[k] ) );
                }
                return text.join('');

            case 3: /*TEXT_NODE*/
            case 4: /*CDATA_SECTION_NODE*/
                return node.nodeValue;
            }
            return '';
        },

        /**
         * Removes all nodes children and adds the text
         *
         * @function setTextContent
         * @param {DOMNode} node from which to retreive text from. Can be any node type.
         * @param {String}  text to be appended to the node.
         */
        setTextContent: function(node, text){
            node = Ink.i(node);
            switch(node && node.nodeType)
            {
            case 1: /*ELEMENT_NODE*/
                if ('innerText' in node) {
                    node.innerText = text;
                    break;
                }
                /* falls through */
            case 11: /*DOCUMENT_FRAGMENT_NODE*/
                if ('textContent' in node) {
                    node.textContent = text;
                    break;
                }
                /* falls through */
            case 9: /*DOCUMENT_NODE*/
                while(node.firstChild) {
                    node.removeChild(node.firstChild);
                }
                if (text !== '') {
                    var doc = node.ownerDocument || node;
                    node.appendChild(doc.createTextNode(text));
                }
                break;

            case 3: /*TEXT_NODE*/
            case 4: /*CDATA_SECTION_NODE*/
                node.nodeValue = text;
                break;
            }
        },

        /**
         * Tells if element is a clickable link
         *
         * @function isLink
         * @param {DOMNode} node to check if it's link
         * @return {Boolean}
         */
        isLink: function(element){
            var b = element && element.nodeType === 1 && ((/^a|area$/i).test(element.tagName) ||
                element.hasAttributeNS && element.hasAttributeNS('http://www.w3.org/1999/xlink','href'));
            return !!b;
        },

        /**
         * Tells if ancestor is ancestor of node
         *
         * @function isAncestorOf
         * @param {DOMNode} ancestor  ancestor node
         * @param {DOMNode} node      descendant node
         * @return {Boolean}
         */
        isAncestorOf: function(ancestor, node){
            /*jshint boss:true */
            if (!node || !ancestor) {
                return false;
            }
            if (node.compareDocumentPosition) {
                return (ancestor.compareDocumentPosition(node) & 0x10) !== 0;/*Node.DOCUMENT_POSITION_CONTAINED_BY*/
            }
            while (node = node.parentNode){
                if (node === ancestor){
                    return true;
                }
            }
            return false;
        },

        /**
         * Tells if descendant is descendant of node
         *
         * @function descendantOf
         * @param {DOMNode} node        the ancestor
         * @param {DOMNode} descendant  the descendant
         * @return {Boolean} true if 'descendant' is descendant of 'node'
         */
        descendantOf: function(node, descendant){
            return node !== descendant && this.isAncestorOf(node, descendant);
        },

        /**
         * Get first child in document order of node type 1
         * @function firstElementChild
         * @param {DOMNode} parent node
         * @return {DOMNode} the element child
         */
        firstElementChild: function(elm){
            if(!elm) {
                return null;
            }
            if ('firstElementChild' in elm) {
                return elm.firstElementChild;
            }
            var child = elm.firstChild;
            while(child && child.nodeType !== 1) {
                child = child.nextSibling;
            }
            return child;
        },

        /**
         * Get last child in document order of node type 1
         * @function lastElementChild
         * @param {DOMNode} parent node
         * @return {DOMNode} the element child
         */
        lastElementChild: function(elm){
            if(!elm) {
                return null;
            }
            if ('lastElementChild' in elm) {
                return elm.lastElementChild;
            }
            var child = elm.lastChild;
            while(child && child.nodeType !== 1) {
                child = child.previousSibling;
            }
            return child;
        },

        /**
         * Get the first element sibling after the node
         * 
         * @function nextElementSibling
         * @param {DOMNode} node  current node
         * @return {DOMNode|Null} the first element sibling after node or null if none is found
         */
        nextElementSibling: function(node){
            var sibling = null;

            if(!node){ return sibling; }

            if("nextElementSibling" in node){
                return node.nextElementSibling;
            } else {
                sibling = node.nextSibling;

                // 1 === Node.ELEMENT_NODE
                while(sibling && sibling.nodeType !== 1){
                    sibling = sibling.nextSibling;
                }

                return sibling;
            }
        },

        /**
         * Get the first element sibling before the node
         *
         * @function previousElementSibling
         * @param {DOMNode}        node  current node
         * @return {DOMNode|Null} the first element sibling before node or null if none is found
         */
        previousElementSibling: function(node){
            var sibling = null;

            if(!node){ return sibling; }

            if("previousElementSibling" in node){
                return node.previousElementSibling;
            } else {
                sibling = node.previousSibling;

                // 1 === Node.ELEMENT_NODE
                while(sibling && sibling.nodeType !== 1){
                    sibling = sibling.previousSibling;
                }

                return sibling;
            }
        },

        /**
         * Returns the width of the given element, in pixels
         *
         * @function elementWidth
         * @param {DOMElement|string} element target DOM element or target ID
         * @return {Number} the element's width
         */
        elementWidth: function(element) {
            if(typeof element === "string") {
                element = document.getElementById(element);
            }
            return element.offsetWidth;
        },

        /**
         * Returns the height of the given element, in pixels
         *
         * @function elementHeight
         * @param {DOMElement|string} element target DOM element or target ID
         * @return {Number} the element's height
         */
        elementHeight: function(element) {
            if(typeof element === "string") {
                element = document.getElementById(element);
            }
            return element.offsetHeight;
        },

        /**
         * Returns the element's left position in pixels
         *
         * @function elementLeft
         * @param {DOMElement|string} element target DOM element or target ID
         * @return {Number} element's left position
         */
        elementLeft: function(element) {
            if(typeof element === "string") {
                element = document.getElementById(element);
            }
            return element.offsetLeft;
        },

        /**
         * Returns the element's top position in pixels
         *
         * @function elementTop
         * @param {DOMElement|string} element target DOM element or target ID
         * @return {Number} element's top position
         */
        elementTop: function(element) {
            if(typeof element === "string") {
                element = document.getElementById(element);
            }
            return element.offsetTop;
        },

        /**
         * Returns the dimensions of the given element, in pixels
         *
         * @function elementDimensions
         * @param {element} element target element
         * @return {Array} array with element's width and height
         */
        elementDimensions: function(element) {
            if(typeof element === "string") {
                element = document.getElementById(element);
            }
            return Array(element.offsetWidth, element.offsetHeight);
        },

        /**
         * Applies the cloneFrom's dimensions to cloneTo
         *
         * @function clonePosition
         * @param {DOMElement} cloneTo    element to be position cloned
         * @param {DOMElement} cloneFrom  element to get the cloned position
         * @return {DOMElement} the element with positionClone
         */
        clonePosition: function(cloneTo, cloneFrom){
            /*
            cloneTo.style.top = this.offsetTop(cloneFrom) + 'px';
            cloneTo.style.left = this.offsetLeft(cloneFrom) + 'px';
            */
            var pos = this.offset2(cloneFrom);
            cloneTo.style.left = pos[0]+'px';
            cloneTo.style.top = pos[1]+'px';

            return cloneTo;
        },

        /**
         * Slices off a piece of text at the end of the element and adds the ellipsis
         * so all text fits in the element.
         *
         * @function ellipsizeText
         * @param {DOMElement} element     which text is to add the ellipsis
         * @param {String}     [ellipsis]  String to append to the chopped text
         */
        ellipsizeText: function(element, ellipsis){
            /*jshint boss:true */
            if (element = Ink.i(element)){
                while (element && element.scrollHeight > (element.offsetHeight + 8)) {
                    element.textContent = element.textContent.replace(/(\s+\S+)\s*$/, ellipsis || '\u2026');
                }
            }
        },

        /**
         * Searches up the DOM tree for an element of specified class name
         *
         * @function findUpwardsByClass
         * @param {DOMElement}  element
         * @param {String}      className
         * @return {DOMElement|Boolean} the found element or false
         */
        findUpwardsByClass: function(element, className) {
            var re = new RegExp("(^|\\s)" + className + "(\\s|$)");
            while (true) {
                if (typeof(element.className) !== 'undefined' && re.test(element.className)) {
                    return element;
                }
                else {
                    element = element.parentNode;
                    if (!element || element.nodeType !== 1) {
                        return false;
                    }
                }
            }
        },

        /**
         * Searches up the DOM tree for an element of specified tag name
         *
         * @function findUpwardsByTag
         * @param {DOMElement}  element
         * @param {String}      tag
         * @return {DOMElement|Boolean} the found element or false
         */
        findUpwardsByTag: function(element, tag) {
            while (true) {
                if (element && element.nodeName.toUpperCase() === tag.toUpperCase()) {
                    return element;
                } else {
                    element = element.parentNode;
                    if (!element || element.nodeType !== 1) {
                        return false;
                    }
                }
            }
        },

        /**
         * Searches up the DOM tree for an element with the given id
         *
         * @function findUpwardsById
         * @param {DOMElement}  element
         * @param {String}      id
         * @return {DOMElement|Boolean} the found element or false
         */
        findUpwardsById: function(element, id) {
            while (true) {
                if (typeof(element.id) !== 'undefined' && element.id === id) {
                    return element;
                } else {
                    element = element.parentNode;
                    if (!element || element.nodeType !== 1) {
                        return false;
                    }
                }
            }
        },


        /**
         * Returns trimmed text content of descendants
         *
         * @function getChildrenText
         * @param {DOMElement}  el          element being seeked
         * @param {Boolean}     [removeIt]  whether to remove the found text nodes or not
         * @return {String} text found
         */
        getChildrenText: function(el, removeIt) {
            var node,
                j,
                part,
                nodes = el.childNodes,
                jLen = nodes.length,
                text = '';

            if (!el) {
                return text;
            }

            for (j = 0; j < jLen; ++j) {
                node = nodes[j];
                if (!node) {    continue;   }
                if (node.nodeType === 3) {  // TEXT NODE
                    part = this._trimString( String(node.data) );
                    if (part.length > 0) {
                        text += part;
                        if (removeIt) { el.removeChild(node);   }
                    }
                    else {  el.removeChild(node);   }
                }
            }

            return text;
        },

        /**
         * String trim implementation
         * Used by getChildrenText
         *
         * function _trimString
         * param {String} text
         * return {String} trimmed text
         */
        _trimString: function(text) {
            return (String.prototype.trim) ? text.trim() : text.replace(/^\s*/, '').replace(/\s*$/, '');
        },

        /**
         * Returns the values of a select element
         *
         * @function getSelectValues
         * @param {DomElement|String} select element
         * @return {Array} selected values
         */
        getSelectValues: function (select) {
            var selectEl = Ink.i(select);
            var values = [];
            for (var i = 0; i < selectEl.options.length; ++i) {
                values.push( selectEl.options[i].value );
            }
            return values;
        },


        /* used by fills */
        _normalizeData: function(data) {
            var d, data2 = [];
            for (var i = 0, f = data.length; i < f; ++i) {
                d = data[i];

                if (!(d instanceof Array)) {    // if not array, wraps primitive twice:     val -> [val, val]
                    d = [d, d];
                }
                else if (d.length === 1) {      // if 1 element array:                      [val] -> [val, val]
                    d.push(d[0]);
                }
                data2.push(d);
            }
            return data2;
        },


        /**
         * Fills select element with choices
         *
         * @function fillSelect
         * @param {DomElement|String}  container       select element which will get filled
         * @param {Array}              data            data which will populate the component
         * @param {Boolean}            [skipEmpty]     true to skip empty option
         * @param {String|Number}      [defaultValue]  primitive value to select at beginning
         */
        fillSelect: function(container, data, skipEmpty, defaultValue) {
            var containerEl = Ink.i(container);
            if (!containerEl) {   return; }

            containerEl.innerHTML = '';
            var d, optionEl;

            if (!skipEmpty) {
                // add initial empty option
                optionEl = document.createElement('option');
                optionEl.setAttribute('value', '');
                containerEl.appendChild(optionEl);
            }

            data = this._normalizeData(data);

            for (var i = 0, f = data.length; i < f; ++i) {
                d = data[i];

                optionEl = document.createElement('option');
                optionEl.setAttribute('value', d[0]);
                if (d.length > 2) {
                    optionEl.setAttribute('extra', d[2]);
                }
                optionEl.appendChild( document.createTextNode(d[1]) );

                if (d[0] === defaultValue) {
                    optionEl.setAttribute('selected', 'selected');
                }

                containerEl.appendChild(optionEl);
            }
        },


        /**
         * Select element on steroids - allows the creation of new values
         * 
         * @function fillSelect2
         * @param {DomElement|String} ctn select element which will get filled
         * @param {Object} opts
         * @param {Array}                      [opts.data]               data which will populate the component
         * @param {Boolean}                    [opts.skipEmpty]          if true empty option is not created (defaults to false)
         * @param {String}                     [opts.emptyLabel]         label to display on empty option
         * @param {String}                     [opts.createLabel]        label to display on create option
         * @param {String}                     [opts.optionsGroupLabel]  text to display on group surrounding value options
         * @param {String}                     [opts.defaultValue]       option to select initially
         * @param {Function(selEl, addOptFn)}  [opts.onCreate]           callback that gets called once user selects the create option
         */
        fillSelect2: function(ctn, opts) {
            ctn = Ink.i(ctn);
            ctn.innerHTML = '';

            var defs = {
                skipEmpty:              false,
                skipCreate:             false,
                emptyLabel:             'none',
                createLabel:            'create',
                optionsGroupLabel:      'groups',
                emptyOptionsGroupLabel: 'none exist',
                defaultValue:           ''
            };
            if (!opts) {      throw 'param opts is a requirement!';   }
            if (!opts.data) { throw 'opts.data is a requirement!';    }
            opts = Ink.extendObj(defs, opts);

            var optionEl, d;

            var optGroupValuesEl = document.createElement('optgroup');
            optGroupValuesEl.setAttribute('label', opts.optionsGroupLabel);

            opts.data = this._normalizeData(opts.data);

            if (!opts.skipCreate) {
                opts.data.unshift(['$create$', opts.createLabel]);
            }

            if (!opts.skipEmpty) {
                opts.data.unshift(['', opts.emptyLabel]);
            }

            for (var i = 0, f = opts.data.length; i < f; ++i) {
                d = opts.data[i];

                optionEl = document.createElement('option');
                optionEl.setAttribute('value', d[0]);
                optionEl.appendChild( document.createTextNode(d[1]) );

                if (d[0] === opts.defaultValue) {   optionEl.setAttribute('selected', 'selected');  }

                if (d[0] === '' || d[0] === '$create$') {
                    ctn.appendChild(optionEl);
                }
                else {
                    optGroupValuesEl.appendChild(optionEl);
                }
            }

            var lastValIsNotOption = function(data) {
                var lastVal = data[data.length-1][0];
                return (lastVal === '' || lastVal === '$create$');
            };

            if (lastValIsNotOption(opts.data)) {
                optionEl = document.createElement('option');
                optionEl.setAttribute('value', '$dummy$');
                optionEl.setAttribute('disabled', 'disabled');
                optionEl.appendChild(   document.createTextNode(opts.emptyOptionsGroupLabel)    );
                optGroupValuesEl.appendChild(optionEl);
            }

            ctn.appendChild(optGroupValuesEl);

            var addOption = function(v, l) {
                var optionEl = ctn.options[ctn.options.length - 1];
                if (optionEl.getAttribute('disabled')) {
                    optionEl.parentNode.removeChild(optionEl);
                }

                // create it
                optionEl = document.createElement('option');
                optionEl.setAttribute('value', v);
                optionEl.appendChild(   document.createTextNode(l)  );
                optGroupValuesEl.appendChild(optionEl);

                // select it
                ctn.options[ctn.options.length - 1].setAttribute('selected', true);
            };

            if (!opts.skipCreate) {
                ctn.onchange = function() {
                    if ((ctn.value === '$create$') && (typeof opts.onCreate === 'function')) {  opts.onCreate(ctn, addOption);  }
                };
            }
        },


        /**
         * Creates set of radio buttons, returns wrapper
         *
         * @function fillRadios
         * @param {DomElement|String}  insertAfterEl   element which will precede the input elements
         * @param {String}             name            name to give to the form field ([] is added if not as suffix already)
         * @param {Array}              data            data which will populate the component
         * @param {Boolean}            [skipEmpty]     true to skip empty option
         * @param {String|Number}      [defaultValue]  primitive value to select at beginning
         * @param {String}             [splitEl]       name of element to add after each input element (example: 'br')
         * @return {DOMElement} wrapper element around radio buttons
         */
        fillRadios: function(insertAfterEl, name, data, skipEmpty, defaultValue, splitEl) {
            var afterEl = Ink.i(insertAfterEl);
            afterEl = afterEl.nextSibling;
            while (afterEl && afterEl.nodeType !== 1) {
                afterEl = afterEl.nextSibling;
            }
            var containerEl = document.createElement('span');
            if (afterEl) {
                afterEl.parentNode.insertBefore(containerEl, afterEl);
            } else {
                Ink.i(insertAfterEl).appendChild(containerEl);
            }

            data = this._normalizeData(data);

            if (name.substring(name.length - 1) !== ']') {
                name += '[]';
            }

            var d, inputEl;

            if (!skipEmpty) {
                // add initial empty option
                inputEl = document.createElement('input');
                inputEl.setAttribute('type', 'radio');
                inputEl.setAttribute('name', name);
                inputEl.setAttribute('value', '');
                containerEl.appendChild(inputEl);
                if (splitEl) {  containerEl.appendChild( document.createElement(splitEl) ); }
            }

            for (var i = 0; i < data.length; ++i) {
                d = data[i];

                inputEl = document.createElement('input');
                inputEl.setAttribute('type', 'radio');
                inputEl.setAttribute('name', name);
                inputEl.setAttribute('value', d[0]);
                containerEl.appendChild(inputEl);
                containerEl.appendChild( document.createTextNode(d[1]) );
                if (splitEl) {  containerEl.appendChild( document.createElement(splitEl) ); }

                if (d[0] === defaultValue) {
                    inputEl.checked = true;
                }
            }

            return containerEl;
        },


        /**
         * Creates set of checkbox buttons, returns wrapper
         *
         * @function fillChecks
         * @param {DomElement|String}  insertAfterEl   element which will precede the input elements
         * @param {String}             name            name to give to the form field ([] is added if not as suffix already)
         * @param {Array}              data            data which will populate the component
         * @param {Boolean}            [skipEmpty]     true to skip empty option
         * @param {String|Number}      [defaultValue]  primitive value to select at beginning
         * @param {String}             [splitEl]       name of element to add after each input element (example: 'br')
         * @return {DOMElement} wrapper element around checkboxes
         */
        fillChecks: function(insertAfterEl, name, data, defaultValue, splitEl) {
            var afterEl = Ink.i(insertAfterEl);
            afterEl = afterEl.nextSibling;
            while (afterEl && afterEl.nodeType !== 1) {
                afterEl = afterEl.nextSibling;
            }
            var containerEl = document.createElement('span');
            if (afterEl) {
                afterEl.parentNode.insertBefore(containerEl, afterEl);
            } else {
                Ink.i(insertAfterEl).appendChild(containerEl);
            }

            data = this._normalizeData(data);

            if (name.substring(name.length - 1) !== ']') {
                name += '[]';
            }

            var d, inputEl;

            for (var i = 0; i < data.length; ++i) {
                d = data[i];

                inputEl = document.createElement('input');
                inputEl.setAttribute('type', 'checkbox');
                inputEl.setAttribute('name', name);
                inputEl.setAttribute('value', d[0]);
                containerEl.appendChild(inputEl);
                containerEl.appendChild( document.createTextNode(d[1]) );
                if (splitEl) {  containerEl.appendChild( document.createElement(splitEl) ); }

                if (d[0] === defaultValue) {
                    inputEl.checked = true;
                }
            }

            return containerEl;
        },


        /**
         * Returns index of element from parent, -1 if not child of parent...
         *
         * @function parentIndexOf
         * @param {DOMElement}  parentEl  Element to parse
         * @param {DOMElement}  childEl   Child Element to look for
         * @return {Number}
         */
        parentIndexOf: function(parentEl, childEl) {
            var node, idx = 0;
            for (var i = 0, f = parentEl.childNodes.length; i < f; ++i) {
                node = parentEl.childNodes[i];
                if (node.nodeType === 1) {  // ELEMENT
                    if (node === childEl) { return idx; }
                    ++idx;
                }
            }
            return -1;
        },


        /**
         * Returns an array of elements - the next siblings
         *
         * @function nextSiblings
         * @param {String|DomElement} elm element
         * @return {Array} Array of next sibling elements
         */
        nextSiblings: function(elm) {
            if(typeof(elm) === "string") {
                elm = document.getElementById(elm);
            }
            if(typeof(elm) === 'object' && elm !== null && elm.nodeType && elm.nodeType === 1) {
                var elements = [],
                    siblings = elm.parentNode.children,
                    index    = this.parentIndexOf(elm.parentNode, elm);

                for(var i = ++index, len = siblings.length; i<len; i++) {
                    elements.push(siblings[i]);
                }

                return elements;
            }
            return [];
        },


        /**
         * Returns an array of elements - the previous siblings
         *
         * @function previousSiblings
         * @param {String|DomElement} elm element
         * @return {Array} Array of previous sibling elements
         */
        previousSiblings: function(elm) {
            if(typeof(elm) === "string") {
                elm = document.getElementById(elm);
            }
            if(typeof(elm) === 'object' && elm !== null && elm.nodeType && elm.nodeType === 1) {
                var elements    = [],
                    siblings    = elm.parentNode.children,
                    index       = this.parentIndexOf(elm.parentNode, elm);

                for(var i = 0, len = index; i<len; i++) {
                    elements.push(siblings[i]);
                }

                return elements;
            }
            return [];
        },


        /**
         * Returns an array of elements - its siblings
         *
         * @function siblings
         * @param {String|DomElement} elm element
         * @return {Array} Array of sibling elements
         */
        siblings: function(elm) {
            if(typeof(elm) === "string") {
                elm = document.getElementById(elm);
            }
            if(typeof(elm) === 'object' && elm !== null && elm.nodeType && elm.nodeType === 1) {
                var elements   = [],
                    siblings   = elm.parentNode.children;

                for(var i = 0, len = siblings.length; i<len; i++) {
                    if(elm !== siblings[i]) {
                        elements.push(siblings[i]);
                    }
                }

                return elements;
            }
            return [];
        },

        /**
         * fallback to elem.childElementCount
         *
         * @function childElementCount
         * @param {String|DomElement} elm element
         * @return {Number} number of child elements
         */
        childElementCount: function(elm) {
            elm = Ink.i(elm);
            if ('childElementCount' in elm) {
                return elm.childElementCount;
            }
            if (!elm) { return 0; }
            return this.siblings(elm).length + 1;
        },

       /**
        * parses and appends an html string to a container, not destroying its contents
        *
        * @function appendHTML
        * @param {String|DomElement} elm   element
        * @param {String}            html  markup string
        */
        appendHTML: function(elm, html){
            var temp = document.createElement('div');
            temp.innerHTML = html;
            var tempChildren = temp.children;
            for (var i = 0; i < tempChildren.length; i++){
                elm.appendChild(tempChildren[i]);
            }
        },

        /**
        * parses and prepends an html string to a container, not destroying its contents
        *
        * @function prependHTML
        * @param {String|DomElement} elm   element
        * @param {String}            html  markup string
        */
        prependHTML: function(elm, html){
            var temp = document.createElement('div');
            temp.innerHTML = html;
            var first = elm.firstChild;
            var tempChildren = temp.children;
            for (var i = tempChildren.length - 1; i >= 0; i--){
                elm.insertBefore(tempChildren[i], first);
                first = elm.firstChild;
            }
        },

        /**
         * Pass an HTML string and receive a documentFragment with the corresponding elements
         * @function htmlToFragment
         * @param  {String} html  html string
         * @return {DocumentFragment} DocumentFragment containing all of the elements from the html string
         */
        htmlToFragment: function(html){
            /*jshint boss:true */
            /*global Range:false */
            if(typeof document.createRange === 'function' && typeof Range.prototype.createContextualFragment === 'function'){
                this.htmlToFragment = function(html){
                    var range;

                    if(typeof html !== 'string'){ return document.createDocumentFragment(); }

                    range = document.createRange();

                    // set the context to document.body (firefox does this already, webkit doesn't)
                    range.selectNode(document.body);

                    return range.createContextualFragment(html);
                };
            } else {
                this.htmlToFragment = function(html){
                    var fragment = document.createDocumentFragment(),
                        tempElement,
                        current;

                    if(typeof html !== 'string'){ return fragment; }

                    tempElement = document.createElement('div');
                    tempElement.innerHTML = html;

                    // append child removes elements from the original parent
                    while(current = tempElement.firstChild){ // intentional assignment
                        fragment.appendChild(current);
                    }

                    return fragment;
                };
            }

            return this.htmlToFragment.call(this, html);
        },

        _camelCase: function(str) 
        {
            return str ? str.replace(/-(\w)/g, function (_, $1){
                    return $1.toUpperCase(); 
            }) : str;
        },

        /**
         * Gets all of the data attributes from an element
         *
         * @function data
         * @param {String|DomElement} selector Element or CSS selector
         * @return {Object} Object with the data-* properties or empty if none found.
        */
        data: function( selector ){
            if( typeof selector !== 'object' && typeof selector !== 'string'){
                throw '[Ink.Dom.Element.data] :: Invalid selector defined';
            }

            if( typeof selector === 'object' ){
                //this._element = selector;
                var _element = selector;
            } else {
                var InkDomSelector = Ink.getModule('Ink.Dom.Selector', 1);
                if(!InkDomSelector) {
                    throw "[Ink.Dom.Element.data] :: This method requires Ink.Dom.Selector - v1";
                }
                //this._element = InkDomSelector.select( selector );
                var _element = InkDomSelector.select( selector );
                if( _element.length <= 0) {
                    throw "[Ink.Dom.Element.data] :: Can't find any element with the specified selector";
                }
                //this._element = this._element[0];
                _element = _element[0];
            }

            var dataset = {};
            // var attributesElements = _element.dataset || _element.attributes || {}; 
            var attributesElements = _element.attributes || []; 
            var prop ;

            var curAttr, curAttrName, curAttrValue;
            // if(_element.dataset) {
            //     for( prop in attributesElements ){
            //         if(attributesElements.hasOwnProperty && attributesElements.hasOwnProperty(prop)) {
            //             //if(typeof(attributesElements[prop]) === 'object') {
            //             dataset[prop] = attributesElements[prop];
            //             //}
            //         }
            //     }
            // } else {
            if( attributesElements ){
                for(var i=0, total=attributesElements.length; i < total; i++){
                    curAttrName = attributesElements[i].name;
                    curAttrValue = attributesElements[i].value;
                    if(curAttrName && curAttrName.indexOf('data-') === 0) {
                        dataset[this._camelCase(curAttrName.replace('data-', ''))] = curAttrValue;
                    }
                    /*
                       if(attributesElements.hasOwnProperty && attributesElements.hasOwnProperty(prop)) {
                       if( typeof attributesElements[prop] === 'undefined' ){
                       continue;
                       } else if( typeof attributesElements[prop] === 'object' ){
                       prop = attributesElements[prop].name || prop;
                       if(
                       ( ( attributesElements[prop].name || attributesElements[prop].nodeValue ) && ( prop.indexOf('data-') !== 0 ) ) ||
                       !( attributesElements[prop].nodeValue || attributesElements[prop].value || attributesElements[prop] )
                       ){
                       continue;
                       }
                       }

                       propName = prop.replace('data-','');
                       if( propName.indexOf('-') !== -1 ){
                       propName = propName.split("-");
                       for( i=1; i<propName.length; i+=1 ){
                       propName[i] = propName[i].substr(0,1).toUpperCase() + propName[i].substr(1);
                       }
                       propName = propName.join('');
                       }
                       dataset[propName] = attributesElements[prop].nodeValue || attributesElements[prop].value || attributesElements[prop];
                       if( dataset[propName] === "true" || dataset[propName] === "false" ){
                       dataset[propName] = ( dataset[propName] === 'true' );
                       }
                       }
                     */
                }
            }

            return dataset;
        },

        /**
         * @function moveCursorTo
         * @param  {Input|Textarea}  el
         * @param  {Number}          t
         */
        moveCursorTo: function(el, t) {
            if (el.setSelectionRange) {
                el.setSelectionRange(t, t);
                //el.focus();
            }
            else {
                var range = el.createTextRange();
                range.collapse(true);
                range.moveEnd(  'character', t);
                range.moveStart('character', t);
                range.select();
            }
        },

        /**
         * @function pageWidth
         * @return {Number} page width
         */
        pageWidth: function() {
            var xScroll;

            if (window.innerWidth && window.scrollMaxX) {
                xScroll = window.innerWidth + window.scrollMaxX;
            } else if (document.body.scrollWidth > document.body.offsetWidth){
                xScroll = document.body.scrollWidth;
            } else {
                xScroll = document.body.offsetWidth;
            }

            var windowWidth;

            if (window.self.innerWidth) {
                if(document.documentElement.clientWidth){
                    windowWidth = document.documentElement.clientWidth;
                } else {
                    windowWidth = window.self.innerWidth;
                }
            } else if (document.documentElement && document.documentElement.clientWidth) {
                windowWidth = document.documentElement.clientWidth;
            } else if (document.body) {
                windowWidth = document.body.clientWidth;
            }

            if(xScroll < windowWidth){
                return xScroll;
            } else {
                return windowWidth;
            }
        },

        /**
         * @function pageHeight
         * @return {Number} page height
         */
        pageHeight: function() {
            var yScroll;

            if (window.innerHeight && window.scrollMaxY) {
                yScroll = window.innerHeight + window.scrollMaxY;
            } else if (document.body.scrollHeight > document.body.offsetHeight){
                yScroll = document.body.scrollHeight;
            } else {
                yScroll = document.body.offsetHeight;
            }

            var windowHeight;

            if (window.self.innerHeight) {
                windowHeight = window.self.innerHeight;
            } else if (document.documentElement && document.documentElement.clientHeight) {
                windowHeight = document.documentElement.clientHeight;
            } else if (document.body) {
                windowHeight = document.body.clientHeight;
            }

            if(yScroll < windowHeight){
                return windowHeight;
            } else {
                return yScroll;
            }
        },

       /**
         * @function viewportWidth
         * @return {Number} viewport width
         */
        viewportWidth: function() {
            if(typeof window.innerWidth !== "undefined") {
                return window.innerWidth;
            }
            if (document.documentElement && typeof document.documentElement.offsetWidth !== "undefined") {
                return document.documentElement.offsetWidth;
            }
        },

        /**
         * @function viewportHeight
         * @return {Number} viewport height
         */
        viewportHeight: function() {
            if (typeof window.innerHeight !== "undefined") {
                return window.innerHeight;
            }
            if (document.documentElement && typeof document.documentElement.offsetHeight !== "undefined") {
                return document.documentElement.offsetHeight;
            }
        },

        /**
         * @function scrollWidth
         * @return {Number} scroll width
         */
        scrollWidth: function() {
            if (typeof window.self.pageXOffset !== 'undefined') {
                return window.self.pageXOffset;
            }
            if (typeof document.documentElement !== 'undefined' && typeof document.documentElement.scrollLeft !== 'undefined') {
                return document.documentElement.scrollLeft;
            }
            return document.body.scrollLeft;
        },

        /**
         * @function scrollHeight
         * @return {Number} scroll height
         */
        scrollHeight: function() {
            if (typeof window.self.pageYOffset !== 'undefined') {
                return window.self.pageYOffset;
            }
            if (typeof document.documentElement !== 'undefined' && typeof document.documentElement.scrollTop !== 'undefined') {
                return document.documentElement.scrollTop;
            }
            return document.body.scrollTop;
        }
    };

    return Element;

});

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
     * Returns the target of the event object
     *
     * @function element
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
     * @function relatedTarget
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
     * @function findElement
     * @param {Object}  ev          event object
     * @param {String}  elmTagName  tag name to find
     * @param {Boolean} [force]     force the return of the wanted type of tag, or false otherwise
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
     * @function fire
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

    /**
     * Attaches an event to element
     *
     * @function observe
     * @param {DOMElement|String}  element      element id or element
     * @param {String}             eventName    event name
     * @param {Function}           callBack     receives event object as a
     * parameter. If you're manually firing custom events, check the
     * eventName property of the event object to make sure you're handling
     * the right event.
     * @param {Boolean}            [useCapture]  set to true to change event listening from bubbling to capture.
     */
    observe: function(element, eventName, callBack, useCapture)
    {
        element = Ink.i(element);
        if(element !== null && element !== undefined) {
            if(eventName.indexOf(':') !== -1 ||
                (eventName === "hashchange" && element.attachEvent && !window.onhashchange)
                ) {

                /**
                 *
                 * prevent that each custom event fire without any test
                 * This prevents that if you have multiple custom events
                 * on dataavailable to trigger the callback event if it
                 * is a different custom event
                 *
                 */
                var argCallback = callBack;
                callBack = Ink.bindEvent(function(ev, eventName, cb){

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

                eventName = 'dataavailable';
            }

            if(element.addEventListener) {
                element.addEventListener(eventName, callBack, !!useCapture);
            } else {
                element.attachEvent('on' + eventName, callBack);
            }
        }
    },

    /**
     * Remove an event attached to an element
     *
     * @function stopObserving
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
     * @function stop
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
     * Stops event default behaviour
     *
     * @function stopDefault
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
     * @function pointer
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
     * @function pointerX
     * @param {Object} ev event object
     * @return {Number} mouse X position
     */
    pointerX: function(ev)
    {
        return ev.pageX || (ev.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft));
    },

    /**
     * @function pointerY
     * @param {Object} ev event object
     * @return {Number} mouse Y position
     */
    pointerY: function(ev)
    {
        return ev.pageY || (ev.clientY + (document.documentElement.scrollTop || document.body.scrollTop));
    },

    /**
     * @function isLeftClick
     * @param {Object} ev  event object
     * @return {Boolea} True if there is a left click on the event
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
     * @function isRightClick
     * @param {Object} ev  event object
     * @return {Boolean} True if there is a right click on the event
     */
    isRightClick: function(ev) {
        return (ev.button === 2);
    },

    /**
     * @function isMiddleClick
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
     * @function getCharFromKeyboardEvent
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

/**
 * @module Ink.Dom.Loaded_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.Dom.Loaded', 1, [], function() {

    'use strict';

    /**
     * The Loaded class provides a method that allows developers to queue functions to run when
     * the page is loaded (document is ready).
     *
     * @class Ink.Dom.Loaded
     * @version 1
     * @static
     */
    var Loaded = {

        /**
         * Functions queue.
         *
         * @property _cbQueue
         * @type {Array}
         * @private
         * @static
         * @readOnly
         */
        _cbQueue: [], // Callbacks' queue

        /**
         * Adds a new function that will be invoked once the document is ready
         *
         * @method run
         * @param {Object}   [win] Window object to attach/add the event
         * @param {Function} fn  Callback function to be run after the page is loaded
         * @public
         * @example
         *     Ink.requireModules(['Ink.Dom.Loaded_1'],function(Loaded){
         *         Loaded.run(function(){
         *             console.log('This will run when the page/document is ready/loaded');
         *         });
         *     });
         */
        run: function(win, fn) {
            if (!fn) {
                fn  = win;
                win = window;
            }

            this._win  = win;
            this._doc  = win.document;
            this._root = this._doc.documentElement;
            this._done = false;
            this._top  = true;

            this._handlers = {
                checkState: Ink.bindEvent(this._checkState, this),
                poll:       Ink.bind(this._poll, this)
            };

            var   ael = this._doc.addEventListener;
            this._add = ael ? 'addEventListener' : 'attachEvent';
            this._rem = ael ? 'removeEventListener' : 'detachEvent';
            this._pre = ael ? '' : 'on';
            this._det = ael ? 'DOMContentLoaded' : 'onreadystatechange';
            this._wet = this._pre + 'load';

            var csf = this._handlers.checkState;

            if (this._doc.readyState === 'complete'){
                fn.call(this._win, 'lazy');
            }
            else {
                this._cbQueue.push(fn);

                this._doc[this._add]( this._det , csf );
                this._win[this._add]( this._wet , csf );

                var frameElement = 1;
                try{
                    frameElement = this._win.frameElement;
                } catch(e) {}

                if ( !ael && this._root.doScroll ) { // IE HACK
                    try {
                        this._top = !frameElement;
                    } catch(e) { }
                    if (this._top) {
                        this._poll();
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
        _checkState: function(event) {
            if ( !event || (event.type === 'readystatechange' && this._doc.readyState !== 'complete')) {
                return;
            }
            var where = (event.type === 'load') ? this._win : this._doc;
            where[this._rem](this._pre+event.type, this._handlers.checkState, false);
            this._ready();
        },

        /**
         * Polls the load progress of the page to see if it has already loaded or not
         *
         * @method _poll
         * @private
         */

        /**
         *
         * function _poll
         */
        _poll: function() {
            try {
                this._root.doScroll('left');
            } catch(e) {
                return setTimeout(this._handlers.poll, 50);
            }
            this._ready();
        },

        /**
         * Function that runs the callbacks from the queue when the document is ready.
         *
         * @method _ready
         * @private
         */
        _ready: function() {
            if (!this._done) {
                this._done = true;
                for (var i = 0; i < this._cbQueue.length; ++i) {
                    this._cbQueue[i].call(this._win);
                }
                this._cbQueue = [];
            }
        }
    };

    return Loaded;

});

/**
 * @module Ink.Dom.Selector_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.Dom.Selector', 1, [], function() {
	'use strict';

    /**
     * @class Ink.Dom.Selector
     * @static
     * @version 1
     */


/*!
 * Sizzle CSS Selector Engine
 * Copyright 2013 jQuery Foundation and other contributors
 * Released under the MIT license
 * http://sizzlejs.com/
 */

var i,
	cachedruns,
	Expr,
	getText,
	isXML,
	compile,
	outermostContext,
	recompare,
	sortInput,

	// Local document vars
	setDocument,
	document,
	docElem,
	documentIsHTML,
	rbuggyQSA,
	rbuggyMatches,
	matches,
	contains,

	// Instance-specific data
	expando = "sizzle" + -(new Date()),
	preferredDoc = window.document,
	support = {},
	dirruns = 0,
	done = 0,
	classCache = createCache(),
	tokenCache = createCache(),
	compilerCache = createCache(),
	hasDuplicate = false,
	sortOrder = function() { return 0; },

	// General-purpose constants
	strundefined = typeof undefined,
	MAX_NEGATIVE = 1 << 31,

	// Array methods
	arr = [],
	pop = arr.pop,
	push_native = arr.push,
	push = arr.push,
	slice = arr.slice,
	// Use a stripped-down indexOf if we can't use a native one
	indexOf = arr.indexOf || function( elem ) {
		var i = 0,
			len = this.length;
		for ( ; i < len; i++ ) {
			if ( this[i] === elem ) {
				return i;
			}
		}
		return -1;
	},


	// Regular expressions

	// Whitespace characters http://www.w3.org/TR/css3-selectors/#whitespace
	whitespace = "[\\x20\\t\\r\\n\\f]",
	// http://www.w3.org/TR/css3-syntax/#characters
	characterEncoding = "(?:\\\\.|[\\w-]|[^\\x00-\\xa0])+",

	// Loosely modeled on CSS identifier characters
	// An unquoted value should be a CSS identifier http://www.w3.org/TR/css3-selectors/#attribute-selectors
	// Proper syntax: http://www.w3.org/TR/CSS21/syndata.html#value-def-identifier
	identifier = characterEncoding.replace( "w", "w#" ),

	// Acceptable operators http://www.w3.org/TR/selectors/#attribute-selectors
	operators = "([*^$|!~]?=)",
	attributes = "\\[" + whitespace + "*(" + characterEncoding + ")" + whitespace +
		"*(?:" + operators + whitespace + "*(?:(['\"])((?:\\\\.|[^\\\\])*?)\\3|(" + identifier + ")|)|)" + whitespace + "*\\]",

	// Prefer arguments quoted,
	//   then not containing pseudos/brackets,
	//   then attribute selectors/non-parenthetical expressions,
	//   then anything else
	// These preferences are here to reduce the number of selectors
	//   needing tokenize in the PSEUDO preFilter
	pseudos = ":(" + characterEncoding + ")(?:\\(((['\"])((?:\\\\.|[^\\\\])*?)\\3|((?:\\\\.|[^\\\\()[\\]]|" + attributes.replace( 3, 8 ) + ")*)|.*)\\)|)",

	// Leading and non-escaped trailing whitespace, capturing some non-whitespace characters preceding the latter
	rtrim = new RegExp( "^" + whitespace + "+|((?:^|[^\\\\])(?:\\\\.)*)" + whitespace + "+$", "g" ),

	rcomma = new RegExp( "^" + whitespace + "*," + whitespace + "*" ),
	rcombinators = new RegExp( "^" + whitespace + "*([\\x20\\t\\r\\n\\f>+~])" + whitespace + "*" ),
	rpseudo = new RegExp( pseudos ),
	ridentifier = new RegExp( "^" + identifier + "$" ),

	matchExpr = {
		"ID": new RegExp( "^#(" + characterEncoding + ")" ),
		"CLASS": new RegExp( "^\\.(" + characterEncoding + ")" ),
		"NAME": new RegExp( "^\\[name=['\"]?(" + characterEncoding + ")['\"]?\\]" ),
		"TAG": new RegExp( "^(" + characterEncoding.replace( "w", "w*" ) + ")" ),
		"ATTR": new RegExp( "^" + attributes ),
		"PSEUDO": new RegExp( "^" + pseudos ),
		"CHILD": new RegExp( "^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\(" + whitespace +
			"*(even|odd|(([+-]|)(\\d*)n|)" + whitespace + "*(?:([+-]|)" + whitespace +
			"*(\\d+)|))" + whitespace + "*\\)|)", "i" ),
		// For use in libraries implementing .is()
		// We use this for POS matching in `select`
		"needsContext": new RegExp( "^" + whitespace + "*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\(" +
			whitespace + "*((?:-\\d)?\\d*)" + whitespace + "*\\)|)(?=[^-]|$)", "i" )
	},

	rsibling = /[\x20\t\r\n\f]*[+~]/,

	rnative = /^[^{]+\{\s*\[native code/,

	// Easily-parseable/retrievable ID or TAG or CLASS selectors
	rquickExpr = /^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/,

	rinputs = /^(?:input|select|textarea|button)$/i,
	rheader = /^h\d$/i,

	rescape = /'|\\/g,
	rattributeQuotes = /\=[\x20\t\r\n\f]*([^'"\]]*)[\x20\t\r\n\f]*\]/g,

	// CSS escapes http://www.w3.org/TR/CSS21/syndata.html#escaped-characters
	runescape = /\\([\da-fA-F]{1,6}[\x20\t\r\n\f]?|.)/g,
	funescape = function( _, escaped ) {
		var high = "0x" + escaped - 0x10000;
		// NaN means non-codepoint
		return high !== high ?
			escaped :
			// BMP codepoint
			high < 0 ?
				String.fromCharCode( high + 0x10000 ) :
				// Supplemental Plane codepoint (surrogate pair)
				String.fromCharCode( high >> 10 | 0xD800, high & 0x3FF | 0xDC00 );
	};

// Optimize for push.apply( _, NodeList )
try {
	push.apply(
		(arr = slice.call( preferredDoc.childNodes )),
		preferredDoc.childNodes
	);
	// Support: Android<4.0
	// Detect silently failing push.apply
	arr[ preferredDoc.childNodes.length ].nodeType;
} catch ( e ) {
	push = { apply: arr.length ?

		// Leverage slice if possible
		function( target, els ) {
			push_native.apply( target, slice.call(els) );
		} :

		// Support: IE<9
		// Otherwise append directly
		function( target, els ) {
			var j = target.length,
				i = 0;
			// Can't trust NodeList.length
			while ( (target[j++] = els[i++]) ) {}
			target.length = j - 1;
		}
	};
}

/**
 * For feature detection
 * @param {Function} fn The function to test for native support
 */
function isNative( fn ) {
	return rnative.test( fn + "" );
}

/**
 * Create key-value caches of limited size
 * @returns {Function(string, Object)} Returns the Object data after storing it on itself with
 *	property name the (space-suffixed) string and (if the cache is larger than Expr.cacheLength)
 *	deleting the oldest entry
 */
function createCache() {
	var cache,
		keys = [];

	return (cache = function( key, value ) {
		// Use (key + " ") to avoid collision with native prototype properties (see Issue #157)
		if ( keys.push( key += " " ) > Expr.cacheLength ) {
			// Only keep the most recent entries
			delete cache[ keys.shift() ];
		}
		return (cache[ key ] = value);
	});
}

/**
 * Mark a function for special use by Sizzle
 * @param {Function} fn The function to mark
 */
function markFunction( fn ) {
	fn[ expando ] = true;
	return fn;
}

/**
 * Support testing using an element
 * @param {Function} fn Passed the created div and expects a boolean result
 */
function assert( fn ) {
	var div = document.createElement("div");

	try {
		return !!fn( div );
	} catch (e) {
		return false;
	} finally {
		// release memory in IE
		div = null;
	}
}

function Sizzle( selector, context, results, seed ) {
	var match, elem, m, nodeType,
		// QSA vars
		i, groups, old, nid, newContext, newSelector;

	if ( ( context ? context.ownerDocument || context : preferredDoc ) !== document ) {
		setDocument( context );
	}

	context = context || document;
	results = results || [];

	if ( !selector || typeof selector !== "string" ) {
		return results;
	}

	if ( (nodeType = context.nodeType) !== 1 && nodeType !== 9 ) {
		return [];
	}

	if ( documentIsHTML && !seed ) {

		// Shortcuts
		if ( (match = rquickExpr.exec( selector )) ) {
			// Speed-up: Sizzle("#ID")
			if ( (m = match[1]) ) {
				if ( nodeType === 9 ) {
					elem = context.getElementById( m );
					// Check parentNode to catch when Blackberry 4.6 returns
					// nodes that are no longer in the document #6963
					if ( elem && elem.parentNode ) {
						// Handle the case where IE, Opera, and Webkit return items
						// by name instead of ID
						if ( elem.id === m ) {
							results.push( elem );
							return results;
						}
					} else {
						return results;
					}
				} else {
					// Context is not a document
					if ( context.ownerDocument && (elem = context.ownerDocument.getElementById( m )) &&
						contains( context, elem ) && elem.id === m ) {
						results.push( elem );
						return results;
					}
				}

			// Speed-up: Sizzle("TAG")
			} else if ( match[2] ) {
				push.apply( results, context.getElementsByTagName( selector ) );
				return results;

			// Speed-up: Sizzle(".CLASS")
			} else if ( (m = match[3]) && support.getElementsByClassName && context.getElementsByClassName ) {
				push.apply( results, context.getElementsByClassName( m ) );
				return results;
			}
		}

		// QSA path
		if ( support.qsa && !rbuggyQSA.test(selector) ) {
			old = true;
			nid = expando;
			newContext = context;
			newSelector = nodeType === 9 && selector;

			// qSA works strangely on Element-rooted queries
			// We can work around this by specifying an extra ID on the root
			// and working up from there (Thanks to Andrew Dupont for the technique)
			// IE 8 doesn't work on object elements
			if ( nodeType === 1 && context.nodeName.toLowerCase() !== "object" ) {
				groups = tokenize( selector );

				if ( (old = context.getAttribute("id")) ) {
					nid = old.replace( rescape, "\\$&" );
				} else {
					context.setAttribute( "id", nid );
				}
				nid = "[id='" + nid + "'] ";

				i = groups.length;
				while ( i-- ) {
					groups[i] = nid + toSelector( groups[i] );
				}
				newContext = rsibling.test( selector ) && context.parentNode || context;
				newSelector = groups.join(",");
			}

			if ( newSelector ) {
				try {
					push.apply( results,
						newContext.querySelectorAll( newSelector )
					);
					return results;
				} catch(qsaError) {
				} finally {
					if ( !old ) {
						context.removeAttribute("id");
					}
				}
			}
		}
	}

	// All others
	return select( selector.replace( rtrim, "$1" ), context, results, seed );
}

/**
 * Detect xml
 * @param {Element|Object} elem An element or a document
 */
isXML = Sizzle.isXML = function( elem ) {
	// documentElement is verified for cases where it doesn't yet exist
	// (such as loading iframes in IE - #4833)
	var documentElement = elem && (elem.ownerDocument || elem).documentElement;
	return documentElement ? documentElement.nodeName !== "HTML" : false;
};

/**
 * Sets document-related variables once based on the current document
 * @param {Element|Object} [doc] An element or document object to use to set the document
 * @returns {Object} Returns the current document
 */
setDocument = Sizzle.setDocument = function( node ) {
	var doc = node ? node.ownerDocument || node : preferredDoc;

	// If no document and documentElement is available, return
	if ( doc === document || doc.nodeType !== 9 || !doc.documentElement ) {
		return document;
	}

	// Set our document
	document = doc;
	docElem = doc.documentElement;

	// Support tests
	documentIsHTML = !isXML( doc );

	// Check if getElementsByTagName("*") returns only elements
	support.getElementsByTagName = assert(function( div ) {
		div.appendChild( doc.createComment("") );
		return !div.getElementsByTagName("*").length;
	});

	// Check if attributes should be retrieved by attribute nodes
	support.attributes = assert(function( div ) {
		div.innerHTML = "<select></select>";
		var type = typeof div.lastChild.getAttribute("multiple");
		// IE8 returns a string for some attributes even when not present
		return type !== "boolean" && type !== "string";
	});

	// Check if getElementsByClassName can be trusted
	support.getElementsByClassName = assert(function( div ) {
		// Opera can't find a second classname (in 9.6)
		div.innerHTML = "<div class='hidden e'></div><div class='hidden'></div>";
		if ( !div.getElementsByClassName || !div.getElementsByClassName("e").length ) {
			return false;
		}

		// Safari 3.2 caches class attributes and doesn't catch changes
		div.lastChild.className = "e";
		return div.getElementsByClassName("e").length === 2;
	});

	// Check if getElementsByName privileges form controls or returns elements by ID
	// If so, assume (for broader support) that getElementById returns elements by name
	support.getByName = assert(function( div ) {
		// Inject content
		div.id = expando + 0;
		// Support: Windows 8 Native Apps
		// Assigning innerHTML with "name" attributes throws uncatchable exceptions
		// http://msdn.microsoft.com/en-us/library/ie/hh465388.aspx
		div.appendChild( document.createElement("a") ).setAttribute( "name", expando );
		div.appendChild( document.createElement("i") ).setAttribute( "name", expando );
		docElem.appendChild( div );

		// Test
		var pass = doc.getElementsByName &&
			// buggy browsers will return fewer than the correct 2
			doc.getElementsByName( expando ).length === 2 +
			// buggy browsers will return more than the correct 0
			doc.getElementsByName( expando + 0 ).length;

		// Cleanup
		docElem.removeChild( div );

		return pass;
	});

	// Support: Webkit<537.32
	// Detached nodes confoundingly follow *each other*
	support.sortDetached = assert(function( div1 ) {
		return div1.compareDocumentPosition &&
			// Should return 1, but Webkit returns 4 (following)
			(div1.compareDocumentPosition( document.createElement("div") ) & 1);
	});

	// IE6/7 return modified attributes
	Expr.attrHandle = assert(function( div ) {
		div.innerHTML = "<a href='#'></a>";
		return div.firstChild && typeof div.firstChild.getAttribute !== strundefined &&
			div.firstChild.getAttribute("href") === "#";
	}) ?
		{} :
		{
			"href": function( elem ) {
				return elem.getAttribute( "href", 2 );
			},
			"type": function( elem ) {
				return elem.getAttribute("type");
			}
		};

	// ID find and filter
	if ( support.getByName ) {
		Expr.find["ID"] = function( id, context ) {
			if ( typeof context.getElementById !== strundefined && documentIsHTML ) {
				var m = context.getElementById( id );
				// Check parentNode to catch when Blackberry 4.6 returns
				// nodes that are no longer in the document #6963
				return m && m.parentNode ? [m] : [];
			}
		};
		Expr.filter["ID"] = function( id ) {
			var attrId = id.replace( runescape, funescape );
			return function( elem ) {
				return elem.getAttribute("id") === attrId;
			};
		};
	} else {
		Expr.find["ID"] = function( id, context ) {
			if ( typeof context.getElementById !== strundefined && documentIsHTML ) {
				var m = context.getElementById( id );

				return m ?
					m.id === id || typeof m.getAttributeNode !== strundefined && m.getAttributeNode("id").value === id ?
						[m] :
						undefined :
					[];
			}
		};
		Expr.filter["ID"] =  function( id ) {
			var attrId = id.replace( runescape, funescape );
			return function( elem ) {
				var node = typeof elem.getAttributeNode !== strundefined && elem.getAttributeNode("id");
				return node && node.value === attrId;
			};
		};
	}

	// Tag
	Expr.find["TAG"] = support.getElementsByTagName ?
		function( tag, context ) {
			if ( typeof context.getElementsByTagName !== strundefined ) {
				return context.getElementsByTagName( tag );
			}
		} :
		function( tag, context ) {
			var elem,
				tmp = [],
				i = 0,
				results = context.getElementsByTagName( tag );

			// Filter out possible comments
			if ( tag === "*" ) {
				while ( (elem = results[i++]) ) {
					if ( elem.nodeType === 1 ) {
						tmp.push( elem );
					}
				}

				return tmp;
			}
			return results;
		};

	// Name
	Expr.find["NAME"] = support.getByName && function( tag, context ) {
		if ( typeof context.getElementsByName !== strundefined ) {
			return context.getElementsByName( name );
		}
	};

	// Class
	Expr.find["CLASS"] = support.getElementsByClassName && function( className, context ) {
		if ( typeof context.getElementsByClassName !== strundefined && documentIsHTML ) {
			return context.getElementsByClassName( className );
		}
	};

	// QSA and matchesSelector support

	// matchesSelector(:active) reports false when true (IE9/Opera 11.5)
	rbuggyMatches = [];

	// qSa(:focus) reports false when true (Chrome 21),
	// no need to also add to buggyMatches since matches checks buggyQSA
	// A support test would require too much code (would include document ready)
	rbuggyQSA = [ ":focus" ];

	if ( (support.qsa = isNative(doc.querySelectorAll)) ) {
		// Build QSA regex
		// Regex strategy adopted from Diego Perini
		assert(function( div ) {
			// Select is set to empty string on purpose
			// This is to test IE's treatment of not explicitly
			// setting a boolean content attribute,
			// since its presence should be enough
			// http://bugs.jquery.com/ticket/12359
			div.innerHTML = "<select><option selected=''></option></select>";

			// IE8 - Some boolean attributes are not treated correctly
			if ( !div.querySelectorAll("[selected]").length ) {
				rbuggyQSA.push( "\\[" + whitespace + "*(?:checked|disabled|ismap|multiple|readonly|selected|value)" );
			}

			// Webkit/Opera - :checked should return selected option elements
			// http://www.w3.org/TR/2011/REC-css3-selectors-20110929/#checked
			// IE8 throws error here and will not see later tests
			if ( !div.querySelectorAll(":checked").length ) {
				rbuggyQSA.push(":checked");
			}
		});

		assert(function( div ) {

			// Opera 10-12/IE8 - ^= $= *= and empty values
			// Should not select anything
			div.innerHTML = "<input type='hidden' i=''/>";
			if ( div.querySelectorAll("[i^='']").length ) {
				rbuggyQSA.push( "[*^$]=" + whitespace + "*(?:\"\"|'')" );
			}

			// FF 3.5 - :enabled/:disabled and hidden elements (hidden elements are still enabled)
			// IE8 throws error here and will not see later tests
			if ( !div.querySelectorAll(":enabled").length ) {
				rbuggyQSA.push( ":enabled", ":disabled" );
			}

			// Opera 10-11 does not throw on post-comma invalid pseudos
			div.querySelectorAll("*,:x");
			rbuggyQSA.push(",.*:");
		});
	}

	if ( (support.matchesSelector = isNative( (matches = docElem.matchesSelector ||
		docElem.mozMatchesSelector ||
		docElem.webkitMatchesSelector ||
		docElem.oMatchesSelector ||
		docElem.msMatchesSelector) )) ) {

		assert(function( div ) {
			// Check to see if it's possible to do matchesSelector
			// on a disconnected node (IE 9)
			support.disconnectedMatch = matches.call( div, "div" );

			// This should fail with an exception
			// Gecko does not error, returns false instead
			matches.call( div, "[s!='']:x" );
			rbuggyMatches.push( "!=", pseudos );
		});
	}

	rbuggyQSA = new RegExp( rbuggyQSA.join("|") );
	rbuggyMatches = rbuggyMatches.length && new RegExp( rbuggyMatches.join("|") );

	// Element contains another
	// Purposefully does not implement inclusive descendent
	// As in, an element does not contain itself
	contains = isNative(docElem.contains) || docElem.compareDocumentPosition ?
		function( a, b ) {
			var adown = a.nodeType === 9 ? a.documentElement : a,
				bup = b && b.parentNode;
			return a === bup || !!( bup && bup.nodeType === 1 && (
				adown.contains ?
					adown.contains( bup ) :
					a.compareDocumentPosition && a.compareDocumentPosition( bup ) & 16
			));
		} :
		function( a, b ) {
			if ( b ) {
				while ( (b = b.parentNode) ) {
					if ( b === a ) {
						return true;
					}
				}
			}
			return false;
		};

	// Document order sorting
	sortOrder = docElem.compareDocumentPosition ?
	function( a, b ) {

		// Flag for duplicate removal
		if ( a === b ) {
			hasDuplicate = true;
			return 0;
		}

		var compare = b.compareDocumentPosition && a.compareDocumentPosition && a.compareDocumentPosition( b );

		if ( compare ) {
			// Disconnected nodes
			if ( compare & 1 ||
				(recompare && b.compareDocumentPosition( a ) === compare) ) {

				// Choose the first element that is related to our preferred document
				if ( a === doc || contains(preferredDoc, a) ) {
					return -1;
				}
				if ( b === doc || contains(preferredDoc, b) ) {
					return 1;
				}

				// Maintain original order
				return sortInput ?
					( indexOf.call( sortInput, a ) - indexOf.call( sortInput, b ) ) :
					0;
			}

			return compare & 4 ? -1 : 1;
		}

		// Not directly comparable, sort on existence of method
		return a.compareDocumentPosition ? -1 : 1;
	} :
	function( a, b ) {
		var cur,
			i = 0,
			aup = a.parentNode,
			bup = b.parentNode,
			ap = [ a ],
			bp = [ b ];

		// Exit early if the nodes are identical
		if ( a === b ) {
			hasDuplicate = true;
			return 0;

		// Parentless nodes are either documents or disconnected
		} else if ( !aup || !bup ) {
			return a === doc ? -1 :
				b === doc ? 1 :
				aup ? -1 :
				bup ? 1 :
				0;

		// If the nodes are siblings, we can do a quick check
		} else if ( aup === bup ) {
			return siblingCheck( a, b );
		}

		// Otherwise we need full lists of their ancestors for comparison
		cur = a;
		while ( (cur = cur.parentNode) ) {
			ap.unshift( cur );
		}
		cur = b;
		while ( (cur = cur.parentNode) ) {
			bp.unshift( cur );
		}

		// Walk down the tree looking for a discrepancy
		while ( ap[i] === bp[i] ) {
			i++;
		}

		return i ?
			// Do a sibling check if the nodes have a common ancestor
			siblingCheck( ap[i], bp[i] ) :

			// Otherwise nodes in our document sort first
			ap[i] === preferredDoc ? -1 :
			bp[i] === preferredDoc ? 1 :
			0;
	};

	return document;
};

Sizzle.matches = function( expr, elements ) {
	return Sizzle( expr, null, null, elements );
};

Sizzle.matchesSelector = function( elem, expr ) {
	// Set document vars if needed
	if ( ( elem.ownerDocument || elem ) !== document ) {
		setDocument( elem );
	}

	// Make sure that attribute selectors are quoted
	expr = expr.replace( rattributeQuotes, "='$1']" );

	// rbuggyQSA always contains :focus, so no need for an existence check
	if ( support.matchesSelector && documentIsHTML && (!rbuggyMatches || !rbuggyMatches.test(expr)) && !rbuggyQSA.test(expr) ) {
		try {
			var ret = matches.call( elem, expr );

			// IE 9's matchesSelector returns false on disconnected nodes
			if ( ret || support.disconnectedMatch ||
					// As well, disconnected nodes are said to be in a document
					// fragment in IE 9
					elem.document && elem.document.nodeType !== 11 ) {
				return ret;
			}
		} catch(e) {}
	}

	return Sizzle( expr, document, null, [elem] ).length > 0;
};

Sizzle.contains = function( context, elem ) {
	// Set document vars if needed
	if ( ( context.ownerDocument || context ) !== document ) {
		setDocument( context );
	}
	return contains( context, elem );
};

Sizzle.attr = function( elem, name ) {
	var val;

	// Set document vars if needed
	if ( ( elem.ownerDocument || elem ) !== document ) {
		setDocument( elem );
	}

	if ( documentIsHTML ) {
		name = name.toLowerCase();
	}
	if ( (val = Expr.attrHandle[ name ]) ) {
		return val( elem );
	}
	if ( !documentIsHTML || support.attributes ) {
		return elem.getAttribute( name );
	}
	return ( (val = elem.getAttributeNode( name )) || elem.getAttribute( name ) ) && elem[ name ] === true ?
		name :
		val && val.specified ? val.value : null;
};

Sizzle.error = function( msg ) {
	throw new Error( "Syntax error, unrecognized expression: " + msg );
};

// Document sorting and removing duplicates
Sizzle.uniqueSort = function( results ) {
	var elem,
		duplicates = [],
		j = 0,
		i = 0;

	// Unless we *know* we can detect duplicates, assume their presence
	hasDuplicate = !support.detectDuplicates;
	// Compensate for sort limitations
	recompare = !support.sortDetached;
	sortInput = !support.sortStable && results.slice( 0 );
	results.sort( sortOrder );

	if ( hasDuplicate ) {
		while ( (elem = results[i++]) ) {
			if ( elem === results[ i ] ) {
				j = duplicates.push( i );
			}
		}
		while ( j-- ) {
			results.splice( duplicates[ j ], 1 );
		}
	}

	return results;
};

/**
 * Checks document order of two siblings
 * @param {Element} a
 * @param {Element} b
 * @returns Returns -1 if a precedes b, 1 if a follows b
 */
function siblingCheck( a, b ) {
	var cur = b && a,
		diff = cur && ( ~b.sourceIndex || MAX_NEGATIVE ) - ( ~a.sourceIndex || MAX_NEGATIVE );

	// Use IE sourceIndex if available on both nodes
	if ( diff ) {
		return diff;
	}

	// Check if b follows a
	if ( cur ) {
		while ( (cur = cur.nextSibling) ) {
			if ( cur === b ) {
				return -1;
			}
		}
	}

	return a ? 1 : -1;
}

// Returns a function to use in pseudos for input types
function createInputPseudo( type ) {
	return function( elem ) {
		var name = elem.nodeName.toLowerCase();
		return name === "input" && elem.type === type;
	};
}

// Returns a function to use in pseudos for buttons
function createButtonPseudo( type ) {
	return function( elem ) {
		var name = elem.nodeName.toLowerCase();
		return (name === "input" || name === "button") && elem.type === type;
	};
}

// Returns a function to use in pseudos for positionals
function createPositionalPseudo( fn ) {
	return markFunction(function( argument ) {
		argument = +argument;
		return markFunction(function( seed, matches ) {
			var j,
				matchIndexes = fn( [], seed.length, argument ),
				i = matchIndexes.length;

			// Match elements found at the specified indexes
			while ( i-- ) {
				if ( seed[ (j = matchIndexes[i]) ] ) {
					seed[j] = !(matches[j] = seed[j]);
				}
			}
		});
	});
}

/**
 * Utility function for retrieving the text value of an array of DOM nodes
 * @param {Array|Element} elem
 */
getText = Sizzle.getText = function( elem ) {
	var node,
		ret = "",
		i = 0,
		nodeType = elem.nodeType;

	if ( !nodeType ) {
		// If no nodeType, this is expected to be an array
		for ( ; (node = elem[i]); i++ ) {
			// Do not traverse comment nodes
			ret += getText( node );
		}
	} else if ( nodeType === 1 || nodeType === 9 || nodeType === 11 ) {
		// Use textContent for elements
		// innerText usage removed for consistency of new lines (see #11153)
		if ( typeof elem.textContent === "string" ) {
			return elem.textContent;
		} else {
			// Traverse its children
			for ( elem = elem.firstChild; elem; elem = elem.nextSibling ) {
				ret += getText( elem );
			}
		}
	} else if ( nodeType === 3 || nodeType === 4 ) {
		return elem.nodeValue;
	}
	// Do not include comment or processing instruction nodes

	return ret;
};

Expr = Sizzle.selectors = {

	// Can be adjusted by the user
	cacheLength: 50,

	createPseudo: markFunction,

	match: matchExpr,

	find: {},

	relative: {
		">": { dir: "parentNode", first: true },
		" ": { dir: "parentNode" },
		"+": { dir: "previousSibling", first: true },
		"~": { dir: "previousSibling" }
	},

	preFilter: {
		"ATTR": function( match ) {
			match[1] = match[1].replace( runescape, funescape );

			// Move the given value to match[3] whether quoted or unquoted
			match[3] = ( match[4] || match[5] || "" ).replace( runescape, funescape );

			if ( match[2] === "~=" ) {
				match[3] = " " + match[3] + " ";
			}

			return match.slice( 0, 4 );
		},

		"CHILD": function( match ) {
			/* matches from matchExpr["CHILD"]
				1 type (only|nth|...)
				2 what (child|of-type)
				3 argument (even|odd|\d*|\d*n([+-]\d+)?|...)
				4 xn-component of xn+y argument ([+-]?\d*n|)
				5 sign of xn-component
				6 x of xn-component
				7 sign of y-component
				8 y of y-component
			*/
			match[1] = match[1].toLowerCase();

			if ( match[1].slice( 0, 3 ) === "nth" ) {
				// nth-* requires argument
				if ( !match[3] ) {
					Sizzle.error( match[0] );
				}

				// numeric x and y parameters for Expr.filter.CHILD
				// remember that false/true cast respectively to 0/1
				match[4] = +( match[4] ? match[5] + (match[6] || 1) : 2 * ( match[3] === "even" || match[3] === "odd" ) );
				match[5] = +( ( match[7] + match[8] ) || match[3] === "odd" );

			// other types prohibit arguments
			} else if ( match[3] ) {
				Sizzle.error( match[0] );
			}

			return match;
		},

		"PSEUDO": function( match ) {
			var excess,
				unquoted = !match[5] && match[2];

			if ( matchExpr["CHILD"].test( match[0] ) ) {
				return null;
			}

			// Accept quoted arguments as-is
			if ( match[4] ) {
				match[2] = match[4];

			// Strip excess characters from unquoted arguments
			} else if ( unquoted && rpseudo.test( unquoted ) &&
				// Get excess from tokenize (recursively)
				(excess = tokenize( unquoted, true )) &&
				// advance to the next closing parenthesis
				(excess = unquoted.indexOf( ")", unquoted.length - excess ) - unquoted.length) ) {

				// excess is a negative index
				match[0] = match[0].slice( 0, excess );
				match[2] = unquoted.slice( 0, excess );
			}

			// Return only captures needed by the pseudo filter method (type and argument)
			return match.slice( 0, 3 );
		}
	},

	filter: {

		"TAG": function( nodeName ) {
			if ( nodeName === "*" ) {
				return function() { return true; };
			}

			nodeName = nodeName.replace( runescape, funescape ).toLowerCase();
			return function( elem ) {
				return elem.nodeName && elem.nodeName.toLowerCase() === nodeName;
			};
		},

		"CLASS": function( className ) {
			var pattern = classCache[ className + " " ];

			return pattern ||
				(pattern = new RegExp( "(^|" + whitespace + ")" + className + "(" + whitespace + "|$)" )) &&
				classCache( className, function( elem ) {
					return pattern.test( elem.className || (typeof elem.getAttribute !== strundefined && elem.getAttribute("class")) || "" );
				});
		},

		"ATTR": function( name, operator, check ) {
			return function( elem ) {
				var result = Sizzle.attr( elem, name );

				if ( result == null ) {
					return operator === "!=";
				}
				if ( !operator ) {
					return true;
				}

				result += "";

				return operator === "=" ? result === check :
					operator === "!=" ? result !== check :
					operator === "^=" ? check && result.indexOf( check ) === 0 :
					operator === "*=" ? check && result.indexOf( check ) > -1 :
					operator === "$=" ? check && result.slice( -check.length ) === check :
					operator === "~=" ? ( " " + result + " " ).indexOf( check ) > -1 :
					operator === "|=" ? result === check || result.slice( 0, check.length + 1 ) === check + "-" :
					false;
			};
		},

		"CHILD": function( type, what, argument, first, last ) {
			var simple = type.slice( 0, 3 ) !== "nth",
				forward = type.slice( -4 ) !== "last",
				ofType = what === "of-type";

			return first === 1 && last === 0 ?

				// Shortcut for :nth-*(n)
				function( elem ) {
					return !!elem.parentNode;
				} :

				function( elem, context, xml ) {
					var cache, outerCache, node, diff, nodeIndex, start,
						dir = simple !== forward ? "nextSibling" : "previousSibling",
						parent = elem.parentNode,
						name = ofType && elem.nodeName.toLowerCase(),
						useCache = !xml && !ofType;

					if ( parent ) {

						// :(first|last|only)-(child|of-type)
						if ( simple ) {
							while ( dir ) {
								node = elem;
								while ( (node = node[ dir ]) ) {
									if ( ofType ? node.nodeName.toLowerCase() === name : node.nodeType === 1 ) {
										return false;
									}
								}
								// Reverse direction for :only-* (if we haven't yet done so)
								start = dir = type === "only" && !start && "nextSibling";
							}
							return true;
						}

						start = [ forward ? parent.firstChild : parent.lastChild ];

						// non-xml :nth-child(...) stores cache data on `parent`
						if ( forward && useCache ) {
							// Seek `elem` from a previously-cached index
							outerCache = parent[ expando ] || (parent[ expando ] = {});
							cache = outerCache[ type ] || [];
							nodeIndex = cache[0] === dirruns && cache[1];
							diff = cache[0] === dirruns && cache[2];
							node = nodeIndex && parent.childNodes[ nodeIndex ];

							while ( (node = ++nodeIndex && node && node[ dir ] ||

								// Fallback to seeking `elem` from the start
								(diff = nodeIndex = 0) || start.pop()) ) {

								// When found, cache indexes on `parent` and break
								if ( node.nodeType === 1 && ++diff && node === elem ) {
									outerCache[ type ] = [ dirruns, nodeIndex, diff ];
									break;
								}
							}

						// Use previously-cached element index if available
						} else if ( useCache && (cache = (elem[ expando ] || (elem[ expando ] = {}))[ type ]) && cache[0] === dirruns ) {
							diff = cache[1];

						// xml :nth-child(...) or :nth-last-child(...) or :nth(-last)?-of-type(...)
						} else {
							// Use the same loop as above to seek `elem` from the start
							while ( (node = ++nodeIndex && node && node[ dir ] ||
								(diff = nodeIndex = 0) || start.pop()) ) {

								if ( ( ofType ? node.nodeName.toLowerCase() === name : node.nodeType === 1 ) && ++diff ) {
									// Cache the index of each encountered element
									if ( useCache ) {
										(node[ expando ] || (node[ expando ] = {}))[ type ] = [ dirruns, diff ];
									}

									if ( node === elem ) {
										break;
									}
								}
							}
						}

						// Incorporate the offset, then check against cycle size
						diff -= last;
						return diff === first || ( diff % first === 0 && diff / first >= 0 );
					}
				};
		},

		"PSEUDO": function( pseudo, argument ) {
			// pseudo-class names are case-insensitive
			// http://www.w3.org/TR/selectors/#pseudo-classes
			// Prioritize by case sensitivity in case custom pseudos are added with uppercase letters
			// Remember that setFilters inherits from pseudos
			var args,
				fn = Expr.pseudos[ pseudo ] || Expr.setFilters[ pseudo.toLowerCase() ] ||
					Sizzle.error( "unsupported pseudo: " + pseudo );

			// The user may use createPseudo to indicate that
			// arguments are needed to create the filter function
			// just as Sizzle does
			if ( fn[ expando ] ) {
				return fn( argument );
			}

			// But maintain support for old signatures
			if ( fn.length > 1 ) {
				args = [ pseudo, pseudo, "", argument ];
				return Expr.setFilters.hasOwnProperty( pseudo.toLowerCase() ) ?
					markFunction(function( seed, matches ) {
						var idx,
							matched = fn( seed, argument ),
							i = matched.length;
						while ( i-- ) {
							idx = indexOf.call( seed, matched[i] );
							seed[ idx ] = !( matches[ idx ] = matched[i] );
						}
					}) :
					function( elem ) {
						return fn( elem, 0, args );
					};
			}

			return fn;
		}
	},

	pseudos: {
		// Potentially complex pseudos
		"not": markFunction(function( selector ) {
			// Trim the selector passed to compile
			// to avoid treating leading and trailing
			// spaces as combinators
			var input = [],
				results = [],
				matcher = compile( selector.replace( rtrim, "$1" ) );

			return matcher[ expando ] ?
				markFunction(function( seed, matches, context, xml ) {
					var elem,
						unmatched = matcher( seed, null, xml, [] ),
						i = seed.length;

					// Match elements unmatched by `matcher`
					while ( i-- ) {
						if ( (elem = unmatched[i]) ) {
							seed[i] = !(matches[i] = elem);
						}
					}
				}) :
				function( elem, context, xml ) {
					input[0] = elem;
					matcher( input, null, xml, results );
					return !results.pop();
				};
		}),

		"has": markFunction(function( selector ) {
			return function( elem ) {
				return Sizzle( selector, elem ).length > 0;
			};
		}),

		"contains": markFunction(function( text ) {
			return function( elem ) {
				return ( elem.textContent || elem.innerText || getText( elem ) ).indexOf( text ) > -1;
			};
		}),

		// "Whether an element is represented by a :lang() selector
		// is based solely on the element's language value
		// being equal to the identifier C,
		// or beginning with the identifier C immediately followed by "-".
		// The matching of C against the element's language value is performed case-insensitively.
		// The identifier C does not have to be a valid language name."
		// http://www.w3.org/TR/selectors/#lang-pseudo
		"lang": markFunction( function( lang ) {
			// lang value must be a valid identifier
			if ( !ridentifier.test(lang || "") ) {
				Sizzle.error( "unsupported lang: " + lang );
			}
			lang = lang.replace( runescape, funescape ).toLowerCase();
			return function( elem ) {
				var elemLang;
				do {
					if ( (elemLang = documentIsHTML ?
						elem.lang :
						elem.getAttribute("xml:lang") || elem.getAttribute("lang")) ) {

						elemLang = elemLang.toLowerCase();
						return elemLang === lang || elemLang.indexOf( lang + "-" ) === 0;
					}
				} while ( (elem = elem.parentNode) && elem.nodeType === 1 );
				return false;
			};
		}),

		// Miscellaneous
		"target": function( elem ) {
			var hash = window.location && window.location.hash;
			return hash && hash.slice( 1 ) === elem.id;
		},

		"root": function( elem ) {
			return elem === docElem;
		},

		"focus": function( elem ) {
			return elem === document.activeElement && (!document.hasFocus || document.hasFocus()) && !!(elem.type || elem.href || ~elem.tabIndex);
		},

		// Boolean properties
		"enabled": function( elem ) {
			return elem.disabled === false;
		},

		"disabled": function( elem ) {
			return elem.disabled === true;
		},

		"checked": function( elem ) {
			// In CSS3, :checked should return both checked and selected elements
			// http://www.w3.org/TR/2011/REC-css3-selectors-20110929/#checked
			var nodeName = elem.nodeName.toLowerCase();
			return (nodeName === "input" && !!elem.checked) || (nodeName === "option" && !!elem.selected);
		},

		"selected": function( elem ) {
			// Accessing this property makes selected-by-default
			// options in Safari work properly
			if ( elem.parentNode ) {
				elem.parentNode.selectedIndex;
			}

			return elem.selected === true;
		},

		// Contents
		"empty": function( elem ) {
			// http://www.w3.org/TR/selectors/#empty-pseudo
			// :empty is only affected by element nodes and content nodes(including text(3), cdata(4)),
			//   not comment, processing instructions, or others
			// Thanks to Diego Perini for the nodeName shortcut
			//   Greater than "@" means alpha characters (specifically not starting with "#" or "?")
			for ( elem = elem.firstChild; elem; elem = elem.nextSibling ) {
				if ( elem.nodeName > "@" || elem.nodeType === 3 || elem.nodeType === 4 ) {
					return false;
				}
			}
			return true;
		},

		"parent": function( elem ) {
			return !Expr.pseudos["empty"]( elem );
		},

		// Element/input types
		"header": function( elem ) {
			return rheader.test( elem.nodeName );
		},

		"input": function( elem ) {
			return rinputs.test( elem.nodeName );
		},

		"button": function( elem ) {
			var name = elem.nodeName.toLowerCase();
			return name === "input" && elem.type === "button" || name === "button";
		},

		"text": function( elem ) {
			var attr;
			// IE6 and 7 will map elem.type to 'text' for new HTML5 types (search, etc)
			// use getAttribute instead to test this case
			return elem.nodeName.toLowerCase() === "input" &&
				elem.type === "text" &&
				( (attr = elem.getAttribute("type")) == null || attr.toLowerCase() === elem.type );
		},

		// Position-in-collection
		"first": createPositionalPseudo(function() {
			return [ 0 ];
		}),

		"last": createPositionalPseudo(function( matchIndexes, length ) {
			return [ length - 1 ];
		}),

		"eq": createPositionalPseudo(function( matchIndexes, length, argument ) {
			return [ argument < 0 ? argument + length : argument ];
		}),

		"even": createPositionalPseudo(function( matchIndexes, length ) {
			var i = 0;
			for ( ; i < length; i += 2 ) {
				matchIndexes.push( i );
			}
			return matchIndexes;
		}),

		"odd": createPositionalPseudo(function( matchIndexes, length ) {
			var i = 1;
			for ( ; i < length; i += 2 ) {
				matchIndexes.push( i );
			}
			return matchIndexes;
		}),

		"lt": createPositionalPseudo(function( matchIndexes, length, argument ) {
			var i = argument < 0 ? argument + length : argument;
			for ( ; --i >= 0; ) {
				matchIndexes.push( i );
			}
			return matchIndexes;
		}),

		"gt": createPositionalPseudo(function( matchIndexes, length, argument ) {
			var i = argument < 0 ? argument + length : argument;
			for ( ; ++i < length; ) {
				matchIndexes.push( i );
			}
			return matchIndexes;
		})
	}
};

// Add button/input type pseudos
for ( i in { radio: true, checkbox: true, file: true, password: true, image: true } ) {
	Expr.pseudos[ i ] = createInputPseudo( i );
}
for ( i in { submit: true, reset: true } ) {
	Expr.pseudos[ i ] = createButtonPseudo( i );
}

function tokenize( selector, parseOnly ) {
	var matched, match, tokens, type,
		soFar, groups, preFilters,
		cached = tokenCache[ selector + " " ];

	if ( cached ) {
		return parseOnly ? 0 : cached.slice( 0 );
	}

	soFar = selector;
	groups = [];
	preFilters = Expr.preFilter;

	while ( soFar ) {

		// Comma and first run
		if ( !matched || (match = rcomma.exec( soFar )) ) {
			if ( match ) {
				// Don't consume trailing commas as valid
				soFar = soFar.slice( match[0].length ) || soFar;
			}
			groups.push( tokens = [] );
		}

		matched = false;

		// Combinators
		if ( (match = rcombinators.exec( soFar )) ) {
			matched = match.shift();
			tokens.push( {
				value: matched,
				// Cast descendant combinators to space
				type: match[0].replace( rtrim, " " )
			} );
			soFar = soFar.slice( matched.length );
		}

		// Filters
		for ( type in Expr.filter ) {
			if ( (match = matchExpr[ type ].exec( soFar )) && (!preFilters[ type ] ||
				(match = preFilters[ type ]( match ))) ) {
				matched = match.shift();
				tokens.push( {
					value: matched,
					type: type,
					matches: match
				} );
				soFar = soFar.slice( matched.length );
			}
		}

		if ( !matched ) {
			break;
		}
	}

	// Return the length of the invalid excess
	// if we're just parsing
	// Otherwise, throw an error or return tokens
	return parseOnly ?
		soFar.length :
		soFar ?
			Sizzle.error( selector ) :
			// Cache the tokens
			tokenCache( selector, groups ).slice( 0 );
}

function toSelector( tokens ) {
	var i = 0,
		len = tokens.length,
		selector = "";
	for ( ; i < len; i++ ) {
		selector += tokens[i].value;
	}
	return selector;
}

function addCombinator( matcher, combinator, base ) {
	var dir = combinator.dir,
		checkNonElements = base && dir === "parentNode",
		doneName = done++;

	return combinator.first ?
		// Check against closest ancestor/preceding element
		function( elem, context, xml ) {
			while ( (elem = elem[ dir ]) ) {
				if ( elem.nodeType === 1 || checkNonElements ) {
					return matcher( elem, context, xml );
				}
			}
		} :

		// Check against all ancestor/preceding elements
		function( elem, context, xml ) {
			var data, cache, outerCache,
				dirkey = dirruns + " " + doneName;

			// We can't set arbitrary data on XML nodes, so they don't benefit from dir caching
			if ( xml ) {
				while ( (elem = elem[ dir ]) ) {
					if ( elem.nodeType === 1 || checkNonElements ) {
						if ( matcher( elem, context, xml ) ) {
							return true;
						}
					}
				}
			} else {
				while ( (elem = elem[ dir ]) ) {
					if ( elem.nodeType === 1 || checkNonElements ) {
						outerCache = elem[ expando ] || (elem[ expando ] = {});
						if ( (cache = outerCache[ dir ]) && cache[0] === dirkey ) {
							if ( (data = cache[1]) === true || data === cachedruns ) {
								return data === true;
							}
						} else {
							cache = outerCache[ dir ] = [ dirkey ];
							cache[1] = matcher( elem, context, xml ) || cachedruns;
							if ( cache[1] === true ) {
								return true;
							}
						}
					}
				}
			}
		};
}

function elementMatcher( matchers ) {
	return matchers.length > 1 ?
		function( elem, context, xml ) {
			var i = matchers.length;
			while ( i-- ) {
				if ( !matchers[i]( elem, context, xml ) ) {
					return false;
				}
			}
			return true;
		} :
		matchers[0];
}

function condense( unmatched, map, filter, context, xml ) {
	var elem,
		newUnmatched = [],
		i = 0,
		len = unmatched.length,
		mapped = map != null;

	for ( ; i < len; i++ ) {
		if ( (elem = unmatched[i]) ) {
			if ( !filter || filter( elem, context, xml ) ) {
				newUnmatched.push( elem );
				if ( mapped ) {
					map.push( i );
				}
			}
		}
	}

	return newUnmatched;
}

function setMatcher( preFilter, selector, matcher, postFilter, postFinder, postSelector ) {
	if ( postFilter && !postFilter[ expando ] ) {
		postFilter = setMatcher( postFilter );
	}
	if ( postFinder && !postFinder[ expando ] ) {
		postFinder = setMatcher( postFinder, postSelector );
	}
	return markFunction(function( seed, results, context, xml ) {
		var temp, i, elem,
			preMap = [],
			postMap = [],
			preexisting = results.length,

			// Get initial elements from seed or context
			elems = seed || multipleContexts( selector || "*", context.nodeType ? [ context ] : context, [] ),

			// Prefilter to get matcher input, preserving a map for seed-results synchronization
			matcherIn = preFilter && ( seed || !selector ) ?
				condense( elems, preMap, preFilter, context, xml ) :
				elems,

			matcherOut = matcher ?
				// If we have a postFinder, or filtered seed, or non-seed postFilter or preexisting results,
				postFinder || ( seed ? preFilter : preexisting || postFilter ) ?

					// ...intermediate processing is necessary
					[] :

					// ...otherwise use results directly
					results :
				matcherIn;

		// Find primary matches
		if ( matcher ) {
			matcher( matcherIn, matcherOut, context, xml );
		}

		// Apply postFilter
		if ( postFilter ) {
			temp = condense( matcherOut, postMap );
			postFilter( temp, [], context, xml );

			// Un-match failing elements by moving them back to matcherIn
			i = temp.length;
			while ( i-- ) {
				if ( (elem = temp[i]) ) {
					matcherOut[ postMap[i] ] = !(matcherIn[ postMap[i] ] = elem);
				}
			}
		}

		if ( seed ) {
			if ( postFinder || preFilter ) {
				if ( postFinder ) {
					// Get the final matcherOut by condensing this intermediate into postFinder contexts
					temp = [];
					i = matcherOut.length;
					while ( i-- ) {
						if ( (elem = matcherOut[i]) ) {
							// Restore matcherIn since elem is not yet a final match
							temp.push( (matcherIn[i] = elem) );
						}
					}
					postFinder( null, (matcherOut = []), temp, xml );
				}

				// Move matched elements from seed to results to keep them synchronized
				i = matcherOut.length;
				while ( i-- ) {
					if ( (elem = matcherOut[i]) &&
						(temp = postFinder ? indexOf.call( seed, elem ) : preMap[i]) > -1 ) {

						seed[temp] = !(results[temp] = elem);
					}
				}
			}

		// Add elements to results, through postFinder if defined
		} else {
			matcherOut = condense(
				matcherOut === results ?
					matcherOut.splice( preexisting, matcherOut.length ) :
					matcherOut
			);
			if ( postFinder ) {
				postFinder( null, results, matcherOut, xml );
			} else {
				push.apply( results, matcherOut );
			}
		}
	});
}

function matcherFromTokens( tokens ) {
	var checkContext, matcher, j,
		len = tokens.length,
		leadingRelative = Expr.relative[ tokens[0].type ],
		implicitRelative = leadingRelative || Expr.relative[" "],
		i = leadingRelative ? 1 : 0,

		// The foundational matcher ensures that elements are reachable from top-level context(s)
		matchContext = addCombinator( function( elem ) {
			return elem === checkContext;
		}, implicitRelative, true ),
		matchAnyContext = addCombinator( function( elem ) {
			return indexOf.call( checkContext, elem ) > -1;
		}, implicitRelative, true ),
		matchers = [ function( elem, context, xml ) {
			return ( !leadingRelative && ( xml || context !== outermostContext ) ) || (
				(checkContext = context).nodeType ?
					matchContext( elem, context, xml ) :
					matchAnyContext( elem, context, xml ) );
		} ];

	for ( ; i < len; i++ ) {
		if ( (matcher = Expr.relative[ tokens[i].type ]) ) {
			matchers = [ addCombinator(elementMatcher( matchers ), matcher) ];
		} else {
			matcher = Expr.filter[ tokens[i].type ].apply( null, tokens[i].matches );

			// Return special upon seeing a positional matcher
			if ( matcher[ expando ] ) {
				// Find the next relative operator (if any) for proper handling
				j = ++i;
				for ( ; j < len; j++ ) {
					if ( Expr.relative[ tokens[j].type ] ) {
						break;
					}
				}
				return setMatcher(
					i > 1 && elementMatcher( matchers ),
					i > 1 && toSelector( tokens.slice( 0, i - 1 ) ).replace( rtrim, "$1" ),
					matcher,
					i < j && matcherFromTokens( tokens.slice( i, j ) ),
					j < len && matcherFromTokens( (tokens = tokens.slice( j )) ),
					j < len && toSelector( tokens )
				);
			}
			matchers.push( matcher );
		}
	}

	return elementMatcher( matchers );
}

function matcherFromGroupMatchers( elementMatchers, setMatchers ) {
	// A counter to specify which element is currently being matched
	var matcherCachedRuns = 0,
		bySet = setMatchers.length > 0,
		byElement = elementMatchers.length > 0,
		superMatcher = function( seed, context, xml, results, expandContext ) {
			var elem, j, matcher,
				setMatched = [],
				matchedCount = 0,
				i = "0",
				unmatched = seed && [],
				outermost = expandContext != null,
				contextBackup = outermostContext,
				// We must always have either seed elements or context
				elems = seed || byElement && Expr.find["TAG"]( "*", expandContext && context.parentNode || context ),
				// Use integer dirruns iff this is the outermost matcher
				dirrunsUnique = (dirruns += contextBackup == null ? 1 : Math.random() || 0.1);

			if ( outermost ) {
				outermostContext = context !== document && context;
				cachedruns = matcherCachedRuns;
			}

			// Add elements passing elementMatchers directly to results
			// Keep `i` a string if there are no elements so `matchedCount` will be "00" below
			for ( ; (elem = elems[i]) != null; i++ ) {
				if ( byElement && elem ) {
					j = 0;
					while ( (matcher = elementMatchers[j++]) ) {
						if ( matcher( elem, context, xml ) ) {
							results.push( elem );
							break;
						}
					}
					if ( outermost ) {
						dirruns = dirrunsUnique;
						cachedruns = ++matcherCachedRuns;
					}
				}

				// Track unmatched elements for set filters
				if ( bySet ) {
					// They will have gone through all possible matchers
					if ( (elem = !matcher && elem) ) {
						matchedCount--;
					}

					// Lengthen the array for every element, matched or not
					if ( seed ) {
						unmatched.push( elem );
					}
				}
			}

			// Apply set filters to unmatched elements
			matchedCount += i;
			if ( bySet && i !== matchedCount ) {
				j = 0;
				while ( (matcher = setMatchers[j++]) ) {
					matcher( unmatched, setMatched, context, xml );
				}

				if ( seed ) {
					// Reintegrate element matches to eliminate the need for sorting
					if ( matchedCount > 0 ) {
						while ( i-- ) {
							if ( !(unmatched[i] || setMatched[i]) ) {
								setMatched[i] = pop.call( results );
							}
						}
					}

					// Discard index placeholder values to get only actual matches
					setMatched = condense( setMatched );
				}

				// Add matches to results
				push.apply( results, setMatched );

				// Seedless set matches succeeding multiple successful matchers stipulate sorting
				if ( outermost && !seed && setMatched.length > 0 &&
					( matchedCount + setMatchers.length ) > 1 ) {

					Sizzle.uniqueSort( results );
				}
			}

			// Override manipulation of globals by nested matchers
			if ( outermost ) {
				dirruns = dirrunsUnique;
				outermostContext = contextBackup;
			}

			return unmatched;
		};

	return bySet ?
		markFunction( superMatcher ) :
		superMatcher;
}

compile = Sizzle.compile = function( selector, group /* Internal Use Only */ ) {
	var i,
		setMatchers = [],
		elementMatchers = [],
		cached = compilerCache[ selector + " " ];

	if ( !cached ) {
		// Generate a function of recursive functions that can be used to check each element
		if ( !group ) {
			group = tokenize( selector );
		}
		i = group.length;
		while ( i-- ) {
			cached = matcherFromTokens( group[i] );
			if ( cached[ expando ] ) {
				setMatchers.push( cached );
			} else {
				elementMatchers.push( cached );
			}
		}

		// Cache the compiled function
		cached = compilerCache( selector, matcherFromGroupMatchers( elementMatchers, setMatchers ) );
	}
	return cached;
};

function multipleContexts( selector, contexts, results ) {
	var i = 0,
		len = contexts.length;
	for ( ; i < len; i++ ) {
		Sizzle( selector, contexts[i], results );
	}
	return results;
}

function select( selector, context, results, seed ) {
	var i, tokens, token, type, find,
		match = tokenize( selector );

	if ( !seed ) {
		// Try to minimize operations if there is only one group
		if ( match.length === 1 ) {

			// Take a shortcut and set the context if the root selector is an ID
			tokens = match[0] = match[0].slice( 0 );
			if ( tokens.length > 2 && (token = tokens[0]).type === "ID" &&
					context.nodeType === 9 && documentIsHTML &&
					Expr.relative[ tokens[1].type ] ) {

				context = ( Expr.find["ID"]( token.matches[0].replace(runescape, funescape), context ) || [] )[0];
				if ( !context ) {
					return results;
				}

				selector = selector.slice( tokens.shift().value.length );
			}

			// Fetch a seed set for right-to-left matching
			i = matchExpr["needsContext"].test( selector ) ? 0 : tokens.length;
			while ( i-- ) {
				token = tokens[i];

				// Abort if we hit a combinator
				if ( Expr.relative[ (type = token.type) ] ) {
					break;
				}
				if ( (find = Expr.find[ type ]) ) {
					// Search, expanding context for leading sibling combinators
					if ( (seed = find(
						token.matches[0].replace( runescape, funescape ),
						rsibling.test( tokens[0].type ) && context.parentNode || context
					)) ) {

						// If seed is empty or no tokens remain, we can return early
						tokens.splice( i, 1 );
						selector = seed.length && toSelector( tokens );
						if ( !selector ) {
							push.apply( results, seed );
							return results;
						}

						break;
					}
				}
			}
		}
	}

	// Compile and execute a filtering function
	// Provide `match` to avoid retokenization if we modified the selector above
	compile( selector, match )(
		seed,
		context,
		!documentIsHTML,
		results,
		rsibling.test( selector )
	);
	return results;
}

// Deprecated
Expr.pseudos["nth"] = Expr.pseudos["eq"];

// Easy API for creating new setFilters
function setFilters() {}
setFilters.prototype = Expr.filters = Expr.pseudos;
Expr.setFilters = new setFilters();

// Check sort stability
support.sortStable = expando.split("").sort( sortOrder ).join("") === expando;

// Initialize with the default document
setDocument();

// Always assume the presence of duplicates if sort doesn't
// pass them to our comparison function (as in Google Chrome).
[0, 0].sort( sortOrder );
support.detectDuplicates = hasDuplicate;

// EXPOSE
/*if ( typeof define === "function" && define.amd ) {
	define(function() { return Sizzle; });
} else {
	window.Sizzle = Sizzle;
}*/
// EXPOSE

/**
 * Alias for the Sizzle search engine
 *
 * @method select
 * @param {String} selector CSS selector to search for elements
 * @param {DOMElement} [context] By default the search is done in the document element. However, you can specify an element as search context
 * @param {Array} [results] By default this is considered an empty array. But if you want to merge it with other searches you did, pass their result array through here.
 * @param {Object} [seed]
 * @return {Array} Array of DOM Elements as results
 */

/**
 * Retorna elementos que façam match com o 2º argumento da função
 *
 * @method matches
 * @param {String} selector CSS selector to search for elements
 * @param {Array} matches Elements to be 'matched' with
 * @result {Array} Elements that matched
 */

return {
    select: Sizzle,
    matches: Sizzle.matches
};


}); //( window );

/**
 * @module Ink.Dom.Browser_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.Dom.Browser', '1', [], function() {
    'use strict';    

    /**
     * @class Ink.Dom.Browser
     * @version 1
     * @static
     * @example
     *     <input type="text" id="dPicker" />
     *     <script>
     *         Ink.requireModules(['Ink.Dom.Browser_1'],function( InkBrowser ){
     *             if( InkBrowser.CHROME ){
     *                 console.log( 'This is a CHROME browser.' );
     *             }
     *         });
     *     </script>
     */
    var Browser = {
        /**
         * True if the browser is Internet Explorer
         *
         * @property IE
         * @type {Boolean}
         * @public
         * @static
         */
        IE: false,

        /**
         * True if the browser is Gecko based
         *
         * @property GECKO
         * @type {Boolean}
         * @public
         * @static
         */
        GECKO: false,

        /**
         * True if the browser is Opera
         *
         * @property OPERA
         * @type {Boolean}
         * @public
         * @static
         */
        OPERA: false,

        /**
         * True if the browser is Safari
         *
         * @property SAFARI
         * @type {Boolean}
         * @public
         * @static
         */
        SAFARI: false,

        /**
         * True if the browser is Konqueror
         *
         * @property KONQUEROR
         * @type {Boolean}
         * @public
         * @static
         */
        KONQUEROR: false,

        /**
         * True if browser is Chrome
         *
         * @property CHROME
         * @type {Boolean}
         * @public
         * @static
         */
        CHROME: false,

        /**
         * The specific browser model. False if it is unable to get it.
         *
         * @property model
         * @type {Boolean|String}
         * @public
         * @static
         */
        model: false,

        /**
         * The browser version. False if it is unable to get it.
         *
         * @property version
         * @type {Boolean|String}
         * @public
         * @static
         */
        version: false,

        /**
         * The user agent string. False if it is unable to get it.
         *
         * @property userAgent
         * @type {Boolean|String}
         * @public
         * @static
         */
        userAgent: false,

        /**
         * Initialization function for the Browser object
         *
         * @method init
         * @public
         */
        init: function()
        {
            this.detectBrowser();
            this.setDimensions();
            this.setReferrer();
        },

        /**
         * Stores window dimensions
         *
         * @method setDimensions
         * @public
         */
        setDimensions: function()
        {
            //this.windowWidth=window.innerWidth !== null? window.innerWidth : document.documentElement && document.documentElement.clientWidth ? document.documentElement.clientWidth : document.body !== null ? document.body.clientWidth : null;
            //this.windowHeight=window.innerHeight != null? window.innerHeight : document.documentElement && document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body != null? document.body.clientHeight : null;
            var myWidth = 0, myHeight = 0;
            if ( typeof window.innerWidth=== 'number' ) {
                myWidth = window.innerWidth;
                myHeight = window.innerHeight;
            } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
                myWidth = document.documentElement.clientWidth;
                myHeight = document.documentElement.clientHeight;
            } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
                myWidth = document.body.clientWidth;
                myHeight = document.body.clientHeight;
            }
            this.windowWidth = myWidth;
            this.windowHeight = myHeight;
        },

        /**
         * Stores the referrer
         *
         * @method setReferrer
         * @public
         */
        setReferrer: function()
        {
            this.referrer = document.referrer !== undefined? document.referrer.length > 0 ? window.escape(document.referrer) : false : false;
        },

        /**
         * Detects the browser and stores the found properties
         *
         * @method detectBrowser
         * @public
         */
        detectBrowser: function()
        {
            var sAgent = navigator.userAgent;

            this.userAgent = sAgent;

            sAgent = sAgent.toLowerCase();

            if((new RegExp("applewebkit\/")).test(sAgent)) {

                if((new RegExp("chrome\/")).test(sAgent)) {
                    // Chrome
                    this.CHROME = true;
                    this.model = 'chrome';
                    this.version = sAgent.replace(new RegExp("(.*)chrome\/([^\\s]+)(.*)"), "$2");
                    this.cssPrefix = '-webkit-';
                    this.domPrefix = 'Webkit';
                } else {
                    // Safari
                    this.SAFARI = true;
                    this.model = 'safari';
                    this.version = sAgent.replace(new RegExp("(.*)applewebkit\/([^\\s]+)(.*)"), "$2");
                    this.cssPrefix = '-webkit-';
                    this.domPrefix = 'Webkit';
                }
            } else if((new RegExp("opera")).test(sAgent)) {
                // Opera
                this.OPERA = true;
                this.model = 'opera';
                this.version = sAgent.replace(new RegExp("(.*)opera.([^\\s$]+)(.*)"), "$2");
                this.cssPrefix = '-o-';
                this.domPrefix = 'O';
            } else if((new RegExp("konqueror")).test(sAgent)) {
                // Konqueror
                this.KONQUEROR = true;
                this.model = 'konqueror';
                this.version = sAgent.replace(new RegExp("(.*)konqueror\/([^;]+);(.*)"), "$2");
                this.cssPrefix = '-khtml-';
                this.domPrefix = 'Khtml';
            } else if((new RegExp("msie\\ ")).test(sAgent)) {
                // MSIE
                this.IE = true;
                this.model = 'ie';
                this.version = sAgent.replace(new RegExp("(.*)\\smsie\\s([^;]+);(.*)"), "$2");
                this.cssPrefix = '-ms-';
                this.domPrefix = 'ms';
            } else if((new RegExp("gecko")).test(sAgent)) {
                // GECKO
                // Supports only:
                // Camino, Chimera, Epiphany, Minefield (firefox 3), Firefox, Firebird, Phoenix, Galeon,
                // Iceweasel, K-Meleon, SeaMonkey, Netscape, Songbird, Sylera,
                this.GECKO = true;
                var re = new RegExp("(camino|chimera|epiphany|minefield|firefox|firebird|phoenix|galeon|iceweasel|k\\-meleon|seamonkey|netscape|songbird|sylera)");
                if(re.test(sAgent)) {
                    this.model = sAgent.match(re)[1];
                    this.version = sAgent.replace(new RegExp("(.*)"+this.model+"\/([^;\\s$]+)(.*)"), "$2");
                    this.cssPrefix = '-moz-';
                    this.domPrefix = 'Moz';
                } else {
                    // probably is mozilla
                    this.model = 'mozilla';
                    var reVersion = new RegExp("(.*)rv:([^)]+)(.*)");
                    if(reVersion.test(sAgent)) {
                        this.version = sAgent.replace(reVersion, "$2");
                    }
                    this.cssPrefix = '-moz-';
                    this.domPrefix = 'Moz';
                }
            }
        },

        /**
         * Debug function to help checking values.
         *
         * @method debug
         * @public
         */
        debug: function()
        {
            /*global alert:false */
            var str = "known browsers: (ie, gecko, opera, safari, konqueror) \n";
                str += [this.IE, this.GECKO, this.OPERA, this.SAFARI, this.KONQUEROR] +"\n";
                str += "model -> "+this.model+"\n";
                str += "version -> "+this.version+"\n";
                str += "\n";
                str += "original UA -> "+this.userAgent;

                alert(str);
        }
    };

    Browser.init();

    return Browser;
});
/**
 * @module Ink.Util.Url_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.Util.Url', '1', [], function() {

    'use strict';

    /**
     * Utility functions to use with URLs
     *
     * @class Ink.Util.Url
     * @version 1
     * @static
     */
    var Url = {

        /**
         * Auxiliary string for encoding
         *
         * @property _keyStr
         * @type {String}
         * @readOnly
         * @private
         */
        _keyStr : 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=',


        /**
         * Get current URL of page
         *
         * @method getUrl
         * @return {String}    Current URL
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Url_1'], function( InkUrl ){
         *         console.log( InkUrl.getUrl() ); // Will return it's window URL
         *     });
         */
        getUrl: function()
        {
            return window.location.href;
        },

        /**
         * Generates an uri with query string based on the parameters object given
         *
         * @method genQueryString
         * @param {String} uri
         * @param {Object} params
         * @return {String} URI with query string set
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Url_1'], function( InkUrl ){
         *         var queryString = InkUrl.genQueryString( 'http://www.sapo.pt/', {
         *             'param1': 'valueParam1',
         *             'param2': 'valueParam2'
         *         });
         *
         *         console.log( queryString ); // Result: http://www.sapo.pt/?param1=valueParam1&param2=valueParam2
         *     });
         */
        genQueryString: function(uri, params) {
            var hasQuestionMark = uri.indexOf('?') !== -1;
            var sep, pKey, pValue, parts = [uri];

            for (pKey in params) {
                if (params.hasOwnProperty(pKey)) {
                    if (!hasQuestionMark) {
                        sep = '?';
                        hasQuestionMark = true;
                    } else {
                        sep = '&';
                    }
                    pValue = params[pKey];
                    if (typeof pValue !== 'number' && !pValue) {
                        pValue = '';
                    }
                    parts = parts.concat([sep, encodeURIComponent(pKey), '=', encodeURIComponent(pValue)]);
                }
            }

            return parts.join('');
        },

        /**
         * Get query string of current or passed URL
         *
         * @method getQueryString
         * @param {String} [str] URL String. When not specified it uses the current URL.
         * @return {Object} Key-Value object with the pairs variable: value
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Url_1'], function( InkUrl ){
         *         var queryStringParams = InkUrl.getQueryString( 'http://www.sapo.pt/?var1=valueVar1&var2=valueVar2' );
         *         console.log( queryStringParams );
         *         // Result:
         *         // {
         *         //    var1: 'valueVar1',
         *         //    var2: 'valueVar2'
         *         // }
         *     });
         */
        getQueryString: function(str)
        {
            var url;
            if(str && typeof(str) !== 'undefined') {
                url = str;
            } else {
                url = this.getUrl();
            }
            var aParams = {};
            if(url.match(/\?(.+)/i)) {
                var queryStr = url.replace(/^(.*)\?([^\#]+)(\#(.*))?/g, "$2");
                if(queryStr.length > 0) {
                    var aQueryStr = queryStr.split(/[;&]/);
                    for(var i=0; i < aQueryStr.length; i++) {
                        var pairVar = aQueryStr[i].split('=');
                        aParams[decodeURIComponent(pairVar[0])] = (typeof(pairVar[1]) !== 'undefined' && pairVar[1]) ? decodeURIComponent(pairVar[1]) : false;
                    }
                }
            }
            return aParams;
        },

        /**
         * Get URL hash
         *
         * @method getAnchor
         * @param {String} [str] URL String. If not set, it will get the current URL.
         * @return {String|Boolean} Hash in the URL. If there's no hash, returns false.
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Url_1'], function( InkUrl ){
         *         var anchor = InkUrl.getAnchor( 'http://www.sapo.pt/page.php#TEST' );
         *         console.log( anchor ); // Result: TEST
         *     });
         */
        getAnchor: function(str)
        {
            var url;
            if(str && typeof(str) !== 'undefined') {
                url = str;
            } else {
                url = this.getUrl();
            }
            var anchor = false;
            if(url.match(/#(.+)/)) {
                anchor = url.replace(/([^#]+)#(.*)/, "$2");
            }
            return anchor;
        },

        /**
         * Get anchor string of current or passed URL
         *
         * @method getAnchorString
         * @param {String} [string] If not provided it uses the current URL.
         * @return {Object} Returns a key-value object of the 'variables' available in the hashtag of the URL
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Url_1'], function( InkUrl ){
         *         var hashParams = InkUrl.getAnchorString( 'http://www.sapo.pt/#var1=valueVar1&var2=valueVar2' );
         *         console.log( hashParams );
         *         // Result:
         *         // {
         *         //    var1: 'valueVar1',
         *         //    var2: 'valueVar2'
         *         // }
         *     });
         */
        getAnchorString: function(string)
        {
            var url;
            if(string && typeof(string) !== 'undefined') {
                url = string;
            } else {
                url = this.getUrl();
            }
            var aParams = {};
            if(url.match(/#(.+)/i)) {
                var anchorStr = url.replace(/^([^#]+)#(.*)?/g, "$2");
                if(anchorStr.length > 0) {
                    var aAnchorStr = anchorStr.split(/[;&]/);
                    for(var i=0; i < aAnchorStr.length; i++) {
                        var pairVar = aAnchorStr[i].split('=');
                        aParams[decodeURIComponent(pairVar[0])] = (typeof(pairVar[1]) !== 'undefined' && pairVar[1]) ? decodeURIComponent(pairVar[1]) : false;
                    }
                }
            }
            return aParams;
        },


        /**
         * Parse passed URL
         *
         * @method parseUrl
         * @param {String} url URL to be parsed
         * @return {Object} Parsed URL as a key-value object.
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Url_1'], function( InkUrl ){
         *         var parsedURL = InkUrl.parseUrl( 'http://www.sapo.pt/index.html?var1=value1#anchor' )
         *         console.log( parsedURL );
         *         // Result:
         *         // {
         *         //   'scheme'    => 'http',
         *         //   'host'      => 'www.sapo.pt',
         *         //   'path'      => '/index.html',
         *         //   'query'     => 'var1=value1',
         *         //   'fragment'  => 'anchor'
         *         // }
         *     });
         *
         */
        parseUrl: function(url)
        {
            var aURL = {};
            if(url && typeof(url) !== 'undefined' && typeof(url) === 'string') {
                if(url.match(/^([^:]+):\/\//i)) {
                    var re = /^([^:]+):\/\/([^\/]*)\/?([^\?#]*)\??([^#]*)#?(.*)/i;
                    if(url.match(re)) {
                        aURL.scheme   = url.replace(re, "$1");
                        aURL.host     = url.replace(re, "$2");
                        aURL.path     = '/'+url.replace(re, "$3");
                        aURL.query    = url.replace(re, "$4") || false;
                        aURL.fragment = url.replace(re, "$5") || false;
                    }
                } else {
                    var re1 = new RegExp("^([^\\?]+)\\?([^#]+)#(.*)", "i");
                    var re2 = new RegExp("^([^\\?]+)\\?([^#]+)#?", "i");
                    var re3 = new RegExp("^([^\\?]+)\\??", "i");
                    if(url.match(re1)) {
                        aURL.scheme   = false;
                        aURL.host     = false;
                        aURL.path     = url.replace(re1, "$1");
                        aURL.query    = url.replace(re1, "$2");
                        aURL.fragment = url.replace(re1, "$3");
                    } else if(url.match(re2)) {
                        aURL.scheme = false;
                        aURL.host   = false;
                        aURL.path   = url.replace(re2, "$1");
                        aURL.query  = url.replace(re2, "$2");
                        aURL.fragment = false;
                    } else if(url.match(re3)) {
                        aURL.scheme   = false;
                        aURL.host     = false;
                        aURL.path     = url.replace(re3, "$1");
                        aURL.query    = false;
                        aURL.fragment = false;
                    }
                }
                if(aURL.host) {
                    var regPort = new RegExp("^(.*)\\:(\\d+)$","i");
                    // check for port
                    if(aURL.host.match(regPort)) {
                        var tmpHost1 = aURL.host;
                        aURL.host = tmpHost1.replace(regPort, "$1");
                        aURL.port = tmpHost1.replace(regPort, "$2");
                    } else {
                        aURL.port = false;
                    }
                    // check for user and pass
                    if(aURL.host.match(/@/i)) {
                        var tmpHost2 = aURL.host;
                        aURL.host = tmpHost2.split('@')[1];
                        var tmpUserPass = tmpHost2.split('@')[0];
                        if(tmpUserPass.match(/\:/)) {
                            aURL.user = tmpUserPass.split(':')[0];
                            aURL.pass = tmpUserPass.split(':')[1];
                        } else {
                            aURL.user = tmpUserPass;
                            aURL.pass = false;
                        }
                    }
                }
            }
            return aURL;
        },

        /**
         * Get last loaded script element
         *
         * @method currentScriptElement
         * @param {String} [match] String to match against the script src attribute
         * @return {DOMElement|Boolean} Returns the <script> DOM Element or false if unable to find it.
         * @public
         * @static
         */
        currentScriptElement: function(match)
        {
            var aScripts = document.getElementsByTagName('script');
            if(typeof(match) === 'undefined') {
                if(aScripts.length > 0) {
                    return aScripts[(aScripts.length - 1)];
                } else {
                    return false;
                }
            } else {
                var curScript = false;
                var re = new RegExp(""+match+"", "i");
                for(var i=0, total = aScripts.length; i < total; i++) {
                    curScript = aScripts[i];
                    if(re.test(curScript.src)) {
                        return curScript;
                    }
                }
                return false;
            }
        },

        
        /*
        base64Encode: function(string)
        {
            /**
         * --function {String} ?
         * --Convert a string to BASE 64
         * @param {String} string - string to convert
         * @return base64 encoded string
         *
         * 
            if(!SAPO.Utility.String || typeof(SAPO.Utility.String) === 'undefined') {
                throw "SAPO.Utility.Url.base64Encode depends of SAPO.Utility.String, which has not been referred.";
            }

            var output = "";
            var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
            var i = 0;

            var input = SAPO.Utility.String.utf8Encode(string);

            while (i < input.length) {

                chr1 = input.charCodeAt(i++);
                chr2 = input.charCodeAt(i++);
                chr3 = input.charCodeAt(i++);

                enc1 = chr1 >> 2;
                enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
                enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
                enc4 = chr3 & 63;

                if (isNaN(chr2)) {
                    enc3 = enc4 = 64;
                } else if (isNaN(chr3)) {
                    enc4 = 64;
                }

                output = output +
                this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
                this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
            }
            return output;
        },
        base64Decode: function(string)
        {
         * --function {String} ?
         * Decode a BASE 64 encoded string
         * --param {String} string base64 encoded string
         * --return string decoded
            if(!SAPO.Utility.String || typeof(SAPO.Utility.String) === 'undefined') {
                throw "SAPO.Utility.Url.base64Decode depends of SAPO.Utility.String, which has not been referred.";
            }

            var output = "";
            var chr1, chr2, chr3;
            var enc1, enc2, enc3, enc4;
            var i = 0;

            var input = string.replace(/[^A-Za-z0-9\+\/\=]/g, "");

            while (i < input.length) {

                enc1 = this._keyStr.indexOf(input.charAt(i++));
                enc2 = this._keyStr.indexOf(input.charAt(i++));
                enc3 = this._keyStr.indexOf(input.charAt(i++));
                enc4 = this._keyStr.indexOf(input.charAt(i++));

                chr1 = (enc1 << 2) | (enc2 >> 4);
                chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
                chr3 = ((enc3 & 3) << 6) | enc4;

                output = output + String.fromCharCode(chr1);

                if (enc3 !== 64) {
                    output = output + String.fromCharCode(chr2);
                }
                if (enc4 !== 64) {
                    output = output + String.fromCharCode(chr3);
                }
            }
            output = SAPO.Utility.String.utf8Decode(output);
            return output;
        },
        */


        /**
         * Debug function ?
         *
         * @method _debug
         * @private
         * @static
         */
        _debug: function() {}

    };

    return Url;

});

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
     * @uses Ink.Dom.Event
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

            if( this._options.stopEvents === true ){
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


/**
 * @module Ink.Util.String_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.Util.String', '1', [], function() {

    'use strict';

    /**
     * String Manipulation Utilities
     *
     * @class Ink.Util.String
     * @version 1
     * @static
     */
    var InkUtilString = {

        /**
         * List of special chars
         * 
         * @property _chars
         * @type {Array}
         * @private
         * @readOnly
         * @static
         */
        _chars: ['&','à','á','â','ã','ä','å','æ','ç','è','é',
                'ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô',
                'õ','ö','ø','ù','ú','û','ü','ý','þ','ÿ','À',
                'Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë',
                'Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö',
                'Ø','Ù','Ú','Û','Ü','Ý','Þ','€','\"','ß','<',
                '>','¢','£','¤','¥','¦','§','¨','©','ª','«',
                '¬','\xad','®','¯','°','±','²','³','´','µ','¶',
                '·','¸','¹','º','»','¼','½','¾'],

        /**
         * List of the special characters' html entities
         * 
         * @property _entities
         * @type {Array}
         * @private
         * @readOnly
         * @static
         */
        _entities: ['amp','agrave','aacute','acirc','atilde','auml','aring',
                    'aelig','ccedil','egrave','eacute','ecirc','euml','igrave',
                    'iacute','icirc','iuml','eth','ntilde','ograve','oacute',
                    'ocirc','otilde','ouml','oslash','ugrave','uacute','ucirc',
                    'uuml','yacute','thorn','yuml','Agrave','Aacute','Acirc',
                    'Atilde','Auml','Aring','AElig','Ccedil','Egrave','Eacute',
                    'Ecirc','Euml','Igrave','Iacute','Icirc','Iuml','ETH','Ntilde',
                    'Ograve','Oacute','Ocirc','Otilde','Ouml','Oslash','Ugrave',
                    'Uacute','Ucirc','Uuml','Yacute','THORN','euro','quot','szlig',
                    'lt','gt','cent','pound','curren','yen','brvbar','sect','uml',
                    'copy','ordf','laquo','not','shy','reg','macr','deg','plusmn',
                    'sup2','sup3','acute','micro','para','middot','cedil','sup1',
                    'ordm','raquo','frac14','frac12','frac34'],

        /**
         * List of accented chars
         * 
         * @property _accentedChars
         * @type {Array}
         * @private
         * @readOnly
         * @static
         */
        _accentedChars:['à','á','â','ã','ä','å',
                        'è','é','ê','ë',
                        'ì','í','î','ï',
                        'ò','ó','ô','õ','ö',
                        'ù','ú','û','ü',
                        'ç','ñ',
                        'À','Á','Â','Ã','Ä','Å',
                        'È','É','Ê','Ë',
                        'Ì','Í','Î','Ï',
                        'Ò','Ó','Ô','Õ','Ö',
                        'Ù','Ú','Û','Ü',
                        'Ç','Ñ'],

        /**
         * List of the accented chars (above), but without the accents
         * 
         * @property _accentedRemovedChars
         * @type {Array}
         * @private
         * @readOnly
         * @static
         */
        _accentedRemovedChars:['a','a','a','a','a','a',
                               'e','e','e','e',
                               'i','i','i','i',
                               'o','o','o','o','o',
                               'u','u','u','u',
                               'c','n',
                               'A','A','A','A','A','A',
                               'E','E','E','E',
                               'I','I','I','I',
                               'O','O','O','O','O',
                               'U','U','U','U',
                               'C','N'],
        /**
         * Object that contains the basic HTML unsafe chars, as keys, and their HTML entities as values
         * 
         * @property _htmlUnsafeChars
         * @type {Object}
         * @private
         * @readOnly
         * @static
         */
        _htmlUnsafeChars:{'<':'&lt;','>':'&gt;','&':'&amp;','"':'&quot;',"'":'&apos;'},

        /**
         * Convert first letter of a word to upper case <br />
         * If param as more than one word, it converts first letter of all words that have more than 2 letters
         *
         * @method ucFirst
         * @param {String} string
         * @return {String} string camel cased
         * @public
         * @static
         */
        ucFirst: function(string)
        {
            return string ? String(string).replace(/(^|\s)(\w)(\S{2,})/g, function(_, $1, $2, $3){
                return $1 + $2.toUpperCase() + $3.toLowerCase();
            }) : string;
        },

        /**
         * Remove spaces and new line from biggin and ends of string
         *
         * @method trim
         * @param {String} string
         * @return {String} string trimmed
         * @public
         * @static
         */
        trim: function(string)
        {
            if (typeof string === 'string') {
                return string.replace(/^\s+|\s+$|\n+$/g, '');
            }
            return string;
        },

        /**
         * Removes HTML tags of string
         *
         * @method stripTags
         * @param {String} string
         * @param {String} allowed
         * @return {String} String stripped from HTML tags, leaving only the allowed ones (if any)
         * @public
         * @static
         * @example
         *     <script>
         *          var myvar='isto e um texto <b>bold</b> com imagem <img src=""> e br <br /> um <p>paragrafo</p>';
         *          SAPO.Utility.String.stripTags(myvar, 'b,u');
         *     </script>
         */
        stripTags: function(string, allowed)
        {
            if (allowed && typeof allowed === 'string') {
                var aAllowed = this.trim(allowed).split(',');
                var aNewAllowed = [];
                var cleanedTag = false;
                for(var i=0; i < aAllowed.length; i++) {
                    if(this.trim(aAllowed[i]) !== '') {
                        cleanedTag = this.trim(aAllowed[i].replace(/(\<|\>)/g, '').replace(/\s/, ''));
                        aNewAllowed.push('(<'+cleanedTag+'\\s[^>]+>|<(\\s|\\/)?(\\s|\\/)?'+cleanedTag+'>)');
                    }
                }
                var strAllowed = aNewAllowed.join('|');
                var reAllowed = new RegExp(strAllowed, "i");

                var aFoundTags = string.match(new RegExp("<[^>]*>", "g"));

                for(var j=0; j < aFoundTags.length; j++) {
                    if(!aFoundTags[j].match(reAllowed)) {
                        string = string.replace((new RegExp(aFoundTags[j], "gm")), '');
                    }
                }
                return string;
            } else {
                return string.replace(/\<[^\>]+\>/g, '');
            }
        },

        /**
         * Convert listed characters to HTML entities
         *
         * @method htmlEntitiesEncode
         * @param {String} string
         * @return {String} string encoded
         * @public
         * @static
         */
        htmlEntitiesEncode: function(string)
        {
            if (string && string.replace) {
                var re = false;
                for (var i = 0; i < this._chars.length; i++) {
                    re = new RegExp(this._chars[i], "gm");
                    string = string.replace(re, '&' + this._entities[i] + ';');
                }
            }
            return string;
        },

        /**
         * Convert listed HTML entities to character
         *
         * @method htmlEntitiesDecode
         * @param {String} string
         * @return {String} string decoded
         * @public
         * @static
         */
        htmlEntitiesDecode: function(string)
        {
            if (string && string.replace) {
                var re = false;
                for (var i = 0; i < this._entities.length; i++) {
                    re = new RegExp("&"+this._entities[i]+";", "gm");
                    string = string.replace(re, this._chars[i]);
                }
                string = string.replace(/&#[^;]+;?/g, function($0){
                    if ($0.charAt(2) === 'x') {
                        return String.fromCharCode(parseInt($0.substring(3), 16));
                    }
                    else {
                        return String.fromCharCode(parseInt($0.substring(2), 10));
                    }
                });
            }
            return string;
        },

        /**
         * Encode a string to UTF8
         *
         * @method utf8Encode
         * @param {String} string
         * @return {String} string utf8 encoded
         * @public
         * @static
         */
        utf8Encode: function(string)
        {
            string = string.replace(/\r\n/g,"\n");
            var utfstring = "";

            for (var n = 0; n < string.length; n++) {

                var c = string.charCodeAt(n);

                if (c < 128) {
                    utfstring += String.fromCharCode(c);
                }
                else if((c > 127) && (c < 2048)) {
                    utfstring += String.fromCharCode((c >> 6) | 192);
                    utfstring += String.fromCharCode((c & 63) | 128);
                }
                else {
                    utfstring += String.fromCharCode((c >> 12) | 224);
                    utfstring += String.fromCharCode(((c >> 6) & 63) | 128);
                    utfstring += String.fromCharCode((c & 63) | 128);
                }

            }
            return utfstring;
        },

        /**
         * Make a string shorter without cutting words
         *
         * @method shortString
         * @param {String} str
         * @param {Number} n - number of chars of the short string
         * @return {String} string shortened
         * @public
         * @static
         */
        shortString: function(str,n) {
          var words = str.split(' ');
          var resultstr = '';
          for(var i = 0; i < words.length; i++ ){
            if((resultstr + words[i] + ' ').length>=n){
              resultstr += '&hellip;';
              break;
              }
            resultstr += words[i] + ' ';
            }
          return resultstr;
        },

        /**
         * Truncates a string, breaking words and adding ... at the end
         *
         * @method truncateString
         * @param {String} str
         * @param {Number} length - length limit for the string. String will be
         *        at most this big, ellipsis included.
         * @return {String} string truncated
         * @public
         * @static
         */
        truncateString: function(str, length) {
            if(str.length - 1 > length) {
                return str.substr(0, length - 1) + "\u2026";
            } else {
                return str;
            }
        },

        /**
         * Decode a string from UTF8
         *
         * @method utf8Decode
         * @param {String} string
         * @return {String} string utf8 decoded
         * @public
         * @static
         */
        utf8Decode: function(utfstring)
        {
            var string = "";
            var i = 0, c = 0, c2 = 0, c3 = 0;

            while ( i < utfstring.length ) {

                c = utfstring.charCodeAt(i);

                if (c < 128) {
                    string += String.fromCharCode(c);
                    i++;
                }
                else if((c > 191) && (c < 224)) {
                    c2 = utfstring.charCodeAt(i+1);
                    string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                    i += 2;
                }
                else {
                    c2 = utfstring.charCodeAt(i+1);
                    c3 = utfstring.charCodeAt(i+2);
                    string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                    i += 3;
                }

            }
            return string;
        },

        /**
         * Convert all accented chars to char without accent.
         *
         * @method removeAccentedChars
         * @param {String} string
         * @return {String} string without accented chars
         * @public
         * @static
         */
        removeAccentedChars: function(string)
        {
            var newString = string;
            var re = false;
            for (var i = 0; i < this._accentedChars.length; i++) {
                re = new RegExp(this._accentedChars[i], "gm");
                newString = newString.replace(re, '' + this._accentedRemovedChars[i] + '');
            }
            return newString;
        },

        /**
         * Count the number of occurrences of a specific needle in a haystack
         *
         * @method substrCount
         * @param {String} haystack
         * @param {String} needle
         * @return {Number} Number of occurrences
         * @public
         * @static
         */
        substrCount: function(haystack,needle)
        {
            return haystack ? haystack.split(needle).length - 1 : 0;
        },

        /**
         * Eval a JSON string to a JS object
         *
         * @method evalJSON
         * @param {String} strJSON
         * @param {Boolean} sanitize
         * @return {Object} JS Object
         * @public
         * @static
         */
        evalJSON: function(strJSON, sanitize)
        {
            if( (typeof sanitize === 'undefined' || sanitize === null) || this.isJSON(strJSON)) {
                try {
                    if(typeof(JSON) !== "undefined" && typeof(JSON.parse) !== 'undefined'){
                        return JSON.parse(strJSON);
                    }
                    return eval('('+strJSON+')');
                } catch(e) {
                    throw new Error('ERROR: Bad JSON string...');
                }
            }
        },

        /**
         * Checks if a string is a valid JSON object (string encoded)
         *
         * @method isJSON
         * @param {String} str
         * @return {Boolean}
         * @public
         * @static
         */
        isJSON: function(str)
        {
            str = str.replace(/\\./g, '@').replace(/"[^"\\\n\r]*"/g, '');
            return (/^[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]*$/).test(str);
        },

        /**
         * Escapes unsafe html chars to their entities
         *
         * @method htmlEscapeUnsafe
         * @param {String} str String to escape
         * @return {String} Escaped string
         * @public
         * @static
         */
        htmlEscapeUnsafe: function(str){
            var chars = this._htmlUnsafeChars;
            return str != null ? String(str).replace(/[<>&'"]/g,function(c){return chars[c];}) : str;
        },

        /**
         * Normalizes whitespace in string.
         * String is trimmed and sequences of many
         * Whitespaces are collapsed.
         *
         * @method normalizeWhitespace
         * @param {String} str String to normalize
         * @return {String} string normalized
         * @public
         * @static
         */
        normalizeWhitespace: function(str){
            return str != null ? this.trim(String(str).replace(/\s+/g,' ')) : str;
        },

        /**
         * Converts string to unicode
         *
         * @method toUnicode
         * @param {String} str
         * @return {String} string unicoded
         * @public
         * @static
         */
        toUnicode: function(str)
        {
            if (typeof str === 'string') {
                var unicodeString = '';
                var inInt = false;
                var theUnicode = false;
                var total = str.length;
                var i=0;

                while(i < total)
                {
                    inInt = str.charCodeAt(i);
                    if( (inInt >= 32 && inInt <= 126) ||
                            inInt == 8 ||
                            inInt == 9 ||
                            inInt == 10 ||
                            inInt == 12 ||
                            inInt == 13 ||
                            inInt == 32 ||
                            inInt == 34 ||
                            inInt == 47 ||
                            inInt == 58 ||
                            inInt == 92) {

                        /*
                        if(inInt == 34 || inInt == 92 || inInt == 47) {
                            theUnicode = '\\'+str.charAt(i);
                        } else {
                        }
                        */
                        if(inInt == 8) {
                            theUnicode = '\\b';
                        } else if(inInt == 9) {
                            theUnicode = '\\t';
                        } else if(inInt == 10) {
                            theUnicode = '\\n';
                        } else if(inInt == 12) {
                            theUnicode = '\\f';
                        } else if(inInt == 13) {
                            theUnicode = '\\r';
                        } else {
                            theUnicode = str.charAt(i);
                        }
                    } else {
                        theUnicode = str.charCodeAt(i).toString(16)+''.toUpperCase();
                        while (theUnicode.length < 4) {
                            theUnicode = '0' + theUnicode;
                        }
                        theUnicode = '\\u' + theUnicode;
                    }
                    unicodeString += theUnicode;

                    i++;
                }
                return unicodeString;
            }
        },

        /**
         * Escapes a unicode character. returns \xXX if hex smaller than 0x100, otherwise \uXXXX
         *
         * @method ucFirst
         * @param {String} c Char
         * @return {String} escaped char
         * @public
         * @static
         */

        /**
         * @param {String} c char
         */
        escape: function(c) {
            var hex = (c).charCodeAt(0).toString(16).split('');
            if (hex.length < 3) {
                while (hex.length < 2) { hex.unshift('0'); }
                hex.unshift('x');
            }
            else {
                while (hex.length < 4) { hex.unshift('0'); }
                hex.unshift('u');
            }

            hex.unshift('\\');
            return hex.join('');
        },

        /**
         * Unescapes a unicode character escape sequence
         *
         * @method unescape
         * @param {String} es Escape sequence
         * @return {String} String des-unicoded
         * @public
         * @static
         */
        unescape: function(es) {
            var idx = es.lastIndexOf('0');
            idx = idx === -1 ? 2 : Math.min(idx, 2);
            //console.log(idx);
            var hexNum = es.substring(idx);
            //console.log(hexNum);
            var num = parseInt(hexNum, 16);
            return String.fromCharCode(num);
        },

        /**
         * Escapes a string to unicode characters
         *
         * @method escapeText
         * @param {String} txt
         * @param {Array} [whiteList]
         * @return {String} Escaped to Unicoded string
         * @public
         * @static
         */
        escapeText: function(txt, whiteList) {
            if (whiteList === undefined) {
                whiteList = ['[', ']', '\'', ','];
            }
            var txt2 = [];
            var c, C;
            for (var i = 0, f = txt.length; i < f; ++i) {
                c = txt[i];
                C = c.charCodeAt(0);
                if (C < 32 || C > 126 && whiteList.indexOf(c) === -1) {
                    c = this.escape(c);
                }
                txt2.push(c);
            }
            return txt2.join('');
        },

        /**
         * Regex to check escaped strings
         *
         * @property escapedCharRegex
         * @type {Regex}
         * @public
         * @readOnly
         * @static
         */
        escapedCharRegex: /(\\x[0-9a-fA-F]{2})|(\\u[0-9a-fA-F]{4})/g,

        /**
         * Unescapes a string
         *
         * @method unescapeText
         * @param {String} txt
         * @return {String} Unescaped string
         * @public
         * @static
         */
        unescapeText: function(txt) {
            /*jshint boss:true */
            var m;
            while (m = this.escapedCharRegex.exec(txt)) {
                m = m[0];
                txt = txt.replace(m, this.unescape(m));
                this.escapedCharRegex.lastIndex = 0;
            }
            return txt;
        },

        /**
         * Compares two strings
         *
         * @method strcmp
         * @param {String} str1
         * @param {String} str2
         * @return {Number}
         * @public
         * @static
         */
        strcmp: function(str1, str2) {
            return ((str1 === str2) ? 0 : ((str1 > str2) ? 1 : -1));
        },

        /**
         * Splits long string into string of, at most, maxLen (that is, all but last have length maxLen,
         * last can measure maxLen or less)
         *
         * @method packetize
         * @param {String} string string to divide
         * @param {Number} maxLen packet size
         * @return {Array} string divided
         * @public
         * @static
         */
        packetize: function(str, maxLen) {
            var len = str.length;
            var parts = new Array( Math.ceil(len / maxLen) );
            var chars = str.split('');
            var sz, i = 0;
            while (len) {
                sz = Math.min(maxLen, len);
                parts[i++] = chars.splice(0, sz).join('');
                len -= sz;
            }
            return parts;
        }
    };

    return InkUtilString;

});

/**
 * @module Ink.Util.Dumper_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.Util.Dumper', '1', [], function() {

    'use strict';

    /**
     * Dump/Profiling Utilities
     *
     * @class Ink.Util.Dumper
     * @version 1
     * @static
     */
    var Dumper = {

        /**
         * Hex code for the 'tab'
         * 
         * @property _tab
         * @type {String}
         * @private
         * @readOnly
         * @static
         *
         */
        _tab: '\xA0\xA0\xA0\xA0',

        /**
         * Function that returns the argument passed formatted
         *
         * @method _formatParam
         * @param {Mixed} param
         * @return {String} The argument passed formatted
         * @private
         * @static
         */
        _formatParam: function(param)
        {
            var formated = '';

            switch(typeof(param)) {
                case 'string':
                    formated = '(string) '+param;
                    break;
                case 'number':
                    formated = '(number) '+param;
                    break;
                case 'boolean':
                    formated = '(boolean) '+param;
                    break;
                case 'object':
                    if(param !== null) {
                        if(param.constructor === Array) {
                            formated = 'Array \n{\n' + this._outputFormat(param, 0) + '\n}';
                        } else {
                            formated = 'Object \n{\n' + this._outputFormat(param, 0) + '\n}';
                        }
                    } else {
                        formated = 'null';
                    }
                    break;
                default:
                    formated = false;
            }

            return formated;
        },

        /**
         * Function that returns the tabs concatenated
         *
         * @method _getTabs
         * @param {Number} numberOfTabs Number of Tabs
         * @return {String} Tabs concatenated
         * @private
         * @static
         */
        _getTabs: function(numberOfTabs)
        {
            var tabs = '';
            for(var _i = 0; _i < numberOfTabs; _i++) {
                tabs += this._tab;
            }
            return tabs;
        },

        /**
         * Function that formats the parameter to display
         *
         * @method _outputFormat
         * @param {Any} param
         * @param {Number} dim
         * @return {String} The parameter passed formatted to displat
         * @private
         * @static
         */
        _outputFormat: function(param, dim)
        {
            var formated = '';
            //var _strVal = false;
            var _typeof = false;
            for(var key in param) {
                if(param[key] !== null) {
                    if(typeof(param[key]) === 'object' && (param[key].constructor === Array || param[key].constructor === Object)) {
                        if(param[key].constructor === Array) {
                            _typeof = 'Array';
                        } else if(param[key].constructor === Object) {
                            _typeof = 'Object';
                        }
                        formated += this._tab + this._getTabs(dim) + '[' + key + '] => <b>'+_typeof+'</b>\n';
                        formated += this._tab + this._getTabs(dim) + '{\n';
                        formated += this._outputFormat(param[key], dim + 1) + this._tab + this._getTabs(dim) + '}\n';
                    } else if(param[key].constructor === Function) {
                        continue;
                    } else {
                        formated = formated + this._tab + this._getTabs(dim) + '[' + key + '] => ' + param[key] + '\n';
                    }
                } else {
                    formated = formated + this._tab + this._getTabs(dim) + '[' + key + '] => null \n';
                }
            }
            return formated;
        },

        /**
         * Print variable structure. Can be passed an output target
         *
         * @method printDump
         * @param {Object|String|Boolean} param
         * @param {optional String|Object} target (can be an element ID or an element)
         * @public
         * @static
         */
        printDump: function(param, target)
        {
            if(!target || typeof(target) === 'undefined') {
                document.write('<pre>'+this._formatParam(param)+'</pre>');
            } else {
                if(typeof(target) === 'string') {
                    document.getElementById(target).innerHTML = '<pre>' + this._formatParam(param) + '</pre>';
                } else if(typeof(target) === 'object') {
                    target.innerHTML = '<pre>'+this._formatParam(param)+'</pre>';
                } else {
                    throw "TARGET must be an element or an element ID";
                }
            }
        },

        /**
         * Function that returns the variable's structure
         *
         * @method returnDump
         * @param {Object|String|Boolean} param
         * @return {String} The variable structure
         * @public
         * @static
         */
        returnDump: function(param)
        {
            return this._formatParam(param);
        },

        /**
         * Function that alerts the variable structure
         *
         * @method alertDump
         * @param {Object|String|Boolean} param
         * @public
         * @static
         */
        alertDump: function(param)
        {
            window.alert(this._formatParam(param).replace(/(<b>)(Array|Object)(<\/b>)/g, "$2"));
        },

        /**
         * Print to new window the variable structure
         *
         * @method windowDump
         * @param {Object|String|Boolean} param
         * @public
         * @static
         */
        windowDump: function(param)
        {
            var dumperwindow = 'dumperwindow_'+(Math.random() * 10000);
            var win = window.open('',
                dumperwindow,
                'width=400,height=300,left=50,top=50,status,menubar,scrollbars,resizable'
            );
            win.document.open();
            win.document.write('<pre>'+this._formatParam(param)+'</pre>');
            win.document.close();
            win.focus();
        }

    };

    return Dumper;

});

/**
 * @module Ink.Util.Date_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.Util.Date', '1', [], function() {

    'use strict';

    /**
     * Class to provide the same features that php date does
     *
     * @class Ink.Util.Date
     * @version 1
     * @static
     */
    var InkDate = {

        /**
         * Function that returns the string representation of the month [PT only]
         *
         * @method _months
         * @param {Number} index Month javascript (0 to 11)
         * @return {String} The month's name
         * @private
         * @static
         * @example
         *     console.log( InkDate._months(0) ); // Result: Janeiro
         */
        _months: function(index){
            var _m = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
            return _m[index];
        },

        /**
         * Function that returns the month [PT only] ( 0 to 11 )
         *
         * @method _iMonth
         * @param {String} month Month javascript (0 to 11)
         * @return {Number} The month's number
         * @private
         * @static
         * @example
         *     console.log( InkDate._iMonth('maio') ); // Result: 4
         */
        _iMonth : function( month )
        {
            if ( Number( month ) ) { return +month - 1; }
            return {
                'janeiro'   : 0  ,
                'jan'       : 0  ,
                'fevereiro' : 1  ,
                'fev'       : 1  ,
                'março'     : 2  ,
                'mar'       : 2  ,
                'abril'     : 3  ,
                'abr'       : 3  ,
                'maio'      : 4  ,
                'mai'       : 4  ,
                'junho'     : 5  ,
                'jun'       : 5  ,
                'julho'     : 6  ,
                'jul'       : 6  ,
                'agosto'    : 7  ,
                'ago'       : 7  ,
                'setembro'  : 8  ,
                'set'       : 8  ,
                'outubro'   : 9  ,
                'out'       : 9  ,
                'novembro'  : 10 ,
                'nov'       : 10 ,
                'dezembro'  : 11 ,
                'dez'       : 11
            }[ month.toLowerCase( ) ];
        } ,

        /**
         * Function that returns the representation the day of the week [PT Only]
         *
         * @method _wDays
         * @param {Number} index Week's day index
         * @return {String} The week's day name
         * @private
         * @static
         * @example
         *     console.log( InkDate._wDays(0) ); // Result: Domingo
         */
        _wDays: function(index){
            var _d = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
            return _d[index];
        },

        /**
         * Function that returns day of the week in javascript 1 to 7
         *
         * @method _iWeek
         * @param {String} week Week's day name
         * @return {Number} The week's day index
         * @private
         * @static
         * @example
         *     console.log( InkDate._iWeek('quarta') ); // Result: 3
         */
        _iWeek: function( week )
        {
            if ( Number( week ) ) { return +week || 7; }
            return {
                'segunda' : 1  ,
                'seg'     : 1  ,
                'terça'   : 2  ,
                'ter'     : 2  ,
                'quarta'  : 3  ,
                'qua'     : 3  ,
                'quinta'  : 4  ,
                'qui'     : 4  ,
                'sexta'   : 5  ,
                'sex'     : 5  ,
                'sábado'  : 6  ,
                'sáb'     : 6  ,
                'domingo' : 7  ,
                'dom'     : 7
            }[ week.toLowerCase( ) ];
        },

        /**
         * Function that returns the number of days of a given month (m) on a given year (y)
         *
         * @method _daysInMonth
         * @param {Number} _m Month
         * @param {Number} _y Year
         * @return {Number} Number of days of a give month on a given year
         * @private
         * @static
         * @example
         *     console.log( InkDate._daysInMonth(2,2013) ); // Result: 28
         */
        _daysInMonth: function(_m,_y){
            var nDays;

            if(_m===1 || _m===3 || _m===5 || _m===7 || _m===8 || _m===10 || _m===12)
            {
                nDays= 31;
            }
            else if ( _m===4 || _m===6 || _m===9 || _m===11)
            {
                nDays = 30;
            }
            else
            {
                if((_y%400===0) || (_y%4===0 && _y%100!==0))
                {
                    nDays = 29;
                }
                else
                {
                    nDays = 28;
                }
            }
            return nDays;
        },

        /**
         * Function that works exactly as php date() function
         * Works like PHP 5.2.2 <a href="http://php.net/manual/en/function.date.php" target="_blank">PHP Date function</a>
         *
         * @method get
         * @param {String}        format - as the string in which the date it will be formatted - mandatory
         * @param {Date} [_date] - the date to format. If undefined it will do it on now() date. Can receive unix timestamp or a date object
         * @return {String} Formatted date
         * @public
         * @static
         * @example
         *     <script>
         *         Ink.requireModules( ['Ink.Util.Date_1'], function( InkDate ){
         *             console.log( InkDate.get('Y-m-d') ); // Result (at the time of writing): 2013-05-07
         *         });
         *     </script>
         */
        get: function(format, _date){
            /*jshint maxcomplexity:50 */
            if(typeof(format) === 'undefined' || format === ''){
                format = "Y-m-d";
            }


            var iFormat = format.split("");
            var result = new Array(iFormat.length);
            var escapeChar = "\\";
            var jsDate;

        if (typeof(_date) === 'undefined'){
            jsDate = new Date();
        } else if (typeof(_date)==='number'){
            jsDate = new Date(_date*1000);
        } else {
            jsDate = new Date(_date);
        }

        var jsFirstDay, jsThisDay, jsHour;
        /* This switch is presented in the same order as in php date function (PHP 5.2.2) */
        for (var i = 0; i < iFormat.length; i++) {
           switch(iFormat[i]) {
                case escapeChar:
                    result[i] = iFormat[i+1];
                    i++;
                    break;


                /* DAY */
                case "d":   /* Day of the month, 2 digits with leading zeros; ex: 01 to 31  */
                    var jsDay = jsDate.getDate();
                    result[i] = (String(jsDay).length > 1) ? jsDay : "0" + jsDay;
                    break;

                case "D":   /* A textual representation of a day, three letters; Seg to Dom */
                    result[i] = this._wDays(jsDate.getDay()).substring(0, 3);
                    break;

                case "j":  /* Day of the month without leading zeros; ex: 1 to 31  */
                    result[i] = jsDate.getDate();
                    break;

                case "l":   /* A full textual representation of the day of the week; Domingo to Sabado  */
                    result[i] = this._wDays(jsDate.getDay());
                    break;

                case "N":  /* ISO-8601 numeric representation of the day of the week; 1 (Segunda) to 7 (Domingo)  */
                    result[i] = jsDate.getDay() || 7;
                    break;

                case "S":  /* English ordinal suffix for the day of the month, 2 characters; st, nd, rd or th. Works well with j */
                    var temp     = jsDate.getDate();
                    var suffixes = ["st", "nd", "rd"];
                    var suffix   = "";

                    if (temp >= 11 && temp <= 13) {
                        result[i] = "th";
                    } else {
                        result[i]  = (suffix = suffixes[String(temp).substr(-1) - 1]) ? (suffix) : ("th");
                    }
                    break;

                case "w":    /* Numeric representation of the day of the week; 0 (for Sunday) through 6 (for Saturday) */
                    result[i] = jsDate.getDay();
                    break;

                case "z":    /* The day of the year (starting from 0); 0 to 365 */
                    jsFirstDay = Date.UTC(jsDate.getFullYear(), 0, 0);
                    jsThisDay = Date.UTC(jsDate.getFullYear(), jsDate.getMonth(), jsDate.getDate());
                    result[i] = Math.floor((jsThisDay - jsFirstDay) / (1000 * 60 * 60 * 24));
                    break;

                /* WEEK */
                case "W":    /* ISO-8601 week number of year, weeks starting on Monday; ex: 42 (the 42nd week in the year)  */
                    var jsYearStart = new Date( jsDate.getFullYear( ) , 0 , 1 );
                    jsFirstDay = jsYearStart.getDay() || 7;

                    var days = Math.floor( ( jsDate - jsYearStart ) / ( 24 * 60 * 60 * 1000 ) + 1 );

                    result[ i ] = Math.ceil( ( days - ( 8 - jsFirstDay ) ) / 7 ) + 1;
                    break;


                /* MONTH */
                case "F":   /* A full textual representation of a month, such as Janeiro or Marco; Janeiro a Dezembro */
                    result[i] = this._months(jsDate.getMonth());
                    break;

                case "m":   /* Numeric representation of a month, with leading zeros; 01 to 12  */
                    var jsMonth = String(jsDate.getMonth() + 1);
                    result[i] = (jsMonth.length > 1) ? jsMonth : "0" + jsMonth;
                    break;

                case "M":   /* A short textual representation of a month, three letters; Jan a Dez */
                    result[i] = this._months(jsDate.getMonth()).substring(0,3);
                    break;

                case "n":   /* Numeric representation of a month, without leading zeros; 1 a 12  */
                    result[i] = jsDate.getMonth() + 1;
                    break;

                case "t":   /* Number of days in the given month; ex: 28 */
                    result[i] = this._daysInMonth(jsDate.getMonth()+1,jsDate.getYear());
                    break;

                /* YEAR */
                case "L":   /* Whether it's a leap year; 1 if it is a leap year, 0 otherwise.  */
                    var jsYear = jsDate.getFullYear();
                    result[i] = (jsYear % 4) ? false : ( (jsYear % 100) ?  true : ( (jsYear % 400) ? false : true  ) );
                    break;

                case "o":  /* ISO-8601 year number. This has the same value as Y, except that if the ISO week number (W) belongs to the previous or next year, that year is used instead.  */
                    throw '"o" not implemented!';

                case "Y":  /* A full numeric representation of a year, 4 digits; 1999  */
                    result[i] = jsDate.getFullYear();
                    break;

                case "y":  /* A two digit representation of a year; 99  */
                    result[i] = String(jsDate.getFullYear()).substring(2);
                    break;

                /* TIME */
                case "a":   /* Lowercase Ante meridiem and Post meridiem; am or pm */
                    result[i] = (jsDate.getHours() < 12) ? "am" : "pm";
                    break;

                case "A":   /* Uppercase Ante meridiem and Post meridiem; AM or PM  */
                    result[i] = (jsDate.getHours < 12) ? "AM" : "PM";
                    break;

                case "B":  /* Swatch Internet time; 000 through 999  */
                    throw '"B" not implemented!';

                case "g":   /* 12-hour format of an hour without leading zeros;  1 to 12 */
                    jsHour = jsDate.getHours();
                    result[i] = (jsHour <= 12) ? jsHour : (jsHour - 12);
                    break;

                case "G":   /* 24-hour format of an hour without leading zeros; 1 to 23 */
                    result[i] = String(jsDate.getHours());
                    break;

                case "h":   /* 12-hour format of an hour with leading zeros; 01 to 12 */
                    jsHour = String(jsDate.getHours());
                    jsHour = (jsHour <= 12) ? jsHour : (jsHour - 12);
                    result[i] = (jsHour.length > 1) ? jsHour : "0" + jsHour;
                    break;

                case "H":   /* 24-hour format of an hour with leading zeros; 01 to 24 */
                    jsHour = String(jsDate.getHours());
                    result[i] = (jsHour.length > 1) ? jsHour : "0" + jsHour;
                    break;

                case "i":   /* Minutes with leading zeros; 00 to 59 */
                    var jsMinute  = String(jsDate.getMinutes());
                    result[i] = (jsMinute.length > 1) ? jsMinute : "0" + jsMinute;
                    break;

                case "s":   /* Seconds with leading zeros; 00 to 59; */
                    var jsSecond  = String(jsDate.getSeconds());
                    result[i]  = (jsSecond.length > 1) ? jsSecond : "0" + jsSecond;
                    break;

                case "u":  /* Microseconds */
                    throw '"u" not implemented!';


                /* TIMEZONE */

                case "e": /* Timezone identifier  */
                    throw '"e" not implemented!';

                case "I":   /*  "1" if Daylight Savings Time, "0" otherwise. Works only on the northern hemisphere */
                    jsFirstDay = new Date(jsDate.getFullYear(), 0, 1);
                    result[i] = (jsDate.getTimezoneOffset() !== jsFirstDay.getTimezoneOffset()) ? (1) : (0);
                    break;

                case "O":  /* Difference to Greenwich time (GMT) in hours */
                    var jsMinZone = jsDate.getTimezoneOffset();
                    var jsMinutes = jsMinZone % 60;
                    jsHour = String(((jsMinZone - jsMinutes) / 60) * -1);

                    if (jsHour.charAt(0) !== "-") {
                        jsHour = "+" + jsHour;
                    }

                    jsHour = (jsHour.length === 3) ? (jsHour) : (jsHour.replace(/([+\-])(\d)/, "$1" + 0 + "$2"));
                    result[i]  = jsHour + jsMinutes + "0";
                    break;

                case "P": /* Difference to Greenwich time (GMT) with colon between hours and minutes */
                    throw '"P" not implemented!';

                case "T": /* Timezone abbreviation */
                    throw '"T" not implemented!';

                case "Z": /* Timezone offset in seconds. The offset for timezones west of UTC is always negative, and for those east of UTC is always positive. */
                    result[i] = jsDate.getTimezoneOffset() * 60;
                    break;


                /* FULL DATE/TIME  */

                case "c": /* ISO 8601 date */
                    throw '"c" not implemented!';

                case "r": /* RFC 2822 formatted date  */
                    var jsDayName = this._wDays(jsDate.getDay()).substr(0, 3);
                    var jsMonthName = this._months(jsDate.getMonth()).substr(0, 3);
                    result[i] = jsDayName + ", " + jsDate.getDate() + " " + jsMonthName + this.get(" Y H:i:s O",jsDate);
                    break;

                case "U":  /* Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)  */
                    result[i] = Math.floor(jsDate.getTime() / 1000);
                    break;

                default:
                    result[i] = iFormat[i];
            }
        }

        return result.join('');

        },

        /**
         * Functions that works like php date() function but return a date based on the formatted string
         * Works like PHP 5.2.2 <a href="http://php.net/manual/en/function.date.php" target="_blank">PHP Date function</a>
         *
         * @method set
         * @param {String} [format] As the string in which the date it will be formatted. By default is 'Y-m-d'
         * @param {String} str_date The date formatted - Mandatory.
         * @return {Date} Date object based on the formatted date
         * @public
         * @static
         */
        set : function( format , str_date ) {
            if ( typeof str_date === 'undefined' ) { return ; }
            if ( typeof format === 'undefined' || format === '' ) { format = "Y-m-d"; }

            var iFormat = format.split("");
            var result = new Array( iFormat.length );
            var escapeChar = "\\";
            var mList;

            var objIndex = {
                year  : undefined ,
                month : undefined ,
                day   : undefined ,
                dayY  : undefined ,
                dayW  : undefined ,
                week  : undefined ,
                hour  : undefined ,
                hourD : undefined ,
                min   : undefined ,
                sec   : undefined ,
                msec  : undefined ,
                ampm  : undefined ,
                diffM : undefined ,
                diffH : undefined ,
                date  : undefined
            };

            var matches = 0;

            /* This switch is presented in the same order as in php date function (PHP 5.2.2) */
            for ( var i = 0; i < iFormat.length; i++) {
                switch( iFormat[ i ] ) {
                    case escapeChar:
                        result[i]      = iFormat[ i + 1 ];
                        i++;
                        break;

                    /* DAY */
                    case "d":   /* Day of the month, 2 digits with leading zeros; ex: 01 to 31  */
                        result[ i ]    = '(\\d{2})';
                        objIndex.day   = { original : i , match : matches++ };
                        break;

                    case "j":  /* Day of the month without leading zeros; ex: 1 to 31  */
                        result[ i ]    = '(\\d{1,2})';
                        objIndex.day   = { original : i , match : matches++ };
                        break;

                    case "D":   /* A textual representation of a day, three letters; Seg to Dom */
                        result[ i ]    = '([\\wá]{3})';
                        objIndex.dayW  = { original : i , match : matches++ };
                        break;

                    case "l":   /* A full textual representation of the day of the week; Domingo to Sabado  */
                        result[i]      = '([\\wá]{5,7})';
                        objIndex.dayW  = { original : i , match : matches++ };
                        break;

                    case "N":  /* ISO-8601 numeric representation of the day of the week; 1 (Segunda) to 7 (Domingo)  */
                        result[ i ]    = '(\\d)';
                        objIndex.dayW  = { original : i , match : matches++ };
                        break;

                    case "w":    /* Numeric representation of the day of the week; 0 (for Sunday) through 6 (for Saturday) */
                        result[ i ]    = '(\\d)';
                        objIndex.dayW  = { original : i , match : matches++ };
                        break;

                    case "S":  /* English ordinal suffix for the day of the month, 2 characters; st, nd, rd or th. Works well with j */
                        result[ i ]    = '\\w{2}';
                        break;

                    case "z":    /* The day of the year (starting from 0); 0 to 365 */
                        result[ i ]    = '(\\d{1,3})';
                        objIndex.dayY  = { original : i , match : matches++ };
                        break;

                    /* WEEK */
                    case "W":    /* ISO-8601 week number of year, weeks starting on Monday; ex: 42 (the 42nd week in the year)  */
                        result[ i ]    = '(\\d{1,2})';
                        objIndex.week  = { original : i , match : matches++ };
                        break;

                    /* MONTH */
                    case "F":   /* A full textual representation of a month, such as Janeiro or Marco; Janeiro a Dezembro */
                        result[ i ]    = '([\\wç]{4,9})';
                        objIndex.month = { original : i , match : matches++ };
                        break;

                    case "M":   /* A short textual representation of a month, three letters; Jan a Dez */
                        result[ i ]    = '(\\w{3})';
                        objIndex.month = { original : i , match : matches++ };
                        break;

                    case "m":   /* Numeric representation of a month, with leading zeros; 01 to 12  */
                        result[ i ]    = '(\\d{2})';
                        objIndex.month = { original : i , match : matches++ };
                        break;

                    case "n":   /* Numeric representation of a month, without leading zeros; 1 a 12  */
                        result[ i ]    = '(\\d{1,2})';
                        objIndex.month = { original : i , match : matches++ };
                        break;

                    case "t":   /* Number of days in the given month; ex: 28 */
                        result[ i ]    = '\\d{2}';
                        break;

                    /* YEAR */
                    case "L":   /* Whether it's a leap year; 1 if it is a leap year, 0 otherwise.  */
                        result[ i ]    = '\\w{4,5}';
                        break;

                    case "o":  /* ISO-8601 year number. This has the same value as Y, except that if the ISO week number (W) belongs to the previous or next year, that year is used instead.  */
                        throw '"o" not implemented!';

                    case "Y":  /* A full numeric representation of a year, 4 digits; 1999  */
                        result[ i ]    = '(\\d{4})';
                        objIndex.year  = { original : i , match : matches++ };
                        break;

                    case "y":  /* A two digit representation of a year; 99  */
                        result[ i ]    = '(\\d{2})';
                        if ( typeof objIndex.year === 'undefined' || iFormat[ objIndex.year.original ] !== 'Y' ) {
                            objIndex.year = { original : i , match : matches++ };
                        }
                        break;

                    /* TIME */
                    case "a":   /* Lowercase Ante meridiem and Post meridiem; am or pm */
                        result[ i ]    = '(am|pm)';
                        objIndex.ampm  = { original : i , match : matches++ };
                        break;

                    case "A":   /* Uppercase Ante meridiem and Post meridiem; AM or PM  */
                        result[ i ]    = '(AM|PM)';
                        objIndex.ampm  = { original : i , match : matches++ };
                        break;

                    case "B":  /* Swatch Internet time; 000 through 999  */
                        throw '"B" not implemented!';

                    case "g":   /* 12-hour format of an hour without leading zeros;  1 to 12 */
                        result[ i ]    = '(\\d{1,2})';
                        objIndex.hourD = { original : i , match : matches++ };
                        break;

                    case "G":   /* 24-hour format of an hour without leading zeros; 1 to 23 */
                        result[ i ]    = '(\\d{1,2})';
                        objIndex.hour  = { original : i , match : matches++ };
                        break;

                    case "h":   /* 12-hour format of an hour with leading zeros; 01 to 12 */
                        result[ i ]    = '(\\d{2})';
                        objIndex.hourD = { original : i , match : matches++ };
                        break;

                    case "H":   /* 24-hour format of an hour with leading zeros; 01 to 24 */
                        result[ i ]    = '(\\d{2})';
                        objIndex.hour  = { original : i , match : matches++ };
                        break;

                    case "i":   /* Minutes with leading zeros; 00 to 59 */
                        result[ i ]    = '(\\d{2})';
                        objIndex.min   = { original : i , match : matches++ };
                        break;

                    case "s":   /* Seconds with leading zeros; 00 to 59; */
                        result[ i ]    = '(\\d{2})';
                        objIndex.sec   = { original : i , match : matches++ };
                        break;

                    case "u":  /* Microseconds */
                        throw '"u" not implemented!';

                    /* TIMEZONE */
                    case "e": /* Timezone identifier  */
                        throw '"e" not implemented!';

                    case "I":   /*  "1" if Daylight Savings Time, "0" otherwise. Works only on the northern hemisphere */
                        result[i]      = '\\d';
                        break;

                    case "O":  /* Difference to Greenwich time (GMT) in hours */
                        result[ i ]    = '([-+]\\d{4})';
                        objIndex.diffH = { original : i , match : matches++ };
                        break;

                    case "P": /* Difference to Greenwich time (GMT) with colon between hours and minutes */
                        throw '"P" not implemented!';

                    case "T": /* Timezone abbreviation */
                        throw '"T" not implemented!';

                    case "Z": /* Timezone offset in seconds. The offset for timezones west of UTC is always negative, and for those east of UTC is always positive. */
                        result[ i ]    = '(\\-?\\d{1,5})';
                        objIndex.diffM = { original : i , match : matches++ };
                        break;

                    /* FULL DATE/TIME  */
                    case "c": /* ISO 8601 date */
                        throw '"c" not implemented!';

                    case "r": /* RFC 2822 formatted date  */
                        result[ i ]    = '([\\wá]{3}, \\d{1,2} \\w{3} \\d{4} \\d{2}:\\d{2}:\\d{2} [+\\-]\\d{4})';
                        objIndex.date  = { original : i , match : matches++ };
                        break;

                    case "U":  /* Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)  */
                        result[ i ]    = '(\\d{1,13})';
                        objIndex.date  = { original : i , match : matches++ };
                        break;

                    default:
                        result[ i ]    = iFormat[ i ];
                }
            }

            var pattr = new RegExp( result.join('') );

            try {
                mList = str_date.match( pattr );
                if ( !mList ) { return; }
            }
            catch ( e ) { return ; }

            var _haveDatetime = typeof objIndex.date  !== 'undefined';

            var _haveYear     = typeof objIndex.year  !== 'undefined';

            var _haveYDay     = typeof objIndex.dayY  !== 'undefined';

            var _haveDay      = typeof objIndex.day   !== 'undefined';
            var _haveMonth    = typeof objIndex.month !== 'undefined';
            var _haveMonthDay =  _haveMonth && _haveDay;
            var _haveOnlyDay  = !_haveMonth && _haveDay;

            var _haveWDay     = typeof objIndex.dayW  !== 'undefined';
            var _haveWeek     = typeof objIndex.week  !== 'undefined';
            var _haveWeekWDay =  _haveWeek && _haveWDay;
            var _haveOnlyWDay = !_haveWeek && _haveWDay;

            var _validDate    = _haveYDay || _haveMonthDay || !_haveYear && _haveOnlyDay || _haveWeekWDay || !_haveYear && _haveOnlyWDay;
            var _noDate       = !_haveYear && !_haveYDay && !_haveDay && !_haveMonth && !_haveWDay && !_haveWeek;

            var _haveHour12   = typeof objIndex.hourD !== 'undefined' && typeof objIndex.ampm !== 'undefined';
            var _haveHour24   = typeof objIndex.hour  !== 'undefined';
            var _haveHour     = _haveHour12 || _haveHour24;

            var _haveMin      = typeof objIndex.min   !== 'undefined';
            var _haveSec      = typeof objIndex.sec   !== 'undefined';
            var _haveMSec     = typeof objIndex.msec  !== 'undefined';

            var _haveMoreM    = !_noDate || _haveHour;
            var _haveMoreS    = _haveMoreM || _haveMin;

            var _haveDiffM    = typeof objIndex.diffM !== 'undefined';
            var _haveDiffH    = typeof objIndex.diffH !== 'undefined';
            //var _haveGMT      = _haveDiffM || _haveDiffH;
            var hour;
            var min;

            if ( _haveDatetime ) {
                if ( iFormat[ objIndex.date.original ] === 'U' ) {
                    return new Date( +mList[ objIndex.date.match + 1 ] * 1000 );
                }

                var dList = mList[ objIndex.date.match + 1 ].match( /\w{3}, (\d{1,2}) (\w{3}) (\d{4}) (\d{2}):(\d{2}):(\d{2}) ([+\-]\d{4})/ );
                hour  = +dList[ 4 ] + ( +dList[ 7 ].slice( 0 , 3 ) );
                min   = +dList[ 5 ] + ( dList[ 7 ].slice( 0 , 1 ) + dList[ 7 ].slice( 3 ) ) / 100 * 60;

                return new Date( dList[ 3 ] , this._iMonth( dList[ 2 ] ) , dList[ 1 ] , hour  , min , dList[ 6 ] );
            }

            var _d = new Date( );
            var year;
            var month;
            var day;
            var date;
            var sec;
            var msec;
            var gmt;

            if ( !_validDate && !_noDate ) { return ; }

            if ( _validDate ) {
                if ( _haveYear ) {
                    var _y = _d.getFullYear( ) - 50 + '';
                    year   = mList[ objIndex.year.match + 1 ];
                    if ( iFormat[ objIndex.year.original ] === 'y' ) {
                        year = +_y.slice( 0 , 2 ) + ( year >= ( _y ).slice( 2 ) ? 0 : 1 ) + year;
                    }
                } else {
                    year = _d.getFullYear();
                }

                if ( _haveYDay ) {
                    month = 0;
                    day   = mList[ objIndex.dayY.match + 1 ];
                } else if ( _haveDay ) {
                    if ( _haveMonth ) {
                        month = this._iMonth( mList[ objIndex.month.match + 1 ] );
                    } else {
                        month = _d.getMonth( );
                    }

                    day = mList[ objIndex.day.match + 1 ];
                } else {
                    month = 0;

                    var week;
                    if ( _haveWeek ) {
                        week = mList[ objIndex.week.match + 1 ];
                    } else {
                        week = this.get( 'W' , _d );
                    }

                    day = ( week - 2 ) * 7 + ( 8 - ( ( new Date( year , 0 , 1 ) ).getDay( ) || 7 ) ) + this._iWeek( mList[ objIndex.week.match + 1 ] );
                }

                if ( month === 0 && day > 31 ) {
                    var aux = new Date( year , month , day );
                    month   = aux.getMonth( );
                    day     = aux.getDate( );
                }
            }
            else {
                year  = _d.getFullYear( );
                month = _d.getMonth( );
                day   = _d.getDate( );
            }

            date = year + '-' + ( month + 1 ) + '-' + day + ' ';

            if      ( _haveHour12 ) { hour = +mList[ objIndex.hourD.match + 1 ] + ( mList[ objIndex.ampm.match + 1 ] === 'pm' ? 12 : 0 ); }
            else if ( _haveHour24 ) { hour = mList[ objIndex.hour.match + 1 ]; }
            else if ( _noDate     ) { hour = _d.getHours( ); }
            else                    { hour = '00'; }

            if      (  _haveMin   ) { min  = mList[ objIndex.min.match + 1 ]; }
            else if ( !_haveMoreM ) { min  = _d.getMinutes( ); }
            else                    { min  = '00'; }

            if      (  _haveSec   ) { sec  = mList[ objIndex.sec.match + 1 ]; }
            else if ( !_haveMoreS ) { sec  = _d.getSeconds( ); }
            else                    { sec  = '00'; }

            if      ( _haveMSec )   { msec = mList[ objIndex.msec.match + 1 ]; }
            else                    { msec = '000'; }

            if      ( _haveDiffH )  { gmt  = mList[ objIndex.diffH.match + 1 ]; }
            else if ( _haveDiffM )  { gmt  = String( -1 * mList[ objIndex.diffM.match + 1 ] / 60 * 100 ).replace( /^(\d)/ , '+$1' ).replace( /(^[\-+])(\d{3}$)/ , '$10$2' ); }
            else                    { gmt  = '+0000'; }

            return new Date( date + hour + ':' + min + ':' + sec + '.' + msec + gmt );
        }
    };


    return InkDate;

});

/**
 * @module Ink.Util.Cookie_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.Util.Cookie', '1', [], function() {

    'use strict';

    /**
     * Utilities for Cookie handling
     *
     * @class Ink.Util.Cookie
     * @version 1
     * @static
     */
    var Cookie = {

        /**
         * Gets an object with current page cookies
         *
         * @method get
         * @param {String} name
         * @return {String|Object} If the name is specified, it returns the value related to that property. Otherwise it returns the full cookie object
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Cookie_1'], function( InkCookie ){
         *         var myCookieValue = InkCookie.get('someVarThere');
         *         console.log( myCookieValue ); // This will output the value of the cookie 'someVarThere', from the cookie object.
         *     });
         */
        get: function(name)
        {
            var cookie = document.cookie || false;

            var _Cookie = {};
            if(cookie) {
                cookie = cookie.replace(new RegExp("; ", "g"), ';');
                var aCookie = cookie.split(';');
                var aItem = [];
                if(aCookie.length > 0) {
                    for(var i=0; i < aCookie.length; i++) {
                        aItem = aCookie[i].split('=');
                        if(aItem.length === 2) {
                            _Cookie[aItem[0]] = decodeURIComponent(aItem[1]);
                        }
                        aItem = [];
                    }
                }
            }
            if(name) {
                if(typeof(_Cookie[name]) !== 'undefined') {
                    return _Cookie[name];
                } else {
                    return null;
                }
            }
            return _Cookie;
        },

        /**
         * Sets a cookie
         *
         * @method set
         * @param {String} name Cookie name
         * @param {String} value Cookie value
         * @param {Number} [expires] Number to add to current Date in seconds
         * @param {String} [path] Path to sets cookie (default '/')
         * @param {String} [domain] Domain to sets cookie (default current hostname)
         * @param {Boolean} [secure] True if wants secure, default 'false'
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Cookie_1'], function( InkCookie ){
         *         var expireDate = new Date( 2014,00,01, 0,0,0);
         *         InkCookie.set( 'someVarThere', 'anyValueHere', expireDate.getTime() );
         *     });
         */
        set: function(name, value, expires, path, domain, secure)
        {
            var sName;
            if(!name || value===false || typeof(name) === 'undefined' || typeof(value) === 'undefined') {
                return false;
            } else {
                sName = name+'='+encodeURIComponent(value);
            }
            var sExpires = false;
            var sPath = false;
            var sDomain = false;
            var sSecure = false;

            if(expires && typeof(expires) !== 'undefined' && !isNaN(expires)) {
                var oDate = new Date();
                var sDate = (parseInt(Number(oDate.valueOf()), 10) + (Number(parseInt(expires, 10)) * 1000));

                var nDate = new Date(sDate);
                var expiresString = nDate.toGMTString();

                var re = new RegExp("([^\\s]+)(\\s\\d\\d)\\s(\\w\\w\\w)\\s(.*)");
                expiresString = expiresString.replace(re, "$1$2-$3-$4");

                sExpires = 'expires='+expiresString;
            } else {
                if(typeof(expires) !== 'undefined' && !isNaN(expires) && Number(parseInt(expires, 10))===0) {
                    sExpires = '';
                } else {
                    sExpires = 'expires=Thu, 01-Jan-2037 00:00:01 GMT';
                }
            }

            if(path && typeof(path) !== 'undefined') {
                sPath = 'path='+path;
            } else {
                sPath = 'path=/';
            }

            if(domain && typeof(domain) !== 'undefined') {
                sDomain = 'domain='+domain;
            } else {
                var portClean = new RegExp(":(.*)");
                sDomain = 'domain='+window.location.host;
                sDomain = sDomain.replace(portClean,"");
            }

            if(secure && typeof(secure) !== 'undefined') {
                sSecure = secure;
            } else {
                sSecure = false;
            }

            document.cookie = sName+'; '+sExpires+'; '+sPath+'; '+sDomain+'; '+sSecure;
        },

        /**
         * Delete a cookie
         *
         * @method remove
         * @param {String} cookieName Cookie name
         * @param {String} [path] Path of the cookie (default '/')
         * @param {String} [domain] Domain of the cookie (default current hostname)
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Cookie_1'], function( InkCookie ){
         *         InkCookie.remove( 'someVarThere' );
         *     });
         */
        remove: function(cookieName, path, domain)
        {
            //var expiresDate = 'Thu, 01-Jan-1970 00:00:01 GMT';
            var sPath = false;
            var sDomain = false;
            var expiresDate = -999999999;

            if(path && typeof(path) !== 'undefined') {
                sPath = path;
            } else {
                sPath = '/';
            }

            if(domain && typeof(domain) !== 'undefined') {
                sDomain = domain;
            } else {
                sDomain = window.location.host;
            }

            this.set(cookieName, 'deleted', expiresDate, sPath, sDomain);
        }
    };

    return Cookie;

});

/**
 * @module Ink.Util.Array_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.Util.Array', '1', [], function() {

    'use strict';

    /**
     * Utility functions to use with Arrays
     *
     * @class Ink.Util.Array
     * @version 1
     * @static
     */
    var InkArray = {

        /**
         * Checks if value exists in array
         *
         * @method inArray
         * @param {Mixed} value
         * @param {Array} arr
         * @return {Boolean}    True if value exists in the array
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Array_1'], function( InkArray ){
         *         var testArray = [ 'value1', 'value2', 'value3' ];
         *         if( InkArray.inArray( 'value2', testArray ) === true ){
         *             console.log( "Yep it's in the array." );
         *         } else {
         *             console.log( "No it's NOT in the array." );
         *         }
         *     });
         */
        inArray: function(value, arr) {
            if (typeof arr === 'object') {
                for (var i = 0, f = arr.length; i < f; ++i) {
                    if (arr[i] === value) {
                        return true;
                    }
                }
            }
            return false;
        },

        /**
         * Sorts an array of object by an object property
         *
         * @method sortMulti
         * @param {Array} arr array of objects to sort
         * @param {String} key property to sort by
         * @return {Array|Boolean} False if it's not an array, returns a sorted array if it's an array.
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Array_1'], function( InkArray ){
         *         var testArray = [
         *             { 'myKey': 'value1' },
         *             { 'myKey': 'value2' },
         *             { 'myKey': 'value3' }
         *         ];
         *
         *         InkArray.sortMulti( testArray, 'myKey' );
         *     });
         */
        sortMulti: function(arr, key) {
            if (typeof arr === 'undefined' || arr.constructor !== Array) { return false; }
            if (typeof key !== 'string') { return arr.sort(); }
            if (arr.length > 0) {
                if (typeof(arr[0][key]) === 'undefined') { return false; }
                arr.sort(function(a, b){
                    var x = a[key];
                    var y = b[key];
                    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
                });
            }
            return arr;
        },

        /**
         * Returns the associated key of an array value
         *
         * @method keyValue
         * @param {String} value Value to search for
         * @param {Array} arr Array where the search will run
         * @param {Boolean} [first] Flag that determines if the search stops at first occurrence. It also returns an index number instead of an array of indexes.
         * @return {Boolean|Number|Array} False if not exists | number if exists and 3rd input param is true | array if exists and 3rd input param is not set or it is !== true
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Array_1'], function( InkArray ){
         *         var testArray = [ 'value1', 'value2', 'value3', 'value2' ];
         *         console.log( InkArray.keyValue( 'value2', testArray, true ) ); // Result: 1
         *         console.log( InkArray.keyValue( 'value2', testArray ) ); // Result: [1, 3]
         *     });
         */
        keyValue: function(value, arr, first) {
            if (typeof value !== 'undefined' && typeof arr === 'object' && this.inArray(value, arr)) {
                var aKeys = [];
                for (var i = 0, f = arr.length; i < f; ++i) {
                    if (arr[i] === value) {
                        if (typeof first !== 'undefined' && first === true) {
                            return i;
                        } else {
                            aKeys.push(i);
                        }
                    }
                }
                return aKeys;
            }
            return false;
        },

        /**
         * Returns the array shuffled, false if the param is not an array
         *
         * @method shuffle
         * @param {Array} arr Array to shuffle
         * @return {Boolean|Number|Array} False if not an array | Array shuffled
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Array_1'], function( InkArray ){
         *         var testArray = [ 'value1', 'value2', 'value3', 'value2' ];
         *         console.log( InkArray.shuffle( testArray ) ); // Result example: [ 'value3', 'value2', 'value2', 'value1' ]
         *     });
         */
        shuffle: function(arr) {
            if (typeof(arr) !== 'undefined' && arr.constructor !== Array) { return false; }
            var total   = arr.length,
                tmp1    = false,
                rnd     = false;

            while (total--) {
                rnd        = Math.floor(Math.random() * (total + 1));
                tmp1       = arr[total];
                arr[total] = arr[rnd];
                arr[rnd]   = tmp1;
            }
            return arr;
        },

        /**
         * Runs a functions through each of the elements of an array
         *
         * @method each
         * @param {Array} arr Array to be cycled/iterated
         * @param {Function} cb The function receives as arguments the value, index and array.
         * @return {Array} Array iterated.
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Array_1'], function( InkArray ){
         *         var testArray = [ 'value1', 'value2', 'value3', 'value2' ];
         *         InkArray.each( testArray, function( value, index, arr ){
         *             console.log( 'The value is: ' + value + ' | The index is: ' + index );
         *         });
         *     });
         */
        each: function(arr, cb) {
            var arrCopy    = arr.slice(0),
                total      = arrCopy.length,
                iterations = Math.floor(total / 8),
                leftover   = total % 8,
                i          = 0;

            if (leftover > 0) { // Duff's device pattern
                do {
                    cb(arrCopy[i++], i-1, arr);
                } while (--leftover > 0);
            }
            if (iterations === 0) { return arr; }
            do {
                cb(arrCopy[i++], i-1, arr);
                cb(arrCopy[i++], i-1, arr);
                cb(arrCopy[i++], i-1, arr);
                cb(arrCopy[i++], i-1, arr);
                cb(arrCopy[i++], i-1, arr);
                cb(arrCopy[i++], i-1, arr);
                cb(arrCopy[i++], i-1, arr);
                cb(arrCopy[i++], i-1, arr);
            } while(--iterations > 0);

            return arr;
        },

        /**
         * Runs a callback function, which should return true or false.
         * If one of the 'runs' returns true, it will return. Otherwise if none returns true, it will return false.
         * See more at: https://developer.mozilla.org/en-US/docs/JavaScript/Reference/Global_Objects/Array/some (MDN)
         *
         * @method some
         * @param {Array} arr The array you walk to iterate through
         * @param {Function} cb The callback that will be called on the array's elements. It receives the value, the index and the array as arguments.
         * @param {Object} Context object of the callback function
         * @return {Boolean} True if the callback returns true at any point, false otherwise
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Array_1'], function( InkArray ){
         *         var testArray1 = [ 10, 20, 50, 100, 30 ];
         *         var testArray2 = [ 1, 2, 3, 4, 5 ];
         *
         *         function myTestFunction( value, index, arr ){
         *             if( value > 90 ){
         *                 return true;
         *             }
         *             return false;
         *         }
         *         console.log( InkArray.some( testArray1, myTestFunction, null ) ); // Result: true
         *         console.log( InkArray.some( testArray2, myTestFunction, null ) ); // Result: false
         *     });
         */
        some: function(arr, cb, context){

            if (arr === null){
                throw new TypeError('First argument is invalid.');
            }

            var t = Object(arr);
            var len = t.length >>> 0;
            if (typeof cb !== "function"){ throw new TypeError('Second argument must be a function.'); }

            for (var i = 0; i < len; i++) {
                if (i in t && cb.call(context, t[i], i, t)){ return true; }
            }

            return false;
        },

        /**
         * Returns an array containing every item that is shared between the two given arrays
         *
         * @method intersect
         * @param {Array} arr Array1 to be intersected with Array2
         * @param {Array} arr Array2 to be intersected with Array1
         * @return {Array} Empty array if one of the arrays is false (or do not intersect) | Array with the intersected values
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Array_1'], function( InkArray ){
         *         var testArray1 = [ 'value1', 'value2', 'value3' ];
         *         var testArray2 = [ 'value2', 'value3', 'value4', 'value5', 'value6' ];
         *         console.log( InkArray.intersect( testArray1,testArray2 ) ); // Result: [ 'value2', 'value3' ]
         *     });
         */
        intersect: function(arr1, arr2) {
            if (!arr1 || !arr2 || arr1 instanceof Array === false || arr2 instanceof Array === false) {
                return [];
            }

            var shared = [];
            for (var i = 0, I = arr1.length; i<I; ++i) {
                for (var j = 0, J = arr2.length; j < J; ++j) {
                    if (arr1[i] === arr2[j]) {
                        shared.push(arr1[i]);
                    }
                }
            }

            return shared;
        },

        /**
         * Convert lists type to type array
         *
         * @method convert
         * @param {Array} arr Array to be converted
         * @return {Array} Array resulting of the conversion
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Array_1'], function( InkArray ){
         *         var testArray = [ 'value1', 'value2' ];
         *         testArray.myMethod = function(){
         *             console.log('stuff');
         *         }
         *         
         *         console.log( InkArray.convert( testArray ) ); // Result: [ 'value1', 'value2' ]
         *     });
         */
        convert: function(arr) {
            return Array.prototype.slice.call(arr || [], 0);
        },

        /**
         * Insert value into the array on specified idx
         *
         * @method insert
         * @param {Array} arr Array where the value will be inserted
         * @param {Number} idx Index of the array where the value should be inserted
         * @param {Mixed} value Value to be inserted
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Array_1'], function( InkArray ){
         *         var testArray = [ 'value1', 'value2' ];
         *         console.log( InkArray.insert( testArray, 1, 'value3' ) ); // Result: [ 'value1', 'value3', 'value2' ]
         *     });
         */
        insert: function(arr, idx, value) {
            arr.splice(idx, 0, value);
        },

        /**
         * Remove a range of values from the array
         *
         * @method remove
         * @param {Array} arr Array where the value will be inserted
         * @param {Number} from Index of the array where the removal will start removing.
         * @param {Number} rLen Number of items to be removed from the index onwards.
         * @return {Array} An array with the remaining values
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Array_1'], function( InkArray ){
         *         var testArray = [ 'value1', 'value2', 'value3', 'value4', 'value5' ];
         *         console.log( InkArray.remove( testArray, 1, 3 ) ); // Result: [ 'value1', 'value4', 'value5' ]
         *     });
         */
        remove: function(arr, from, rLen){
            var output = [];

            for(var i = 0, iLen = arr.length; i < iLen; i++){
                if(i >= from && i < from + rLen){
                    continue;
                }

                output.push(arr[i]);
            }

            return output;
        }
    };

    return InkArray;

});


/*
 *  TODO - INCLUDE THIS ON Ink.Util.Array
 *
// Production steps of ECMA-262, Edition 5, 15.4.4.18
// Reference: http://es5.github.com/#x15.4.4.18
// https://developer.mozilla.org/en-US/docs/JavaScript/Reference/Global_Objects/Array/forEach
if (!Array.prototype.forEach) {
    Array.prototype.forEach = function forEach(cb, thisArg) {
        var O, len, T, k, kValue;

        if (this === null || this === undefined) {
            throw new TypeError('this is null or not defined');
        }

        O = Object(this);
        len = O.length >>> 0;

        if ({}.toString.call(cb) !== '[object Function]') {
            throw new TypeError(cb + ' is not a function');
        }

        if (thisArg) {
            T = thisArg;
        }

        k = 0;

        while (k < len) {
            if (Object.prototype.hasOwnProperty.call(O, k)) {
                kValue = O[k];
                cb.call(T, kValue, k, O);
            }
            ++k;
        }
    };
}


// Production steps of ECMA-262, Edition 5, 15.4.4.19
// Reference: http://es5.github.com/#x15.4.4.19
// https://developer.mozilla.org/en-US/docs/JavaScript/Reference/Global_Objects/Array/map
if (!Array.prototype.map) {
    Array.prototype.map = function(callback, thisArg) {
        var T, A, k;

        if (this === null || this === undefined) {
            new TypeError(" this is null or not defined");
        }

        var O = Object(this);
        var len = O.length >>> 0;

        if ({}.toString.call(callback) !== "[object Function]") {
            throw new TypeError(callback + " is not a function");
        }

        if (thisArg) {
            T = thisArg;
        }
        A = new Array(len);
        k = 0;

        while(k < len) {
            var kValue, mappedValue;
            if (k in O) {
                kValue = O[ k ];
                mappedValue = callback.call(T, kValue, k, O);
                A[ k ] = mappedValue;
            }
            ++k;
        }
        return A;
    };
}

*/

/**
 * @module Ink.Util.Validator_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.Util.Validator', '1', [], function() {

    'use strict';

    /**
     * Set of functions to provide validation
     *
     * @class Ink.Util.Validator
     * @version 1
     * @static
     */
    var Validator = {

        /**
         * List of country codes avaible for isPhone function
         * 
         * @property _countryCodes
         * @type {Array}
         * @private
         * @static
         * @readOnly
         */
        _countryCodes : [
                        'AO',
                        'CV',
                        'MZ',
                        'PT'
                    ],

        /**
         * International number for portugal
         * 
         * @property _internacionalPT
         * @type {Number}
         * @private
         * @static
         * @readOnly
         *
         */
        _internacionalPT: 351,

        /**
         * List of all portuguese number prefixes
         * 
         * @property _indicativosPT
         * @type {Object}
         * @private
         * @static
         * @readOnly
         *
         */
        _indicativosPT: {
                        21: 'lisboa',
                        22: 'porto',
                        231: 'mealhada',
                        232: 'viseu',
                        233: 'figueira da foz',
                        234: 'aveiro',
                        235: 'arganil',
                        236: 'pombal',
                        238: 'seia',
                        239: 'coimbra',
                        241: 'abrantes',
                        242: 'ponte de sôr',
                        243: 'santarém',
                        244: 'leiria',
                        245: 'portalegre',
                        249: 'torres novas',
                        251: 'valença',
                        252: 'vila nova de famalicão',
                        253: 'braga',
                        254: 'peso da régua',
                        255: 'penafiel',
                        256: 'são joão da madeira',
                        258: 'viana do castelo',
                        259: 'vila real',
                        261: 'torres vedras',
                        262: 'caldas da raínha',
                        263: 'vila franca de xira',
                        265: 'setúbal',
                        266: 'évora',
                        268: 'estremoz',
                        269: 'santiago do cacém',
                        271: 'guarda',
                        272: 'castelo branco',
                        273: 'bragança',
                        274: 'proença-a-nova',
                        275: 'covilhã',
                        276: 'chaves',
                        277: 'idanha-a-nova',
                        278: 'mirandela',
                        279: 'moncorvo',
                        281: 'tavira',
                        282: 'portimão',
                        283: 'odemira',
                        284: 'beja',
                        285: 'moura',
                        286: 'castro verde',
                        289: 'faro',
                        291: 'funchal, porto santo',
                        292: 'corvo, faial, flores, horta, pico',
                        295: 'angra do heroísmo, graciosa, são jorge, terceira',
                        296: 'ponta delgada, são miguel, santa maria',

                        91 : 'rede móvel 91 (Vodafone / Yorn)',
                        93 : 'rede móvel 93 (Optimus)',
                        96 : 'rede móvel 96 (TMN)',
                        92 : 'rede móvel 92 (TODOS)',
                        //925 : 'rede móvel 925 (TMN 925)',
                        //926 : 'rede móvel 926 (TMN 926)',
                        //927 : 'rede móvel 927 (TMN 927)',
                        //922 : 'rede móvel 922 (Phone-ix)',

                        707: 'número único',
                        760: 'número único',
                        800: 'número grátis',
                        808: 'chamada local',
                        30:  'voip'
                          },
        /**
         * International number for Cabo Verde
         * 
         * @property _internacionalCV
         * @type {Number}
         * @private
         * @static
         * @readOnly
         */
        _internacionalCV: 238,

        /**
         * List of all Cabo Verde number prefixes
         * 
         * @property _indicativosCV
         * @type {Object}
         * @private
         * @static
         * @readOnly
         */
        _indicativosCV: {
                        2: 'fixo',
                        91: 'móvel 91',
                        95: 'móvel 95',
                        97: 'móvel 97',
                        98: 'móvel 98',
                        99: 'móvel 99'
                    },
        /**
         * International number for angola
         *
         * @property _internacionalAO
         * @type {Number}
         * @private
         * @static
         * @readOnly
         */
        _internacionalAO: 244,

        /**
         * List of all Angola number prefixes
         *
         * @property _indicativosAO
         * @type {Object}
         * @private
         * @static
         * @readOnly
         */
        _indicativosAO: {
                        2: 'fixo',
                        91: 'móvel 91',
                        92: 'móvel 92'
                    },
        /**
         * International number for mozambique
         *
         * @property _internacionalMZ
         * @type {Number}
         * @private
         * @static
         * @readOnly
         */
        _internacionalMZ: 258,

        /**
         * List of all Mozambique number prefixes
         *
         * @property _indicativosMZ
         * @type {Object}
         * @private
         * @static
         * @readOnly
         */
        _indicativosMZ: {
                        2: 'fixo',
                        82: 'móvel 82',
                        84: 'móvel 84'
                    },

        /**
         * International number for Timor
         *
         * @property _internacionalTL
         * @type {Number}
         * @private
         * @static
         * @readOnly
         */
        _internacionalTL: 670,

        /**
         * List of all Timor number prefixes
         *
         * @property _indicativosTL
         * @type {Object}
         * @private
         * @static
         * @readOnly
         */
        _indicativosTL: {
                        3: 'fixo',
                        7: 'móvel 7'
                    },

        /**
         * Checks if a year is Leap "Bissexto"
         *
         * @method _isLeapYear
         * @param {Number} year Year to be checked
         * @return {Boolean} True if it is a leap year.
         * @private
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Validator_1'], function( InkValidator ){
         *         console.log( InkValidator._isLeapYear( 2004 ) ); // Result: true
         *         console.log( InkValidator._isLeapYear( 2006 ) ); // Result: false
         *     });
         */
        _isLeapYear: function(year){

            var yearRegExp = /^\d{4}$/;

            if(yearRegExp.test(year)){
                return ((year%4) ? false: ((year%100) ? true : ((year%400)? false : true)) );
            }

            return false;
        },

        /**
         * Object with the date formats available for validation
         * 
         * @property _dateParsers
         * @type {Object}
         * @private
         * @static
         * @readOnly
         */
        _dateParsers: {
            'yyyy-mm-dd': {day:5, month:3, year:1, sep: '-', parser: /^(\d{4})(\-)(\d{1,2})(\-)(\d{1,2})$/},
            'yyyy/mm/dd': {day:5, month:3, year:1, sep: '/', parser: /^(\d{4})(\/)(\d{1,2})(\/)(\d{1,2})$/},
            'yy-mm-dd': {day:5, month:3, year:1, sep: '-', parser: /^(\d{2})(\-)(\d{1,2})(\-)(\d{1,2})$/},
            'yy/mm/dd': {day:5, month:3, year:1, sep: '/', parser: /^(\d{2})(\/)(\d{1,2})(\/)(\d{1,2})$/},
            'dd-mm-yyyy': {day:1, month:3, year:5, sep: '-', parser: /^(\d{1,2})(\-)(\d{1,2})(\-)(\d{4})$/},
            'dd/mm/yyyy': {day:1, month:3, year:5, sep: '/', parser: /^(\d{1,2})(\/)(\d{1,2})(\/)(\d{4})$/},
            'dd-mm-yy': {day:1, month:3, year:5, sep: '-', parser: /^(\d{1,2})(\-)(\d{1,2})(\-)(\d{2})$/},
            'dd/mm/yy': {day:1, month:3, year:5, sep: '/', parser: /^(\d{1,2})(\/)(\d{1,2})(\/)(\d{2})$/}
        },

        /**
         * Calculates the number of days in a given month of a given year
         *
         * @method _daysInMonth
         * @param {Number} _m - month (1 to 12)
         * @param {Number} _y - year
         * @return {Number} Returns the number of days in a given month of a given year
         * @private
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Validator_1'], function( InkValidator ){
         *         console.log( InkValidator._daysInMonth( 2, 2004 ) ); // Result: 29
         *         console.log( InkValidator._daysInMonth( 2, 2006 ) ); // Result: 28
         *     });
         */
        _daysInMonth: function(_m,_y){
            var nDays=0;

            if(_m===1 || _m===3 || _m===5 || _m===7 || _m===8 || _m===10 || _m===12)
            {
                nDays= 31;
            }
            else if ( _m===4 || _m===6 || _m===9 || _m===11)
            {
                nDays = 30;
            }
            else
            {
                if((_y%400===0) || (_y%4===0 && _y%100!==0))
                {
                    nDays = 29;
                }
                else
                {
                    nDays = 28;
                }
            }

            return nDays;
        },



        /**
         * Checks if a date is valid
         *
         * @method _isValidDate
         * @param {Number} year
         * @param {Number} month
         * @param {Number} day
         * @return {Boolean} True if it's a valid date
         * @private
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Validator_1'], function( InkValidator ){
         *         console.log( InkValidator._isValidDate( 2004, 2, 29 ) ); // Result: true
         *         console.log( InkValidator._isValidDate( 2006, 2, 29 ) ); // Result: false
         *     });
         */
        _isValidDate: function(year, month, day){

            var yearRegExp = /^\d{4}$/;
            var validOneOrTwo = /^\d{1,2}$/;
            if(yearRegExp.test(year) && validOneOrTwo.test(month) && validOneOrTwo.test(day)){
                if(month>=1 && month<=12 && day>=1 && this._daysInMonth(month,year)>=day){
                    return true;
                }
            }

            return false;
        },

        /**
         * Checks if a email is valid
         *
         * @method mail
         * @param {String} email
         * @return {Boolean} True if it's a valid e-mail
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Validator_1'], function( InkValidator ){
         *         console.log( InkValidator.mail( 'agfsdfgfdsgdsf' ) ); // Result: false
         *         console.log( InkValidator.mail( 'inkdev@sapo.pt' ) ); // Result: true
         *     });
         */
        mail: function(email)
        {
            var emailValido = new RegExp("^[_a-z0-9-]+((\\.|\\+)[_a-z0-9-]+)*@([\\w]*-?[\\w]*\\.)+[a-z]{2,4}$", "i");
            if(!emailValido.test(email)) {
                return false;
            } else {
                return true;
            }
        },

        /**
         * Checks if a url is valid
         *
         * @method url
         * @param {String} url URL to be checked
         * @param {Boolean} [full] If true, validates a full URL (one that should start with 'http')
         * @return {Boolean} True if the given URL is valid
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Validator_1'], function( InkValidator ){
         *         console.log( InkValidator.url( 'www.sapo.pt' ) );                // Result: true
         *         console.log( InkValidator.url( 'http://www.sapo.pt', true ) );   // Result: true
         *         console.log( InkValidator.url( 'meh' ) );                        // Result: false
         *     });
         */
        url: function(url, full)
        {
            if(typeof full === "undefined" || full === false) {
                var reHTTP = new RegExp("(^(http\\:\\/\\/|https\\:\\/\\/)(.+))", "i");
                if(reHTTP.test(url) === false) {
                    url = 'http://'+url;
                }
            }

            var reUrl = new RegExp("^(http:\\/\\/|https:\\/\\/)([\\w]*(-?[\\w]*)*\\.)+[a-z]{2,4}", "i");
            if(reUrl.test(url) === false) {
                return false;
            } else {
                return true;
            }
        },

        /**
         * Checks if a phone is valid in Portugal
         *
         * @method isPTPhone
         * @param {Number} phone Phone number to be checked
         * @return {Boolean} True if it's a valid Portuguese Phone
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Validator_1'], function( InkValidator ){
         *         console.log( InkValidator.isPTPhone( '213919264' ) );        // Result: true
         *         console.log( InkValidator.isPTPhone( '00351213919264' ) );   // Result: true
         *         console.log( InkValidator.isPTPhone( '+351213919264' ) );    // Result: true
         *         console.log( InkValidator.isPTPhone( '1' ) );                // Result: false
         *     });
         */
        isPTPhone: function(phone)
        {

            phone = phone.toString();
            var aInd = [];
            for(var i in this._indicativosPT) {
                if(typeof(this._indicativosPT[i]) === 'string') {
                    aInd.push(i);
                }
            }
            var strInd = aInd.join('|');

            var re351 = /^(00351|\+351)/;
            if(re351.test(phone)) {
                phone = phone.replace(re351, "");
            }

            var reSpecialChars = /(\s|\-|\.)+/g;
            phone = phone.replace(reSpecialChars, '');
            //var reInt = new RegExp("\\d", "i");
            var reInt = /[\d]{9}/i;
            if(phone.length === 9 && reInt.test(phone)) {
                var reValid = new RegExp("^("+strInd+")");
                if(reValid.test(phone)) {
                    return true;
                }
            }

            return false;
        },

        /**
         * Alias function for isPTPhone
         *
         * @method isPortuguesePhone
         * @param {Number} phone Phone number to be checked
         * @return {Boolean} True if it's a valid Portuguese Phone
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Validator_1'], function( InkValidator ){
         *         console.log( InkValidator.isPortuguesePhone( '213919264' ) );        // Result: true
         *         console.log( InkValidator.isPortuguesePhone( '00351213919264' ) );   // Result: true
         *         console.log( InkValidator.isPortuguesePhone( '+351213919264' ) );    // Result: true
         *         console.log( InkValidator.isPortuguesePhone( '1' ) );                // Result: false
         *     });
         */
        isPortuguesePhone: function(phone)
        {
            return this.isPTPhone(phone);
        },

        /**
         * Checks if a phone is valid in Cabo Verde
         *
         * @method isCVPhone
         * @param {Number} phone Phone number to be checked
         * @return {Boolean} True if it's a valid Cape Verdean Phone
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Validator_1'], function( InkValidator ){
         *         console.log( InkValidator.isCVPhone( '2610303' ) );        // Result: true
         *         console.log( InkValidator.isCVPhone( '002382610303' ) );   // Result: true
         *         console.log( InkValidator.isCVPhone( '+2382610303' ) );    // Result: true
         *         console.log( InkValidator.isCVPhone( '1' ) );              // Result: false
         *     });
         */
        isCVPhone: function(phone)
        {
            phone = phone.toString();
            var aInd = [];
            for(var i in this._indicativosCV) {
                if(typeof(this._indicativosCV[i]) === 'string') {
                    aInd.push(i);
                }
            }
            var strInd = aInd.join('|');

            var re238 = /^(00238|\+238)/;
            if(re238.test(phone)) {
                phone = phone.replace(re238, "");
            }

            var reSpecialChars = /(\s|\-|\.)+/g;
            phone = phone.replace(reSpecialChars, '');
            //var reInt = new RegExp("\\d", "i");
            var reInt = /[\d]{7}/i;
            if(phone.length === 7 && reInt.test(phone)) {
                var reValid = new RegExp("^("+strInd+")");
                if(reValid.test(phone)) {
                    return true;
                }
            }

            return false;
        },

        /**
         * Checks if a phone is valid in Angola
         *
         * @method isAOPhone
         * @param {Number} phone Phone number to be checked
         * @return {Boolean} True if it's a valid Angolan Phone
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Validator_1'], function( InkValidator ){
         *         console.log( InkValidator.isAOPhone( '244222396385' ) );     // Result: true
         *         console.log( InkValidator.isAOPhone( '00244222396385' ) );   // Result: true
         *         console.log( InkValidator.isAOPhone( '+244222396385' ) );    // Result: true
         *         console.log( InkValidator.isAOPhone( '1' ) );                // Result: false
         *     });
         */
        isAOPhone: function(phone)
        {

            phone = phone.toString();
            var aInd = [];
            for(var i in this._indicativosAO) {
                if(typeof(this._indicativosAO[i]) === 'string') {
                    aInd.push(i);
                }
            }
            var strInd = aInd.join('|');

            var re244 = /^(00244|\+244)/;
            if(re244.test(phone)) {
                phone = phone.replace(re244, "");
            }

            var reSpecialChars = /(\s|\-|\.)+/g;
            phone = phone.replace(reSpecialChars, '');
            //var reInt = new RegExp("\\d", "i");
            var reInt = /[\d]{9}/i;
            if(phone.length === 9 && reInt.test(phone)) {
                var reValid = new RegExp("^("+strInd+")");
                if(reValid.test(phone)) {
                    return true;
                }
            }

            return false;
        },

        /**
         * Checks if a phone is valid in Mozambique
         *
         * @method isMZPhone
         * @param {Number} phone Phone number to be checked
         * @return {Boolean} True if it's a valid Mozambican Phone
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Validator_1'], function( InkValidator ){
         *         console.log( InkValidator.isMZPhone( '21426861' ) );        // Result: true
         *         console.log( InkValidator.isMZPhone( '0025821426861' ) );   // Result: true
         *         console.log( InkValidator.isMZPhone( '+25821426861' ) );    // Result: true
         *         console.log( InkValidator.isMZPhone( '1' ) );              // Result: false
         *     });
         */
        isMZPhone: function(phone)
        {

            phone = phone.toString();
            var aInd = [];
            for(var i in this._indicativosMZ) {
                if(typeof(this._indicativosMZ[i]) === 'string') {
                    aInd.push(i);
                }
            }
            var strInd = aInd.join('|');
            var re258 = /^(00258|\+258)/;
            if(re258.test(phone)) {
                phone = phone.replace(re258, "");
            }

            var reSpecialChars = /(\s|\-|\.)+/g;
            phone = phone.replace(reSpecialChars, '');
            //var reInt = new RegExp("\\d", "i");
            var reInt = /[\d]{8,9}/i;
            if((phone.length === 9 || phone.length === 8) && reInt.test(phone)) {
                var reValid = new RegExp("^("+strInd+")");
                if(reValid.test(phone)) {
                   if(phone.indexOf('2') === 0 && phone.length === 8) {
                       return true;
                   } else if(phone.indexOf('8') === 0 && phone.length === 9) {
                       return true;
                   }
                }
            }

            return false;
        },

        /**
         * Checks if a phone is valid in Timor
         *
         * @method isTLPhone
         * @param {Number} phone Phone number to be checked
         * @return {Boolean} True if it's a valid phone from Timor-Leste
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Validator_1'], function( InkValidator ){
         *         console.log( InkValidator.isTLPhone( '6703331234' ) );     // Result: true
         *         console.log( InkValidator.isTLPhone( '006703331234' ) );   // Result: true
         *         console.log( InkValidator.isTLPhone( '+6703331234' ) );    // Result: true
         *         console.log( InkValidator.isTLPhone( '1' ) );              // Result: false
         *     });
         */
        isTLPhone: function(phone)
        {

            phone = phone.toString();
            var aInd = [];
            for(var i in this._indicativosTL) {
                if(typeof(this._indicativosTL[i]) === 'string') {
                    aInd.push(i);
                }
            }
            var strInd = aInd.join('|');
            var re670 = /^(00670|\+670)/;
            if(re670.test(phone)) {
                phone = phone.replace(re670, "");
            }


            var reSpecialChars = /(\s|\-|\.)+/g;
            phone = phone.replace(reSpecialChars, '');
            //var reInt = new RegExp("\\d", "i");
            var reInt = /[\d]{7}/i;
            if(phone.length === 7 && reInt.test(phone)) {
                var reValid = new RegExp("^("+strInd+")");
                if(reValid.test(phone)) {
                    return true;
                }
            }

            return false;
        },

        /**
         * Validates the function in all country codes available or in the ones set in the second param
         *
         * @method isPhone
         * @param {String} phone number
         * @param {optional String|Array}  country or array of countries to validate
         * @return {Boolean} True if it's a valid phone in any country available
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Validator_1'], function( InkValidator ){
         *         console.log( InkValidator.isPhone( '6703331234' ) );        // Result: true
         *     });
         */
        isPhone: function(){
            var index;

            if(arguments.length===0){
                return false;
            }

            var phone = arguments[0];

            if(arguments.length>1){
                if(arguments[1].constructor === Array){
                    var func;
                    for(index=0; index<arguments[1].length; index++ ){
                        if(typeof(func=this['is' + arguments[1][index].toUpperCase() + 'Phone'])==='function'){
                            if(func(phone)){
                                return true;
                            }
                        } else {
                            throw "Invalid Country Code!";
                        }
                    }
                } else if(typeof(this['is' + arguments[1].toUpperCase() + 'Phone'])==='function'){
                    return this['is' + arguments[1].toUpperCase() + 'Phone'](phone);
                } else {
                    throw "Invalid Country Code!";
                }
            } else {
                for(index=0; index<this._countryCodes.length; index++){
                    if(this['is' + this._countryCodes[index] + 'Phone'](phone)){
                        return true;
                    }
                }
            }
            return false;
        },

        /**
         * Validates if a zip code is valid in Portugal
         *
         * @method codPostal
         * @param {Number|String} cp1
         * @param {optional Number|String} cp2
         * @param {optional Boolean} returnBothResults
         * @return {Boolean} True if it's a valid zip code
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Validator_1'], function( InkValidator ){
         *         console.log( InkValidator.codPostal( '1069', '300' ) );        // Result: true
         *         console.log( InkValidator.codPostal( '1069', '300', true ) );  // Result: [true, true]
         *     });
         *
         */
        codPostal: function(cp1,cp2,returnBothResults){


            var cPostalSep = /^(\s*\-\s*|\s+)$/;
            var trim = /^\s+|\s+$/g;
            var cPostal4 = /^[1-9]\d{3}$/;
            var cPostal3 = /^\d{3}$/;
            var parserCPostal = /^(.{4})(.*)(.{3})$/;


            returnBothResults = !!returnBothResults;

            cp1 = cp1.replace(trim,'');
            if(typeof(cp2)!=='undefined'){
                cp2 = cp2.replace(trim,'');
                if(cPostal4.test(cp1) && cPostal3.test(cp2)){
                    if( returnBothResults === true ){
                        return [true, true];
                    } else {
                        return true;
                    }
                }
            } else {
                if(cPostal4.test(cp1) ){
                    if( returnBothResults === true ){
                        return [true,false];
                    } else {
                        return true;
                    }
                }

                var cPostal = cp1.match(parserCPostal);

                if(cPostal!==null && cPostal4.test(cPostal[1]) && cPostalSep.test(cPostal[2]) && cPostal3.test(cPostal[3])){
                    if( returnBothResults === true ){
                        return [true,false];
                    } else {
                        return true;
                    }
                }
            }

            if( returnBothResults === true ){
                return [false,false];
            } else {
                return false;
            }
        },

        /**
         * Checks is a date is valid in a given format
         *
         * @method isDate
         * @param {String} format - defined in _dateParsers
         * @param {String} dateStr - date string
         * @return {Boolean} True if it's a valid date and in the specified format
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Validator_1'], function( InkValidator ){
         *         console.log( InkValidator.isDate( 'yyyy-mm-dd', '2012-05-21' ) );        // Result: true
         *     });
         */
        isDate: function(format, dateStr){



            if(typeof(this._dateParsers[format])==='undefined'){
                return false;
            }
            var yearIndex = this._dateParsers[format].year;
            var monthIndex = this._dateParsers[format].month;
            var dayIndex = this._dateParsers[format].day;
            var dateParser = this._dateParsers[format].parser;
            var separator = this._dateParsers[format].sep;

            /* Trim Deactivated
            * var trim = /^\w+|\w+$/g;
            * dateStr = dateStr.replace(trim,"");
            */
            var data = dateStr.match(dateParser);
            if(data!==null){
                /* Trim Deactivated
                * for(i=1;i<=data.length;i++){
                *   data[i] = data[i].replace(trim,"");
                *}
                */
                if(data[2]===data[4] && data[2]===separator){

                    var _y = ((data[yearIndex].length===2) ? "20" + data[yearIndex].toString() : data[yearIndex] );

                    if(this._isValidDate(_y,data[monthIndex].toString(),data[dayIndex].toString())){
                        return true;
                    }
                }
            }


            return false;
        },

        /**
         * Checks if a string is a valid color
         *
         * @method isColor
         * @param {String} str Color string to be checked
         * @return {Boolean} True if it's a valid color string
         * @public
         * @static
         * @example
         *     Ink.requireModules(['Ink.Util.Validator_1'], function( InkValidator ){
         *         console.log( InkValidator.isColor( '#FF00FF' ) );        // Result: true
         *         console.log( InkValidator.isColor( 'amdafasfs' ) );      // Result: false
         *     });
         */
        isColor: function(str){
            var match, valid = false,
                keyword = /^[a-zA-Z]+$/,
                hexa = /^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/,
                rgb = /^rgb\(\s*([0-9]{1,3})(%)?\s*,\s*([0-9]{1,3})(%)?\s*,\s*([0-9]{1,3})(%)?\s*\)$/,
                rgba = /^rgba\(\s*([0-9]{1,3})(%)?\s*,\s*([0-9]{1,3})(%)?\s*,\s*([0-9]{1,3})(%)?\s*,\s*(1(\.0)?|0(\.[0-9])?)\s*\)$/,
                hsl = /^hsl\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})(%)?\s*,\s*([0-9]{1,3})(%)?\s*\)$/,
                hsla = /^hsla\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})(%)?\s*,\s*([0-9]{1,3})(%)?\s*,\s*(1(\.0)?|0(\.[0-9])?)\s*\)$/;

            // rgb(123, 123, 132) 0 to 255
            // rgb(123%, 123%, 123%) 0 to 100
            // rgba( 4 vals) last val: 0 to 1.0
            // hsl(0 to 360, %, %)
            // hsla( ..., 0 to 1.0)

            if(
                keyword.test(str) ||
                hexa.test(str)
            ){
                return true;
            }

            var i;

            // rgb range check
            if((match = rgb.exec(str)) !== null || (match = rgba.exec(str)) !== null){
                i = match.length;

                while(i--){
                    // check percentage values
                    if((i===2 || i===4 || i===6) && typeof match[i] !== "undefined" && match[i] !== ""){
                        if(typeof match[i-1] !== "undefined" && match[i-1] >= 0 && match[i-1] <= 100){
                            valid = true;
                        } else {
                            return false;
                        }
                    }
                    // check 0 to 255 values
                    if(i===1 || i===3 || i===5 && (typeof match[i+1] === "undefined" || match[i+1] === "")){
                        if(typeof match[i] !== "undefined" && match[i] >= 0 && match[i] <= 255){
                            valid = true;
                        } else {
                            return false;
                        }
                    }
                }
            }

            // hsl range check
            if((match = hsl.exec(str)) !== null || (match = hsla.exec(str)) !== null){
                i = match.length;
                while(i--){
                    // check percentage values
                    if(i===3 || i===5){
                        if(typeof match[i-1] !== "undefined" && typeof match[i] !== "undefined" && match[i] !== "" &&
                        match[i-1] >= 0 && match[i-1] <= 100){
                            valid = true;
                        } else {
                            return false;
                        }
                    }
                    // check 0 to 360 value
                    if(i===1){
                        if(typeof match[i] !== "undefined" && match[i] >= 0 && match[i] <= 360){
                            valid = true;
                        } else {
                            return false;
                        }
                    }
                }
            }

            return valid;
        }
    };

    return Validator;

});

/**
 * @module Ink.UI.Aux_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Aux', '1', ['Ink.Net.Ajax_1','Ink.Dom.Css_1','Ink.Dom.Selector_1','Ink.Util.Url_1'], function(Ajax,Css,Selector,Url) {

    'use strict';

    var instances = {};
    var lastIdNum = 0;

    /**
     * The Aux class provides auxiliar methods to ease some of the most common/repetitive UI tasks.
     *
     * @class Ink.UI.Aux
     * @version 1
     * @uses Ink.Net.Ajax
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Selector
     * @uses Ink.Util.Url
     * @static
     */
    var Aux = {

        /**
         * Supported Ink Layouts
         *
         * @property Layouts
         * @type Object
         * @readOnly
         */
        Layouts: {
            SMALL:  'small',
            MEDIUM: 'medium',
            LARGE:  'large'
        },

        /**
         * Method to check if an item is a valid DOM Element.
         *
         * @method isDOMElement
         * @static
         * @param {Mixed} o     The object to be checked.
         * @return {Boolean}    True if it's a valid DOM Element.
         * @example
         *     var el = Ink.s('#element');
         *     if( Ink.UI.Aux.isDOMElement( el ) === true ){
         *         // It is a DOM Element.
         *     } else {
         *         // It is NOT a DOM Element.
         *     }
         */
        isDOMElement: function(o) {
            return (typeof o === 'object' && 'nodeType' in o && o.nodeType === 1);
        },

        /**
         * Method to check if an item is a valid integer.
         *
         * @method isInteger
         * @static
         * @param {Mixed} n     The value to be checked.
         * @return {Boolean}    True if 'n' is a valid integer.
         * @example
         *     var value = 1;
         *     if( Ink.UI.Aux.isInteger( value ) === true ){
         *         // It is an integer.
         *     } else {
         *         // It is NOT an integer.
         *     }
         */
        isInteger: function(n) {
            return (typeof n === 'number' && n % 1 === 0);
        },

        /**
         * Method to get a DOM Element. The first parameter should be either a DOM Element or a valid CSS Selector.
         * If not, then it will throw an exception. Otherwise, it returns a DOM Element.
         *
         * @method elOrSelector
         * @static
         * @param  {DOMElement|String} elOrSelector Valid DOM Element or CSS Selector
         * @param  {String}            fieldName    This field is used in the thrown Exception to identify the parameter.
         * @return {DOMElement} Returns the DOMElement passed or the first result of the CSS Selector. Otherwise it throws an exception.
         * @example
         *     // In case there are several .myInput, it will retrieve the first found
         *     var el = Ink.UI.Aux.elOrSelector('.myInput','My Input');
         */
        elOrSelector: function(elOrSelector, fieldName) {
            if (!this.isDOMElement(elOrSelector)) {
                var t = Selector.select(elOrSelector);
                if (t.length === 0) { throw new TypeError(fieldName + ' must either be a DOM Element or a selector expression!\nThe script element must also be after the DOM Element itself.'); }
                return t[0];
            }
            return elOrSelector;
        },


        /**
         * Method to make a deep copy (clone) of an object.
         * Note: The object cannot have loops.
         *
         * @method clone
         * @static
         * @param  {Object} o The object to be cloned/copied.
         * @return {Object} Returns the result of the clone/copy.
         * @example
         *     var originalObj = {
         *         key1: 'value1',
         *         key2: 'value2',
         *         key3: 'value3'
         *     };
         *     var cloneObj = Ink.UI.Aux.clone( originalObj );
         */
        clone: function(o) {
            try {
                if (typeof o !== 'object') { throw new Error('Given argument is not an object!'); }
                return JSON.parse( JSON.stringify(o) );
            } catch (ex) {
                throw new Error('Given object cannot have loops!');
            }
        },


        /**
         * Method to return the 'nth' position that an element occupies relatively to its parent.
         *
         * @method childIndex
         * @static
         * @param  {DOMElement} childEl Valid DOM Element.
         * @return {Number} Numerical position of an element relatively to its parent.
         * @example
         *     <!-- Imagine the following HTML: -->
         *     <ul>
         *       <li>One</li>
         *       <li>Two</li>
         *       <li id="test">Three</li>
         *       <li>Four</li>
         *     </ul>
         *
         *     <script>
         *         var testLi = Ink.s('#test');
         *         Ink.UI.Aux.childIndex( testLi ); // Returned value: 3
         *     </script>
         */
        childIndex: function(childEl) {
            if( Aux.isDOMElement(childEl) ){
                var els = Selector.select('> *', childEl.parentNode);
                for (var i = 0, f = els.length; i < f; ++i) {
                    if (els[i] === childEl) {
                        return i;
                    }
                }
            }
            throw 'not found!';
        },


        /**
         * This method provides a more convenient way to do an async AJAX request and expect a JSON response.
         * It offers a callback option, as third paramenter, for a better async handling.
         *
         * @method ajaxJSON
         * @static
         * @async
         * @param  {String} endpoint    Valid URL to be used as target by the request.
         * @param  {Object} params      This field is used in the thrown Exception to identify the parameter.
         * @example
         *     // In case there are several .myInput, it will retrieve the first found
         *     var el = Ink.UI.Aux.elOrSelector('.myInput','My Input');
         */
        ajaxJSON: function(endpoint, params, cb) {
            new Ajax(
                endpoint,
                {
                    evalJS:         'force',
                    method:         'POST',
                    parameters:     params,

                    onSuccess:  function( r) {
                        try {
                            r = r.responseJSON;
                            if (r.status !== 'ok') {
                                throw 'server error: ' + r.message;
                            }
                            cb(null, r);
                        } catch (ex) {
                            cb(ex);
                        }
                    },

                    onFailure: function() {
                        cb('communication failure');
                    }
                }
            );
        },


        /**
         * Method to get the current Ink layout applied.
         *
         * @method currentLayout
         * @static
         * @return {String}         Returns the value of one of the options of the property Layouts above defined.
         * @example
         *     var inkLayout = Ink.UI.Aux.currentLayout();
         */
        currentLayout: function() {
            var i, f, k, v, el, detectorEl = Selector.select('#ink-layout-detector')[0];
            if (!detectorEl) {
                detectorEl = document.createElement('div');
                detectorEl.id = 'ink-layout-detector';
                for (k in this.Layouts) {
                    if (this.Layouts.hasOwnProperty(k)) {
                        v = this.Layouts[k];
                        el = document.createElement('div');
                        el.className = 'show-' + v + ' hide-all';
                        el.setAttribute('data-ink-layout', v);
                        detectorEl.appendChild(el);
                    }
                }
                document.body.appendChild(detectorEl);
            }

            for (i = 0, f = detectorEl.childNodes.length; i < f; ++i) {
                el = detectorEl.childNodes[i];
                if (Css.getStyle(el, 'visibility') !== 'hidden') {
                    return el.getAttribute('data-ink-layout');
                }
            }
        },


        /**
         * Method to set the location's hash (window.location.hash).
         *
         * @method hashSet
         * @static
         * @param  {Object} o   Object with the info to be placed in the location's hash.
         * @example
         *     // It will set the location's hash like: <url>#key1=value1&key2=value2&key3=value3
         *     Ink.UI.Aux.hashSet({
         *         key1: 'value1',
         *         key2: 'value2',
         *         key3: 'value3'
         *     });
         */
        hashSet: function(o) {
            if (typeof o !== 'object') { throw new TypeError('o should be an object!'); }
            var hashParams = Url.getAnchorString();
            hashParams = Ink.extendObj(hashParams, o);
            window.location.hash = Url.genQueryString('', hashParams).substring(1);
        },

        /**
         * Method to remove children nodes from a given object.
         * This method was initially created to help solve a problem in Internet Explorer(s) that occurred when trying
         * to set the innerHTML of some specific elements like 'table'.
         *
         * @method cleanChildren
         * @static
         * @param  {DOMElement} parentEl Valid DOM Element
         * @example
         *     <!-- Imagine the following HTML: -->
         *     <ul id="myUl">
         *       <li>One</li>
         *       <li>Two</li>
         *       <li>Three</li>
         *       <li>Four</li>
         *     </ul>
         *
         *     <script>
         *     Ink.UI.Aux.cleanChildren( Ink.s( '#myUl' ) );
         *     </script>
         *
         *     <!-- After running it, the HTML changes to: -->
         *     <ul id="myUl"></ul>
         */
        cleanChildren: function(parentEl) {
            if( !Aux.isDOMElement(parentEl) ){
                throw 'Please provide a valid DOMElement';
            }
            var prevEl, el = parentEl.lastChild;
            while (el) {
                prevEl = el.previousSibling;
                parentEl.removeChild(el);
                el = prevEl;
            }
        },

        /**
         * This method stores the id and/or the classes of a given element in a given object.
         *
         * @method storeIdAndClasses
         * @static
         * @param  {DOMElement} fromEl    Valid DOM Element to get the id and classes from.
         * @param  {Object}     inObj     Object where the id and classes will be saved.
         * @example
         *     <div id="myDiv" class="aClass"></div>
         *
         *     <script>
         *         var storageObj = {};
         *         Ink.UI.Aux.storeIdAndClasses( Ink.s('#myDiv'), storageObj );
         *         // storageObj changes to:
         *         {
         *           _id: 'myDiv',
         *           _classes: 'aClass'
         *         }
         *     </script>
         */
        storeIdAndClasses: function(fromEl, inObj) {
            if( !Aux.isDOMElement(fromEl) ){
                throw 'Please provide a valid DOMElement as first parameter';
            }

            var id = fromEl.id;
            if (id) {
                inObj._id = id;
            }

            var classes = fromEl.className;
            if (classes) {
                inObj._classes = classes;
            }
        },

        /**
         * This method sets the id and className properties of a given DOM Element based on a given similar object
         * resultant of the previous function 'storeIdAndClasses'.
         *
         * @method restoreIdAndClasses
         * @static
         * @param  {DOMElement} toEl    Valid DOM Element to set the id and classes on.
         * @param  {Object}     inObj   Object where the id and classes to be set are.
         * @example
         *     <div></div>
         *
         *     <script>
         *         var storageObj = {
         *           _id: 'myDiv',
         *           _classes: 'aClass'
         *         };
         *
         *         Ink.UI.Aux.storeIdAndClasses( Ink.s('div'), storageObj );
         *     </script>
         *
         *     <!-- After the code runs the div element changes to: -->
         *     <div id="myDiv" class="aClass"></div>
         */
        restoreIdAndClasses: function(toEl, inObj) {

            if( !Aux.isDOMElement(toEl) ){
                throw 'Please provide a valid DOMElement as first parameter';
            }

            if (inObj._id && toEl.id !== inObj._id) {
                toEl.id = inObj._id;
            }

            if (inObj._classes && toEl.className.indexOf(inObj._classes) === -1) {
                if (toEl.className) { toEl.className += ' ' + inObj._classes; }
                else {                toEl.className  =       inObj._classes; }
            }

            if (inObj._instanceId && !toEl.getAttribute('data-instance')) {
                toEl.setAttribute('data-instance', inObj._instanceId);
            }
        },

        /**
         * This method saves a component's instance reference for later retrieval.
         *
         * @method registerInstance
         * @static
         * @param  {Object}     inst                Object that holds the instance.
         * @param  {DOMElement} el                  DOM Element to associate with the object.
         * @param  {Object}     [optionalPrefix]    Defaults to 'instance'
         */
        registerInstance: function(inst, el, optionalPrefix) {
            if (inst._instanceId) { return; }

            if (typeof inst !== 'object') { throw new TypeError('1st argument must be a JavaScript object!'); }

            if (inst._options && inst._options.skipRegister) { return; }

            if (!this.isDOMElement(el)) { throw new TypeError('2nd argument must be a DOM element!'); }
            if (optionalPrefix !== undefined && typeof optionalPrefix !== 'string') { throw new TypeError('3rd argument must be a string!'); }
            var id = (optionalPrefix || 'instance') + (++lastIdNum);
            instances[id] = inst;
            inst._instanceId = id;
            var dataInst = el.getAttribute('data-instance');
            dataInst = (dataInst !== null) ? [dataInst, id].join(' ') : id;
            el.setAttribute('data-instance', dataInst);
        },

        /**
         * This method deletes/destroy an instance with a given id.
         *
         * @method unregisterInstance
         * @static
         * @param  {String}     id       Id of the instance to be destroyed.
         */
        unregisterInstance: function(id) {
            delete instances[id];
        },

        /**
         * This method retrieves the registered instance(s) of a given element or instance id.
         *
         * @method getInstance
         * @static
         * @param  {String|DOMElement}      instanceIdOrElement      Instance's id or DOM Element from which we want the instances.
         * @return  {Object|Object[]}       Returns an instance or a collection of instances.
         */
        getInstance: function(instanceIdOrElement) {
            var ids;
            if (this.isDOMElement(instanceIdOrElement)) {
                ids = instanceIdOrElement.getAttribute('data-instance');
                if (ids === null) { throw new Error('argument is not a DOM instance element!'); }
            }
            else {
                ids = instanceIdOrElement;
            }

            ids = ids.split(' ');
            var inst, id, i, l = ids.length;

            var res = [];
            for (i = 0; i < l; ++i) {
                id = ids[i];
                if (!id) { throw new Error('Element is not a JS instance!'); }
                inst = instances[id];
                if (!inst) { throw new Error('Instance "' + id + '" not found!'); }
                res.push(inst);
            }

            return (l === 1) ? res[0] : res;
        },

        /**
         * This method retrieves the registered instance(s) of an element based on the given selector.
         *
         * @method getInstanceFromSelector
         * @static
         * @param  {String}             selector    CSS selector to define the element from which it'll get the instance(s).
         * @return  {Object|Object[]}               Returns an instance or a collection of instances.
         */
        getInstanceFromSelector: function(selector) {
            var el = Selector.select(selector)[0];
            if (!el) { throw new Error('Element not found!'); }
            return this.getInstance(el);
        },

        /**
         * This method retrieves the registered instances' ids of all instances.
         *
         * @method getInstanceIds
         * @static
         * @return  {String[]}     Id or collection of ids of all existing instances.
         */
        getInstanceIds: function() {
            var res = [];
            for (var id in instances) {
                if (instances.hasOwnProperty(id)) {
                    res.push( id );
                }
            }
            return res;
        },

        /**
         * This method retrieves all existing instances.
         *
         * @method getInstances
         * @static
         * @return  {Object[]}     Collection of existing instances.
         */
        getInstances: function() {
            var res = [];
            for (var id in instances) {
                if (instances.hasOwnProperty(id)) {
                    res.push( instances[id] );
                }
            }
            return res;
        },

        /**
         * This method is not to supposed to be invoked by the Aux component.
         * Components should copy this method as its destroy method.
         *
         * @method destroyComponent
         * @static
         */
        destroyComponent: function() {
            Ink.Util.Aux.unregisterInstance(this._instanceId);
            this._element.parentNode.removeChild(this._element);
        }

    };

    return Aux;

});

/**
 * @module Ink.UI.Modal_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Modal', '1', ['Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1'], function(Aux, Event, Css, Element, Selector, InkArray ) {
    'use strict';

    /**
     * @class Ink.UI.Modal
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @uses Ink.Util.Array
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *      @param {String}    [options.width]             Default/Initial width. Ex: '600px'
     *      @param {String}    [options.height]            Default/Initial height. Ex: '400px'
     *      @param {String}    [options.shadeClass]        Custom class to be added to the div.ink-shade
     *      @param {String}    [options.modalClass]        Custom class to be added to the div.ink-modal
     *      @param {String}    [options.trigger]           CSS Selector to target elements that will trigger the Modal.
     *      @param {String}    [options.triggerEvent]      Trigger's event to be listened. 'click' is the default value. Ex: 'mouseover', 'touchstart'...
     *      @param {String}    [options.markup]            Markup to be placed in the Modal when created
     *      @param {Function}  [options.onShow]            Callback function to run when the Modal is opened.
     *      @param {Function}  [options.onDismiss]         Callback function to run when the Modal is closed.
     *      @param {Boolean}   [options.closeOnClick]      Determines if the Modal should close when clicked outside of it. 'false' by default.
     *      @param {Boolean}   [options.responsive]        Determines if the Modal should behave responsively (adapt to smaller viewports).
     *      @param {Boolean}   [options.disableScroll]     Determines if the Modal should 'disable' the page's scroll (not the Modal's body).
     *
     * @example
     *      <div class="ink-shade fade">
     *          <div id="test" class="ink-modal fade" data-trigger="#bModal" data-width="800px" data-height="400px">
     *              <div class="modal-header">
     *                  <button class="modal-close ink-dismiss"></button>
     *                  <h5>Modal windows can have headers</h5>
     *              </div>
     *              <div class="modal-body" id="modalContent">
     *                  <h3>Please confirm your previous choice</h3>
     *                  <p>"No," said Peleg, "and he hasn't been baptized right either, or it would have washed some of that devil's blue off his face."</p>
     *                  <p>
     *                      <img src="http://placehold.it/800x400" style="width: 100%;" alt="">
     *                  </p>
     *                  <p>"Do tell, now," cried Bildad, "is this Philistine a regular member of Deacon Deuteronomy's meeting? I never saw him going there, and I pass it every Lord's day."</p>
     *                  <p>"I don't know anything about Deacon Deuteronomy or his meeting," said I; "all I know is, that Queequeg here is a born member of the First Congregational Church. He is a deacon himself, Queequeg is."</p>
     *              </div>
     *              <div class="modal-footer">
     *                  <div class="push-right">
     *                      <button class="ink-button info">Confirm</button>
     *                      <button class="ink-button caution ink-dismiss">Cancel</button>
     *                  </div>
     *              </div>
     *          </div>
     *      </div>
     *      <a href="#" id="bModal">Open modal</a>
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.Modal_1'], function( Selector, Modal ){
     *              var modalElement = Ink.s('#test');
     *              var modalObj = new Modal( modalElement );
     *          });
     *      </script>
     */
    var Modal = function(selector, options) {

        if( (typeof selector !== 'string') && (typeof selector !== 'object') && (typeof options.markup === 'undefined') ){
            throw 'Invalid Modal selector';
        } else if(typeof selector === 'string'){
            if( selector !== '' ){
                this._element = Selector.select(selector);
                if( this._element.length === 0 ){
                    /**
                     * From a developer's perspective this should be like it is...
                     * ... from a user's perspective, if it doesn't find elements, should just ignore it, no?
                     */
                    throw 'The Modal selector has not returned any elements';
                } else {
                    this._element = this._element[0];
                }
            }
        } else if( !!selector ){
            this._element = selector;
        }

        this._options = {
            /**
             * Width, height and markup really optional, as they can be obtained by the element
             */
            width:        undefined,
            height:       undefined,

            /**
             * To add extra classes
             */
            shadeClass: undefined,
            modalClass: undefined,

            /**
             * Optional trigger properties
             */
            trigger:      undefined,
            triggerEvent: 'click',

            /**
             * Remaining options
             */
            markup:       undefined,
            onShow:       undefined,
            onDismiss:    undefined,
            closeOnClick: false,
            responsive:    true,
            disableScroll: true
        };


        this._handlers = {
            click:   Ink.bindEvent(this._onClick, this),
            keyDown: Ink.bindEvent(this._onKeyDown, this),
            resize:  Ink.bindEvent(this._onResize, this)
        };

        this._wasDismissed = false;

        /**
         * Modal Markup
         */
        if( this._element ){
            this._markupMode = Css.hasClassName(this._element,'ink-modal'); // Check if the full modal comes from the markup
        } else {
            this._markupMode = false;
        }




        if( !this._markupMode ){


            this._modalShadow      = document.createElement('div');
            this._modalShadowStyle = this._modalShadow.style;

            this._modalDiv         = document.createElement('div');
            this._modalDivStyle    = this._modalDiv.style;

            if( !!this._element ){
                this._options.markup = this._element.innerHTML;
            }

            /**
             * Not in full markup mode, let's set the classes and css configurations
             */
            Css.addClassName( this._modalShadow,'ink-shade' );
            Css.addClassName( this._modalDiv,'ink-modal' );
            Css.addClassName( this._modalDiv,'ink-space' );

            /**
             * Applying the main css styles
             */
            // this._modalDivStyle.position = 'absolute';
            this._modalShadow.appendChild( this._modalDiv);
            document.body.appendChild( this._modalShadow );
        } else {
            this._modalDiv         = this._element;
            this._modalDivStyle    = this._modalDiv.style;
            this._modalShadow      = this._modalDiv.parentNode;
            this._modalShadowStyle = this._modalShadow.style;

            this._contentContainer = Selector.select(".modal-body",this._modalDiv);
            if( !this._contentContainer.length ){
                throw 'Missing div with class "modal-body"';
            }

            this._contentContainer = this._contentContainer[0];
            this._options.markup = this._contentContainer.innerHTML;

            /**
             * First, will handle the least important: The dataset
             */
            this._options = Ink.extendObj(this._options,Element.data(this._element));

        }

        /**
         * Now, the most important, the initialization options
         */
        this._options = Ink.extendObj(this._options,options || {});

        if( !this._markupMode ){
            this.setContentMarkup(this._options.markup);
        }

        if( typeof this._options.shadeClass === 'string' ){

            InkArray.each( this._options.shadeClass.split(' '), Ink.bind(function( item ){
                Css.addClassName( this._modalShadow, item.trim() );
            }, this));
        }

        if( typeof this._options.modalClass === 'string' ){
            InkArray.each( this._options.modalClass.split(' '), Ink.bind(function( item ){
                Css.addClassName( this._modalDiv, item.trim() );
            }, this));
        }

        if( ("trigger" in this._options) && ( typeof this._options.trigger !== 'undefined' ) ){
            var triggerElement,i;
            if( typeof this._options.trigger === 'string' ){
                triggerElement = Selector.select( this._options.trigger );
                if( triggerElement.length > 0 ){
                    for( i=0; i<triggerElement.length; i++ ){
                        Event.observe( triggerElement[i], this._options.triggerEvent, Ink.bindEvent(this._init, this) );
                    }
                }
            }
        } else {
            this._init();
        }
    };

    Modal.prototype = {

        /**
         * Init function called by the constructor
         * 
         * @method _init
         * @param {Event} [event] In case its fired by the trigger.
         * @private
         */
        _init: function(event) {

            if( event ){ Event.stop(event); }

            var elem = (document.compatMode === "CSS1Compat") ?  document.documentElement : document.body;

            this._resizeTimeout    = null;

            Css.addClassName( this._modalShadow,'ink-shade' );
            this._modalShadowStyle.display = this._modalDivStyle.display = 'block';
            setTimeout(Ink.bind(function(){
                Css.addClassName( this._modalShadow,'visible' );
                Css.addClassName( this._modalDiv,'visible' );
            }, this),100);

            /**
             * Fallback to the old one
             */
            this._contentElement = this._modalDiv;
            this._shadeElement   = this._modalShadow;

            /**
             * Setting the content of the modal
             */
            this.setContentMarkup( this._options.markup );

            /**
             * If any size has been user-defined, let's set them as max-width and max-height
             */
            if( typeof this._options.width !== 'undefined' ){
                this._modalDivStyle.width = this._options.width;
                if( this._options.width.indexOf('%') === -1 ){
                    this._modalDivStyle.maxWidth = Element.elementWidth(this._modalDiv) + 'px';
                }
            } else {
                this._modalDivStyle.maxWidth = this._modalDivStyle.width = Element.elementWidth(this._modalDiv)+'px';
            }

            if( parseInt(elem.clientWidth,10) <= parseInt(this._modalDivStyle.width,10) ){
                this._modalDivStyle.width = (~~(parseInt(elem.clientWidth,10)*0.9))+'px';
            }

            if( typeof this._options.height !== 'undefined' ){
                this._modalDivStyle.height = this._options.height;
                if( this._options.height.indexOf('%') === -1 ){
                    this._modalDivStyle.maxHeight = Element.elementHeight(this._modalDiv) + 'px';
                }
            } else {
                this._modalDivStyle.maxHeight = this._modalDivStyle.height = Element.elementHeight(this._modalDiv) + 'px';
            }

            if( parseInt(elem.clientHeight,10) <= parseInt(this._modalDivStyle.height,10) ){
                this._modalDivStyle.height = (~~(parseInt(elem.clientHeight,10)*0.9))+'px';
            }

            this.originalStatus = {
                viewportHeight:     parseInt(elem.clientHeight,10),
                viewportWidth:      parseInt(elem.clientWidth,10),
                width:              parseInt(this._modalDivStyle.maxWidth,10),
                height:             parseInt(this._modalDivStyle.maxHeight,10)
            };

            /**
             * Let's 'resize' it:
             */
            if(this._options.responsive) {
                this._onResize(true);
                Event.observe( window,'resize',this._handlers.resize );
            } else {
                this._resizeContainer();
                this._reposition();
            }

            if (this._options.onShow) {
                this._options.onShow(this);
            }

            if(this._options.disableScroll) {
                this._disableScroll();
            }

            // subscribe events
            Event.observe(this._shadeElement, 'click',   this._handlers.click);
            Event.observe(document,           'keydown', this._handlers.keyDown);

            Aux.registerInstance(this, this._shadeElement, 'modal');
        },

        /**
         * Responsible for repositioning the modal
         * 
         * @method _reposition
         * @private
         */
        _reposition: function(){

            this._modalDivStyle.top = this._modalDivStyle.left = '50%';

            this._modalDivStyle.marginTop = '-' + ( ~~( Element.elementHeight(this._modalDiv)/2) ) + 'px';
            this._modalDivStyle.marginLeft = '-' + ( ~~( Element.elementWidth(this._modalDiv)/2) ) + 'px';
        },

        /**
         * Responsible for resizing the modal
         * 
         * @method _onResize
         * @param {Boolean|Event} runNow Its executed in the begining to resize/reposition accordingly to the viewport. But usually it's an event object.
         * @private
         */
        _onResize: function( runNow ){

            if( typeof runNow === 'boolean' ){
                this._timeoutResizeFunction.call(this);
            } else if( !this._resizeTimeout && (typeof runNow === 'object') ){
                this._resizeTimeout = setTimeout(Ink.bind(this._timeoutResizeFunction, this),250);
            }
        },

        /**
         * Timeout Resize Function
         * 
         * @method _timeoutResizeFunction
         * @private
         */
        _timeoutResizeFunction: function(){
            /**
             * Getting the current viewport size
             */
            var
                elem = (document.compatMode === "CSS1Compat") ?  document.documentElement : document.body,
                currentViewportHeight = parseInt(elem.clientHeight,10),
                currentViewportWidth = parseInt(elem.clientWidth,10)
            ;

            if( ( currentViewportWidth > this.originalStatus.width ) /* && ( parseInt(this._modalDivStyle.maxWidth,10) >= Element.elementWidth(this._modalDiv) )*/ ){
                /**
                 * The viewport width has expanded
                 */
                this._modalDivStyle.width = this._modalDivStyle.maxWidth;

            } else {
                /**
                 * The viewport width has not changed or reduced
                 */
                //this._modalDivStyle.width = (( currentViewportWidth * this.originalStatus.width ) / this.originalStatus.viewportWidth ) + 'px';
                this._modalDivStyle.width = (~~( currentViewportWidth * 0.9)) + 'px';
            }

            if( (currentViewportHeight > this.originalStatus.height) && (parseInt(this._modalDivStyle.maxHeight,10) >= Element.elementHeight(this._modalDiv) ) ){

                /**
                 * The viewport height has expanded
                 */
                //this._modalDivStyle.maxHeight =
                this._modalDivStyle.height = this._modalDivStyle.maxHeight;

            } else {
                /**
                 * The viewport height has not changed, or reduced
                 */
                this._modalDivStyle.height = (~~( currentViewportHeight * 0.9)) + 'px';
            }

            this._resizeContainer();
            this._reposition();
            this._resizeTimeout = undefined;
        },

        /**
         * Navigation click handler
         * 
         * @method _onClick
         * @param {Event} ev
         * @private
         */
        _onClick: function(ev) {
            var tgtEl = Event.element(ev);

            if (Css.hasClassName(tgtEl, 'ink-close') || Css.hasClassName(tgtEl, 'ink-dismiss') ||
                (
                    this._options.closeOnClick &&
                    (!Element.descendantOf(this._shadeElement, tgtEl) || (tgtEl === this._shadeElement))
                )
            ) {
                var 
                    alertsInTheModal = Selector.select('.ink-alert',this._shadeElement),
                    alertsLength = alertsInTheModal.length
                ;
                for( var i = 0; i < alertsLength; i++ ){
                    if( Element.descendantOf(alertsInTheModal[i], tgtEl) ){
                        return;
                    }
                }

                Event.stop(ev);
                this.dismiss();
            }
        },

        /**
         * Responsible for handling the escape key pressing.
         *
         * @method _onKeyDown
         * @param  {Event} ev
         * @private
         */
        _onKeyDown: function(ev) {
            if (ev.keyCode !== 27 || this._wasDismissed) { return; }
            this.dismiss();
        },

        /**
         * Responsible for setting the size of the modal (and position) based on the viewport.
         * 
         * @method _resizeContainer
         * @private
         */
        _resizeContainer: function()
        {

            this._contentElement.style.overflow = this._contentElement.style.overflowX = this._contentElement.style.overflowY = 'hidden';
            var containerHeight = Element.elementHeight(this._modalDiv);

            this._modalHeader = Selector.select('.modal-header',this._modalDiv);
            if( this._modalHeader.length>0 ){
                this._modalHeader = this._modalHeader[0];
                containerHeight -= Element.elementHeight(this._modalHeader);
            }

            this._modalFooter = Selector.select('.modal-footer',this._modalDiv);
            if( this._modalFooter.length>0 ){
                this._modalFooter = this._modalFooter[0];
                containerHeight -= Element.elementHeight(this._modalFooter);
            }

            this._contentContainer.style.height = containerHeight + 'px';
            if( containerHeight !== Element.elementHeight(this._contentContainer) ){
                this._contentContainer.style.height = ~~(containerHeight - (Element.elementHeight(this._contentContainer) - containerHeight)) + 'px';
            }

            if( this._markupMode ){ return; }

            this._contentContainer.style.overflow = this._contentContainer.style.overflowX = 'hidden';
            this._contentContainer.style.overflowY = 'auto';
            this._contentElement.style.overflow = this._contentElement.style.overflowX = this._contentElement.style.overflowY = 'visible';
        },

        /**
         * Responsible for 'disabling' the page scroll
         * 
         * @method _disableScroll
         * @private
         */
        _disableScroll: function()
        {
            this._oldScrollPos = Element.scroll();
            this._onScrollBinded = Ink.bindEvent(function(event) {
                var tgtEl = Event.element(event);

                if( !Element.descendantOf(this._modalShadow, tgtEl) ){
                    Event.stop(event);
                    window.scrollTo(this._oldScrollPos[0], this._oldScrollPos[1]);
                }
            },this);
            Event.observe(window, 'scroll', this._onScrollBinded);
            Event.observe(document, 'touchmove', this._onScrollBinded);
        },

        /**************
         * PUBLIC API *
         **************/

        /**
         * Dismisses the modal
         * 
         * @method dismiss
         * @public
         */
        dismiss: function() {
            if (this._options.onDismiss) {
                this._options.onDismiss(this);
            }

            if(this._options.disableScroll) {
                Event.stopObserving(window, 'scroll', this._onScrollBinded);
                Event.stopObserving(document, 'touchmove', this._onScrollBinded);
            }

            if( this._options.responsive ){
                Event.stopObserving(window, 'resize', this._handlers.resize);
            }

            // this._modalShadow.parentNode.removeChild(this._modalShadow);

            if( !this._markupMode ){
                this._modalShadow.parentNode.removeChild(this._modalShadow);
                this.destroy();
            } else {
                Css.removeClassName( this._modalDiv, 'visible' );
                Css.removeClassName( this._modalShadow, 'visible' );

                var
                    dismissInterval,
                    transitionEndFn = Ink.bindEvent(function(){
                        if( !dismissInterval ){ return; }
                        this._modalShadowStyle.display = 'none';
                        Event.stopObserving(document,'transitionend',transitionEndFn);
                        Event.stopObserving(document,'oTransitionEnd',transitionEndFn);
                        Event.stopObserving(document,'webkitTransitionEnd',transitionEndFn);
                        clearInterval(dismissInterval);
                        dismissInterval = undefined;
                    }, this)
                ;

                Event.observe(document,'transitionend',transitionEndFn);
                Event.observe(document,'oTransitionEnd',transitionEndFn);
                Event.observe(document,'webkitTransitionEnd',transitionEndFn);

                if( !dismissInterval ){
                    dismissInterval = setInterval(Ink.bind(function(){
                        if( this._modalShadowStyle.opacity > 0 ){
                            return;
                        } else {
                            this._modalShadowStyle.display = 'none';
                            clearInterval(dismissInterval);
                            dismissInterval = undefined;
                        }

                    }, this),500);
                }
            }
        },

        /**
         * Removes the modal from the DOM
         * 
         * @method destroy
         * @public
         */
        destroy: function() {
            Aux.unregisterInstance(this._instanceId);

        },

        /**
         * Returns the content DOM element
         * 
         * @method getContentElement
         * @return {DOMElement} Modal main cointainer.
         * @public
         */
        getContentElement: function() {
            return this._contentContainer;
        },

        /**
         * Replaces the content markup
         * 
         * @method setContentMarkup
         * @param {String} contentMarkup
         * @public
         */
        setContentMarkup: function(contentMarkup) {
            if( !this._markupMode ){
                this._modalDiv.innerHTML = [contentMarkup].join('');
                this._contentContainer = Selector.select(".modal-body",this._modalDiv);
                if( !this._contentContainer.length ){
                    // throw 'Missing div with class "modal-body"';
                    var tempHeader = Selector.select(".modal-header",this._modalDiv);
                    var tempFooter = Selector.select(".modal-footer",this._modalDiv);

                    InkArray.each(tempHeader,Ink.bind(function( element ){ element.parentNode.removeChild(element); },this));
                    InkArray.each(tempFooter,Ink.bind(function( element ){ element.parentNode.removeChild(element); },this));

                    var body = document.createElement('div');
                    Css.addClassName(body,'modal-body');
                    body.innerHTML = this._modalDiv.innerHTML;
                    this._modalDiv.innerHTML = '';

                    InkArray.each(tempHeader,Ink.bind(function( element ){ this._modalDiv.appendChild(element); },this));
                    this._modalDiv.appendChild(body);
                    InkArray.each(tempFooter,Ink.bind(function( element ){ this._modalDiv.appendChild(element); },this));
                    
                    this._contentContainer = Selector.select(".modal-body",this._modalDiv);
                }
                this._contentContainer = this._contentContainer[0];
            } else {
                this._contentContainer.innerHTML = [contentMarkup].join('');
            }
            this._contentElement = this._modalDiv;
            this._resizeContainer();
        }

    };

    return Modal;

});

/**
 * @module Ink.UI.ProgressBar_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.ProgressBar', '1', ['Ink.Dom.Selector_1','Ink.Dom.Element_1'], function( Selector, Element ) {
    'use strict';

    /**
     * Associated to a .ink-progress-bar element, it provides the necessary
     * method - setValue() - for the user to change the element's value.
     * 
     * @class Ink.UI.ProgressBar
     * @constructor
     * @version 1
     * @uses Ink.Dom.Selector
     * @uses Ink.Dom.Element
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *     @param {Number}     [options.startValue]          Percentage of the bar that is filled. Range between 0 and 100. Default: 0
     *     @param {Function}   [options.onStart]             Callback that is called when a change of value is started
     *     @param {Function}   [options.onEnd]               Callback that is called when a change of value ends
     *
     * @example
     *      <div class="ink-progress-bar grey" data-start-value="70%">
     *          <span class="caption">I am a grey progress bar</span>
     *          <div class="bar grey"></div>
     *      </div>
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.ProgressBar_1'], function( Selector, ProgressBar ){
     *              var progressBarElement = Ink.s('.ink-progress-bar');
     *              var progressBarObj = new ProgressBar( progressBarElement );
     *          });
     *      </script>
     */
    var ProgressBar = function( selector, options ){

        if( typeof selector !== 'object' ){
            if( typeof selector !== 'string' ){
                throw '[Ink.UI.ProgressBar] :: Invalid selector';
            } else {
                this._element = Selector.select(selector);
                if( this._element.length < 1 ){
                    throw "[Ink.UI.ProgressBar] :: Selector didn't find any elements";
                }
                this._element = this._element[0];
            }
        } else {
            this._element = selector;
        }


        this._options = Ink.extendObj({
            'startValue': 0,
            'onStart': function(){},
            'onEnd': function(){}
        },Element.data(this._element));

        this._options = Ink.extendObj( this._options, options || {});
        this._value = this._options.startValue;

        this._init();
    };

    ProgressBar.prototype = {

        /**
         * Init function called by the constructor
         * 
         * @method _init
         * @private
         */
        _init: function(){
            this._elementBar = Selector.select('.bar',this._element);
            if( this._elementBar.length < 1 ){
                throw '[Ink.UI.ProgressBar] :: Bar element not found';
            }
            this._elementBar = this._elementBar[0];

            this._options.onStart = Ink.bind(this._options.onStart,this);
            this._options.onEnd = Ink.bind(this._options.onEnd,this);
            this.setValue( this._options.startValue );
        },

        /**
         * Sets the value of the Progressbar
         * 
         * @method setValue
         * @param {Number} newValue Numeric value, between 0 and 100, that represents the percentage of the bar.
         * @public
         */
        setValue: function( newValue ){
            this._options.onStart( this._value);

            newValue = parseInt(newValue,10);
            if( isNaN(newValue) || (newValue < 0) ){
                newValue = 0;
            } else if( newValue>100 ){
                newValue = 100;
            }
            this._value = newValue;
            this._elementBar.style.width =  this._value + '%';

            this._options.onEnd( this._value );
        }
    };

    return ProgressBar;

});

/**
 * @module Ink.UI.SmoothScroller_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.SmoothScroller', '1', ['Ink.Dom.Event_1','Ink.Dom.Selector_1','Ink.Dom.Loaded_1'], function(Event, Selector, Loaded ) {
    'use strict';

    /**
     * @class Ink.UI.SmoothScroller
     * @version 1
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Selector
     * @uses Ink.Dom.Loaded
     * @static
     */
    var SmoothScroller = {

        /**
         * Sets the speed of the scrolling
         *
         * @property
         * @type {Number}
         * @readOnly
         * @static
         */
        speed: 10,

        /**
         * Returns the Y position of the div
         *
         * @method gy
         * @param  {DOMElement} d DOMElement to get the Y position from
         * @return {Number}   Y position of div 'd'
         * @public
         * @static
         */
        gy: function(d) {
            var gy;
            gy = d.offsetTop;
            if (d.offsetParent){
                while ( (d = d.offsetParent) ){
                    gy += d.offsetTop;
                }
            }
            return gy;
        },


        /**
         * Returns the current scroll position
         *
         * @method scrollTop
         * @return {Number}  Current scroll position
         * @public
         * @static
         */
        scrollTop: function() {
            var
                body = document.body,
                d = document.documentElement
            ;
            if (body && body.scrollTop){
                return body.scrollTop;
            }
            if (d && d.scrollTop){
                return d.scrollTop;
            }
            if (window.pageYOffset)
            {
                return window.pageYOffset;
            }
            return 0;
        },

        /**
         * Attaches an event for an element
         *
         * @method add
         * @param  {DOMElement} el DOMElement to make the listening of the event
         * @param  {String} event Event name to be listened
         * @param  {DOMElement} fn Callback function to run when the event is triggered.
         * @public
         * @static
         */
        add: function(el, event, fn) {
            Event.observe(el,event,fn);
            return;
        },


        /**
         * Kill an event of an element
         *
         * @method end
         * @param  {String} e Event to be killed/stopped
         * @public
         * @static
         */
        // kill an event of an element
        end: function(e) {
            if (window.event) {
                window.event.cancelBubble = true;
                window.event.returnValue = false;
                return;
            }
            Event.stop(e);
        },


        /**
         * Moves the scrollbar to the target element
         *
         * @method scroll
         * @param  {Number} d Y coordinate value to stop
         * @public
         * @static
         */
        scroll: function(d) {
            var a = Ink.UI.SmoothScroller.scrollTop();
            if (d > a) {
                a += Math.ceil((d - a) / Ink.UI.SmoothScroller.speed);
            } else {
                a = a + (d - a) / Ink.UI.SmoothScroller.speed;
            }

            window.scrollTo(0, a);
            if ((a) === d || Ink.UI.SmoothScroller.offsetTop === a)
            {
                clearInterval(Ink.UI.SmoothScroller.interval);
            }
            Ink.UI.SmoothScroller.offsetTop = a;
        },


        /**
         * Initializer that adds the rendered to run when the page is ready
         *
         * @method init
         * @public
         * @static
         */
        // initializer that adds the renderer to the onload function of the window
        init: function() {
            Loaded.run(Ink.UI.SmoothScroller.render);
        },

        /**
         * This method extracts all the anchors and validates thenm as # and attaches the events
         *
         * @method render
         * @public
         * @static
         */
        render: function() {
            var a = Selector.select('a.scrollableLink');

            Ink.UI.SmoothScroller.end(this);

            for (var i = 0; i < a.length; i++) {
                var _elm = a[i];
                if (_elm.href && _elm.href.indexOf('#') !== -1 && ((_elm.pathname === location.pathname) || ('/' + _elm.pathname === location.pathname))) {
                    Ink.UI.SmoothScroller.add(_elm, 'click', Ink.UI.SmoothScroller.end);
                    Event.observe(_elm,'click', Ink.bindEvent(Ink.UI.SmoothScroller.clickScroll, this, _elm));
                }
            }
        },


        /**
         * Click handler
         *
         * @method clickScroll
         * @public
         * @static
         */
        clickScroll: function(event, _elm) {
            /*
            Ink.UI.SmoothScroller.end(this);
            var hash = this.hash.substr(1);
            var elm = Selector.select('a[name="' + hash + '"],#' + hash);

            if (typeof(elm[0]) !== 'undefined') {

                if (this.parentNode.className.indexOf('active') === -1) {
                    var ul = this.parentNode.parentNode,
                        li = ul.firstChild;
                    do {
                        if ((typeof(li.tagName) !== 'undefined') && (li.tagName.toUpperCase() === 'LI') && (li.className.indexOf('active') !== -1)) {
                            li.className = li.className.replace('active', '');
                            break;
                        }
                    } while ((li = li.nextSibling));
                    this.parentNode.className += " active";
                }
                clearInterval(Ink.UI.SmoothScroller.interval);
                Ink.UI.SmoothScroller.interval = setInterval('Ink.UI.SmoothScroller.scroll(' + Ink.UI.SmoothScroller.gy(elm[0]) + ')', 10);

            }
            */
            Ink.UI.SmoothScroller.end(_elm);
            if(_elm !== null && _elm.getAttribute('href') !== null) {
                var hashIndex = _elm.href.indexOf('#');
                if(hashIndex === -1) {
                    return;
                }
                var hash = _elm.href.substr((hashIndex + 1));
                var elm = Selector.select('a[name="' + hash + '"],#' + hash);

                if (typeof(elm[0]) !== 'undefined') {

                    if (_elm.parentNode.className.indexOf('active') === -1) {
                        var ul = _elm.parentNode.parentNode,
                            li = ul.firstChild;
                        do {
                            if ((typeof(li.tagName) !== 'undefined') && (li.tagName.toUpperCase() === 'LI') && (li.className.indexOf('active') !== -1)) {
                                li.className = li.className.replace('active', '');
                                break;
                            }
                        } while ((li = li.nextSibling));
                        _elm.parentNode.className += " active";
                    }
                    clearInterval(Ink.UI.SmoothScroller.interval);
                    Ink.UI.SmoothScroller.interval = setInterval('Ink.UI.SmoothScroller.scroll(' + Ink.UI.SmoothScroller.gy(elm[0]) + ')', 10);

                }
            }

        }
    };

    return SmoothScroller;

});

/**
 * @module Ink.UI.SortableList_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.SortableList', '1', ['Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1'], function(Aux, Event, Css, Element, Selector, InkArray ) {
    'use strict';

    /**
     * Adds sortable behaviour to any list!
     * 
     * @class Ink.UI.SortableList
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @uses Ink.Util.Array
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *     @param {String} [options.dragObject] CSS Selector. The element that will trigger the dragging in the list. Default is 'li'.
     * @example
     *      <ul class="unstyled ink-sortable-list" id="slist" data-instance="sortableList9">
     *          <li><span class="ink-label info"><i class="icon-reorder"></i>drag here</span>primeiro</li>
     *          <li><span class="ink-label info"><i class="icon-reorder"></i>drag here</span>segundo</li>
     *          <li><span class="ink-label info"><i class="icon-reorder"></i>drag here</span>terceiro</li>
     *      </ul>
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.SortableList_1'], function( Selector, SortableList ){
     *              var sortableListElement = Ink.s('.ink-sortable-list');
     *              var sortableListObj = new SortableList( sortableListElement );
     *          });
     *      </script>
     */
    var SortableList = function(selector, options) {

        this._element = Aux.elOrSelector(selector, '1st argument');

        if( !Aux.isDOMElement(selector) && (typeof selector !== 'string') ){
            throw '[Ink.UI.SortableList] :: Invalid selector';
        } else if( typeof selector === 'string' ){
            this._element = Ink.Dom.Selector.select( selector );

            if( this._element.length < 1 ){
                throw '[Ink.UI.SortableList] :: Selector has returned no elements';
            }
            this._element = this._element[0];

        } else {
            this._element = selector;
        }

        this._options = Ink.extendObj({
            dragObject: 'li'
        }, Ink.Dom.Element.data(this._element));

        this._options = Ink.extendObj( this._options, options || {});

        this._handlers = {
            down: Ink.bindEvent(this._onDown,this),
            move: Ink.bindEvent(this._onMove,this),
            up:   Ink.bindEvent(this._onUp,this)
        };

        this._model = [];
        this._index = undefined;
        this._isMoving = false;

        if (this._options.model instanceof Array) {
            this._model = this._options.model;
            this._createdFrom = 'JSON';
        }
        else if (this._element.nodeName.toLowerCase() === 'ul') {
            this._createdFrom = 'DOM';
        }
        else {
            throw new TypeError('You must pass a selector expression/DOM element as 1st option or provide a model on 2nd argument!');
        }


        this._dragTriggers = Selector.select( this._options.dragObject, this._element );
        if( !this._dragTriggers ){
            throw "[Ink.UI.SortableList] :: Drag object not found";
        }

        this._init();
    };

    SortableList.prototype = {

        /**
         * Init function called by the constructor.
         * 
         * @method _init
         * @private
         */
        _init: function() {
            // extract model
            if (this._createdFrom === 'DOM') {
                this._extractModelFromDOM();
                this._createdFrom = 'JSON';
            }

            var isTouch = 'ontouchstart' in document.documentElement;

            this._down = isTouch ? 'touchstart': 'mousedown';
            this._move = isTouch ? 'touchmove' : 'mousemove';
            this._up   = isTouch ? 'touchend'  : 'mouseup';

            // subscribe events
            var db = document.body;
            Event.observe(db, this._move, this._handlers.move);
            Event.observe(db, this._up,   this._handlers.up);
            this._observe();

            Aux.registerInstance(this, this._element, 'sortableList');
        },

        /**
         * Sets the event handlers.
         * 
         * @method _observe
         * @private
         */
        _observe: function() {
            Event.observe(this._element, this._down, this._handlers.down);
        },

        /**
         * Updates the model from the UL representation
         * 
         * @method _extractModelFromDOM
         * @private
         */
        _extractModelFromDOM: function() {
            this._model = [];
            var that = this;

            var liEls = Selector.select('> li', this._element);

            InkArray.each(liEls,function(liEl) {
                //var t = Element.getChildrenText(liEl);
                var t = liEl.innerHTML;
                that._model.push(t);
            });
        },

        /**
         * Returns the top element for the gallery DOM representation
         * 
         * @method _generateMarkup
         * @return {DOMElement}
         * @private
         */
        _generateMarkup: function() {
            var el = document.createElement('ul');
            el.className = 'unstyled ink-sortable-list';
            var that = this;

            InkArray.each(this._model,function(label, idx) {
                var liEl = document.createElement('li');
                if (idx === that._index) {
                    liEl.className = 'drag';
                }
                liEl.innerHTML = [
                    // '<span class="ink-label ink-info"><i class="icon-reorder"></i>', that._options.dragLabel, '</span>', label
                    label
                ].join('');
                el.appendChild(liEl);
            });

            return el;
        },

        /**
         * Extracts the Y coordinate of the mouse from the given MouseEvent
         * 
         * @method _getY
         * @param  {Event} ev
         * @return {Number}
         * @private
         */
        _getY: function(ev) {
            if (ev.type.indexOf('touch') === 0) {
                //console.log(ev.type, ev.changedTouches[0].pageY);
                return ev.changedTouches[0].pageY;
            }
            if (typeof ev.pageY === 'number') {
                return ev.pageY;
            }
            return ev.clientY;
        },

        /**
         * Refreshes the markup.
         * 
         * @method _refresh
         * @param {Boolean} skipObs True if needs to set the event handlers, false if not.
         * @private
         */
        _refresh: function(skipObs) {
            var el = this._generateMarkup();
            this._element.parentNode.replaceChild(el, this._element);
            this._element = el;

            Aux.restoreIdAndClasses(this._element, this);

            this._dragTriggers = Selector.select( this._options.dragObject, this._element );

            // subscribe events
            if (!skipObs) { this._observe(); }
        },

        /**
         * Mouse down handler
         * 
         * @method _onDown
         * @param {Event} ev
         * @return {Boolean|undefined} [description]
         * @private
         */
        _onDown: function(ev) {
            if (this._isMoving) { return; }
            var tgtEl = Event.element(ev);

            if( !InkArray.inArray(tgtEl,this._dragTriggers) ){
                while( !InkArray.inArray(tgtEl,this._dragTriggers) && (tgtEl.nodeName.toLowerCase() !== 'body') ){
                    tgtEl = tgtEl.parentNode;
                }

                if( tgtEl.nodeName.toLowerCase() === 'body' ){
                    return;
                }
            }

            Event.stop(ev);

            var liEl;
            if( tgtEl.nodeName.toLowerCase() !== 'li' ){
                while( (tgtEl.nodeName.toLowerCase() !== 'li') && (tgtEl.nodeName.toLowerCase() !== 'body') ){
                    tgtEl = tgtEl.parentNode;
                }
            }
            liEl = tgtEl;

            this._index = Aux.childIndex(liEl);
            this._height = liEl.offsetHeight;
            this._startY = this._getY(ev);
            this._isMoving = true;

            document.body.style.cursor = 'move';

            this._refresh(false);

            return false;
        },

        /**
         * Mouse move handler
         * 
         * @method _onMove
         * @param {Event} ev
         * @private
         */
        _onMove: function(ev) {
            if (!this._isMoving) { return; }
            Event.stop(ev);

            var y = this._getY(ev);
            //console.log(y);
            var dy = y - this._startY;
            var sign = dy > 0 ? 1 : -1;
            var di = sign * Math.floor( Math.abs(dy) / this._height );
            if (di === 0) { return; }
            di = di / Math.abs(di);
            if ( (di === -1 && this._index === 0) ||
                 (di === 1 && this._index === this._model.length - 1) ) { return; }

            var a = di > 0 ? this._index : this._index + di;
            var b = di < 0 ? this._index : this._index + di;
            //console.log(a, b);
            this._model.splice(a, 2, this._model[b], this._model[a]);
            this._index += di;
            this._startY = y;

            this._refresh(false);
        },

        /**
         * Mouse up handler
         * 
         * @method _onUp
         * @param {Event} ev
         * @private
         */
        _onUp: function(ev) {
            if (!this._isMoving) { return; }
            Event.stop(ev);

            this._index = undefined;
            this._isMoving = false;
            document.body.style.cursor = '';

            this._refresh();
        },



        /**************
         * PUBLIC API *
         **************/

        /**
         * Returns a copy of the model
         * 
         * @method getModel
         * @return {Array} Copy of the model
         * @public
         */
        getModel: function() {
            return this._model.slice();
        },

        /**
         * Unregisters the component and removes its markup from the DOM
         * 
         * @method destroy
         * @public
         */
        destroy: Aux.destroyComponent

    };

    return SortableList;

});

/**
 * @module Ink.UI.Spy_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Spy', '1', ['Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1'], function(Aux, Event, Css, Element, Selector, InkArray ) {
    'use strict';

    /**
     * Spy is a component that 'spies' an element (or a group of elements) and when they leave the viewport (through the top),
     * highlight an option - related to that element being spied - that resides in a menu, initially identified as target.
     * 
     * @class Ink.UI.Spy
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @uses Ink.Util.Array
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *     @param {DOMElement|String}     options.target          Target menu on where the spy will highlight the right option.
     * @example
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.Spy_1'], function( Selector, Spy ){
     *              var menuElement = Ink.s('#menu');
     *              var specialAnchorToSpy = Ink.s('#specialAnchor');
     *              var spyObj = new Spy( specialAnchorToSpy, {
     *                  target: menuElement
     *              });
     *          });
     *      </script>
     */
    var Spy = function( selector, options ){

        this._rootElement = Aux.elOrSelector(selector,'1st argument');

        /**
         * Setting default options and - if needed - overriding it with the data attributes
         */
        this._options = Ink.extendObj({
            target: undefined
        }, Element.data( this._rootElement ) );

        /**
         * In case options have been defined when creating the instance, they've precedence
         */
        this._options = Ink.extendObj(this._options,options || {});

        this._options.target = Aux.elOrSelector( this._options.target, 'Target' );

        this._scrollTimeout = null;
        this._init();
    };

    Spy.prototype = {

        /**
         * Stores the spy elements
         *
         * @property _elements
         * @type {Array}
         * @readOnly
         * 
         */
        _elements: [],

        /**
         * Init function called by the constructor
         * 
         * @method _init
         * @private
         */
        _init: function(){
            Event.observe( document, 'scroll', Ink.bindEvent(this._onScroll,this) );
            this._elements.push(this._rootElement);
        },

        /**
         * Scroll handler. Responsible for highlighting the right options of the target menu.
         * 
         * @method _onScroll
         * @private
         */
        _onScroll: function(){

            var scrollHeight = Element.scrollHeight(); 
            if( (scrollHeight < this._rootElement.offsetTop) ){
                return;
            } else {
                for( var i = 0, total = this._elements.length; i < total; i++ ){
                    if( (this._elements[i].offsetTop <= scrollHeight) && (this._elements[i] !== this._rootElement) && (this._elements[i].offsetTop > this._rootElement.offsetTop) ){
                        return;
                    }
                }
            }

            InkArray.each(
                Selector.select(
                    'a',
                    this._options.target
                ), Ink.bind(function(item){

                    var comparisonValue = ( ("name" in this._rootElement) && this._rootElement.name ?
                        '#' + this._rootElement.name : '#' + this._rootElement.id
                    );

                    if( item.href.substr(item.href.indexOf('#')) === comparisonValue ){
                        Css.addClassName(Element.findUpwardsByTag(item,'li'),'active');
                    } else {
                        Css.removeClassName(Element.findUpwardsByTag(item,'li'),'active');
                    }
                },this)
            );
        }

    };

    return Spy;

});

/**
 * @module Ink.UI.Sticky_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Sticky', '1', ['Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1'], function(Aux, Event, Css, Element, Selector ) {
    'use strict';

    /**
     * The Sticky component takes an element and transforms it's behavior in order to, when the user scrolls he sets its position
     * to fixed and maintain it until the user scrolls back to the same place.
     * 
     * @class Ink.UI.Sticky
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *     @param {Number}     options.offsetBottom       Number of pixels of distance from the bottomElement.
     *     @param {Number}     options.offsetTop          Number of pixels of distance from the topElement.
     *     @param {String}     options.topElement         CSS Selector that specifies a top element with which the component could collide.
     *     @param {String}     options.bottomElement      CSS Selector that specifies a bottom element with which the component could collide.
     * @example
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.Sticky_1'], function( Selector, Sticky ){
     *              var menuElement = Ink.s('#menu');
     *              var stickyObj = new Sticky( menuElement );
     *          });
     *      </script>
     */
    var Sticky = function( selector, options ){

        if( typeof selector !== 'object' && typeof selector !== 'string'){
            throw '[Sticky] :: Invalid selector defined';
        }

        if( typeof selector === 'object' ){
            this._rootElement = selector;
        } else {
            this._rootElement = Selector.select( selector );
            if( this._rootElement.length <= 0) {
                throw "[Sticky] :: Can't find any element with the specified selector";
            }
            this._rootElement = this._rootElement[0];
        }

        /**
         * Setting default options and - if needed - overriding it with the data attributes
         */
        this._options = Ink.extendObj({
            offsetBottom: 0,
            offsetTop: 0,
            topElement: undefined,
            bottomElement: undefined
        }, Element.data( this._rootElement ) );

        /**
         * In case options have been defined when creating the instance, they've precedence
         */
        this._options = Ink.extendObj(this._options,options || {});

        if( typeof( this._options.topElement ) !== 'undefined' ){
            this._options.topElement = Aux.elOrSelector( this._options.topElement, 'Top Element');
        } else {
            this._options.topElement = Aux.elOrSelector( 'body', 'Top Element');
        }

        if( typeof( this._options.bottomElement ) !== 'undefined' ){
            this._options.bottomElement = Aux.elOrSelector( this._options.bottomElement, 'Bottom Element');
        } else {
            this._options.bottomElement = Aux.elOrSelector( 'body', 'Top Element');
        }

        this._computedStyle = window.getComputedStyle ? window.getComputedStyle(this._rootElement, null) : this._rootElement.currentStyle;
        this._dims = {
            height: this._computedStyle.height,
            width: this._computedStyle.width
        };
        this._init();
    };

    Sticky.prototype = {

        /**
         * Init function called by the constructor
         * 
         * @method _init
         * @private
         */
        _init: function(){
            Event.observe( document, 'scroll', Ink.bindEvent(this._onScroll,this) );
            Event.observe( window, 'resize', Ink.bindEvent(this._onResize,this) );

            this._calculateOriginalSizes();

            this._calculateOffsets();

        },

        /**
         * Scroll handler.
         * 
         * @method _onScroll
         * @private
         */
        _onScroll: function(){


            var viewport = (document.compatMode === "CSS1Compat") ?  document.documentElement : document.body;

            if( 
                ( ( (Element.elementWidth(this._rootElement)*100)/viewport.clientWidth ) > 90 ) ||
                ( viewport.clientWidth<=649 )
            ){
                if( Element.hasAttribute(this._rootElement,'style') ){
                    this._rootElement.removeAttribute('style');
                }
                return;
            }


            if( this._scrollTimeout ){
                clearTimeout(this._scrollTimeout);
            }

            this._scrollTimeout = setTimeout(Ink.bind(function(){
                    
                var scrollHeight = Element.scrollHeight();

                if( Element.hasAttribute(this._rootElement,'style') ){
                    if( scrollHeight <= this._options.offsetTop){
                        this._rootElement.removeAttribute('style');
                    } else if( ((document.body.scrollHeight-(scrollHeight+parseInt(this._dims.height,10))) < this._options.offsetBottom) ){
                        this._rootElement.style.position = 'fixed';
                        this._rootElement.style.top = 'auto';
                        if( this._options.offsetBottom < parseInt(document.body.scrollHeight - (document.documentElement.clientHeight+scrollHeight),10) ){
                            this._rootElement.style.bottom = this._options.originalOffsetBottom + 'px';
                        } else {
                            this._rootElement.style.bottom = this._options.offsetBottom - parseInt(document.body.scrollHeight - (document.documentElement.clientHeight+scrollHeight),10) + 'px';
                        }
                        this._rootElement.style.width = this._options.originalWidth + 'px';
                    } else if( ((document.body.scrollHeight-(scrollHeight+parseInt(this._dims.height,10))) >= this._options.offsetBottom) ){
                        this._rootElement.style.position = 'fixed';
                        this._rootElement.style.bottom = 'auto';
                        this._rootElement.style.top = this._options.originalOffsetTop + 'px';
                        this._rootElement.style.width = this._options.originalWidth + 'px';
                    }
                } else {
                    if(scrollHeight <= this._options.offsetTop ){
                        return;
                    }

                    this._rootElement.style.position = 'fixed';
                    this._rootElement.style.bottom = 'auto';
                    this._rootElement.style.top = this._options.offsetTop + 'px';
                    this._rootElement.style.width = this._options.originalWidth + 'px';
                }

                this._scrollTimeout = undefined;
            },this), 0);
        },

        /**
         * Resize handler
         * 
         * @method _onResize
         * @private
         */
        _onResize: function(){

            if( this._resizeTimeout ){
                clearTimeout(this._resizeTimeout);
            }

            this._resizeTimeout = setTimeout(Ink.bind(function(){
                this._rootElement.removeAttribute('style');
                this._calculateOriginalSizes();
                this._calculateOffsets();
            }, this),0);

        },

        /**
         * On each resizing (and in the beginning) the component recalculates the offsets, since
         * the top and bottom element heights might have changed.
         * 
         * @method _calculateOffsets
         * @private
         */
        _calculateOffsets: function(){

            /**
             * Calculating the offset top
             */
            if( typeof this._options.topElement !== 'undefined' ){


                if( this._options.topElement.nodeName.toLowerCase() !== 'body' ){
                    var
                        topElementHeight = Element.elementHeight( this._options.topElement ),
                        topElementTop = Element.elementTop( this._options.topElement )
                    ;

                    this._options.offsetTop = ( parseInt(topElementHeight,10) + parseInt(topElementTop,10) ) + parseInt(this._options.originalOffsetTop,10);
                } else {
                    this._options.offsetTop = parseInt(this._options.originalOffsetTop,10);
                }
            }

            /**
             * Calculating the offset bottom
             */
            if( typeof this._options.bottomElement !== 'undefined' ){

                if( this._options.bottomElement.nodeName.toLowerCase() !== 'body' ){
                    var
                        bottomElementHeight = Element.elementHeight(this._options.bottomElement)
                    ;
                    this._options.offsetBottom = parseInt(bottomElementHeight,10) + parseInt(this._options.originalOffsetBottom,10);
                } else {
                    this._options.offsetBottom = parseInt(this._options.originalOffsetBottom,10);
                }
            }

            this._onScroll();

        },

        /**
         * Function to calculate the 'original size' of the element.
         * It's used in the begining (_init method) and when a scroll happens
         *
         * @method _calculateOriginalSizes
         * @private
         */
        _calculateOriginalSizes: function(){
            this._options.originalOffsetTop = parseInt(this._options.offsetTop,10);
            this._options.originalOffsetBottom = parseInt(this._options.offsetBottom,10);
            this._options.originalTop = parseInt(this._rootElement.offsetTop,10);
            if(isNaN(this._options.originalWidth = parseInt(this._dims.width,10))) {
                this._options.originalWidth = 0;
            }
            this._options.originalWidth = parseInt(this._computedStyle.width,10);
        }

    };

    return Sticky;

});

/**
 * @module Ink.UI.Table_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Table', '1', ['Ink.Net.Ajax_1','Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1','Ink.Util.String_1'], function(Ajax, Aux, Event, Css, Element, Selector, InkArray, InkString ) {
    'use strict';

    /**
     * The Table component transforms the native/DOM table element into a
     * sortable, paginated component.
     * 
     * @class Ink.UI.Table
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @uses Ink.Util.Array
     * @uses Ink.UI.Pagination
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *     @param {Number}     options.pageSize       Number of rows per page.
     *     @param {String}     options.endpoint       Endpoint to get the records via AJAX
     * @example
     *      <table class="ink-table alternating" data-page-size="6">
     *          <thead>
     *              <tr>
     *                  <th data-sortable="true" width="75%">Pepper</th>
     *                  <th data-sortable="true" width="25%">Scoville Rating</th>
     *              </tr>
     *          </thead>
     *          <tbody>
     *              <tr>
     *                  <td>Trinidad Moruga Scorpion</td>
     *                  <td>1500000</td>
     *              </tr>
     *              <tr>
     *                  <td>Bhut Jolokia</td>
     *                  <td>1000000</td>
     *              </tr>
     *              <tr>
     *                  <td>Naga Viper</td>
     *                  <td>1463700</td>
     *              </tr>
     *              <tr>
     *                  <td>Red Savina Habanero</td>
     *                  <td>580000</td>
     *              </tr>
     *              <tr>
     *                  <td>Habanero</td>
     *                  <td>350000</td>
     *              </tr>
     *              <tr>
     *                  <td>Scotch Bonnet</td>
     *                  <td>180000</td>
     *              </tr>
     *              <tr>
     *                  <td>Malagueta</td>
     *                  <td>50000</td>
     *              </tr>
     *              <tr>
     *                  <td>Tabasco</td>
     *                  <td>35000</td>
     *              </tr>
     *              <tr>
     *                  <td>Serrano Chili</td>
     *                  <td>27000</td>
     *              </tr>
     *              <tr>
     *                  <td>Jalapeño</td>
     *                  <td>8000</td>
     *              </tr>
     *              <tr>
     *                  <td>Poblano</td>
     *                  <td>1500</td>
     *              </tr>
     *              <tr>
     *                  <td>Peperoncino</td>
     *                  <td>500</td>
     *              </tr>
     *          </tbody>
     *      </table>
     *      <nav class="ink-navigation"><ul class="pagination"></ul></nav>
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.Table_1'], function( Selector, Table ){
     *              var tableElement = Ink.s('.ink-table');
     *              var tableObj = new Table( tableElement );
     *          });
     *      </script>
     */
    var Table = function( selector, options ){

        /**
         * Get the root element
         */
        this._rootElement = Aux.elOrSelector(selector, '1st argument');

        if( this._rootElement.nodeName.toLowerCase() !== 'table' ){
            throw '[Ink.UI.Table] :: The element is not a table';
        }

        this._options = Ink.extendObj({
            pageSize: undefined,
            endpoint: undefined,
            loadMode: 'full',
            allowResetSorting: false,
            visibleFields: undefined
        },Element.data(this._rootElement));

        this._options = Ink.extendObj( this._options, options || {});

        /**
         * Checking if it's in markup mode or endpoint mode
         */
        this._markupMode = ( typeof this._options.endpoint === 'undefined' );

        if( !!this._options.visibleFields ){
            this._options.visibleFields = this._options.visibleFields.split(',');
        }

        /**
         * Initializing variables
         */
        this._handlers = {
            click: Ink.bindEvent(this._onClick,this)
        };
        this._originalFields = [];
        this._sortableFields = {};
        this._originalData = this._data = [];
        this._headers = [];
        this._pagination = null;
        this._totalRows = 0;

        this._init();
    };

    Table.prototype = {

        /**
         * Init function called by the constructor
         * 
         * @method _init
         * @private
         */
        _init: function(){

            /**
             * If not is in markup mode, we have to do the initial request
             * to get the first data and the headers
             */
             if( !this._markupMode ){
                this._getData( this._options.endpoint, true );
             } else{
                this._setHeadersHandlers();

                /**
                 * Getting the table's data
                 */
                InkArray.each(Selector.select('tbody tr',this._rootElement),Ink.bind(function(tr){
                    this._data.push(tr);
                },this));
                this._originalData = this._data.slice(0);

                this._totalRows = this._data.length;

                /**
                 * Set pagination if defined
                 * 
                 */
                if( ("pageSize" in this._options) && (typeof this._options.pageSize !== 'undefined') ){
                    /**
                     * Applying the pagination
                     */
                    this._pagination = this._rootElement.nextSibling;
                    while(this._pagination.nodeType !== 1){
                        this._pagination = this._pagination.nextSibling;
                    }

                    if( this._pagination.nodeName.toLowerCase() !== 'nav' ){
                        throw '[Ink.UI.Table] :: Missing the pagination markup or is mis-positioned';
                    }

                    var Pagination = Ink.getModule('Ink.UI.Pagination',1);

                    this._pagination = new Pagination( this._pagination, {
                        size: Math.ceil(this._totalRows/this._options.pageSize),
                        onChange: Ink.bind(function( pagingObj ){
                            this._paginate( (pagingObj._current+1) );
                        },this)
                    });

                    this._paginate(1);
                }
             }

        },

        /**
         * Click handler. This will mainly handle the sorting (when you click in the headers)
         * 
         * @method _onClick
         * @param {Event} event Event obj
         * @private
         */
        _onClick: function( event ){
            Event.stop(event);
            var
                tgtEl = Event.element(event),
                dataset = Element.data(tgtEl),
                index,i,
                paginated = ( ("pageSize" in this._options) && (typeof this._options.pageSize !== 'undefined') )
            ;
            if( (tgtEl.nodeName.toLowerCase() !== 'th') || ( !("sortable" in dataset) || (dataset.sortable.toString() !== 'true') ) ){
                return;
            }

            index = -1;
            if( InkArray.inArray( tgtEl,this._headers ) ){
                for( i=0; i<this._headers.length; i++ ){
                    if( this._headers[i] === tgtEl ){
                        index = i;
                        break;
                    }
                }
            }

            if( !this._markupMode && paginated ){

                for( var prop in this._sortableFields ){
                    if( prop !== ('col_' + index) ){
                        this._sortableFields[prop] = 'none';
                        this._headers[prop.replace('col_','')].innerHTML = InkString.stripTags(this._headers[prop.replace('col_','')].innerHTML);
                    }
                }

                if( this._sortableFields['col_'+index] === 'asc' )
                {
                    this._sortableFields['col_'+index] = 'desc';
                    this._headers[index].innerHTML = InkString.stripTags(this._headers[index].innerHTML) + '<i class="icon-caret-down"></i>';
                } else {
                    this._sortableFields['col_'+index] = 'asc';
                    this._headers[index].innerHTML = InkString.stripTags(this._headers[index].innerHTML) + '<i class="icon-caret-up"></i>';

                }

                this._pagination.setCurrent(this._pagination._current);

            } else {

                if( index === -1){
                    return;
                }

                if( (this._sortableFields['col_'+index] === 'desc') && (this._options.allowResetSorting && (this._options.allowResetSorting.toString() === 'true')) )
                {
                    this._headers[index].innerHTML = InkString.stripTags(this._headers[index].innerHTML);
                    this._sortableFields['col_'+index] = 'none';

                    // if( !found ){
                        this._data = this._originalData.slice(0);
                    // }
                } else {

                    for( var prop in this._sortableFields ){
                        if( prop !== ('col_' + index) ){
                            this._sortableFields[prop] = 'none';
                            this._headers[prop.replace('col_','')].innerHTML = InkString.stripTags(this._headers[prop.replace('col_','')].innerHTML);
                        }
                    }

                    this._sort(index);

                    if( this._sortableFields['col_'+index] === 'asc' )
                    {
                        this._data.reverse();
                        this._sortableFields['col_'+index] = 'desc';
                        this._headers[index].innerHTML = InkString.stripTags(this._headers[index].innerHTML) + '<i class="icon-caret-down"></i>';
                    } else {
                        this._sortableFields['col_'+index] = 'asc';
                        this._headers[index].innerHTML = InkString.stripTags(this._headers[index].innerHTML) + '<i class="icon-caret-up"></i>';

                    }
                }


                var tbody = Selector.select('tbody',this._rootElement)[0];
                Aux.cleanChildren(tbody);
                InkArray.each(this._data,function(item){
                    tbody.appendChild(item);
                });

                this._pagination.setCurrent(0);
                this._paginate(1);
            }
        },

        /**
         * Applies and/or changes the CSS classes in order to show the right columns
         * 
         * @method _paginate
         * @param {Number} page Current page
         * @private
         */
        _paginate: function( page ){
            InkArray.each(this._data,Ink.bind(function(item, index){
                if( (index >= ((page-1)*parseInt(this._options.pageSize,10))) && (index < (((page-1)*parseInt(this._options.pageSize,10))+parseInt(this._options.pageSize,10)) ) ){
                    Css.removeClassName(item,'hide-all');
                } else {
                    Css.addClassName(item,'hide-all');
                }
            },this));
        },

        /**
         * Sorts by a specific column.
         * 
         * @method _sort
         * @param {Number} index Column number (starting at 0)
         * @private
         */
        _sort: function( index ){
            this._data.sort(Ink.bind(function(a,b){
                var
                    aValue = Selector.select('td',a)[index].innerText,
                    bValue = Selector.select('td',b)[index].innerText
                ;

                var regex = new RegExp(/\d/g);
                if( !isNaN(aValue) && regex.test(aValue) ){
                    aValue = parseInt(aValue,10);
                } else if( !isNaN(aValue) ){
                    aValue = parseFloat(aValue);
                }

                if( !isNaN(bValue) && regex.test(bValue) ){
                    bValue = parseInt(bValue,10);
                } else if( !isNaN(bValue) ){
                    bValue = parseFloat(bValue);
                }

                if( aValue === bValue ){
                    return 0;
                } else {
                    return ( ( aValue>bValue ) ? 1 : -1 );
                }
            },this));
        },

        /**
         * Assembles the headers markup
         *
         * @method _setHeaders
         * @param  {Object} headers Key-value object that contains the fields as keys, their configuration (label and sorting ability) as value
         * @private
         */
        _setHeaders: function( headers, rows ){
            var
                field, header,
                thead, tr, th,
                index = 0
            ;

            if( (thead = Selector.select('thead',this._rootElement)).length === 0 ){
                thead = this._rootElement.createTHead();
                tr = thead.insertRow(0);

                for( field in headers ){

                    if( !!this._options.visibleFields && (this._options.visibleFields.indexOf(field) === -1) ){
                        continue;
                    }

                    // th = tr.insertCell(index++);
                    th = document.createElement('th');
                    header = headers[field];

                    if( ("sortable" in header) && (header.sortable.toString() === 'true') ){
                        th.setAttribute('data-sortable','true');
                    }

                    if( ("label" in header) ){
                        th.innerText = header.label;
                    }

                    this._originalFields.push(field);
                    tr.appendChild(th);
                }
            } else {
                var firstLine = rows[0];

                for( field in firstLine ){
                    if( !!this._options.visibleFields && (this._options.visibleFields.indexOf(field) === -1) ){
                        continue;
                    }

                    this._originalFields.push(field);
                }
            }
        },

        /**
         * Method that sets the handlers for the headers
         *
         * @method _setHeadersHandlers
         * @private
         */
        _setHeadersHandlers: function(){

            /**
             * Setting the sortable columns and its event listeners
             */
            Event.observe(Selector.select('thead',this._rootElement)[0],'click',this._handlers.click);
            this._headers = Selector.select('thead tr th',this._rootElement);
            InkArray.each(this._headers,Ink.bind(function(item, index){
                var dataset = Element.data( item );
                if( ('sortable' in dataset) && (dataset.sortable.toString() === 'true') ){
                    this._sortableFields['col_' + index] = 'none';
                }
            },this));

        },

        /**
         * This method gets the rows from AJAX and places them as <tr> and <td>
         *
         * @method _setData
         * @param  {Object} rows Array of objects with the data to be showed
         * @private
         */
        _setData: function( rows ){

            var
                field,
                tbody, tr, td,
                trIndex,
                tdIndex
            ;

            tbody = Selector.select('tbody',this._rootElement);
            if( tbody.length === 0){
                tbody = document.createElement('tbody');
                this._rootElement.appendChild( tbody );
            } else {
                tbody = tbody[0];
                tbody.innerHTML = '';
            }

            this._data = [];


            for( trIndex in rows ){
                tr = document.createElement('tr');
                tbody.appendChild( tr );
                tdIndex = 0;
                for( field in rows[trIndex] ){

                    if( !!this._options.visibleFields && (this._options.visibleFields.indexOf(field) === -1) ){
                        continue;
                    }

                    td = tr.insertCell(tdIndex++);
                    td.innerHTML = rows[trIndex][field];
                }
                this._data.push(tr);
            }

            this._originalData = this._data.slice(0);
        },

        /**
         * Sets the endpoint. Useful for changing the endpoint in runtime.
         *
         * @method _setEndpoint
         * @param {String} endpoint New endpoint
         */
        setEndpoint: function( endpoint, currentPage ){
            if( !this._markupMode ){
                this._options.endpoint = endpoint;
                this._pagination.setCurrent( (!!currentPage) ? parseInt(currentPage,10) : 0 );
            }
        },

        /**
         * Checks if it needs the pagination and creates the necessary markup to have pagination
         *
         * @method _setPagination
         * @private
         */
        _setPagination: function(){
            var paginated = ( ("pageSize" in this._options) && (typeof this._options.pageSize !== 'undefined') );
            /**
             * Set pagination if defined
             */
            if( ("pageSize" in this._options) && (typeof this._options.pageSize !== 'undefined') ){
                /**
                 * Applying the pagination
                 */
                if( !this._pagination ){
                    this._pagination = document.createElement('nav');
                    this._pagination.className = 'ink-navigation';
                    this._rootElement.parentNode.insertBefore(this._pagination,this._rootElement.nextSibling);
                    this._pagination.appendChild( document.createElement('ul') ).className = 'pagination';

                    var Pagination = Ink.getModule('Ink.UI.Pagination',1);

                    this._pagination = new Pagination( this._pagination, {
                        size: Math.ceil(this._totalRows/this._options.pageSize),
                        onChange: Ink.bind(function( ){
                            this._getData( this._options.endpoint );
                        },this)
                    }); 
                }
            }
        },

        /**
         * Method to choose which is the best way to get the data based on the endpoint:
         *     - AJAX
         *     - JSONP
         *
         * @method _getData
         * @param  {String} endpoint     Valid endpoint
         * @param  {Boolean} [firstRequest] If true, will make the request set the headers onSuccess
         * @private
         */
        _getData: function( endpoint ){

            Ink.requireModules(['Ink.Util.Url_1'],Ink.bind(function( InkURL ){

                var
                    parsedURL = InkURL.parseUrl( endpoint ),
                    paginated = ( ("pageSize" in this._options) && (typeof this._options.pageSize !== 'undefined') ),
                    pageNum = ((!!this._pagination) ? this._pagination._current+1 : 1)
                ;

                if( parsedURL.query ){
                    parsedURL.query = parsedURL.query.split("&");
                } else {
                    parsedURL.query = [];
                }

                if( !paginated ){            
                    this._getDataViaAjax( endpoint );
                } else {

                    parsedURL.query.push( 'rows_per_page=' + this._options.pageSize );
                    parsedURL.query.push( 'page=' + pageNum );

                    var sortStr = '';
                    for( var index in this._sortableFields ){
                        if( this._sortableFields[index] !== 'none' ){
                            parsedURL.query.push('sortField=' + this._originalFields[parseInt(index.replace('col_',''),10)]);
                            parsedURL.query.push('sortOrder=' + this._sortableFields[index]);
                            break;
                        }
                    }

                    this._getDataViaAjax( endpoint + '?' + parsedURL.query.join('&') );
                }

            },this));

        },

        /**
         * Gets the data via AJAX and triggers the changes in the 
         * 
         * @param  {[type]} endpoint     [description]
         * @param  {[type]} firstRequest [description]
         * @return {[type]}              [description]
         */
        _getDataViaAjax: function( endpoint ){

            var paginated = ( ("pageSize" in this._options) && (typeof this._options.pageSize !== 'undefined') );

            new Ajax( endpoint, {
                method: 'GET',
                contentType: 'application/json',
                sanitizeJSON: true,
                onSuccess: Ink.bind(function( response ){
                    if( response.status === 200 ){

                        var jsonResponse = JSON.parse( response.responseText );

                        if( this._headers.length === 0 ){
                            this._setHeaders( jsonResponse.headers, jsonResponse.rows );
                            this._setHeadersHandlers();
                        }

                        this._setData( jsonResponse.rows );

                        if( paginated ){
                            if( !!this._totalRows && (parseInt(jsonResponse.totalRows,10) !== parseInt(this._totalRows,10)) ){ 
                                this._totalRows = jsonResponse.totalRows;
                                this._pagination.setSize( Math.ceil(this._totalRows/this._options.pageSize) );
                            } else {
                                this._totalRows = jsonResponse.totalRows;
                            }
                        } else {
                            if( !!this._totalRows && (jsonResponse.rows.length !== parseInt(this._totalRows,10)) ){ 
                                this._totalRows = jsonResponse.rows.length;
                                this._pagination.setSize( Math.ceil(this._totalRows/this._options.pageSize) );
                            } else {
                                this._totalRows = jsonResponse.rows.length;
                            }
                        }

                        this._setPagination( );
                    }

                },this)
            } );
        }
    };

    return Table;

});

/**
 * @module Ink.UI.Tabs_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Tabs', '1', ['Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1'], function(Aux, Event, Css, Element, Selector, InkArray ) {
    'use strict';

    /**
     * Tabs component
     * 
     * @class Ink.UI.Tabs
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @uses Ink.Util.Array
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *     @param {Boolean}      [options.preventUrlChange]        Flag that determines if follows the link on click or stops the event
     *     @param {String}       [options.active]                  ID of the tab to activate on creation
     *     @param {Array}        [options.disabled]                IDs of the tabs that will be disabled on creation
     *     @param {Function}     [options.onBeforeChange]          callback to be executed before changing tabs
     *     @param {Function}     [options.onChange]                callback to be executed after changing tabs
     * @example
     *      <div class="ink-tabs top"> <!-- replace 'top' with 'bottom', 'left' or 'right' to place navigation -->
     *          
     *          <!-- put navigation first if using top, left or right positioning -->
     *          <ul class="tabs-nav">
     *              <li><a href="#home">Home</a></li>
     *              <li><a href="#news">News</a></li>
     *              <li><a href="#description">Description</a></li>
     *              <li><a href="#stuff">Stuff</a></li>
     *              <li><a href="#more_stuff">More stuff</a></li>
     *          </ul>
     *          
     *          <!-- Put your content second if using top, left or right navigation -->
     *          <div id="home" class="tabs-content"><p>Content</p></div>
     *          <div id="news" class="tabs-content"><p>Content</p></div>
     *          <div id="description" class="tabs-content"><p>Content</p></div>
     *          <div id="stuff" class="tabs-content"><p>Content</p></div>
     *          <div id="more_stuff" class="tabs-content"><p>Content</p></div>
     *          <!-- If you're using bottom navigation, switch the nav block with the content blocks -->
     *       
     *      </div>
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.Tabs_1'], function( Selector, Tabs ){
     *              var tabsElement = Ink.s('.ink-tabs');
     *              var tabsObj = new Tabs( tabsElement );
     *          });
     *      </script>
     */
    var Tabs = function(selector, options) {

        if (!Aux.isDOMElement(selector)) {
            selector = Selector.select(selector);
            if (selector.length === 0) { throw new TypeError('1st argument must either be a DOM Element or a selector expression!'); }
            this._element = selector[0];
        } else {
            this._element = selector;
        }


        this._options = Ink.extendObj({
            preventUrlChange: false,
            active: undefined,
            disabled: [],
            onBeforeChange: undefined,
            onChange: undefined
        }, Element.data(selector));

        this._options = Ink.extendObj(this._options,options || {});

        this._handlers = {
            tabClicked: Ink.bindEvent(this._onTabClicked,this),
            disabledTabClicked: Ink.bindEvent(this._onDisabledTabClicked,this),
            resize: Ink.bindEvent(this._onResize,this)
        };

        this._init();
    };

    Tabs.prototype = {

        /**
         * Init function called by the constructor
         * 
         * @method _init
         * @private
         */
        _init: function() {
            this._menu = Selector.select('.tabs-nav', this._element)[0];
            this._menuTabs = this._getChildElements(this._menu);
            this._contentTabs = Selector.select('.tabs-content', this._element);

            //initialization of the tabs, hides all content before setting the active tab
            this._initializeDom();

            // subscribe events
            this._observe();

            //sets the first active tab
            this._setFirstActive();

            //shows the active tab
            this._changeTab(this._activeMenuLink);

            this._handlers.resize();

            Aux.registerInstance(this, this._element, 'tabs');
        },

        /**
         * Initialization of the tabs, hides all content before setting the active tab
         * 
         * @method _initializeDom
         * @private
         */
        _initializeDom: function(){
            for(var i = 0; i < this._contentTabs.length; i++){
                Css.hide(this._contentTabs[i]);
            }
        },

        /**
         * Subscribe events
         * 
         * @method _observe
         * @private
         */
        _observe: function() {
            InkArray.each(this._menuTabs,Ink.bind(function(elem){
                var link = Selector.select('a', elem)[0];
                if(InkArray.inArray(link.getAttribute('href'), this._options.disabled)){
                    this.disable(link);
                } else {
                    this.enable(link);
                }
            },this));

            Event.observe(window, 'resize', this._handlers.resize);
        },

        /**
         * Run at instantiation, to determine which is the first active tab
         * fallsback from window.location.href to options.active to the first not disabled tab
         * 
         * @method _setFirstActive
         * @private
         */
        _setFirstActive: function() {
            var hash = window.location.hash;
            this._activeContentTab = Selector.select(hash, this._element)[0] ||
                                     Selector.select(this._hashify(this._options.active), this._element)[0] ||
                                     Selector.select('.tabs-content', this._element)[0];

            this._activeMenuLink = this._findLinkByHref(this._activeContentTab.getAttribute('id'));
            this._activeMenuTab = this._activeMenuLink.parentNode;
        },

        /**
         * Changes to the desired tab
         * 
         * @method _changeTab
         * @param {DOMElement} link             anchor linking to the content container
         * @param {boolean}    runCallbacks     defines if the callbacks should be run or not
         * @private
         */
        _changeTab: function(link, runCallbacks){
            if(runCallbacks && typeof this._options.onBeforeChange !== 'undefined'){
                this._options.onBeforeChange(this);
            }

            var selector = link.getAttribute('href');
            Css.removeClassName(this._activeMenuTab, 'active');
            Css.removeClassName(this._activeContentTab, 'active');
            Css.addClassName(this._activeContentTab, 'hide-all');

            this._activeMenuLink = link;
            this._activeMenuTab = this._activeMenuLink.parentNode;
            this._activeContentTab = Selector.select(selector.substr(selector.indexOf('#')), this._element)[0];

            Css.addClassName(this._activeMenuTab, 'active');
            Css.addClassName(this._activeContentTab, 'active');
            Css.removeClassName(this._activeContentTab, 'hide-all');
            Css.show(this._activeContentTab);

            if(runCallbacks && typeof(this._options.onChange) !== 'undefined'){
                this._options.onChange(this);
            }
        },

        /**
         * Tab clicked handler
         * 
         * @method _onTabClicked
         * @param {Event} ev
         * @private
         */
        _onTabClicked: function(ev) {
            Event.stop(ev);

            var target = Event.findElement(ev, 'A');
            if(target.nodeName.toLowerCase() !== 'a') {
                return;
            }

            if( this._options.preventUrlChange.toString() !== 'true'){
                window.location.hash = target.getAttribute('href').substr(target.getAttribute('href').indexOf('#'));
            }

            if(target === this._activeMenuLink){
                return;
            }
            this.changeTab(target);
        },

        /**
         * Disabled tab clicked handler
         * 
         * @method _onDisabledTabClicked
         * @param {Event} ev
         * @private
         */
        _onDisabledTabClicked: function(ev) {
            Event.stop(ev);
        },

        /**
         * Resize handler
         * 
         * @method _onResize
         * @private
         */
        _onResize: function(){
            var currentLayout = Aux.currentLayout();
            if(currentLayout === this._lastLayout){
                return;
            }

            if(currentLayout === Aux.Layouts.SMALL || currentLayout === Aux.Layouts.MEDIUM){
                Css.removeClassName(this._menu, 'menu');
                Css.removeClassName(this._menu, 'horizontal');
                // Css.addClassName(this._menu, 'pills');
            } else {
                Css.addClassName(this._menu, 'menu');
                Css.addClassName(this._menu, 'horizontal');
                // Css.removeClassName(this._menu, 'pills');
            }
            this._lastLayout = currentLayout;
        },

        /*****************
         * Aux Functions *
         *****************/

        /**
         * Allows the hash to be passed with or without the cardinal sign
         * 
         * @method _hashify
         * @param {String} hash     the string to be hashified
         * @return {String} Resulting hash
         * @private
         */
        _hashify: function(hash){
            if(!hash){
                return "";
            }
            return hash.indexOf('#') === 0? hash : '#' + hash;
        },

        /**
         * Returns the anchor with the desired href
         * 
         * @method _findLinkBuHref
         * @param {String} href     the href to be found on the returned link
         * @return {String|undefined} [description]
         * @private
         */
        _findLinkByHref: function(href){
            href = this._hashify(href);
            var ret;
            InkArray.each(this._menuTabs,Ink.bind(function(elem){
                var link = Selector.select('a', elem)[0];
                if( (link.getAttribute('href').indexOf('#') !== -1) && ( link.getAttribute('href').substr(link.getAttribute('href').indexOf('#')) === href ) ){
                    ret = link;
                }
            },this));
            return ret;
        },

        /**
         * Returns the child elements of a given parent element
         * 
         * @method _getChildElements
         * @param {DOMElement} parent  DOMElement to fetch the child elements from.
         * @return {Array}  Child elements of the given parent.
         * @private
         */
        _getChildElements: function(parent){
            var childNodes = [];
            var children = parent.children;
            for(var i = 0; i < children.length; i++){
                if(children[i].nodeType === 1){
                    childNodes.push(children[i]);
                }
            }
            return childNodes;
        },

        /**************
         * PUBLIC API *
         **************/

        /**
         * Changes to the desired tag
         * 
         * @method changeTab
         * @param {String|DOMElement} selector      the id of the desired tab or the link that links to it
         * @public
         */
        changeTab: function(selector) {
            var element = (selector.nodeType === 1)? selector : this._findLinkByHref(this._hashify(selector));
            if(!element || Css.hasClassName(element, 'ink-disabled')){
                return;
            }
            this._changeTab(element, true);
        },

        /**
         * Disables the desired tag
         * 
         * @method disable
         * @param {String|DOMElement} selector      the id of the desired tab or the link that links to it
         * @public
         */
        disable: function(selector){
            var element = (selector.nodeType === 1)? selector : this._findLinkByHref(this._hashify(selector));
            if(!element){
                return;
            }
            Event.stopObserving(element, 'click', this._handlers.tabClicked);
            Event.observe(element, 'click', this._handlers.disabledTabClicked);
            Css.addClassName(element, 'ink-disabled');
        },

         /**
         * Enables the desired tag
         * 
         * @method enable
         * @param {String|DOMElement} selector      the id of the desired tab or the link that links to it
         * @public
         */
        enable: function(selector){
            var element = (selector.nodeType === 1)? selector : this._findLinkByHref(this._hashify(selector));
            if(!element){
                return;
            }
            Event.stopObserving(element, 'click', this._handlers.disabledTabClicked);
            Event.observe(element, 'click', this._handlers.tabClicked);
            Css.removeClassName(element, 'ink-disabled');
        },

        /***********
         * Getters *
         ***********/

        /**
         * Returns the active tab id
         * 
         * @method activeTab
         * @return {String} ID of the active tab.
         * @public
         */
        activeTab: function(){
            return this._activeContentTab.getAttribute('id');
        },

        /**
         * Returns the current active Menu LI
         * 
         * @method activeMenuTab
         * @return {DOMElement} Active menu LI.
         * @public
         */
        activeMenuTab: function(){
            return this._activeMenuTab;
        },

        /**
         * Returns the current active Menu anchorChanges to the desired tag
         * 
         * @method activeMenuLink
         * @return {DOMElement} Active menu link
         * @public
         */
        activeMenuLink: function(){
            return this._activeMenuLink;
        },

        /**
         * Returns the current active Content Tab
         * 
         * @method activeContentTab
         * @return {DOMElement} Active Content Tab
         * @public
         */
        activeContentTab: function(){
            return this._activeContentTab;
        },

        /**
         * Unregisters the component and removes its markup from the DOM
         * 
         * @method destroy
         * @public
         */
        destroy: Aux.destroyComponent
    };

    return Tabs;

});

/**
 * @module Ink.UI.ImageQuery_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.ImageQuery', '1', ['Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1'], function(Aux, Event, Css, Element, Selector, InkArray ) {
    'use strict';

    /**
     * @class Ink.UI.ImageQuery
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @uses Ink.Util.Array
     *
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *      @param {String|Function}    [options.src]             String or Callback function (that returns a string) with the path to be used to get the images.
     *      @param {String|Function}    [options.retina]          String or Callback function (that returns a string) with the path to be used to get RETINA specific images.
     *      @param {Array}              [options.queries]         Array of queries
     *          @param {String}              [options.queries.label]         Label of the query. Ex. 'small'
     *          @param {Number}              [options.queries.width]         Min-width to use this query
     *      @param {Function}           [options.onLoad]          Date format string
     *
     * @example
     *      <div class="imageQueryExample large-100 medium-100 small-100 content-center clearfix vspace">
     *          <img src="/assets/imgs/imagequery/small/image.jpg" />
     *      </div>
     *      <script type="text/javascript">
     *      Ink.requireModules( ['Ink.Dom.Selector_1', 'Ink.UI.ImageQuery_1'], function( Selector, ImageQuery ){
     *          var imageQueryElement = Ink.s('.imageQueryExample img');
     *          var imageQueryObj = new ImageQuery('.imageQueryExample img',{
     *              src: '/assets/imgs/imagequery/{:label}/{:file}',
     *              queries: [
     *                  {
     *                      label: 'small',
     *                      width: 480
     *                  },
     *                  {
     *                      label: 'medium',
     *                      width: 640
     *                  },
     *                  {
     *                      label: 'large',
     *                      width: 1024
     *                  }   
     *              ]
     *          });
     *      } );
     *      </script>
     */
    var ImageQuery = function(selector, options){

        /**
         * Selector's type checking
         */
        if( !Aux.isDOMElement(selector) && (typeof selector !== 'string') ){
            throw '[ImageQuery] :: Invalid selector';
        } else if( typeof selector === 'string' ){
            this._element = Selector.select( selector );

            if( this._element.length < 1 ){
                throw '[ImageQuery] :: Selector has returned no elements';
            } else if( this._element.length > 1 ){
                var i;
                for( i=1;i<this._element.length;i+=1 ){
                    new Ink.UI.ImageQuery(this._element[i],options);
                }
            }
            this._element = this._element[0];

        } else {
            this._element = selector;
        }


        /**
         * Default options and they're overrided by data-attributes if any.
         * The parameters are:
         * @param {array} queries Array of objects that determine the label/name and its min-width to be applied.
         * @param {boolean} allowFirstLoad Boolean flag to allow the loading of the first element.
         */
        this._options = Ink.extendObj({
            queries:[],
            onLoad: null
        },Element.data(this._element));

        this._options = Ink.extendObj(this._options, options || {});

        /**
         * Determining the original basename (with the querystring) of the file.
         */
        var pos;
        if( (pos=this._element.src.lastIndexOf('?')) !== -1 ){
            var search = this._element.src.substr(pos);
            this._filename = this._element.src.replace(search,'').split('/').pop()+search;
        } else {
            this._filename = this._element.src.split('/').pop();
        }

        this._init();
    };

    ImageQuery.prototype = {

        /**
         * Init function called by the constructor
         * 
         * @method _init
         * @private
         */
        _init: function(){

            /**
             * Sort queries by width, in descendant order.
             */
            this._options.queries = InkArray.sortMulti(this._options.queries,'width').reverse();

            /**
             * Declaring the event handlers, in this case, the window.resize and the (element) load.
             * @type {Object}
             */
            this._handlers = {
                resize: Ink.bindEvent(this._onResize,this),
                load: Ink.bindEvent(this._onLoad,this)
            };

            if( typeof this._options.onLoad === 'function' ){
                Event.observe(this._element, 'onload', this._handlers.load);
            }

            Event.observe(window, 'resize', this._handlers.resize);

            // Imediate call to apply the right images based on the current viewport
            this._handlers.resize.call(this);

        },

        /**
         * Handles the resize event (as specified in the _init function)
         *
         * @method _onResize
         * @private
         */
        _onResize: function(){

            clearTimeout(timeout);

            var timeout = setTimeout(Ink.bind(function(){

                if( !this._options.queries || (this._options.queries === {}) ){
                    clearTimeout(timeout);
                    return;
                }

                var
                    query, selected,
                    viewportWidth
                ;

                /**
                 * Gets viewport width
                 */
                if( typeof( window.innerWidth ) === 'number' ) {
                   viewportWidth = window.innerWidth;
                } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
                   viewportWidth = document.documentElement.clientWidth;
                } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
                   viewportWidth = document.body.clientWidth;
                }

                /**
                 * Queries are in a descendant order. We want to find the query with the highest width that fits
                 * the viewport, therefore the first one.
                 */
                for( query=0; query < this._options.queries.length; query+=1 ){
                    if (this._options.queries[query].width <= viewportWidth){
                        selected = query;
                        break;
                    }
                }

                /**
                 * If it doesn't find any selectable query (because they don't meet the requirements)
                 * let's select the one with the smallest width
                 */
                if( typeof selected === 'undefined' ){ selected = this._options.queries.length-1; }

                /**
                 * Choosing the right src. The rule is:
                 *
                 *   "If there is specifically defined in the query object, use that. Otherwise uses the global src."
                 *
                 * The above rule applies to a retina src.
                 */
                var src = this._options.queries[selected].src || this._options.src;
                if ( ("devicePixelRatio" in window && window.devicePixelRatio>1) && ('retina' in this._options ) ) {
                    src = this._options.queries[selected].retina || this._options.retina;
                }

                /**
                 * Injects the file variable for usage in the 'templating system' below
                 */
                this._options.queries[selected].file = this._filename;

                /**
                 * Since we allow the src to be a callback, let's run it and get the results.
                 * For the inside, we're passing the element (img) being processed and the object of the selected
                 * query.
                 */
                if( typeof src === 'function' ){
                    src = src.apply(this,[this._element,this._options.queries[selected]]);
                    if( typeof src !== 'string' ){
                        throw '[ImageQuery] :: "src" callback does not return a string';
                    }
                }

                /**
                 * Replace the values of the existing properties on the query object (except src and retina) in the
                 * defined src and/or retina.
                 */
                var property;
                for( property in this._options.queries[selected] ){
                    if( ( property === 'src' ) || ( property === 'retina' ) ){ continue; }
                    src = src.replace("{:" + property + "}",this._options.queries[selected][property]);
                }
                this._element.src = src;

                // Removes the injected file property
                delete this._options.queries[selected].file;

                timeout = undefined;

            },this),300);
        },

        /**
         * Handles the element loading (img onload) event
         *
         * @method _onLoad
         * @private
         */
        _onLoad: function(){

            /**
             * Since we allow a callback for this let's run it.
             */
            this._options.onLoad.call(this);
        }

    };

    return ImageQuery;

});

/**
 * @module Ink.UI.TreeView_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.TreeView', '1', ['Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1'], function(Aux, Event, Css, Element, Selector, InkArray ) {
    'use strict';

    /**
     * TreeView is an Ink's component responsible for presenting a defined set of elements in a tree-like hierarchical structure
     * 
     * @class Ink.UI.TreeView
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @uses Ink.Util.Array
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *     @param {String} options.node        CSS selector that identifies the elements that are considered nodes.
     *     @param {String} options.child       CSS selector that identifies the elements that are children of those nodes.
     * @example
     *      <ul class="ink-tree-view">
     *        <li class="open"><span></span><a href="#">root</a>
     *          <ul>
     *            <li><a href="">child 1</a></li>
     *            <li><span></span><a href="">child 2</a>
     *              <ul>
     *                <li><a href="">grandchild 2a</a></li>
     *                <li><span></span><a href="">grandchild 2b</a>
     *                  <ul>
     *                    <li><a href="">grandgrandchild 1bA</a></li>
     *                    <li><a href="">grandgrandchild 1bB</a></li>
     *                  </ul>
     *                </li>
     *              </ul>
     *            </li>
     *            <li><a href="">child 3</a></li>
     *          </ul>
     *        </li>
     *      </ul>
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.TreeView_1'], function( Selector, TreeView ){
     *              var treeViewElement = Ink.s('.ink-tree-view');
     *              var treeViewObj = new TreeView( treeViewElement );
     *          });
     *      </script>
     */
    var TreeView = function(selector, options){

        /**
         * Gets the element
         */
        if( !Aux.isDOMElement(selector) && (typeof selector !== 'string') ){
            throw '[Ink.UI.TreeView] :: Invalid selector';
        } else if( typeof selector === 'string' ){
            this._element = Selector.select( selector );
            if( this._element.length < 1 ){
                throw '[Ink.UI.TreeView] :: Selector has returned no elements';
            }
            this._element = this._element[0];
        } else {
            this._element = selector;
        }

        /**
         * Default options and they're overrided by data-attributes if any.
         * The parameters are:
         * @param {string} node Selector to define which elements are seen as nodes. Default: li
         * @param {string} child Selector to define which elements are represented as childs. Default: ul
         */
        this._options = Ink.extendObj({
            node:   'li',
            child:  'ul'
        },Element.data(this._element));

        this._options = Ink.extendObj(this._options, options || {});

        this._init();
    };

    TreeView.prototype = {

        /**
         * Init function called by the constructor. Sets the necessary event handlers.
         * 
         * @method _init
         * @private
         */
        _init: function(){

            this._handlers = {
                click: Ink.bindEvent(this._onClick,this)
            };

            Event.observe(this._element, 'click', this._handlers.click);

            var
                nodes = Selector.select(this._options.node,this._element),
                children
            ;
            InkArray.each(nodes,Ink.bind(function(item){
                if( Css.hasClassName(item,'open') )
                {
                    return;
                }

                if( !Css.hasClassName(item, 'closed') ){
                    Css.addClassName(item,'closed');
                }

                children = Selector.select(this._options.child,item);
                InkArray.each(children,Ink.bind(function( inner_item ){
                    if( !Css.hasClassName(inner_item, 'hide-all') ){
                        Css.addClassName(inner_item,'hide-all');
                    }
                },this));
            },this));

        },

        /**
         * Handles the click event (as specified in the _init function).
         * 
         * @method _onClick
         * @param {Event} event
         * @private
         */
        _onClick: function(event){

            /**
             * Summary:
             * If the clicked element is a "node" as defined in the options, will check if it has any "child".
             * If so, will show it or hide it, depending on its current state. And will stop the event's default behavior.
             * If not, will execute the event's default behavior.
             *
             */
            var tgtEl = Event.element(event);

            if( this._options.node[0] === '.' ) {
                if( !Css.hasClassName(tgtEl,this._options.node.substr(1)) ){
                    while( (!Css.hasClassName(tgtEl,this._options.node.substr(1))) && (tgtEl.nodeName.toLowerCase() !== 'body') ){
                        tgtEl = tgtEl.parentNode;
                    }
                }
            } else if( this._options.node[0] === '#' ){
                if( tgtEl.id !== this._options.node.substr(1) ){
                    while( (tgtEl.id !== this._options.node.substr(1)) && (tgtEl.nodeName.toLowerCase() !== 'body') ){
                        tgtEl = tgtEl.parentNode;
                    }
                }
            } else {
                if( tgtEl.nodeName.toLowerCase() !== this._options.node ){
                    while( (tgtEl.nodeName.toLowerCase() !== this._options.node) && (tgtEl.nodeName.toLowerCase() !== 'body') ){
                        tgtEl = tgtEl.parentNode;
                    }
                }
            }

            if(tgtEl.nodeName.toLowerCase() === 'body'){ return; }

            var child = Selector.select(this._options.child,tgtEl);
            if( child.length > 0 ){
                Event.stop(event);
                child = child[0];
                if( Css.hasClassName(child,'hide-all') ){ Css.removeClassName(child,'hide-all'); Css.addClassName(tgtEl,'open'); Css.removeClassName(tgtEl,'closed'); }
                else { Css.addClassName(child,'hide-all'); Css.removeClassName(tgtEl,'open'); Css.addClassName(tgtEl,'closed'); }
            }

        }

    };

    return TreeView;

});

/**
 * @module Ink.UI.Gallery_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Gallery', '1', ['Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1','Ink.Util.Swipe_1'], function(Aux, Event, Css, Element, Selector, InkArray, Swipe ) {
    'use strict';
    
    /**
     * Function to calculate the size based on a given max. size and image size.
     * 
     * @function maximizeBox
     * @param  {Number} maxSz
     * @param  {Number} imageSz
     * @param  {Boolean} forceMaximize
     * @return {Number}
     * @private
     */
    var maximizeBox = function(maxSz, imageSz, forceMaximize) {
        var w = imageSz[0];
        var h = imageSz[1];

        if (forceMaximize || (w > maxSz[0] || h > maxSz[1]) ) {
            var arImg = w / h;
            var arMax = maxSz[0] / maxSz[1];
            var s = (arImg > arMax) ? maxSz[0] / w : maxSz[1] / h;
            return [parseInt(w * s + 0.5, 10), parseInt(h * s + 0.5, 10)];
        }

        return imageSz;
    };
    
    /**
     * @function maximizeBox
     * @param  {Object} o
     * @param  {Function} cb Callback function to run on each image loaded.
     * @private
     */
    var getDimsAsync = function(o, cb) {
        cb = Ink.bind(cb,o);

        var dims = [o.img.offsetWidth, o.img.offsetHeight];
        if (dims[0] && dims[1]) {
            cb(dims);
        }
        o.img.onload = Ink.bindEvent(function() {
            cb([this.img.offsetWidth, this.img.offsetHeight]);
        },o);
    };

    /**
     * @class Ink.UI.Gallery
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @uses Ink.Util.Array
     * @uses Ink.Util.Swipe
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options for the gallery
     *      @param {Number}   [options.fullImageMaxWidth]       Default value is 400.
     *      @param {Number}   [options.thumbnailMaxWidth]       Max. width of the thumbnail. Default value is 106.
     *      @param {Number}   [options.layout]                  This determines what layout the gallery will have. Numeric value between 0 and 3.
     *      @param {Boolean}  [options.circular]                Determines if the gallery behaves in a circular never ending cycle.
     *      @param {Boolean}  [options.fixImageSizes]           Specifies if the images should be forced to have the gallery size.
     * @example
     *     <ul class="ink-gallery-source">
     *         <li class="hentry hmedia">
     *             <a rel="enclosure" href="http://imgs.sapo.pt/ink/assets/imgs_gal/1.1.png">
     *                 <img alt="s1" src="http://imgs.sapo.pt/ink/assets/imgs_gal/thumb1.png">
     *             </a>
     *             <a class="bookmark" href="http://imgs.sapo.pt/ink/assets/imgs_gal/1.1.png">
     *                 <span class="entry-title">s1</span>
     *             </a>
     *             <span class="entry-content"><p>hello world 1</p></span>
     *         </li>
     *         <li class="hentry hmedia">
     *             <a rel="enclosure" href="http://imgs.sapo.pt/ink/assets/imgs_gal/1.2.png">
     *                 <img alt="s1" src="http://imgs.sapo.pt/ink/assets/imgs_gal/thumb2.png">
     *             </a>
     *             <a class="bookmark" href="http://imgs.sapo.pt/ink/assets/imgs_gal/1.2.png">
     *                 <span class="entry-title">s2</span>
     *             </a>
     *             <span class="entry-content"><p>hello world 2</p></span>
     *         </li>
     *     </ul>
     *     <script>
     *         Ink.requireModules(['Ink.Dom.Selector_1','Ink.UI.Gallery_1'],function( Selector, Gallery ){
     *             var galleryElement = Ink.s('ul.ink-gallery-source');
     *             var galleryObj = new Gallery( galleryElement );
     *         });
     *     </script>
     */
    var Gallery = function(selector, options) {

        this._element = Aux.elOrSelector(selector, '1st argument');

        this._options = Ink.extendObj({
            fullImageMaxWidth:   600,
            fullImageMaxHeight:  400,
            thumbnailMaxWidth:   106,
            layout:              0,
            circular:            false,
            fixImageSizes:       false
        }, Element.data(this._element));

        this._options = Ink.extendObj(this._options, options || {});

        this._handlers = {
            navClick:        Ink.bindEvent(this._onNavClick,this),
            paginationClick: Ink.bindEvent(this._onPaginationClick,this),
            thumbsClick:     Ink.bindEvent(this._onThumbsClick,this),
            focusBlur:       Ink.bindEvent(this._onFocusBlur,this),
            keyDown:         Ink.bindEvent(this._onKeyDown,this)
        };

        this._isFocused = false;
        this._model = [];

        if (this._options.model instanceof Array) {
            this._model = this._options.model;
            this._createdFrom = 'JSON';
        }
        else if (this._element.nodeName.toLowerCase() === 'ul') {
            this._createdFrom = 'DOM';
        }
        else {
            throw new TypeError('You must pass a selector expression/DOM element as 1st option or provide a model on 2nd argument!');
        }

        this._index      = 0;
        this._thumbIndex = 0;

        if( !isNaN(this._options.layout) ){

            this._options.layout = parseInt(this._options.layout,10);
            if (this._options.layout === 0) {
                this._showThumbs            = false;
                this._showDescription       = false;
                this._paginationHasPrevNext = false;
            }
            else if (this._options.layout === 1 || this._options.layout === 2 || this._options.layout === 3) {
                this._showThumbs            = true;
                this._showDescription       = true;
                this._paginationHasPrevNext = true;
            }
            else {
                throw new TypeError('supported layouts are 0-3!');
            }
        }

        if (this._element.getAttribute('data-fix-image-sizes') !== null) {
            this._options.fixImageSizes = true;
        }

        this._init();
    };

    Gallery.prototype = {

        /**
         * Init function called from the constructor.
         *
         * @method  _init
         * @private
         */
        _init: function() {
            // extract model
            if (this._createdFrom === 'DOM') {
                this._extractModelFromDOM();
            }

            // generate and apply DOM
            var el = this._generateMarkup();
            var parentEl = this._element.parentNode;

            if (!this._notFirstInit) {
                Aux.storeIdAndClasses(this._element, this);
                this._notFirstInit = true;
            }

            parentEl.insertBefore(el, this._element);
            parentEl.removeChild(this._element);
            this._element = el;

            Aux.restoreIdAndClasses(this._element, this);

            // subscribe events
            Event.observe(this._paginationEl, 'click',     this._handlers.paginationClick);
            Event.observe(this._navEl,        'click',     this._handlers.navClick);

            if (this._showThumbs) {
                Event.observe(this._thumbsUlEl,   'click',     this._handlers.thumbsClick);
            }

            Event.observe(this._element,      'mouseover', this._handlers.focusBlur);
            Event.observe(this._element,      'mouseout',  this._handlers.focusBlur);
            Event.observe(document,           'keydown',   this._handlers.keyDown);

            Aux.registerInstance(this, this._element, 'gallery');
        },

        /**
         * Updates the model from the UL representation
         *
         * @method _extractModelFromDOM
         * @private
         */
        _extractModelFromDOM: function() {
            /*global console:false */
            var m = [];
            var dims;

            var liEls = Selector.select('> li', this._element);
            InkArray.each(liEls,function(liEl) {
                try {
                    var d = {
                        image_full:  '',
                        image_thumb: '',
                        title_text:  '',
                        title_link:  '',
                        description: '',
                        content_overlay: document.createDocumentFragment()
                    };

                    var enclosureAEl       = Selector.select('> a[rel=enclosure]',          liEl)[0];
                    var thumbImgEl         = Selector.select('> img',                       enclosureAEl)[0];
                    var bookmarkAEl        = Selector.select('> a[class=bookmark]',         liEl)[0];
                    var titleSpanEl        = Selector.select('span[class=entry-title]',     liEl)[0];
                    var entryContentSpanEl = Selector.select('> span[class=entry-content]', liEl)[0];
                    var contentOverlayEl   = Selector.select('> .content-overlay',          liEl)[0];

                    dims = enclosureAEl.getAttribute('data-dims');
                    if (dims !== null) {
                        dims = dims.split(',');
                        dims[0] = parseInt(dims[0], 10);
                        dims[1] = parseInt(dims[1], 10);
                    }
                    if (dims && !isNaN(dims[0]) && !isNaN(dims[1])) { d.dims = dims; }

                    d.image_full  = enclosureAEl.getAttribute('href');
                    d.image_thumb = thumbImgEl.getAttribute('src');
                    if (bookmarkAEl) {
                        d.title_link  = bookmarkAEl.getAttribute('href');
                    }
                    d.title_text  = titleSpanEl.innerHTML;
                    if (entryContentSpanEl) {
                        d.description = entryContentSpanEl.innerHTML;
                    }

                    if(contentOverlayEl){
                        d.content_overlay.appendChild(contentOverlayEl);
                    }

                    m.push(d);
                } catch(ex) {
                    console.error('problematic element:');
                    console.error(liEl);
                    console.error(ex);
                    throw new Error('Problem parsing gallery data from DOM!');
                }
            });

            this._model = m;
        },

        /**
         * Returns the top element for the gallery DOM representation
         *
         * @method _generateMarkup
         * @private
         * @return {DOMElement} Returns the Gallery element totally rendered.
         */
        _generateMarkup: function() {
            /*jshint maxstatements:80 */
            var el = document.createElement('div');
            el.className = 'ink-gallery';

            var stageEl = document.createElement('div');
            stageEl.className = 'stage';

            // nav
            var navEl = document.createElement('nav');
            navEl.innerHTML = [
                '<ul class="unstyled">',
                    '<li><a href="#" class="next"></a></li>',
                    '<li><a href="#" class="previous"></a></li>',
                '</ul>'
            ].join('');
            this._navEl = navEl;

            // slider
            var sliderEl = document.createElement('div');
            sliderEl.className = 'slider';

            var ulEl = document.createElement('ul');
            this._sliderUlEl = ulEl;

            var that = this;

            var W = this._options.fullImageMaxWidth;
            var H = this._options.fullImageMaxHeight;

            InkArray.each(this._model,function(d, i) {
                var liEl = document.createElement('li');
                var imgEl = document.createElement('img');
                imgEl.setAttribute('name', 'image ' + (i + 1));
                imgEl.setAttribute('src',  d.image_full);
                imgEl.setAttribute('alt',  d.title_text);
                //imgEl.style.maxWidth = that._options.fullImageMaxWidth + 'px';
                //imgEl.setAttribute('width', that._options.fullImageMaxWidth);       // TODO?
                liEl.appendChild(imgEl);

                if(d.content_overlay){
                    if(d.content_overlay.nodeType === 1 || d.content_overlay.nodeType === 11){
                        d.content_overlay = liEl.appendChild(d.content_overlay);
                    } else if(typeof d.content_overlay === 'string'){
                        var contentOverlayEl = document.createElement('div');

                        contentOverlayEl.className = 'content-overlay';
                        contentOverlayEl.innerHTML = d.content_overlay;

                        d.content_overlay = liEl.appendChild(contentOverlayEl);
                    }
                }

                ulEl.appendChild(liEl);

                if (that._options.fixImageSizes) {
                    var dimsCb = function(dims) {
                        //console.log(this, dims);
                        var imgEl = this.img;
                        var data  = this.data;

                        if (!data.dims) { data.dims = dims; }

                        var dims2 = maximizeBox([W, H], dims);

                        var w = dims2[0];
                        var h = dims2[1];
                        var dw = Math.floor( (W - w)/2 );
                        var dh = Math.floor( (H - h)/2 );

                        if (w !== W || h !== H) {
                            imgEl.setAttribute('width',  w);
                            imgEl.setAttribute('height', h);

                            var s = imgEl.style;
                            if (dw > 0) { s.paddingLeft   = dw + 'px'; }
                            if (dh > 0) { s.paddingBottom = dh + 'px'; }
                        }
                    };

                    if (d.dims) { dimsCb.call( {img:imgEl, data:d}, d.dims); }
                    else {        getDimsAsync({img:imgEl, data:d}, dimsCb); }
                }
            });

            sliderEl.appendChild(ulEl);
            this._sliderEl = sliderEl;

            // description
            var articleTextDivEl;
            if (this._showDescription) {
                var d = this._model[this._index];
                articleTextDivEl = document.createElement('div');
                articleTextDivEl.className = ['article_text', 'example' + (this._options.layout === 3 ? 2 : this._options.layout)].join(' ');
                if (d.title_link) {
                    articleTextDivEl.innerHTML = ['<h1><a href="', d.title_link, '">', d.title_text, '</a></h1>', d.description].join('');
                }
                else {
                    articleTextDivEl.innerHTML = ['<h1>', d.title_text, '</h1>', d.description].join('');
                }
                this._articleTextDivEl = articleTextDivEl;
            }

            // thumbs
            var thumbsDivEl;
            if (this._showThumbs) {
                thumbsDivEl = document.createElement('div');
                thumbsDivEl.className = 'thumbs';
                ulEl = document.createElement('ul');
                ulEl.className = 'unstyled';

                InkArray.each(this._model,function(d, i) {
                    var liEl = document.createElement('li');
                    var aEl = document.createElement('a');
                    aEl.setAttribute('href', '#');
                    var imgEl = document.createElement('img');
                    imgEl.setAttribute('name', 'thumb ' + (i + 1));
                    imgEl.setAttribute('src', d.image_thumb);
                    imgEl.setAttribute('alt', (i + 1));
                    var spanEl = document.createElement('span');
                    spanEl.innerHTML = d.title_text;
                    aEl.appendChild(imgEl);
                    aEl.appendChild(spanEl);
                    liEl.appendChild(aEl);
                    ulEl.appendChild(liEl);
                });
                thumbsDivEl.appendChild(ulEl);

                this._thumbsDivEl = thumbsDivEl;
                this._thumbsUlEl = ulEl;
            }


            // pagination
            var paginationEl = document.createElement('div');
            paginationEl.className = 'pagination';

            var aEl;
            if (this._paginationHasPrevNext) {
                aEl = document.createElement('a');
                aEl.setAttribute('href', '#');
                aEl.className = 'previous';
                paginationEl.appendChild(aEl);
            }

            InkArray.each(this._model,function(d, i) {
                var aEl = document.createElement('a');
                aEl.setAttribute('href', '#');
                aEl.setAttribute('data-index', i);
                if (i === that._index) { aEl.className = 'active'; }
                paginationEl.appendChild(aEl);
            });

            if (this._paginationHasPrevNext) {
                aEl = document.createElement('a');
                aEl.setAttribute('href', '#');
                aEl.className = 'next';
                paginationEl.appendChild(aEl);
            }

            this._paginationEl = paginationEl;

            // last appends...
            if (this._options.layout === 0) {
                stageEl.appendChild(navEl);
                stageEl.appendChild(sliderEl);
                stageEl.appendChild(paginationEl);
                el.appendChild(stageEl);
            }
            else if (this._options.layout === 1 || this._options.layout === 2 || this._options.layout === 3) {
                stageEl.appendChild(navEl);
                stageEl.appendChild(sliderEl);
                stageEl.appendChild(articleTextDivEl);
                el.appendChild(stageEl);

                if (this._options.layout === 3) {
                    //this._thumbsUlEl.appendChild(paginationEl);
                    this._thumbsUlEl.className = 'thumbs unstyled';
                    Css.addClassName(el, 'rightNav');
                    el.appendChild(this._thumbsUlEl);
                }
                else {
                    thumbsDivEl.appendChild(paginationEl);
                    el.appendChild(thumbsDivEl);
                }
            }

            this._swipeDir = 'x';
            this._swipeThumbsDir = this._options.layout === 0 ? '' : (this._options.layout === 3 ? 'y' : 'x');

            if (Swipe._supported) {
                new Swipe(el, {
                    callback:    Ink.bind(function(sw, o) {
                        var th =              this._isMeOrParent(o.target, this._thumbsUlEl);
                        var sl = th ? false : this._isMeOrParent(o.target, el);//this._sliderUlEl);
                        if ( (!th && !sl) || (th && !this._swipeThumbsDir) ) { return; }
                        if ( (sl && o.axis !== this._swipeDir) || (th && o.axis !== this._swipeThumbsDir) ) { return; }
                        if (o.dr[0] < 0) { if (th) { this.thumbNext();     } else { this.next();     } }
                        else {             if (th) { this.thumbPrevious(); } else { this.previous(); } }
                    },this),
                    maxDuration: 0.4,
                    minDist:     50
                });
            }

            return el;
        },

        /**
         * Verifies if a given element is equals to its parent
         *
         * @method _isMeOrParent
         * @param  {DOMElement}  el       Element to be compared with the parent element
         * @param  {DOMElement}  parentEl Parent element to be compared with the element
         * @return {Boolean|undefined}          In case the 'el' variable is not defined, it returns undefined. Otherwise, it will return true or false depending on the comparison.
         * @private
         */
        _isMeOrParent: function(el, parentEl) {
            if (!el) {return;}
            do {
                if (el === parentEl) { return true; }
                el = el.parentNode;
            } while (el);
            return false;
        },

        /**
         * Navigation click handler
         *
         * @method _onNavClick
         * @param {Event} ev
         * @private
         */
        _onNavClick: function(ev) {
            var tgtEl = Event.element(ev);
            var delta;
            if      (Css.hasClassName(tgtEl, 'previous')) { delta = -1; }
            else if (Css.hasClassName(tgtEl, 'next')) {     delta =  1; }
            else { return; }

            Event.stop(ev);
            this.goTo(delta, true);
        },

        /**
         * Pagination click handler
         *
         * @method _onPaginationClick
         * @param {Event} ev
         * @private
         */
        _onPaginationClick: function(ev) {
            var tgtEl = Event.element(ev);
            var i = tgtEl.getAttribute('data-index');
            var isRelative = false;
            if      (Css.hasClassName(tgtEl, 'previous')) { i = -1; isRelative = true; }
            else if (Css.hasClassName(tgtEl, 'next')) {     i =  1; isRelative = true; }
            else if (i === null) { return; }
            else { i = parseInt(i, 10); }
            Event.stop(ev);

            if (isRelative) { this.thumbGoTo(i, true); }
            else {            this.goTo(i);            }
        },

        /**
         * Thumbs click handler
         *
         * @method _onThumbsClick
         * @param {Event} ev
         * @private
         */
        _onThumbsClick: function(ev) {
            var tgtEl = Event.element(ev);
            if      (tgtEl.nodeName.toLowerCase() === 'img') {}
            else if (tgtEl.nodeName.toLowerCase() === 'span') {
                tgtEl = Selector.select('> img', tgtEl.parentNode)[0];
            }
            else { return; }

            Event.stop(ev);
            var i = parseInt(tgtEl.getAttribute('alt'), 10) - 1;
            this.goTo(i);
        },

        /**
         * Focus handler
         *
         * @method _onFocusBlur
         * @param  {Event} ev
         * @private
         */
        _onFocusBlur: function(ev) {
            this._isFocused = (ev.type === 'mouseover');
        },

        /**
         * Key handler
         *
         * @method _onKeyDown
         * @param  {Event} ev
         * @private
         */
        _onKeyDown: function(ev) {
            if (!this._isFocused) { return; }
            var kc = ev.keyCode;
            if      (kc === 37) { this.previous(); }
            else if (kc === 39) { this.next();     }
            else { return; }
            Event.stop(ev);
        },

        /**
         * Validates the number of the item against the gallery items.
         *
         * @method _validateValue
         * @param  {Number}  i  The number of the item being validated
         * @param  {Boolean} [isRelative]
         * @param  {Boolean} [isThumb]
         * @return {Number|Boolean}
         * @private
         */
        _validateValue: function(i, isRelative, isThumb) {
            // check arguments
            if (!Aux.isInteger(i)) {
                throw new TypeError('1st parameter must be an integer number!');
            }
            if ( isRelative !== undefined &&
                 isRelative !== false     &&
                 isRelative !== true ) {
                throw new TypeError('2nd parameter must either be boolean or ommitted!');
            }

            var val = isThumb ? this._thumbIndex : this._index;

            // compute new index
            if (isRelative) { i += val; }

            if (this._options.circular) {
                if      (i < 0) {                   i = this._model.length - 1; }
                else if (i >= this._model.length) { i = 0;                      }
            }
            else {
                if (i < 0 || i >= this._model.length || i === val) { return false; }
            }

            return i;
        },



        /**************
         * PUBLIC API *
         **************/

        /**
         * Returns the index of the current image
         *
         * @method getIndex
         * @return {Number} Index of the current image
         * @public
         */
        getIndex: function() {
            return this._index;
        },

        /**
         * Returns the number of images in the gallery
         *
         * @method getLength
         * @return {Number} Number of images in the gallery
         * @public
         */
        getLength: function() {
            return this._model.length;
        },

        /**
         * Moves gallery to the nth - 1 image
         *
         * @method goTo
         * @param  {Number} i Absolute or relative index
         * @param  {Boolean} [isRelative] pass true for relative movement, otherwise absolute
         * @public
         */
        goTo: function(i, isRelative) {
            i = this._validateValue(i, isRelative, false);
            if (i === false) { return; }
            this._index = i;

            // update DOM representation
            var paginationAEls = Selector.select('> a', this._paginationEl);
            var that = this;
            InkArray.each(paginationAEls,function(aEl, i) {
                Css.setClassName(aEl, 'active', (i - (that._paginationHasPrevNext ? 1 : 0)) === that._index);
            });

            this._sliderUlEl.style.marginLeft = ['-', this._options.fullImageMaxWidth * this._index, 'px'].join('');

            if (this._showDescription) {
                var d = this._model[this._index];
                if (d.title_link) {
                    this._articleTextDivEl.innerHTML = ['<h1><a href="', d.title_link, '">', d.title_text, '</a></h1>', d.description].join('');
                }
                else {
                    this._articleTextDivEl.innerHTML = ['<h1>', d.title_text, '</h1>', d.description].join('');
                }
            }
        },

        /**
         * Moves gallery to the nth - 1 thumb
         *
         * @method thumbGoTo
         * @param  {Number} i Absolute or relative index
         * @param  {Boolean} [isRelative] pass true for relative movement, otherwise absolute
         * @public
         */
        thumbGoTo: function(i, isRelative) {
            i = this._validateValue(i, isRelative, true);
            if (i === false) { return; }
            this._thumbIndex = i;

            // update DOM representation
            var prop = 'margin' + (this._swipeThumbsDir === 'x' ? 'Left' : 'Top');
            this._thumbsUlEl.style[prop] = ['-', this._options.thumbnailMaxWidth * this._thumbIndex, 'px'].join('');
        },

        /**
         * Move to the previous image
         *
         * @method previous
         * @public
         */
        previous: function() {
            this.goTo(-1, true);
        },

        /**
         * Move to the next image
         *
         * @method next
         * @public
         */
        next: function() {
            this.goTo(1, true);
        },

        /**
         * Move to the previous thumb
         *
         * @method thumbPrevious
         * @public
         */
        thumbPrevious: function() {
            this.thumbGoTo(-1, true);
        },

        /**
         * Move to the next thumb
         *
         * @method thumbNext
         * @public
         */
        thumbNext: function() {
            this.thumbGoTo(1, true);
        },

        /**
         * Unregisters the component and removes its markup from the DOM
         *
         * @method destroy
         * @public
         */
        destroy: Aux.destroyComponent

    };

    return Gallery;

});

/**
 * @module Ink.UI.FormValidator_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.FormValidator', '1', ['Ink.Dom.Css_1','Ink.Util.Validator_1'], function( Css, InkValidator ) {
    'use strict';

    /**
     * @class Ink.UI.FormValidator
     * @version 1
     * @uses Ink.Dom.Css
     * @uses Ink.Util.Validator
     */
    var FormValidator = {

        /**
         * Specifies the version of the component
         *
         * @property version
         * @type {String}
         * @readOnly
         * @public
         */
        version: '1',

        /**
         * Available flags to use in the validation process.
         * The keys are the 'rules', and their values are objects with the key 'msg', determining
         * what is the error message.
         *
         * @property _flagMap
         * @type {Object}
         * @readOnly
         * @private
         */
        _flagMap: {
            //'ink-fv-required': {msg: 'Campo obrigat&oacute;rio'},
            'ink-fv-required': {msg: 'Required field'},
            //'ink-fv-email': {msg: 'E-mail inv&aacute;lido'},
            'ink-fv-email': {msg: 'Invalid e-mail address'},
            //'ink-fv-url': {msg: 'URL inv&aacute;lido'},
            'ink-fv-url': {msg: 'Invalid URL'},
            //'ink-fv-number': {msg: 'N&uacute;mero inv&aacute;lido'},
            'ink-fv-number': {msg: 'Invalid number'},
            //'ink-fv-phone_pt': {msg: 'N&uacute;mero de telefone inv&aacute;lido'},
            'ink-fv-phone_pt': {msg: 'Invalid phone number'},
            //'ink-fv-phone_cv': {msg: 'N&uacute;mero de telefone inv&aacute;lido'},
            'ink-fv-phone_cv': {msg: 'Invalid phone number'},
            //'ink-fv-phone_mz': {msg: 'N&uacute;mero de telefone inv&aacute;lido'},
            'ink-fv-phone_mz': {msg: 'Invalid phone number'},
            //'ink-fv-phone_ao': {msg: 'N&uacute;mero de telefone inv&aacute;lido'},
            'ink-fv-phone_ao': {msg: 'Invalid phone number'},
            //'ink-fv-date': {msg: 'Data inv&aacute;lida'},
            'ink-fv-date': {msg: 'Invalid date'},
            //'ink-fv-confirm': {msg: 'Confirma&ccedil;&atilde;o inv&aacute;lida'},
            'ink-fv-confirm': {msg: 'Confirmation does not match'},
            'ink-fv-custom': {msg: ''}
        },

        /**
         * This property holds all form elements for later validation
         *
         * @property elements
         * @type {Object}
         * @public
         */
        elements: {},

        /**
         * This property holds the objects needed to cross-check for the 'confirm' rule
         *
         * @property confirmElms
         * @type {Object}
         * @public
         */
        confirmElms: {},

        /**
         * This property holds the previous elements in the confirmElms property, but with a
         * true/false specifying if it has the class ink-fv-confirm.
         *
         * @property hasConfirm
         * @type {Object}
         */
        hasConfirm: {},

        /**
         * Defined class name to use in error messages label
         *
         * @property _errorClassName
         * @type {String}
         * @readOnly
         * @private
         */
        _errorClassName: 'tip',

        /**
         * @property _errorValidationClassName
         * @type {String}
         * @readOnly
         * @private
         */
        _errorValidationClassName: 'validaton',

        /**
         * @property _errorTypeWarningClassName
         * @type {String}
         * @readOnly
         * @private
         */
        _errorTypeWarningClassName: 'warning',

        /**
         * @property _errorTypeErrorClassName
         * @type {String}
         * @readOnly
         * @private
         */
        _errorTypeErrorClassName: 'error',

        /**
         * Check if a form is valid or not
         * 
         * @method validate
         * @param {DOMElement|String} elm DOM form element or form id
         * @param {Object} options Options for
         *      @param {Function} [options.onSuccess] function to run when form is valid
         *      @param {Function} [options.onError] function to run when form is not valid
         *      @param {Array} [options.customFlag] custom flags to use to validate form fields
         * @public
         * @return {Boolean}
         */
        validate: function(elm, options)
        {
            this._free();

            options = Ink.extendObj({
                onSuccess: false,
                onError: false,
                customFlag: false,
                confirmGroup: []
            }, options || {});

            if(typeof(elm) === 'string') {
                elm = document.getElementById(elm);
            }
            if(elm === null){
                return false;
            }
            this.element = elm;

            if(typeof(this.element.id) === 'undefined' || this.element.id === null || this.element.id === '') {
                // generate a random ID
                this.element.id = 'ink-fv_randomid_'+(Math.round(Math.random() * 99999));
            }

            this.custom = options.customFlag;

            this.confirmGroup = options.confirmGroup;

            var fail = this._validateElements();

            if(fail.length > 0) {
                if(options.onError) {
                    options.onError(fail);
                } else {
                    this._showError(elm, fail);
                }
                return false;
            } else {
                if(!options.onError) {
                    this._clearError(elm);
                }
                this._clearCache();
                if(options.onSuccess) {
                    options.onSuccess();
                }
                return true;
            }

        },

        /**
         * Reset previously generated validation errors
         * 
         * @method reset
         * @public
         */
        reset: function()
        {
            this._clearError();
            this._clearCache();
        },

        /**
         * Cleans the object
         * 
         * @method _free
         * @private
         */
        _free: function()
        {
            this.element = null;
            //this.elements = [];
            this.custom = false;
            this.confirmGroup = false;
        },

        /**
         * Cleans the properties responsible for caching
         * 
         * @method _clearCache
         * @private
         */
        _clearCache: function()
        {
            this.element = null;
            this.elements = [];
            this.custom = false;
            this.confirmGroup = false;
        },

        /**
         * Gets the form elements and stores them in the caching properties
         * 
         * @method _getElements
         * @private
         */
        _getElements: function()
        {
            //this.elements = [];
            // if(typeof(this.elements[this.element.id]) !== 'undefined') {
            //     return;
            // }

            this.elements[this.element.id] = [];
            this.confirmElms[this.element.id] = [];
            //console.log(this.element);
            //console.log(this.element.elements);
            var formElms = this.element.elements;
            var curElm = false;
            for(var i=0, totalElm = formElms.length; i < totalElm; i++) {
                curElm = formElms[i];

                if(curElm.getAttribute('type') !== null && curElm.getAttribute('type').toLowerCase() === 'radio') {
                    if(this.elements[this.element.id].length === 0 ||
                            (
                             curElm.getAttribute('type') !== this.elements[this.element.id][(this.elements[this.element.id].length - 1)].getAttribute('type') &&
                            curElm.getAttribute('name') !== this.elements[this.element.id][(this.elements[this.element.id].length - 1)].getAttribute('name')
                            )) {
                        for(var flag in this._flagMap) {
                            if(Css.hasClassName(curElm, flag)) {
                                this.elements[this.element.id].push(curElm);
                                break;
                            }
                        }
                    }
                } else {
                    for(var flag2 in this._flagMap) {
                        if(Css.hasClassName(curElm, flag2) && flag2 !== 'ink-fv-confirm') {
                            /*if(flag2 == 'ink-fv-confirm') {
                                this.confirmElms[this.element.id].push(curElm);
                                this.hasConfirm[this.element.id] = true;
                            }*/
                            this.elements[this.element.id].push(curElm);
                            break;
                        }
                    }

                    if(Css.hasClassName(curElm, 'ink-fv-confirm')) {
                        this.confirmElms[this.element.id].push(curElm);
                        this.hasConfirm[this.element.id] = true;
                    }

                }
            }
            //debugger;
        },

        /**
         * Runs the validation for each element
         * 
         * @method _validateElements
         * @private
         */
        _validateElements: function()
        {
            var oGroups;
            this._getElements();
            //console.log('HAS CONFIRM', this.hasConfirm);
            if(typeof(this.hasConfirm[this.element.id]) !== 'undefined' && this.hasConfirm[this.element.id] === true) {
                oGroups = this._makeConfirmGroups();
            }

            var errors = [];

            var curElm = false;
            var customErrors = false;
            var inArray;
            for(var i=0, totalElm = this.elements[this.element.id].length; i < totalElm; i++) {
                inArray = false;
                curElm = this.elements[this.element.id][i];

                if(!curElm.disabled) {
                    for(var flag in this._flagMap) {
                        if(Css.hasClassName(curElm, flag)) {

                            if(flag !== 'ink-fv-custom' && flag !== 'ink-fv-confirm') {
                                if(!this._isValid(curElm, flag)) {

                                    if(!inArray) {
                                        errors.push({elm: curElm, errors:[flag]});
                                        inArray = true;
                                    } else {
                                        errors[(errors.length - 1)].errors.push(flag);
                                    }
                                }
                            } else if(flag !== 'ink-fv-confirm'){
                                customErrors = this._isCustomValid(curElm);
                                if(customErrors.length > 0) {
                                    errors.push({elm: curElm, errors:[flag], custom: customErrors});
                                }
                            } else if(flag === 'ink-fv-confirm'){
                            }
                        }
                    }
                }
            }
            errors = this._validateConfirmGroups(oGroups, errors);
            //console.log(InkDumper.returnDump(errors));
            return errors;
        },

        /**
         * Runs the 'confirm' validation for each group of elements
         * 
         * @method _validateConfirmGroups
         * @param {Array} oGroups Array/Object that contains the group of confirm objects
         * @param {Array} errors Array that will store the errors
         * @private
         * @return {Array} Array of errors that was passed as 2nd parameter (either changed, or not, depending if errors were found).
         */
        _validateConfirmGroups: function(oGroups, errors)
        {
            //console.log(oGroups);
            var curGroup = false;
            for(var i in oGroups) {
                curGroup = oGroups[i];
                if(curGroup.length === 2) {
                    if(curGroup[0].value !== curGroup[1].value) {
                        errors.push({elm:curGroup[1], errors:['ink-fv-confirm']});
                    }
                }
            }
            return errors;
        },

        /**
         * Creates the groups of 'confirm' objects
         * 
         * @method _makeConfirmGroups
         * @private
         * @return {Array|Boolean} Returns the array of confirm elements or false on error.
         */
        _makeConfirmGroups: function()
        {
            var oGroups;
            if(this.confirmGroup && this.confirmGroup.length > 0) {
                oGroups = {};
                var curElm = false;
                var curGroup = false;
                //this.confirmElms[this.element.id];
                for(var i=0, total=this.confirmElms[this.element.id].length; i < total; i++) {
                    curElm = this.confirmElms[this.element.id][i];
                    for(var j=0, totalG=this.confirmGroup.length; j < totalG; j++) {
                        curGroup =  this.confirmGroup[j];
                        if(Css.hasClassName(curElm, curGroup)) {
                            if(typeof(oGroups[curGroup]) === 'undefined') {
                                oGroups[curGroup] = [curElm];
                            } else {
                                oGroups[curGroup].push(curElm);
                            }
                        }
                    }
                }
                return oGroups;
            } else {
                if(this.confirmElms[this.element.id].length === 2) {
                    oGroups = {
                        "ink-fv-confirm": [
                                this.confirmElms[this.element.id][0],
                                this.confirmElms[this.element.id][1]
                            ]
                    };
                }
                return oGroups;
            }
            return false;
        },

        /**
         * Validates an element with a custom validation
         * 
         * @method _isCustomValid
         * @param {DOMElemenmt} elm Element to be validated
         * @private
         * @return {Array} Array of errors. If no errors are found, results in an empty array.
         */
        _isCustomValid: function(elm)
        {
            var customErrors = [];
            var curFlag = false;
            for(var i=0, tCustom = this.custom.length; i < tCustom; i++) {
                curFlag = this.custom[i];
                if(Css.hasClassName(elm, curFlag.flag)) {
                    if(!curFlag.callback(elm, curFlag.msg)) {
                        customErrors.push({flag: curFlag.flag, msg: curFlag.msg});
                    }
                }
            }
            return customErrors;
        },

        /**
         * Runs the normal validation functions for a specific element
         * 
         * @method :_isValid
         * @param {DOMElement} elm DOMElement that will be validated
         * @param {String} fieldType Rule to be validated. This must be one of the keys present in the _flagMap property.
         * @private
         * @return {Boolean} The result of the validation.
         */
        _isValid: function(elm, fieldType)
        {
            /*jshint maxstatements:50, maxcomplexity:50 */
            switch(fieldType) {
                case 'ink-fv-required':
                    if(elm.nodeName.toLowerCase() === 'select') {
                        if(elm.selectedIndex > 0) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                    if(elm.getAttribute('type') !== 'checkbox' && elm.getAttribute('type') !== 'radio') {
                        if(this._trim(elm.value) !== '') {
                            return true;
                        }
                    } else if(elm.getAttribute('type') === 'checkbox') {
                        if(elm.checked === true) {
                            return true;
                        }
                    } else if(elm.getAttribute('type') === 'radio') { // get top radio
                        var aFormRadios = elm.form[elm.name];
                        if(typeof(aFormRadios.length) === 'undefined') {
                            aFormRadios = [aFormRadios];
                        }
                        var isChecked = false;
                        for(var i=0, totalRadio = aFormRadios.length; i < totalRadio; i++) {
                            if(aFormRadios[i].checked === true) {
                                isChecked = true;
                            }
                        }
                        return isChecked;
                    }
                    break;

                case 'ink-fv-email':
                    if(this._trim(elm.value) === '') {
                        if(Css.hasClassName(elm, 'ink-fv-required')) {
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        if(InkValidator.mail(elm.value)) {
                            return true;
                        }
                    }
                    break;
                case 'ink-fv-url':
                    if(this._trim(elm.value) === '') {
                        if(Css.hasClassName(elm, 'ink-fv-required')) {
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        if(InkValidator.url(elm.value)) {
                            return true;
                        }
                    }
                    break;
                case 'ink-fv-number':
                    if(this._trim(elm.value) === '') {
                        if(Css.hasClassName(elm, 'ink-fv-required')) {
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        if(!isNaN(Number(elm.value))) {
                            return true;
                        }
                    }
                    break;
                case 'ink-fv-phone_pt':
                    if(this._trim(elm.value) === '') {
                        if(Css.hasClassName(elm, 'ink-fv-required')) {
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        if(InkValidator.isPTPhone(elm.value)) {
                            return true;
                        }
                    }
                    break;
                case 'ink-fv-phone_cv':
                    if(this._trim(elm.value) === '') {
                        if(Css.hasClassName(elm, 'ink-fv-required')) {
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        if(InkValidator.isCVPhone(elm.value)) {
                            return true;
                        }
                    }
                    break;
                case 'ink-fv-phone_ao':
                    if(this._trim(elm.value) === '') {
                        if(Css.hasClassName(elm, 'ink-fv-required')) {
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        if(InkValidator.isAOPhone(elm.value)) {
                            return true;
                        }
                    }
                    break;
                case 'ink-fv-phone_mz':
                    if(this._trim(elm.value) === '') {
                        if(Css.hasClassName(elm, 'ink-fv-required')) {
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        if(InkValidator.isMZPhone(elm.value)) {
                            return true;
                        }
                    }
                    break;
                case 'ink-fv-date':
                    if(this._trim(elm.value) === '') {
                        if(Css.hasClassName(elm, 'ink-fv-required')) {
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        var Element = Ink.getModule('Ink.Dom.Element',1);
                        var dataset = Element.data( elm );
                        var validFormat = 'yyyy-mm-dd';

                        if( Css.hasClassName(elm, 'ink-datepicker') && ("format" in dataset) ){
                            validFormat = dataset.format;
                        } else if( ("validFormat" in dataset) ){
                            validFormat = dataset.validFormat;
                        }

                        if( !(validFormat in InkValidator._dateParsers ) ){
                            var validValues = [];
                            for( var val in InkValidator._dateParsers ){
                                validValues.push(val);
                            }
                            throw "The attribute data-valid-format must be one of the following values: " + validValues.join(',');
                        }
                        
                        return InkValidator.isDate( validFormat, elm.value );
                    }
                    break;
                case 'ink-fv-custom':
                    break;
            }

            return false;
        },

        /**
         * Makes the necessary changes to the markup to show the errors of a given element
         * 
         * @method _showError
         * @param {DOMElement} formElm The form element to be changed to show the errors
         * @param {Array} aFail An array with the errors found.
         * @private
         */
        _showError: function(formElm, aFail)
        {
            this._clearError(formElm);

            //ink-warning-field

            //console.log(aFail);
            var curElm = false;
            for(var i=0, tFail = aFail.length; i < tFail; i++) {
                curElm = aFail[i].elm;

                if(curElm.getAttribute('type') !== 'radio') {

                    var newLabel = document.createElement('p');
                    //newLabel.setAttribute('for',curElm.id);
                    //newLabel.className = this._errorClassName;
                    //newLabel.className += ' ' + this._errorTypeErrorClassName;
                    Css.addClassName(newLabel, this._errorClassName);
                    Css.addClassName(newLabel, this._errorTypeErrorClassName);
                    if(aFail[i].errors[0] !== 'ink-fv-custom') {
                        newLabel.innerHTML = this._flagMap[aFail[i].errors[0]].msg;
                    } else {
                        newLabel.innerHTML = aFail[i].custom[0].msg;
                    }

                    if(curElm.getAttribute('type') !== 'checkbox') {
                        curElm.nextSibling.parentNode.insertBefore(newLabel, curElm.nextSibling);
                        if(Css.hasClassName(curElm.parentNode, 'control')) {
                            Css.addClassName(curElm.parentNode.parentNode, 'validation');
                            if(aFail[i].errors[0] === 'ink-fv-required') {
                                Css.addClassName(curElm.parentNode.parentNode, 'error');
                            } else {
                                Css.addClassName(curElm.parentNode.parentNode, 'warning');
                            }
                        }
                    } else {
                        /* // TODO checkbox... does not work with this CSS
                        curElm.parentNode.appendChild(newLabel);
                        if(Css.hasClassName(curElm.parentNode.parentNode, 'control-group')) {
                            Css.addClassName(curElm.parentNode.parentNode, 'control');
                            Css.addClassName(curElm.parentNode.parentNode, 'validation');
                            Css.addClassName(curElm.parentNode.parentNode, 'error');
                        }*/
                    }
                } else {
                    if(Css.hasClassName(curElm.parentNode.parentNode, 'control-group')) {
                        Css.addClassName(curElm.parentNode.parentNode, 'validation');
                        Css.addClassName(curElm.parentNode.parentNode, 'error');
                    }
                }
            }
        },

        /**
         * Clears the error of a given element. Normally executed before any validation, for all elements, as a reset.
         * 
         * @method _clearErrors
         * @param {DOMElement} formElm Form element to be cleared.
         * @private
         */
        _clearError: function(formElm)
        {
            //return;
            var aErrorLabel = formElm.getElementsByTagName('p');

            var curElm = false;
            for(var i = (aErrorLabel.length - 1); i >= 0; i--) {
                curElm = aErrorLabel[i];
                if(Css.hasClassName(curElm, this._errorClassName)) {
                    if(Css.hasClassName(curElm.parentNode, 'control')) {
                        Css.removeClassName(curElm.parentNode.parentNode, 'validation');
                        Css.removeClassName(curElm.parentNode.parentNode, 'error');
                        Css.removeClassName(curElm.parentNode.parentNode, 'warning');
                    }

                    if(Css.hasClassName(curElm,'tip') && Css.hasClassName(curElm,'error')){
                        curElm.parentNode.removeChild(curElm);
                    }
                }
            }

            var aErrorLabel2 = formElm.getElementsByTagName('ul');
            for(i = (aErrorLabel2.length - 1); i >= 0; i--) {
                curElm = aErrorLabel2[i];
                if(Css.hasClassName(curElm, 'control-group')) {
                    Css.removeClassName(curElm, 'validation');
                    Css.removeClassName(curElm, 'error');
                }
            }
        },

        /**
         * Removes unnecessary spaces to the left or right of a string
         * 
         * @method _trim
         * @param {String} stri String to be trimmed
         * @private
         * @return {String|undefined} String trimmed.
         */
        _trim: function(str)
        {
            if(typeof(str) === 'string')
            {
                return str.replace(/^\s+|\s+$|\n+$/g, '');
            }
        }
    };

    return FormValidator;

});

/**
 * @module Ink.UI.Droppable_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule("Ink.UI.Droppable","1",["Ink.Dom.Element_1", "Ink.Dom.Event_1", "Ink.Dom.Css_1"], function( Element, Event, Css) {

    /**
     * @class Ink.UI.Droppable
     * @version 1
     * @static
     */
	var Droppable = {
		/**
		 * Flag that determines if it's in debug mode or not
		 *
		 * @property debug
		 * @type {Boolean}
		 * @private
		 */
		debug: false,

        /**
         * Associative array with the elements that are droppable
         * 
         * @property _elements
         * @type {Object}
         * @private
         */
		_elements: {}, // indexed by id

        /**
		 * Makes an element droppable and adds it to the stack of droppable elements.
		 * Can consider it a constructor of droppable elements, but where no Droppable object is returned.
         * 
         * @method add
		 * @param {String|DOMElement}       element    - target element
		 * @param {optional Object}         options    - options object
		 *     @param {String}       [options.hoverclass] - Classname applied when an acceptable draggable element is hovering the element
		 *     @param {Array|String} [options.accept]     - Array or comma separated string of classnames for elements that can be accepted by this droppable
		 *     @param {Function}     [options.onHover]    - callback called when an acceptable draggable element is hovering the droppable. Gets the draggable and the droppable element as parameters.
		 *     @param {Function}     [options.onDrop]     - callback called when an acceptable draggable element is dropped. Gets the draggable, the droppable and the event as parameterse.
         * @public
         */
		add: function(element, options) {
			var opt = Ink.extendObj( {
				hoverclass:		false,
				accept:			false,
				onHover:		false,
				onDrop:			false,
				onDropOut:		false				
			}, options || {});

			element = Ink.i(element);

			if (opt.accept && opt.accept.constructor === Array) {
				opt.accept = opt.accept.join();
			}

			this._elements[element.id] = {options: opt};
			this.update(element.id);
		},

        /**
		 * Invoke every time a drag starts
         * 
         * @method updateAll
         * @public
         */
		/**
		 */
		updateAll: function() {
			for (var id in this._elements) {
				if (!this._elements.hasOwnProperty(id)) {	continue;	}
				this.update(Ink.i(id));
			}
		},

        /**
		 * Updates location and size of droppable element
         * 
         * @method update
		 * @param {String|DOMElement} element - target element
         * @public
         */
		update: function(element) {
			element = Ink.i(element);
			var data = this._elements[element.id];
			if (!data) {
				return; /*throw 'Data about element with id="' + element.id + '" was not found!';*/
			}

			data.left	= Element.offsetLeft(element);
			data.top	= Element.offsetTop( element);
			data.right	= data.left + Element.elementWidth( element);
			data.bottom	= data.top  + Element.elementHeight(element);

			// if (this.debug) {
			// 	// for debugging purposes
			// 	if (!data.rt) {		data.rt = SAPO.Utility.Debug.addRect(document.body,	[data.left, data.top], [data.right-data.left+1, data.bottom-data.top+1]);	}
			// 	else {				SAPO.Utility.Debug.updateRect(data.rt,				[data.left, data.top], [data.right-data.left+1, data.bottom-data.top+1]);	}
			// }
		},

        /**
		 * Removes an element from the droppable stack and removes the droppable behavior
		 * 
         * @method remove
		 * @param {String|DOMElement} el - target element
         * @public
         */
		remove: function(el) {
			el = Ink.i(el);
			delete this._elements[el.id];
		},

        /**
		 * Method called by a draggable to execute an action on a droppable
         * 
         * @method action
		 * @param {Object} coords    - coordinates where the action happened
		 * @param {String} type      - type of action. drag or drop.
		 * @param {Object} ev        - Event object
		 * @param {Object} draggable - draggable element
         * @public
         */
		action: function(coords, type, ev, draggable) {
			var opt, classnames, accept, el, element;

			// check all droppable elements
			for (var elId in this._elements) {
				if (!this._elements.hasOwnProperty(elId)) {	continue;	}
				el = this._elements[elId];
				opt = el.options;
				accept = false;
				element = Ink.i(elId);

				// check if our draggable is over our droppable
				if (coords.x >= el.left && coords.x <= el.right && coords.y >= el.top && coords.y <= el.bottom) {

					// INSIDE

					// check if the droppable accepts the draggable
					if (opt.accept) {
						classnames = draggable.className.split(' ');
						for ( var j = 0, lj = classnames.length; j < lj; j++) {
							if (opt.accept.search(classnames[j]) >= 0 && draggable !== element) {
								accept = true;
							}
						}
					}
					else {
						accept = true;
					}

					if (accept) {
						if (type === 'drag') {
							if (opt.hoverclass) {
								Css.addClassName(element, opt.hoverclass);
							}
							if (opt.onHover) {
								opt.onHover(draggable, element);
							}
						}
						else {
							if (type === 'drop' && opt.onDrop) {
								if (opt.hoverclass) {
									Css.removeClassName(element, opt.hoverclass);
								}
								if (opt.onDrop) {
									opt.onDrop(draggable, element, ev);
								}
							}
                        }
					}
				}
				else {
					// OUTSIDE
					if (type === 'drag' && opt.hoverclass) {
						Css.removeClassName(element, opt.hoverclass);
					}
					if(type === 'drop'){
						if(opt.onDropOut){
							opt.onDropOut(draggable, element, ev);
						}
					}
				}
			}
		}
	};

	return Droppable;
});

/**
 * @module Ink.UI.Draggable_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule("Ink.UI.Draggable","1",["Ink.Dom.Element_1", "Ink.Dom.Event_1", "Ink.Dom.Css_1", "Ink.Dom.Browser_1", "Ink.UI.Droppable_1"],function( Element, Event, Css, Browser, Droppable) {

    /**
     * @class Ink.UI.Draggable
     * @version 1
     * @constructor
     * @param {String|DOMElement} selector Either a CSS Selector string, or the form's DOMElement
     * @param {Object} [opptions] Optional object for configuring the component
	 *     @param {String}            [options.constraint]     - Movement constraint. None by default. Can be either vertical or horizontal.
	 *     @param {Number}            [options.top]            - top limit for the draggable area
	 *     @param {Number}            [options.right]          - right limit for the draggable area
	 *     @param {Number}            [options.bottom]         - bottom limit for the draggable area
	 *     @param {Number}            [options.left]           - left limit for the draggable area
	 *     @param {String|DOMElement} [options.handler]        - if specified, only this element will be used for dragging instead of the whole target element
	 *     @param {Boolean}           [options.revert]         - if true, reverts the draggable to the original position when dragging stops
	 *     @param {String}            [options.cursor]         - cursor type used over the draggable object
	 *     @param {Number}            [options.zindex]         - zindex applied to the draggable element while dragged
	 *     @param {Number}            [options.fps]            - if defined, on drag will run every n frames per second only
	 *     @param {DomElement}        [options.droppableProxy] - if set, a shallow copy of the droppableProxy will be put on document.body with transparent bg
	 *     @param {String}            [options.mouseAnchor]    - defaults to mouse cursor. can be 'left|center|right top|center|bottom'
	 *     @param {Function}          [options.onStart]        - callback called when dragging starts
	 *     @param {Function}          [options.onEnd]          - callback called when dragging stops
	 *     @param {Function}          [options.onDrag]         - callback called while dragging, prior to position updates
	 *     @param {Function}          [options.onChange]       - callback called while dragging, after position updates
     * @example
     *     Ink.requireModules( ['Ink.UI.Draggable_1'], function( Draggable ){
     *         new Draggable( 'myElementId' );
     *     });
     */
	var Draggable = function(element, options) {
		this.init(element, options);
	};

	Draggable.prototype = {

        /**
         * Init function called by the constructor
         * 
         * @method _init
         * @param {String|DOMElement} element ID of the element or DOM Element.
         * @param {Object} [options] Options object for configuration of the module.
         * @private
         */
		init: function(element, options) {
			var o = Ink.extendObj( {
				constraint:			false,
				top:				0,
				right:				Element.pageWidth(),
				bottom:				Element.pageHeight(),
				left:				0,
				handler:			false,
				revert:				false,
				cursor:				'move',
				zindex:				9999,
				onStart:			false,
				onEnd:				false,
				onDrag:				false,
				onChange:			false,
				droppableProxy:		false,
				mouseAnchor:		undefined,
				skipChildren:		true,
				debug:				false
			}, options || {});

			this.options = o;
			this.element = Ink.i(element);

			this.handle				= false;
			this.elmStartPosition	= false;
			this.active				= false;
			this.dragged			= false;
			this.prevCoords			= false;
			this.placeholder		= false;

			this.position			= false;
			this.zindex				= false;
			this.firstDrag			= true;

			if (o.fps) {
				this.deltaMs = 1000 / o.fps;
				this.lastRanAt = 0;
			}

			this.handlers = {};
			this.handlers.start			= Ink.bindEvent(this._onStart,this);
			this.handlers.dragFacade	= Ink.bindEvent(this._onDragFacade,this);
			this.handlers.drag			= Ink.bindEvent(this._onDrag,this);
			this.handlers.end			= Ink.bindEvent(this._onEnd,this);
			this.handlers.selectStart	= function(event) {	Event.stop(event);	return false;	};

			// set handler
			this.handle = (this.options.handler) ? Ink.i(this.options.handler) : this.element;
			this.handle.style.cursor = o.cursor;

			if (o.right  !== false) {	this.options.right	= o.right  - Element.elementWidth( element);	}
			if (o.bottom !== false) {	this.options.bottom	= o.bottom - Element.elementHeight(element);	}

			Event.observe(this.handle, 'touchstart', this.handlers.start);
			Event.observe(this.handle, 'mousedown', this.handlers.start);

			if (Browser.IE) {
				Event.observe(this.element, 'selectstart', this.handlers.selectStart);
			}
		},

        /**
		 * Removes the ability of the element of being dragged
         * 
         * @method destroy
         * @public
         */
		destroy: function() {
			Event.stopObserving(this.handle, 'touchstart', this.handlers.start);
			Event.stopObserving(this.handle, 'mousedown', this.handlers.start);

			if (Browser.IE) {
				Event.stopObserving(this.element, 'selectstart', this.handlers.selectStart);
			}
		},

        /**
		 * Browser-independant implementation of page scroll
         * 
         * @method _getPageScroll
         * @return {Array} Array where the first position is the scrollLeft and the second position is the scrollTop
         * @private
         */
		_getPageScroll: function() {

			if (typeof self.pageXOffset !== "undefined") {
				return [ self.pageXOffset, self.pageYOffset ];
			}
			if (typeof document.documentElement !== "undefined" && typeof document.documentElement.scrollLeft !== "undefined") {
				return [ document.documentElement.scrollLeft, document.documentElement.scrollTop ];
			}
			return [ document.body.scrollLeft, document.body.scrollTop ];
		},

        /**
		 * Gets coordinates for a given event (with added page scroll)
         * 
         * @method _getCoords
         * @param {Object} e window.event object.
         * @return {Array} Array where the first position is the x coordinate, the second is the y coordinate
         * @private
         */
		_getCoords: function(e) {
			var ps = this._getPageScroll();
			return {
				x: (e.touches ? e.touches[0].clientX : e.clientX) + ps[0],
				y: (e.touches ? e.touches[0].clientY : e.clientY) + ps[1]
			};
		},

        /**
		 * Clones src element's relevant properties to dst
         * 
         * @method _cloneStyle
         * @param {DOMElement} src Element from where we're getting the styles
         * @param {DOMElement} dst Element where we're placing the styles.
         * @private
         */
		_cloneStyle: function(src, dst) {
			dst.className = src.className;
			dst.style.borderWidth	= '0';
			dst.style.padding		= '0';
			dst.style.position		= 'absolute';
			dst.style.width			= Element.elementWidth(src)		+ 'px';
			dst.style.height		= Element.elementHeight(src)	+ 'px';
			dst.style.left			= Element.elementLeft(src)		+ 'px';
			dst.style.top			= Element.elementTop(src)		+ 'px';
			dst.style.cssFloat		= Css.getStyle(src, 'float');
			dst.style.display		= Css.getStyle(src, 'display');
		},

        /**
         * onStart event handler
         * 
         * @method _onStart
         * @param {Object} e window.event object
         * @return {Boolean|void} In some cases return false. Otherwise is void
         * @private
         */
		_onStart: function(e) {
			if (!this.active && Event.isLeftClick(e) || typeof e.button === 'undefined') {

				var tgtEl = e.target || e.srcElement;
				if (this.options.skipChildren && tgtEl !== this.element) {	return;	}

				Event.stop(e);

				this.elmStartPosition = [
					Element.elementLeft(this.element),
					Element.elementTop( this.element)
				];

				var pos = [
					parseInt(Css.getStyle(this.element, 'left'), 10),
					parseInt(Css.getStyle(this.element, 'top'),  10)
				];

				var dims = [
					Element.elementWidth( this.element),
					Element.elementHeight(this.element)
				];

				this.originalPosition = [ pos[0] ? pos[0]: null, pos[1] ? pos[1] : null ];
				this.delta = this._getCoords(e); // mouse coords at beginning of drag

				this.active = true;
				this.position = Css.getStyle(this.element, 'position');
				this.zindex = Css.getStyle(this.element, 'zIndex');

				var div = document.createElement('div');
				div.style.position		= this.position;
				div.style.width			= dims[0] + 'px';
				div.style.height		= dims[1] + 'px';
				div.style.marginTop		= Css.getStyle(this.element, 'margin-top');
				div.style.marginBottom	= Css.getStyle(this.element, 'margin-bottom');
				div.style.marginLeft	= Css.getStyle(this.element, 'margin-left');
				div.style.marginRight	= Css.getStyle(this.element, 'margin-right');
				div.style.borderWidth	= '0';
				div.style.padding		= '0';
				div.style.cssFloat		= Css.getStyle(this.element, 'float');
				div.style.display		= Css.getStyle(this.element, 'display');
				div.style.visibility	= 'hidden';

				this.delta2 = [ this.delta.x - this.elmStartPosition[0], this.delta.y - this.elmStartPosition[1] ]; // diff between top-left corner of obj and mouse
				if (this.options.mouseAnchor) {
					var parts = this.options.mouseAnchor.split(' ');
					var ad = [dims[0], dims[1]];	// starts with 'right bottom'
					if (parts[0] === 'left') {	ad[0] = 0;	} else if(parts[0] === 'center') {	ad[0] = parseInt(ad[0]/2, 10);	}
					if (parts[1] === 'top') {	ad[1] = 0;	} else if(parts[1] === 'center') {	ad[1] = parseInt(ad[1]/2, 10);	}
					this.applyDelta = [this.delta2[0] - ad[0], this.delta2[1] - ad[1]];
				}

				this.placeholder = div;

				if (this.options.onStart) {		this.options.onStart(this.element, e);		}

				if (this.options.droppableProxy) {	// create new transparent div to optimize DOM traversal during drag
					this.proxy = document.createElement('div');
					dims = [
						window.innerWidth	|| document.documentElement.clientWidth		|| document.body.clientWidth,
						window.innerHeight	|| document.documentElement.clientHeight	|| document.body.clientHeight
					];
					var fs = this.proxy.style;
					fs.width			= dims[0] + 'px';
					fs.height			= dims[1] + 'px';
					fs.position			= 'fixed';
					fs.left				= '0';
					fs.top				= '0';
					fs.zIndex			= this.options.zindex + 1;
					fs.backgroundColor	= '#FF0000';
					Css.setOpacity(this.proxy, 0);

					var firstEl = document.body.firstChild;
					while (firstEl && firstEl.nodeType !== 1) {	firstEl = firstEl.nextSibling;	}
					document.body.insertBefore(this.proxy, firstEl);

					Event.observe(this.proxy, 'mousemove', this.handlers[this.options.fps ? 'dragFacade' : 'drag']);
					Event.observe(this.proxy, 'touchmove', this.handlers[this.options.fps ? 'dragFacade' : 'drag']);
				}
				else {
					Event.observe(document, 'mousemove', this.handlers[this.options.fps ? 'dragFacade' : 'drag']);
				}

				this.element.style.position = 'absolute';
				this.element.style.zIndex = this.options.zindex;
				this.element.parentNode.insertBefore(this.placeholder, this.element);

				this._onDrag(e);

				Event.observe(document, 'mouseup',	this.handlers.end);
				Event.observe(document, 'touchend',	this.handlers.end);

				return false;
			}
		},

        /**
         * Function that gets the timestamp of the current run from time to time. (FPS)
         * 
         * @method _onDragFacade
         * @param {Object} window.event object.
         * @private
         */
		_onDragFacade: function(e) {
			var now = new Date().getTime();
			if (!this.lastRanAt || now > this.lastRanAt + this.deltaMs) {
				this.lastRanAt = now;
				this._onDrag(e);
			}
		},

        /**
         * Function that handles the dragging movement
         * 
         * @method _onDrag
         * @param {Object} window.event object.
         * @private
         */
		_onDrag: function(e) {
			if (this.active) {
				Event.stop(e);
				this.dragged = true;
				var mouseCoords	= this._getCoords(e),
					mPosX		= mouseCoords.x,
					mPosY		= mouseCoords.y,
					o			= this.options,
					newX		= false,
					newY		= false;

				if (!this.prevCoords) {		this.prevCoords = {x: 0, y: 0};		}

				if (mPosX !== this.prevCoords.x || mPosY !== this.prevCoords.y) {
					if (o.onDrag) {		o.onDrag(this.element, e);		}
					this.prevCoords = mouseCoords;

					newX = this.elmStartPosition[0] + mPosX - this.delta.x;
					newY = this.elmStartPosition[1] + mPosY - this.delta.y;

					if (o.constraint === 'horizontal' || o.constraint === 'both') {
						if (o.right !== false && newX > o.right) {		newX = o.right;		}
						if (o.left  !== false && newX < o.left) {		newX = o.left;		}
					}
					if (o.constraint === 'vertical' || o.constraint === 'both') {
						if (o.bottom !== false && newY > o.bottom) {	newY = o.bottom;	}
						if (o.top    !== false && newY < o.top) {		newY = o.top;		}
					}

					if (this.firstDrag) {
						if (Droppable) {	Droppable.updateAll();	}
						/*this.element.style.position = 'absolute';
						this.element.style.zIndex = this.options.zindex;
						this.element.parentNode.insertBefore(this.placeholder, this.element);*/
						this.firstDrag = false;
					}

					if (newX) {		this.element.style.left = newX + 'px';		}
					if (newY) {		this.element.style.top  = newY + 'px';		}

					if (Droppable) {
						// apply applyDelta defined on drag init
						var mouseCoords2 = this.options.mouseAnchor ? {x: mPosX - this.applyDelta[0], y: mPosY - this.applyDelta[1]} : mouseCoords;

						// for debugging purposes
						// if (this.options.debug) {
						// 	if (!this.pt) {
						// 		this.pt = Debug.addPoint(document.body, [mouseCoords2.x, mouseCoords2.y], '#0FF', 9);
						// 		this.pt.style.zIndex = this.options.zindex + 1;
						// 	}
						// 	else {
						// 		Debug.movePoint(this.pt, [mouseCoords2.x, mouseCoords2.y]);
						// 	}
						// }

						Droppable.action(mouseCoords2, 'drag', e, this.element);
					}
					if (o.onChange) {	o.onChange(this);	}
				}
			}
		},

        /**
         * Function that handles the end of the dragging process
         * 
         * @method _onEnd
         * @param {Object} window.event object.
         * @private
         */
		_onEnd: function(e) {
			Event.stopObserving(document, 'mousemove', this.handlers.drag);
			Event.stopObserving(document, 'touchmove', this.handlers.drag);

			if (this.options.fps) {
				this._onDrag(e);
			}

			if (this.active && this.dragged) {

				if (this.options.droppableProxy) {	// remove transparent div...
					document.body.removeChild(this.proxy);
				}

				if (this.pt) {	// remove debugging element...
					this.pt.parentNode.removeChild(this.pt);
					this.pt = undefined;
				}

	            /*if (this.options.revert) {
					this.placeholder.parentNode.removeChild(this.placeholder);
				}*/

	            if(this.placeholder) {
	                this.placeholder.parentNode.removeChild(this.placeholder);
	            }

				if (this.options.revert) {
					this.element.style.position = this.position;
					if (this.zindex !== null) {
						this.element.style.zIndex = this.zindex;
					}
					else {
						this.element.style.zIndex = 'auto';
					} // restore default zindex of it had none

					this.element.style.left = (this.originalPosition[0]) ? this.originalPosition[0] + 'px' : '';
					this.element.style.top  = (this.originalPosition[1]) ? this.originalPosition[1] + 'px' : '';
				}

				if (this.options.onEnd) {
					this.options.onEnd(this.element, e);
				}

				if (Droppable) {
					Droppable.action(this._getCoords(e), 'drop', e, this.element);
				}

				this.position	= false;
				this.zindex		= false;
				this.firstDrag	= true;
			}

			this.active			= false;
			this.dragged		= false;
		}
	};

	return Draggable;

});

/**
 * @module Ink.UI.DatePicker_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.DatePicker', '1', ['Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1','Ink.Util.Date_1'], function(Aux, Event, Css, Element, Selector, InkArray, InkDate ) {
    'use strict';    

    /**
     * @class Ink.UI.DatePicker
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @uses Ink.Util.Array
     * @uses Ink.Util.Date
     *
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *      @param {String}   [options.instance]         unique id for the datepicker
     *      @param {String}   [options.format]           Date format string
     *      @param {String}   [options.cssClass]         CSS class to be applied to the datepicker
     *      @param {String}   [options.position]         position the datepicker. Accept right or bottom, default is right
     *      @param {Boolean}  [options.onFocus]          if the datepicker should open when the target element is focused
     *      @param {Function} [options.onYearSelected]   callback function to execute when the year is selected
     *      @param {Function} [options.onMonthSelected]  callback function to execute when the month is selected
     *      @param {Function} [options.validDayFn]       callback function to execute when 'rendering' the day (in the month view)
     *      @param {String}   [options.startDate]        Date to define init month. Must be in yyyy-mm-dd format
     *      @param {Function} [options.onSetDate]        callback to execute when set date
     *      @param {Boolean}  [options.displayInSelect]  whether to display the component in a select. defaults to false.
     *      @param {Boolean}  [options.showClose]        whether to display the close button or not. defaults to true.
     *      @param {Boolean}  [options.showClean]        whether to display the clean button or not. defaults to true.
     *      @param {String}   [options.yearRange]        enforce limits to year for the Date, ex: '1990:2020' (deprecated)
     *      @param {String}   [options.dateRange]        enforce limits to year, month and day for the Date, ex: '1990-08-25:2020-11'
     *      @paran {Number}   [options.startWeekDay]     day to use as first column on the calendar view. Defaults to Monday (1)
     *      @param {String}   [options.closeText]        text to display on close button. defaults to 'Fechar'
     *      @param {String}   [options.cleanText]        text to display on clean button. defaults to 'Limpar'
     *      @param {String}   [options.prevLinkText]     text to display on the previous button. defaults to '«'
     *      @param {String}   [options.nextLinkText]     text to display on the previous button. defaults to '«'
     *      @param {String}   [options.ofText]           text to display between month and year. defaults to ' de '
     *      @param {Object}   [options.month]            Hash of month names. Defaults to portuguese month names. January is 1.
     *      @param {Object}   [options.wDay]             Hash of weekdays. Defaults to portuguese month names. Sunday is 0.
     *
     * @example
     *     <input type="text" id="dPicker" />
     *     <script>
     *         Ink.requireModules(['Ink.Dom.Selector_1','Ink.UI.DatePicker_1'],function( Selector, DatePicker ){
     *             var datePickerElement = Ink.s('#dPicker');
     *             var datePickerObj = new DatePicker( datePickerElement );
     *         });
     *     </script>
     */
    var DatePicker = function(selector, options) {

        if (selector) {
            this._dataField = Aux.elOrSelector(selector, '1st argument');
        }

        this._options = Ink.extendObj({
            instance:        'scdp_' + Math.round(99999*Math.random()),
            format:          'yyyy-mm-dd',
            cssClass:        'sapo_component_datepicker',
            position:        'right',
            onFocus:         true,
            onYearSelected:  undefined,
            onMonthSelected: undefined,
            validDayFn:      undefined,
            startDate:       false, // format yyyy-mm-dd
            onSetDate:       false,
            displayInSelect: false,
            showClose:       true,
            showClean:       true,
            yearRange:       false,
            dateRange:       false,
            startWeekDay:    1,
            closeText:       'Close',
            cleanText:       'Clear',
            prevLinkText:    '«',
            nextLinkText:    '»',
            ofText:          '&nbsp;de&nbsp;',
            month: {
                 1:'January',
                 2:'February',
                 3:'March',
                 4:'April',
                 5:'May',
                 6:'June',
                 7:'July',
                 8:'August',
                 9:'September',
                10:'October',
                11:'November',
                12:'December'
            },
            wDay: {
                0:'Sunday',
                1:'Monday',
                2:'Tuesday',
                3:'Wednesday',
                4:'Thursday',
                5:'Friday',
                6:'Saturday'
            }
        }, Element.data(this._dataField) || {});

        this._options = Ink.extendObj(this._options, options || {});

        this._options.format = this._dateParsers[ this._options.format ] || this._options.format;

        this._hoverPicker = false;

        this._picker = null;
        if (this._options.pickerField) {
            this._picker = Aux.elOrSelector(this._options.pickerField, 'pickerField');
        }

        this._today = new Date();
        this._day   = this._today.getDate( );
        this._month = this._today.getMonth( );
        this._year  = this._today.getFullYear( );

        this._setMinMax( this._options.dateRange || this._options.yearRange );
        this._data = new Date( Date.UTC.apply( this , this._checkDateRange( this._year , this._month , this._day ) ) );

        if(this._options.startDate && typeof this._options.startDate === 'string' && /\d\d\d\d\-\d\d\-\d\d/.test(this._options.startDate)) {
            this.setDate( this._options.startDate );
        }

        this._init();

        this._render();

        if ( !this._options.startDate ){
            if( this._dataField && typeof this._dataField.value === 'string' && this._dataField.value){
                this.setDate( this._dataField.value );
            }
        }

        Aux.registerInstance(this, this._containerObject, 'datePicker');
    };

    DatePicker.prototype = {
        version: '0.1',

        /**
         * Initialization function. Called by the constructor and
         * receives the same parameters.
         *
         * @method _init
         * @private
         */
        _init: function(){
            Ink.extendObj(this._options,this._lang || {});
        },

        /**
         * Renders the DatePicker's markup
         *
         * @method _render
         * @private
         */
        _render: function() {
            /*jshint maxstatements:100, maxcomplexity:30 */
            this._containerObject = document.createElement('div');

            this._containerObject.id = this._options.instance;

            this._containerObject.className = 'sapo_component_datepicker';
            var dom = document.getElementsByTagName('body')[0];

            if(this._options.showClose || this._options.showClean){
                this._superTopBar = document.createElement("div");
                this._superTopBar.className = 'sapo_cal_top_options';
                if(this._options.showClean){
                    var clean = document.createElement('a');
                    clean.className = 'clean';
                    clean.innerHTML = this._options.cleanText;
                    this._superTopBar.appendChild(clean);
                }
                if(this._options.showClose){
                    var close = document.createElement('a');
                    close.className = 'close';
                    close.innerHTML = this._options.closeText;
                    this._superTopBar.appendChild(close);
                }
                this._containerObject.appendChild(this._superTopBar);
            }


            var calendarTop = document.createElement("div");
            calendarTop.className = 'sapo_cal_top';

            this._monthDescContainer = document.createElement("div");
            this._monthDescContainer.className = 'sapo_cal_month_desc';

            this._monthPrev = document.createElement('div');
            this._monthPrev.className = 'sapo_cal_prev';
            this._monthPrev.innerHTML ='<a href="#prev" class="change_month_prev">' + this._options.prevLinkText + '</a>';

            this._monthNext = document.createElement('div');
            this._monthNext.className = 'sapo_cal_next';
            this._monthNext.innerHTML ='<a href="#next" class="change_month_next">' + this._options.nextLinkText + '</a>';

            calendarTop.appendChild(this._monthPrev);
            calendarTop.appendChild(this._monthDescContainer);
            calendarTop.appendChild(this._monthNext);

            this._monthContainer = document.createElement("div");
            this._monthContainer.className = 'sapo_cal_month';

            this._containerObject.appendChild(calendarTop);
            this._containerObject.appendChild(this._monthContainer);

            this._monthSelector = document.createElement('ul');
            this._monthSelector.className = 'sapo_cal_month_selector';

            var ulSelector;
            var liMonth;
            for(var i=1; i<=12; i++){
                if ((i-1) % 4 === 0) {
                    ulSelector = document.createElement('ul');
                }
                liMonth = document.createElement('li');
                liMonth.innerHTML = '<a href="#" class="sapo_calmonth_' + ( (String(i).length === 2) ? i : "0" + i) + '">' + this._options.month[i].substring(0,3) + '</a>';
                ulSelector.appendChild(liMonth);
                if (i % 4 === 0) {
                    this._monthSelector.appendChild(ulSelector);
                }
            }

            this._containerObject.appendChild(this._monthSelector);

            this._yearSelector = document.createElement('ul');
            this._yearSelector.className = 'sapo_cal_year_selector';

            this._containerObject.appendChild(this._yearSelector);

            if(!this._options.onFocus || this._options.displayInSelect){
                if(!this._options.pickerField){
                    this._picker = document.createElement('a');
                    this._picker.href = '#open_cal';
                    this._picker.innerHTML = 'open';
                    this._picker.style.position = 'absolute';
                    this._picker.style.top = Element.elementTop(this._dataField);
                    this._picker.style.left = Element.elementLeft(this._dataField) + (Element.elementWidth(this._dataField) || 0) + 5 + 'px';
                    this._dataField.parentNode.appendChild(this._picker);
                    this._picker.className = 'sapo_cal_date_picker';
                } else {
                    this._picker = Aux.elOrSelector(this._options.pickerField, 'pickerField');
                }
            }

            if(this._options.displayInSelect){
                if (this._options.dayField && this._options.monthField && this._options.yearField || this._options.pickerField) {
                    this._options.dayField   = Aux.elOrSelector(this._options.dayField,   'dayField');
                    this._options.monthField = Aux.elOrSelector(this._options.monthField, 'monthField');
                    this._options.yearField  = Aux.elOrSelector(this._options.yearField,  'yearField');
                }
                else {
                    throw "To use display in select you *MUST* to set dayField, monthField, yearField and pickerField!";
                }
            }

            dom.insertBefore(this._containerObject, dom.childNodes[0]);
            // this._dataField.parentNode.appendChild(this._containerObject, dom.childNodes[0]);

            if (!this._picker) {
                Event.observe(this._dataField,'focus',Ink.bindEvent(function(){
                    this._containerObject = Element.clonePosition(this._containerObject, this._dataField);

                    if ( this._options.position === 'bottom' )
                    {
                        this._containerObject.style.top = Element.elementHeight(this._dataField) + Element.offsetTop(this._dataField) + 'px';
                        this._containerObject.style.left = Element.offset2(this._dataField)[0] +'px';
                    }
                    else
                    {
                        this._containerObject.style.top = Element.offset2(this._dataField)[1] +'px';
                        this._containerObject.style.left = Element.elementWidth(this._dataField) + Element.offset2(this._dataField)[0] +'px';
                    }
                    //dom.appendChild(this._containerObject);
                    this._updateDate();
                    this._showMonth();
                    this._containerObject.style.display = 'block';
                },this));
            }
            else {
                Event.observe(this._picker,'click',Ink.bindEvent(function(e){
                    Event.stop(e);
                    this._containerObject = Element.clonePosition(this._containerObject,this._picker);
                    this._updateDate();
                    this._showMonth();
                    this._containerObject.style.display = 'block';
                },this));
            }

            if(!this._options.displayInSelect){
                Event.observe(this._dataField,'change', Ink.bindEvent(function() {
                        this._updateDate( );
                        this._showDefaultView( );
                        this.setDate( );
                        if ( !this._hoverPicker )
                        {
                            this._containerObject.style.display = 'none';
                        }
                    },this));
                Event.observe(this._dataField,'blur', Ink.bindEvent(function() {
                        if ( !this._hoverPicker )
                        {
                            this._containerObject.style.display = 'none';
                        }
                    },this));
            }
            else {
                Event.observe(this._options.dayField,'change', Ink.bindEvent(function(){
                        var yearSelected = this._options.yearField[this._options.yearField.selectedIndex].value;
                        if(yearSelected !== '' && yearSelected !== 0) {
                            this._updateDate();
                            this._showDefaultView();
                        }
                    },this));
               Event.observe(this._options.monthField,'change', Ink.bindEvent(function(){
                        var yearSelected = this._options.yearField[this._options.yearField.selectedIndex].value;
                        if(yearSelected !== '' && yearSelected !== 0){
                            this._updateDate();
                            this._showDefaultView();
                        }
                    },this));
                Event.observe(this._options.yearField,'change', Ink.bindEvent(function(){
                        this._updateDate();
                        this._showDefaultView();
                    },this));
            }

            Event.observe(document,'click',Ink.bindEvent(function(e){
                if (e.target === undefined) {   e.target = e.srcElement;    }
                if (!Element.descendantOf(this._containerObject, e.target) && e.target !== this._dataField) {
                    if (!this._picker) {
                        this._containerObject.style.display = 'none';
                    }
                    else if (e.target !== this._picker &&
                             (!this._options.displayInSelect ||
                              (e.target !== this._options.dayField && e.target !== this._options.monthField && e.target !== this._options.yearField) ) ) {
                        if (!this._options.dayField ||
                                (!Element.descendantOf(this._options.dayField,   e.target) &&
                                 !Element.descendantOf(this._options.monthField, e.target) &&
                                 !Element.descendantOf(this._options.yearField,  e.target)      ) ) {
                            this._containerObject.style.display = 'none';
                        }
                    }
                }
            },this));

            this._showMonth();

            this._monthChanger = document.createElement('a');
            this._monthChanger.href = '#monthchanger';
            this._monthChanger.className = 'sapo_cal_link_month';
            this._monthChanger.innerHTML = this._options.month[this._month + 1];

            this._deText = document.createElement('span');
            this._deText.innerHTML = this._options._deText;


            this._yearChanger = document.createElement('a');
            this._yearChanger.href = '#yearchanger';
            this._yearChanger.className = 'sapo_cal_link_year';
            this._yearChanger.innerHTML = this._year;
            this._monthDescContainer.innerHTML = '';
            this._monthDescContainer.appendChild(this._monthChanger);
            this._monthDescContainer.appendChild(this._deText);
            this._monthDescContainer.appendChild(this._yearChanger);

            Event.observe(this._containerObject,'mouseover',Ink.bindEvent(function(e)
            {
                Event.stop( e );
                this._hoverPicker = true;
            },this));

            Event.observe(this._containerObject,'mouseout',Ink.bindEvent(function(e)
            {
                Event.stop( e );
                this._hoverPicker = false;
            },this));

            Event.observe(this._containerObject,'click',Ink.bindEvent(function(e){
                    if(typeof(e.target) === 'undefined'){
                        e.target = e.srcElement;
                    }
                    var className = e.target.className;
                    var isInactive  = className.indexOf( 'sapo_cal_off' ) !== -1;

                    Event.stop(e);

                    if( className.indexOf('sapo_cal_') === 0 && !isInactive ){
                            var day = className.substr( 9 , 2 );
                            if( Number( day ) ) {
                                this.setDate( this._year + '-' + ( this._month + 1 ) + '-' + day );
                                this._containerObject.style.display = 'none';
                            } else if(className === 'sapo_cal_link_month'){
                                this._monthContainer.style.display = 'none';
                                this._yearSelector.style.display = 'none';
                                this._monthPrev.childNodes[0].className = 'action_inactive';
                                this._monthNext.childNodes[0].className = 'action_inactive';
                                this._setActiveMonth();
                                this._monthSelector.style.display = 'block';
                            } else if(className === 'sapo_cal_link_year'){
                                this._monthPrev.childNodes[0].className = 'action_inactive';
                                this._monthNext.childNodes[0].className = 'action_inactive';
                                this._monthSelector.style.display = 'none';
                                this._monthContainer.style.display = 'none';
                                this._showYearSelector();
                                this._yearSelector.style.display = 'block';
                            }
                    } else if( className.indexOf("sapo_calmonth_") === 0 && !isInactive ){
                            var month=className.substr(14,2);
                            if(Number(month)){
                                this._month = month - 1;
                                // if( typeof this._options.onMonthSelected === 'function' ){
                                //     this._options.onMonthSelected(this, {
                                //         'year': this._year,
                                //         'month' : this._month
                                //     });
                                // }
                                this._monthSelector.style.display = 'none';
                                this._monthPrev.childNodes[0].className = 'change_month_prev';
                                this._monthNext.childNodes[0].className = 'change_month_next';

                                if ( this._year < this._yearMin || this._year === this._yearMin && this._month <= this._monthMin ){
                                    this._monthPrev.childNodes[0].className = 'action_inactive';
                                }
                                else if( this._year > this._yearMax || this._year === this._yearMax && this._month >= this._monthMax ){
                                    this._monthNext.childNodes[0].className = 'action_inactive';
                                }

                                this._updateCal();
                                this._monthContainer.style.display = 'block';
                            }
                    } else if( className.indexOf("sapo_calyear_") === 0 && !isInactive ){
                            var year=className.substr(13,4);
                            if(Number(year)){
                                this._year = year;
                                if( typeof this._options.onYearSelected === 'function' ){
                                    this._options.onYearSelected(this, {
                                        'year': this._year
                                    });
                                }
                                this._monthPrev.childNodes[0].className = 'action_inactive';
                                this._monthNext.childNodes[0].className = 'action_inactive';
                                this._yearSelector.style.display='none';
                                this._setActiveMonth();
                                this._monthSelector.style.display='block';
                            }
                    } else if( className.indexOf('change_month_') === 0 && !isInactive ){
                            if(className === 'change_month_next'){
                                this._updateCal(1);
                            } else if(className === 'change_month_prev'){
                                this._updateCal(-1);
                            }
                    } else if( className.indexOf('change_year_') === 0 && !isInactive ){
                            if(className === 'change_year_next'){
                                this._showYearSelector(1);
                            } else if(className === 'change_year_prev'){
                                this._showYearSelector(-1);
                            }
                    } else if(className === 'clean'){
                        if(this._options.displayInSelect){
                            this._options.yearField.selectedIndex = 0;
                            this._options.monthField.selectedIndex = 0;
                            this._options.dayField.selectedIndex = 0;
                        } else {
                            this._dataField.value = '';
                        }
                    } else if(className === 'close'){
                        this._containerObject.style.display = 'none';
                    }

                    this._updateDescription();
                },this));

        },

        /**
         * Sets the range of dates allowed to be selected in the Date Picker
         *
         * @method _setMinMax
         * @param {String} dateRange Two dates separated by a ':'. Example: 2013-01-01:2013-12-12
         * @private
         */
        _setMinMax : function( dateRange )
        {
            var auxDate;
            if( dateRange )
            {
                var dates = dateRange.split( ':' );
                var pattern = /^(\d{4})((\-)(\d{1,2})((\-)(\d{1,2}))?)?$/;
                if ( dates[ 0 ] )
                {
                    if ( dates[ 0 ] === 'NOW' )
                    {
                        this._yearMin   = this._today.getFullYear( );
                        this._monthMin  = this._today.getMonth( ) + 1;
                        this._dayMin    = this._today.getDate( );
                    }
                    else if ( pattern.test( dates[ 0 ] ) )
                    {
                        auxDate = dates[ 0 ].split( '-' );

                        this._yearMin   = Math.floor( auxDate[ 0 ] );
                        this._monthMin  = Math.floor( auxDate[ 1 ] ) || 1;
                        this._dayMin    = Math.floor( auxDate[ 2 ] ) || 1;

                        if ( 1 < this._monthMin && this._monthMin > 12 )
                        {
                            this._monthMin = 1;
                            this._dayMin = 1;
                        }

                        if ( 1 < this._dayMin && this._dayMin > this._daysInMonth( this._yearMin , this._monthMin ) )
                        {
                            this._dayMin = 1;
                        }
                    }
                    else
                    {
                        this._yearMin   = Number.MIN_VALUE;
                        this._monthMin  = 1;
                        this._dayMin    = 1;
                    }
                }

                if ( dates[ 1 ] )
                {
                    if ( dates[ 1 ] === 'NOW' )
                    {
                        this._yearMax   = this._today.getFullYear( );
                        this._monthMax  = this._today.getMonth( ) + 1;
                        this._dayMax    = this._today.getDate( );
                    }
                    else if ( pattern.test( dates[ 1 ] ) )
                    {
                        auxDate = dates[ 1 ].split( '-' );

                        this._yearMax   = Math.floor( auxDate[ 0 ] );
                        this._monthMax  = Math.floor( auxDate[ 1 ] ) || 12;
                        this._dayMax    = Math.floor( auxDate[ 2 ] ) || this._daysInMonth( this._yearMax , this._monthMax );

                        if ( 1 < this._monthMax && this._monthMax > 12 )
                        {
                            this._monthMax = 12;
                            this._dayMax = 31;
                        }

                        var MDay = this._daysInMonth( this._yearMax , this._monthMax );
                        if ( 1 < this._dayMax && this._dayMax > MDay )
                        {
                            this._dayMax = MDay;
                        }
                    }
                    else
                    {
                        this._yearMax   = Number.MAX_VALUE;
                        this._monthMax  = 12;
                        this._dayMax   = 31;
                    }
                }

                if ( !( this._yearMax >= this._yearMin && (this._monthMax > this._monthMin || ( (this._monthMax === this._monthMin) && (this._dayMax >= this._dayMin) ) ) ) )
                {
                    this._yearMin   = Number.MIN_VALUE;
                    this._monthMin  = 1;
                    this._dayMin    = 1;

                    this._yearMax   = Number.MAX_VALUE;
                    this._monthMax  = 12;
                    this._dayMaXx   = 31;
                }
            }
            else
            {
                this._yearMin   = Number.MIN_VALUE;
                this._monthMin  = 1;
                this._dayMin    = 1;

                this._yearMax   = Number.MAX_VALUE;
                this._monthMax  = 12;
                this._dayMax   = 31;
            }
        },

        /**
         * Checks if a date is between the valid range.
         * Starts by checking if the date passed is valid. If not, will fallback to the 'today' date.
         * Then checks if the all params are inside of the date range specified. If not, it will fallback to the nearest valid date (either Min or Max).
         *
         * @method _checkDateRange
         * @param  {Number} year  Year with 4 digits (yyyy)
         * @param  {Number} month Month
         * @param  {Number} day   Day
         * @return {Array}       Array with the final processed date.
         * @private
         */
        _checkDateRange : function( year , month , day )
        {
            if ( !this._isValidDate( year , month + 1 , day ) )
            {
                year  = this._today.getFullYear( );
                month = this._today.getMonth( );
                day   = this._today.getDate( );
            }

            if ( year > this._yearMax )
            {
                year  = this._yearMax;
                month = this._monthMax - 1;
                day   = this._dayMax;
            }
            else if ( year < this._yearMin )
            {
                year  = this._yearMin;
                month = this._monthMin - 1;
                day   = this._dayMin;
            }

            if ( year === this._yearMax && month + 1 > this._monthMax )
            {
                month = this._monthMax - 1;
                day   = this._dayMax;
            }
            else if ( year === this._yearMin && month + 1 < this._monthMin )
            {
                month = this._monthMin - 1;
                day   = this._dayMin;
            }

            if ( year === this._yearMax && month + 1 === this._monthMax && day > this._dayMax ){ day = this._dayMax; }
            else if ( year === this._yearMin && month + 1 === this._monthMin && day < this._dayMin ){ day = this._dayMin; }
            else if ( day > this._daysInMonth( year , month + 1 ) ){ day = this._daysInMonth( year , month + 1 ); }

            return [ year , month , day ];
        },

        /**
         * Sets the markup in the default view mode (showing the days).
         * Also disables the previous and next buttons in case they don't meet the range requirements.
         *
         * @method _showDefaultView
         * @private
         */
        _showDefaultView: function(){
            this._yearSelector.style.display = 'none';
            this._monthSelector.style.display = 'none';
            this._monthPrev.childNodes[0].className = 'change_month_prev';
            this._monthNext.childNodes[0].className = 'change_month_next';

            if ( this._year < this._yearMin || this._year === this._yearMin && this._month + 1 <= this._monthMin ){
                this._monthPrev.childNodes[0].className = 'action_inactive';
            }
            else if( this._year > this._yearMax || this._year === this._yearMax && this._month + 1 >= this._monthMax ){
                this._monthNext.childNodes[0].className = 'action_inactive';
            }

            this._monthContainer.style.display = 'block';
        },

        /**
         * Updates the date shown on the datepicker
         *
         * @method _updateDate
         * @private
         */
        _updateDate: function(){
            var dataParsed;
             if(!this._options.displayInSelect){
                 if(this._dataField.value !== ''){
                    if(this._isDate(this._options.format,this._dataField.value)){
                        dataParsed = this._getDataArrayParsed(this._dataField.value);
                        dataParsed = this._checkDateRange( dataParsed[ 0 ] , dataParsed[ 1 ] - 1 , dataParsed[ 2 ] );

                        this._year  = dataParsed[ 0 ];
                        this._month = dataParsed[ 1 ];
                        this._day   = dataParsed[ 2 ];
                    }else{
                        this._dataField.value = '';
                        this._year  = this._data.getFullYear( );
                        this._month = this._data.getMonth( );
                        this._day   = this._data.getDate( );
                    }
                    this._data.setFullYear( this._year , this._month , this._day );
                    this._dataField.value = this._writeDateInFormat( );
                }
            } else {
                dataParsed = [];
                if(this._isValidDate(
                    dataParsed[0] = this._options.yearField[this._options.yearField.selectedIndex].value,
                    dataParsed[1] = this._options.monthField[this._options.monthField.selectedIndex].value,
                    dataParsed[2] = this._options.dayField[this._options.dayField.selectedIndex].value
                )){
                    dataParsed = this._checkDateRange( dataParsed[ 0 ] , dataParsed[ 1 ] - 1 , dataParsed[ 2 ] );

                    this._year  = dataParsed[ 0 ];
                    this._month = dataParsed[ 1 ];
                    this._day   = dataParsed[ 2 ];
                } else {
                    dataParsed = this._checkDateRange( dataParsed[ 0 ] , dataParsed[ 1 ] - 1 , 1 );
                    if(this._isValidDate( dataParsed[ 0 ], dataParsed[ 1 ] + 1 ,dataParsed[ 2 ] )){
                        this._year  = dataParsed[ 0 ];
                        this._month = dataParsed[ 1 ];
                        this._day   = this._daysInMonth(dataParsed[0],dataParsed[1]);

                        this.setDate();
                    }
                }
            }
            this._updateDescription();
            this._showMonth();
        },

        /**
         * Updates the date description shown at the top of the datepicker
         *
         * @method  _updateDescription
         * @private
         */
        _updateDescription: function(){
            this._monthChanger.innerHTML = this._options.month[ this._month + 1 ];
            this._deText.innerHTML = this._options.ofText;
            this._yearChanger.innerHTML = this._year;
        },

        /**
         * Renders the year selector view of the datepicker
         *
         * @method _showYearSelector
         * @private
         */
        _showYearSelector: function(){
            if (arguments.length){
                var year = + this._year + arguments[0]*10;
                year=year-year%10;
                if ( year>this._yearMax || year+9<this._yearMin ){
                    return;
                }
                this._year = + this._year + arguments[0]*10;
            }

            var str = "<li>";
            var ano_base = this._year-(this._year%10);

            for (var i=0; i<=11; i++){
                if (i % 4 === 0){
                    str+='<ul>';
                }

                if (!i || i === 11){
                    if ( i && (ano_base+i-1)<=this._yearMax && (ano_base+i-1)>=this._yearMin ){
                        str+='<li><a href="#year_next" class="change_year_next">' + this._options.nextLinkText + '</a></li>';
                    } else if( (ano_base+i-1)<=this._yearMax && (ano_base+i-1)>=this._yearMin ){
                         str+='<li><a href="#year_prev" class="change_year_prev">' + this._options.prevLinkText + '</a></li>';
                    } else {
                        str +='<li>&nbsp;</li>';
                    }
                } else {
                    if ( (ano_base+i-1)<=this._yearMax && (ano_base+i-1)>=this._yearMin ){
                        str+='<li><a href="#" class="sapo_calyear_' + (ano_base+i-1)  + (((ano_base+i-1) === this._data.getFullYear()) ? ' sapo_cal_on' : '') + '">' + (ano_base+i-1) +'</a></li>';
                    } else {
                        str+='<li><a href="#" class="sapo_cal_off">' + (ano_base+i-1) +'</a></li>';

                    }
                }

                if ((i+1) % 4 === 0) {
                    str+='</ul>';
                }
            }

            str += "</li>";

            this._yearSelector.innerHTML = str;
        },

        /**
         * This function returns the given date in an array format
         *
         * @method _getDataArrayParsed
         * @param {String} dateStr A date on a string.
         * @private
         * @return {Array} The given date in an array format
         */
        _getDataArrayParsed: function(dateStr){
            var arrData = [];
            var data = InkDate.set( this._options.format , dateStr );
            if (data) {
                arrData = [ data.getFullYear( ) , data.getMonth( ) + 1 , data.getDate( ) ];
            }
            return arrData;
        },

        /**
         * Checks if a date is valid
         *
         * @method _isValidDate
         * @param {Number} year
         * @param {Number} month
         * @param {Number} day
         * @private
         * @return {Boolean} True if the date is valid, false otherwise
         */
        _isValidDate: function(year, month, day){
            var yearRegExp = /^\d{4}$/;
            var validOneOrTwo = /^\d{1,2}$/;
            return (
                yearRegExp.test(year)     &&
                validOneOrTwo.test(month) &&
                validOneOrTwo.test(day)   &&
                month >= 1  &&
                month <= 12 &&
                day   >= 1  &&
                day   <= this._daysInMonth(year,month)
            );
        },

        /**
         * Checks if a given date is an valid format.
         *
         * @method _isDate
         * @param {String} format A date format.
         * @param {String} dateStr A date on a string.
         * @private
         * @return {Boolean} True if the given date is valid according to the given format
         */
        _isDate: function(format, dateStr){
            try {
                if (typeof format === 'undefined'){
                    return false;
                }
                var data = InkDate.set( format , dateStr );
                if( data && this._isValidDate( data.getFullYear( ) , data.getMonth( ) + 1 , data.getDate( ) ) ){
                    return true;
                }
            } catch (ex) {}

            return false;
        },


        /**
         * This method returns the date written with the format specified on the options
         *
         * @method _writeDateInFormat
         * @private
         * @return {String} Returns the current date of the object in the specified format
         */
       _writeDateInFormat:function(){
            return InkDate.get( this._options.format , this._data );
        },

        /**
         * This method allows the user to set the DatePicker's date on run-time.
         *
         * @method setDate
         * @param {String} dateString A date string in yyyy-mm-dd format.
         * @public
         */
        setDate : function( dateString )
        {
            if ( typeof dateString === 'string' && /\d{4}-\d{1,2}-\d{1,2}/.test( dateString ) )
            {
                var auxDate = dateString.split( '-' );
                this._year  = auxDate[ 0 ];
                this._month = auxDate[ 1 ] - 1;
                this._day   = auxDate[ 2 ];
            }

            this._setDate( );
        },

        /**
         * Sets the chosen date on the target input field
         *
         * @method _setDate
         * @param {DOMElement} objClicked Clicked object inside the DatePicker's calendar.
         * @private
         */
        _setDate : function( objClicked ){
            if( typeof objClicked !== 'undefined' && objClicked.className && objClicked.className.indexOf('sapo_cal_') === 0 )
            {
                this._day = objClicked.className.substr( 9 , 2 );
            }
            this._data.setFullYear.apply( this._data , this._checkDateRange( this._year , this._month , this._day ) );

            if(!this._options.displayInSelect){
                this._dataField.value = this._writeDateInFormat();
            } else {
                this._options.dayField.value   = this._data.getDate();
                this._options.monthField.value = this._data.getMonth()+1;
                this._options.yearField.value  = this._data.getFullYear();
            }
            if(this._options.onSetDate) {
                this._options.onSetDate( this , { date : this._data } );
            }
        },

        /**
         * Makes the necessary work to update the calendar
         * when choosing a different month
         *
         * @method _updateCal
         * @param {Number} inc Indicates previous or next month
         * @private
         */
        _updateCal: function(inc){
            
            if( typeof this._options.onMonthSelected === 'function' ){
                this._options.onMonthSelected(this, {
                    'year': this._year,
                    'month' : this._month
                });
            }
            this._updateMonth(inc);
            this._showMonth();
        },

        /**
         * Function that returns the number of days on a given month on a given year
         *
         * @method _daysInMonth
         * @param {Number} _y - year
         * @param {Number} _m - month
         * @private
         * @return {Number} The number of days on a given month on a given year
         */
        _daysInMonth: function(_y,_m){
            var nDays = 31;

            switch (_m) {
                case 2:
                    nDays = ((_y % 400 === 0) || (_y % 4 === 0 && _y % 100 !== 0)) ? 29 : 28;
                    break;

                case 4:
                case 6:
                case 9:
                case 11:
                    nDays = 30;
                    break;
            }

            return nDays;
        },


        /**
         * Updates the calendar when a different month is chosen
         *
         * @method _updateMonth
         * @param {Number} incValue - indicates previous or next month
         * @private
         */
        _updateMonth: function(incValue){
            if(typeof incValue === 'undefined') {
                incValue = "0";
            }

            var mes = this._month + 1;
            var ano = this._year;
            switch(incValue){
                case -1:
                    if (mes===1){
                        if(ano === this._yearMin){ return; }
                        mes=12;
                        ano--;
                    }
                    else {
                        mes--;
                    }
                    this._year  = ano;
                    this._month = mes - 1;
                    break;
                case 1:
                    if(mes === 12){
                        if(ano === this._yearMax){ return; }
                        mes=1;
                        ano++;
                    }
                    else{
                        mes++;
                    }
                    this._year  = ano;
                    this._month = mes - 1;
                    break;
                default:

            }
        },

        /**
         * Key-value object that (for a given key) points to the correct parsing format for the DatePicker
         * @property _dateParsers
         * @type {Object}
         * @readOnly
         */
        _dateParsers: {
            'yyyy-mm-dd' : 'Y-m-d' ,
            'yyyy/mm/dd' : 'Y/m/d' ,
            'yy-mm-dd'   : 'y-m-d' ,
            'yy/mm/dd'   : 'y/m/d' ,
            'dd-mm-yyyy' : 'd-m-Y' ,
            'dd/mm/yyyy' : 'd/m/Y' ,
            'dd-mm-yy'   : 'd-m-y' ,
            'dd/mm/yy'   : 'd/m/y' ,
            'mm/dd/yyyy' : 'm/d/Y' ,
            'mm-dd-yyyy' : 'm-d-Y'
        },

        /**
         * Renders the current month
         *
         * @method _showMonth
         * @private
         */
        _showMonth: function(){
            /*jshint maxstatements:100, maxcomplexity:20 */
            var i, j;
            var mes = this._month + 1;
            var ano = this._year;
            var maxDay = this._daysInMonth(ano,mes);

            var wDayFirst = (new Date( ano , mes - 1 , 1 )).getDay();

            var startWeekDay = this._options.startWeekDay || 0;

            this._monthPrev.childNodes[0].className = 'change_month_prev';
            this._monthNext.childNodes[0].className = 'change_month_next';

            if ( ano < this._yearMin || ano === this._yearMin && mes <= this._monthMin ){
                this._monthPrev.childNodes[0].className = 'action_inactive';
            }
            else if( ano > this._yearMax || ano === this._yearMax && mes >= this._monthMax ){
                this._monthNext.childNodes[0].className = 'action_inactive';
            }

            if(startWeekDay && Number(startWeekDay)){
                if(startWeekDay > wDayFirst) {
                    wDayFirst = 7 + startWeekDay - wDayFirst;
                } else {
                    wDayFirst += startWeekDay;
                }
            }

            var html = '';

            html += '<ul class="sapo_cal_header">';

            for(i=0; i<7; i++){
                html+='<li>' + this._options.wDay[i + (((startWeekDay+i)>6) ? startWeekDay-7 : startWeekDay )].substring(0,1)  + '</li>';
            }
            html+='</ul>';

            var counter = 0;
            html+='<ul>';
            if(wDayFirst){
                for(j = startWeekDay; j < wDayFirst - startWeekDay; j++) {
                    if (!counter){
                        html+='<ul>';
                    }
                    html+='<li class="sapo_cal_empty">&nbsp;</li>';
                    counter++;
                }
            }

            for (i = 1; i <= maxDay; i++) {
                if (counter === 7){
                    counter=0;
                    html+='<ul>';
                }
                var idx = 'sapo_cal_' + ((String(i).length === 2) ? i : "0" + i);
                idx += ( ano === this._yearMin && mes === this._monthMin && i < this._dayMin ||
                    ano === this._yearMax && mes === this._monthMax && i > this._dayMax ||
                    ano === this._yearMin && mes < this._monthMin ||
                    ano === this._yearMax && mes > this._monthMax ||
                    ano < this._yearMin || ano > this._yearMax || ( this._options.validDayFn && !this._options.validDayFn.call( this, new Date( ano , mes - 1 , i) ) ) ) ? " sapo_cal_off" :
                    (this._data.getFullYear( ) === ano && this._data.getMonth( ) === mes - 1 && i === this._day) ? " sapo_cal_on" : "";
                html+='<li><a href="#" class="' + idx + '">' + i + '</a></li>';

                counter++;
                if(counter === 7){
                    html+='</ul>';
                }
            }
            if (counter !== 7){
                for(i = counter; i < 7; i++){
                    html+='<li class="sapo_cal_empty">&nbsp;</li>';
                }
                html+='</ul>';
            }
            html+='</ul>';


            this._monthContainer.innerHTML = html;

        },

        /**
         * This method sets the active month
         *
         * @method _setActiveMonth
         * @param {DOMElement} parent DOMElement where all the months are.
         * @private
         */
        _setActiveMonth: function(parent){
            if (typeof parent === 'undefined') {
                parent = this._monthSelector;
            }

            var length = parent.childNodes.length;

            if (parent.className && parent.className.match(/sapo_calmonth_/)) {
                var year = this._year;
                var month = parent.className.substr( 14 , 2 );

                if ( year === this._data.getFullYear( ) && month === this._data.getMonth( ) + 1 )
                {
                    Css.addClassName( parent , 'sapo_cal_on' );
                    Css.removeClassName( parent , 'sapo_cal_off' );
                }
                else
                {
                    Css.removeClassName( parent , 'sapo_cal_on' );
                    if ( year === this._yearMin && month < this._monthMin ||
                        year === this._yearMax && month > this._monthMax ||
                        year < this._yearMin ||
                        year > this._yearMax )
                    {
                        Css.addClassName( parent , 'sapo_cal_off' );
                    }
                    else
                    {
                        Css.removeClassName( parent , 'sapo_cal_off' );
                    }
                }
            }
            else if (length !== 0){
                for (var i = 0; i < length; i++) {
                    this._setActiveMonth(parent.childNodes[i]);
                }
            }
        },

        /**
         * Prototype's method to allow the 'i18n files' to change all objects' language at once.
         * @param  {Object} options Object with the texts' configuration.
         *     @param {String} closeText Text of the close anchor
         *     @param {String} cleanText Text of the clean text anchor
         *     @param {String} prevLinkText "Previous" link's text
         *     @param {String} nextLinkText "Next" link's text
         *     @param {String} ofText The text "of", present in 'May of 2013'
         *     @param {Object} month An object with keys from 1 to 12 that have the full months' names
         *     @param {Object} wDay An object with keys from 0 to 6 that have the full weekdays' names
         * @public
         */
        lang: function( options ){
            this._lang = options;
        },

        /**
         * This calls the rendering of the selected month.
         *
         * @method showMonth
         * @public
         */
        showMonth: function(){
            this._showMonth();
        },

        /**
         * Returns true if the calendar sceen is in 'select day' mode
         * 
         * @return {Boolean} True if the calendar sceen is in 'select day' mode
         * @public
         */
        isMonthRendered: function(){
            var header = Selector.select('.sapo_cal_header',this._containerObject)[0];

            return ( (Css.getStyle(header.parentNode,'display') !== 'none') && (Css.getStyle(header.parentNode.parentNode,'display') !== 'none') );
        }
    };

    return DatePicker;

});

/**
 * @module Ink.UI.Close_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Close', '1', ['Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Util.Array_1'], function(Event, Css, InkArray ) {
    'use strict';

    /**
     * Subscribes clicks on the document.body. If and only if you clicked on an element
     * having class "ink-close" or "ink-dismiss", will go up the DOM hierarchy looking for an element with any
     * of the following classes: "ink-alert", "ink-alert-block".
     * If it is found, it is removed from the DOM.
     * 
     * One should call close once per page (full page refresh).
     * 
     * @class Ink.UI.Close
     * @constructor
     * @version 1
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Util.Array
     * @example
     *     <script>
     *         Ink.requireModules(['Ink.UI.Close_1'],function( Close ){
     *             new Close();
     *         });
     *     </script>
     */
    var Close = function() {

        Event.observe(document.body, 'click', function(ev) {
            var el = Event.element(ev);
            if (!Css.hasClassName(el, 'ink-close') && !Css.hasClassName(el, 'ink-dismiss')) { return; }

            var classes;
            do { 
                if (!el.className) { continue; }
                classes = el.className.split(' ');
                if (!classes) { continue; }
                if ( InkArray.inArray('ink-alert',       classes) ||
                     InkArray.inArray('ink-alert-block', classes) ) { break; }
            } while ((el = el.parentNode));

            if (el) {
                Event.stop(ev);
                el.parentNode.removeChild(el);
            }
        });
    };

    return Close;

});

/**
00 * @module Ink.UI.Toggle_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Toggle', '1', ['Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1'], function(Aux, Event, Css, Element, Selector ) {
    'use strict';

    /**
     * Toggle component
     * 
     * @class Ink.UI.Toggle
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *     @param {String}       options.target                    CSS Selector that specifies the elements that will toggle
     *     @param {String}       [options.triggerEvent]            Event that will trigger the toggling. Default is 'click'
     *     @param {Boolean}      [options.closeOnClick]            Flag that determines if, when clicking outside of the toggled content, it should hide it. Default: true.
     * @example
     *      <div class="ink-dropdown">
     *          <button class="ink-button toggle" data-target="#dropdown">Dropdown <span class="icon-caret-down"></span></button>
     *          <ul id="dropdown" class="dropdown-menu">
     *              <li class="heading">Heading</li>
     *              <li class="separator-above"><a href="#">Option</a></li>
     *              <li><a href="#">Option</a></li>
     *              <li class="separator-above disabled"><a href="#">Disabled option</a></li>
     *              <li class="submenu">
     *                  <a href="#" class="toggle" data-target="#submenu1">A longer option name</a>
     *                  <ul id="submenu1" class="dropdown-menu">
     *                      <li class="submenu">
     *                          <a href="#" class="toggle" data-target="#ultrasubmenu">Sub option</a>
     *                          <ul id="ultrasubmenu" class="dropdown-menu">
     *                              <li><a href="#">Sub option</a></li>
     *                              <li><a href="#" data-target="ultrasubmenu">Sub option</a></li>
     *                              <li><a href="#">Sub option</a></li>
     *                          </ul>
     *                      </li>
     *                      <li><a href="#">Sub option</a></li>
     *                      <li><a href="#">Sub option</a></li>
     *                  </ul>
     *              </li>
     *              <li><a href="#">Option</a></li>
     *          </ul>
     *      </div>
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.Toggle_1'], function( Selector, Toggle ){
     *              var toggleElement = Ink.s('.toggle');
     *              var toggleObj = new Toggle( toggleElement );
     *          });
     *      </script>
     */
    var Toggle = function( selector, options ){

        if( typeof selector !== 'string' && typeof selector !== 'object' ){
            throw '[Ink.UI.Toggle] Invalid CSS selector to determine the root element';
        }

        if( typeof selector === 'string' ){
            this._rootElement = Selector.select( selector );
            if( this._rootElement.length <= 0 ){
                throw '[Ink.UI.Toggle] Root element not found';
            }

            this._rootElement = this._rootElement[0];
        } else {
            this._rootElement = selector;
        }

        this._options = Ink.extendObj({
            target : undefined,
            triggerEvent: 'click',
            closeOnClick: true
        },Element.data(this._rootElement));

        this._options = Ink.extendObj(this._options,options || {});

        if( typeof this._options.target === 'undefined' ){
            throw '[Ink.UI.Toggle] Target option not defined';
        }

        this._childElement = Aux.elOrSelector( this._options.target, 'Target' );
        // this._childElement = Selector.select( this._options.target, this._rootElement );
        // if( this._childElement.length <= 0 ){
        //     if( this._childElement.length <= 0 ){
        //         this._childElement = Selector.select( this._options.target, this._rootElement.parentNode );
        //     }

        //     if( this._childElement.length <= 0 ){
        //         this._childElement = Selector.select( this._options.target );
        //     }

        //     if( this._childElement.length <= 0 ){
        //         return;
        //     }
        // }
        // this._childElement = this._childElement[0];

        this._init();

    };

    Toggle.prototype = {

        /**
         * Init function called by the constructor
         * 
         * @method _init
         * @private
         */
        _init: function(){

            this._accordion = ( Css.hasClassName(this._rootElement.parentNode,'accordion') || Css.hasClassName(this._childElement.parentNode,'accordion') );

            Event.observe( this._rootElement, this._options.triggerEvent, Ink.bindEvent(this._onTriggerEvent,this) );
            if( this._options.closeOnClick.toString() === 'true' ){
                Event.observe( document, 'click', Ink.bindEvent(this._onClick,this));
            }
        },

        /**
         * Event handler. It's responsible for handling the <triggerEvent> defined in the options.
         * This will trigger the toggle.
         * 
         * @method _onTriggerEvent
         * @param {Event} event
         * @private
         */
        _onTriggerEvent: function( event ){

            if( this._accordion ){
                var elms, i, accordionElement;
                if( Css.hasClassName(this._childElement.parentNode,'accordion') ){
                    accordionElement = this._childElement.parentNode;
                } else {
                    accordionElement = this._childElement.parentNode.parentNode;
                }
                elms = Selector.select('.toggle',accordionElement);
                for( i=0; i<elms.length; i+=1 ){
                    var
                        dataset = Element.data( elms[i] ),
                        targetElm = Selector.select( dataset.target,accordionElement )
                    ;
                    if( (targetElm.length > 0) && (targetElm[0] !== this._childElement) ){
                            targetElm[0].style.display = 'none';
                    }
                }
            }

            var finalClass = ( Css.getStyle(this._childElement,'display') === 'none') ? 'show-all' : 'hide-all';
            var finalDisplay = ( Css.getStyle(this._childElement,'display') === 'none') ? 'block' : 'none';
            Css.removeClassName(this._childElement,'show-all');
            Css.removeClassName(this._childElement, 'hide-all');
            Css.addClassName(this._childElement, finalClass);
            this._childElement.style.display = finalDisplay;

            if( finalClass === 'show-all' ){
                Css.addClassName(this._rootElement,'active');
            } else {
                Css.removeClassName(this._rootElement,'active');
            }
        },

        /**
         * Click handler. Will handle clicks outside the toggle component.
         * 
         * @method _onClick
         * @param {Event} event
         * @private
         */
        _onClick: function( event ){
            var
                tgtEl = Event.element(event),
                shades
            ;

            if( (this._rootElement === tgtEl) || Element.isAncestorOf( this._rootElement, tgtEl ) || Element.isAncestorOf( this._childElement, tgtEl ) ){
                return;
            } else if( (shades = Ink.ss('.ink-shade')).length ) {
                var
                    shadesLength = shades.length
                ;

                for( var i = 0; i < shadesLength; i++ ){
                    if( Element.isAncestorOf(shades[i],tgtEl) && Element.isAncestorOf(shades[i],this._rootElement) ){
                        return;
                    }
                }
            }
            
            this._dismiss( this._rootElement );
        },

        /**
         * Dismisses the toggling.
         * 
         * @method _dismiss
         * @private
         */
        _dismiss: function( ){
            if( ( Css.getStyle(this._childElement,'display') === 'none') ){
                return;
            }
            Css.removeClassName(this._childElement, 'show-all');
            Css.removeClassName(this._rootElement,'active');
            Css.addClassName(this._childElement, 'hide-all');
            this._childElement.style.display = 'none';
        }
    };

    return Toggle;

});

/**
 * @module Ink.UI.Pagination_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Pagination', '1', ['Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1'], function(Aux, Event, Css, Element, Selector ) {
    'use strict';

    /**
     * Function to create the pagination anchors
     *
     * @method genAel
     * @param  {String} inner HTML to be placed inside the anchor.
     * @return {DOMElement}  Anchor created
     */
    var genAEl = function(inner) {
        var aEl = document.createElement('a');
        aEl.setAttribute('href', '#');
        aEl.innerHTML = inner;
        return aEl;
    };

    /**
     * @class Ink.UI.Pagination
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @param {String|DOMElement} selector
     * @param {Object} options Options
     * @param {Number}   options.size                number of pages
     * @param {Number}   [options.maxSize]           if passed, only shows at most maxSize items. displays also first|prev page and next page|last buttons
     * @param {Number}   [options.start]             start page. defaults to 1
     * @param {String}   [options.previousLabel]     label to display on previous page button
     * @param {String}   [options.nextLabel]         label to display on next page button
     * @param {String}   [options.previousPageLabel] label to display on previous page button
     * @param {String}   [options.nextPageLabel]     label to display on next page button
     * @param {String}   [options.firstLabel]        label to display on previous page button
     * @param {String}   [options.lastLabel]         label to display on next page button
     * @param {Function} [options.onChange]          optional callback
     * @param {Boolean}  [options.setHash]           if true, sets hashParameter on the location.hash. default is disabled
     * @param {String}   [options.hashParameter]     parameter to use on setHash. by default uses 'page'
     */
    var Pagination = function(selector, options) {

        this._options = Ink.extendObj({
            size:          undefined,
            start:         1,
            firstLabel:    'First',
            lastLabel:     'Last',
            previousLabel: 'Previous',
            nextLabel:     'Next',
            onChange:      undefined,
            setHash:       false,
            hashParameter: 'page'
        }, options || {});

        if (!this._options.previousPageLabel) {
            this._options.previousPageLabel = 'Previous ' + this._options.maxSize;
        }

        if (!this._options.nextPageLabel) {
            this._options.nextPageLabel = 'Next ' + this._options.maxSize;
        }


        this._handlers = {
            click: Ink.bindEvent(this._onClick,this)
        };

        this._element = Aux.elOrSelector(selector, '1st argument');

        if (!Aux.isInteger(this._options.size)) {
            throw new TypeError('size option is a required integer!');
        }

        if (!Aux.isInteger(this._options.start) && this._options.start > 0 && this._options.start <= this._options.size) {
            throw new TypeError('start option is a required integer between 1 and size!');
        }

        if (this._options.maxSize && !Aux.isInteger(this._options.maxSize) && this._options.maxSize > 0) {
            throw new TypeError('maxSize option is a positive integer!');
        }

        else if (this._options.size < 0) {
            throw new RangeError('size option must be equal or more than 0!');
        }

        if (this._options.onChange !== undefined && typeof this._options.onChange !== 'function') {
            throw new TypeError('onChange option must be a function!');
        }

        this._current = this._options.start - 1;
        this._itemLiEls = [];

        this._init();
    };

    Pagination.prototype = {

        /**
         * Init function called by the constructor
         * 
         * @method _init
         * @private
         */
        _init: function() {
            // generate and apply DOM
            this._generateMarkup(this._element);
            this._updateItems();

            // subscribe events
            this._observe();

            Aux.registerInstance(this, this._element, 'pagination');
        },

        /**
         * Responsible for setting listener in the 'click' event of the Pagination element.
         * 
         * @method _observe
         * @private
         */
        _observe: function() {
            Event.observe(this._element, 'click', this._handlers.click);
        },

        /**
         * Updates the markup everytime there's a change in the Pagination object.
         * 
         * @method _updateItems
         * @private
         */
        _updateItems: function() {
            var liEls = this._itemLiEls;

            var isSimpleToggle = this._options.size === liEls.length;

            var i, f, liEl;

            if (isSimpleToggle) {
                // just toggle active class
                for (i = 0, f = this._options.size; i < f; ++i) {
                    Css.setClassName(liEls[i], 'active', i === this._current);
                }
            }
            else {
                // remove old items
                for (i = liEls.length - 1; i >= 0; --i) {
                    this._ulEl.removeChild(liEls[i]);
                }

                // add new items
                liEls = [];
                for (i = 0, f = this._options.size; i < f; ++i) {
                    liEl = document.createElement('li');
                    liEl.appendChild( genAEl( i + 1 ) );
                    Css.setClassName(liEl, 'active', i === this._current);
                    this._ulEl.insertBefore(liEl, this._nextEl);
                    liEls.push(liEl);
                }
                this._itemLiEls = liEls;
            }

            if (this._options.maxSize) {
                // toggle visible items
                var page = Math.floor( this._current / this._options.maxSize );
                var pi = this._options.maxSize * page;
                var pf = pi + this._options.maxSize - 1;

                for (i = 0, f = this._options.size; i < f; ++i) {
                    liEl = liEls[i];
                    Css.setClassName(liEl, 'hide-all', i < pi || i > pf);
                }

                this._pageStart = pi;
                this._pageEnd = pf;
                this._page = page;

                Css.setClassName(this._prevPageEl, 'disabled', !this.hasPreviousPage());
                Css.setClassName(this._nextPageEl, 'disabled', !this.hasNextPage());

                Css.setClassName(this._firstEl, 'disabled', this.isFirst());
                Css.setClassName(this._lastEl, 'disabled', this.isLast());
            }

            // update prev and next
            Css.setClassName(this._prevEl, 'disabled', !this.hasPrevious());
            Css.setClassName(this._nextEl, 'disabled', !this.hasNext());
        },

        /**
         * Returns the top element for the gallery DOM representation
         * 
         * @method _generateMarkup
         * @param {DOMElement} el
         * @private
         */
        _generateMarkup: function(el) {
            Css.addClassName(el, 'ink-navigation');

            var
                ulEl,liEl,
                hasUlAlready = false
            ;
            if( ( ulEl = Selector.select('ul.pagination',el)).length < 1 ){
                ulEl = document.createElement('ul');
                Css.addClassName(ulEl, 'pagination');
            } else {
                hasUlAlready = true;
                ulEl = ulEl[0];
            }

            if (this._options.maxSize) {
                liEl = document.createElement('li');
                liEl.appendChild( genAEl(this._options.firstLabel) );
                this._firstEl = liEl;
                Css.addClassName(liEl, 'first');
                ulEl.appendChild(liEl);

                liEl = document.createElement('li');
                liEl.appendChild( genAEl(this._options.previousPageLabel) );
                this._prevPageEl = liEl;
                Css.addClassName(liEl, 'previousPage');
                ulEl.appendChild(liEl);
            }

            liEl = document.createElement('li');
            liEl.appendChild( genAEl(this._options.previousLabel) );
            this._prevEl = liEl;
            Css.addClassName(liEl, 'previous');
            ulEl.appendChild(liEl);

            liEl = document.createElement('li');
            liEl.appendChild( genAEl(this._options.nextLabel) );
            this._nextEl = liEl;
            Css.addClassName(liEl, 'next');
            ulEl.appendChild(liEl);

            if (this._options.maxSize) {
                liEl = document.createElement('li');
                liEl.appendChild( genAEl(this._options.nextPageLabel) );
                this._nextPageEl = liEl;
                Css.addClassName(liEl, 'nextPage');
                ulEl.appendChild(liEl);

                liEl = document.createElement('li');
                liEl.appendChild( genAEl(this._options.lastLabel) );
                this._lastEl = liEl;
                Css.addClassName(liEl, 'last');
                ulEl.appendChild(liEl);
            }

            if( !hasUlAlready ){
                el.appendChild(ulEl);
            }

            this._ulEl = ulEl;
        },

        /**
         * Click handler
         * 
         * @method _onClick
         * @param {Event} ev
         * @private
         */
        _onClick: function(ev) {
            Event.stop(ev);

            var tgtEl = Event.element(ev);
            if (tgtEl.nodeName.toLowerCase() !== 'a') {
                do{
                    tgtEl = tgtEl.parentNode;
                }while( (tgtEl.nodeName.toLowerCase() !== 'a') && (tgtEl !== this._element) );
                
                if( tgtEl === this._element){
                    return;
                }
            }

            var liEl = tgtEl.parentNode;
            if (liEl.nodeName.toLowerCase() !== 'li') { return; }

            if ( Css.hasClassName(liEl, 'active') ||
                 Css.hasClassName(liEl, 'disabled') ) { return; }

            var isPrev = Css.hasClassName(liEl, 'previous');
            var isNext = Css.hasClassName(liEl, 'next');
            var isPrevPage = Css.hasClassName(liEl, 'previousPage');
            var isNextPage = Css.hasClassName(liEl, 'nextPage');
            var isFirst = Css.hasClassName(liEl, 'first');
            var isLast = Css.hasClassName(liEl, 'last');

            if (isFirst) {
                this.setCurrent(0);
            }
            else if (isLast) {
                this.setCurrent(this._options.size - 1);
            }
            else if (isPrevPage || isNextPage) {
                this.setCurrent( (isPrevPage ? -1 : 1) * this._options.maxSize, true);
            }
            else if (isPrev || isNext) {
                this.setCurrent(isPrev ? -1 : 1, true);
            }
            else {
                var nr = parseInt( tgtEl.innerHTML, 10) - 1;
                this.setCurrent(nr);
            }
        },



        /**************
         * PUBLIC API *
         **************/

        /**
         * Sets the number of pages
         * 
         * @method setSize
         * @param {Number} sz number of pages
         * @public
         */
        setSize: function(sz) {
            if (!Aux.isInteger(sz)) {
                throw new TypeError('1st argument must be an integer number!');
            }

            this._options.size = sz;
            this._updateItems();
            this._current = 0;
        },

        /**
         * Sets the current page
         * 
         * @method setCurrent
         * @param {Number} nr sets the current page to given number
         * @param {Boolean} isRelative trueish to set relative change instead of absolute (default)
         * @public
         */
        setCurrent: function(nr, isRelative) {
            if (!Aux.isInteger(nr)) {
                throw new TypeError('1st argument must be an integer number!');
            }

            if (isRelative) {
                nr += this._current;
            }

            if (nr < 0) {
                nr = 0;
            }
            else if (nr > this._options.size - 1) {
                nr = this._options.size - 1;
            }
            this._current = nr;
            this._updateItems();

            /*if (this._options.setHash) {
                var o = {};
                o[this._options.hashParameter] = nr;
                Aux.setHash(o);
            }*/

            if (this._options.onChange) { this._options.onChange(this); }
        },

        /**
         * Returns the number of pages
         * 
         * @method getSize
         * @return {Number} Number of pages
         * @public
         */
        getSize: function() {
            return this._options.size;
        },

        /**
         * Returns current page
         * 
         * @method getCurrent
         * @return {Number} Current page
         * @public
         */
        getCurrent: function() {
            return this._current;
        },

        /**
         * Returns true iif at first page
         * 
         * @method isFirst
         * @return {Boolean} True if at first page
         * @public
         */
        isFirst: function() {
            return this._current === 0;
        },

        /**
         * Returns true iif at last page
         * 
         * @method isLast
         * @return {Boolean} True if at last page
         * @public
         */
        isLast: function() {
            return this._current === this._options.size - 1;
        },

        /**
         * Returns true iif has prior pages
         * 
         * @method hasPrevious
         * @return {Boolean} True if has prior pages
         * @public
         */
        hasPrevious: function() {
            return this._current > 0;
        },

        /**
         * Returns true iif has pages ahead
         * 
         * @method hasNext
         * @return {Boolean} True if has pages ahead
         * @public
         */
        hasNext: function() {
            return this._current < this._options.size - 1;
        },

        /**
         * Returns true iif has prior set of page(s)
         * 
         * @method hasPreviousPage
         * @return {Boolean} Returns true iif has prior set of page(s)
         * @public
         */
        hasPreviousPage: function() {
            return this._options.maxSize && this._current > this._options.maxSize - 1;
        },

        /**
         * Returns true iif has set of page(s) ahead
         * 
         * @method hasNextPage
         * @return {Boolean} Returns true iif has set of page(s) ahead
         * @public
         */
        hasNextPage: function() {
            return this._options.maxSize && this._options.size - this._current >= this._options.maxSize + 1;
        },

        /**
         * Unregisters the component and removes its markup from the DOM
         * 
         * @method destroy
         * @public
         */
        destroy: Aux.destroyComponent
    };

    return Pagination;

});
