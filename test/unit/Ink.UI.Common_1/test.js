

Ink.requireModules(['Ink.UI.Common_1', 'Ink.Dom.Event_1', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1', 'Ink.Dom.Browser_1'], function (Common, InkEvent, InkElement, Selector, Browser) {
    'use strict';

    function bagTest(message, cb) {
        test(message, function () {
            var bag = InkElement.create('div', {insertBottom: document.body, id: 'bag'});
            bag.appendChild(InkElement.create('div', { id: 'somediv'}));
            bag.appendChild(InkElement.create('div', { className: 'somedivs'}));
            bag.appendChild(InkElement.create('div', { className: 'somedivs'}));
            cb(bag);
            document.body.removeChild(bag);
        });
    }

    module('elsOrSelector()');

    bagTest('send a valid selector with elements', function () {
        var result = Common.elsOrSelector('#bag .somedivs');
        ok(typeof result === 'object' && typeof result.length === 'number');
        ok(result[0] === bag.children[1]);
        ok(result[1] === bag.children[2]);
    });

    bagTest('"required" argument', function () {
        deepEqual(
            Common.elsOrSelector('#bag #i-do-not-exist', 'TestFieldName', /* not required */false),
            [],
            'required can be set to false to avoid errors');

        throws(function () {
            Common.elsOrSelector('#bag #i-do-not-exist', 'TestFieldName', true);
        }, /TestFieldName/, '');

        deepEqual(
            Common.elsOrSelector('#bag #i-do-not-exist', 'TestFieldName' /* [false] */),
            [],
            'When ommitted, defaults to `false`');
    });

    bagTest('send an invalid selector', function () {
        throws(function () {
            Common.elsOrSelector('[ro.ng');
        }, /\[ro.ng/);
    });

    bagTest('Send a single element', function (bag) {
        ok(Common.elsOrSelector(bag)[0] === bag);
    });

    bagTest('Send an actual array of elements, returned as-is', function (bag) {
        ok(bag.children.length, 'sanity check');
        deepEqual(
            Common.elsOrSelector(Selector.select('*', bag)),
            Selector.select('*', bag));
        deepEqual(Common.elsOrSelector([], '', false), []);
    });


    module('options()');

    test('elm argument is optional, options come with defaults', function () {
        var o = Common.options({
            'target': ['Number', -1],
            'somethingElse': ['Number', -1]
        }, {target: 'target'});

        equal(o.target, 'target');
        equal(o.somethingElse, -1);
    });

    test('precedence of options in elm', function () {
        var o = Common.options({
            'defaultOpt': ['String', 'true'],
            'elementOpt': ['String', ''],
            'givenOpt': ['String', '']
        }, {
            givenOpt: 'true'
        }, InkElement.create('div', {
            'data-element-opt': 'true'
        }));

        ok(o.defaultOpt.toString() === 'true');
        ok(o.elementOpt.toString() === 'true');
        ok(o.  givenOpt.toString() === 'true');
    });

    (function () {
        var defaults = {
            'target': ['Element', null],
            'stuff': ['Number', 0.1],
            'stuff2': ['Integer', 0],
            'doKickFlip': ['Boolean', false],
            'targets': ['Elements'], // Required option
            'onClick': ['Function', null]
        };
        bagTest('type coersion for the types typeof can give you', function (bag) {
            bag.setAttribute('data-target', '#somediv');
            bag.setAttribute('data-stuff', '0.1');
            bag.setAttribute('data-stuff2', '0');
            bag.setAttribute('data-do-kick-flip', 'false');
            bag.setAttribute('data-targets', '.somedivs');

            var o = Common.options(defaults, {}, bag);

            equal(typeof o.target, 'object');
            equal(typeof o.stuff, 'number');
            equal(typeof o.stuff2, 'number');
            equal(typeof o.doKickFlip, 'boolean');
            equal(typeof o.targets, 'object');
            equal(typeof o.onClick, 'object');

            ok   (o.target === bag.children[0], 'target is first child');
            equal(o.stuff, 0.1);
            equal(o.stuff2, 0);
            equal(o.doKickFlip, false);
            equal(o.targets.length, 2);
            equal(o.targets[0], bag.children[1]);
            equal(o.targets[1], bag.children[2]);
            equal(o.onClick, null);
        });

        bagTest('stuff should go to default when unspecified', function (bag) {
            var o = Common.options(defaults, {targets:12345}, bag);
            deepEqual(o, {
                'target': null,
                'stuff': 0.1,
                'stuff2': 0,
                'doKickFlip': false,
                'targets': 12345,
                'onClick': null
            });
        });

        bagTest('non-arity as default', function (bag) {
            var o = Common.options({ str: ['string', '123'], bool: ['boolean', true] }, {}, bag);
            ok(o.str === '123');
            deepEqual(o.bool, true);
        });

        bagTest('the empty string as an option', function (bag) {
            bag.setAttribute('data-str', '');
            bag.setAttribute('data-bool', '');
            var o = Common.options({ str: ['string'], bool: ['boolean'] }, {}, bag);
            ok(o.str === '');
            deepEqual(o.bool, false);
        });

        bagTest('invalid options', function (bag) {
            bag.setAttribute('data-int', '1.1');
            bag.setAttribute('data-numb', 'NaN');
            bag.setAttribute('data-func', 'somethin');

            sinon.spy(Ink, 'error');

            deepEqual(Common.options('fakeComponent', {
                    int: ['Integer', 2]
                }, {}, bag),
                {int: 2});

            deepEqual(Common.options('fakeComponent', {
                    numb: ['Float', 2.2]
                }, {}, bag),
                { numb: 2.2 });

            deepEqual(Common.options('fakeComponent', {
                    func: ['Function', 'somethin']
                }, {}, bag),
                { func: 'somethin' });

            var errCalls = Ink.error.getCalls();
            equal(errCalls.length, 3, 'Ink.error called 3 times');

            for (var i = 0, len = errCalls.length; i < len; i++) {
                ok(/fakeComponent/.test(errCalls[i].args[0]), i + 'th call to Ink.error mentions "fakeComponent" substring');
            }

            Ink.error.restore();
        });
    }());

    test('defaults are not type-checked or coerced', function () {
        equal(Common.options({
            option1: ['Number', 'CTHULHU']
        }).option1, 'CTHULHU');  // no exception either
    });

    test('no type coersion for data given through javascript', function (bag) {
        var o;
        o = Common.options({
            option1: ['Number', 10]
        }, {option1: '10' });
        ok(o.option1 === '10');
        o = Common.options({
            option1: ['Boolean', false]
        }, {option1: '123' });
        ok(o.option1 === '123');
        o = Common.options({
            option1: ['Boolean', true]
        }, {option1: '123' });
        ok(o.option1 === '123');
    });

    test('error is thrown when nothing comes through for required options', function () {
        throws(function () {
            Common.options({
                required: ['string']
            });
        }, /required/);
    });

    test('error is thrown when type is unknown', function () {
        throws(function () {
            Common.options({
                thisIsOfAnUnknownType: ['strungngabhng', '']
            });
        }, /strungngabhng/);
    });

    test('error comes with passed fieldId string embedded unto it', function () {
        throws(function () {
            Common.options('fieldIdTheMostWeirdArgumentNameEVER', {
                thisIsOfAnUnknownType: ['strungngabhng', '']
            });
        }, /fieldIdTheMostWeirdArgumentNameEVER/);
    });
});

