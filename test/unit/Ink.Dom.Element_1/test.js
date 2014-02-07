

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
            InkElement.prependHTML(table, '<tbody></tbody>');
            equalHTML(table.innerHTML, '<tbody></tbody>');
        })

        test('prepending theads to tables', function () {
            init();
            equalHTML(table.innerHTML, '<tbody><tr><td></td></tr></tbody>');
            InkElement.prependHTML(table, '<thead></thead>');
            // No automatically generated tbody!
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
        var elm = InkElement.create('div');
        elm.innerHTML = '<span>Hi!</span>';

        InkElement.setHTML(elm, '<span>Hello!</span>');
        equalHTML(elm.innerHTML, '<span>Hello!</span>');
    });

    test('setHTML with tables', function () {
        var elm = InkElement.create('table');
        var tbody = InkElement.create('tbody', { insertBottom: elm });
        var tr = InkElement.create('tr', { insertBottom: tbody });
        var td = InkElement.create('td', { insertBottom: tr});

        InkElement.setHTML(td, '<span>Hello!</span>');
        ok(td.getElementsByTagName('span'))
        InkElement.setHTML(tr, '<td><span>Hello!</span></td>');
        ok(tr.getElementsByTagName('td')[0].getElementsByTagName('span'))
        InkElement.setHTML(elm, '<thead>Hello!</thead><tbody></tbody>');
        ok(elm.getElementsByTagName('thead'))

        equalHTML(tbody.innerHTML,
            '<tr><td><span>Hello!</span></td></tr>');
    });

    test('setHTML and text nodes', function () {
        var elm = InkElement.create('a');
        InkElement.setHTML(elm, 'hi!');
        equalHTML(elm.innerHTML, 'hi!');

        InkElement.setHTML(elm, 'hi!<br>');
        equalHTML(elm.innerHTML, 'hi!<br>');

        InkElement.setHTML(elm, '<br>hi!');
        equalHTML(elm.innerHTML, '<br>hi!');
    });

    test('setHTML and automatically generated tbodies', function () {
        var elm = InkElement.create('table');
        InkElement.appendHTML(elm, '<thead><tr><th>hi!</th></tr></thead>');
        equalHTML(elm.innerHTML, '<thead><tr><th>hi!</th></tr></thead>', 'no tbody!');

        InkElement.appendHTML(elm, '<tbody><tr><th>hi!</th></tr></tbody>');
        equalHTML(elm.innerHTML, '<thead><tr><th>hi!</th></tr></thead>'
            + '<tbody><tr><th>hi!</th></tr></tbody>', 'no tbody!');
    });

    function toArray(pseudoArray) {
        var ret = [];
        for (var i = 0, len = pseudoArray.length; i < len; i++) {
            ret.push(pseudoArray[i]);
        }
        return ret;
    }

    test('wrap()', function () {
        var elm = InkElement.create('div');
        var child1 = InkElement.create('div', { insertBottom: elm });
        var child2 = InkElement.create('div', { insertBottom: elm });
        var child3 = InkElement.create('div', { insertBottom: elm });

        var wrap = InkElement.wrap(child2, InkElement.create('section'));

        deepEqual(toArray(elm.children), [child1, wrap, child3]);
        deepEqual(toArray(wrap.children), [child2]);
    });

    test('unwrap()', function () {
        var theParent = InkElement.create('div');
        var wrapper = InkElement.create('div', { insertBottom: theParent });
        var child = InkElement.create('div', { insertBottom: wrapper });
        
        InkElement.unwrap(child);

        strictEqual(child.parentNode, theParent)
        strictEqual(child.nextSibling, wrapper);
        strictEqual(wrapper.firstChild, null)
    });

    test('unwrap(elem, selector)', function () {
        var theParent = InkElement.create('div');
        var wrapper = InkElement.create('div', { insertBottom: theParent, className: 'outer-wrapper'});
        var anotherWrapper = InkElement.create('div', { insertBottom: wrapper, className: 'another-wrapper'});
        var child = InkElement.create('div', { insertBottom: wrapper });

        InkElement.unwrap(child, '.outer-wrapper');

        strictEqual(child.parentNode, theParent);
        strictEqual(child.nextSibling, wrapper);
        strictEqual(wrapper.firstChild, anotherWrapper);
        strictEqual(anotherWrapper.firstChild, null);
    });

    test('replace()', function () {
        var elm = InkElement.create('div');
        elm.className = 'elm';
        var elm2 = InkElement.create('div');
        elm2.className = 'elm2';

        var parent = InkElement.create('div');
        parent.appendChild(elm);

        InkElement.replace(elm, elm2);

        equal(parent.children.length, 1);
        equal(parent.children[0].className, 'elm2');
    });

    test('outerDimensions', function () {
        var elm = InkElement.create('div');
        document.body.appendChild(elm);

        elm.style.width = '30px';
        elm.style.paddingRight = '5px';
        elm.style.marginRight = '5px';

        elm.style.height = '10px';
        elm.style.paddingBottom = '5px';
        elm.style.marginBottom = '5px';

        equal(InkElement.outerDimensions(elm)[0], 40);
        equal(InkElement.outerDimensions(elm)[1], 20);

        elm.style.width = '30.25px';
        elm.style.paddingRight = '5.25px';
        elm.style.marginRight = '5.25px';
        equal(InkElement.outerDimensions(elm)[0], 40.75);

        elm.style.height = '10.25px'
        elm.style.paddingBottom = '5.25px'
        elm.style.marginBottom = '5.25px'
        equal(InkElement.outerDimensions(elm)[1], 20.75);

        document.body.removeChild(elm);
    });

    test('outerDimensions in an element detached from the DOM', function () {
        var elm = InkElement.create('div');

        elm.style.width = '30px';
        elm.style.paddingRight = '5px';
        elm.style.marginRight = '5px';

        elm.style.height = '10px';
        elm.style.paddingBottom = '5px';
        elm.style.marginBottom = '5px';

        equal(InkElement.outerDimensions(elm).length, 2);
        notStrictEqual(InkElement.outerDimensions(elm)[0], NaN);
        notStrictEqual(InkElement.outerDimensions(elm)[1], NaN);
        equal(typeof InkElement.outerDimensions(elm)[0], 'number');
        equal(typeof InkElement.outerDimensions(elm)[1], 'number');

    });
});
