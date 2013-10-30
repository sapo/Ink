/*globals equal,test,asyncTest,stop,start,ok,expect*/
Ink.requireModules(['Ink.Dom.Event_1', 'Ink.Dom.Browser_1'], function (InkEvent, Browser) {
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
        var fewTimes = throttle(function () { ok(true); }, 20);
        
        setTimeout(fewTimes, 1);
        setTimeout(fewTimes, 100);
        setTimeout(fewTimes, 200);

        setTimeout(start, 300);
    });
    asyncTest('throttle called with the correct timing between calls', function () {
        // Timing of the calls we will barrage throttled() with
        var cTming = [
            0,0,0,0,0,100,
            1000];

        // The times at which throttled() should be called
        var timing = [0, 500, 1000];
        var c = -1;

        var nearEqual = function (a, b, threshold, msg) {
            threshold = threshold || 250;
            msg = msg || '';
            ok( a - threshold < b && a + threshold > b, [msg, ':', a, '~=', b].join(' ') );
            start();
        };

        var startTime = +new Date();
        var throttled = InkEvent.throttle(function () {
            var theTime = new Date() - startTime;
            nearEqual(timing[++c], theTime);
        }, 500);

        for (var i = 0, len = cTming.length; i < len; i++) {
            setTimeout(throttled, cTming[i]);
        }

        /* stop [timing] times */
        stop(timing.length - 1);
        /* expect [timing] assertions */
        expect(timing.length);
    });

    asyncTest('test hashchange', function ( ) {
        if (Browser.IE && parseFloat(Browser.version) < 8) {
            ok(true, 'skipped');
            start();
            return;
        }
        
        location.hash = '';

        var cb = InkEvent.observe( window , 'hashchange' , function( e ) {
            ok(true, 'callback to onhashchange called');

            /* cleanup */
            InkEvent.stopObserving(window, 'hashchange', cb);
            location.hash = '';

            start();
        });

        location.hash = 'changed';
    });

    asyncTest('test pushState', function ( ) {
        if (Browser.IE && parseFloat(Browser.version) < 10) {
            ok(true, 'skipped');
            start();
            return;
        }
        history.pushState( '' , '' , location.pathname.replace( /_\d\/$/ , '_2/' ) );

        var cb = InkEvent.observe( window , 'popstate' , function( e ) {
            ok(true);
            InkEvent.stopObserving(window, 'popstate', cb);
            start();
        });

        expect(1);
        history.back( );
    });
});
