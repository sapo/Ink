Ink.requireModules(['Ink.UI.Calendar_1', 'Ink.Dom.Css_1', 'Ink.Dom.Event_1', 'Ink.Dom.Element_1', 'Ink.Util.Array_1'], function (Calendar, Css, InkEvent, InkElement, InkArray) {
var body = document.body;
var dtElm;
var dt;

function mkCalendar(options) {
    var testWrapper = InkElement.create('div', { insertBottom: body });
    var dtElm = InkElement.create('table', { className: 'ink-calendar', insertBottom: testWrapper });
    var dt = new Calendar(dtElm, Ink.extendObj({
        startDate: '2000-10-10'
    }, options));
    return {
        testWrapper: testWrapper,
        dtElm: dtElm,
        dt: dt
    }
}

module('main', {
    setup: function () {
        var stuff = mkCalendar({});
        testWrapper = stuff.testWrapper;
        dtElm = stuff.dtElm;
        dt = stuff.dt;
    },
    teardown: function () {
        InkElement.remove(testWrapper);
    }
});

test('_dateCmp', function () {
    var dt = mkCalendar({}).dt;
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
    var dt = mkCalendar({}).dt;
    debugger
    dt.setDate('2000-10-12');
    equal(dt._year, 2000);
    equal(dt._month + 1, 10);
    equal(dt._day, 12);

    dt.setDate('2000-01-01');
    equal(dt._year, 2000);
    equal(dt._month + 1, 1);
    equal(dt._day, 1);
});

test('setDate affects the DOM by selecting a date element', function () {
    var stuff = mkCalendar({});
    var dt = stuff.dt;
    var dtElm = stuff.dtElm
    dt.setDate('2010-10-12');
    ok(Ink.s('td.active [data-cal-day="12"]', dtElm));
    equal(
        InkElement.textContent(Ink.s('.ink-calendar-link-month', dtElm)),
        'October',
        'Month text became "October"')
    equal(
        InkElement.textContent(Ink.s('.ink-calendar-link-year', dtElm)),
        '2010',
        'Year text became "2010"')
});


test('click to change days', function () {
    var stuff = mkCalendar({});
    sinon.spy(stuff.dt, '_setDate')

    stop();
    Syn.click(Ink.s('[data-cal-day="11"]', stuff.dtElm), function () {
        start();
        ok(stuff.dt._setDate.calledWith({ _year: 2000, _month: 9, _day: 11 }));
    });
});

test('click month to change months', function () {
    var stuff = mkCalendar({});
    stuff.dt.yearView();
    sinon.spy(stuff.dt, '_setDate')

    stop();
    Syn.click(Ink.s('[data-cal-month="11"]', stuff.dtElm), function () {
        start();
        ok(stuff.dt._setDate.calledOnce);
        ok(stuff.dt._setDate.calledWith({ _year: 2000, _month: 11, _day: 10 }));
    });
});

test('click year to change years', function () {
    var stuff = mkCalendar({});
    stuff.dt.decadeView();
    sinon.spy(stuff.dt, '_setDate');

    stop();
    Syn.click(Ink.s('[data-cal-year="2001"]', stuff.dtElm), function () {
        start();
        ok(stuff.dt._setDate.calledOnce);
        ok(stuff.dt._setDate.calledWith({ _year: 2001, _month: 9, _day: 10 }));
    });
});

test('next and prev buttons call _onNextPrevClicked', function () {
    var stuff = mkCalendar({});
    sinon.spy(stuff.dt, '_onNextPrevClicked')

    stop();
    Syn.click(Ink.s('[href$="next"]', stuff.dtElm), function () {
        ok(stuff.dt._onNextPrevClicked.calledWith('_month', 1))

        Syn.click(Ink.s('[href$="prev"]', stuff.dtElm), function () {
            ok(stuff.dt._onNextPrevClicked.calledWith('_month', -1))
            start();
        })
    });
});

test('_onNextPrevClicked calls tryLeap, then _setDate with the resulting date, then {fragment}view() to render the new view', function () {
    var nextMonth = { _year: 2100, _month: 1, _day: 1 }
    var prevYear = { _year: 100, _month: 10, _day: 30 }
    var nextDecade = { _year: 2010, _month: 1, _day: 1 }

    sinon.stub(dt, '_tryLeap').returns(nextMonth)

    sinon.stub(dt, '_setDate')

    sinon.stub(dt, 'monthView')
    sinon.stub(dt, 'yearView')
    sinon.stub(dt, 'decadeView')

    dt._onNextPrevClicked('_month', 1);
    ok(dt._setDate.calledWith(nextMonth), '_setDate called with the next month as given by _getNextMonth');
    ok(dt.monthView.calledOnce, 'monthView called');
    ok(dt._tryLeap.calledWith('_month', 1));

    dt._tryLeap.returns(prevYear);
    dt._onNextPrevClicked('_year', -1);
    ok(dt._setDate.calledWith(prevYear), '_setDate called with given year');
    ok(dt.yearView.calledOnce, 'yearView called');
    ok(dt._tryLeap.calledWith('_year', -1));

    dt._tryLeap.returns(nextDecade);
    dt._onNextPrevClicked('_decade', 1);
    ok(dt._setDate.calledWith(nextDecade), '_setDate called with given decade');
    ok(dt.decadeView.calledOnce, 'decadeView called');
    ok(dt._tryLeap.calledWith('_decade', 1));

    equal(dt._setDate.callCount, 3);
})

test('setDate with Date objects', function () {
    var stuff = mkCalendar({});
    stuff.dt.setDate(new Date(2010, 11, 12));
    equal(stuff.dt._year, 2010);
    equal(stuff.dt._month + 1, 12);
    equal(stuff.dt._day, 12);
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

test('_tryLeap', function () {
    var dt = mkCalendar({}).dt;
    dt.setDate('2000-10-10');
    deepEqual(dt._tryLeap('_month', 1), { _year: 2000, _month: 10, _day: 10 });
    dt.setDate('2000-01-01');
    deepEqual(dt._tryLeap('_month', 1), { _year: 2000, _month: 1, _day: 1 });
    dt.setDate('2000-11-01');
    deepEqual(dt._tryLeap('_month', 1), { _year: 2000, _month: 11, _day: 1 });
    dt.setDate('2000-12-01');
    deepEqual(dt._tryLeap('_month', 1), { _year: 2001, _month: 0, _day: 1 });

    dt.setDate('2000-10-10');
    deepEqual(dt._tryLeap('_month', -1), { _year: 2000, _month: 8, _day: 10 });
    dt.setDate('2000-01-01');
    deepEqual(dt._tryLeap('_month', -1), { _year: 1999, _month: 11, _day: 1 });

    dt.setDate('2000-05-05');
    deepEqual(dt._tryLeap('_year', 1), {_year: 2001, _month: 4, _day: 5});
    deepEqual(dt._tryLeap('_year', -1), {_year: 1999, _month: 4, _day: 5});

    dt._setMinMax('1999-10-10:2001-01-02');
    deepEqual(dt._tryLeap('_year', 1), {_year: 2001, _month: 0, _day: 2});
    deepEqual(dt._tryLeap('_year', -1), {_year: 1999, _month: 9, _day: 10});

    dt.setDate('2001-01-01');
    deepEqual(dt._tryLeap('_year', 1), null);

    dt.setDate('1999-11-11');
    deepEqual(dt._tryLeap('_year', -1), null);

    dt._setMinMax('EVER:EVER');
    dt.setDate('2001-05-05');
    equal(dt._tryLeap('_decade', 1)._year, 2010);
    equal(dt._tryLeap('_decade', -1)._year, 1990);

    dt._setMinMax('2000-05-01:2020-05-05');
    dt.setDate('2001-05-05');
    deepEqual(dt._tryLeap('_decade', -1), null);
    dt.setDate('2020-01-01');
    deepEqual(dt._tryLeap('_decade', 1), null);
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
    var dt = mkCalendar({ startWeekDay: 0 /* sunday, like the cal above*/}).dt;
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
    var dt = mkCalendar({ startWeekDay: 1 /* monday */}).dt;
    strictEqual(dt._getFirstDayIndex(2014, 2), 5);
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
    var stuff = mkCalendar({})
    stuff.dt._setMinMax('2000-05-05:2001-05-05');

    stuff.dt.setDate('2000-06-01');
    deepEqual(stuff.dt._tryLeap('_month', -1), { _year: 2000, _month: 4, _day: 5 });
    stuff.dt.setDate('2001-04-09');
    deepEqual(stuff.dt._tryLeap('_month', 1), { _year: 2001, _month: 4, _day: 5 });

    stuff.dt.setDate('2000-06-03');
    deepEqual(stuff.dt._tryLeap('_month', -1), { _year: 2000, _month: 4, _day: 5 });

    stuff.dt.setDate('2000-06-06');
    deepEqual(stuff.dt._tryLeap('_month', -1), { _year: 2000, _month: 4, _day: 6 });

    stuff.dt.setDate('2000-05-06');
    deepEqual(stuff.dt._tryLeap('_month', -1), null);

    stuff.dt.setDate('2001-05-04');
    deepEqual(stuff.dt._tryLeap('_month', 1), null);
});

test('validDayFn', function () {
    dt._options.validDayFn = sinon.stub().returns(false);
    dt.setDate('2000-01-01');
    dt.monthView();

    var findEnabled = function (button) {
        return !(/disabled/.test(button.className));
    };
    var buttons = Ink.ss('.month tr:not(.header) td', dt.getElement());
    ok(InkArray.some(buttons, findEnabled),
        'All buttons are disabled');

    var spy = dt._options.validDayFn = sinon.stub().returns(true);
    dt.monthView();
    ok(InkArray.some(buttons, findEnabled),
        'No buttons are disabled, I made all days valid with validDayFn');

    var lastCall = spy.getCall(30);
    ok(lastCall, 'There\'s a 30th call');
    ok(!spy.getCall(31), 'but not a 31st call');
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

    deepEqual(dt._tryLeap('_month', 1), expectedNextValidDate, 'next month is the result of nextValidDateFn');
    ok(next.calledOnce, 'cb called once');
    ok(next.calledWithExactly(2000, 1, 1), 'cb called with year, month, day');
    ok(next.lastCall.thisValue === dt, 'cb called with this=datepicker');

    deepEqual(dt._tryLeap('_month', -1), expectedPrevValidDate, 'prev month is the result of prevValidDateFn');
    ok(prev.calledOnce, 'cb called once');
    ok(prev.calledWithExactly(2000, 1, 1), 'cb called with year, month, day');
    ok(prev.lastCall.thisValue === dt, 'cb called with this=datepicker');

    ok(true, '--- Checking if returning nulls as it should ---');
    next = sinon.stub().returns(null);
    prev = sinon.stub().returns(null);
    dt._options.nextValidDateFn = next;
    dt._options.prevValidDateFn = prev;

    strictEqual(dt._tryLeap('_month', 1), null);
    strictEqual(dt._tryLeap('_month', -1), null);

    ok(next.calledOnce);
    ok(prev.calledOnce);
});

test('_tryLeap and next/prevValidDateFn', function () {
    dt.setDate('2000-01-01');
    var next = sinon.spy(sinon.stub().returns(new Date(2012, 1 - 1, 1)));
    var prev = sinon.spy(sinon.stub().returns(new Date(1990, 1 - 1, 1)));

    dt._options.nextValidDateFn = next;
    dt._options.prevValidDateFn = prev;

    var expectedNextValidDate = {_year: 2012, _month: 0, _day: 1};
    var expectedPrevValidDate = {_year: 1990, _month: 0, _day: 1};

    deepEqual(dt._tryLeap('_decade', 1), expectedNextValidDate);
    deepEqual(dt._tryLeap('_decade', -1), expectedPrevValidDate);
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
    equal(dt._daysInMonth(2000, 0), 31);
    equal(dt._daysInMonth(2000, 1), 29);
    equal(dt._daysInMonth(2001, 1), 28);
});

test('destroy', function () {
    ok(testWrapper.children.length === 1, 'sanity check. if this fails, review the test because you\'ve changed the DOM structure of this component');
    dt.destroy();
    equal(testWrapper.children.length, 0, 'removed from the DOM');
});

test('regression: months have correct amount of days', function () {
    var stuff = mkCalendar({
        startDate: '2014-02-01' });
    stuff.dt.monthView();
    equal(Ink.ss('[data-cal-day]', stuff.testWrapper).length, 28);

    var stuff = mkCalendar({
        startDate: '2014-01-01' });
    stuff.dt.monthView();
    equal(Ink.ss('[data-cal-day]', stuff.testWrapper).length, 31);
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
     * 1 day in the first line  2 days in the first line
     */
    var stuff;

    equal(Ink.ss('[data-cal-day]', getFirstLine(0)).length, 1);

    equal(Ink.ss('[data-cal-day]', getFirstLine(1)).length, 2);

    function getFirstLine(startWeekDay) {
        stuff = mkCalendar({
            startDate: '2014-03-01',
            startWeekDay: startWeekDay });
        stuff.dt.monthView();
        var firstLine = Ink.s('.header + tr', stuff.testWrapper);
        //ok(firstLine, 'sanity check');
        return firstLine;
    }
});

test('(regression): Changing march to february when cursor is in the 30th day', function () {
    dt.setDate('2014-03-30');
    deepEqual(dt._tryLeap('_month', -1), { _year: 2014, _month: 1, _day: 28 });

    dt.setDate('2014-05-31');
    deepEqual(dt._tryLeap('_month', -1), { _year: 2014, _month: 3, _day: 30 });

    dt.setDate('2014-01-30');
    deepEqual(dt._tryLeap('_month', 1), { _year: 2014, _month: 1, _day: 28 });

    dt.setDate('2014-03-31');
    deepEqual(dt._tryLeap('_month', 1), { _year: 2014, _month: 3, _day: 30 });
});

});
