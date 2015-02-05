Ink.requireModules(['Ink.UI.ImageQuery_1', 'Ink.UI.Common_1', 'Ink.Dom.Element_1', 'Ink.Dom.Event_1', 'Ink.Dom.Selector_1'], function (ImageQuery, Common, InkElement, InkEvent, Selector) {

    function makeImage() {
        return InkElement.create('img');
    }

    function testImageQuery(name, testBack, options) {
        test(name, function ()  {
            location.hash = '#no-hash';
            var img = makeImage();
            var iq = new ImageQuery(img, Ink.extendObj({
                src: '/images/{:label}.jpg',
                queries: [
                    {
                        label: '100px',
                        width: 100
                    },
                    {
                        label: '200px',
                        width: 200
                    },
                    {
                        label: '300px',
                        width: 300
                    }
                ]
            }, options || {}));
            testBack(iq, img);
        });
    }

    testImageQuery('_findCurrentQuery', sinon.test(function (iq) {
        var vw = this.stub(InkElement, 'viewportWidth');

        vw.returns(90);
        deepEqual(iq._findCurrentQuery(),
            { label: '100px', width: 100 },
            '90px viewport -> 100px image (first query)');

        vw.returns(200);
        deepEqual(iq._findCurrentQuery(),
            { label: '200px', width: 200 },
            '200px viewport -> 200px image');

        vw.returns(110);
        deepEqual(iq._findCurrentQuery(),
            { label: '100px', width: 100 },
            '110px viewport -> 100px image');

        vw.returns(201);
        deepEqual(iq._findCurrentQuery(),
            { label: '200px', width: 200 },
            '201px viewport -> 200px image');

        vw.returns(2001);
        deepEqual(iq._findCurrentQuery(),
            { label: '300px', width: 300 },
            '300px viewport -> 300px image (last query)');
    }));

    testImageQuery('getQuerySrc', function (iq, img) {
        iq.setOption('src', '/{:label}.jpg');
        equal(
            iq.getQuerySrc({
                label: '200px',
                width: 200
            }),
            '/200px.jpg',
            'Simple template query');
        equal(
            iq.getQuerySrc({
                label: '200px',
                src: '/alt/{:label}.jpg',
                width: 200
            }),
            '/alt/200px.jpg',
            'Overriding the global src set in the options');
        equal(
            iq.getQuerySrc({ src: sinon.stub().returns('/returned/from/function.jpg')}),
            '/returned/from/function.jpg',
            'when src: is a function');
    });

    testImageQuery('_onResize chooses and then sets the image\'s src attribute by calling _findCurrentQuery and getQuerySrc', sinon.test(function (iq, img) {
        var dummyQuery = dummyQuery;
        var getQuerySrc = this.stub(iq, 'getQuerySrc').returns('/from/getquerysrc.jpg');
        var findCurrentQuery = this.stub(iq, '_findCurrentQuery').returns(dummyQuery);

        iq._onResize();

        ok(findCurrentQuery.calledOnce);
        ok(getQuerySrc.calledOnce);
        ok(getQuerySrc.calledWith(dummyQuery));

        ok((new RegExp('/from/getquerysrc.jpg$')).test(img.getAttribute('src')),
            'image gets src returned from getQuerySrc');
    }));
});
