/**
 * JSON Utilities
 * @module Ink.Util.Json_1
 * @version 1
 */

Ink.createModule('Ink.Util.Json', '1', [], function() {
    'use strict';

    var function_call = Function.prototype.call;
    var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;

    function twoDigits(n) {
        var r = '' + n;
        if (r.length === 1) {
            return '0' + r;
        } else {
            return r;
        }
    }

    var dateToISOString = Date.prototype.toISOString ?
        Ink.bind(function_call, Date.prototype.toISOString) :
        function(date) {
            // Adapted from https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date/toISOString
            return date.getUTCFullYear() +
                '-' + twoDigits( date.getUTCMonth() + 1 ) +
                '-' + twoDigits( date.getUTCDate() ) +
                'T' + twoDigits( date.getUTCHours() ) +
                ':' + twoDigits( date.getUTCMinutes() ) +
                ':' + twoDigits( date.getUTCSeconds() ) +
                '.' + String( (date.getUTCMilliseconds()/1000).toFixed(3) ).slice( 2, 5 ) +
                'Z';
        };

    /**
     * Use this class to convert JSON strings to JavaScript objects
     * `.parse()` and also to do the opposite operation `.stringify()`.
     * Internally, the standard JSON implementation is used if available
     * Otherwise, the functions mimic the standard implementation.
     *
     * Here's how to produce JSON from an existing object:
     * 
     *      Ink.requireModules(['Ink.Util.Json_1'], function (Json) {
     *          var obj = {
     *              key1: 'value1',
     *              key2: 'value2',
     *              keyArray: ['arrayValue1', 'arrayValue2', 'arrayValue3']
     *          };
     *          Json.stringify(obj);  // The above object as a JSON string
     *      });
     *
     * And here is how to parse JSON:
     *
     *      Ink.requireModules(['Ink.Util.Json_1'], function (Json) {
     *          var source = '{"key": "value", "array": [true, null, false]}';
     *          Json.parse(source);  // The above JSON string as an object
     *      });
     *
     * @namespace Ink.Util.Json_1 
     * @static
     * 
     */
    var InkJson = {
        _nativeJSON: window.JSON || null,

        _convertToUnicode: false,

        // Escape characters so as to embed them in JSON strings
        _escape: function (theString) {
            var _m = { '\b': '\\b', '\t': '\\t', '\n': '\\n', '\f': '\\f', '\r': '\\r', '"': '\\"',  '\\': '\\\\' };

            if (/["\\\x00-\x1f]/.test(theString)) {
                theString = theString.replace(/([\x00-\x1f\\"])/g, function(a, b) {
                    var c = _m[b];
                    if (c) {
                        return c;
                    }
                    c = b.charCodeAt();
                    return '\\u00' + Math.floor(c / 16).toString(16) + (c % 16).toString(16);
                });
            }

            return theString;
        },

        // A character conversion map
        _toUnicode: function (theString)
        {
            if(!this._convertToUnicode) {
                return this._escape(theString);
            } else {
                var unicodeString = '';
                var inInt = false;
                var theUnicode = false;
                var i = 0;
                var total = theString.length;
                while(i < total) {
                    inInt = theString.charCodeAt(i);
                    if( (inInt >= 32 && inInt <= 126) ||
                            //(inInt >= 48 && inInt <= 57) ||
                            //(inInt >= 65 && inInt <= 90) ||
                            //(inInt >= 97 && inInt <= 122) ||
                            inInt === 8 ||
                            inInt === 9 ||
                            inInt === 10 ||
                            inInt === 12 ||
                            inInt === 13 ||
                            inInt === 32 ||
                            inInt === 34 ||
                            inInt === 47 ||
                            inInt === 58 ||
                            inInt === 92) {

                        if(inInt === 34 || inInt === 92 || inInt === 47) {
                            theUnicode = '\\'+theString.charAt(i);
                        } else if(inInt === 8) {
                            theUnicode = '\\b';
                        } else if(inInt === 9) {
                            theUnicode = '\\t';
                        } else if(inInt === 10) {
                            theUnicode = '\\n';
                        } else if(inInt === 12) {
                            theUnicode = '\\f';
                        } else if(inInt === 13) {
                            theUnicode = '\\r';
                        } else {
                            theUnicode = theString.charAt(i);
                        }
                    } else {
                        if(this._convertToUnicode) {
                            theUnicode = theString.charCodeAt(i).toString(16)+''.toUpperCase();
                            while (theUnicode.length < 4) {
                                theUnicode = '0' + theUnicode;
                            }
                            theUnicode = '\\u' + theUnicode;
                        } else {
                            theUnicode = theString.charAt(i);
                        }
                    }
                    unicodeString += theUnicode;

                    i++;
                }

                return unicodeString;
            }

        },

        _stringifyValue: function(param) {
            if (typeof param === 'string') {
                return '"' + this._toUnicode(param) + '"';
            } else if (typeof param === 'number' && (isNaN(param) || !isFinite(param))) {  // Unusable numbers go null
                return 'null';
            } else if (typeof param === 'undefined' || param === null) {  // And so does undefined
                return 'null';
            } else if (typeof param.toJSON === 'function') {
                var t = param.toJSON();
                if (typeof t === 'string') {
                    return '"' + this._escape(t) + '"';
                } else {
                    return this._escape(t.toString());
                }
            } else if (typeof param === 'number' || typeof param === 'boolean') {  // These ones' toString methods return valid JSON.
                return '' + param;
            } else if (typeof param === 'function') {
                return 'null';  // match JSON.stringify
            } else if (param.constructor === Date) {
                return '"' + this._escape(dateToISOString(param)) + '"';
            } else if (param.constructor === Array) {
                var arrayString = '';
                for (var i = 0, len = param.length; i < len; i++) {
                    if (i > 0) {
                        arrayString += ',';
                    }
                    arrayString += this._stringifyValue(param[i]);
                }
                return '[' + arrayString + ']';
            } else {  // Object
                var objectString = '';
                for (var k in param)  {
                    if ({}.hasOwnProperty.call(param, k)) {
                        if (objectString !== '') {
                            objectString += ',';
                        }
                        objectString += '"' + this._escape(k) + '": ' + this._stringifyValue(param[k]);
                    }
                }
                return '{' + objectString + '}';
            }
        },

        /**
         * Serializes a JSON object into a string.
         *
         * @method stringify
         * @param   {Object}      input                 Data to be serialized into JSON
         * @param   {Boolean}     convertToUnicode      When `true`, converts string contents to unicode \uXXXX
         * @return  {String}                            Serialized string
         *
         * @sample Ink_Util_Json_stringify.html 
         */
        stringify: function(input, convertToUnicode) {
            this._convertToUnicode = !!convertToUnicode;
            if(!this._convertToUnicode && this._nativeJSON) {
                return this._nativeJSON.stringify(input);
            }
            return this._stringifyValue(input);  // And recurse.
        },
        
        /**
         * Parses a JSON text through a function
         * 
         * @method parse
         * @param text      {String}    Input string
         * @param reviver   {Function}  Function receiving `(key, value)`, and `this`=(containing object), used to walk objects.
         * 
         * @return {Object}             JSON object
         *
         * @sample Ink_Util_Json_parse.html 
         */
        /* From https://github.com/douglascrockford/JSON-js/blob/master/json.js */
        parse: function (text, reviver) {
            /*jshint evil:true*/

// The parse method takes a text and an optional reviver function, and returns
// a JavaScript value if the text is a valid JSON text.

            var j;

            function walk(holder, key) {

// The walk method is used to recursively walk the resulting structure so
// that modifications can be made.

                var k, v, value = holder[key];
                if (value && typeof value === 'object') {
                    for (k in value) {
                        if (Object.prototype.hasOwnProperty.call(value, k)) {
                            v = walk(value, k);
                            if (v !== undefined) {
                                value[k] = v;
                            } else {
                                delete value[k];
                            }
                        }
                    }
                }
                return reviver.call(holder, key, value);
            }


// Parsing happens in four stages. In the first stage, we replace certain
// Unicode characters with escape sequences. JavaScript handles many characters
// incorrectly, either silently deleting them, or treating them as line endings.

            text = String(text);
            cx.lastIndex = 0;
            if (cx.test(text)) {
                text = text.replace(cx, function (a) {
                    return '\\u' +
                        ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
                });
            }

// In the second stage, we run the text against regular expressions that look
// for non-JSON patterns. We are especially concerned with '()' and 'new'
// because they can cause invocation, and '=' because it can cause mutation.
// But just to be safe, we want to reject all unexpected forms.

// We split the second stage into 4 regexp operations in order to work around
// crippling inefficiencies in IE's and Safari's regexp engines. First we
// replace the JSON backslash pairs with '@' (a non-JSON character). Second, we
// replace all simple value tokens with ']' characters. Third, we delete all
// open brackets that follow a colon or comma or that begin the text. Finally,
// we look to see that the remaining characters are only whitespace or ']' or
// ',' or ':' or '{' or '}'. If that is so, then the text is safe for eval.

            if (/^[\],:{}\s]*$/
                    .test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@')
                        .replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']')
                        .replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {

// In the third stage we use the eval function to compile the text into a
// JavaScript structure. The '{' operator is subject to a syntactic ambiguity
// in JavaScript: it can begin a block or an object literal. We wrap the text
// in parens to eliminate the ambiguity.

                j = eval('(' + text + ')');

// In the optional fourth stage, we recursively walk the new structure, passing
// each name/value pair to a reviver function for possible transformation.

                return typeof reviver === 'function' ?
                    walk({'': j}, '') :
                    j;
            }

// If the text is not JSON parseable, then a SyntaxError is thrown.

            throw new SyntaxError('JSON.parse');
        }
    };

    return InkJson;
});
