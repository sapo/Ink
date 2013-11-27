Ink.requireModules(['Ink.UI.DatePicker_1', 'Ink.Dom.Event_1', 'Ink.Dom.Element_1'], function (DatePicker, InkEvent, InkElement) {

var body = document.body;
var dtElm;
var dt;

module('main', {
    setup: function () {
        dtElm = InkElement.create('input', { type: 'text', insertBottom: body })
        dt = new DatePicker(dtElm, {
            startDate: '2000-10-10'
        });
    },
    teardown: function () {
        InkElement.remove(dtElm);
    }
})

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

test('_getNextMonth', function () {
    
});

});
