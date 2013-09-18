/*globals equal,test,start,stop,expect,QUnit,ok*/
QUnit.config.testTimeout = 4000;
Ink.requireModules(['Ink.Dom.Loaded_1'], function (Loaded) {
    'use strict';
    test('several Loaded callbacks called in order', function () {
        expect(3);  // 3 assertions
        stop(3);  // 3 start()s
        var i = 0;
        Loaded.run(function () {
            equal(++i, 1, 'called first');
            start();
        });
        Loaded.run(function () {
            equal(++i, 2, 'called second');
            start();
        });
        Loaded.run(function () {
            equal(++i, 3, 'called third');
            start();
        });
    });
    test('Several contexts', function () {
        expect(2);
        stop(2);
        var iframe = document.createElement('iframe');
        iframe.src = 'iframe.html';
        document.getElementsByTagName('body')[0].appendChild(iframe);
        var iframeWindow = iframe.contentWindow;
        Loaded.run(iframeWindow, function () {
            equal(iframeWindow, this, 'being called with the iframe window');
            iframe.parentNode.removeChild(iframe);
            start();
        });
        Loaded.run(window, function () {
            equal(window, this, 'being called with this window');
            start();
        });
    });
    test('Nested calls', function () {
        expect(3);
        stop(3); // start() gets called thrice
        var i = 0;
        Loaded.run(function () {
            equal(++i, 1, 'called first');
            start();
            Loaded.run(function () {
                equal(++i, 2, 'called second');
                start();
                Loaded.run(function () {
                    equal(++i, 3, 'called third');
                    start();
                });
            });
        });
    });
    test('When document is loaded, still wait for next tick', function () {
        expect(2);
        stop();
        var i = 0;
        Loaded.run(function () {
            equal(++i, 2, 'called second, after the function returns');
            start();
        });
        equal(++i, 1, 'called first');
    });
    test('Use timeout.php, a page that just sleeps a bit', function () {
        expect(4);
        stop(2);
        var iframe,
            startt = +new Date();
        for (var i = 1; i <= 2; i++) {
            iframe = document.createElement('iframe');
            iframe.src = 'timeout.php?w=' + (i / 2.0);
            document.getElementsByTagName('body')[0].appendChild(iframe);
            var cb = makeCallback(iframe, i);
            Loaded.run(iframe.contentWindow, cb);
        }
        function makeCallback(iframe, i) {
            return function () {
                var dt = +new Date() - startt;
                equal(this, iframe.contentWindow, 'iframe content window');
                ok(dt > i / 2.0, dt);
                iframe.parentNode.removeChild(iframe);
                start();
            };
        }
    });
});
