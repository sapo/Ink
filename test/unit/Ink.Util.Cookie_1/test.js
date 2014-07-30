/*globals equal,test,deepEqual,hugeObject,ok,module*/
Ink.requireModules(['Ink.Util.Cookie'], function (Cookie) {
    'use strict';

    var randCookie = 'ink-util-cookie-test-' + new Date().getTime();

    test('Sets a cookie, retrieves a cookie. Default options.', function () {
        Cookie.set(randCookie + '123', '456')
        equal(Cookie.get(randCookie + '123'),
             '456')
    });

    test('deletes a cookie', function () {
        Cookie.set(randCookie + 'foo', '456');
        strictEqual(Cookie.get(randCookie + 'foo'), '456');
        Cookie.remove(randCookie + 'foo');
        strictEqual(Cookie.get(randCookie + 'foo'), null);
    });

    test('Cookie values with dangerous characters ([= ;])', function () {
        Cookie.set(randCookie + 'equal', '=');
        Cookie.set(randCookie + 'plus', '+');
        Cookie.set(randCookie + 'space', ' ');

        strictEqual(
            Cookie.get(randCookie + 'equal'), '=');
        strictEqual(
            Cookie.get(randCookie + 'plus'), '+');
        strictEqual(
            Cookie.get(randCookie + 'space'), ' ');
    });
});
