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

test('deleteModule', function () {
    Ink.createModule('Ink.Util.Whatever', 1, [], function () {
        return 'whatever';
    });
    equal(Ink.getModule('Ink.Util.Whatever_1'), 'whatever');
    Ink.deleteModule('Ink.Util.Whatever_1');
    equal(Ink.getModule('Ink.Util.Whatever_1'), undefined);
});

test('getPath, setPath', function () {
    Ink.setPath('Ink', 'http://example.com/');
    equal(Ink.getPath('Ink'), 'http://example.com/');

    Ink.setPath('App', 'http://example.com/app/');
    equal(Ink.getPath('App'), 'http://example.com/app/');

    // cleanup
    Ink.setPath('Ink', undefined);
    Ink.setPath('App', undefined);
});

test('getPath, setPath', function () {
    Ink.setPath('Ink.Sub', 'http://example.com/sub/');
    equal(Ink.getPath('Ink.Sub'), 'http://example.com/sub/');
    equal(Ink.getPath('Ink.Sub.Sub_1'), 'http://example.com/sub/');

    Ink.setPath('Plug.Sub', 'http://example.com/subplug/');
    equal(Ink.getPath('Plug.Sub'), 'http://example.com/subplug/');
    equal(Ink.getPath('Plug.Sub.Sub'), 'http://example.com/subplug/');

    Ink.setPath('Ink.Sub.Sub', 'http://example.com/subsub/');
    equal(Ink.getPath('Ink.Sub'), 'http://example.com/sub/');
    equal(Ink.getPath('Ink.Sub.Sub_whoo'), 'http://example.com/subsub/');

    Ink.setPath('Plug.Sub.Sub', 'http://example.com/subsubplug/');
    equal(Ink.getPath('Plug.Sub'), 'http://example.com/subplug/');
    equal(Ink.getPath('Plug.Sub.Sub'), 'http://example.com/subsubplug/');
});

