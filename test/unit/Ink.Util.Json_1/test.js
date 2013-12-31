/*globals equal,test,deepEqual,hugeObject,ok,module*/
Ink.requireModules(['Ink.Util.Json'], function (Json) {
    'use strict';

    // We already know that the browser has a decent implementation of JSON.
    Json._nativeJSON = null;
    var nativeJSON = window.nativeJSON;
    var crockfordJSON = window.JSON;
    
    // shortcut for Json.stringify
    var s = function (input) {return Json.stringify(input, false);};

    function JSONEqual(a, b, msg) {
        if (typeof b === 'undefined') {
            b = a;
            a = s(a);
        }
        if (typeof msg === 'undefined') {
            msg = a;
        }
        try {
            deepEqual(eval('(' + a + ')'), b, msg);
        } catch(e) {
            ok(false, 'SyntaxError: \'(' + a + ')\' caused: ' + e + '.');
        }
    }

    module('Json.stringify');

    test('Stringify primitive values', function () {
        equal(s(''), '""');
        equal(s('a'), '"a"');
        equal(s('á'), '"á"');
        deepEqual(s(1), '1');
        equal(s(true), 'true');
        equal(s(false), 'false');
        equal(s(null), 'null');
        equal(s(NaN), 'null');
        equal(s(Infinity), 'null');
        equal(s(-Infinity), 'null');
    });

    test('Escaping', function () {
        equal(s('"'), '"\\""');
        equal(s('""'), '"\\"\\""');
        equal(s('\\'), '"\\\\"');
        equal(s('\\\\'), '"\\\\\\\\"');
        equal(s('\n'), '"\\n"');

        equal(s(['"']), '["\\""]');
        equal(s({a:'""'}), '{"a": "\\"\\""}');
        equal(s(['\\']), '["\\\\"]');
        equal(s({a:'\\\\'}), '{"a": "\\\\\\\\"}');
        equal(s(['\n']), '["\\n"]');
        equal(s({'\n':0}), '{"\\n": 0}');
    });

    test('Serialize objects', function () {
        JSONEqual(s({a: 'c'}), {"a": "c"});
        JSONEqual(s({a: 'a'}), {"a": "a"});
        JSONEqual(s({d: 123, e: false, f: null, g: []}),
            {"d": 123,"e": false,"f": null,"g": []});
        JSONEqual(s({1: 2}), {1: 2});
    });

    test('Serialize arrays', function () {
        JSONEqual(s([1, false, 1, 'CTHULHU']),
            [1,false,1,"CTHULHU"]);
        JSONEqual(s([undefined, 1, {}]),
            [null, 1, {}]);
    });

    test('Nesting!', function () {
        var nested = [
            {
                cthulhu: ['fthagn']
            },
            "r'lyeh",
            123
        ];
        JSONEqual(s(nested), nested);
    });

    test('Stringify large objects', function () {
        // hugeObject.js
        serialize(s, hugeObject, 'our JSON stuffs');
        serialize(nativeJSON.stringify, hugeObject, 'native JSON stuffs');
        serialize(crockfordJSON.stringify, hugeObject, 'crockford\'s JSON stuffs');
    });

    function serialize(func, obj, name) {
        var start = new Date();
        var serialized = func(obj);
        ok(true, (new Date() - start) + 'ms with ' + name);
        
        var chk = eval('('+serialized+')');
        equal(nativeJSON.stringify(chk), nativeJSON.stringify(obj), name);
    }

    test('using toJSON', function () {
        var i = 0;
        var tojson = function () {return (i++).toString();};
        var numb = new Number(123),
            str = new String('a'),
            obj = {};
        numb.toJSON = str.toJSON = obj.toJSON = tojson;
        equal(s(numb), '"0"');
        equal(s(str), '"1"');
        equal(s(obj), '"2"');
    });

    test('Escape values returned from toJSON', function () {
        var obj = {
            toJSON: function () {
                return 'a quote: ". a backslash: \\';
            }
        };
        equal(s(obj), '"a quote: \\". a backslash: \\\\"');
    });

    test('Internal _escape method for adding slashes to stuff', function () {
        equal(Json._escape('\\'), '\\\\');
        equal(Json._escape('\n'), '\\n');
        equal(Json._escape('\t'), '\\t');
        equal(Json._escape('\\"\\'), '\\\\\\"\\\\');
    });

    test('Functions can\'t be stringified, to match the native JSON API', function () {
        deepEqual(s(function () {}), "null");
        deepEqual(s(new Function()), "null");
        var f = function(){"...";};
        f.toJSON = Ink.bind(f.toString, f);
        deepEqual(s(f), '"' + Json._escape(f.toString()) + '"');
    });
    
    module('Json.parse');

    test('Doug Crockford\'s JSON.parse', function () {
        function check(json, shouldSucceed) {
            var parsed,
                evalled;
            try {
                parsed = Json.parse(json);
            } catch (e) {
                if (shouldSucceed !== false) {
                    ok(false, 'failed to parse ' + json);
                }
            }
            try {
                evalled = eval('(' + json + ')');
            } catch (e) {
                if (shouldSucceed !== false) {
                    ok(false, 'failed to eval ' + json);
                }
            }
            if (parsed && evalled) {
                deepEqual(parsed, evalled);
            }
        }
        check('""');
        check('true');
        check('false');
        check('null');

        check('[function(){}]', false);

        check('123123');
        check('-123123');
        check('1.23123');
        check('-1.23123');

        check('0x1234', false);

        check('{}');
        check('[]');

        check('[1, 2, 3, 4]');

        check('["ur mom\\""]');
        check('["ur mom\\"]', false);

        check('["ur mom","ur backslash\\n ","\\n", "ur qu\\"ote"]');

        check('"\\n"');
        check('"\\u0000"');
        check('"\\a"', false);

        check(" { \"asd\":\"basd\"  }  ");
        check('{asd:"basd"}', false);
        check('{"asd":\'basd\'}', false);
    });

    module('[general]');

    test('dates', function () {
        var aDate = new Date();
        aDate.setUTCFullYear(2013);
        aDate.setUTCMonth(7 - 1);
        aDate.setUTCDate(1);
        aDate.setUTCMinutes(10);
        aDate.setUTCHours(10);
        aDate.setUTCSeconds(5);
        ok(/"2013-07-01T10:10:05(\.\d+)?Z"/.test(s(aDate)));
    });
});
