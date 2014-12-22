Ink.requireModules(['Ink.Net.JsonP_1', 'Ink.Dom.Selector_1'], function (JsonP, Selector) {
    function mock(jsonp) {
        // We don't have a JSONP endpoint for this test.
        // So we put a callback on the window and the JSONP file calls that.
        window.mockJsonPCallback = function (response) {
            var callback = Ink.Net.JsonP[jsonp.options.internalCallback];
            callback(response);
            window.mockJsonPCallback = undefined;
        };
        return jsonp;
    }
    test('basic usage', function () {
        stop();
        expect(1);
        var jsonp = new JsonP('test_jsonp.js', {
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
            timeout: 1 // default is 10 and we don't wanna wait
        });
        mock(jsonp);
    });
    test('JsonP cleans up its script tag', function () {
        expect(2);
        var jsonp = new JsonP('test_jsonp.js', {
            onSuccess: function () {
                // Because the script is not deleted yet
                setTimeout(function () {
                    equal(Selector.select('script[src="'+jsonp.uri+'"]').length, 0);
                    start();
                }, 0);
            }
        });
        mock(jsonp);
        equal(Selector.select('script[src="'+jsonp.uri+'"]').length, 1);
        stop();
    });
    test('abort() method aborts the request. It doesn\'t call anything.', function () {
        expect(2);
        var onSuccess = sinon.spy();
        var onFailure = sinon.spy();

        var clock = sinon.useFakeTimers(+new Date());

        var jsonp = new JsonP('./somethign.js', {
            onSuccess: onSuccess,
            onFailure: onFailure,
            timeout: 1
        });

        jsonp.abort();  // Abort before timeout ends

        var callback = Ink.Net.JsonP[jsonp.options.internalCallback]
        callback({ some: 'data' });  // Call the JsonP callback, which would trigger onSuccess

        clock.tick(2000);  // Wait 2 seconds, which would trigger onFailure because the timeout ended

        ok(onSuccess.notCalled, 'onSuccess not called');
        ok(onFailure.notCalled, 'onFailure not called');

        clock.restore();
    });
});
