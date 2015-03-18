
Ink.requireModules(['Ink.UI.LazyLoad_1', 'Ink.Dom.Element_1', 'Ink.Dom.Css_1', 'Ink.Dom.Event_1', 'Ink.Dom.Selector_1'], function (LazyLoad, InkElement, Css, InkEvent, Selector) {
    'use strict';

    function makeContainer(options) {
        var container = InkElement.create('div', {
            className: 'container ' + (options.className || ''),
            style: 'display: none',
            insertBottom: document.body
        });

        return container;
    }

    function testLazyLoad(name, testBack, options) {
        options = Ink.extendObj({ disableThrottle: true, autoInit: false }, options || {});
        test(name, function () {
            var sandbox = sinon.sandbox.create();
            var container = makeContainer(options);
            for (var i = 0; i < (options.childCount || 3); i++) {
                container.appendChild(InkElement.create('div', {
                    className: 'lazyload-item test-div test-div-' + i,
                    "data-src": 'data-src-' + i
                }));
            }
            if (options.before) { options.before.call(sandbox, container) }
            if (options.disableThrottle) { sandbox.stub(InkEvent, 'throttle', function (f) {return f}) }
            if (options.stubInViewport) { sandbox.stub(InkElement, 'inViewport').returns(options.stubInViewport.ret || false); }
            var component = new LazyLoad(container, Ink.extendObj({
            }, options || {}));
            try {
                testBack.call(sandbox, component, container, container.children);
                sandbox.restore()
            } catch(e) {
                sandbox.restore();
                throw e;
            }
        });
    }

    testLazyLoad('When LazyLoad is initted, LazyLoad_1.elInViewport is called when element is deemed to be in viewport.', function(ll, cont) {
        this.spy(ll, '_elInViewport');
        InkElement.inViewport.withArgs(Ink.s('.test-div-0', cont)).returns(true);
        ll.reload();
        equal(ll._elInViewport.callCount, 1, 'One of the elements is in the viewport, so it was called only once');
    }, { stubInViewport: { ret: false } });

    testLazyLoad('When an element is in the viewport, its [data-src] goes to [src]', function (ll, cont) {
        var div2 = Ink.s('.test-div-2', cont);
        var src = div2.getAttribute('data-src');
        InkElement.inViewport.withArgs(div2).returns(true);
        ll.reload();
        equal(div2.getAttribute('src'), src);
    }, { stubInViewport: { ret: false } });

    testLazyLoad('When LazyLoad takes a `placeholder` option, it gets added automatically to the [src] of all elements', function (ll, cont) {
        var ll = Ink.ss('.lazyload-item', cont);
        ok(/\/my-image.jpg$/.test(ll[0].getAttribute('src')), 'All images got my-image.jpg (the placeholder) as their src. This one got: ' + ll[0].getAttribute('src'))
        ok(/\/my-image.jpg$/.test(ll[1].getAttribute('src')), 'All images got my-image.jpg (the placeholder) as their src. This one got: ' + ll[1].getAttribute('src'))
        ok(/\/my-image.jpg$/.test(ll[2].getAttribute('src')), 'All images got my-image.jpg (the placeholder) as their src. This one got: ' + ll[2].getAttribute('src'))
    }, { autoInit: true, placeholder: './my-image.jpg', stubInViewport: { ret: false } });

    testLazyLoad('inViewport is called when the scroll event happens', function (ll, cont) {
        ll._onScroll();
        ok(InkElement.inViewport.called, 'InkElement.inViewport called when a scroll happens');
    }, { autoInit: true, childCount: 1, stubInViewport: { ret: false }  });

    var scrollElement = InkElement.create('div');
    document.body.appendChild(scrollElement)
    testLazyLoad('inViewport is called when the scroll event happens on the scrollElement', function (ll, cont) {
        ll._onScroll();
        ok(InkElement.inViewport.called, 'InkElement.inViewport called when a scroll happens')
    }, { autoInit: true, childCount: 1, scrollElement: scrollElement, stubInViewport: { ret: false }  });

    testLazyLoad("loadedClass", sinon.test(function (ll, cont) {
        this.stub(Css, 'addClassName');
        ll._elInViewport({ elm: Ink.s('.test-div-0', cont), original: '' })
        ok(Css.addClassName.called)
    }), { loadedClass: 'im-loaded' });
});
