QUnit.config.testTimeout = 4000;

Ink.requireModules(['Ink.Dom.Event_1', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1', 'Ink.Dom.Browser_1'], function (InkEvent, InkElement, Selector, Browser) {

    var nearEqual = function (a, b, threshold, msg) {
        threshold = threshold || 250;
        msg = msg || '';
        ok( a - threshold < b && a + threshold > b, [msg, ':', a, '~=', b].join(' ') );
    };

    (function () {
        module('throttle');

        var throttledFunc = InkEvent.throttle(function () {
            ok(true, 'called');
        }, 100);

        asyncTest('limit amount of calls', function () {
            throttledFunc();
            throttledFunc();
            throttledFunc();
            throttledFunc();
            throttledFunc(); // Call this a couple of times, assert called twice.
            expect(2);
            setTimeout(start, 300);
        });
        asyncTest('limit amount of calls (2)', function () {
            throttledFunc(); // Call this once, assert called once.
            expect(1);
            setTimeout(start, 200);
        });
        asyncTest('throttle (context and arguments)', function () {
            expect(2);
            var withArgs = InkEvent.throttle(function (arg) {
                equal(arg, 'arg');
                equal(this, 'this');
            }, 0);
            withArgs.call('this', 'arg');
            setTimeout(start, 50);
        });
        asyncTest('throttle (called few times)', function () {
            var fewTimes = InkEvent.throttle(function () { ok(true); }, 20);

            expect(3);
            setTimeout(fewTimes, 1);
            setTimeout(fewTimes, 100);
            setTimeout(fewTimes, 200);

            setTimeout(start, 300);
        });

        test('throttle called with the correct timing between calls', function () {
            // Timing of the calls we will barrage throttled() with
            var cTming = [
                0,0,0,0,0,100,
                1000];

            // The times at which throttled() should be called
            var timing = [0, 500, 1000];
            var c = -1;


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
            stop(timing.length);
            /* expect [timing] assertions */
            expect(timing.length);
        });
    }());

    module('fire');

    asyncTest('firing events', function () {
        var elem = InkElement.create('div');
        document.body.appendChild(elem);

        InkEvent.observe(elem, 'click', function (ev) {
            equal(ev.memo.memo, 'check');
            start();
        });

        document.body.removeChild(elem);

        InkEvent.fire(elem, 'click', {memo: 'check'});
    });

    (function () {
        var elem,
            child,
            grandChild;

        module('observeDelegated', {
            setup: function () {
                elem = InkElement.create('ul');
                child = InkElement.create('li');
                grandChild = InkElement.create('span');
                grandChild.className = 'the-grandchild';

                elem.appendChild(child);
                child.appendChild(grandChild);
                document.body.appendChild(elem);
            },
            teardown: function () {
                document.body.removeChild(elem);
            }
        });
        asyncTest('observeDelegated', function () {
            expect(1);
            InkEvent.observeDelegated(elem, 'click', 'li', function (event) {
                ok(this === child, '<this> is the selected tag');
                start();
            });

            InkEvent.fire(child, 'click');
        });

        asyncTest('observeDelegated', function () {
            expect(0);
            InkEvent.observeDelegated(elem, 'click', 'ul', function (event) {
                ok(false, 'should not fire event on delegation parent');
            });

            InkEvent.fire(child, 'click');
            setTimeout(start, 100);
        });

        asyncTest('observeDelegated + some selectors', function () {
            
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

            InkEvent.observeDelegated(elem, 'click', 'li > span.the-grandchild', function () {
                ok(true, 'selected by class, correctly');
            });

            expect(2);

            InkEvent.fire(grandChild, 'click');

            setTimeout(start, 100);
        });
    }());

    module('hashchange', {
        setup: function () { location.hash = ''; },
        teardown: function () { location.hash = ''; }
    });

    asyncTest('observe it', function ( ) {
        if (Browser.IE && parseFloat(Browser.version) < 8) {
            ok(true, 'skipped');
            start();
            return;
        }

        var cb = InkEvent.observe( window , 'hashchange' , function( e ) {
            ok(true, 'callback to onhashchange called');
            InkEvent.stopObserving(window, 'hashchange', cb);
            start();
        });

        location.hash = 'changed';
    });

    module('pushstate');
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

