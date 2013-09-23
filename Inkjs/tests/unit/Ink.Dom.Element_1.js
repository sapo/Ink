
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
        equal(rootElm.innerHTML.toLowerCase(), '<span>before</span><b>the</b>some-text<span>after</span><i>it</i>');
    });
    test('appendHTML appending text nodes', function () {
        var elem = InkElement.create('a');
        InkElement.appendHTML(elem, 'text');
        equal(elem.innerHTML, 'text');
    });
    test('prependHTML prepending text nodes', function () {
        var elem = InkElement.create('a');
        InkElement.prependHTML(elem, 'text');
        equal(elem.innerHTML, 'text');
    });
    test('prependHTML on tables', function () {
        var table = InkElement.create('table');
        var tbody = InkElement.create('tbody');
        var tr = InkElement.create('tr');
        var td = InkElement.create('td');

        tr.appendChild(td);
        tbody.appendChild(tr);
        table.appendChild(tbody);

        InkElement.prependHTML(tbody, '<tr><td id="td1">td1</td></tr>');
        equal(Ink.s('#td1', table).innerHTML, 'td1');

        InkElement.prependHTML(tr, '<td id="td2">td2</td>');
        equal(Ink.s('#td2', table).innerHTML, 'td2');

        InkElement.prependHTML(tr, '<td id="td3">td3</td><td id="td3-b">td3-b</td>');
        equal(Ink.s('#td3', tr).innerHTML, 'td3');
        equal(Ink.s('#td3-b', tr).innerHTML, 'td3-b');

        InkElement.prependHTML(tr, '<td id="td4">td4</td>');
        equal(Ink.s('#td4', table).innerHTML, 'td4');

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
        equal(Ink.s('#td1', table).innerHTML, 'td1');

        InkElement.appendHTML(tr, '<td id="td2">td2</td>');
        equal(Ink.s('#td2', table).innerHTML, 'td2');

        InkElement.appendHTML(tr, '<td id="td3">td3</td><td id="td3-b">td3-b</td>');
        equal(Ink.s('#td3', tr).innerHTML, 'td3');
        equal(Ink.s('#td3-b', tr).innerHTML, 'td3-b');

        InkElement.appendHTML(tr, '<td id="td4">td4</td>');
        equal(Ink.s('#td4', table).innerHTML, 'td4');

        equal(tbody.children.length, 2);
        equal(tr.children.length, 5);
    });
});
