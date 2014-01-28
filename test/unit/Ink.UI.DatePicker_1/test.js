Ink.requireModules(['Ink.UI.DatePicker_1', 'Ink.Dom.Css_1', 'Ink.Dom.Event_1', 'Ink.Dom.Element_1', 'Ink.Util.Array_1'], function (DatePicker, Css, InkEvent, InkElement, InkArray) {

var body = document.body;
var dtElm;
var dt;

module('main', {
    setup: function () {
        testWrapper = InkElement.create('div', { insertBottom: body })
        dtElm = InkElement.create('input', { type: 'text', insertBottom: testWrapper });
        dt = new DatePicker(dtElm, {
            startDate: '2000-10-10',
            format: 'dd/mm/yyyy'
        });
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
    ok(lastCall.thisValue, dt, 'called with this=datepicker');
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
    equal(Css.getStyle(dt._containerObject, 'display'), 'block');
});

test('destroy', function () {
    ok(testWrapper.children.length > 1 || testWrapper.firstChild !== dtElm, 'sanity check. if this fails, review the test because you\'ve changed the DOM structure of this component');
    dt.destroy();
    equal(testWrapper.children.length, 1, 'destroyed remaining instances');
    strictEqual(testWrapper.firstChild, dtElm, 'the only element there is our original input');
});

});
