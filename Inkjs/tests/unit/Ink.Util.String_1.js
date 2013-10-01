/*globals equal,test*/
Ink.requireModules( [ 'Ink.Util.String_1' ] , function ( InkString ) {
    'use strict';
    
    test('ucFirst', function () {
        var base = ' hello world  !! 1 ';
        equal(InkString.ucFirst(base), ' Hello World  !! 1 ');
        equal(InkString.ucFirst(base, true), ' Hello world  !! 1 ');
    });
});
