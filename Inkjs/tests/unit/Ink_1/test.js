/*globals equal,test,asyncTest,stop,start,ok,expect*/
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
test('staticMode', function () {
    Ink.setStaticMode(true);
    throws(function () {
        Ink.requireModules(['Ink.Dom.Element_1'], function () {});
    }, /[sS]tatic( mode)?/);
    Ink.setStaticMode(false);
});

/*
asyncTest('createExt', function () {
    expect(1);  // only one assertion
    
    Ink.createExt('Lol.Parser', 1, [], function () {
        debugger
        return {
            parse: function () {}
        };
    });

    setTimeout(function() {
        Ink.requireModules(['Ink.Ext.Lol.Parser'], function (Parser) {
            equal(typeof Parser.parse, 'function', 'checking module');
            start();  // async done
        });
    }, 100);
});
*/

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
});


asyncTest('loadScript', function () {
    window.loadScriptWorks = function (sayYeah) {
        equal(sayYeah, 'yeah');
        start();
    }
    Ink.loadScript('./loadscript-test.js');  // This script calls window.loadScriptWorks('yeah')
});

(function () {
    Ink.createModule('My.Module', 1, [], function () {
        return {
            my: 'module'
        };
    });

    Ink.createModule('Ink.My.Module', 1, [], function () {
        return {
            my: 'ink-module'
        };
    });

    Ink.createModule('My.Other.Module', 1, ['My.Module_1'], function (mymodule) {
        return {
            my: 'othermodule',
            dependency: mymodule
        };
    });

    asyncTest('createModule dependencies', function () {
        var myModule = Ink.getModule('My.Module_1');
        equal(myModule.my, 'module');

        var inkMyModule = Ink.getModule('Ink.My.Module_1');
        equal(inkMyModule.my, 'ink-module');

        var myOtherModule = Ink.getModule('My.Other.Module_1');
        equal(myOtherModule.my, 'othermodule');
        equal(myOtherModule.dependency.my, 'module');
        start();
    });

    asyncTest('global variables', function () {
        ok(Ink.My.Module);
        ok(Ink.My.Module_1);
        equal(Ink.My.Module.my, 'ink-module');
        equal(Ink.My.Module_1.my, 'ink-module');
        start();
    });

    asyncTest('requireModules', function () {
        Ink.requireModules(['My.Module_1'], function (module) {
            equal(module.my, 'module');
            start();
        });
    });
}());

asyncTest('createModule waits a tick before creating the module', function () {
    Ink.createModule('Ink.foo', '1', [], function () {
        ok(true, 'module created');
        start();
        return {}; 
    }); 
    equal(Ink.foo_1, undefined, 'Ink.foo will not exist until next tick');
});

asyncTest('requireModules waits a tick before requiring the module', function () {
    var required = 'not yet'
    Ink.requireModules(['Ink.foo_1'], function () {
        required = true;
        ok(true, 'module required');
        start();
    }); 
    equal(required, 'not yet', 'requireModules callback function not called yet');
});

asyncTest('Nested requireModules', function () {
    expect(2);// expecting all the callbacks to run
    Ink.requireModules(['Ink.nest1_1'], function () {
        ok(true, 'first callback run');
        Ink.requireModules(['Ink.nest2_1'], function () {
            ok(true, 'second callback run');
            start();
        });
    });
    Ink.createModule('Ink.nest1', 1, [], function () { return {} });
    Ink.createModule('Ink.nest2', 1, [], function () { return {} });
});

asyncTest('pinkySwear integration', function () {
    var promise = Ink.promise();
    setTimeout(function () {
        promise(true, ['its okay']);
    }, 100);
    promise
        .then(function (okay) {
            equal(okay, 'its okay');
            start();
        })
});

asyncTest('promise errors', function () {
    var promiseForBetterTimes = Ink.promise();
    setTimeout(function () {
        promiseForBetterTimes(false, ['better times failed to come']);
    }, 100);
    promiseForBetterTimes
        .then(function (value) {
            ok(false, 'this should never happen');
            start();
        })
        .error(function (error) {
            equal(error, 'better times failed to come');
            start();
        })
});

asyncTest('promise.all', function () {
    var promise = Ink.promise();
    
    var dependency1 = Ink.promise();
    var dependency2 = Ink.promise();

    promise.all([dependency1, dependency2]).then(function (results) {
        deepEqual(results, ['result 1', 'result 2']);
        start();
    });

    setTimeout(function () {
        dependency1(true, ['result 1']);
    });
    setTimeout(function () {
        dependency2(true, ['result 2']);
    });
});

asyncTest('promise.all, no dependencies', function () {
    var promise = Ink.promise();
    
    promise.all([]).then(function (results) {
        deepEqual(results, []);
        start();
    });
});

asyncTest('requireModules should wait at least a tick until a module is created', function () {
    expect(0);  // this test just tries to cause a 404 error.
    Ink.requireModules(['Ink.notYet_1'], function () {
        start();
    });
    Ink.createModule('Ink.notYet', 1, [], function () {
        return {};
    });
});

asyncTest('trying to load TestModule/1/lib.js', function () {
    expect(1);
    Ink.setPath('TestModule', './TestModule')
    Ink.requireModules(['TestModule_1'], function (TestModule) {
        equal(TestModule.hello, 'world');
        start();
    });
});

asyncTest('trying to load TestModuleWithDependencies/1/lib.js', function () {
    expect(2);
    Ink.setPath('TestModule', './TestModule'); // TestModuleWithDependencies's dependency
    Ink.setPath('TestModuleWithDependencies', './TestModuleWithDependencies')
    Ink.requireModules(['TestModuleWithDependencies_1'], function (TestModule) {
        equal(TestModule.hello, 'dependencies');
        ok(TestModule.TestModule);
        start();
    });
});

// TEMPORARY TESTS:
asyncTest('requireModules, no dependencies', function () {
    Ink.requireModules([], function () {
        ok(true);
        start();
    });
});

asyncTest('createModule, no dependencies', function () {
    Ink.createModule('Ink.Foo.Bar.Baz.NoDependencies', 1, [], function () {
        ok(true);
        start();
    });
});


