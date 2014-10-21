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

    testImageQuery('_onResize sets the image src to things by calling _findCurrentQuery', sinon.test(function (iq, img) {
        var findCurrentQuery = this.stub(iq, '_findCurrentQuery').returns({ label: 'lol', width: 100 });

        iq._onResize();

        equal(img.getAttribute('src'), '/images/lol.jpg', 'fetched /images/lol.jpg because it was given a query with label "lol"');

        findCurrentQuery.returns({ src: '/will-override.png', label: 'label', width: 100 });

        iq._onResize();

        equal(img.getAttribute('src'), '/will-override.png', 'query.src overrides query.label');
    }));

    testImageQuery('...and it calls "src" in options if it\'s a function', sinon.test(function (iq, img) {
        var src = this.stub().returns('/returned/from/function.jpg');
        var theQuery = { src: src };
        var findCurrentQuery = this.stub(iq, '_findCurrentQuery')
            .returns(theQuery);

        iq._onResize();

        ok(src.calledWith(img, theQuery));

        equal(img.getAttribute('src'), '/returned/from/function.jpg');
    }));
});
