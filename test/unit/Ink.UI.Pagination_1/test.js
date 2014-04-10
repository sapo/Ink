Ink.requireModules(['Ink.UI.Pagination_1', 'Ink.Dom.Element_1', 'Ink.Dom.Css_1', 'Ink.Dom.Event_1', 'Ink.Dom.Selector_1'], function (Pagination, InkElement, Css, InkEvent, Selector) {
    function makeContainer(options) {
        var container = InkElement.create('div', {
            className: 'ink-navigation ink-pagination ' + (options.className || ''),
            style: 'display: none',
            insertBottom: document.body
        });

        InkElement.create('ul', {
            className: 'pagination ' + (options.ulClassNames || ''),
            insertBottom: container
        });

        return container;
    }

    function testPagination(name, testBack, options) {
        test(name, function ()  {
            var container = makeContainer(options || {});
            var component = new Pagination(container, Ink.extendObj({
                size: 3
            }, options || {}));
            testBack(component, container);
        });
    }

    test('Creates an <ul> element automatically', function () {
        var container = InkElement.create('div');
        var pagination = new Pagination(container, {size: 3});
        equal(container.children.length, 1);
        equal(container.children[0].tagName.toLowerCase(), 'ul');
        ok(Css.hasClassName(container.children[0], 'pagination'));
    });

    testPagination('_calculateSize', function (component) {
        equal(component._calculateSize(5, 5), 1);
        equal(component._calculateSize(10, 5), 2);
    });

    test('_calculateSize called to calculate page count when itemsPerPage and totalItemCount options passed', function () {
        var container = InkElement.create('div');
        var spy = sinon.spy(Pagination.prototype, '_calculateSize');
        var pagination = new Pagination(container, {itemsPerPage: 5, totalItemCount: 64});
        ok(spy.calledOnce, '_calculateSize was called');
        deepEqual(spy.lastCall.args, [64, 5]);
        spy.restore();
    });

    testPagination('When Pagination has the "sideButtons" option set to false, no previous/next <li> elements are created', function (comp, container) {
        equal(Ink.ss('.previous,.next', container).length, 0, 'no previous nor next elements were created');
        equal(Ink.ss('li', container).length, 2, 'no previous nor next elements were created');
    }, { sideButtons: false, size: 2});

    testPagination('When Pagination has the "chevron" option, the "next" and "previous" <li>s have <span> tags inside their <a> tags', function (_, container) {
        ok(Ink.s('li.previous a span', container), '.previous is wrapped in a span');
        ok(Ink.s('li.next a span', container), '.next is wrapped too');
        ok(!Ink.s('li:not(.previous):not(.next) a span', container), 'sanity check: everything else is not.');
    }, { ulClassNames: ' chevron' });
});
