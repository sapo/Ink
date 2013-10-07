

function equalHTML(html1, html2, message) {
    equal(
        html1.toLowerCase().replace(/\s+/g, ''),
        html2.toLowerCase().replace(/\s+/g, ''),
        message)
}

Ink.requireModules(['Ink.Dom.Element_1', 'Ink.Dom.Selector_1', 'Ink.Dom.Css_1'], function (InkElement, Selector, Css) {
    var testArea = document.createElement('div');
    document.body.appendChild(testArea);

    test('createElement adding classes', function () {
        var a = InkElement.create('a', {
            className: 'link a b'
        });
        ok(Css.hasClassName(a, 'link'));
        ok(Css.hasClassName(a, 'a'));
        ok(Css.hasClassName(a, 'b'));
    });

    test('createElement, improved', function () {
		var a = InkElement.create('a', {
			setTextContent: 'click here!',
			insertBottom: testArea
		});
        equal(InkElement.textContent(a), 'click here!');
        equal(a.parentNode, testArea);
        testArea.innerHTML = '';
    });

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


    test('setHTML', function () {
        var elm = InkElement.create('div')
        InkElement.setHTML(elm, '<span>Hello!</span>');
        equal(elm.getElementsByTagName('span')[0].innerHTML, 'Hello!');
    });

    test('setHTML with tables', function () {
        var elm = InkElement.create('table');
        var tr = InkElement.create('tr', { insertBottom: elm });
        var td = InkElement.create('td', { insertBottom: tr });

        InkElement.setHTML(td, '<span>Hello!</span>');
        ok(td.getElementsByTagName('span'))
        InkElement.setHTML(tr, '<td><span>Hello!</span></td>');
        ok(tr.getElementsByTagName('td')[0].getElementsByTagName('span'))
        InkElement.setHTML(elm, '<thead>Hello!</thead>');
        ok(elm.getElementsByTagName('thead'))
    });
});
