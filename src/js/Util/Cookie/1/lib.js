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
