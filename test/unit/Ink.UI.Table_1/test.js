Ink.requireModules(['Ink.UI.Table_1', 'Ink.UI.Common_1', 'Ink.Dom.Element_1', 'Ink.Util.Array_1', 'Ink.Dom.Selector_1', 'Ink.UI.Pagination_1'], function (Table, Common, InkElement, InkArray, Selector, Pagination) {
    function makeContainer() {
        return InkElement.create('div', {
            // style: 'display: none',
            setHTML: [
                '<table class="ink-table">',
                    '<thead>',
                        '<tr>',
                            '<th id="col1" data-sortable="true">col1</th>',
                            '<th id="col2" data-sortable="true">col2</th>',
                        '</tr>',
                    '</thead>',
                    '<tbody>',
                        '<tr>',
                            '<td id="col1-1">1</td>',
                            '<td id="col2-1">1</td>',
                        '</tr><tr>',
                            '<td id="col1-2">2</td>',
                            '<td id="col2-2">2</td>',
                        '</tr><tr>',
                            '<td id="col1-3">3</td>',
                            '<td id="col2-3">3</td>',
                        '</tr><tr>',
                            '<td id="col1-4">4</td>',
                            '<td id="col2-4">4</td>',
                        '</tr>',
                    '</tbody>',
                '</table>'
            ].join(''),
            insertBottom: document.body
        });
    }

    function testTable(name, testBack, options) {
        test(name, function ()  {
            var container = makeContainer();
            var table = Ink.s('table', container);
            var component = new Table(table, options || {});
            testBack(component, table, container);
        });
    }

    function itemsInColumn(tableEl, col) {
        return InkArray.map(Ink.ss('tbody tr', tableEl), function (tr) {
            var s = Ink.ss('td', tr)[col - 1].innerHTML;
            return isNaN(+s) ? s : +s;
        });
    }

    testTable('_paginate() changes page by hiding/showing elements', function (table, tableEl) {
        // Pagination works by hiding elements not visible in the current page
        equal(Ink.ss('tr.hide-all', tableEl).length, 1);
        table._paginate(2);
        equal(Ink.ss('tr.hide-all', tableEl).length, 3);
    }, { pageSize: 3 });

    testTable('Setting the page causes the _paginate function to be called', sinon.test(function (table, tableEl, container) {
        this.stub(table, '_paginate');
        var pag = Pagination.getInstance(Ink.s('nav', container));
        pag.setCurrent(1);  // 0-based
        ok(table._paginate.calledOnce);
        equal(table._paginate.lastCall.args[0], 2, 'table._paginate was called with the new page number, 1-based.');
    }), { pageSize: 3 });

    testTable('sorting', function (table, tableEl) {
        for (var line = 1; line <= 4; line++)
            Ink.s('#col2-' + line, tableEl).innerHTML = 5 - line;

        deepEqual(
            itemsInColumn(tableEl, 1),
            [1, 2, 3, 4],
            'initial order is just as in the DOM');
        deepEqual(
            itemsInColumn(tableEl, 2),
            [4, 3, 2, 1],
            'initial order is just as in the DOM');

        var headers = Ink.ss('thead th', tableEl);
        var header1 = headers[0];
        var header2 = headers[1];

        stop();

        // TODO refactor so as to add a sort() method, so we don't have to fake events
        Syn.click(header1, function () {
            deepEqual(
                itemsInColumn(tableEl, 1),
                [1, 2, 3, 4],
                'numbers in column 1 are sorted');
            deepEqual(
                itemsInColumn(tableEl, 2),
                [4, 3, 2, 1],
                'numbers in column 2 are reversed');
            Syn.click(header2, function () {
                deepEqual(
                    itemsInColumn(tableEl, 2),
                    [1, 2, 3, 4],
                    'numbers in column 2 are sorted');
                deepEqual(
                    itemsInColumn(tableEl, 1),
                    [4, 3, 2, 1],
                    'numbers in column 1 are reversed');
                start();
            });
        });
    });

    testTable('sorting letters + numbers', function (table, tableEl) {
        var header1 = Ink.ss('thead th', tableEl)[0];

        Ink.s('#col1-1', tableEl).innerHTML = 'a';
        Ink.s('#col1-2', tableEl).innerHTML = 'b';

        stop();

        Syn.click(header1, function () {
            deepEqual(
                itemsInColumn(tableEl, 1),
                ['a', 'b', 3, 4],
                'numbers in column 1 are sorted, numbers go last');

            Syn.click(header1, function () {
                deepEqual(
                    itemsInColumn(tableEl, 1),
                    [4, 3, 'b', 'a'],
                    'numbers in column 1 are reversed, numbers go first');
                start();
            });
        });
    });
});

