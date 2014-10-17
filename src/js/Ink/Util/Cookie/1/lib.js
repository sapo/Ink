/**
 * Cookie Utilities
 * @module Ink.Util.Cookie_1
 * @version 1
 */

Ink.createModule('Ink.Util.Cookie', '1', [], function() {
    'use strict';

    /**
     * @namespace Ink.Util.Cookie_1
     */
    var Cookie = {

        /**
         * Gets an object with the current page cookies, or a specific cookie if you specify the `name`.
         *
         * @method get
         * @param   {String}          [name]    The cookie name.
         * @return  {String|Object}             If the name is specified, it returns the value of that key. Otherwise it returns the full cookie object
         * @public
         * @static
         * @sample Ink_Util_Cookie_get.html
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
                    }
                }
                if(name) {
                    if(typeof(_Cookie[name]) !== 'undefined') {
                        return _Cookie[name];
                    } else {
                        return null;
                    }
                }
            }
            return _Cookie;
        },

        /**
         * Sets a cookie.
         *
         * @method set
         * @param {String}      name        Cookie name.
         * @param {String}      value       Cookie value.
         * @param {Number}      [expires]   Number of seconds the cookie will be valid for.
         * @param {String}      [path]      Path for the cookie. Defaults to '/'.
         * @param {String}      [domain]    Domain for the cookie. Defaults to current hostname.
         * @param {Boolean}     [secure]    Flag for secure. Default 'false'.
         * @return {void}
         * @public
         * @static
         * @sample Ink_Util_Cookie_set.html
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

            if(domain) {
                sDomain = 'domain='+domain;
            } else if (/\./.test(window.location.hostname)) {
                // When trying to set domain=localhost or any other domain
                // without dots, setting the cookie fails.
                // Anyways, the cookies are bound to the current domain by default so let it be.
                sDomain = 'domain='+window.location.hostname;
            }

            if(secure && typeof(secure) !== 'undefined') {
                sSecure = secure;
            } else {
                sSecure = false;
            }

            document.cookie = sName +
                '; ' + sExpires +
                '; ' + sPath +
                (sDomain ? '; ' + sDomain : '') +
                '; ' + sSecure;
        },

        /**
         * Deletes a cookie.
         *
         * @method remove
         * @param {String}  cookieName   Cookie name.
         * @param {String}  [path]       Path of the cookie. Defaults to '/'.
         * @param {String}  [domain]     Domain of the cookie. Defaults to current hostname.
         * @return {void}
         * @public
         * @static
         * @sample Ink_Util_Cookie_remove.html
         */
        remove: function(cookieName, path, domain) {
            var expiresDate = -1;

            this.set(cookieName, 'deleted', expiresDate, path, domain);
        }
    };

    return Cookie;

});
