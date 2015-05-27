/*globals deepEqual,test,expect*/
Ink.requireModules(['Ink.Util.Array_1'], function (InkArray) {
    'use strict';
    module('ES5 additions');
    test('isArray', function () {
        var shouldBeArray = [
            [],
            [1]
        ];

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
            all[i] = ('mess').charAt(i);
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

    var sum = function (a, b) { return a + b }

    test('reduce', function () {
        equal(InkArray.reduce([1, 2, 3], sum), 6);
    })

    test('reduce(with initial value', function () {
        throws(function () {
            equal(InkArray.reduce([], sum), 6);
        });
        equal(InkArray.reduce([2, 3], sum, 1), 6);
        equal(InkArray.reduce([], sum, 'foo'), 'foo');
    })

    module('');

    test('forEachObj', function() {
        var inpt = {
            a: '1',
            b: '2'
        }

        var ctx = {}

        var callback = sinon.spy()

        InkArray.forEachObj(inpt, callback, ctx)

        ok(callback.calledTwice)

        ok(callback.calledWith('1', 'a', inpt), 'called with (value, key, all)')
        ok(callback.calledWith('2', 'b', inpt), 'called with (value2, key2, all)')
        ok(callback.calledOn(ctx), 'called with the correct context')
    })

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

    test('groupBy()', function () {
        deepEqual(
            InkArray.groupBy(
                'AAAABBBCCDAABBB'.split('')),
            [
                ['A', 'A', 'A', 'A', 'A', 'A'],
                ['B', 'B', 'B', 'B', 'B', 'B'],
                ['C', 'C'],
                ['D']
            ],
            'default behaviour');

        deepEqual(
            InkArray.groupBy(
                'CAABCD'.split('')),
            [
                ['C', 'C'],
                ['A', 'A'],
                ['B'],
                ['D']
            ],
            'grouping does not sort the groups');

        deepEqual(
            InkArray.groupBy('AAAABBBCCDAABBB'.split(''), { adjacentGroups: true }),
            [
                ['A', 'A', 'A', 'A'],
                ['B', 'B', 'B'],
                ['C', 'C'],
                ['D'],
                ['A', 'A'],
                ['B', 'B', 'B']
            ],
            'adjacent groups');

        deepEqual(
            InkArray.groupBy(
                [0.1, 0.2, 0.3, 2.2, 1.1, 1.2, 1.3, 2.5],
                { key: Math.floor, pairs: true }),
            [
                [0, [0.1, 0.2, 0.3]],
                [2, [2.2, 2.5]],
                [1, [1.1, 1.2, 1.3]]
            ],
            'key function and pairs:true');

        deepEqual(
            InkArray.groupBy(
                [0.1, 0.2, 0.3, 2.3, 1.1, 1.2, 1.3, 2.5],
                { key: Math.floor, pairs: true, adjacentGroups: true }),
            [
                [0, [0.1, 0.2, 0.3]],
                [2, [2.3]],
                [1, [1.1, 1.2, 1.3]],
                [2, [2.5]]
            ],
            'key function, pairs:true and adjacent groups');
    });

    test('groupBy() key function can be a string, it takes it from the object', function () {
        deepEqual(
            InkArray.groupBy([
                { name: 'Bob', 'class': 1999 },
                { name: 'Jane', 'class': 2001 },
                { name: 'Steve', 'class': 2001 },
                { name: 'Bettie', 'class': 2002 }
            ], { key: 'class' }),
            [
                [
                    { "class": 1999, "name": "Bob" }
                ],
                [
                    { "class": 2001, "name": "Jane" },
                    { "class": 2001, "name": "Steve" }
                ],
                [
                    { "class": 2002, "name": "Bettie" }
                ]
            ],
            'Using a string as a key');
    });

    test('range()', function () {
        var range = InkArray.range;
        deepEqual(range(0, 10), [0, 1, 2, 3, 4, 5, 6, 7, 8, 9], 'Simple range');
        deepEqual(range(0, 10, 2), [0, 2, 4, 6, 8], 'Range with step');
        deepEqual(range(0, 10), [0, 1, 2, 3, 4, 5, 6, 7, 8, 9], 'range property test');
        deepEqual(range(10, 0, -1), [10, 9, 8, 7, 6, 5, 4, 3, 2, 1], 'negative step');
        deepEqual(range(0, 3, 2), [0, 2], 'regression: if step is not divisible by abs(start - stop) we get an infinite loop');
        deepEqual(range(3, 0, -2), [3, 1], 'regression: if step is not divisible by abs(start - stop) we get an infinite loop (negative step now)');
    })

    test('regression: range(10) should behave like range(0, 10)', function() {
        var range = InkArray.range

        deepEqual(range(5), [0, 1, 2, 3, 4])
    })

    test('keys', function() {
        deepEqual(InkArray.keys({ foo: 1, bar: 2}), ['foo', 'bar'], 'returns keys in object')
        deepEqual(InkArray.keys({ }), [], 'empty object results in empty array')
        throws(function () {
            InkArray.keys(null)
        })
    })
});
