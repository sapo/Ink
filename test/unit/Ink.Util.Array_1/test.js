/*globals deepEqual,test,expect*/
Ink.requireModules(['Ink.Util.Array_1'], function (InkArray) {
    'use strict';
    module('ES5 additions');
    test('isArray', function () {
        var shouldBeArray = [[], [1]];

        var shouldNotBeArray = [
            null,
            void 0,
            '',
            'asd',
            123,
            { length: 1, '0': 'foo' }
        ];

        for (var i = 0, len = shouldNotBeArray.length; i < len; i++) {
            equal(InkArray.isArray(shouldNotBeArray[i]), false, 'isArray(' + shouldNotBeArray[i] + ') should be false!');
        }

        for (i = 0, len = shouldBeArray.length; i < len; i++) {
            equal(InkArray.isArray(shouldBeArray[i]), true, 'isArray(' + shouldBeArray[i] + ') should be true!');
        }
    });

    test('Map', function () {
        var inp = [3, 5, 2, 6];
        expect(9);
        var mapped = InkArray.map(inp, function (v, i, all) {
            deepEqual(v, inp[i]);
            deepEqual(all, inp);
            return v + 1;
        });
        deepEqual(mapped, [4, 6, 3, 7]);
    });

    test('forEach', function () {
        var inp = [3, 5, 2, 6];
        expect(9);
        InkArray.forEach(inp, function (v, i, all) {
            deepEqual(v, inp[i]);
            deepEqual(all, inp);
            all[i] = 'mess'[i];
        });
        deepEqual(inp, ['m','e','s','s']);
    });

    test('filter', function () {
        var inp = [3, 5, 2, 6];
        expect(9);
        var filtered = InkArray.filter(inp, function (v, i, all) {
            deepEqual(v, inp[i]);
            deepEqual(all, inp);
            return v <= 3;
        });
        deepEqual(filtered, [3, 2]);
    });

    test('map context', 1, function () {
        InkArray.map([1], function (v, i, all) {
            deepEqual(this, 'this');
        }, 'this');
    });
    test('forEach context', 1, function () {
        InkArray.forEach([1], function (v, i, all) {
            deepEqual(this, 'this');
        }, 'this');
    });
    test('filter context', 1, function () {
        InkArray.filter([1], function (v, i, all) {
            deepEqual(this, 'this');
        }, 'this');
    });
});
