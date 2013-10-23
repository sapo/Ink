// QUnit.config.testTimeout = 4000;

Ink.requireModules(['Ink.Dom.Event_1', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1', 'Ink.Dom.Browser_1'], function (InkEvent, InkElement, Selector, Browser) {
    (function () {
        module('throttle');

        var nearEqual = function (a, b, threshold, msg) {
            threshold = threshold || 250;
            msg = msg || '';
            ok( a - threshold < b && a + threshold > b, [msg, ':', a, '~=', b].join(' ') );
        };
        var pass = function () { ok(true); };
        var passStart = function () { ok(true); start(); };
        var fail = function () { ok(false); };
        var throttledFunc = InkEvent.throttle(function (cb) {
            cb && cb();
        }, 100);

        test('limit amount of calls', function () {
            expect(2); stop(2);
            throttledFunc(passStart);
            throttledFunc(passStart);
            throttledFunc(fail);
            throttledFunc(fail);
            throttledFunc(fail); // Call this a couple of times, assert called twice.
        });
        asyncTest('limit amount of calls (2)', function () {
            throttledFunc(passStart); // Call this once, assert called once.
            expect(1);
        });
        asyncTest('throttle (context and arguments)', function () {
            expect(2);
            InkEvent.throttle(function (arg) {
                equal(arg, 'arg');
                equal(this, 'this');
                start();
            }, 0).call('this', 'arg');
        });
        test('throttle (called few times)', function () {
            var fewTimes = InkEvent.throttle(passStart, 20);

            expect(3);
            stop(3);
            setTimeout(fewTimes, 0);
            setTimeout(fewTimes, 50);
            setTimeout(fewTimes, 100);
        });

        asyncTest('throttle called with the correct timing between calls', function () {
            var firstCallTime;
            var throttled = InkEvent.throttle(function () {
                if (firstCallTime) {
                    nearEqual(+new Date() - firstCallTime, 200, 150);
                    start();
                } else {
                    firstCallTime = +new Date();
                }
            }, 200);

            expect(1);

            throttled();
            throttled();
            throttled();
        });
    }());

    module('fire');

    asyncTest('firing events', function () {
        var elem = InkElement.create('div');
        document.body.appendChild(elem);

        InkEvent.observe(elem, 'click', function (ev) {
            equal(ev.memo.memo, 'check');
            document.body.removeChild(elem);
            start();
        });


        InkEvent.fire(elem, 'click', {memo: 'check'});
    });

    asyncTest('fire() and bubbling', function () {
        var elem = InkElement.create('div', {className: 'elem'});
        var child = InkElement.create('div', { insertBottom: elem });
        document.body.appendChild(elem);

        InkEvent.observe(elem, 'click', function (ev) {
            equal(ev.memo.memo, 'check');
            ok(InkEvent.element(ev) === child);
            ok(this === elem);
            document.body.removeChild(elem);
            start();
        });

        InkEvent.fire(child, 'click', {memo: 'check'});
    });

    (function () {
        // TODO DRY this
        var elem,
            child,
            grandChild;

        module('observeDelegated fired', {
            setup: function () {
                elem = InkElement.create('ul');
                child = InkElement.create('li');
                grandChild = InkElement.create('span');
                grandChild.className = 'the-grandchild';

                elem.appendChild(child);
                child.appendChild(grandChild);
                document.body.appendChild(elem);
            },
            teardown: function () { document.body.removeChild(elem); }
        });

        asyncTest('observeDelegated fired', function () {
            
            expect(2);
            InkEvent.observeDelegated(elem, 'click', 'li', function (event) {
                ok(this === child, '<this> is the selected tag');
                ok(InkEvent.element(event) === grandChild, 'InkEvent.element(event) resolves to the element that the event came from');
                start( );
            });

            InkEvent.fire(grandChild, 'click');
        });
    })();

    (function () {
        var elem,
            child,
            grandChild;

        module('observeDelegated not fired' , {
            setup: function () {
                elem = InkElement.create('ul');
                child = InkElement.create('li');
                grandChild = InkElement.create('span');
                grandChild.className = 'the-grandchild';

                elem.appendChild(child);
                child.appendChild(grandChild);
                document.body.appendChild(elem);
            },
            teardown: function () { document.body.removeChild(elem); }
        });
        asyncTest('observeDelegated not fired', function () {
            expect(1);
            InkEvent.observeDelegated(elem, 'click', 'ul', function (event) {
                ok(false, 'should not fire event on delegation parent');
            });
            InkEvent.observe(elem, 'click', function (event) {
                ok(true, 'should fire normal event');
                start( )
            });

            InkEvent.fire(elem, 'click');
        });
    })( );

    (function () {
        var elem,
            child,
            grandChild;

        module('observeDelegated not fired' , {
            setup: function () {
                elem = InkElement.create('ul');
                child = InkElement.create('li');
                grandChild = InkElement.create('span');
                grandChild.className = 'the-grandchild';

                elem.appendChild(child);
                child.appendChild(grandChild);
                document.body.appendChild(elem);
            },
            teardown: function () { document.body.removeChild(elem); }
        });

        test('observeDelegated + some selectors', function () {
            expect(2);
            stop(2);
            InkEvent.observeDelegated(elem, 'click', 'li > span.classIDontHave', function () {
                ok(false, 'should not find this element');
            });

            InkEvent.observeDelegated(child, 'click', 'ul > li > span', function (event) {
                ok(false, 'should not be able to select through parents');
            });

            InkEvent.observeDelegated(elem, 'click', 'li > span', function () {
                ok(true, 'selected by tag name, correctly');
                start();
            });

            InkEvent.observeDelegated(elem, 'click', 'li > span.the-grandchild', function () {
                ok(true, 'selected by class, correctly');
                start();
            });

            InkEvent.fire(grandChild, 'click');
        });
    }());

    (function () {
        var elem,
            child,
            grandChild;

        module('observeDelegated on many children' , {
            setup: function () {
                elem = InkElement.create('ul');
                child = InkElement.create('li');
                grandChild = InkElement.create('span');
                grandChild2 = InkElement.create('span');
                grandChild3 = InkElement.create('span');
                grandChild.className = 'the-grandchild';
                grandChild2.className = 'the-grandchild-2';
                grandChild3.className = 'the-grandchild-3';

                elem.appendChild(child);
                child.appendChild(grandChild);
                child.appendChild(grandChild2);
                child.appendChild(grandChild3);
                document.body.appendChild(elem);
            },
            teardown: function () { document.body.removeChild(elem); }
        });

        asyncTest('observeDelegated on many children', function () {
            expect(1);

            InkEvent.observeDelegated(elem, 'click', '.the-grandchild', function () {
                ok(false);
            });

            InkEvent.observeDelegated(elem, 'click', '.the-grandchild-2', function () {
                ok(this === grandChild2);
                start();
            });

            InkEvent.observeDelegated(elem, 'click', '.the-grandchild-3', function () {
                ok(false);
            });

            InkEvent.fire(grandChild2, 'click');
        });
    }())

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

