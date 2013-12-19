modul('yahoo.search');

test('length', function() {
    var arr = [1,2,3];
    ok(arr.length == 3);
});

test('indexOf()', function() {
    var arr = [1, 2, 3];
    ok(arr.indexOf(1) === 0);
    ok(arr.indexOf(2) === 1);
    equal(arr.indexOf(3), 2, 'oops');
});

test('length', function() {
    ok(brName === 'ch', 'foo lengthhhh');
});



test('asd', function() {
    br.url('http://search.yahoo.com');
    br.setValue('#yschsp', 'JavaScript');
    //br.showTest(true, 'received', 'expected', 'test message goes here');
    br.submitForm('#sf');
    //br.tests.visible('#resultCount', true, brName + ' got result count?');
        //.saveScreenshot('shots/results_' + brName + '.png')
        //
    notEqual(2, 3, 'not equal');
    deepEqual({a:'b', c:3, e:true, f:['a', 'b']}, {a:'b', c:3, e:true, f:['a', 'b']}, 'deep eql');
    br.end();
});
