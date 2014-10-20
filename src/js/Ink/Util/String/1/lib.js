/**
 * String Utilities
 * @module Ink.Util.String_1
 * @version 1
 */

Ink.createModule('Ink.Util.String', '1', [], function() {

    'use strict';

    /**
     * @namespace Ink.Util.String_1 
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
         * Capitalizes a word.
         * If param as more than one word, it converts first letter of all words that have more than 2 letters
         *
         * @method ucFirst
         * @param   {String}  string                String to capitalize.
         * @param   {Boolean} [firstWordOnly]=false Flag to capitalize only the first word.
         * @return  {String}                        Camel cased string.
         * @public
         * @static
         * @sample Ink_Util_String_ucFirst.html 
         */
        ucFirst: function(string, firstWordOnly) {
            var replacer = firstWordOnly ? /(^|\s)(\w)(\S{2,})/ : /(^|\s)(\w)(\S{2,})/g;
            return string ? String(string).replace(replacer, function(_, $1, $2, $3){
                return $1 + $2.toUpperCase() + $3.toLowerCase();
            }) : string;
        },

        /**
         * Trims whitespace from strings
         *
         * @method trim
         * @param   {String} string     String to be trimmed
         * @return  {String}            Trimmed string
         * @public
         * @static
         * @sample Ink_Util_String_trim.html 
         */
        trim: function(string)
        {
            if (typeof string === 'string') {
                return string.replace(/^\s+|\s+$|\n+$/g, '');
            }
            return string;
        },

        /**
         * Strips HTML tags from strings
         *
         * @method stripTags
         * @param   {String} string     String to strip tags from.
         * @param   {String} allowed    Comma separated list of allowed tags.
         * @return  {String}            Stripped string
         * @public
         * @static
         * @sample Ink_Util_String_stripTags.html 
         */
        stripTags: function(string, allowed)
        {
            if (allowed && typeof allowed === 'string') {
                var aAllowed = InkUtilString.trim(allowed).split(',');
                var aNewAllowed = [];
                var cleanedTag = false;
                for(var i=0; i < aAllowed.length; i++) {
                    if(InkUtilString.trim(aAllowed[i]) !== '') {
                        cleanedTag = InkUtilString.trim(aAllowed[i].replace(/(<|\>)/g, '').replace(/\s/, ''));
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
                return string.replace(/<[^\>]+\>/g, '');
            }
        },

        /**
         * Encodes string into HTML entities.
         *
         * @method htmlEntitiesEncode
         * @param {String} string Input string.
         * @return {String} HTML encoded string.
         * @public
         * @static
         * @sample Ink_Util_String_htmlEntitiesEncode.html 
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
         * Decodes string from HTML entities.
         *
         * @method htmlEntitiesDecode
         * @param   {String}    string  String to be decoded
         * @return  {String}            Decoded string
         * @public
         * @static
         * @sample Ink_Util_String_htmlEntitiesDecode.html 
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
         * Encode a string to UTF-8.
         *
         * @method utf8Encode
         * @param   {String}    string      String to be encoded
         * @return  {String}    string      UTF-8 encoded string
         * @public
         * @static
         */
        utf8Encode: function(string) {
            /*jshint bitwise:false*/
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
         * Truncates a string without breaking words. Inserts an ellipsis HTML entity at the end of the string if it's too long.
         *
         * @method shortString
         * @param   {String}    str     String to truncate
         * @param   {Number}    n       Number of chars of the short string
         * @return  {String}            Truncated string, or the original `str` if it's shorter than `n`
         * @public
         * @static
         * @sample Ink_Util_String_shortString.html 
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
         * Truncates a string, breaking words and adding ... at the end.
         *
         * @method truncateString
         * @param   {String} str        String to truncate
         * @param   {Number} length     Limit for the returned string, ellipsis included.
         * @return  {String}            Truncated String
         * @public
         * @static
         * @sample Ink_Util_String_truncateString.html 
         */
        truncateString: function(str, length) {
            if(str.length - 1 > length) {
                return str.substr(0, length - 1) + "\u2026";
            } else {
                return str;
            }
        },

        /**
         * Decodes a string from UTF-8.
         *
         * @method utf8Decode
         * @param   {String} string     String to be decoded
         * @return  {String}            Decoded string
         * @public
         * @static
         */
        utf8Decode: function(string) {
            /*jshint bitwise:false*/
            var ret = "";
            var i = 0, c = 0, c2 = 0, c3 = 0;

            while ( i < string.length ) {

                c = string.charCodeAt(i);

                if (c < 128) {
                    ret += String.fromCharCode(c);
                    i++;
                }
                else if((c > 191) && (c < 224)) {
                    c2 = string.charCodeAt(i+1);
                    ret += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                    i += 2;
                }
                else {
                    c2 = string.charCodeAt(i+1);
                    c3 = string.charCodeAt(i+2);
                    ret += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                    i += 3;
                }

            }
            return ret;
        },

        /**
         * Removes all accented characters from a string.
         *
         * @method removeAccentedChars
         * @param   {String} string     String to remove accents from
         * @return  {String}            String without accented chars
         * @public
         * @static
         * @sample Ink_Util_String_removeAccentedChars.html 
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
         * @param   {String} haystack   String to search in
         * @param   {String} needle     String to search for
         * @return  {Number}            Number of occurrences
         * @public
         * @static
         * @sample Ink_Util_String_substrCount.html 
         */
        substrCount: function(haystack,needle)
        {
            return haystack ? haystack.split(needle).length - 1 : 0;
        },

        /**
         * Eval a JSON - We recommend you Ink.Util.Json
         *
         * @method evalJSON
         * @param   {String}    strJSON     JSON string to eval
         * @param   {Boolean}   sanitize    Flag to sanitize input
         * @return  {Object}                JS Object
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
         * @param   {String}    str      String to check
         * @return  {Boolean}   Return whether it's JSON.
         * @public
         * @static
         */
        isJSON: function(str)
        {
            str = str.replace(/\\./g, '@').replace(/"[^"\\\n\r]*"/g, '');
            return (/^[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]*$/).test(str);
        },

        /**
         * Escapes unsafe html chars as HTML entities
         *
         * @method htmlEscapeUnsafe
         * @param {String} str String to escape
         * @return {String} Escaped string
         * @public
         * @static
         * @sample Ink_Util_String_htmlEscapeUnsafe.html 
         */
        htmlEscapeUnsafe: function(str){
            var chars = InkUtilString._htmlUnsafeChars;
            return str !== null ? String(str).replace(/[<>&'"]/g,function(c){return chars[c];}) : str;
        },

        /**
         * Normalizes whitespace in string.
         * String is trimmed and sequences of whitespaces are collapsed.
         *
         * @method normalizeWhitespace
         * @param   {String}    str     String to normalize
         * @return  {String}            Normalized string
         * @public
         * @static
         * @sample Ink_Util_String_normalizeWhitespace.html 
         */
        normalizeWhitespace: function(str){
            return str !== null ? InkUtilString.trim(String(str).replace(/\s+/g,' ')) : str;
        },

        /**
         * Converts string to unicode.
         *
         * @method toUnicode
         * @param   {String} str    String to convert
         * @return  {String}        Unicoded String
         * @public
         * @static
         * @sample Ink_Util_String_toUnicode.html 
         */
        toUnicode: function(str) {
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

                        /*
                        if(inInt == 34 || inInt == 92 || inInt == 47) {
                            theUnicode = '\\'+str.charAt(i);
                        } else {
                        }
                        */
                        if(inInt === 8) {
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
         * Escapes a unicode character.
         *
         * @method escape
         * @param {String}  c   Character to escape
         * @return {String} Escaped character. Returns \xXX if hex smaller than 0x100, otherwise \uXXXX
         * @public
         * @static
         * @sample Ink_Util_String_escape.html 
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
         * @param   {String} es     Escape sequence
         * @return  {String}        String un-unicoded
         * @public
         * @static
         * @sample Ink_Util_String_unescape.html 
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
         * Escapes unicode characters in a string as unicode character entities (`\x##`, where the `##` are hex digits).
         *
         * @method escapeText
         * @param   {String}    txt             String with characters outside the ASCII printable range (32 < charCode < 127)
         * @param   {Array}     [whiteList]     Whitelist of characters which should NOT be escaped
         * @return  {String}                    String escaped with unicode character entities.
         * @public
         * @static
         * @sample Ink_Util_String_escapeText.html 
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
         * Removes unicode entities (in the format "\x##" or "\u####", where "#" is a hexadecimal digit)
         *
         * @method unescapeText
         * @param {String} txt Text you intend to remove unicode character entities.
         * @return {String} Unescaped string
         * @public
         * @static
         * @sample Ink_Util_String_unescapeText.html 
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
         * Compares two strings.
         *
         * @method strcmp
         * @param   {String}    str1     First String
         * @param   {String}    str2     Second String
         * @return  {Number} 0 if given strings are equal, 1 if str1 is greater than str2, and -1 if str2 is greater than str1.
         * @public
         * @static
         * @sample Ink_Util_String_strcmp.html 
         */
        strcmp: function(str1, str2) {
            return ((str1 === str2) ? 0 : ((str1 > str2) ? 1 : -1));
        },

        /**
         * Splits a string into smaller chunks
         *
         * @method packetize
         * @param   {String} str        String to divide
         * @param   {Number} maxLen     Maximum chunk size (in characters)
         * @return  {Array}             Chunks of the original string
         * @public
         * @static
         * @sample Ink_Util_String_packetize.html 
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
