QUnit.config.testTimeout = 4000;

Ink.requireModules(['Ink.Dom.Event_1', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1', 'Ink.Dom.Browser_1'], function (InkEvent, InkElement, Selector, Browser) {
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
        };

        var startTime = +new Date();
        var throttled = InkEvent.throttle(function () {
            var theTime = new Date() - startTime;
            nearEqual(timing[++c], theTime);
            start();
        }, 500);

        for (var i = 0, len = cTming.length; i < len; i++) {
            setTimeout(throttled, cTming[i]);
        }

        /* stop [timing] times */
        stop(timing.length - 1);
        /* expect [timing] assertions */
        expect(timing.length);
    });

    asyncTest('observeDelegated', function () {
        var elem = InkElement.create('ul');
        var child = InkElement.create('li');
        var grandChild = InkElement.create('span');

        elem.appendChild(child);
        child.appendChild(grandChild);

        expect(1);
        InkEvent.observeDelegated(elem, 'click', 'li', function (event) {
            ok(this === child, '<this> is the selected tag');
            start();
        });

        InkEvent.fire(child, 'click');
    });

    asyncTest('observeDelegated', function () {
        var elem = InkElement.create('ul');
        var child = InkElement.create('li');

        elem.appendChild(child);

        expect(0);
        InkEvent.observeDelegated(elem, 'click', 'ul', function (event) {
            ok(false, 'should not fire event on delegation parent');
        });

        InkEvent.fire(child, 'click');
        setTimeout(start, 100);
    });

    asyncTest('observeDelegated + some selectors', function () {
        var elem = InkElement.create('ul');
        var child = InkElement.create('li');
        var grandChild = InkElement.create('span');

        elem.appendChild(child);
        child.appendChild(grandChild);

        grandChild.className = 'class-i-have';
        
        expect(1);
        InkEvent.observeDelegated(elem, 'click', 'li > span.classIDontHave', function () {
            ok(false, 'should not find this element');
        });

        InkEvent.observeDelegated(child, 'click', 'ul > li > span', function (event) {
            ok(false, 'should not be able to select through parents');
        });

        InkEvent.observeDelegated(elem, 'click', 'li > span', function () {
            ok(true, 'selected by class, correctly');
        });

        InkEvent.fire(grandChild, 'click');

        setTimeout(start, 100);
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

