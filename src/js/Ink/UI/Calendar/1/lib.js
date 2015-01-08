/**
 * Calendar widget (if you want a datepicker, see Ink.UI.DatePicker_1)
 * @module Ink.UI.Calendar_1
 * @version 1
 */

Ink.createModule('Ink.UI.Calendar', 1, ['Ink.UI.Common_1', 'Ink.Dom.Event_1', 'Ink.Dom.Element_1', 'Ink.Dom.Css_1', 'Ink.Util.Array_1'], function (Common, Event, InkElement, Css, InkArray) {
    'use strict';

    function cmp(a, b) {
        if (a < b) { return -1; }
        if (a > b) { return 1; }
        return 0;
    }

    function dateFromDateish(dateish) {
        return new Date(dateish._year, dateish._month, dateish._day, 0, 0);
    }

    function dateishFromDate(date) {
        if (isNaN(+date)) { return null; }
        return {_year: date.getFullYear(), _month: date.getMonth(), _day: date.getDate()};
    }

    function dateishFromYMDString(YMD) {
        YMD = YMD.split('-');
        return dateishFromDate(new Date(+YMD[0], +YMD[1]-1, +YMD[2]));
    }

    function dateishCopy(dateish, extension) {
        extension = extension || {};
        return {
            _year: '_year' in extension ? extension._year : dateish._year,
            _month: '_month' in extension ? extension._month : dateish._month,
            _day: '_day' in extension ? extension._day : dateish._day
        };
    }

    function roundDecade(year) {
        if (year._year) {
            return roundDecade(year._year);
        }

        return Math.floor(year / 10) * 10;
    }

    // Clamp a number into a min/max limit
    function clamp(n, min, max) {
        if (n > max) { n = max; }
        if (n < min) { n = min; }

        return n;
    }

    /**
     * @class Ink.UI.Calendar
     * @constructor
     * @version 1
     *
     * @param {String|Element}      selector                    Calendar Eelment
     * @param {Object}              [options]                   Options
     * @param {String}              [options.startDate]         Initial date. Must be in yyyy-mm-dd format. Defaults to the current day.
     * @param {String}              [options.dateRange]         Minimum and maximum dates which can be selected, ex: '1990-08-25:2020-11-10'
     * @param {Number}              [options.startWeekDay]      First day of the week. Sunday is zero. Defaults to 1 (Monday).
     * @param {String}              [options.prevLinkText]      Text for the previous button. Defaults to '«'.
     * @param {String}              [options.nextLinkText]      Text for the previous button. Defaults to '«'.
     * @param {String}              [options.ofText]            HTML string in the TD between month and year. Defaults to ' of '.
     * @param {Function}            [options.onSetDate]         Callback to execute when the date is set.
     * @param {Function}            [options.validYearFn]       Function to validate the each year. Use this to filter the available dates. (in the decade view)
     * @param {Function}            [options.validMonthFn]      Function to validate the each month. Use this to filter the available dates. (in the year view)
     * @param {Function}            [options.validDayFn]        Function to validate the each day. Use this to filter the available dates. (in the month view)
     * @param {Function}            [options.nextValidDateFn]   Function to calculate the next valid date, given the current one. Use this only if your valid days are many months or years apart, otherwise stick to validYearFn, validMonthFn and validDayFn.
     * @param {Function}            [options.prevValidDateFn]   Function to calculate the previous valid date, given the current. Use this only if your valid days are many months or years apart, otherwise stick to validYearFn, validMonthFn and validDayFn.
     * @param {Object}              [options.wDay]              Week day names. Example: { 0:'Sunday', 1:'Monday', ...}. Defaults to english week day names.
     * @param {Object}              [options.month]             Month names. Example: { 1: 'January', 2: 'February', ...}. Defaults to the english month names.
     *
     * @sample Ink_UI_Calendar_1.html
     */
    function Calendar() {
        Common.BaseUIComponent.apply(this, arguments);
    }

    Calendar._name = 'Calendar_1';

    Calendar._optionDefinition = {
        dateRange:       ['String', null],

        nextLinkText:    ['String', '»'],
        prevLinkText:    ['String', '«'],
        ofText:          ['String', ' of '],
        onSetDate:       ['Function', null],
        startDate:       ['String', null], // format yyyy-mm-dd,
        startWeekDay:    ['Number', 1],

        // Validation
        validDayFn:      ['Function', null],
        validMonthFn:    ['Function', null],
        validYearFn:     ['Function', null],
        nextValidDateFn: ['Function', null],
        prevValidDateFn: ['Function', null],
        yearRange:       ['String', null],  /* [3.1.0] deprecate this */

        // Text
        month: ['Object', {
             1:'January',
             2:'February',
             3:'March',
             4:'April',
             5:'May',
             6:'June',
             7:'July',
             8:'August',
             9:'September',
            10:'October',
            11:'November',
            12:'December'
        }],
        wDay: ['Object', {
            0:'Sunday',
            1:'Monday',
            2:'Tuesday',
            3:'Wednesday',
            4:'Thursday',
            5:'Friday',
            6:'Saturday'
        }]
    };

    Calendar.prototype = {
        _init: function () {
            if (this._options.startWeekDay < 0 || this._options.startWeekDay > 6) {
                Ink.warn('Ink.UI.Calendar_1: option "startWeekDay" must be between 0 (sunday) and 6 (saturday)');
                this._options.startWeekDay = clamp(this._options.startWeekDay, 0, 6);
            }

            Ink.extendObj(this._options,this._lang || {});

            this._setMinMax( this._options.dateRange || this._options.yearRange );

            this.setDate(this._options.startDate || new Date());  // Sets the date
            this._bindEvents();  // Binds events for changing date and whatnot.

            this._renderTopBar();  // Creates the thead
            this.monthView();  // Creates the tbody, shows the month
        },

        _bindEvents: function () {
            var self = this;

            // Top bar
            Event.on(this._element, 'click', '[href^="#monthchanger"]', function (ev) {
                ev.preventDefault();
                self.yearView();
            });
            Event.on(this._element, 'click', '[href^="#yearchanger"]', function (ev) {
                ev.preventDefault();
                self.decadeView();
            });

            // Next and previous buttons
            Event.on(this._element, 'click', ':not(.disabled) [href^="#next"], :not(.disabled) [href^="#prev"]', function (ev) {
                ev.preventDefault();
                var tbody = Ink.s('tbody', self._element);
                var isNext = /#next$/.test(ev.currentTarget.href);
                var fragment = Css.hasClassName(tbody, 'month') ? '_month' :
                               Css.hasClassName(tbody, 'year')  ? '_year' :
                                                                  '_decade';

                var increment = isNext ? 1 : -1;

                self._onNextPrevClicked(fragment, increment);
            });

            function extendDate(partialDateish) {
                var dt = dateishCopy(self, partialDateish);
                self._setDate(dt);
            }

            // Month view
            Event.on(this._element, 'click', '[data-cal-day]', function (ev) {
                extendDate({ _day: +ev.currentTarget.getAttribute('data-cal-day') });
                self.monthView();
            });
            // Year view
            Event.on(this._element, 'click', '[data-cal-month]', function (ev) {
                extendDate({ _month: +ev.currentTarget.getAttribute('data-cal-month') });
                self.monthView();
            });
            // Decade view
            Event.on(this._element, 'click', '[data-cal-year]', function (ev) {
                extendDate({ _year: +ev.currentTarget.getAttribute('data-cal-year') });
                self.yearView();
            });
        },

        _renderTopBar: function () {
            this._calendarHeader = this._element.appendChild(
                    document.createElement("thead"));

            var calendarHeaderTr = this._calendarHeader.appendChild(InkElement.create('tr'));

            var monthPrevTd = calendarHeaderTr.appendChild(InkElement.create('th', {
                className: 'previous' }));

            var monthTitleTd = calendarHeaderTr.appendChild(InkElement.create('th', {
                className: 'title',
                colspan: '5'
            }));

            var monthNextTd = calendarHeaderTr.appendChild(InkElement.create('th', {
                className: 'next' }));


            (function renderMonthTitle() {
                this._monthChanger = monthTitleTd.appendChild(InkElement.create('a', {
                    href: '#monthchanger',
                    className: 'ink-calendar-link-month'
                }));

                monthTitleTd.appendChild(InkElement.create('span', {
                    className: 'ink-calendar-of-text',
                    setHTML: this._options.ofText
                }));

                this._yearChanger = monthTitleTd.appendChild(InkElement.create('a', {
                    href: '#yearchanger',
                    className: 'ink-calendar-link-year'
                }));

                this._updateTopBar();
            }.call(this));


            monthNextTd.appendChild(InkElement.create('a', {
                href: '#next',
                className: 'change_month_next'  /* fa fa-angle-double-right (?) */,
                setHTML: this._options.nextLinkText
            }));

            monthPrevTd.appendChild(InkElement.create('a', {
                href: '#prev',
                className: 'change_month_prev'  /* see above */,
                setHTML: this._options.prevLinkText
            }));
        },

        _updateTopBar: function () {
            if (this._monthChanger && this._yearChanger) {
                InkElement.setTextContent(this._monthChanger, this._options.month[this._month + 1]);
                InkElement.setTextContent(this._yearChanger, this._year);
            }
        },

        _replaceTbody: function (className) {
            var existingTbody = Ink.s('tbody', this._element);
            if (existingTbody) {
                InkElement.remove(existingTbody);
            }
            return this._element.appendChild(InkElement.create('tbody', { className: className || '' }));
        },

        /**
         * Show the month view (the one with the days).
         *
         * @method monthView
         */
        monthView: function () {
            var container = this._replaceTbody('month');

            container.appendChild(
                    this._getMonthCalendarHeader(this._options.startWeekDay));

            container.appendChild(
                    this._getDayButtons(dateishCopy(this)));
        },

        /** Write the top bar of the calendar (M T W T F S S) */
        _getMonthCalendarHeader: function (startWeekDay) {
            var header = InkElement.create('tr', {
                className: 'header'
            });

            var wDay;
            for(var i=0; i<7; i++){
                wDay = (startWeekDay + i) % 7;
                header.appendChild(InkElement.create('td', {
                    setTextContent: this._options.wDay[wDay].substring(0, 1)
                }));
            }

            return header;
        },

        /**
         * Figure out where the first day of a month lies
         * in the first row of the calendar.
         *
         *      having options.startWeekDay === 0
         *
         *      Su Mo Tu We Th Fr Sa  
         *                         1  <- The "1" is in the 7th day. return 6.
         *       2  3  4  5  6  7  8  
         *       9 10 11 12 13 14 15  
         *      16 17 18 19 20 21 22  
         *      23 24 25 26 27 28 29  
         *      30 31
         *
         * This obviously changes according to the user option "startWeekDay"
         **/
        _getFirstDayIndex: function (year, month) {
            var wDayFirst = (new Date( year , month , 1 )).getDay();  // Sunday=0
            var startWeekDay = this._options.startWeekDay || 0;  // Sunday=0

            var result = wDayFirst - startWeekDay;

            result %= 7;

            if (result < 0) {
                result += 7;
            }

            return result;
        },

        _getDayButtons: function (date) {
            var daysInMonth = this._daysInMonth(date._year, date._month);

            var ret = document.createDocumentFragment();

            var tr = InkElement.create('tr');
            ret.appendChild(tr);

            var firstDayIndex = this._getFirstDayIndex(date._year, date._month);

            // Add padding if the first day of the month is not monday.
            for (var i = 0; i < firstDayIndex; i ++) {
                tr.appendChild(InkElement.create('td'));
            }

            for (date._day = 1; date._day <= daysInMonth; date._day++) {
                if ((date._day - 1 + firstDayIndex) % 7 === 0){ // new week, new tr
                    tr = ret.appendChild(InkElement.create('tr'));
                }

                tr.appendChild(this._getButton({
                    number: date._day,
                    date: date,
                    dayMonthOrYear: 'day',
                    validator: this._acceptableDay
                }));
            }
            return ret;
        },

        /**
         * Show the year view (the one with the months).
         *
         * @method yearView
         * @return {void}
         * @public
         */
        yearView: function () {
            var yearView = this._replaceTbody('year');

            var tr = document.createElement('tr');
            for(var mon=0; mon<12; mon++){
                var monthButton = this._getButton({ number: mon,
                    date: { _year: this._year, _month: mon },
                    dayMonthOrYear: 'month',
                    validator: this._acceptableMonth,
                    linkText: this._options.month[mon + 1].substring(0, 3)
                });

                tr.appendChild(monthButton);

                if (mon % 4 === 3) {
                    monthButton.setAttribute('colspan', 4);
                    yearView.appendChild(tr);
                    tr = document.createElement('tr');
                }
            }

            return yearView;
        },

        /**
         * Show the decade view (the one with the years).
         *
         * @method decadeView
         * @return {void}
         * @public
         */
        decadeView: function () {
            var view = this._replaceTbody('decade');

            var thisDecade = roundDecade(this);
            var nextDecade = thisDecade + 10;

            var tr = view.appendChild(InkElement.create('tr'));

            for (var year = thisDecade; year < nextDecade; year++) {
                var td = this._getButton({
                    number: year,
                    date: { _year: year },
                    dayMonthOrYear: 'year',
                    validator: this._acceptableYear
                });

                tr.appendChild(td);

                if (year % 5 === 4) {
                    td.setAttribute('colspan', 3);
                    tr = view.appendChild(InkElement.create('tr'));
                }
            }
        },

        /** 
         * Generate a button (td > a) with a data-cal-year/month/day.
         * 
         * DRY base for {month,year,decade}View() functions.
         **/
        _getButton: function (opt /* contains: number, date, dayMonthOrYear, validator, linkText */) {
            var button = InkElement.create('td');
            var link = button.appendChild(InkElement.create('a', {
                setTextContent: opt.linkText || opt.number
            }));

            var isValid = opt.validator.call(this, opt.date);
            var isToday = this._dateCmpUntil(this, opt.date,
                '_' + opt.dayMonthOrYear) === 0;

            if (isValid) {
                link.setAttribute('data-cal-' + opt.dayMonthOrYear, opt.number);
            } else {
                button.className = 'disabled';
            }

            if (isToday && isValid) {
                button.className = 'active';
            }

            return button;
        },

        /**
         * Gets the currently selected date as a JavaScript date.
         *
         * @method getDate
         * @return {Date} Current date
         * @public
         */
        getDate: function () {
            return dateFromDateish(this);
        },

        /**
         * Sets the currently selected date.
         *
         * @method setDate
         * @param {Date} newDate A Date object or a 'YYYY-MM-DD' string. Make sure it's a valid date (that is, make sure it's within options.dateRange, and that you haven't defined a validDateFn which states this date is invalid.)
         * @return {void}
         * @public
         **/
        setDate: function (newDate) {
            if (newDate && typeof newDate.getDate === 'function') {
                newDate = dateishFromDate(newDate);
            }

            if ( typeof newDate === 'string' && newDate.split('-').length === 3 ) {
                newDate = dateishFromYMDString(newDate);
            }

            this._setDate( newDate );

            this.monthView();
        },

        _setDate: function (newDate) {
            newDate = this._fitDateToRange(newDate);

            if (this._day !== undefined &&
                    this._dateCmp(this, newDate) === 0) {
                return;
            }

            var yearChanged = this._year !== newDate._year;
            var monthChanged = this._month !== newDate._month || yearChanged;

            this._year = newDate._year;
            this._month = newDate._month;
            this._day = newDate._day;

            this._updateTopBar();

            var changeEvent = {
                date: this.getDate(),
                year: this._year,
                month: this._month,
                day: this._day
            };

            if (typeof this._options.onSetDate === 'function') {
                this._options.onSetDate.call(this, changeEvent);
            }

            /* [3.2.0] deprecate onYearSelected, onMonthSelected. onSetDate is enough. */
            var self = this;
            function callDeprecatedUserCallback(callback) {
                if (typeof callback === 'function') {
                    Ink.warn('The Ink.UI.Calendar (and thus, Ink.UI.DatePicker) callbacks "onYearSelected" and "onMonthSelected" are eventually going to be deprecated.');
                    callback.call(self, self, changeEvent);
                }
            }

            if (yearChanged) {
                callDeprecatedUserCallback(this._options.onYearSelected);
            }
            if (monthChanged) {
                callDeprecatedUserCallback(this._options.onMonthSelected);
            }
        },

        /**
         * Called when "next" or "previous" button is clicked.
         *
         * @method _onNextPrevClicked
         *
         * @param dateFragment "Year", "Decade", or "Month", depending on current view
         * @param nextOrPrev {Number} +1 or -1
         **/
        _onNextPrevClicked: function (dateFragment, increment) {
            var newDate = this._tryLeap(dateFragment, increment);

            if (!newDate) { return; }

            this._setDate(newDate);

            if (dateFragment === '_month') {
                this.monthView();
            } else if (dateFragment === '_year') {
                this.yearView();
            } else if (dateFragment === '_decade') {
                this.decadeView();
            }
        },

        /**
         * Checks if a date is between the valid range.
         * Starts by checking if the date passed is valid. If not, will fallback to the 'today' date.
         * Then checks if the all params are inside of the date range specified. If not, it will fallback to the nearest valid date (either Min or Max).
         *
         * @method _fitDateToRange
         * @param  dateish
         * @return {Array}       Array with the final processed date.
         * @private
         */
        _fitDateToRange: function( date ) {
            if (this._dateCmp(date, this._min) < 0) {
                return dateishCopy(this._min);
            } else if (this._dateCmp(date, this._max) > 0) {
                return dateishCopy(this._max);
            }

            return dateishCopy(date);  // date is okay already, just copy it.
        },

        _acceptableDay: function (date) {
            return this._acceptableDateComponent(date, 'validDayFn') && this._acceptableMonth(date);
        },

        _acceptableMonth: function (date) {
            return this._acceptableDateComponent(date, 'validMonthFn') && this._acceptableYear(date);
        },

        _acceptableYear: function (date) {
            return this._acceptableDateComponent(date, 'validYearFn');
        },

        /** DRY base for the above 2 functions */
        _acceptableDateComponent: function (date, userCb) {
            if (this._options[userCb]) {
                return !!this._callUserCallback(this._options[userCb], date);
            } else {
                return this._dateWithinRange(date);
            }
        },

        /**
         * Function that returns the number of days on a given month on a given year
         *
         * @method _daysInMonth
         * @param {Number} _y - year
         * @param {Number} _m - month
         * @private
         * @return {Number} The number of days on a given month on a given year
         */
        _daysInMonth: function(_y,_m){
            // We get the days in this month by creating a javascript Date object
            // with an overflowing month (which makes it advance to the next month)
            // and an underflowing day (which makes it recede to the last day of the previous month)
            return (new Date(_y, _m + 1, 0)).getDate();
        },

        /**
         * DRY base for a function which tries to get the next or previous valid year or month.
         *
         * It checks if we can go forward by using _dateCmp with atomic
         * precision (this means, {_year} for leaping years, and
         * {_year, month} for leaping months), then it tries to get the
         * result from the user-supplied callback (nextDateFn or prevDateFn),
         * and when this is not present, advance the date forward using the
         * `advancer` callback.
         *
         * @example:
         *     cal._tryLeap('_year', 1) // -> The next year, if valid. Otherwise, null.
         *     cal._tryLeap('_decade', 1) // -> The next decade, if valid. Otherwise, null.
         *     cal._tryLeap('_month', -1) // -> The previous month, if valid. Otherwise, null.
         */
        _tryLeap: function (atomName, increment) {
            var date = dateishCopy(this);
            var before = dateishCopy(date);

            var directionName = increment > 0 ? 'next' : 'prev';

            var leapUserCb = this._options[directionName + 'ValidDateFn'];
            if (leapUserCb) {
                return this._callUserCallbackDate(leapUserCb, date);
            } else {
                if (atomName === '_decade') {
                    increment *= 10;
                    date._year += increment;
                    date._year = roundDecade(date._year);
                } else {
                    date[atomName] += increment;
                    // If the above makes _month > 11 or < 0 it's okay, but days have to be clamped to the month limit because every month has its own limit.
                    var daysInThisMonth = this._daysInMonth(date._year, date._month);
                    if (date._day > daysInThisMonth) {
                        date._day = daysInThisMonth;
                    }

                    date = dateishFromDate(new Date(date._year, date._month, date._day ));
                }
            }

            date = this._fitDateToRange(date);

            if (this._dateCmpUntil(date, before, atomName) === 0) {
                return null;
            }

            return date;
        },

        _callUserCallback: function (cb, date) {
            return cb.call(this, date._year, date._month + 1, date._day);
        },

        _callUserCallbackDate: function (cb, date) {
            var ret = this._callUserCallback(cb, date);
            return ret ? dateishFromDate(ret) : null;
        },

        /**
         * Sets the range of dates allowed to be selected in the Date Picker.
         *
         * Parses the string passed in by the user for options.dateRange.
         *
         * @method _setMinMax
         * @param {String} dateRange Two dates separated by a ':'. Example: 2013-01-01:2013-12-12
         * @private
         */
        _setMinMax: function( dateRange ) {
            var self = this;

            var noMinLimit = { _year: -Number.MAX_VALUE, _month: 0, _day: 1 };
            var noMaxLimit = { _year: Number.MAX_VALUE, _month: 11, _day: 31 };

            function noLimits() {
                self._min = noMinLimit;
                self._max = noMaxLimit;
            }

            if (!dateRange) {
                noLimits();
                return;
            }

            var minMax = dateRange.toUpperCase().split( ':' );

            function readMinMax(str) {
                return str === 'NOW'         ? dateishFromDate(new Date()) :
                       str === 'EVER'        ? null :
                                               dateishFromYMDString(str);
            }

            this._min = readMinMax(minMax[0]) || noMinLimit;
            this._max = readMinMax(minMax[1]) || noMaxLimit;

            // _max should be greater than, or equal to, _min.
            if (this._dateCmp(this._max, this._min) === -1) {
                noLimits();
            }
        },

        /**
         * Checks whether a date is within the valid date range
         * @method _dateWithinRange
         * @param {Object} date Input dateish
         * @return {Boolean} Whether the date is within range.
         * @private
         */
        _dateWithinRange: function (date) {
            return (this._dateCmp(date, this._max) <= 0 &&
                    this._dateCmp(date, this._min) >= 0);
        },

        /**
         * Compare two dateish objects. Returns positive if self > oth,
         * negative if self < oth, or 0 if they are equal.
         */
        _dateCmp: function (self, oth) {
            return this._dateCmpUntil(self, oth, '_day');
        },

        /**
         * _dateCmp with varied precision. You can compare down to the day field, or, just to the month.
         * // the following two dates are considered equal because we asked
         * // _dateCmpUntil to just check up to the years.
         *
         * _dateCmpUntil({_year: 2000, _month: 10}, {_year: 2000, _month: 11}, '_year') === 0
         * _dateCmpUntil({_year: 2000, _month: 10}, {_year: 2000, _month: 11}, '_day') === -1  // doesn't break even though the second date doesn't have a _day.
         *
         * @method _dateCmpUntil
         * @param self {Object} Dateish 1
         * @param oth {Object} Dateish 2
         * @param depth {String} One of '_year', '_month', '_day' or '_decade'. This defines how deep your comparison goes. For example, when `depth` is '_month', all days of the same months are considered equal (thus this function returns `0`).
         */
        _dateCmpUntil: function (self, oth, depth) {
            if (depth === '_decade') {
                return cmp(roundDecade(self._year), roundDecade(oth._year));
            }

            var props = ['_year', '_month', '_day'];
            var endLoop = InkArray.keyValue(depth, props, true) + 1;

            var self_cpy = { _month: 0, _day: 1 };
            var oth_cpy = { _month: 0, _day: 1 };

            for (var i = 0; i < endLoop; i++) {
                if (
                    self[props[i]] === undefined ||
                    oth[props[i]] === undefined
                ) {
                    break;
                }

                self_cpy[props[i]] = self[props[i]];
                oth_cpy[props[i]] = oth[props[i]];
            }

            return cmp(dateFromDateish(self_cpy), dateFromDateish(oth_cpy));
        },

        /**
         * Checks if the month view (where you select the day) is active.
         *
         * @method isMonthRendered
         * @return {Boolean} True if the calendar screen is in 'select day' mode
         * @public
         */
        isMonthRendered: function(){
            return !!Ink.s('thead.month', this._element);
        },

        /**
         * Checks if the year view (where you select the month) is active.
         *
         * @method isYearRendered
         * @return {Boolean} True if the calendar screen is in 'select month' mode
         * @public
         */
        isYearRendered: function(){
            return !!Ink.s('thead.year', this._element);
        },

        /**
         * Checks if the decade view (where you select the year) is active.
         *
         * @method isDecadeRendered
         * @return {Boolean} True if the calendar screen is in 'select year' mode
         * @public
         */
        isDecadeRendered: function(){
            return !!Ink.s('thead.decade', this._element);
        },

        /**
         * Remove the calendar from the DOM and clean up the related events
         *
         * @method destroy
         * @public
         **/
        destroy: function () {
            Event.off(this._element);
            InkElement.remove(this._element);
            Common.unregisterInstance.call(this);
        }
    };


    Common.createUIComponent(Calendar);

    return Calendar;
});
