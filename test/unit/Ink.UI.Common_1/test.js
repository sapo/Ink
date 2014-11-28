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

    var elm, inst;
    module('UI module registry', {
        setup: function () {
            elm = InkElement.create('div');
            function UIComp() {};
            inst = new UIComp();
            inst._element = elm;
        }
    });

    test('Holds instances, can retrieve them', function () {
        Common.registerInstance(inst, elm);
        strictEqual(Common.getInstance(elm)[0], inst);
    });

    test('Can unregister an instance', function () {
        Common.registerInstance(inst, elm);
        Common.unregisterInstance(inst);
        ok(!Common.getInstance(elm)[0]);
    });

    test('Does not break when getting instances from an element without them', function () {
        deepEqual(Common.getInstance(elm), []);
    });

    test('Calls Common.isDOMElement. If it returns false, warns and returns [].', sinon.test(function () {
        this.stub(Ink, 'warn');
        this.stub(Common, 'isDOMElement').returns(false);

        var ret = Common.getInstance('fakeelement');

        ok(Common.isDOMElement.calledWith('fakeelement'), 'isDOMElement called with the element');

        deepEqual(ret, [], 'getInstance returned []');

        ok(Ink.warn.called, 'Ink.warn called');
        ok(/fakeelement/.test(Ink.warn.lastCall.args[0]), 'Warned the supposed "element"');
    }));

    test('Can unregister instances', function () {
        Common.registerInstance(inst, elm);
        Common.unregisterInstance(inst);
        deepEqual(Common.getInstance(elm), []);
    });

    test('getInstance retrieves an instance by instance type', function () {
        Common.registerInstance(inst, elm);
        strictEqual(Common.getInstance(elm, inst.constructor), inst);
        strictEqual(Common.getInstance(elm, function SomeOtherConstructor() {}), null);
    });

    test('Can hold instances of different components, ordering them', function () {
        var inst2 = {};
        Common.registerInstance(inst, elm);
        Common.registerInstance(inst2, elm);

        deepEqual(Common.getInstance(elm), [inst, inst2]);
    });

    test('calls _warnDoubleInstantiation', sinon.test(function () {
        this.spy(Common, '_warnDoubleInstantiation');
        Common.registerInstance(inst, elm);
        ok(Common._warnDoubleInstantiation.calledOnce);
        deepEqual(Common._warnDoubleInstantiation.lastCall.args[0], elm);
    }));

    test('_warnDoubleInstantiation calls Ink.warn if not the first module of the same type to be initialized', sinon.test(function () {
        function SomeConstructor() {}
        SomeConstructor._name = 'Modal_1';

        inst = new SomeConstructor();

        this.spy(Ink, 'warn');
        Common._warnDoubleInstantiation(elm, inst);
        ok(Ink.warn.notCalled, 'Ink.warn wasn\'t called');
        Common.registerInstance(inst, elm);
        Common._warnDoubleInstantiation(elm, inst);
        ok(Ink.warn.called, 'Ink.warn was called');
    }));

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

    var testFunc;
    module('createUIComponent', {
        setup: function () {
            testFunc = function () {
                Common.BaseUIComponent.apply(this, arguments);
            }

            testFunc._name = 'TestModule_1';
            testFunc._optionDefinition = {};
        }
    })

    function throwsWithArgument(argument) {
        throws(function () { Common.createUIComponent(argument) })
    }

    test('Fails on null/undefined constructors', function () {
        throwsWithArgument(null);
        throwsWithArgument(undefined);
        throwsWithArgument('');
    });
    test('Fails on constructors without required properties', function () {
        Common.createUIComponent(testFunc);
        delete testFunc._name;
        delete testFunc._optionDefinition;
        throwsWithArgument(testFunc);
    });
    test('Makes the module inherit BaseUIComponent', function () {
        Common.createUIComponent(testFunc)
        ok((new testFunc(document.createElement('div'))) instanceof Common.BaseUIComponent);
    });
    test('Doesn\'t hurt existing prototype', function () {
        testFunc.prototype.foobarbaz = 'foobarbaz'
        Common.createUIComponent(testFunc);
        equal(testFunc.prototype.foobarbaz, 'foobarbaz');
    });

    var testEl;
    var testOpts;

    module('BaseUIComponent', {
        setup: function () {
            testFunc = function TestFunc () {
                Common.BaseUIComponent.apply(this, arguments);
            };

            testFunc.prototype._init = function () {};
            testFunc._name = 'TestModule_1';
            testFunc._optionDefinition = {
                foo: ['String', null]
            };

            testEl = document.createElement('div');
            testOpts = { foo: 'bar' };

            Common.createUIComponent(testFunc);
        }
    });

    test('its constructor: calls _init, populates _options and _element', sinon.test(function () {
        this.stub(testFunc.prototype, '_init');
        var instance = new testFunc(testEl, testOpts);
        equal(testFunc.prototype._init.calledOnce, true, '_init was called');
        ok(testFunc.prototype._init.calledOn(instance), '_init was called on the instance');

        deepEqual(instance._options, testOpts, 'options were created upon the instance');
        strictEqual(instance._element, testEl, 'element was passed');
    }));

    test('its constructor: (regression) calls Ink.error() when selector finds nothing', sinon.test(function () {
        this.stub(Common, 'elsOrSelector').returns([])
        this.stub(Ink, 'error');

        var instance = new testFunc(testEl, testOpts);

        ok(Ink.error.called);
    }))

    test('its constructor: calls BaseUIComponent._validateInstance', sinon.test(function () {
        this.stub(Common.BaseUIComponent, '_validateInstance');

        var instance = new testFunc(testEl, testOpts);

        ok(Common.BaseUIComponent._validateInstance.calledOnce);
        ok(Common.BaseUIComponent._validateInstance.calledWith(instance));
    }));

    test('its constructor: calls Common.registerInstance', sinon.test(function () {
        this.stub(Common, 'registerInstance');

        var instance = new testFunc(testEl, testOpts);

        ok(Common.registerInstance.calledOnce);
        ok(Common.registerInstance.calledWith(instance));
    }));

    test('its constructor: if BaseUIComponent._validateInstance returns false, stubs the instance by calling BaseUIComponent._stubInstance', sinon.test(function () {
        this.stub(Common.BaseUIComponent, '_stubInstance');
        var stub = this.stub(Common.BaseUIComponent, '_validateInstance');
        stub.returns(true);

        equal(Common.BaseUIComponent._validateInstance(), true, 'sanity check');

        new testFunc(testEl, testOpts);
        equal(Common.BaseUIComponent._stubInstance.callCount, 0);

        stub.returns(false);
        equal(Common.BaseUIComponent._validateInstance(), false, 'sanity check');

        var inst = new testFunc(testEl, testOpts);

        equal(Common.BaseUIComponent._stubInstance.callCount, 1, '_stubInstance was called once');
        equal(Common.BaseUIComponent._stubInstance.calledWith(inst), true, '... with the instance');
    }));

    test('_validateInstance calls the instance\'s _validate() method, returns false if it returnsor throws an error', function () {
        var _validateInstance = Ink.bindMethod(Common.BaseUIComponent, '_validateInstance');
        var mockInstance = {}

        mockInstance._validate = sinon.stub().returns(undefined) 
        equal(
            _validateInstance(mockInstance, testFunc, 'TestFunc_1'),
            true,
            'validate returned non-error');

        mockInstance._validate = sinon.stub().returns(new Error);
        equal(
            _validateInstance(mockInstance, testFunc, 'TestFunc_1'),
            false,
            '_validate() returned an error');

        mockInstance._validate = sinon.stub().throws(new Error('Oops! I threw it again!'));
        equal(
            _validateInstance(mockInstance, testFunc, 'TestFunc_1'),
            false,
            '_validate() threw an error');
    });

    test('_stubInstance Replaces instance\'s functions with stubs which do nothing other than call Ink.warn', sinon.test(function () {
        var _stubInstance = Ink.bindMethod(Common.BaseUIComponent, '_stubInstance');

        var fooMeth = sinon.stub();
        var mockInstance = { 'foo': fooMeth }

        sinon.stub(Ink, 'warn');
        _stubInstance(mockInstance, { prototype: { foo: function () {} } }, 'THE_NAME')
        ok(Ink.warn.calledWith(sinon.match('THE_NAME')))

        notStrictEqual(mockInstance.foo, fooMeth);

        mockInstance.foo();
        ok(Ink.warn.calledTwice);
    }));

    var baseUIProto = Common.BaseUIComponent.prototype;
    test('#getOption and #getElement', function() {
        var inst = new testFunc(testEl, { foo: 'qux' });

        strictEqual(inst.getOption('foo'), 'qux');
        strictEqual(inst.getElement(), testEl);
    });

    test('setOption', function () {
        var inst = new testFunc(testEl);

        strictEqual(inst.getOption('foo'), null);
        inst.setOption('foo', 'baz');
        strictEqual(inst.getOption('foo'), 'baz');
    })

    test('static methods: getInstance', sinon.test(function () {
        this.spy(Common, 'elOrSelector');
        this.stub(Common, 'getInstance').returns('the instance!');

        strictEqual(
            testFunc.getInstance(testEl),
            'the instance!',
            'Calling getInstance returns whatever Common.getInstance returned')

        ok(Common.elOrSelector.calledWith(testEl), 'elOrSelector called with testEl');
        ok(Common.getInstance.calledWith(testEl, testFunc), 'getInstance called with testEl, testFunc');
    }))
});

