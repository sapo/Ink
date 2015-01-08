/**
 * Date selector
 * @module Ink.UI.DatePicker_1
 * @version 1
 */

Ink.createModule('Ink.UI.DatePicker', '1', ['Ink.UI.Common_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1','Ink.Util.Date_1', 'Ink.UI.Calendar_1'], function(Common, Event, Css, InkElement, Selector, InkArray, InkDate, Calendar ) {
    'use strict';

    /**
     * @class Ink.UI.DatePicker
     * @constructor
     * @version 1
     *
     * @param {String|Element}      selector                    Datepicker element
     * @param {Object}              [options]                   Options
     * @param {Boolean}             [options.autoOpen]          Flag to automatically open the datepicker.
     * @param {Boolean}             [options.showClean]         If the clean button should be visible. Defaults to true.
     * @param {Boolean}             [options.showClose]         If the close button should be visible. Defaults to true.
     * @param {String}              [options.cleanText]         Text for the clean button. Defaults to 'Clear'.
     * @param {String}              [options.closeText]         Text for the close button. Defaults to 'Close'.
     * @param {String}              [options.cssClass]          CSS class to be applied on the datepicker
     * @param {String}              [options.position]          Position for the datepicker. Either 'right' or 'bottom'. Defaults to 'right'.
     * @param {String|Element}      [options.pickerField]       (if not using in an input[type="text"]) Element which displays the DatePicker when clicked. Defaults to an "open" link.
     * @param {Boolean}             [options.displayInSelect]   Flag to display the component in a select element.
     * @param {String|Element}      [options.dayField]          (if using options.displayInSelect) `select` field with days.
     * @param {String|Element}      [options.monthField]        (if using options.displayInSelect) `select` field with months.
     * @param {String|Element}      [options.yearField]         (if using options.displayInSelect) `select` field with years.
     * @param {String}              [options.format]            Date format string
     * @param {Boolean}             [options.shy]               If the datepicker should start automatically. Defaults to true.
     * @param {Boolean}             [options.onFocus]           If the datepicker should open when the target element is focused. Defaults to true.
     * @param {Object}              [options.options for calendar] The following options are used to create the Calendar:
     * @param {String}              [options.startDate]         Initial date. Must be in yyyy-mm-dd format. Defaults to the current day.
     * @param {String}              [options.dateRange]         Minimum and maximum dates which can be selected, ex: '1990-08-25:2020-11-10'
     * @param {Number}              [options.startWeekDay]      First day of the week. Sunday is zero. Defaults to 1 (Monday).
     * @param {String}              [options.nextLinkText]      Text for the previous button. Defaults to '«'.
     * @param {String}              [options.prevLinkText]      Text for the next button. Defaults to '»'.
     * @param {String}              [options.ofText]            HTML string in the TD between month and year. Defaults to ' of '.
     * @param {Function}            [options.onSetDate]         Callback to execute when the date changes.
     * @param {Object}              [options.month]             Month names. Example: { 1: 'January', 2: 'February', ...}. Defaults to the english month names.
     * @param {Object}              [options.wDay]              Week day names. Example: { 0:'Sunday', 1:'Monday', ...}. Defaults to english week day names.
     * @param {Function}            [options.validYearFn]       (advanced) Function to validate the each year. Use this to filter the available dates. (in the decade view)
     * @param {Function}            [options.validMonthFn]      (advanced) Function to validate the each month. Use this to filter the available dates. (in the year view)
     * @param {Function}            [options.validDayFn]        (advanced) Function to validate the each day. Use this to filter the available dates. (in the month view)
     * @param {Function}            [options.nextValidDateFn]   (advanced) Function to calculate the next valid date, given the current one. Use this only if your valid days are many months or years apart, otherwise stick to validYearFn, validMonthFn and validDayFn.
     * @param {Function}            [options.prevValidDateFn]   (advanced) Function to calculate the previous valid date, given the current. Use this only if your valid days are many months or years apart, otherwise stick to validYearFn, validMonthFn and validDayFn.
     *
     * @sample Ink_UI_DatePicker_1.html
     */
    function DatePicker() {
        Common.BaseUIComponent.apply(this, arguments);
    }

    DatePicker._name = 'DatePicker_1';

    DatePicker._optionDefinition = {
        autoOpen:        ['Boolean', false],
        cleanText:       ['String', 'Clear'],
        closeText:       ['String', 'Close'],
        pickerField:     ['Element', null],
        containerElement:['Element', null],
        cssClass:        ['String', 'ink-calendar bottom'],
        dateRange:       ['String', null],
        
        // use this in a <select>
        displayInSelect: ['Boolean', false],
        dayField:        ['Element', null],
        monthField:      ['Element', null],
        yearField:       ['Element', null],

        format:          ['String', 'yyyy-mm-dd'],
        nextLinkText:    ['String', '»'],
        ofText:          ['String', ' of '],
        onFocus:         ['Boolean', true],
        // TODO removed option, what to do? onMonthSelected: ['Function', null],
        onSetDate:       ['Function', null],
        // TODO removed option, what to do? onYearSelected:  ['Function', null],
        position:        ['String', 'right'],
        prevLinkText:    ['String', '«'],
        showClean:       ['Boolean', true],
        showClose:       ['Boolean', true],
        shy:             ['Boolean', true],
        startDate:       ['String', null], // format yyyy-mm-dd,
        startWeekDay:    ['Number', 1],

        // Validation
        validDayFn:      ['Function', null],
        validMonthFn:    ['Function', null],
        validYearFn:     ['Function', null],
        nextValidDateFn: ['Function', null],
        prevValidDateFn: ['Function', null],
        yearRange:       ['String', null],

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

    DatePicker.prototype = {
        /**
         * Initialization function. Called by the constructor and receives the same parameters.
         *
         * @method _init
         * @private
         */
        _init: function(){
            this._calendarEl = InkElement.create('table', { className: 'ink-calendar hide-all' });
            this._calendar = new Calendar(this._calendarEl, this._options);

            this._options.format = this._dateParsers[ this._options.format ] || this._options.format;

            this._appendDatePickerToDom();

            this._hoverPicker = false;

            this._picker = this._options.pickerField || null;


            if(!this._options.onFocus || this._options.displayInSelect){
                if(!this._options.pickerField){
                    this._picker = InkElement.create('a', {
                        href: '#open_cal',
                        setHTML: 'open',
                        insertBottom: this._element.parentNode,
                        className: 'ink-datepicker-picker-field'
                    });
                } else {
                    this._picker = Common.elOrSelector(this._options.pickerField, 'pickerField');
                }
            }
            var self = this;
            this._calendar.setOption('onSetDate', function () {
                if(!self._options.displayInSelect){
                    self._element.value = self._writeDateInFormat();
                } else {
                    self._options.dayField.value = this._day;
                    self._options.monthField.value = this._month + 1;
                    self._options.yearField.value = this._year;
                }
            });
            this._renderClearAndCloseButtons();
            this._listenToContainerObjectEvents();
            this._addOpenCloseEvents();
            this._addDateChangeHandlersToInputs();
        },

        _validate: function () {
            if(this._options.displayInSelect &&
                    !(this._options.dayField && this._options.monthField && this._options.yearField)){
                throw new Error(
                    'Ink.UI.DatePicker: displayInSelect option enabled.'+
                    'Please specify dayField, monthField and yearField selectors.');
            }
        },

        _renderClearAndCloseButtons: function () {
            if((!this._options.showClose) && (!this._options.showClean)){ return; }
            var both = !!(
                this._options.showClose &&
                this._options.showClean);
            var calendarHeader = Ink.s('thead', this._calendar.getElement());
            var clearCloseBar = InkElement.create("tr", {
                className: 'ink-calendar-top-options' });
            calendarHeader.insertBefore(clearCloseBar, calendarHeader.firstChild);
            //InkElement.insertTop(calendarHeader, clearCloseBar);
            var th;
            if(this._options.showClean){
                th = clearCloseBar.appendChild(InkElement.create('th', { colspan: both ? '3' : '7' }));
                th.appendChild(InkElement.create('a', {
                    className: 'top-button clean',
                    setHTML: this._options.cleanText
                }));
            }
            if (both) {
                // Add a th to the middle.
                th = clearCloseBar.appendChild(InkElement.create('th', { colspan: '1' }));
            }
            if(this._options.showClose){
                th = clearCloseBar.appendChild(InkElement.create('th', { colspan: both ? '3' : '7' }));
                th.appendChild(InkElement.create('a', {
                    className: 'top-button close',
                    setHTML: this._options.closeText
                }));
            }
        },

        /**
         * This method returns the date written with the format specified on the options
         *
         * @method _writeDateInFormat
         * @return {String} Returns the current date of the object in the specified format
         * @private
         */
        _writeDateInFormat:function(){
            return InkDate.get( this._options.format , this.getDate());
        },

        _render: function () { /* Legacy. Real thing was moved to Calendar. Here just in case someone calls it. */ },

        _addDateChangeHandlersToInputs: function () {
            var fields = [this._element];
            if (this._options.displayInSelect) {
                fields = [
                    this._options.dayField,
                    this._options.monthField,
                    this._options.yearField];
            }
            Event.observeMulti(fields ,'change', Ink.bindEvent(function(){
                this._updateDate( );

                if ( !this._inline && !this._hoverPicker ) {
                    this._hide(true /* it's a blur */);
                }
            },this));
        },

        _updateDate: function () {
            var dataParsed;
            if(!this._options.displayInSelect && this._element.value){
                dataParsed = this._parseDate(this._element.value);
            } else if (this._options.displayInSelect) {
                dataParsed = {
                    _year: this._options.yearField[this._options.yearField.selectedIndex].value,
                    _month: this._options.monthField[this._options.monthField.selectedIndex].value - 1,
                    _day: this._options.dayField[this._options.dayField.selectedIndex].value
                };
            }

            if (dataParsed) {
                this._calendar.setDate(dataParsed);
            }
        },

        /**
         * This function returns the given date in the dateish format
         *
         * @method _parseDate
         * @param {String} dateStr A date on a string.
         * @return {dateish|null} The dateish object if it was a valid date, null otherwise
         * @private
         */
        _parseDate: function(dateStr){
            var date = InkDate.set( this._options.format , dateStr );
            if (date) {
                return { _year: date.getFullYear(), _month: date.getMonth(), _day: date.getDate() };
            }
            return null;
        },

        /**
         * Shows the calendar.
         *
         * @method show
         * @return {void}
         * @public
         **/
        show: function () {
            Css.removeClassName(this._calendarEl, 'hide-all');
        },

         /**
         * Gets the Ink.UI.Calendar instance which DatePicker uses under the hood
         *
         * @method getCalendar
         * @return {Ink.UI.Calendar} The Calendar instance of this DatePicker.
         * @public
         **/
        getCalendar: function () { return this._calendar; },

        _addOpenCloseEvents: function () {
            var opener = this._picker || this._element;

            Event.observe(opener, 'click', Ink.bindEvent(function(e){
                Event.stop(e);
                this.show();
            },this));
            Event.observe(opener, 'focus', Ink.bindEvent(function(e){
                Event.stop(e);
                this.show();
            },this));

            if (this._options.autoOpen) {
                this.show();
            }

            if(!this._options.displayInSelect){
                Event.observe(opener, 'blur', Ink.bindEvent(function() {
                    if ( !this._hoverPicker ) {
                        this._hide(true);
                    }
                },this));
            }

            if (this._options.shy) {
                // Close the picker when clicking elsewhere.
                Event.observe(document,'click',Ink.bindEvent(function(e){
                    var target = Event.element(e);

                    // "elsewhere" is outside any of these elements:
                    var cannotBe = [
                        this._options.dayField,
                        this._options.monthField,
                        this._options.yearField,
                        this._picker,
                        this._element
                    ];

                    for (var i = 0, len = cannotBe.length; i < len; i++) {
                        if (cannotBe[i] && InkElement.descendantOf(cannotBe[i], target)) {
                            return;
                        }
                    }

                    this._hide(true);
                },this));
            }
        },

        _appendDatePickerToDom: function () {
            if(this._options.containerElement) {
                var appendTarget =
                    Common.elOrSelector(this._options.containerElement);
                appendTarget.appendChild(this._calendarEl);
            }

            var parentIsControl = Selector.matchesSelector(
                this._element.parentNode,
                '.ink-form .control-group .control');

            if (parentIsControl) {
                this._wrapper = this._element.parentNode;
                this._wrapperIsControl = true;
            } else {
                this._wrapper = InkElement.create('div', { className: 'ink-datepicker' /* TODO must be datepicker-wrapper because autoload */ });
                InkElement.wrap(this._element, this._wrapper);
            }

            InkElement.insertAfter(this._calendarEl, this._element);
        },

        _listenToContainerObjectEvents: function () {
            Event.observe(this._calendarEl,'mouseover',Ink.bindEvent(function(e){
                Event.stop( e );
                this._hoverPicker = true;
            },this));

            Event.observe(this._calendarEl,'mouseout',Ink.bindEvent(function(e){
                Event.stop( e );
                this._hoverPicker = false;
            },this));
        },

        /**
         * Clear the `<input>`'s value.
         *
         * @method clear
         * @return {void}
         * @public
         **/
        clear: function () {
            if(this._options.displayInSelect){
                this._options.yearField.selectedIndex = 0;
                this._options.monthField.selectedIndex = 0;
                this._options.dayField.selectedIndex = 0;
            } else {
                this._element.value = '';
            }
        },

        /**
         * Hide the DatePicker.
         *
         * @method hide
         * @public
         */
        hide: function () { this._hide(false); },

        _hide: function(blur) {
            blur = blur === undefined ? true : blur;
            if (blur === false || (blur && this._options.shy)) {
                Css.addClassName(this._calendarEl, 'hide-all');
            }
        },

        /**
         * This method allows the user to set the DatePicker's date on run-time.
         *
         * @method setDate
         * @param {Date|String} dateString A Date object, or date string in yyyy-mm-dd format.
         * @returns {void}
         * @public
         */
        setDate: function( dateString ) {
            this._calendar.setDate(dateString);
        },

        /**
         * Gets the currently selected date as a JavaScript date.
         *
         * @method getDate
         * @returns {Date} Current date
         * @public
         */
        getDate: function () {
            return this._calendar.getDate();
        },

        /*
         * // TODO implement this
         * Prototype's method to allow the 'i18n files' to change all objects' language at once.
         * @xparam {Object} options Object with the texts' configuration.
         * @xparam {String} options.closeText Text of the close anchor
         * @xparam {String} options.cleanText Text of the clean text anchor
         * @xparam {String} options.prevLinkText "Previous" link's text
         * @xparam {String} options.nextLinkText "Next" link's text
         * @xparam {String} options.ofText The text "of", present in 'May of 2013'
         * @xparam {Object} options.month An object with keys from 1 to 12 for the full months' names
         * @xparam {Object} options.wDay An object with keys from 0 to 6 for the full weekdays' names
         * @xpublic
         */
        lang: function( options ){
            this._lang = options;
        },

        /**
         * Render the month view (call this if the user is in Year or Decade view)
         *
         * @method showMonth
         * @public
         * @returns {void}
         */
        showMonth: function(){
            this._calendar.monthView();
        },

        /**
         * Checks if the month view (where you select the day) is active.
         *
         * @method isMonthRendered
         * @return {Boolean} True if the calendar screen is in 'select day' mode
         * @public
         */
        isMonthRendered: function(){
            return this._calendar.isMonthRendered();
        },

        /**
         * Checks if the year view (where you select the month) is active.
         *
         * @method isYearRendered
         * @return {Boolean} True if the calendar screen is in 'select month' mode
         * @public
         */
        isYearRendered: function(){
            return this._calendar.isYearRendered();
        },

        /**
         * Checks if the decade view (where you select the year) is active.
         *
         * @method isDecadeRendered
         * @return {Boolean} True if the calendar screen is in 'select year' mode
         * @public
         */
        isDecadeRendered: function(){
            return this._calendar.isDecadeRendered();
        },

        /**
         * Key-value object that (for a given key) points to the correct parsing format for the DatePicker
         * @property _dateParsers
         * @type {Object}
         * @readOnly
         */
        _dateParsers: {
            'yyyy-mm-dd' : 'Y-m-d' ,
            'yyyy/mm/dd' : 'Y/m/d' ,
            'yy-mm-dd'   : 'y-m-d' ,
            'yy/mm/dd'   : 'y/m/d' ,
            'dd-mm-yyyy' : 'd-m-Y' ,
            'dd/mm/yyyy' : 'd/m/Y' ,
            'dd-mm-yy'   : 'd-m-y' ,
            'dd/mm/yy'   : 'd/m/y' ,
            'mm/dd/yyyy' : 'm/d/Y' ,
            'mm-dd-yyyy' : 'm-d-Y'
        },

        /**
         * Destroys this datepicker, removing it from the page.
         *
         * @method destroy
         * @return {void}
         * @public
         **/
        destroy: function () {
            this._wrapper.parentNode.insertBefore(this._element, this._wrapper.nextSibling);
            InkElement.remove(this._wrapper);
            this._calendar.destroy();
            Common.unregisterInstance.call(this);
        }
    };

    Common.createUIComponent(DatePicker);

    return DatePicker;
});
