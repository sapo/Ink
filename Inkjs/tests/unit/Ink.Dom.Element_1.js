
function equalHTML(html1, html2, message) {
    equal(
        html1.toLowerCase().replace(/\s+/g, ''),
        html2.toLowerCase().replace(/\s+/g, ''),
        message)
}

Ink.requireModules(['Ink.Dom.Element_1', 'Ink.Dom.Selector_1'], function (InkElement, Selector) {
    /**
     *Ink.Dom.Element.create('a',{href:'http://www.sapo.pt',innerHTML:'click',className:'cenas'})

    // Expected output:
    // <a href='http://www.sapo.pt' class='cenas'>click</a>

    // Current Output:
    // <a href="http://www.sapo.pt" innerhtml="click" class="undefined"></a>
    //
    */

    test('{append,prepend}HTML', function () {
        var rootElm = InkElement.create('div');
        rootElm.innerHTML = 'some-text';
        InkElement.prependHTML(rootElm, '<span>before</span><b>the</b>')
        InkElement.appendHTML(rootElm, '<span>after</span><i>it</i>')
        equalHTML(rootElm.innerHTML, '<span>before</span><b>the</b>some-text<span>after</span><i>it</i>');
    });
    test('appendHTML appending text nodes', function () {
        var elem = InkElement.create('a');
        InkElement.appendHTML(elem, 'text');
        equalHTML(elem.innerHTML, 'text');
    });
    test('prependHTML prepending text nodes', function () {
        var elem = InkElement.create('a');
        InkElement.prependHTML(elem, 'text');
        equalHTML(elem.innerHTML, 'text');
    });
    (function () {
        var table,
            tbody,
            tr,
            td;

        function init() {
            table = InkElement.create('table');
            tbody = InkElement.create('tbody');
            tr = InkElement.create('tr');
            td = InkElement.create('td');

            tr.appendChild(td);
            tbody.appendChild(tr);
            table.appendChild(tbody);
        }

        test('prepending tbodies to tables', function () {
            var table = InkElement.create('table');
            table = document.body.appendChild(table)
            InkElement.prependHTML(table, '<tbody></tbody>');
            equalHTML(table.innerHTML, '<tbody></tbody>');
        })

        test('prepending theads to tables', function () {
            init();
            InkElement.prependHTML(table, '<thead></thead>');
            equalHTML(table.innerHTML, '<thead></thead><tbody><tr><td></td></tr></tbody>');
        });
        test('prepending trs to tbodies', function () {
            init();
            InkElement.prependHTML(tbody, '<tr><td>td1</td></tr>')
            equalHTML(tbody.innerHTML, '<tr><td>td1</td></tr><tr><td></td></tr>');
        });
        test('prepending tds to trs', function () {
            init();
            InkElement.prependHTML(tr, '<td>td1</td>')
            equalHTML(tr.innerHTML, '<td>td1</td><td></td>');
        });

        test('appending tbodies to tables', function () {
            var table = InkElement.create('table');
            equal(table.children.length, 0);
            equalHTML(table.innerHTML, '');
            InkElement.appendHTML(table, '<tbody>');
            equal(table.children.length, 1);
            equalHTML(table.innerHTML, '<tbody></tbody>');
        });
        test('appending theads to tables', function () {
            var table = InkElement.create('table');
            InkElement.appendHTML(table, '<thead></thead>');
            equalHTML(table.innerHTML, '<thead></thead>');
        });
        test('appending trs to tbodies', function () {
            init();
            InkElement.appendHTML(tbody, '<tr><td>td1</td></tr>')
            equalHTML(tbody.innerHTML, '<tr><td></td></tr><tr><td>td1</td></tr>');
        });
        test('appending tds to trs', function () {
            init();
            InkElement.appendHTML(tr, '<td>td1</td>')
            equalHTML(tr.innerHTML, '<td></td><td>td1</td>');
        });
    }());
    /*test('prependHTML on tables', function () {
        var table = InkElement.create('table');
        var tbody = InkElement.create('tbody');
        var tr = InkElement.create('tr');
        var td = InkElement.create('td');

        tr.appendChild(td);
        tbody.appendChild(tr);
        table.appendChild(tbody);

        InkElement.prependHTML(tbody, '<tr><td id="td1">td1</td></tr>');
        equalHTML(Ink.s('#td1', table).innerHTML, 'td1');

        InkElement.prependHTML(tr, '<td id="td2">td2</td>');
        equalHTML(Ink.s('#td2', table).innerHTML, 'td2');

        InkElement.prependHTML(tr, '<td id="td3">td3</td><td id="td3-b">td3-b</td>');
        equalHTML(Ink.s('#td3', tr).innerHTML, 'td3');
        equalHTML(Ink.s('#td3-b', tr).innerHTML, 'td3-b');

        InkElement.prependHTML(tr, '<td id="td4">td4</td>');
        equalHTML(Ink.s('#td4', table).innerHTML, 'td4');

        equal(tbody.children.length, 2);
        equal(tr.children.length, 5);
    });
    test('appendHTML on tables', function () {
        var table = InkElement.create('table');
        var tbody = InkElement.create('tbody');
        var tr = InkElement.create('tr');
        var td = InkElement.create('td');

        tr.appendChild(td);
        tbody.appendChild(tr);
        table.appendChild(tbody);

        InkElement.appendHTML(tbody, '<tr><td id="td1">td1</td></tr>');
        equalHTML(Ink.s('#td1', table).innerHTML, 'td1');

        InkElement.appendHTML(tr, '<td id="td2">td2</td>');
        equalHTML(Ink.s('#td2', table).innerHTML, 'td2');

        InkElement.appendHTML(tr, '<td id="td3">td3</td><td id="td3-b">td3-b</td>');
        equalHTML(Ink.s('#td3', tr).innerHTML, 'td3');
        equalHTML(Ink.s('#td3-b', tr).innerHTML, 'td3-b');

        InkElement.appendHTML(tr, '<td id="td4">td4</td>');
        equalHTML(Ink.s('#td4', table).innerHTML, 'td4');

        equal(tbody.children.length, 2);
        equal(tr.children.length, 5);
    });*/
    test('DIV in a P', function () {
        var p = InkElement.create('p');
        InkElement.prependHTML(p, '<div>hello</div>');
        equalHTML(p.innerHTML, '<div>hello</div>');
    });
});
