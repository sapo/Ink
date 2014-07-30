/*globals equal,test,deepEqual,hugeObject,ok,module*/
Ink.requireModules(['Ink.Util.Cookie'], function (Cookie) {
    'use strict';

    test('Sets a cookie, retrieves a cookie. Default options.', function () {
        Cookie.set('123', '456')
        equal(Cookie.get('123'),
             '456')
    });
});
