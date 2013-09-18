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
