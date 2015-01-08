Ink.requireModules(['Ink.UI.Common_1', 'Ink.UI.DatePicker_1', 'Ink.Dom.Css_1', 'Ink.Dom.Event_1', 'Ink.Dom.Element_1', 'Ink.Util.Array_1', 'Ink.UI.Calendar_1'], function (Common, DatePicker, Css, InkEvent, InkElement, InkArray, Calendar) {

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
}

module('main', {
    setup: function () {
        mkDatePicker({});
    },
    teardown: function () {
        InkElement.remove(testWrapper);
    }
});

test('has a calendar', function () {
    ok(dt._calendar instanceof Calendar);
    strictEqual(dt._calendar, dt.getCalendar());
});

test('gives it a table.ink-calendar', sinon.test(function () {
    this.spy(Common, 'BaseUIComponent')
    mkDatePicker({})
    var elm = dt._calendar._element
    equal(elm.tagName.toLowerCase(), 'table', 'element is a table')
    ok(Css.hasClassName(elm, 'ink-calendar'), 'table has class name ink-calendar')
}));

test('Passes Calendar-relevant options to Calendar', sinon.test(function () {
    this.spy(Common, 'BaseUIComponent');

    var optsToPass = {
        dateRange:         'EVER:NOW',
        nextLinkText:      'nextLinkText',
        prevLinkText:      'prevLinkText',
        ofText:            ' ofText ',
        onSetDate:         sinon.spy(),
        startDate:         '2015-04-05', // format yyyy-mm-dd,
        startWeekDay:      3,

        // Validation
        validDayFn:        sinon.spy(),
        validMonthFn:      sinon.spy(),
        validYearFn:       sinon.spy(),
        nextValidDateFn:   sinon.spy(),
        prevValidDateFn:   sinon.spy(),
        yearRange:         '2010-2020',  /* [3.1.0] deprecate this */

        // Text
        month: {
             1:'1 is the month of January',
             2:'2 is the month of February',
             3:'3 is the month of March',
             4:'4 is the month of April',
             5:'5 is the month of May',
             6:'6 is the month of June',
             7:'7 is the month of July',
             8:'8 is the month of August',
             9:'9 is the month of September',
            10:'10 is the month of October',
            11:'11 is the month of November',
            12:'12 is the month of December'
        },
        wDay: {
            0:'0 is the day of Sunday',
            1:'1 is the day of Monday',
            2:'2 is the day of Tuesday',
            3:'3 is the day of Wednesday',
            4:'4 is the day of Thursday',
            5:'5 is the day of Friday',
            6:'6 is the day of Saturday'
        }
    };

    var dt = new DatePicker('LOOOL', optsToPass);

    ok(Common.BaseUIComponent.calledTwice)
    ok(Common.BaseUIComponent.calledWithNew())

    var opts = Common.BaseUIComponent.lastCall.args[1];

    for (var key in optsToPass) {
        if (optsToPass.hasOwnProperty(key)) {
            strictEqual(
                optsToPass[key],
                opts[key],
                'option ' + key + ' passed to Calendar')
        }
    }
}));

test('puts the calendar next to the element', function () {
    strictEqual(dt.getElement().nextSibling, dt.getCalendar().getElement());
});

test('alias: setDate', function () {
    var spy = sinon.stub(dt._calendar, 'setDate');
    dt.setDate('2000-10-12');
    ok(spy.calledOnce);
    ok(spy.calledWith('2000-10-12'));
});

test('set', function () {
    // Because it had a bug
    var dt = Ink.Util.Date_1.set('Y-m-d', '2012-10-10');
    equal(dt.getFullYear(), 2012);
    equal(dt.getMonth(), 9);
    equal(dt.getDate(), 10);
});

test('show, hide', function () {
    equal(Css.getStyle(dt._calendarEl, 'display'), 'none');
    dt.show();
    notEqual(Css.getStyle(dt._calendarEl, 'display'), 'none');
    dt.hide();
    equal(Css.getStyle(dt._calendarEl, 'display'), 'none');
    dt.hide();
});

test('shows the datepicker when the input is focused', function () {
    stop()
    sinon.spy(dt, 'show')
    Syn.click(dt._element, function () {
        ok(dt.show.calledOnce, 'element was shown!')
        start()
    });
});

test('Changes the datepicker\'s date when the input is changed', function () {
    stop()
    var setDate = sinon.spy(dt._calendar, 'setDate')
    Syn.type(dt._element, '12/12/2012[enter]', function () {
        ok(setDate.calledOnce, 'setDate was called when the user typed a date')
        start()
    });
});

test('destroy', function () {
    ok(testWrapper.children.length > 1 || testWrapper.firstChild !== dtElm, 'sanity check. if this fails, review the test because you\'ve changed the DOM structure of this component');
    dt.destroy();
    equal(testWrapper.children.length, 1, 'destroyed remaining instances');
    strictEqual(testWrapper.firstChild, dtElm, 'the only element there is our original input');
});

test('setDate', function () {
    dt.setDate('2000-10-12');
    equal(dt._calendar._year, 2000);
    equal(dt._calendar._month + 1, 10);
    equal(dt._calendar._day, 12);

    dt.setDate('2000-01-01');
    equal(dt._calendar._year, 2000);
    equal(dt._calendar._month + 1, 1);
    equal(dt._calendar._day, 1);
});

test('setDate with Date objects', function () {
    dt.setDate(new Date(2010, 11, 12));
    equal(dt._calendar._year, 2010);
    equal(dt._calendar._month + 1, 12);
    equal(dt._calendar._day, 12);
});

test('updateDate', function () {
    dt._element.value = '11/11/2012';
    dt._updateDate();
    equal(dt._calendar._year, 2012);
    equal(dt._calendar._month, 10);
    equal(dt._calendar._day, 11);

    dt._element.value = '31/12/2032';
    dt._updateDate();
    equal(dt._calendar._year, 2032);
    equal(dt._calendar._month, 11);
    equal(dt._calendar._day, 31);
});

});
