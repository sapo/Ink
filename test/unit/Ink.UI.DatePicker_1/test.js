Ink.requireModules(['Ink.UI.DatePicker_1', 'Ink.Dom.Css_1', 'Ink.Dom.Event_1', 'Ink.Dom.Element_1', 'Ink.Util.Array_1', 'Ink.Util.I18n_1'], function (DatePicker, Css, InkEvent, InkElement, InkArray, I18n) {

var body = document.body;
var dtElm;
var dt;

function mkDatePicker(options) {
    testWrapper = InkElement.create('div', { insertBottom: body });
    dtElm = InkElement.create('input', { type: 'text', insertBottom: testWrapper });
    dt = new DatePicker(dtElm, Ink.extendObj({
        startDate: '2000-10-10',
        format: 'dd/mm/yyyy'
    }, options));
    return dt;
}

module('main', {
    setup: function () {
        mkDatePicker({});
    },
    teardown: function () {
        InkElement.remove(testWrapper);
    }
});

test('_dateCmp', function () {
    equal(dt._dateCmp({_year: 2012}, {_year: 2012}), 0);
    equal(dt._dateCmp({_year: 2012, _month: 10}, {_year: 2012}), 0);
    equal(dt._dateCmp({_year: 2012, _month: 10}, {_year: 2012, _month: 11}), -1);
    equal(dt._dateCmp({_year: 2012, _month: 10}, {_year: 2012, _month: 9}), 1);
    equal(dt._dateCmp({_year: 2012, _month: 10, _day: 10}, {_year: 2012, _month: 10}), 0);
    equal(dt._dateCmp({_year: 2012, _month: 10, _day: 10}, {_year: 2012, _month: 10, _day: 11}), -1);
    equal(dt._dateCmp({_year: 2012, _month: 10, _day: 10}, {_year: 2012, _month: 10, _day: 9}), 1);
    equal(dt._dateCmp({_year: 2012, _month: 10, _day: 10}, {_year: 2012, _month: 10, _day: 10}), 0);
});

test('setDate', function () {
    dt.setDate('2000-10-12');
    equal(dt._year, 2000);
    equal(dt._month + 1, 10);
    equal(dt._day, 12);

    dt.setDate('2000-01-01');
    equal(dt._year, 2000);
    equal(dt._month + 1, 1);
    equal(dt._day, 1);
});

test('onSetDate', function () {
    var onSetDate = sinon.spy();
    var dt = mkDatePicker({ onSetDate: onSetDate });
    ok(onSetDate.notCalled, 'onSetDate won\'t be called until the user selects a date');

    dt.setDate('2001-10-9');

    ok(onSetDate.notCalled, 'onSetDate won\'t be called when the API sets the date');

    dt._year = 2001;
    dt._month = 5 + 1;
    dt._setDate({ dataset: { calDay: 10 } });

    ok(onSetDate.calledOnce, 'onSetDate called when the user clicks an element which chooses a date');
})

test('regression: onSetDate called on click', function () {
    var onSetDate = sinon.spy();
    var dt = mkDatePicker({ onSetDate: onSetDate });

    dt.show()

    InkEvent.fire(
        Ink.s('[data-cal-day="6"]', dt._containerObject),
        'click')

    ok(onSetDate.calledOnce)
})

test('i18n', function () {
    var i18n = new I18n({
        tt_TT: {  // Test lang
            'datepicker.clean': 'CLEEAN',
            'datepicker.close': 'CLOOSE',
            'datepicker.prev_button': 'PREEV',
            'datepicker.next_button': 'NEEXT',
            'datepicker.of': 'OOF',
            'datepicker.week_days': {
                0: 'X',
                1: 'M',
                2: 'T',
                3: 'W',
                4: 'T',
                5: '%',
                6: '_'
            },
            'datepicker.months': {
                1: 'T',
                2: 'T',
                3: 'T',
                4: 'T',
                5: 'T',
                6: 'T',
                7: 'T',
                8: 'T',
                9: 'T',
                10: 'T',
                11: 'T',
                12: 'T'
            }
        }
    }, 'tt_TT');

    dt.setI18n(i18n);
    dt._render();
    dt.setLanguage('tt_TT');
    dt._renderSuperTopBar();

    ok(/CLEEAN/.test(dt._superTopBar.outerHTML), 'making sure i18n is being used on the top bar');
    ok(/CLOOSE/.test(dt._superTopBar.outerHTML), 'making sure i18n is being used on the top bar');

    dt._showYearSelector();

    ok(/PREEV/.test(dt._yearSelector.outerHTML), 'making sure i18n is being used on the previous button');
    ok(/NEEXT/.test(dt._yearSelector.outerHTML), 'making sure i18n is being used on the next button');

    ok(/OOF/.test(dt._monthDescContainer.outerHTML), 'making sure i18n is being used for the of text');

    ok(/X/.test(dt._getMonthCalendarHeader(0).outerHTML), 'making sure i18n is being used for the week day names');

    dt._showMonthSelector();
    ok(/T/.test(dt._monthSelector.outerHTML), 'making sure i18n is being used for the month names');
});

test('_options.lang', function() {
    mkDatePicker({
        lang: 'pt_PT'
    });

    equal(dt._options.lang, 'pt_PT')
    equal(dt.getLanguage(), 'pt_PT');
    equal(dt.getI18n().lang(), 'pt_PT', 'there\'s an i18n instance here');
});

test('setDate with Date objects', function () {
    dt.setDate(new Date(2010, 11, 12));
    equal(dt._year, 2010);
    equal(dt._month + 1, 12);
    equal(dt._day, 12);
});

test('_fitDateToRange', function () {
    dt._setMinMax('2000-05-05:2001-05-05');
    deepEqual(
        dt._fitDateToRange({ _year: 2000, _month: 10, _day: 10}),
        { _year: 2000, _month: 10, _day: 10});
    deepEqual(
        dt._fitDateToRange({ _year: 1999, _month: 10, _day: 10}),
        { _year: 2000, _month: 4, _day: 5});
});

test('_getNextMonth', function () {
    dt.setDate('2000-10-10');
    deepEqual(dt._getNextMonth(), { _year: 2000, _month: 10, _day: 10 });
    dt.setDate('2000-01-01');
    deepEqual(dt._getNextMonth(), { _year: 2000, _month: 1, _day: 1 });
    dt.setDate('2000-11-01');
    deepEqual(dt._getNextMonth(), { _year: 2000, _month: 11, _day: 1 });
    dt.setDate('2000-12-01');
    deepEqual(dt._getNextMonth(), { _year: 2001, _month: 0, _day: 1 });
});

test('_getFirstDayIndex', function () {
    /* Cal 2014-03
     *
     * Su Mo Tu We Th Fr Sa  
     *                    1  <- The "1" is in the 7th day
     *  2  3  4  5  6  7  8  
     *  9 10 11 12 13 14 15  
     * 16 17 18 19 20 21 22  
     * 23 24 25 26 27 28 29  
     * 30 31       
     */
    mkDatePicker({ startWeekDay: 0 /* sunday, like the cal above*/});
    strictEqual(dt._getFirstDayIndex(2014, 2 /* month - 1 */), 6);
    /* Cal 2014-03 (starting in monday)
     *
     * Mo Tu We Th Fr Sa Su  
     *                 1  2  <- Now "1" is the sixth day
     *  3  4  5  6  7  8  9  
     * 10 11 12 13 14 15 16  
     * 17 18 19 20 21 22 23  
     * 24 25 26 27 28 29 30  
     * 31                    
     */
    mkDatePicker({ startWeekDay: 1 /* monday */});
    strictEqual(dt._getFirstDayIndex(2014, 2), 5);
});

test('regression: _getFirstDayIndex of february 2015 should actually be sunday', function () {
    mkDatePicker({ startWeekDay: 0 });
    strictEqual(dt._getFirstDayIndex(2015, 1 /* month - 1 */), 0);
    mkDatePicker({ startWeekDay: 1 });
    strictEqual(dt._getFirstDayIndex(2015, 1), 6);
});

test('_getPrevMonth', function () {
    dt.setDate('2000-10-10');
    deepEqual(dt._getPrevMonth(), { _year: 2000, _month: 8, _day: 10 });
    dt.setDate('2000-01-01');
    deepEqual(dt._getPrevMonth(), { _year: 1999, _month: 11, _day: 1 });
});

test('no start limit date', function () {
    dt._setMinMax('EVER:2000-01-01');
    deepEqual(dt._min, {
        _year: -Number.MAX_VALUE,
        _month: 0,
        _day: 1
    });

    ok(dt._dateWithinRange({_year: -1000, _month: 1, _day: 1}));
    ok(dt._dateWithinRange({_year: 2000, _month: 0, _day: 1}));
    ok(!dt._dateWithinRange({_year: 2001, _month: 1, _day: 1}));
});
test('no end limit date', function () {
    dt._setMinMax('2000-01-01:EVER');
    deepEqual(dt._max, {
        _year: Number.MAX_VALUE,
        _month: 11,
        _day: 31
    });

    ok(!dt._dateWithinRange({_year: -1000, _month: 1, _day: 1}));
    ok(dt._dateWithinRange({_year: 2001, _month: 1, _day: 1}));
});

test('_get(Next|Prev)Month when hitting a limit', function () {
    dt._setMinMax('2000-05-05:2001-05-05');

    dt.setDate('2000-06-01');
    deepEqual(dt._getPrevMonth(), { _year: 2000, _month: 4, _day: 5 });
    dt.setDate('2001-04-09');
    deepEqual(dt._getNextMonth(), { _year: 2001, _month: 4, _day: 5 });

    dt.setDate('2000-05-01');
    deepEqual(dt._getPrevMonth(), null);

    dt.setDate('2001-05-06');
    deepEqual(dt._getNextMonth(), null);
});

test('validDayFn', function () {
    dt._options.validDayFn = sinon.stub().returns(false);
    dt.setDate('2000-01-01');
    dt.showMonth();

    var findEnabled = function (button) {
        return (/ink-calendar-off/.test(button.className));
    };
    var buttons = dt._monthContainer.getElementsByTagName('a');
    ok(InkArray.some(buttons, findEnabled),
        'No buttons are disabled');

    var spy = dt._options.validDayFn = sinon.spy(sinon.stub().returns(true));
    dt.showMonth();
    buttons = dt._monthContainer.getElementsByTagName('a');
    ok(!spy.notCalled);
    ok(!InkArray.some(buttons, findEnabled),
        'No buttons are disabled, I made all days valid with validDayFn');

    var lastCall = spy.getCall(30);
    ok(lastCall);
    ok(!spy.getCall(31));
    deepEqual(lastCall.args, [2000, 1, 31], 'called with last day of january');
    strictEqual(lastCall.thisValue, dt, 'called with this=datepicker');
});

test('nextValidDateFn', function () {
    dt.setDate('2000-01-01');
    var next = sinon.spy(sinon.stub().returns(new Date(2012, 1 - 1, 1)));
    var prev = sinon.spy(sinon.stub().returns(new Date(1990, 1 - 1, 1)));

    dt._options.nextValidDateFn = next;
    dt._options.prevValidDateFn = prev;

    var expectedNextValidDate = {_year: 2012, _month: 0, _day: 1};
    var expectedPrevValidDate = {_year: 1990, _month: 0, _day: 1};

    deepEqual(dt._getNextMonth(), expectedNextValidDate, 'next month is the result of nextValidDateFn');
    ok(next.calledOnce, 'cb called once');
    ok(next.calledWithExactly(2000, 1, 1), 'cb called with year, month, day');
    ok(next.lastCall.thisValue === dt, 'cb called with this=datepicker');
    deepEqual(dt._getPrevMonth(), expectedPrevValidDate, 'prev month is the result of prevValidDateFn');
    ok(prev.calledOnce, 'cb called once');
    ok(prev.calledWithExactly(2000, 1, 1), 'cb called with year, month, day');
    ok(prev.lastCall.thisValue === dt, 'cb called with this=datepicker');

    ok(true, '--- Checking if returning nulls as it should ---');
    next = sinon.stub().returns(null);
    prev = sinon.stub().returns(null);
    dt._options.nextValidDateFn = next;
    dt._options.prevValidDateFn = prev;

    deepEqual(dt._getNextMonth(expectedNextValidDate), null);
    deepEqual(dt._getPrevMonth(expectedPrevValidDate), null);

    ok(next.calledOnce);
    ok(prev.calledOnce);
});

test('getNextYear, getPrevYear', function () {
    dt.setDate('2000-05-05');
    deepEqual(dt._getNextYear(), {_year: 2001, _month: 4, _day: 5});
    deepEqual(dt._getPrevYear(), {_year: 1999, _month: 4, _day: 5});

    dt._setMinMax('1999-10-10:2001-01-01');
    deepEqual(dt._getNextYear(), {_year: 2001, _month: 0, _day: 1});
    deepEqual(dt._getPrevYear(), {_year: 1999, _month: 9, _day: 10});

    dt.setDate('2001-01-01');
    deepEqual(dt._getNextYear(), null);

    dt.setDate('1999-10-10');
    deepEqual(dt._getPrevYear(), null);
});

test('getCurrentDecade', function () {
    dt.setDate('2000-05-05');
    deepEqual(dt._getCurrentDecade(), 2000);
    dt.setDate('2010-01-01');
    deepEqual(dt._getCurrentDecade(), 2010);
    dt.setDate('2005-01-01');
    deepEqual(dt._getCurrentDecade(), 2000);
    dt.setDate('2019-01-01');
    deepEqual(dt._getCurrentDecade(), 2010);
});

test('getNextDecade, getPrevDecade', function () {
    dt._getCurrentDecade = sinon.spy(dt._getCurrentDecade);
    dt.setDate('2001-05-05');
    deepEqual(dt._getNextDecade(), 2010);
    deepEqual(dt._getPrevDecade(), 1990);
    ok(dt._getCurrentDecade.calledTwice);

    dt._setMinMax('2000-05-01:2020-05-05');
    deepEqual(dt._getPrevDecade(), null);
    dt.setDate('2020-01-01');
    deepEqual(dt._getNextDecade(), null);
});

test('getNextDecade, getPrevDecade and an uneven date-range', function () {
    dt._setMinMax('1939-07-15:1951-02-10');
    dt.setDate('1940-06-01');
    equal(dt._getPrevDecade(), 1930);
    equal(dt._getNextDecade(), 1950);
    dt._setMinMax('1940-01-01:1945-02-10');
    equal(dt._getPrevDecade(), null);
    equal(dt._getNextDecade(), null);
});

test('dateCmp', function () {
    var y2k = { _year: 2000, _month: 0, _day: 1};
    var y2kandaday = { _year: 2000, _month: 0, _day: 2};
    deepEqual(dt._dateCmp(y2k, y2k), 0);
    deepEqual(dt._dateCmp(y2k, y2kandaday), -1);
    deepEqual(dt._dateCmp(y2kandaday, y2k), 1);
});

test('dateCmpUntil', function () {
    var y2k = { _year: 2000, _month: 0, _day: 1};
    var y2kandaday = { _year: 2000, _month: 0, _day: 2 };
    var y2kandamonth = { _year: 2000, _month: 2, _day: 3 };
    deepEqual(dt._dateCmpUntil(y2k, y2kandaday, '_month'), 0, 'too shallow');
    deepEqual(dt._dateCmpUntil(y2k, y2kandaday, '_year'), 0, 'too shallow');
    deepEqual(dt._dateCmpUntil(y2k, y2kandaday, '_day'), -1, 'deep enough, we see a difference');
    deepEqual(dt._dateCmpUntil(y2k, y2kandamonth, '_year'), 0);
    deepEqual(dt._dateCmpUntil(y2k, y2kandamonth, '_month'), -1);
    deepEqual(dt._dateCmpUntil(y2kandamonth, y2k, '_month'), 1);
});

test('daysInMonth', function () {
    equal(dt._daysInMonth(2000, 1), 31);
    equal(dt._daysInMonth(2000, 2), 29);
    equal(dt._daysInMonth(2001, 2), 28);
});

test('updateDate', function () {
    dt._element.value = '11/11/2012';
    dt._updateDate();
    equal(dt._year, 2012);
    equal(dt._month, 10);
    equal(dt._day, 11);
});

test('set', function () {
    // Because it had a bug
    var dt = Ink.Util.Date_1.set('Y-m-d', '2012-10-10');
    equal(dt.getFullYear(), 2012);
    equal(dt.getMonth(), 9);
    equal(dt.getDate(), 10);
});

test('show', function () {
    equal(Css.getStyle(dt._containerObject, 'display'), 'none');
    dt.show();
    notEqual(Css.getStyle(dt._containerObject, 'display'), 'none');
});



test('createSelectOptions fills in the required fields', function () {
    dt.destroy();

    var yearField = InkElement.create('select');
    var monthField = InkElement.create('select');
    var dayField = InkElement.create('select')

    mkDatePicker({ createSelectOptions: true, displayInSelect: true, dayField: dayField, monthField: monthField, yearField: yearField });

    monthField.value = 1;
    InkEvent.fire(monthField, 'change')
    equal(monthField.value, "1", 'sanity check -- must be january');
    deepEqual(
        InkArray.map(dayField.children, function (opt) { return +opt.value }),
        InkArray.range(1, 32));
    deepEqual(
        InkArray.map(monthField.children, function (opt) { return +opt.value }),
        InkArray.range(1, 13));
    deepEqual(
        InkArray.map(yearField.children, function (opt) { return +opt.value }),
        InkArray.range(1900, 2000));
})

test('createSelectOptions -- changes what fields are visible when another is selected', function () {
    dt.destroy();

    var yearField = InkElement.create('select');
    var monthField = InkElement.create('select');
    var dayField = InkElement.create('select')

    mkDatePicker({ createSelectOptions: true, displayInSelect: true, dayField: dayField, monthField: monthField, yearField: yearField });
    monthField.value = 2;
    InkEvent.fire(monthField, 'change')

    deepEqual(
        InkArray.map(dayField.children, function (opt) { return +opt.value }),
        InkArray.range(1, 28 + 1));
    
    yearField.value = 1996;  // leap year
    InkEvent.fire(yearField, 'change')
    deepEqual(
        InkArray.map(dayField.children, function (opt) { return +opt.value }),
        InkArray.range(1, 29 + 1));
})



test('destroy', function () {
    ok(testWrapper.children.length > 1 || testWrapper.firstChild !== dtElm, 'sanity check. if this fails, review the test because you\'ve changed the DOM structure of this component');
    dt.destroy();
    equal(testWrapper.children.length, 1, 'destroyed remaining instances');
    strictEqual(testWrapper.firstChild, dtElm, 'the only element there is our original input');
});

test('regression: months have correct amount of days', function () {
    mkDatePicker({
        startDate: '2014-02-01' });
    dt.show();
    equal(Ink.ss('.ink-calendar-month [data-cal-day]', testWrapper).length, 28);

    mkDatePicker({
        startDate: '2014-01-01' });
    dt.show();
    equal(Ink.ss('.ink-calendar-month [data-cal-day]', testWrapper).length, 31);
});

test('regression: days start in the correct week day by filling with an appropriate amount of "empties"', function () {
    /* March 2014  start=Su     March 2014  start=Mo
     * Su Mo Tu We Th Fr Sa     Mo Tu We Th Fr Sa Su
     *                    1                     1  2
     *  2  3  4  5  6  7  8      3  4  5  6  7  8  9
     *  9 10 11 12 13 14 15     10 11 12 13 14 15 16
     * 16 17 18 19 20 21 22     17 18 19 20 21 22 23
     * 23 24 25 26 27 28 29     24 25 26 27 28 29 30
     * 30 31                    31                  
     *
     * 6 empties before "1"     5 empties before "1"
     */

    dt.destroy();

    equal(Ink.ss('.ink-calendar-empty', getFirstLine(0)).length, 6);
    dt.destroy();

    equal(Ink.ss('.ink-calendar-empty', getFirstLine(1)).length, 5);
    dt.destroy();

    function getFirstLine(startWeekDay) {
        mkDatePicker({
            startDate: '2014-03-01',
            startWeekDay: startWeekDay });
        dt.show();
        var firstLine = Ink.s('.ink-calendar-month .ink-calendar-header + ul', testWrapper);
        ok(firstLine, 'sanity check');
        return firstLine;
    }
});

test('(regression): Changing march to february when cursor is in the 30th day', function () {
    dt.setDate('2014-03-30');
    deepEqual(dt._getPrevMonth(), { _year: 2014, _month: 1, _day: 28 });

    dt.setDate('2014-05-31');
    deepEqual(dt._getPrevMonth(), { _year: 2014, _month: 3, _day: 30 });

    dt.setDate('2014-01-30');
    deepEqual(dt._getNextMonth(), { _year: 2014, _month: 1, _day: 28 });

    dt.setDate('2014-03-31');
    deepEqual(dt._getNextMonth(), { _year: 2014, _month: 3, _day: 30 });
})

});
