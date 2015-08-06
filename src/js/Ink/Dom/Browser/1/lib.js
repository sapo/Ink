/**
 * Browser Detection and User Agent sniffing
 * @module Ink.Dom.Browser_1
 * @version 1
 */
Ink.createModule('Ink.Dom.Browser', '1', [], function() {
    'use strict';    

    /**
     * @namespace Ink.Dom.Browser
     * @version 1
     * @static
     * @example
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
         * The specific browser model.
         * False if it is unavailable.
         *
         * @property model
         * @type {Boolean|String}
         * @public
         * @static
         */
        model: false,

        /**
         * The browser version.
         * False if it is unavailable.
         *
         * @property version
         * @type {Boolean|String}
         * @public
         * @static
         */
        version: false,

        /**
         * The user agent string.
         * False if it is unavailable.
         *
         * @property userAgent
         * @type {Boolean|String}
         * @public
         * @static
         */
        userAgent: false,

        /**
         * The CSS prefix (-moz-, -webkit-, -ms-, ...)
         * False if it is unavailable 
         *
         * @property cssPrefix 
         * @type {Boolean|String}
         * @public 
         * @static 
         */
        cssPrefix: false, 

        /**
         * The DOM prefix (Moz, Webkit, ms, ...)
         * False if it is unavailable 
         * @property domPrefix 
         * @type {Boolean|String}
         * @public 
         * @static 
         */
        domPrefix: false,

        /**
         * Initialization function for the Browser object.
         *
         * Is called automatically when this module is loaded, and calls setDimensions, setBrowser and setReferrer.
         *
         * @method init
         * @return {void}
         * @public
         */
        init: function() {
            this.detectBrowser();
            this.setDimensions();
            this.setReferrer();
        },

        /**
         * Retrieves and stores window dimensions in this object. Called automatically when this module is loaded.
         *
         * @method setDimensions
         * @return {void}
         * @public
         */
        setDimensions: function() {
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
         * Stores the referrer. Called automatically when this module is loaded.
         *
         * @method setReferrer
         * @return {void}
         * @public
         */
        setReferrer: function() {
            if (document.referrer && document.referrer.length) {
                this.referrer = window.escape(document.referrer);
            } else {
                this.referrer = false;
            }
        },

        /**
         * Detects the browser and stores the found properties. Called automatically when this module is loaded.
         *
         * @method detectBrowser
         * @return {void}
         * @public
         */
        detectBrowser: function() {
            this._sniffUserAgent(navigator.userAgent);
        },

        _sniffUserAgent: function (sAgent) {
            this.userAgent = sAgent;

            sAgent = sAgent.toLowerCase();

            if (/applewebkit\//.test(sAgent) && !/iemobile/.test(sAgent)) {
                this.cssPrefix = '-webkit-';
                this.domPrefix = 'Webkit';
                if(/(chrome|crios)\//.test(sAgent)) {
                    // Chrome
                    this.CHROME = true;
                    this.model = 'chrome';
                    this.version = sAgent.replace(/(.*)chrome\/([^\s]+)(.*)/, "$2");
                } else {
                    // Safari
                    this.SAFARI = true;
                    this.model = 'safari';
                    var rVersion = /version\/([^) ]+)/;
                    if (rVersion.test(sAgent)) {
                        this.version = sAgent.match(rVersion)[1];
                    } else {
                        this.version = sAgent.replace(/(.*)applewebkit\/([^\s]+)(.*)/, "$2");
                    }
                }
            } else if (/opera/.test(sAgent)) {
                // Opera
                this.OPERA = true;
                this.model = 'opera';
                this.version = sAgent.replace(/(.*)opera.([^\s$]+)(.*)/, "$2");
                this.cssPrefix = '-o-';
                this.domPrefix = 'O';
            } else if (/konqueror/.test(sAgent)) {
                // Konqueroh
                this.KONQUEROR = true;
                this.model = 'konqueror';
                this.version = sAgent.replace(/(.*)konqueror\/([^;]+);(.*)/, "$2");
                this.cssPrefix = '-khtml-';
                this.domPrefix = 'Khtml';
            } else if (/(msie|trident)/i.test(sAgent)) {
                // MSIE
                this.IE = true;
                this.model = 'ie';
                if (/rv:((?:\d|\.)+)/.test(sAgent)) {  // IE 11
                    this.version = sAgent.match(/rv:((?:\d|\.)+)/)[1];
                } else {
                    this.version = sAgent.replace(/(.*)\smsie\s([^;]+);(.*)/, "$2");
                }
                this.cssPrefix = '-ms-';
                this.domPrefix = 'ms';
            } else if (/gecko/.test(sAgent)) {
                // GECKO
                // Supports only:
                // Camino, Chimera, Epiphany, Minefield (firefox 3), Firefox, Firebird, Phoenix, Galeon,
                // Iceweasel, K-Meleon, SeaMonkey, Netscape, Songbird, Sylera,
                this.cssPrefix = '-moz-';
                this.domPrefix = 'Moz';

                this.GECKO = true;

                var re = /(camino|chimera|epiphany|minefield|firefox|firebird|phoenix|galeon|iceweasel|k\-meleon|seamonkey|netscape|songbird|sylera)/;
                if(re.test(sAgent)) {
                    this.model = sAgent.match(re)[1];
                    this.version = sAgent.replace(new RegExp("(.*)"+this.model+"\/([^;\\s$]+)(.*)"), "$2");
                } else {
                    // probably is mozilla
                    this.model = 'mozilla';
                    var reVersion = /(.*)rv:([^)]+)(.*)/;
                    if(reVersion.test(sAgent)) {
                        this.version = sAgent.replace(reVersion, "$2");
                    }
                }
            }
        },

        /**
         * Debug function which displays browser (and Ink.Dom.Browser) information as an alert message.
         *
         * @method debug
         * @return {void}
         * @public
         * @sample Ink_Dom_Browser_1_debug.html
         */
        debug: function() {
            /*global alert:false */
            var str = "known browsers: (ie, gecko, opera, safari, konqueror) \n";
            str += [this.IE, this.GECKO, this.OPERA, this.SAFARI, this.KONQUEROR] +"\n";
            str += "cssPrefix -> "+this.cssPrefix+"\n";
            str += "domPrefix -> "+this.domPrefix+"\n";
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
