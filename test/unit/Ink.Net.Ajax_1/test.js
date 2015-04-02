QUnit.config.testTimeout = 4000;
Ink.requireModules(['Ink.Net.Ajax_1'], function (Ajax) {
    var statics = Ajax.prototype;

    module('helper functions');

    test('_locationIsHTTP', function () {
        equal(statics._locationIsHTTP(
            statics._locationFromURL('http://www.example.com')),
            true);

        equal(statics._locationIsHTTP(
            statics._locationFromURL('https://www.example.com')),
            true);

        equal(statics._locationIsHTTP(
            statics._locationFromURL('ssh://example.com')),
            false);
    });

    test('_locationIsCrossDomain', function () {
        /* same domain */
        equal(statics._locationIsCrossDomain(
            statics._locationFromURL('http://www.sapo.pt/'),
            statics._locationFromURL('http://www.sapo.pt/')), false);

        /* different subdomain */
        equal(statics._locationIsCrossDomain(
            statics._locationFromURL('http://www.sapo.pt/'),
            statics._locationFromURL('http://sub.sapo.pt/')), true);

        /* same port */
        equal(statics._locationIsCrossDomain(
            statics._locationFromURL('http://www.sapo.pt/'),
            statics._locationFromURL('http://www.sapo.pt:80/')), false);

        /* different port */
        equal(statics._locationIsCrossDomain(
            statics._locationFromURL('http://www.sapo.pt:80/'),
            statics._locationFromURL('http://www.sapo.pt:81/')), false);

        equal(statics._locationIsCrossDomain(
            statics._locationFromURL('foo.jpg'),
            statics._locationFromURL('/my/page.html')), false);

        /* A relative URL on this site */
        equal(statics._locationIsCrossDomain(
            statics._locationFromURL('/my/page.html')), false);

        /* A URL pointing to this site, starting with // */
        equal(statics._locationIsCrossDomain(
            statics._locationFromURL('//' + location.host + '/my/page.html')), false);
    });

    test('Url parameters', function () {
        var paramObjToStr = Ajax.prototype.paramsObjToStr;
        equal(paramObjToStr({a: 3, b: 6}), 'a=3&b=6');
        equal(paramObjToStr({a: [1,2,3]}), 'a[]=1&a[]=2&a[]=3');
    });

    module('local files (run from server only)');

    test('request test.html', function () {
        expect(3);
        stop();
        new Ajax('test.html', {
            method: 'get',
            onSuccess: function (ajx, responseText) {
                equal(ajx.status, 200);
                ok(/this is a random string in the response html/i.test(responseText));
                ok(/this is a random string in the response html/i.test(ajx.responseText));
                start();
            },
            onFailure: function () {
                ok(false, 'failure callback called');
                start();
            }
        });
    });

    test('request test.json', function () {
        stop();
        new Ajax('test.json', {
            method: 'get',
            onSuccess: function (ajx, responseJSON) {
                // Do stuff with response
                equal(responseJSON.responded_okay, true);  // See test.json
                start();
            },
            onFailure: function () {
                ok(false);
                start();
            }
        });
    });

    test('abort a request', function () {
        var ajx = new Ajax('test.json', {
            method: 'get',
            onSuccess: function (ajx, responseJSON) {
                // Do stuff with response
                ok(false);
            },
            onFailure: function () {
                ok(false);
            }
        });

        ajx.abort();

        ok(true);
    });

    test('request does-not-exist.json', function () {
        stop();
        new Ajax('does-not-exist.json', {
            method: 'get',
            onSuccess: function () {
                ok(false, 'should not call success callback');
                start();
            },
            onFailure: function (ajx, response) {
                ok(ajx.status === 404 || ajx.status === 500);
                start();
            }
        });
    });

    test('Attempt to load something cross-domain from where we are results in a call to Ink.warn with a helpful message, and raises an error', function () {
        sinon.stub(Ink, 'error');

        new Ajax('http://cross-domain-from-here.com/lel.json');

        equal(Ink.error.calledOnce, true, 'Ink.warn called once');
        var isHelpful = ok;
        isHelpful(Ink.error.lastCall.args[0]);
        Ink.error.restore();
    });


    test('isJSON', function () {
        var isJSON = Ajax.prototype.isJSON;
        ok(isJSON('{"hello": "world"}'));
        ok(!isJSON('{hi: "world"}'));
        ok(!isJSON('alert("hi!")'));
    });

    test('Ajax.load helper function', function () {
        stop();
        stop();

        Ajax.load('test.json', function (responseJSON) {
            strictEqual(responseJSON.responded_okay, true);
            start();
        });

        Ajax.load('test.html', function (responseText) {
            ok(/this is a random string in the response html/i.test(responseText));
            start();
        });
    });

    test('validateCors', sinon.test(function () {
        sinon.stub(Ink, 'error');
        var ajx = new Ajax('http://example.com/', {
            validateCors: false
        });
        equal(ajx.options.cors, false, 'ajx.options.cors not set because we did not set validateCors');

        var ajx = new Ajax('http://example.com/', {
            validateCors: true
        });
        equal(ajx.options.cors, true, 'ajx.options.cors set to true because validateCors set to true');

        var ajx = new Ajax(('' + window.location), {
            validateCors: true
        });
        equal(ajx.options.cors, false, 'ajx.options.cors set to false because it\'s a same-domain request');

        ok(Ink.error.calledOnce, 'Only one warning was generated');
        Ink.error.restore();
    }))
});

