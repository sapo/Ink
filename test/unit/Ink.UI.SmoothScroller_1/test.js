Ink.requireModules(['Ink.UI.SmoothScroller_1', 'Ink.UI.Common_1', 'Ink.Dom.Element_1', 'Ink.Dom.Event_1'], function (SmoothScroller, Common, InkElement, InkEvent) {
    var fakeLayout;

    // Prevent state from other test runs from infecting this.
    var pathHere = (window.location + '').replace(/\#.*/g, '')
    window.location.hash = '#no-hash';

    SmoothScroller.init()

    var dom = [
        '<ul>',
        '    <li><a href="#part1" class="ink-smooth-scroll">Scroll to "Part 1"</a></li>',
        '    <li><a href="#part2" class="ink-smooth-scroll">Scroll to "Part 2"</a></li>',
        '</ul>',
        '<h1 id="part1">part1</h1>',
        '<h1 id="part2">part2</h1>'
    ].join('\n')

    function testWithList(message, fn) {
        test(message, function() {
            var rootElm = InkElement.create('div', { setHTML: dom, insertBottom: document.body })
            fn(rootElm)
        })
    }

    testWithList('regression: "active" class does not get cleaned up when clicking another item', function (rootElm) {
        stop()
        Syn.click(Ink.s('[href="#part1"]', rootElm), function() {
            ok(Ink.s('li.active + li', rootElm), 'Sanity check: after clicking, the first list item gets the class.')
            Syn.click(Ink.s('[href="#part2"]', rootElm), function() {
                ok(Ink.s('li:not(.active) + li.active', rootElm))
                start()
            })
        })
    });
});

