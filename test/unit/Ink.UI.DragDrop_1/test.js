Ink.requireModules(['Ink.UI.DragDrop_1', 'Ink.UI.Common_1', 'Ink.Dom.Css_1', 'Ink.Dom.Element_1', 'Ink.Dom.Event_1', 'Ink.Dom.Selector_1'], function (DragDrop, Common, Css, InkElement, InkEvent, Selector) {
    function olhometroEqual(a, b, tolerance, msg) {
        if (typeof tolerance !== 'number') {
            msg = tolerance;
            tolerance = 2;
        }
        var diff = Math.abs(a - b);
        ok(tolerance > diff, msg)
    }

    var elmCount = 0
    function makeDragDropDom(opt) {
        opt = opt || {}
        var container = InkElement.create('div', {
            style: opt.style || '',
            insertBottom: document.body
        });

        InkElement.create('div', {
            className: 'drag-item dg1',
            setTextContent: 'drag me!',
            insertBottom: container
        })
        InkElement.create('div', {
            className: 'drag-item dg2',
            setTextContent: 'drag me! 2',
            insertBottom: container
        })
        InkElement.create('div', {
            className: 'drag-item dg3',
            setTextContent: 'drag me! 3',
            insertBottom: container
        })

        return container
    }

    function testDragDrop(name, testBack, options) {
        test(name, function ()  {
            var elm = makeDragDropDom();
            var component = new DragDrop(elm, options || {});
            testBack(component, elm);
        });
    }

    function dragAndTest(name, testBack, options) {
        testDragDrop(name, function (component, elm) {
            var dg1 = Ink.s('.dg1', elm)
            var dg2 = Ink.s('.dg2', elm)
            var dg3 = Ink.s('.dg3', elm)

            var r = dg1.getBoundingClientRect()
            var r2 = dg2.getBoundingClientRect()

            InkEvent.fire(elm, 'mousedown', { target: dg1, clientX: r.left + 5, clientY: r.top + 5 })

            testBack(component, elm, { dg1: dg1, dg2: dg2, r: r, r2: r2, dg3: dg3 });
        }, options || {})
    }

    dragAndTest('Dragging creates a clone and a placeholder. It also hides the original element.', function (component, elm, bag) {
        ok(Ink.s('.drag-cloned-item', elm))
        ok(Ink.s('.drag-placeholder-item', elm))
        ok(Css.hasClassName(bag.dg1, 'hide-all'));
        InkEvent.fire(document, 'mouseup');
    });

    dragAndTest('Moving things, dropping things.', function (component, elm, bag) {
        var oldRect = Ink.s('.drag-cloned-item', elm).getBoundingClientRect()

        InkEvent.fire(document, 'mousemove', { clientX: bag.r.left + 10, clientY: bag.r.top + 10 });

        var newRect = Ink.s('.drag-cloned-item', elm).getBoundingClientRect()

        olhometroEqual(newRect.left - oldRect.left, 5, 'left is differing by 5');
        olhometroEqual(newRect.top - oldRect.top, 5, 'top is differing by 5');

        InkEvent.fire(document, 'mouseup');

        var newRect = bag.dg1.getBoundingClientRect();

        olhometroEqual(newRect.left, oldRect.left, 'thing snapped back');
        olhometroEqual(newRect.top, oldRect.top, 'thing snapped back');
    });

    dragAndTest('Sorting things.', function (component, elm, bag) {
        InkEvent.fire(document, 'mousemove', { clientX: bag.r.left + 5, clientY: bag.r2.top + 5 });
        InkEvent.fire(document, 'mouseup');

        ok(Ink.s('.dg2 + .dg1 + .dg3', elm), 'Can sort down by dragging the mouse down.');

        var r = bag.dg1.getBoundingClientRect();
        var r2 = bag.dg2.getBoundingClientRect();
        InkEvent.fire(elm, 'mousedown', { target: bag.dg1, clientX: r.left + 5, clientY: r.top + 5 });

        InkEvent.fire(document, 'mousemove', { clientX: r2.left + 5, clientY: r2.top + 5 });
        InkEvent.fire(document, 'mouseup');

        ok(Ink.s('.dg1 + .dg2 + .dg3', elm), 'Can sort up by dragging the mouse up.');

        var r = bag.dg1.getBoundingClientRect();
        var r3 = bag.dg3.getBoundingClientRect();
        InkEvent.fire(elm, 'mousedown', { target: bag.dg1, clientX: r.left + 5, clientY: r.top + 5 });

        InkEvent.fire(document, 'mousemove', { clientX: r3.left + 5, clientY: r3.top + 5 });
        InkEvent.fire(document, 'mouseup');

        ok(Ink.s('.dg2 + .dg3 + .dg1', elm), 'Could sort down across two items.');

        var r = bag.dg1.getBoundingClientRect();
        var r2 = bag.dg2.getBoundingClientRect();
        InkEvent.fire(elm, 'mousedown', { target: bag.dg1, clientX: r.left + 5, clientY: r.top + 5 });

        InkEvent.fire(document, 'mousemove', { clientX: r2.left + 5, clientY: r2.top + 5 });
        InkEvent.fire(document, 'mouseup');

        ok(Ink.s('.dg1 + .dg2 + .dg3', elm), 'Could sort up across two items.');
    });

    if ('ontouchstart' in window) {
        // TODO
    }
});
