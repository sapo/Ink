Ink.requireModules(['Ink.UI.DatePicker_1'], function (DatePicker) {

test('_dateCmp', function () {
    var dt = DatePicker.prototype;

    equal(dt._dateCmp({_year: 2012}, {_year: 2012}), 0);
    equal(dt._dateCmp({_year: 2012, _month: 10}, {_year: 2012}), 0);
    equal(dt._dateCmp({_year: 2012, _month: 10}, {_year: 2012, _month: 11}), -1);
    equal(dt._dateCmp({_year: 2012, _month: 10}, {_year: 2012, _month: 9}), 1);
    equal(dt._dateCmp({_year: 2012, _month: 10, _day: 10}, {_year: 2012, _month: 10}), 0);
    equal(dt._dateCmp({_year: 2012, _month: 10, _day: 10}, {_year: 2012, _month: 10, _day: 11}), -1);
    equal(dt._dateCmp({_year: 2012, _month: 10, _day: 10}, {_year: 2012, _month: 10, _day: 9}), 1);
    equal(dt._dateCmp({_year: 2012, _month: 10, _day: 10}, {_year: 2012, _month: 10, _day: 10}), 0);
});

});
