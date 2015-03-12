Ink.requireModules(['Ink.UI.Draggable_1', 'Ink.UI.Common_1', 'Ink.Dom.Element_1', 'Ink.Dom.Event_1', 'Ink.Dom.Selector_1'], function (Draggable, Common, InkElement, InkEvent, Selector) {
    function olhometroEqual(a, b, tolerance, msg) {
        if (typeof tolerance !== 'number') {
            msg = tolerance;
            tolerance = 10;
        }
        var diff = Math.abs(a - b);
        ok(tolerance > diff, msg)
    }

    var draggableCount = 0
    function makeDraggableDom(opt) {
        opt = opt || {}
        return InkElement.create('div', {
            style: opt.style || '',
            setTextContent: 'I am test draggable ' + (++draggableCount),
            insertBottom: document.body
        });
    }

    function testDraggable(name, testBack, options) {
        test(name, function ()  {
            var draggable = makeDraggableDom();
            var tabComponent = new Draggable(draggable, options || {});
            testBack(tabComponent, draggable);
        });
    }

    testDraggable('basic usage', function (tabComponent, draggable) {
        var r = draggable.getBoundingClientRect()
        stop()
        Syn.drag({
            from: { clientX: r.left + 5, clientY: r.top + 5 }, to:'100x100'
        }, draggable, function () {
            start()
            var newR = draggable.getBoundingClientRect()
            olhometroEqual(newR.left, 100)
            olhometroEqual(newR.top, 100)
        })
    });

    if ('ontouchstart' in window) {
        // TODO
    }
});
