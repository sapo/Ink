/**
 * Cross Browser Ajax requests
 * @module Ink.Net.Ajax_1
 * @version 1
 */

Ink.createModule('Ink.Net.Ajax', '1', [], function() {
    'use strict';

    /**
     * Creates a new XMLHttpRequest object
     *
     * @class Ink.Net.Ajax
     * @constructor
     *
     * @param {String}          url                             Request URL
     * @param {Object}          [options]                       Request options, containing:
     * @param {Boolean}         [options.asynchronous=true]     If false, the request synchronous.
     * @param {String}          [options.contentType]           Content-type header to be sent. Defaults to 'application/x-www-form-urlencoded'
     * @param {Boolean}         [options.cors]                  Flag to activate CORS. Set this to true if you're doing a cross-origin request
     * @param {Boolean}         [options.validateCors]          If this is set to `true`, perform a CORS request automatically based on the URL being cross-domain or not.
     * @param {Number}          [options.delay]                 Artificial delay. If the request is completed faster than this delay, wait the remaining time before executing the callbacks
     * @param {Boolean|String}  [options.evalJS=true]           If the request Content-type header is application/json, evaluates the response and populates responseJSON. Use 'force' if you want to force the response evaluation, no matter what Content-type it's using.
     * @param {String}          [options.method='POST']         HTTP request method. POST by default.
     * @param {Object|String}   [options.parameters]            Request parameters to be sent with the request
     * @param {String}          [options.postBody]              POST request body. If not specified, it's filled with the contents from parameters
     * @param {Object}          [options.requestHeaders]        Key-value pairs for additional request headers
     * @param {Boolean}         [options.sanitizeJSON]          Flag to sanitize the content of responseText before evaluation
     * @xparam {Boolean}        [options.signRequest=false]     Send a "X-Requested-With: XMLHttpRequest" header in the request.
     * @param {Number}          [options.timeout]               Request timeout in seconds
     * @param {String}          [options.xhrProxy]              URI for proxy service hosted on the same server as the web app, that can fetch documents from other domains. The service must pipe all input and output untouched (some input sanitization is allowed, like clearing cookies). e.g., requesting http://example.org/doc can become /proxy/http%3A%2F%2Fexample.org%2Fdoc The proxy service will be used for cross-domain requests, if set, else a network error is returned as exception.
     * @param {Function}        [options.onComplete]            Callback executed after the request is completed, regardless of what happened during the request.
     * @param {Function}        [options.onCreate]              Callback executed after object initialization but before the request is made
     * @param {Function}        [options.onException]           Callback executed if an exception occurs. Receives the exception as a parameter.
     * @param {Function}        [options.onFailure]             Callback executed if the request fails (requests with status codes different from 2xx)
     * @param {Function}        [options.onHeaders]             Callback executed when headers of the response arrive.
     * @param {Function}        [options.onInit]                Callback executed before any initialization
     * @param {Function}        [options.onSuccess]             Callback executed if the request is successful (requests with 2xx status codes)
     * @param {Function}        [options.onTimeout]             Callback executed if the request times out
     *
     * @sample Ink_Net_Ajax_1.html 
     */
    var Ajax = function(url, options){
        this.init(url, options);
    };

    /**
    * Options for all requests. These can then be overriden for individual ones.
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
                throw new Error("new Ink.Net.Ajax: Pass a url as the first argument!");
            }
            var options = Ink.extendObj({
                asynchronous: true,
                contentType:  'application/x-www-form-urlencoded',
                cors: false,
                validateCors: false,
                debug: false,
                delay: 0,
                evalJS: true,
                method: 'POST',
                parameters: null,
                postBody: '',
                requestHeaders: null,
                sanitizeJSON: false,
                signRequest: false,
                timeout: 0,
                useCredentials: false,
                xhrProxy: '',
                onComplete: null,
                onCreate: null,
                onException: null,
                onFailure: null,
                onHeaders: null,
                onInit: null,
                onSuccess: null,
                onTimeout: null
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

            this.url = url;

            var urlLocation = this._locationFromURL(url);
            this.isHTTP = this._locationIsHTTP(urlLocation);
            this.isCrossDomain = this._locationIsCrossDomain(urlLocation, location);

            this.requestHasBody = options.method.search(/^get|head$/i) < 0;

            if (this.options.validateCors === true) {
                this.options.cors = this.isCrossDomain;
            }

            if(this.options.cors) {
                this.isCrossDomain = false;
            }

            this.transport = this.getTransport();

            this.request();
        },

        /**
         * Returns a location object from an URL
         *
         * @method _locationFromUrl
         * @param {String} url Input url
         * @return {Location} An `<a>` element with `href` set to the given URL.
         * @private
         **/
        _locationFromURL: function (url) {
            var urlLocation =  document.createElementNS ?
                document.createElementNS('http://www.w3.org/1999/xhtml', 'a') :
                document.createElement('a');
            urlLocation.setAttribute('href', url);
            return urlLocation;
        },

        /**
         * Checks whether a location is HTTP or HTTPS
         *
         * @method locationIsHttp
         * @param {Location} urlLocation Location object or `<a>` element representing the current location.
         * @return {Boolean} `true` if the location is HTTP or HTTPS, `false` otherwise.
         * @private
         */
        _locationIsHTTP: function (urlLocation) {
            return urlLocation.href.match(/^https?:/i) ? true : false;
        },

        /**
         * Checks whether a location is cross-domain from ours.
         *
         * @method _locationIsCrossDomain
         * @param {Location} urlLocation A Location object or an `<a>` elemnt.
         * @param {Location} [location=window.location] A location representing this one. This argument only exists for testing. Don't use it.
         * @return {Boolean} `true` if the locations are in different domains (in which case we need to perform a cross-domain request)
         * @private
         */
        _locationIsCrossDomain: function (urlLocation, location) {
            // TODO because of oldIE compatibility, we can only use <a>.href (the full URL), and none of the other useful properties one can find in Location elements. So we should just pass pure strings around. Not only here.
            location = location || window.location;
            if (!Ajax.prototype._locationIsHTTP(urlLocation) || location.protocol === 'widget:' || typeof window.widget === 'object') {
                return false;
            } else {
                var split1 = urlLocation.href.split('//');
                var split2 = location.href.split('//');

                if (split1.length === 1 || split2.length === 1) {
                    // This occurs when there's no protocol string in either URL
                    // Only happens in IE7 because setting the "href" of a link doesn't make that link show you the full URL when the URI is relative to this host.
                    // So we have our answer.
                    // If there's no protocol string
                    // We know for sure that our `urlLocation` is relative
                    // In which case, they are in the same domain.
                    return false;
                }

                var protocol1 = split1[0];
                var protocol2 = split2[0];

                var colonOrSlash = /:|\//;  // Finds colons or slashes, which are the end of hostnames (without ports)

                var host1 = split1[1].split(colonOrSlash)[0];
                var host2 = split2[1].split(colonOrSlash)[0];

                return protocol1 !== protocol2 ||
                    host1 !== host2;
            }
        },

        /**
         * Creates the appropriate XMLHttpRequest object, depending on our browser and whether we're trying to perform a cross-domain request.
         *
         * @method getTransport
         * @return {Object} XMLHttpRequest object
         * @private
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
         * Set the necessary headers for an ajax request.
         *
         * @method setHeaders
         * @return {void}
         */
        setHeaders: function()
        {
            if (this.transport) {
                try {
                    var headers = {
                        "Accept": "text/javascript,text/xml,application/xml,application/xhtml+xml,text/html,application/json;q=0.9,text/plain;q=0.8,video/x-mng,image/png,image/jpeg,image/gif;q=0.2,*/*;q=0.1",
                        "Accept-Language": navigator.language,
                        "X-Requested-With": "XMLHttpRequest",
                        "X-Ink-Version": "3"
                    };
                    if (this.options.cors) {
                        if (!this.options.signRequest) {
                            delete headers['X-Requested-With'];
                        }
                        delete headers['X-Ink-Version'];
                    }

                    if (this.options.requestHeaders && typeof this.options.requestHeaders === 'object') {
                        for(var headerReqName in this.options.requestHeaders) {
                            if (this.options.requestHeaders.hasOwnProperty(headerReqName)) {
                                headers[headerReqName] = this.options.requestHeaders[headerReqName];
                            }
                        }
                    }

                    if (this.transport.overrideMimeType && (navigator.userAgent.match(/Gecko\/(\d{4})/) || [0,2005])[1] < 2005) {
                        headers.Connection = 'close';
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
         * @method paramsObjToStr
         * @param {Object} optParams Parameters object, example: `{ a: 2, b: 3 }`
         * @return {String} A query string. Example: `'a=2&b=3'`
         * @private
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
                                    encodeURIComponent(p), '[]',   '=',
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
         * Set the url parameters for a GET request
         *
         * @method setParams
         * @return {void}
         * @private
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
         * Gets an HTTP header from the response
         *
         * @method getHeader
         * @param {String} name Header name
         * @return {String} Header content
         * @public
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
         * Gets all the HTTP headers from the response
         *
         * @method getAllHeaders
         * @return {String} The headers, each separated by a newline
         * @public
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
         * Gets the ajax response object
         *
         * @method getResponse
         * @return {Object} The response object
         * @public
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
         * @method abort
         * @return {void}
         * @public
         */
        abort: function(){
            if (this.transport) {
                clearTimeout(this.delayTimeout);
                clearTimeout(this.stoTimeout);
                this._aborted = true;
                try { this.transport.abort(); } catch(ex) {}
                this.finish();
            }
        },

        /**
         * Executes the state changing phase of an ajax request
         *
         * @method runStateChange
         * @return {void}
         * @public
         */
        runStateChange: function() {
            if (this._aborted) { return; }  // We don't care!
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
                        this.safeCall('onException', new Error('Ink.Net.Ajax: network error! (HTTP status 0)'));
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

                if (this.transport.responseXML != null && response.responseJSON == null && this.transport.responseXML.xml !== ""){
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
         * @method finish
         * @param {Mixed} response Response object as returned from getResponse().
         * @param {Mixed} responseContent Content of the response.
         * @return {void}
         * @private
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
         * If you pass in an error as the second argument, it gets thrown if there is no default listener.
         *
         * @method safeCall
         * @param {Function}  handlerName Name of the handler we wish to call
         * @param {Error}     error     This error gets reported to the console using Ink.error if there's no listener to `handlerName`.
         * @param {Mixed}     [args...] Arguments to get passed to the `handlerName` handler.
         * @return {void}
         * @private
         */
        safeCall: function(handlerName /*[error or rest...]*/) {
            var error = arguments[1] instanceof Error ? arguments[1] : null;
            if (typeof this.options[handlerName] === 'function') {
                try {
                    this.options[handlerName].apply(this, [].slice.call(arguments, 1));
                } catch(ex) {
                    Ink.error('Ink.Net.Ajax: an error was raised while executing ' + handlerName + '.', ex);
                }
            } else if (error) {
                Ink.error('Ink.Net.Ajax: ' + error);
            }
        },

        /**
         * Sets a new request header for the next http request
         *
         * @method setRequestHeader
         * @param {String} name Header name.
         * @param {String} value New header value.
         * @return {void}
         * @public
         */
        setRequestHeader: function(name, value){
            if (!this.options.requestHeaders) {
                this.options.requestHeaders = {};
            }
            this.options.requestHeaders[name] = value;
        },

        /**
         * Executes the request
         *
         * @method request
         * @return {void}
         * @private
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
                        Ink.error('Ink.Net.Ajax: You are attempting to request a URL which is cross-domain from this one. To do this, you *must* enable the `cors` option!');
                        return;
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
         * Checks if a given string is valid JSON
         *
         * @method isJSON
         * @param {String} str  String to be evaluated
         * @return {Boolean}    True if the string is valid JSON
         * @public
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
         * @method evalJSON
         * @param {String}  strJSON  String to be evaluated
         * @param {Boolean} sanitize Flag to sanitize the content
         * @return {Object} JSON content as an object
         * @public
         */
        evalJSON: function(strJSON, sanitize)
        {
            if (strJSON && (!sanitize || this.isJSON(strJSON))) {
                try {
                    if (typeof JSON  !== "undefined" && typeof JSON.parse !== 'undefined'){
                        return JSON.parse(strJSON);
                    }
                    /*jshint evil:true */
                    return eval('(' + strJSON + ')');
                } catch(e) {
                    throw new Error('Ink.Net.Ajax: Bad JSON string. ' + e);
                }
            }
            return null;
        }
    };

    /**
     * Loads content from a given url through an XMLHttpRequest.
     *
     * Shortcut function for simple AJAX use cases. Works with JSON, XML and plain text.
     *
     * @method load
     * @param {String}   url        Request URL
     * @param {Function} callback   Callback to be executed if the request is successful
     * @return {Object}             XMLHttpRequest object
     * @public
     *
     * @sample Ink_Net_Ajax_load.html 
     */
    Ajax.load = function(url, callback){
        var isCrossDomain = Ajax.prototype._locationIsCrossDomain(window.location, Ajax.prototype._locationFromURL(url));
        return new Ajax(url, {
            method: 'GET',
            cors: isCrossDomain,
            onSuccess: function(response){
                callback(response.responseJSON || response.responseText, response);
            }
        });
    };

    /**
     * Loads content from a given url through an XMLHttpRequest.
     * Shortcut function for simple AJAX use cases.
     *
     * @method ping
     * @param {String}   url        Request url
     * @param {Function} callback   Callback to be executed if the request is successful
     * @public
     * @return {Object}             XMLHttpRequest object
     */
    Ajax.ping = function(url, callback){
        var isCrossDomain = Ajax.prototype._locationIsCrossDomain(window.location, Ajax.prototype._locationFromURL(url));
        return new Ajax(url, {
            method: 'HEAD',
            cors: isCrossDomain,
            onSuccess: function(response){
                if (typeof callback === 'function'){
                    callback(response);
                }
            }
        });
    };


    return Ajax;
});
