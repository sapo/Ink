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

test('createExt', function () {
    stop();  // async
    expect(1);  // only one assertion
    
    Ink.createExt('Lol.Parser', 1, [], function () {
        return {
            parse: function () {}
        };
    });

    Ink.requireModules(['Ink.Ext.Lol.Parser'], function (Parser) {
        equal(typeof Parser.parse, 'function');
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
}());

