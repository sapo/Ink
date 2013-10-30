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
         * @param {Boolean} [firstWordOnly=false] capitalize only first word.
         * @return {String} string camel cased
         * @public
         * @static
         *
         * @example
         *      InkString.ucFirst('hello world'); // -> 'Hello World'
         *      InkString.ucFirst('hello world', true); // -> 'Hello world'
         */
        ucFirst: function(string, firstWordOnly) {
            var replacer = firstWordOnly ? /(^|\s)(\w)(\S{2,})/ : /(^|\s)(\w)(\S{2,})/g;
            return string ? String(string).replace(replacer, function(_, $1, $2, $3){
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
                var aAllowed = InkUtilString.trim(allowed).split(',');
                var aNewAllowed = [];
                var cleanedTag = false;
                for(var i=0; i < aAllowed.length; i++) {
                    if(InkUtilString.trim(aAllowed[i]) !== '') {
                        cleanedTag = InkUtilString.trim(aAllowed[i].replace(/(\<|\>)/g, '').replace(/\s/, ''));
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
                for (var i = 0; i < InkUtilString._chars.length; i++) {
                    re = new RegExp(InkUtilString._chars[i], "gm");
                    string = string.replace(re, '&' + InkUtilString._entities[i] + ';');
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
                for (var i = 0; i < InkUtilString._entities.length; i++) {
                    re = new RegExp("&"+InkUtilString._entities[i]+";", "gm");
                    string = string.replace(re, InkUtilString._chars[i]);
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
            for (var i = 0; i < InkUtilString._accentedChars.length; i++) {
                re = new RegExp(InkUtilString._accentedChars[i], "gm");
                newString = newString.replace(re, '' + InkUtilString._accentedRemovedChars[i] + '');
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
        evalJSON: function(strJSON, sanitize) {
            /* jshint evil:true */
            if( (typeof sanitize === 'undefined' || sanitize === null) || InkUtilString.isJSON(strJSON)) {
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
            var chars = InkUtilString._htmlUnsafeChars;
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
            return str != null ? InkUtilString.trim(String(str).replace(/\s+/g,' ')) : str;
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
         * @method escape
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
                    c = InkUtilString.escape(c);
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
            while (m = InkUtilString.escapedCharRegex.exec(txt)) {
                m = m[0];
                txt = txt.replace(m, InkUtilString.unescape(m));
                InkUtilString.escapedCharRegex.lastIndex = 0;
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
