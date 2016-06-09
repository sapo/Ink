/**
 * Internationalization Utilities 
 * @module Ink.Util.I18n_1
 * @version 1
 */

Ink.createModule('Ink.Util.I18n', '1', [], function () {
    'use strict';

    var pattrText = /\{(?:(\{.*?})|(?:%s:)?(\d+)|(?:%s)?|([\w-]+))}/g;

    var funcOrVal = function( ret , args ) {
        if ( typeof ret === 'function' ) {
            return ret.apply(this, args);
        } else if (typeof ret !== 'undefined') {
            return ret;
        } else {
            return '';
        }
    };

    /**
     * You can use this module to internationalize your applications. It roughly emulates GNU gettext's API.
     *
     * @class Ink.Util.I18n
     * @constructor
     *
     * @param {Object} dict         Object mapping language codes (in the form of `pt_PT`, `pt_BR`, `fr`, `en_US`, etc.) to their `dictionaries`
     * @param {String} [lang='pt_PT'] language code of the target language
     * @param {Boolean} [testMode=false] Sets the test mode (see `testMode()`) on construction.
     *
     * @sample Ink_Util_I18n_1.html
     */
    var I18n = function( dict , lang , testMode ) {
        if ( !( this instanceof I18n ) ) { return new I18n( dict , lang , testMode ); }

        this.reset( )
            .lang( lang )
            .testMode( testMode )
            .append( dict || { } , lang );
    };

    I18n.prototype = {
        reset: function( ) {
            this._dicts    = [ ];
            this._dict     = { };
            this._testMode = false;
            this._lang     = this._gLang;

            return this;
        },

        clone: function () {
            var theClone = new I18n();
            for (var i = 0, len = this._dicts.length; i < len; i++) {
                theClone.append(this._dicts[i]);
            }
            theClone.testMode(this.testMode());
            theClone.lang(this.lang());
            return theClone;
        },

        /**
         * Adds translation strings for the helper to use.
         *
         * @method append
         * @param   {Object} dict Object containing language objects identified by their language code
         * @return {I18n} (itself)
         *
         * @sample Ink_Util_I18n_1_append.html
         */
        append: function( dict ) {
            this._dicts.push( dict );

            this._dict = Ink.extendObj(this._dict , dict[ this._lang ] );

            return this;
        },
        /**
         * Gets or sets the language.
         * If there are more dictionaries available in cache, they will be loaded.
         *
         * @method lang
         * @param  {String}    [lang]    Language code to set this instance to. Omit this argument if you want to get the language code instead.
         * @return {String|I18n} The language code, if called without arguments, or this I18n instance if called with an argument.
         */
        lang: function( lang ) {
            if ( !arguments.length ) { return this._lang; }

            if ( lang && this._lang !== lang ) {
                this._lang = lang;

                this._dict = { };

                for ( var i = 0, l = this._dicts.length; i < l; i++ ) {
                    this._dict = Ink.extendObj( this._dict , this._dicts[ i ][ lang ] || { } );
                }
            }

            return this;
        },
        /**
         * Sets or unsets test mode.
         * In test mode, unknown strings are wrapped in `[ ... ]`. This is useful for debugging your application and to make sure all your translation keys are in place.
         *
         * @method  testMode
         * @param   {Boolean} [newTestMode] Flag to set the test mode state. Omit this argument to *get* the current testMode instead.
         * @return {String|I18n} The current testMode, if called without arguments, or this I18n instance if called with an argument.
         *
         */
        testMode: function( newTestMode ) {
            if ( !arguments.length ) { return !!this._testMode; }

            if ( newTestMode !== undefined  ) { this._testMode = !!newTestMode; }

            return this;
        },

        /**
         * Gest a key from the current dictionary
         *
         * @method getKey
         * @param {String} key Key you wish to get from the dictionary.
         * @return {Mixed} The object which happened to be in the current language dictionary on the given key.
         *
         * @sample Ink_Util_I18n_1_getKey.html
         */
        getKey: function( key ) {
            var ret;
            var gLang = this._gLang;
            var lang  = this._lang;
    
            if ( key in this._dict ) {
                ret = this._dict[ key ];
            } else {
                I18n.langGlobal( lang );
    
                ret = this._gDict[ key ];
    
                I18n.langGlobal( gLang );
            }
    
            return ret;
        },

        /**
         * Translates a string.
         * Given a translation key, return a translated string, with replaced parameters.
         * When a translated string is not available, the original string is returned unchanged.
         *
         * @method text
         * @param {String} str          Key to look for in i18n dictionary (which is returned verbatim if unknown)
         * @param {Object} [namedParms] Named replacements. Replaces {named} with values in this object.
         * @param {String} [args]      Replacement #1 (replaces first {} and all {1})
         * @param {String} [arg2]       Replacement #2 (replaces second {} and all {2})
         * @param {String} [argn...]      Replacement #n (replaces nth {} and all {n})
         *
         * @return {String} Translated string.
         *
         * @sample Ink_Util_I18n_1_text.html
         */
        text: function( str /*, replacements...*/ ) {
            if ( typeof str !== 'string' ) { return; } // Backwards-compat

            var pars = Array.prototype.slice.call( arguments , 1 );
            var idx = 0;
            var isObj = typeof pars[ 0 ] === 'object';

            var original = this.getKey( str );

            if ( original === undefined ) { original = this._testMode ? '[' + str + ']' : str; }
            if ( typeof original === 'number' ) { original += ''; }

            if (typeof original === 'string') {
                original = original.replace( pattrText , function( m , $1 , $2 , $3 ) {
                    var ret =
                        $1            ? $1 :
                        $2            ? pars[ $2 - ( isObj ? 0 : 1 ) ] :
                        $3 && pars[0] ? pars[ 0 ][ $3 ] || '' :
                             pars[ (idx++) + ( isObj ? 1 : 0 ) ];
                    return funcOrVal( ret , [idx].concat(pars) );
                });
                return original;
            }

            return (
                typeof original === 'function' ? original.apply( this , pars ) :
                original instanceof Array      ? funcOrVal( original[ pars[ 0 ] ] , pars ) :
                typeof original === 'object'   ? funcOrVal( original[ pars[ 0 ] ] , pars ) :
                                                 '');
        },

        /**
         * Translates and pluralizes text.
         * Given a singular string, a plural string and a number, translates either the singular or plural string.
         *
         * @method ntext
         *
         * @param {String} strSin   Word to use when count is 1
         * @param {String} strPlur  Word to use otherwise
         * @param {Number} count    Number which defines which word to use
         * @param {Mixed} [args...] Extra arguments, to be passed to `text()`
         *
         * @return {String} Pluralized text string.
         *
         * @sample Ink_Util_I18n_1_ntext.html
         */
        ntext: function( strSin , strPlur , count ) {
            var pars = Array.prototype.slice.apply( arguments );
            var original;

            if ( pars.length === 2 && typeof strPlur === 'number' ) {
                original = this.getKey( strSin );
                if ( !( original instanceof Array ) ) { return ''; }

                pars.splice( 0 , 1 );
                original = original[ strPlur === 1 ? 0 : 1 ];
            } else {
                pars.splice( 0 , 2 );
                original = count === 1 ? strSin : strPlur;
            }

            return this.text.apply( this , [ original ].concat( pars ) );
        },

        /**
         * Gets the ordinal suffix of a number.
         *
         * This works by using transforms (in the form of Objects or Functions) passed into the function or found in the special key `_ordinals` in the active language dictionary.
         *
         * @method ordinal
         *
         * @param {Number}          num                         Input number
         * @param {Object|Function} [options]={}                Dictionaries for translating. Each of these options' fallback is found in the current language's dictionary. The lookup order is the following: `exceptions`, `byLastDigit`, `default`. Each of these may be either an `Object` or a `Function`. If it's a function, it is called (with `number` and `digit` for any function except for byLastDigit, which is called with the `lastDigit` of the number in question), and if the function returns a string, that is used. If it's an object, the property is looked up using `obj[prop]`. If what is found is a string, it is used directly.
         * @param {Object|Function} [options.byLastDigit]={}    If the language requires the last digit to be considered, mappings of last digits to ordinal suffixes can be created here.
         * @param {Object|Function} [options.exceptions]={}     Map unique, special cases to their ordinal suffixes.
         *
         * @returns {String}        Ordinal suffix for `num`.
         *
         * @sample Ink_Util_I18n_1_ordinal.html
         **/
        ordinal: function( num ) {
            if ( num === undefined ) { return ''; }

            var lastDig = +num.toString( ).slice( -1 );

            var ordDict  = this.getKey( '_ordinals' );
            if ( ordDict === undefined ) { return ''; }

            if ( typeof ordDict === 'string' ) { return ordDict; }

            var ret;

            if ( typeof ordDict === 'function' ) {
                ret = ordDict( num , lastDig );

                if ( typeof ret === 'string' ) { return ret; }
            }

            if ( 'exceptions' in ordDict ) {
                ret = typeof ordDict.exceptions === 'function' ? ordDict.exceptions( num , lastDig ) :
                      num in ordDict.exceptions                ? funcOrVal( ordDict.exceptions[ num ] , [num , lastDig] ) :
                                                                 undefined;

                if ( typeof ret === 'string' ) { return ret; }
            }

            if ( 'byLastDigit' in ordDict ) {
                ret = typeof ordDict.byLastDigit === 'function' ? ordDict.byLastDigit( lastDig , num ) :
                      lastDig in ordDict.byLastDigit            ? funcOrVal( ordDict.byLastDigit[ lastDig ] , [lastDig , num] ) :
                                                                  undefined;

                if ( typeof ret === 'string' ) { return ret; }
            }

            if ( 'default' in ordDict ) {
                ret = funcOrVal( ordDict['default'] , [ num , lastDig ] );

                if ( typeof ret === 'string' ) { return ret; }
            }

            return '';
        },

        /**
         * Create an alias.
         *
         * Returns an alias to this I18n instance. It contains the I18n methods documented here, but is also a function. If you call it, it just calls `text()`. This is commonly assigned to "_".
         *
         * @method alias
         * @returns {Function} an alias to `text()` on this instance. You can also access the rest of the translation API through this alias.
         *
         * @sample Ink_Util_I18n_1_alias.html
         */
        alias: function( ) {
            var ret      = Ink.bind( I18n.prototype.text     , this );
            ret.ntext    = Ink.bind( I18n.prototype.ntext    , this );
            ret.append   = Ink.bind( I18n.prototype.append   , this );
            ret.ordinal  = Ink.bind( I18n.prototype.ordinal  , this );
            ret.testMode = Ink.bind( I18n.prototype.testMode , this );

            return ret;
        }
    };

    /**
     * Resets I18n global state (global dictionaries, and default language for instances)
     *
     * @method reset
     * @return {void}
     * @static
     *
     **/
    I18n.reset = function( ) {
        I18n.prototype._gDicts = [ ];
        I18n.prototype._gDict  = { };
        I18n.prototype._gLang  = 'pt_PT';
    };
    I18n.reset( );

    /**
     * Adds a dictionary to be used in all I18n instances for the corresponding language.
     *
     * @method appendGlobal
     * @static
     *
     * @param {Object} dict Dictionary to be added
     * @param {String} lang Language fo the dictionary being added
     * @return {void}
     *
     */
    I18n.appendGlobal = function( dict , lang ) {
        if ( lang ) {
            if ( !( lang in dict ) ) {
                var obj = { };

                obj[ lang ] = dict;

                dict = obj;
            }

            if ( lang !== I18n.prototype._gLang ) { I18n.langGlobal( lang ); }
        }

        I18n.prototype._gDicts.push( dict );

        Ink.extendObj( I18n.prototype._gDict , dict[ I18n.prototype._gLang ] );
    };

    /**
     * Gets or sets the current default language of I18n instances.
     *
     * @method langGlobal
     * @param {String} [lang] the new language for all I18n instances. Omit this argument if you wish to *get* the current default language instead.
     *
     * @static
     *
     * @return {String} language code, or nothing if not used as a setter.
     */
    I18n.langGlobal = function( lang ) {
        if ( !arguments.length ) { return I18n.prototype._gLang; }

        if ( lang && I18n.prototype._gLang !== lang ) {
            I18n.prototype._gLang = lang;

            I18n.prototype._gDict = { };

            for ( var i = 0, l = I18n.prototype._gDicts.length; i < l; i++ ) {
                Ink.extendObj( I18n.prototype._gDict , I18n.prototype._gDicts[ i ][ lang ] || { } );
            }
        }
    };

    return I18n;
});