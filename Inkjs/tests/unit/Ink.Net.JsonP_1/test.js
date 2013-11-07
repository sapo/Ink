Ink.requireModules(['Ink.Net.JsonP_1', 'Ink.Dom.Selector_1'], function (JsonP, Selector) {
    function mock(jsonp) {
        // We don't have a JSONP endpoint for this test.
        // So we put a callback on the window and the JSONP file calls that.
        window.mockJsonPCallback = function (response) {
            var callback = Ink.Net.JsonP[jsonp.options.internalCallback];
            callback(response);
            delete window.mockJsonPCallback;
        };
        return jsonp;
    }
    test('basic usage', function () {
        stop();
        expect(1);
        var jsonp = new JsonP('test.jsonp', {
            onSuccess: function (object) {
                equal(object.foo, 'bar');
                start();
            }
        });
        mock(jsonp);
    });
    test('404', function () {
        stop();
        expect(1);
        var jsonp = new JsonP('unknown jsonp file', {
            onSuccess: function () {
                ok(false, 'should have failed because there is no JSONp file');
                start();
            },
            onFailure: function () {
                ok(true, 'should call onFailure');
                start();
            },
            timeout: 0.1 // default is 10 and we don't wanna wait
        });
        mock(jsonp);
    });
    test('JsonP cleans up its script tag', function () {
        expect(2);
        var jsonp = new JsonP('test.jsonp', {
            onSuccess: function () {
                equal(Selector.select('script[src="'+jsonp.uri+'"]').length, 0);
                start();
            }
        });
        mock(jsonp);
        equal(Selector.select('script[src="'+jsonp.uri+'"]').length, 1);
        stop();
    });
});
