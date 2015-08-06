

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

    test('textContent', function () {
        var a = document.createElement('a');
        a.innerHTML = 't&amp;sting';
        equal(InkElement.textContent(a), 't&sting');
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
        var table = InkElement.create('table');
        var tbody = InkElement.create('tbody', { insertBottom: table });
        var tr = InkElement.create('tr', { insertBottom: tbody });
        var td = InkElement.create('td', { insertBottom: tr});

        InkElement.setHTML(td, '<span>Hello!</span>');
        equal(td.getElementsByTagName('span').length,
            1,
            'A span was created in the td');
        InkElement.setHTML(tr, '<td><span>Hello!</span></td>');
        equal(tr.getElementsByTagName('td').length, 1, 'Still one <td>');
        equal(tr.getElementsByTagName('td')[0].getElementsByTagName('span').length,
            1,
            'A span was created')
        InkElement.setHTML(table, '<thead><tr><th>Hello!</th></tr></thead><tbody></tbody>');

        equalHTML(table.innerHTML,
            '<thead><tr><th>Hello!</th></tr></thead><tbody></tbody>',
            'Creating a tbody removed our existing tbody');

        tbody = table.getElementsByTagName('tbody')[0];

        InkElement.setHTML(tbody, '<tr><td>1</td></tr><tr><td>2</td></tr>');

        equalHTML(tbody.innerHTML,
                '<tr><td>1</td></tr><tr><td>2</td></tr>',
                'We can create several tds!');
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
        var child = InkElement.create('div', { insertBottom: anotherWrapper });

        InkElement.unwrap(child, '.outer-wrapper');

        strictEqual(child.parentNode, theParent);
        strictEqual(child.nextSibling, wrapper);
        strictEqual(wrapper.firstChild, anotherWrapper);
        strictEqual(anotherWrapper.firstChild, null);
    });

    test('unwrap(elem, elem)', function () {
        var theParent = InkElement.create('div');
        var wrapper = InkElement.create('div', { insertBottom: theParent, className: 'outer-wrapper'});
        var anotherWrapper = InkElement.create('div', { insertBottom: wrapper, className: 'another-wrapper'});
        var child = InkElement.create('div', { insertBottom: anotherWrapper });

        InkElement.unwrap(child, wrapper);

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

    test('parentIndexOf()', function() {
        var parent = document.createElement('div');
        parent.innerHTML = '<span>' +
            [0, 1, 2, 3, 4, 5, 6, 7].join('</span><span>') +
            '</span>';

        equal(InkElement.parentIndexOf(parent.getElementsByTagName('span')[3]), 3);
        equal(InkElement.parentIndexOf(parent.getElementsByTagName('span')[0]), 0);
        equal(InkElement.parentIndexOf(parent.getElementsByTagName('span')[7]), 7);

        equal(InkElement.parentIndexOf(document.createElement('div')), false);
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

    test('inViewport', sinon.test(function () {
        // This test occurs inside a 100 X 100 screen
        var height = this.stub(InkElement, 'viewportHeight').returns(100);
        var width = this.stub(InkElement, 'viewportWidth').returns(100);

        var fakeEl = {};
        var rect = fakeEl.getBoundingClientRect = this.stub();

        // Off to the left
        rect.returns({left: -101, right: -1, top: 10, bottom: 10})
        ok(!InkElement.inViewport(fakeEl))
        ok(rect.called, 'sanity check')

        // Off to the right
        rect.returns({left: 101, right: 200, top: 10, bottom: 10})
        ok(!InkElement.inViewport(fakeEl))

        // Off to the top
        rect.returns({top: -101, bottom: -1, left: 10, right: 10})
        ok(!InkElement.inViewport(fakeEl))

        // Off to the bottom
        rect.returns({top: 101, bottom: 200, left: 10, right: 10})
        ok(!InkElement.inViewport(fakeEl))

        // Partially inside from the left
        rect.returns({left: -101, right: 1, top: 10, bottom: 10})
        ok(InkElement.inViewport(fakeEl, true))

        // Partially inside from the right
        rect.returns({left: 99, right: 200, top: 10, bottom: 10})
        ok(InkElement.inViewport(fakeEl, { partial: true }))

        // Partially inside from the top
        rect.returns({top: -101, bottom: 1, left: 10, right: 10})
        ok(InkElement.inViewport(fakeEl, true))

        // Partially inside from the bottom
        rect.returns({top: 99, bottom: 200, left: 10, right: 10})
        ok(InkElement.inViewport(fakeEl, true))

        function dumbRect(x, y) {
            return { left: x, right: x, top: y, bottom: y };
        }

        // Try with partial = false, then with partial = true.
        for (var partial = false; partial < 2; partial += 1) {
            partial = !!partial;


            ok(true, 'Testing with opts.partial = ' + partial + ', within the margin');

            rect.returns(dumbRect(-3, 3))
            ok(InkElement.inViewport(fakeEl, { margin: 4, partial: partial }), 'inside from the left, because within margin')

            rect.returns(dumbRect(103, 3))
            ok(InkElement.inViewport(fakeEl, { margin: 4, partial: partial }), 'inside from the right, because within margin')

            rect.returns(dumbRect(3, -3))
            ok(InkElement.inViewport(fakeEl, { margin: 4, partial: partial }), 'inside from the top, because within margin')

            rect.returns(dumbRect(3, 103))
            ok(InkElement.inViewport(fakeEl, { margin: 4, partial: partial }), 'inside from the bottom, because within margin')


            ok(true, 'Testing with opts.partial = ' + partial + ', outside the margin');

            rect.returns(dumbRect(-3, 3))
            ok(!InkElement.inViewport(fakeEl, { margin: 2, partial: partial }), 'outside from the left, because outside margin')

            rect.returns(dumbRect(103, 3))
            ok(!InkElement.inViewport(fakeEl, { margin: 2, partial: partial }), 'outside from the right, because outside margin')

            rect.returns(dumbRect(3, -3))
            ok(!InkElement.inViewport(fakeEl, { margin: 2, partial: partial }), 'outside from the top, because outside margin')

            rect.returns(dumbRect(3, 103))
            ok(!InkElement.inViewport(fakeEl, { margin: 2, partial: partial }), 'outside from the bottom, because outside margin')
        }
    }));

    test('ellipsizeText', function () {
        var elem = document.createElement('div');
        elem.innerHTML = (new Array(100)).join('text ')
        elem.style.width = '100px';
        document.body.appendChild(elem);
        var rectng = elem.getBoundingClientRect();
        var h = rectng.bottom - rectng.top;
        InkElement.ellipsizeText(elem);
        rectng = elem.getBoundingClientRect();
        var newH = rectng.bottom - rectng.top;
        ok(newH < h);
        document.body.removeChild(elem);
    });

    test('fillSelect', function () {
        var container = document.createElement('select');
        var data = [
            ['1', 'a'],
            ['2', 'b'],
            ['3', 'c']
        ];
        InkElement.fillSelect(container, data, true);
        equal(container.children.length, 3);

        for (var i = 0, len = container.children.length; i < len; i++) {
            strictEqual(container.children[i].getAttribute('value'), data[i][0]);
            strictEqual(InkElement.textContent(container.children[i]), data[i][1]);
        }
    });
});

