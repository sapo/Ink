/**
 * Date utility functions
 * @module Ink.Util.Date_1
 * @version 1
 */

Ink.createModule('Ink.Util.Date', '1', [], function() {

    'use strict';

    /**
     * @namespace Ink.Util.Date_1 
     */
    var InkDate = {

        /**
         * Function that returns the string representation of the month [PT only]
         *
         * @method _months
         * @param {Number} index Month javascript (0 to 11)
         * @return {String} The month's name
         * @private
         * @static
         * @example
         *     console.log( InkDate._months(0) ); // Result: Janeiro
         */
        _months: function(index){
            var _m = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
            return _m[index];
        },

        /**
         * Function that returns the month [PT only] ( 0 to 11 )
         *
         * @method _iMonth
         * @param {String} month Month javascript (0 to 11)
         * @return {Number} The month's number
         * @private
         * @static
         * @example
         *     console.log( InkDate._iMonth('maio') ); // Result: 4
         */
        _iMonth : function( month )
        {
            if ( Number( month ) ) { return +month - 1; }
            return {
                'janeiro'   : 0  ,
                'jan'       : 0  ,
                'fevereiro' : 1  ,
                'fev'       : 1  ,
                'março'     : 2  ,
                'mar'       : 2  ,
                'abril'     : 3  ,
                'abr'       : 3  ,
                'maio'      : 4  ,
                'mai'       : 4  ,
                'junho'     : 5  ,
                'jun'       : 5  ,
                'julho'     : 6  ,
                'jul'       : 6  ,
                'agosto'    : 7  ,
                'ago'       : 7  ,
                'setembro'  : 8  ,
                'set'       : 8  ,
                'outubro'   : 9  ,
                'out'       : 9  ,
                'novembro'  : 10 ,
                'nov'       : 10 ,
                'dezembro'  : 11 ,
                'dez'       : 11
            }[ month.toLowerCase( ) ];
        } ,

        /**
         * Function that returns the representation the day of the week [PT Only]
         *
         * @method _wDays
         * @param {Number} index Week's day index
         * @return {String} The week's day name
         * @private
         * @static
         * @example
         *     console.log( InkDate._wDays(0) ); // Result: Domingo
         */
        _wDays: function(index){
            var _d = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
            return _d[index];
        },

        /**
         * Function that returns day of the week in javascript 1 to 7
         *
         * @method _iWeek
         * @param {String} week Week's day name
         * @return {Number} The week's day index
         * @private
         * @static
         * @example
         *     console.log( InkDate._iWeek('quarta') ); // Result: 3
         */
        _iWeek: function( week )
        {
            if ( Number( week ) ) { return +week || 7; }
            return {
                'segunda' : 1  ,
                'seg'     : 1  ,
                'terça'   : 2  ,
                'ter'     : 2  ,
                'quarta'  : 3  ,
                'qua'     : 3  ,
                'quinta'  : 4  ,
                'qui'     : 4  ,
                'sexta'   : 5  ,
                'sex'     : 5  ,
                'sábado'  : 6  ,
                'sáb'     : 6  ,
                'domingo' : 7  ,
                'dom'     : 7
            }[ week.toLowerCase( ) ];
        },

        /**
         * Function that returns the number of days of a given month (m) on a given year (y)
         *
         * @method _daysInMonth
         * @param {Number} _m Month
         * @param {Number} _y Year
         * @return {Number} Number of days of a give month on a given year
         * @private
         * @static
         * @example
         *     console.log( InkDate._daysInMonth(2,2013) ); // Result: 28
         */
        _daysInMonth: function(_m,_y){
            var nDays;

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
         * Formats a date object.
         * This works exactly as php date() function. http://php.net/manual/en/function.date.php
         *
         * @method get
         * @param   {String}      format    The format in which the date it will be formatted.
         * @param   {Date}        [_date]   The date to format. Can receive unix timestamp or a date object. Defaults to current time.
         * @return  {String}                Formatted date
         * @public
         * @static
         * @sample Ink_Util_Date_get.html 
         */
        get: function(format, _date){
            /*jshint maxcomplexity:65 */
            if(typeof(format) === 'undefined' || format === ''){
                format = "Y-m-d";
            }


            var iFormat = format.split("");
            var result = new Array(iFormat.length);
            var escapeChar = "\\";
            var jsDate;

        if (typeof(_date) === 'undefined'){
            jsDate = new Date();
        } else if (typeof(_date)==='number'){
            jsDate = new Date(_date*1000);
        } else {
            jsDate = new Date(_date);
        }

        var jsFirstDay, jsThisDay, jsHour;
        /* This switch is presented in the same order as in php date function (PHP 5.2.2) */
        for (var i = 0; i < iFormat.length; i++) {
           switch(iFormat[i]) {
                case escapeChar:
                    result[i] = iFormat[i+1];
                    i++;
                    break;


                /* DAY */
                case "d":   /* Day of the month, 2 digits with leading zeros; ex: 01 to 31  */
                    var jsDay = jsDate.getDate();
                    result[i] = (String(jsDay).length > 1) ? jsDay : "0" + jsDay;
                    break;

                case "D":   /* A textual representation of a day, three letters; Seg to Dom */
                    result[i] = this._wDays(jsDate.getDay()).substring(0, 3);
                    break;

                case "j":  /* Day of the month without leading zeros; ex: 1 to 31  */
                    result[i] = jsDate.getDate();
                    break;

                case "l":   /* A full textual representation of the day of the week; Domingo to Sabado  */
                    result[i] = this._wDays(jsDate.getDay());
                    break;

                case "N":  /* ISO-8601 numeric representation of the day of the week; 1 (Segunda) to 7 (Domingo)  */
                    result[i] = jsDate.getDay() || 7;
                    break;

                case "S":  /* English ordinal suffix for the day of the month, 2 characters; st, nd, rd or th. Works well with j */
                    var temp     = jsDate.getDate();
                    var suffixes = ["st", "nd", "rd"];
                    var suffix   = "";

                    if (temp >= 11 && temp <= 13) {
                        result[i] = "th";
                    } else {
                        result[i]  = (suffix = suffixes[String(temp).substr(-1) - 1]) ? (suffix) : ("th");
                    }
                    break;

                case "w":    /* Numeric representation of the day of the week; 0 (for Sunday) through 6 (for Saturday) */
                    result[i] = jsDate.getDay();
                    break;

                case "z":    /* The day of the year (starting from 0); 0 to 365 */
                    jsFirstDay = Date.UTC(jsDate.getFullYear(), 0, 0);
                    jsThisDay = Date.UTC(jsDate.getFullYear(), jsDate.getMonth(), jsDate.getDate());
                    result[i] = Math.floor((jsThisDay - jsFirstDay) / (1000 * 60 * 60 * 24));
                    break;

                /* WEEK */
                case "W":    /* ISO-8601 week number of year, weeks starting on Monday; ex: 42 (the 42nd week in the year)  */
                    var jsYearStart = new Date( jsDate.getFullYear( ) , 0 , 1 );
                    jsFirstDay = jsYearStart.getDay() || 7;

                    var days = Math.floor( ( jsDate - jsYearStart ) / ( 24 * 60 * 60 * 1000 ) + 1 );

                    result[ i ] = Math.ceil( ( days - ( 8 - jsFirstDay ) ) / 7 ) + 1;
                    break;


                /* MONTH */
                case "F":   /* A full textual representation of a month, such as Janeiro or Marco; Janeiro a Dezembro */
                    result[i] = this._months(jsDate.getMonth());
                    break;

                case "m":   /* Numeric representation of a month, with leading zeros; 01 to 12  */
                    var jsMonth = String(jsDate.getMonth() + 1);
                    result[i] = (jsMonth.length > 1) ? jsMonth : "0" + jsMonth;
                    break;

                case "M":   /* A short textual representation of a month, three letters; Jan a Dez */
                    result[i] = this._months(jsDate.getMonth()).substring(0,3);
                    break;

                case "n":   /* Numeric representation of a month, without leading zeros; 1 a 12  */
                    result[i] = jsDate.getMonth() + 1;
                    break;

                case "t":   /* Number of days in the given month; ex: 28 */
                    result[i] = this._daysInMonth(jsDate.getMonth()+1,jsDate.getYear());
                    break;

                /* YEAR */
                case "L":   /* Whether it's a leap year; 1 if it is a leap year, 0 otherwise.  */
                    var jsYear = jsDate.getFullYear();
                    result[i] = (jsYear % 4) ? false : ( (jsYear % 100) ?  true : ( (jsYear % 400) ? false : true  ) );
                    break;

                case "o":  /* ISO-8601 year number. This has the same value as Y, except that if the ISO week number (W) belongs to the previous or next year, that year is used instead.  */
                    throw '"o" not implemented!';

                case "Y":  /* A full numeric representation of a year, 4 digits; 1999  */
                    result[i] = jsDate.getFullYear();
                    break;

                case "y":  /* A two digit representation of a year; 99  */
                    result[i] = String(jsDate.getFullYear()).substring(2);
                    break;

                /* TIME */
                case "a":   /* Lowercase Ante meridiem and Post meridiem; am or pm */
                    result[i] = (jsDate.getHours() < 12) ? "am" : "pm";
                    break;

                case "A":   /* Uppercase Ante meridiem and Post meridiem; AM or PM  */
                    result[i] = (jsDate.getHours < 12) ? "AM" : "PM";
                    break;

                case "B":  /* Swatch Internet time; 000 through 999  */
                    throw '"B" not implemented!';

                case "g":   /* 12-hour format of an hour without leading zeros;  1 to 12 */
                    jsHour = jsDate.getHours();
                    result[i] = (jsHour <= 12) ? jsHour : (jsHour - 12);
                    break;

                case "G":   /* 24-hour format of an hour without leading zeros; 1 to 23 */
                    result[i] = String(jsDate.getHours());
                    break;

                case "h":   /* 12-hour format of an hour with leading zeros; 01 to 12 */
                    jsHour = String(jsDate.getHours());
                    jsHour = (jsHour <= 12) ? jsHour : (jsHour - 12);
                    result[i] = (jsHour.length > 1) ? jsHour : "0" + jsHour;
                    break;

                case "H":   /* 24-hour format of an hour with leading zeros; 01 to 24 */
                    jsHour = String(jsDate.getHours());
                    result[i] = (jsHour.length > 1) ? jsHour : "0" + jsHour;
                    break;

                case "i":   /* Minutes with leading zeros; 00 to 59 */
                    var jsMinute  = String(jsDate.getMinutes());
                    result[i] = (jsMinute.length > 1) ? jsMinute : "0" + jsMinute;
                    break;

                case "s":   /* Seconds with leading zeros; 00 to 59; */
                    var jsSecond  = String(jsDate.getSeconds());
                    result[i]  = (jsSecond.length > 1) ? jsSecond : "0" + jsSecond;
                    break;

                case "u":  /* Microseconds */
                    throw '"u" not implemented!';


                /* TIMEZONE */

                case "e": /* Timezone identifier  */
                    throw '"e" not implemented!';

                case "I":   /*  "1" if Daylight Savings Time, "0" otherwise. Works only on the northern hemisphere */
                    jsFirstDay = new Date(jsDate.getFullYear(), 0, 1);
                    result[i] = (jsDate.getTimezoneOffset() !== jsFirstDay.getTimezoneOffset()) ? (1) : (0);
                    break;

                case "O":  /* Difference to Greenwich time (GMT) in hours */
                    var jsMinZone = jsDate.getTimezoneOffset();
                    var jsMinutes = jsMinZone % 60;
                    jsHour = String(((jsMinZone - jsMinutes) / 60) * -1);

                    if (jsHour.charAt(0) !== "-") {
                        jsHour = "+" + jsHour;
                    }

                    jsHour = (jsHour.length === 3) ? (jsHour) : (jsHour.replace(/([+\-])(\d)/, "$1" + 0 + "$2"));
                    result[i]  = jsHour + jsMinutes + "0";
                    break;

                case "P": /* Difference to Greenwich time (GMT) with colon between hours and minutes */
                    throw '"P" not implemented!';

                case "T": /* Timezone abbreviation */
                    throw '"T" not implemented!';

                case "Z": /* Timezone offset in seconds. The offset for timezones west of UTC is always negative, and for those east of UTC is always positive. */
                    result[i] = jsDate.getTimezoneOffset() * 60;
                    break;


                /* FULL DATE/TIME  */

                case "c": /* ISO 8601 date */
                    throw '"c" not implemented!';

                case "r": /* RFC 2822 formatted date  */
                    var jsDayName = this._wDays(jsDate.getDay()).substr(0, 3);
                    var jsMonthName = this._months(jsDate.getMonth()).substr(0, 3);
                    result[i] = jsDayName + ", " + jsDate.getDate() + " " + jsMonthName + this.get(" Y H:i:s O",jsDate);
                    break;

                case "U":  /* Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)  */
                    result[i] = Math.floor(jsDate.getTime() / 1000);
                    break;

                default:
                    result[i] = iFormat[i];
            }
        }

        return result.join('');

        },

        /**
         * Creates a date object based on a format string.
         * This works exactly as php date() function. http://php.net/manual/en/function.date.php
         *
         * @method set
         * @param   {String}    [format]    The format in which the date will be formatted. Defaults to 'Y-m-d'
         * @param   {String}    str_date    The date formatted.
         * @return  {Date}                  Date object based on the formatted date and format
         * @public
         * @static
         * @sample Ink_Util_Date_set.html 
         */
        set : function( format , str_date ) {
            if ( typeof str_date === 'undefined' ) { return ; }
            if ( typeof format === 'undefined' || format === '' ) { format = "Y-m-d"; }

            var iFormat = format.split("");
            var result = new Array( iFormat.length );
            var escapeChar = "\\";
            var mList;

            var objIndex = {
                year  : undefined ,
                month : undefined ,
                day   : undefined ,
                dayY  : undefined ,
                dayW  : undefined ,
                week  : undefined ,
                hour  : undefined ,
                hourD : undefined ,
                min   : undefined ,
                sec   : undefined ,
                msec  : undefined ,
                ampm  : undefined ,
                diffM : undefined ,
                diffH : undefined ,
                date  : undefined
            };

            var matches = 0;

            /* This switch is presented in the same order as in php date function (PHP 5.2.2) */
            for ( var i = 0; i < iFormat.length; i++) {
                switch( iFormat[ i ] ) {
                    case escapeChar:
                        result[i]      = iFormat[ i + 1 ];
                        i++;
                        break;

                    /* DAY */
                    case "d":   /* Day of the month, 2 digits with leading zeros; ex: 01 to 31  */
                        result[ i ]    = '(\\d{2})';
                        objIndex.day   = { original : i , match : matches++ };
                        break;

                    case "j":  /* Day of the month without leading zeros; ex: 1 to 31  */
                        result[ i ]    = '(\\d{1,2})';
                        objIndex.day   = { original : i , match : matches++ };
                        break;

                    case "D":   /* A textual representation of a day, three letters; Seg to Dom */
                        result[ i ]    = '([\\wá]{3})';
                        objIndex.dayW  = { original : i , match : matches++ };
                        break;

                    case "l":   /* A full textual representation of the day of the week; Domingo to Sabado  */
                        result[i]      = '([\\wá]{5,7})';
                        objIndex.dayW  = { original : i , match : matches++ };
                        break;

                    case "N":  /* ISO-8601 numeric representation of the day of the week; 1 (Segunda) to 7 (Domingo)  */
                        result[ i ]    = '(\\d)';
                        objIndex.dayW  = { original : i , match : matches++ };
                        break;

                    case "w":    /* Numeric representation of the day of the week; 0 (for Sunday) through 6 (for Saturday) */
                        result[ i ]    = '(\\d)';
                        objIndex.dayW  = { original : i , match : matches++ };
                        break;

                    case "S":  /* English ordinal suffix for the day of the month, 2 characters; st, nd, rd or th. Works well with j */
                        result[ i ]    = '\\w{2}';
                        break;

                    case "z":    /* The day of the year (starting from 0); 0 to 365 */
                        result[ i ]    = '(\\d{1,3})';
                        objIndex.dayY  = { original : i , match : matches++ };
                        break;

                    /* WEEK */
                    case "W":    /* ISO-8601 week number of year, weeks starting on Monday; ex: 42 (the 42nd week in the year)  */
                        result[ i ]    = '(\\d{1,2})';
                        objIndex.week  = { original : i , match : matches++ };
                        break;

                    /* MONTH */
                    case "F":   /* A full textual representation of a month, such as Janeiro or Marco; Janeiro a Dezembro */
                        result[ i ]    = '([\\wç]{4,9})';
                        objIndex.month = { original : i , match : matches++ };
                        break;

                    case "M":   /* A short textual representation of a month, three letters; Jan a Dez */
                        result[ i ]    = '(\\w{3})';
                        objIndex.month = { original : i , match : matches++ };
                        break;

                    case "m":   /* Numeric representation of a month, with leading zeros; 01 to 12  */
                        result[ i ]    = '(\\d{2})';
                        objIndex.month = { original : i , match : matches++ };
                        break;

                    case "n":   /* Numeric representation of a month, without leading zeros; 1 a 12  */
                        result[ i ]    = '(\\d{1,2})';
                        objIndex.month = { original : i , match : matches++ };
                        break;

                    case "t":   /* Number of days in the given month; ex: 28 */
                        result[ i ]    = '\\d{2}';
                        break;

                    /* YEAR */
                    case "L":   /* Whether it's a leap year; 1 if it is a leap year, 0 otherwise.  */
                        result[ i ]    = '\\w{4,5}';
                        break;

                    case "o":  /* ISO-8601 year number. This has the same value as Y, except that if the ISO week number (W) belongs to the previous or next year, that year is used instead.  */
                        throw '"o" not implemented!';

                    case "Y":  /* A full numeric representation of a year, 4 digits; 1999  */
                        result[ i ]    = '(\\d{4})';
                        objIndex.year  = { original : i , match : matches++ };
                        break;

                    case "y":  /* A two digit representation of a year; 99  */
                        result[ i ]    = '(\\d{2})';
                        if ( typeof objIndex.year === 'undefined' || iFormat[ objIndex.year.original ] !== 'Y' ) {
                            objIndex.year = { original : i , match : matches++ };
                        }
                        break;

                    /* TIME */
                    case "a":   /* Lowercase Ante meridiem and Post meridiem; am or pm */
                        result[ i ]    = '(am|pm)';
                        objIndex.ampm  = { original : i , match : matches++ };
                        break;

                    case "A":   /* Uppercase Ante meridiem and Post meridiem; AM or PM  */
                        result[ i ]    = '(AM|PM)';
                        objIndex.ampm  = { original : i , match : matches++ };
                        break;

                    case "B":  /* Swatch Internet time; 000 through 999  */
                        throw '"B" not implemented!';

                    case "g":   /* 12-hour format of an hour without leading zeros;  1 to 12 */
                        result[ i ]    = '(\\d{1,2})';
                        objIndex.hourD = { original : i , match : matches++ };
                        break;

                    case "G":   /* 24-hour format of an hour without leading zeros; 1 to 23 */
                        result[ i ]    = '(\\d{1,2})';
                        objIndex.hour  = { original : i , match : matches++ };
                        break;

                    case "h":   /* 12-hour format of an hour with leading zeros; 01 to 12 */
                        result[ i ]    = '(\\d{2})';
                        objIndex.hourD = { original : i , match : matches++ };
                        break;

                    case "H":   /* 24-hour format of an hour with leading zeros; 01 to 24 */
                        result[ i ]    = '(\\d{2})';
                        objIndex.hour  = { original : i , match : matches++ };
                        break;

                    case "i":   /* Minutes with leading zeros; 00 to 59 */
                        result[ i ]    = '(\\d{2})';
                        objIndex.min   = { original : i , match : matches++ };
                        break;

                    case "s":   /* Seconds with leading zeros; 00 to 59; */
                        result[ i ]    = '(\\d{2})';
                        objIndex.sec   = { original : i , match : matches++ };
                        break;

                    case "u":  /* Microseconds */
                        throw '"u" not implemented!';

                    /* TIMEZONE */
                    case "e": /* Timezone identifier  */
                        throw '"e" not implemented!';

                    case "I":   /*  "1" if Daylight Savings Time, "0" otherwise. Works only on the northern hemisphere */
                        result[i]      = '\\d';
                        break;

                    case "O":  /* Difference to Greenwich time (GMT) in hours */
                        result[ i ]    = '([-+]\\d{4})';
                        objIndex.diffH = { original : i , match : matches++ };
                        break;

                    case "P": /* Difference to Greenwich time (GMT) with colon between hours and minutes */
                        throw '"P" not implemented!';

                    case "T": /* Timezone abbreviation */
                        throw '"T" not implemented!';

                    case "Z": /* Timezone offset in seconds. The offset for timezones west of UTC is always negative, and for those east of UTC is always positive. */
                        result[ i ]    = '(\\-?\\d{1,5})';
                        objIndex.diffM = { original : i , match : matches++ };
                        break;

                    /* FULL DATE/TIME  */
                    case "c": /* ISO 8601 date */
                        throw '"c" not implemented!';

                    case "r": /* RFC 2822 formatted date  */
                        result[ i ]    = '([\\wá]{3}, \\d{1,2} \\w{3} \\d{4} \\d{2}:\\d{2}:\\d{2} [+\\-]\\d{4})';
                        objIndex.date  = { original : i , match : matches++ };
                        break;

                    case "U":  /* Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)  */
                        result[ i ]    = '(\\d{1,13})';
                        objIndex.date  = { original : i , match : matches++ };
                        break;

                    default:
                        result[ i ]    = iFormat[ i ];
                }
            }

            var pattr = new RegExp( result.join('') );

            try {
                mList = str_date.match( pattr );
                if ( !mList ) { return; }
            }
            catch ( e ) { return ; }

            var _haveDatetime = typeof objIndex.date  !== 'undefined';

            var _haveYear     = typeof objIndex.year  !== 'undefined';

            var _haveYDay     = typeof objIndex.dayY  !== 'undefined';

            var _haveDay      = typeof objIndex.day   !== 'undefined';
            var _haveMonth    = typeof objIndex.month !== 'undefined';
            var _haveMonthDay =  _haveMonth && _haveDay;
            var _haveOnlyDay  = !_haveMonth && _haveDay;

            var _haveWDay     = typeof objIndex.dayW  !== 'undefined';
            var _haveWeek     = typeof objIndex.week  !== 'undefined';
            var _haveWeekWDay =  _haveWeek && _haveWDay;
            var _haveOnlyWDay = !_haveWeek && _haveWDay;

            var _validDate    = _haveYDay || _haveMonthDay || !_haveYear && _haveOnlyDay || _haveWeekWDay || !_haveYear && _haveOnlyWDay;
            var _noDate       = !_haveYear && !_haveYDay && !_haveDay && !_haveMonth && !_haveWDay && !_haveWeek;

            var _haveHour12   = typeof objIndex.hourD !== 'undefined' && typeof objIndex.ampm !== 'undefined';
            var _haveHour24   = typeof objIndex.hour  !== 'undefined';
            var _haveHour     = _haveHour12 || _haveHour24;

            var _haveMin      = typeof objIndex.min   !== 'undefined';
            var _haveSec      = typeof objIndex.sec   !== 'undefined';
            var _haveMSec     = typeof objIndex.msec  !== 'undefined';

            var _haveMoreM    = !_noDate || _haveHour;
            var _haveMoreS    = _haveMoreM || _haveMin;

            var _haveDiffM    = typeof objIndex.diffM !== 'undefined';
            var _haveDiffH    = typeof objIndex.diffH !== 'undefined';
            //var _haveGMT      = _haveDiffM || _haveDiffH;
            var hour;
            var min;

            if ( _haveDatetime ) {
                if ( iFormat[ objIndex.date.original ] === 'U' ) {
                    return new Date( +mList[ objIndex.date.match + 1 ] * 1000 );
                }

                var dList = mList[ objIndex.date.match + 1 ].match( /\w{3}, (\d{1,2}) (\w{3}) (\d{4}) (\d{2}):(\d{2}):(\d{2}) ([+\-]\d{4})/ );
                hour  = +dList[ 4 ] + ( +dList[ 7 ].slice( 0 , 3 ) );
                min   = +dList[ 5 ] + ( dList[ 7 ].slice( 0 , 1 ) + dList[ 7 ].slice( 3 ) ) / 100 * 60;

                return new Date( dList[ 3 ] , this._iMonth( dList[ 2 ] ) , dList[ 1 ] , hour  , min , dList[ 6 ] );
            }

            var _d = new Date( );
            var year;
            var month;
            var day;
            var sec;
            var msec;
            var gmt;

            if ( !_validDate && !_noDate ) { return ; }

            if ( _validDate ) {
                if ( _haveYear ) {
                    var _y = _d.getFullYear( ) - 50 + '';
                    year   = mList[ objIndex.year.match + 1 ];
                    if ( iFormat[ objIndex.year.original ] === 'y' ) {
                        year = +_y.slice( 0 , 2 ) + ( year >= ( _y ).slice( 2 ) ? 0 : 1 ) + year;
                    }
                } else {
                    year = _d.getFullYear();
                }

                if ( _haveYDay ) {
                    month = 0;
                    day   = mList[ objIndex.dayY.match + 1 ];
                } else if ( _haveDay ) {
                    if ( _haveMonth ) {
                        month = this._iMonth( mList[ objIndex.month.match + 1 ] );
                    } else {
                        month = _d.getMonth( );
                    }

                    day = mList[ objIndex.day.match + 1 ];
                } else {
                    month = 0;

                    var week;
                    if ( _haveWeek ) {
                        week = mList[ objIndex.week.match + 1 ];
                    } else {
                        week = this.get( 'W' , _d );
                    }

                    day = ( week - 2 ) * 7 + ( 8 - ( ( new Date( year , 0 , 1 ) ).getDay( ) || 7 ) ) + this._iWeek( mList[ objIndex.week.match + 1 ] );
                }

                if ( month === 0 && day > 31 ) {
                    var aux = new Date( year , month , day );
                    month   = aux.getMonth( );
                    day     = aux.getDate( );
                }
            }
            else {
                year  = _d.getFullYear( );
                month = _d.getMonth( );
                day   = _d.getDate( );
            }

            if      ( _haveHour12 ) { hour = +mList[ objIndex.hourD.match + 1 ] + ( mList[ objIndex.ampm.match + 1 ] === 'pm' ? 12 : 0 ); }
            else if ( _haveHour24 ) { hour = mList[ objIndex.hour.match + 1 ]; }
            else if ( _noDate     ) { hour = _d.getHours( ); }
            else                    { hour = '00'; }

            if      (  _haveMin   ) { min  = mList[ objIndex.min.match + 1 ]; }
            else if ( !_haveMoreM ) { min  = _d.getMinutes( ); }
            else                    { min  = '00'; }

            if      (  _haveSec   ) { sec  = mList[ objIndex.sec.match + 1 ]; }
            else if ( !_haveMoreS ) { sec  = _d.getSeconds( ); }
            else                    { sec  = '00'; }

            if      ( _haveMSec )   { msec = mList[ objIndex.msec.match + 1 ]; }
            else                    { msec = '000'; }

            if      ( _haveDiffH )  { gmt  = mList[ objIndex.diffH.match + 1 ]; }
            else if ( _haveDiffM )  { gmt  = String( -1 * mList[ objIndex.diffM.match + 1 ] / 60 * 100 ).replace( /^(\d)/ , '+$1' ).replace( /(^[\-+])(\d{3}$)/ , '$10$2' ); }
            else                    { gmt  = '+0000'; }

            return new Date( year, month, day, hour, min, sec );
        }
    };


    return InkDate;

});
