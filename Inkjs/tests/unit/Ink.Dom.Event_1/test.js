/*globals equal,test,asyncTest,stop,start,ok,expect*/
Ink.requireModules(['Ink.Dom.Event_1'], function (InkEvent) {
    var throttle = Ink.bind(InkEvent.throttle, InkEvent);
    var throttledFunc = throttle(function () {
        ok(true, 'called');
    }, 100);
    asyncTest('throttle (1)', function () {
        expect(2);
        throttledFunc();
        throttledFunc();
        throttledFunc();
        throttledFunc();
        throttledFunc(); // Call this a couple of times, assert called twice.
        setTimeout(start, 300);
    });
    asyncTest('throttle (2)', function () {
        expect(1);
        throttledFunc(); // Call this once, assert called once.
        setTimeout(start, 200);
    });
    asyncTest('throttle (context and arguments)', function () {
        expect(2);
        var withArgs = throttle(function (arg) {
            equal(arg, 'arg');
            equal(this, 'this');
        }, 0);
        withArgs.call('this', 'arg');
        setTimeout(start, 50);
    });
    asyncTest('throttle (called few times)', function () {
        expect(3);
        var fewTimes = throttle(function () { ok(true) }, 20);
        
        setTimeout(fewTimes, 1);
        setTimeout(fewTimes, 100);
        setTimeout(fewTimes, 200);

        setTimeout(start, 300);
    });
});
