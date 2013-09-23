
Ink.requireModules(['Ink.Dom.Element_1'], function (InkElement) {
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
});
