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
         * Regular expression groups for several groups of characters
         *
         * http://en.wikipedia.org/wiki/C0_Controls_and_Basic_Latin
         * http://en.wikipedia.org/wiki/Plane_%28Unicode%29#Basic_Multilingual_Plane
         * http://en.wikipedia.org/wiki/ISO_8859-1
         *
         * @property _characterGroups
         * @type {Object}
         * @private
         * @static
         * @readOnly
         */
        _characterGroups: {
            numbers: ['0-9'],
            asciiAlpha: ['a-zA-Z'],
            latin1Alpha: ['a-zA-Z', '\u00C0-\u00FF'],
            unicodeAlpha: ['a-zA-Z', '\u00C0-\u00FF', '\u0100-\u1FFF', '\u2C00-\uD7FF'],
            /* whitespace characters */
            space: [' '],
            dash: ['-'],
            underscore: ['_'],
            nicknamePunctuation: ['_.-'],

            singleLineWhitespace: ['\t '],
            newline: ['\n'],
            whitespace: ['\t\n\u000B\f\r\u00A0 '],

            asciiPunctuation: ['\u0021-\u002F', '\u003A-\u0040', '\u005B-\u0060', '\u007B-\u007E'],
            latin1Punctuation: ['\u0021-\u002F', '\u003A-\u0040', '\u005B-\u0060', '\u007B-\u007E', '\u00A1-\u00BF', '\u00D7', '\u00F7'],
            unicodePunctuation: ['\u0021-\u002F', '\u003A-\u0040', '\u005B-\u0060', '\u007B-\u007E', '\u00A1-\u00BF', '\u00D7', '\u00F7', '\u2000-\u206F', '\u2E00-\u2E7F', '\u3000-\u303F'],
        },

        /**
         * Create a regular expression for several character groups.
         *
         * @method createRegExp
         *
         * @param Groups... {Object}
         *  Groups to build regular expressions for. Possible keys are:
         *
         * - **numbers**: 0-9
         * - **asciiAlpha**: a-z, A-Z
         * - **latin1Alpha**: asciiAlpha, plus printable characters in latin-1
         * - **unicodeAlpha**: unicode alphanumeric characters.
         * - **space**: ' ', the space character.
         * - **dash**: dash character.
         * - **underscore**: underscore character.
         * - **nicknamePunctuation**: dash, dot, underscore
         * - **singleLineWhitespace**: space and tab (whitespace which only spans one line).
         * - **newline**: newline character ('\n')
         * - **whitespace**: whitespace characters in the ASCII character set.
         * - **asciiPunctuation**: punctuation characters in the ASCII character set.
         * - **latin1Punctuation**: punctuation characters in latin-1.
         * - **unicodePunctuation**: punctuation characters in unicode.
         *
         */
        createRegExp: function (groups) {
            var re = '^[';
            for (var key in groups) if (groups.hasOwnProperty(key)) {
                if (!(key in Validator._characterGroups)) {
                    throw new Error('group ' + key + ' is not a valid character group');
                } else if (groups[key]) {
                    re += Validator._characterGroups[key].join('');
                }
            }
            return new RegExp(re + ']*?$');
        },

        /**
         * Checks if a field has the required groups. Takes an options object for further configuration.
         *
         * @method checkCharacterGroups
         * @param {String}  s               The validation string
         * @param {Object}  [groups={}]     What groups are included.
         *  @param [options.*]              See createRegexp
         */
        checkCharacterGroups: function (s, groups) {
            return Validator.createRegExp(groups).test(s);
        },

        /**
         * Checks whether a field contains unicode printable characters. Takes an
         * options object for further configuration
         *
         * @method unicode
         * @param {String}  s               The validation string
         * @param {Object}  [options={}]    Optional configuration object
         *  @param [options.*]              See createRegexp
         */
        unicode: function (s, options) {
            return Validator.checkCharacterGroups(s, Ink.extendObj({
                unicodeAlpha: true}, options));
        },

        /**
         * Checks that a field only contains only latin-1 alphanumeric
         * characters. Takes options for allowing singleline whitespace,
         * cross-line whitespace and punctuation.
         *
         * @method latin1
         *
         * @param {String}  s               The validation string
         * @param {Object}  [options={}]    Optional configuration object
         *  @param [options.*]              See createRegexp
         */
        latin1: function (s, options) {
            return Validator.checkCharacterGroups(s, Ink.extendObj({
                latin1Alpha: true}, options));
        },

        /**
         * Checks that a field only contains only ASCII alphanumeric
         * characters. Takes options for allowing singleline whitespace,
         * cross-line whitespace and punctuation.
         *
         * @method ascii
         *
         * @param {String}  s               The validation string
         * @param {Object}  [options={}]    Optional configuration object
         *  @param [options.*]              See createRegexp
         */
        ascii: function (s, options) {
            return Validator.checkCharacterGroups(s, Ink.extendObj({
                asciiAlpha: true}, options));
        },

        /**
         * Checks that the number is a valid number
         *
         * @method number
         * @param {String} numb         The number
         * @param {Object} [options]    Further options
         *  @param  [options.decimalSep='.']    Allow decimal separator.
         *  @param  [options.thousandSep=","]   Strip this character from the number.
         *  @param  [options.negative=false]    Allow negative numbers.
         *  @param  [options.decimalPlaces=0]   Maximum number of decimal places. `0` means integer number.
         *  @param  [options.max=null]          Maximum number
         *  @param  [options.min=null]          Minimum number
         *  @param  [options.returnNumber=false] When this option is true, return the number itself when the value is valid.
         */
        number: function (numb, inOptions) {
            numb = numb + '';
            var options = Ink.extendObj({
                decimalSep: '.',
                thousandSep: '',
                negative: true,
                decimalPlaces: null,
                maxDigits: null,
                max: null,
                min: null,
                returnNumber: false
            }, inOptions || {});
            // smart recursion thing sets up aliases for options.
            if (options.thousandSep) {
                numb = numb.replace(new RegExp('\\' + options.thousandSep, 'g'), '');
                options.thousandSep = '';
                return Validator.number(numb, options);
            }
            if (options.negative === false) {
                options.min = 0;
                options.negative = true;
                return Validator.number(numb, options);
            }
            if (options.decimalSep !== '.') {
                numb = numb.replace(new RegExp('\\' + options.decimalSep, 'g'), '.');
            }

            if (!/^(-)?(\d+)?(\.\d+)?$/.test(numb) || numb === '') {
                return false;  // forbidden character found
            }
            
            var split;
            if (options.decimalSep && numb.indexOf(options.decimalSep) !== -1) {
                split = numb.split(options.decimalSep);
                if (options.decimalPlaces !== null &&
                        split[1].length > options.decimalPlaces) {
                    return false;
                }
            } else {
                split = ['' + numb, ''];
            }
            
            if (options.maxDigits!== null) {
                if (split[0].replace(/-/g, '').length > options.maxDigits) {
                    return split
                }
            }
            
            // Now look at the actual float
            var ret = parseFloat(numb);
            
            if (options.maxExcl !== null && ret >= options.maxExcl ||
                    options.minExcl !== null && ret <= options.minExcl) {
                return false;
            }
            if (options.max !== null && ret > options.max ||
                    options.min !== null && ret < options.min) {
                return false;
            }
            
            if (options.returnNumber) {
                return ret;
            } else {
                return true;
            }
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
         *         console.log( InkValidator.email( 'agfsdfgfdsgdsf' ) ); // Result: false
         *         console.log( InkValidator.email( 'inkdev\u0040sapo.pt' ) ); // Result: true (where \u0040 is at sign)
         *     });
         */
        email: function(email)
        {
            var emailValido = new RegExp("^[_a-z0-9-]+((\\.|\\+)[_a-z0-9-]+)*@([\\w]*-?[\\w]*\\.)+[a-z]{2,4}$", "i");
            if(!emailValido.test(email)) {
                return false;
            } else {
                return true;
            }
        },

        /**
         * Deprecated. Alias for email(). Use it instead.
         *
         * @method mail
         * @public
         * @static
         */
        mail: function (mail) { return Validator.email(mail); },

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
        },

        /**
         * Checks if the value is a valid IP. Supports ipv4 and ipv6
         *
         * @method validationFunctions.ip
         * @param  {String} value   Value to be checked
         * @param  {String} ipType Type of IP to be validated. The values are: ipv4, ipv6. By default is ipv4.
         * @return {Boolean}         True if the value is a valid IP address. False if not.
         */
        isIP: function( value, ipType ){
            if( typeof value !== 'string' ){
                return false;
            }

            ipType = (ipType || 'ipv4').toLowerCase();

            switch( ipType ){
                case 'ipv4':
                    return (/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/).test(value);
                case 'ipv6':
                    return (/^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*$/).test(value);
                default:
                    return false;
            }
        },

        /**
         * Credit Card specifications, to be used in the credit card verification.
         *
         * @property _creditCardSpecs
         * @type {Object}
         * @private
         */
        _creditCardSpecs: {
            'default': {
                'length': '13,14,15,16,17,18,19',
                'prefix': /^.+/,
                'luhn': true
            },

            'american express': {
                'length': '15',
                'prefix': /^3[47]/,
                'luhn'  : true
            },

            'diners club': {
                'length': '14,16',
                'prefix': /^36|55|30[0-5]/,
                'luhn'  : true
            },

            'discover': {
                'length': '16',
                'prefix': /^6(?:5|011)/,
                'luhn'  : true
            },

            'jcb': {
                'length': '15,16',
                'prefix': /^3|1800|2131/,
                'luhn'  : true
            },

            'maestro': {
                'length': '16,18',
                'prefix': /^50(?:20|38)|6(?:304|759)/,
                'luhn'  : true
            },

            'mastercard': {
                'length': '16',
                'prefix': /^5[1-5]/,
                'luhn'  : true
            },

            'visa': {
                'length': '13,16',
                'prefix': /^4/,
                'luhn'  : true
            }
        },

        /**
         * Luhn function, to be used when validating credit cards
         *
         */
        _luhn: function (num){

            num = parseInt(num,10);

            if ( (typeof num !== 'number') && (num % 1 !== 0) ){
                // Luhn can only be used on nums!
                return false;
            }

            num = num+'';
            // Check num length
            var length = num.length;

            // Checksum of the card num
            var
                i, checksum = 0
            ;

            for (i = length - 1; i >= 0; i -= 2)
            {
                // Add up every 2nd digit, starting from the right
                checksum += parseInt(num.substr(i, 1),10);
            }

            for (i = length - 2; i >= 0; i -= 2)
            {
                // Add up every 2nd digit doubled, starting from the right
                var dbl = parseInt(num.substr(i, 1) * 2,10);

                // Subtract 9 from the dbl where value is greater than 10
                checksum += (dbl >= 10) ? (dbl - 9) : dbl;
            }

            // If the checksum is a multiple of 10, the number is valid
            return (checksum % 10 === 0);
        },

        /**
         * Validates if a number is of a specific credit card
         *
         * @param  {String}  num            Number to be validates
         * @param  {String|Array}  creditCardType Credit card type. See _creditCardSpecs for the list of supported values.
         * @return {Boolean}
         */
        isCreditCard: function(num, creditCardType){

            if ( /\d+/.test(num) === false ){
                return false;
            }

            if ( typeof creditCardType === 'undefined' ){
                creditCardType = 'default';
            }
            else if ( typeof creditCardType === 'array' ){
                var i, ccLength = creditCardType.length;
                for ( i=0; i < ccLength; i++ ){
                    // Test each type for validity
                    if (this.isCreditCard(num, creditCardType[i]) ){
                        return true;
                    }
                }

                return false;
            }

            // Check card type
            creditCardType = creditCardType.toLowerCase();

            if ( typeof this._creditCardSpecs[creditCardType] === 'undefined' ){
                return false;
            }

            // Check card number length
            var length = num.length+'';

            // Validate the card length by the card type
            if ( this._creditCardSpecs[creditCardType]['length'].split(",").indexOf(length) === -1 ){
                return false;
            }

            // Check card number prefix
            if ( !this._creditCardSpecs[creditCardType]['prefix'].test(num) ){
                return false;
            }

            // No Luhn check required
            if (this._creditCardSpecs[creditCardType]['luhn'] === false){
                return true;
            }

            return this._luhn(num);
        }
    };

    return Validator;

});