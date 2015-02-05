/*globals equal,test,asyncTest,stop,start,ok,expect*/
(function () {
'use strict';

QUnit.config.testTimeout = 4000
test('bindMethod', function () {
    var obj = {
        test0: function () {
            return [].slice.call(arguments);
        },
        test1: function () {
            return [].slice.call(arguments);
        },
        test2: function () {
            return this;
        }
    };
    var test0 = Ink.bindMethod(obj, 'test0');
    var test1 = Ink.bindMethod(obj, 'test1', 1, 2, 3, 4);
    var test2 = Ink.bindMethod(obj, 'test2');

    deepEqual(test0(1, 2, 3, 4), [1, 2, 3, 4], 'returns same arguments as called with');
    deepEqual(test1(), [1, 2, 3, 4], 'returns arguments given at bind time');
    deepEqual(test2(), obj, 'returns the object owning the method');
});

test('extendObj', function () {
    deepEqual(
        Ink.extendObj({ foo: 'not-bar' }, { foo: 'bar' }),
        { foo: 'bar' },
        'Extends objects right to left, overriding properties on the left');

    deepEqual(
        Ink.extendObj({ foo: '0' }, { foo: '1', baz: '-1' }, { bar: '2', baz: '3' }),
        { foo: '1', bar: '2', baz: '3' },
        'Works on many objects');

    var a = {};
    strictEqual(
        Ink.extendObj(a, { wow: 'amaze' }),
        a,
        'returns a reference to the leftmost object')

    a = {}
    var b = { original: 'object' }
    Ink.extendObj(a, b)
    deepEqual(
        b,
        { original: 'object' },
        'does not touch the source objects')

    a = {}
    b = { original: 'object' }
    Ink.extendObj(a, b, {bar: 'baz'})
    deepEqual(
        b,
        { original: 'object' },
        '(regression) even if there\'s more than one of them')
});

test('createExt', function () {
    stop();  // async
    expect(1);  // only one assertion
    
    Ink.createExt('Lol.Parser', 1, [], function () {
        return {
            parse: function () {}
        };
    });

    Ink.requireModules(['Ink.Ext.Lol.Parser_1'], function (Parser) {
        equal(typeof Parser.parse, 'function', 'checking module');
        start();  // async done
    });
});

test('getPath, setPath', function () {
    Ink.setPath('Ink', 'http://example.com/');
    equal(Ink.getPath('Ink'), 'http://example.com/lib.js');
    equal(Ink.getPath('Ink.Dom.Element'), 'http://example.com/Dom/Element/lib.js');
    equal(Ink.getPath('Ink.Dom.Element.Stuff_1'), 'http://example.com/Dom/Element/Stuff/1/lib.js');
    equal(Ink.getPath('Ink', true), 'http://example.com/');  // noLib === true, so no lib.jhs
});

test('getPath, setPath', function () {
    Ink.setPath('Ink.Sub', 'http://example.com/sub/');
    equal(Ink.getPath('Ink.Sub'), 'http://example.com/sub/lib.js');
    equal(Ink.getPath('Ink.Sub.Sub_1'), 'http://example.com/sub/Sub/1/lib.js');

    Ink.setPath('Plug.Sub', 'http://example.com/subplug/');
    equal(Ink.getPath('Plug.Sub'), 'http://example.com/subplug/lib.js');
    equal(Ink.getPath('Plug.Sub.Sub'), 'http://example.com/subplug/Sub/lib.js');

    Ink.setPath('Ink.Sub.Sub', 'http://example.com/subsub/');
    equal(Ink.getPath('Ink.Sub'), 'http://example.com/sub/lib.js');
    equal(Ink.getPath('Ink.Sub.Sub_whoo'), 'http://example.com/subsub/whoo/lib.js');

    Ink.setPath('Plug.Sub.Sub', 'http://example.com/subsubplug/');
    equal(Ink.getPath('Plug.Sub'), 'http://example.com/subplug/lib.js');
    equal(Ink.getPath('Plug.Sub.Sub'), 'http://example.com/subsubplug/lib.js');

    Ink.setPath('Plug.Sub.Module_1', 'http://example.com/subplugversion1/');
    equal(Ink.getPath('Plug.Sub.Module_1'), 'http://example.com/subplugversion1/lib.js');
});

test('getPath when root path is not recognized', function () {
    strictEqual(Ink.getPath('Unknown123'), null, 'Getting the path of an unknown root module should yield null')
    strictEqual(Ink.getPath('Unknown123.Asd'), null, 'Submodules of unknown root modules, too, yield null')
});

test('setPath supports using no trailing slash', function () {
    Ink.setPath('Abc.Def', '/baz');
    equal(Ink.getPath('Abc.Def_1'), '/baz/1/lib.js');
});

test('loadScript', function () {
    stop();
    expect(2);
    window.loadScriptWorks = function (sayYeah) {
        equal(sayYeah, 'yeah', 'yeah said');
        start();
    };
    Ink.loadScript('./loadscript-test.js');  // This script calls window.loadScriptWorks('yeah')
    var scripts = document.getElementsByTagName('script');
    var _a = document.createElement('a');
    _a.href = './loadscript-test.js';
    for (var i = 0, len = scripts.length; i < len; i++) {
        if (scripts[i].src === _a.href) {
            ok(true, 'script tag inserted as expected');
        }
    }
});

if (window.console && window.console.error) {
    test('loadScript + 404', function () {
        expect(1);
        stop();
        var testDone
        sinon.stub(Ink, 'error', function () {
            if (testDone) { return; }
            testDone = true;
            ok(true, 'Ink.error called');
            Ink.error.restore();
            start();
        });
        var theScript = Ink.loadScript('./not-exists-should-be-a-404.js');

        // For old IE. It doesn't fire an error event when scripts result in 404.
        theScript.onreadystatechange = function() {
            if (theScript.readyState === 'loaded') {
                setTimeout(function () {
                    if (testDone) { return; }
                    testDone = true;
                    ok(true, 'This is old IE, it doesn\'t consider a 404 response for a script to be an error.')
                    Ink.error.restore();
                    start();
                }, 100);
            }
        };
    });
}

(function () {
    Ink.createModule('My.Module', 1, [], function () {
        return {
            my: 'module'
        };
    });

    Ink.createModule('My.Other.Module', 1, ['My.Module_1'], function (mymodule) {
        return {
            my: 'othermodule',
            dependency: mymodule
        };
    });

    test('createModule dependencies', function () {
        var myModule = Ink.getModule('My.Module_1');
        equal(myModule.my, 'module');

        var myOtherModule = Ink.getModule('My.Other.Module_1');
        equal(myOtherModule.my, 'othermodule');
        equal(myOtherModule.dependency.my, 'module');
    });

    test('requireModules', function () {
        Ink.requireModules(['My.Module_1'], function (module) {
            equal(module.my, 'module');
        });
    });

    test('requireModules + _moduleRenames', function () {
        sinon.stub(Ink, 'warn');
        Ink._moduleRenames['My.ModulesOldName_1'] = 'My.Module_1'
        Ink.requireModules(['My.ModulesOldName_1'], function (module) {
            equal(module.my, 'module');
            ok(Ink.warn.calledOnce)
        });
        Ink.warn.restore();
    })

    test('getting Ink.UI.Common when asking for Ink.UI.Aux should be enabled by default', function () {
        var clock = sinon.useFakeTimers();
        sinon.stub(Ink, 'warn');
        sinon.stub(Ink, 'loadScript');

        Ink.requireModules(['Ink.UI.Aux_1'], sinon.stub());
        clock.tick(1); // loadScript is not called immediately

        ok(Ink.loadScript.called)
        ok(Ink.loadScript.calledWithExactly('Ink.UI.Common_1'));

        Ink.loadScript.restore();
        Ink.warn.restore();
        clock.restore();
    });
}());

test('createModule makes the module available immediately when there are no dependencies', function () {
    Ink.createModule('Ink.New.Module', 1, [], function () {
        return {};
    });
    ok(Ink.getModule('Ink.New.Module_1'));
});

asyncTest('trying to load TestModuleWithDependencies/1/lib.js', function () {
    expect(3 /* here */ + 2 /* for each createModules */);
    Ink.setPath('TestModule', './TestModule'); // TestModuleWithDependencies's dependency
    Ink.setPath('TestModuleWithDependencies', './TestModuleWithDependencies');
    Ink.requireModules(['TestModuleWithDependencies_1'], function (TestModuleWithDependencies) {
        equal(TestModuleWithDependencies.hello, 'dependencies');
        ok(TestModuleWithDependencies.TestModule);
        equal(TestModuleWithDependencies.TestModule.hello, 'world');
        start();
        return {};
    });
});

asyncTest('Nested requireModules', function () {
    expect(2);// expecting all the callbacks to run

    Ink.createModule('Ink.nest1', 1, [], function () { return {}; });
    Ink.createModule('Ink.nest2', 1, [], function () { return {}; });

    Ink.requireModules(['Ink.nest1_1'], function () {
        ok(true, 'first callback run');
        Ink.requireModules(['Ink.nest2_1'], function () {
            ok(true, 'second callback run');
            start();
        });
    });
});

test('ACBDCD test: more than one cross-dependency', function () {
    expect(4);
    stop(4);
    Ink.createModule('CrossDepRegr_A', '1', ['CrossDepRegr_C_1'], function () {
      ok(true);
      start();
      return {};
    });
    Ink.createModule('CrossDepRegr_B', '1', ['CrossDepRegr_D_1'], function() {
      ok(true);
      start();
      return {};
    });
    Ink.createModule('CrossDepRegr_C', '1', [], function() {
      ok(true);
      start();
      return {};
    });
    Ink.createModule('CrossDepRegr_D', '1', [], function() {
      ok(true);
      start();
      return {};
    });
});

test('ACBDCD test: more than one cross-dependency (and require it later)', function () {
    Ink.createModule('CrossDepRegr2_A', '1', ['CrossDepRegr2_C_1'], function () {
      return { name: 'A' };
    });
    Ink.createModule('CrossDepRegr2_B', '1', ['CrossDepRegr2_D_1'], function() {
      return { name: 'B' };
    });
    Ink.createModule('CrossDepRegr2_C', '1', [], function() {
      return { name: 'C' };
    });
    Ink.createModule('CrossDepRegr2_D', '1', [], function() {
      return { name: 'D' };
    });

    expect(4);
    stop();
    Ink.requireModules(['CrossDepRegr2_A', 'CrossDepRegr2_B', 'CrossDepRegr2_C', 'CrossDepRegr2_D'], function (A, B, C, D) {
        equal(A.name, 'A');
        equal(B.name, 'B');
        equal(C.name, 'C');
        equal(D.name, 'D');
        start();
    });
});

asyncTest('requireModules can require a module which does not yet exist. The script tag is only made on the next tick.', function () {
    expect(2);
    Ink.requireModules(['Ink.notYet_1'], function (obj) {
        var scripts = document.getElementsByTagName('script');
        for (var i = 0, len = scripts.length; i < len; i++) {
            if (scripts[i].src === Ink.getPath('Ink.notYet_1')) {
                ok(false, 'Ink tried to load the module and did not wait a tick!');
                start();
                return;
            }
        }
        ok(true, 'Ink did not try to load the module before waiting a tick');
        start();
        return;
    });
    Ink.createModule('Ink.notYet', 1, [], function () {
        ok(true);
        return {};
    });
});

asyncTest('createModule also waits a tick for dependencies to be created', function () {
    expect(2);
    Ink.createModule('Ink.Test.Wait.Tick.For.Dependencies', 1, ['Ink.Not.Yet.Dependency_1'], function () {
        ok(true);
        start();
        return {};
    });
    Ink.createModule('Ink.Not.Yet.Dependency', 1, [], function () {
        ok(true);
        return {};
    });
});
asyncTest('(regression) createModule can work with a requireModule afterwards when it has dependencies', function () {
    expect(2);

    Ink.setPath('Ink', '.');

    Ink.createModule( 'Ink.UI.SelectFilter' , '1', ['Ink.SomeUnresolvedDependency_1'], function( Common , Selector , InkEvent ) {
        ok(true);
        return {};
    });

    Ink.requireModules( [ 'Ink.UI.SelectFilter_1' ] , function( SF ) {
        var scripts = document.getElementsByTagName('script');
        var _a = document.createElement('a');
        _a.href = Ink.getPath('Ink.UI.SelectFilter_1');
        for (var i = 0, len = scripts.length; i < len; i++) {
            if (scripts[i].src === _a.href) {
                ok(false, 'Ink tried to request the Ink.UI.SelectFilter_1 module using a script tag, even though it was already created but waiting for dependencies!');
                start();
                return;
            }
        }
        ok(true);
        start();
    });

    Ink.createModule( 'Ink.SomeUnresolvedDependency', 1, [], function () { return {}; });
});

test('(regression) requireModules will try to request things which are accidental undefined values', sinon.test(function () {
    this.stub(Ink, '_loadLater');
    var spy = this.spy();

    Ink.requireModules([undefined], spy);

    ok(Ink._loadLater.notCalled)
    ok(spy.calledOnce)
}));

module('Debug mechanisms');


// Fix ie7-9, where console.* functions have typeof 'object'
// Because of that we can't use a sinon stub, so we fake one.
function stubObject(obj, methodName) {
    var old = obj[methodName];
    obj[methodName] = function () {};
    obj[methodName].restore = function () {
        obj[methodName] = old;
    };
}

if (window.console && window.console.log) {
    test('console.* stubs/shortcuts', function () {
        var functions = ['log', 'warn', 'error'];
        var spy;

        for (var i = 0, len = functions.length; i < len; i++) {
            stubObject(window.console, functions[i]);

            spy = sinon.spy(window.console, functions[i]);
            Ink[functions[i]]('log!', 'log2!');
            deepEqual(spy.calledOnce && spy.lastCall.args, ['log!', 'log2!'],
                'console.' + functions[i] + ' called once with correct arguments');

            spy.restore();
            window.console[functions[i]].restore();
        }
    });
} else {
    // If window.console.log don't exist, trying to call Ink.log and Ink.warn, and asserting that nothing is raised will be our test.

    test('Ink.warn and Ink.error when no console object present', function () {
        expect(0);
        Ink.warn('hi!');
        Ink.log('hi!');
    });
}

}());

