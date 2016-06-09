Ink.requireModules(['Ink.UI.DragDrop_1', 'Ink.UI.Common_1', 'Ink.Dom.Css_1', 'Ink.Dom.Element_1', 'Ink.Dom.Event_1', 'Ink.Dom.Selector_1'], function (DragDrop, Common, Css, InkElement, InkEvent, Selector) {
    'use strict';

    if (/PhantomJS/.test(navigator.userAgent)) {
        test('(skipping tests in phantom)', function () { ok(true, 'skippin\''); });
        return;
    }

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
            InkElement.remove(elm)
        });
    }

    testDragDrop('on{Drag,Drop} options', function (component, elm) {
        var onDragSpy = component.getOption('onDrag');
        var onDropSpy = component.getOption('onDrop');

        var target = Ink.s('.dg1', elm);
        var rect = target.getBoundingClientRect();
        InkEvent.fire(elm, 'mousedown', { target: target, clientX: rect.left + 5, clientY: rect.top + 5 });

        ok(onDragSpy.calledOnce, 'onDrag was called once');
        ok(onDropSpy.notCalled, 'onDrop not called yet');

        InkEvent.fire(document, 'mousemove', { clientX: 100, clientY: 100 });
        InkEvent.fire(document, 'mouseup');

        ok(onDropSpy.calledOnce, 'onDrop called');
        ok(onDragSpy.calledOnce, 'onDrag just called that once');

        deepEqual(
            onDragSpy.lastCall.args[0],
            {
                dragItem: target,
                dropZone: elm
            }, 'An object with { dragItem, dropZone } is the argument of the event handler.');
        deepEqual(
            onDropSpy.lastCall.args[0],
            {
                origin: elm,
                dragItem: target,
                dropZone: elm
            }, 'An object with { origin, dragItem, dropZone } is the argument of the event handler.');
        strictEqual(onDragSpy.lastCall.thisValue, component, 'callbacks called with the DragDrop instance as `this`');
        strictEqual(onDropSpy.lastCall.thisValue, component, 'callbacks called with the DragDrop instance as `this`');
    }, {
        onDrag: sinon.spy(),
        onDrop: sinon.spy()
    });

    function testDragDropWithHandle(name, cb, opt) {
        testDragDrop(name, function(component, elm){
            var dg1 = Ink.s('.dg1', elm)
            var dg2 = Ink.s('.dg2', elm)
            var handle1 = InkElement.create('div', {
                style: 'height:10px;width:10px',
                className: 'handle1 drag-handle' })
            var handle2 = InkElement.create('div', {
                style: 'height:10px;width:10px',
                className: 'handle2 drag-handle' })
            dg1.appendChild(handle1)
            dg2.appendChild(handle2)
            // .dg3 intentionally left without handle!
            cb(component, elm)
        }, opt);
    }

    testDragDropWithHandle('Dragging outside the handle does nothing.', function(ddrop, elm){
        // .dg1 has a handle
        var dg1 = Ink.s('.dg1', elm)
        var r = dg1.getBoundingClientRect()
        InkEvent.fire(elm, 'mousedown', { target: dg1, clientX: r.left + 5, clientY: r.top + 5 })
        InkEvent.fire(document, 'mouseup')
        ok(ddrop._options.onDrag.notCalled, 'Drag did not start')
        ok(ddrop._options.onDrop.notCalled, 'Drag did not start')
    }, {
        onDrag: sinon.spy(),
        onDrop: sinon.spy()
    });

    testDragDropWithHandle('(regression) Dragging outside the handle does not preventDefault the event!', function(ddrop, elm){
        // .dg1 has a handle
        var dg1 = Ink.s('.dg1', elm)
        var r = dg1.getBoundingClientRect()
        var preventDefault = sinon.spy()
        InkEvent.fire(elm, 'mousedown', { target: dg1, clientX: r.left + 5, clientY: r.top + 5, preventDefault: preventDefault })
        InkEvent.fire(document, 'mouseup')
        ok(preventDefault.notCalled)
    }, {
        onDrag: sinon.spy(),
        onDrop: sinon.spy()
    });

    function dragAndTest(name, testBack, options) {
        testDragDrop(name, function (component, elm) {
            var dg1 = Ink.s('.dg1', elm)
            var dg2 = Ink.s('.dg2', elm)
            var dg3 = Ink.s('.dg3', elm)

            var r = dg1.getBoundingClientRect()
            var r2 = dg2.getBoundingClientRect()

            InkEvent.fire(elm, 'mousedown', { target: dg1, clientX: r.left + 5, clientY: r.top + 5 })

            testBack(component, elm, { dg1: dg1, dg2: dg2, r: r, r2: r2, dg3: dg3 });

            component.destroy();
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

        InkEvent.fire(document, 'mousemove', { clientX: bag.r.left + 15, clientY: bag.r.top + 15 });

        var newRect = Ink.s('.drag-cloned-item', elm).getBoundingClientRect()

        olhometroEqual(newRect.left - oldRect.left, 10, 3, 'left is differing by 10');
        olhometroEqual(newRect.top - oldRect.top, 10, 3, 'top is differing by 10');

        InkEvent.fire(document, 'mouseup');

        var newRect = bag.dg1.getBoundingClientRect();

        olhometroEqual(newRect.left, oldRect.left, 3, 'thing snapped back');
        olhometroEqual(newRect.top, oldRect.top, 3, 'thing snapped back');
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

    module('Linked containers')

    function dragAndTest2Containers(name, testBack, options) {
        testDragDrop(name, function (component, elm) {
            elm.innerHTML =
                '<div class="drop-zone drop1" style="width: 200px; height: 100px; float: left;">' +
                    '<div class="drag-item drag1">Drag me!</div>' +
                '</div>' +
                '<div class="drop-zone drop2" style="width: 200px; height: 100px; float: left;">' +
                    '<div class="drag-item drag2">Drag me!2</div>' +
                '</div>';

            var drop1 = Ink.s('.drop1', elm)
            var drop2 = Ink.s('.drop2', elm)
            var drag1 = Ink.s('.drag1', elm)
            var drag2 = Ink.s('.drag2', elm)

            var r = drag1.getBoundingClientRect()
            var r2 = drag2.getBoundingClientRect()

            InkEvent.fire(elm, 'mousedown', { target: drag1, clientX: r.left + 5, clientY: r.top + 5 })

            testBack(component, elm, { drop1: drop1, drop2: drop2, drag1: drag1, drag2: drag2, r: r, r2: r2 });
        }, options || {})
    }

    dragAndTest2Containers('Can drop an element in the second container.', function (component, elm, bag) {
        InkEvent.fire(document, 'mousemove', { clientX: bag.r2.left + 5, clientY: bag.r2.top + 5 })
        InkEvent.fire(document, 'mouseup');

        ok(Ink.s('.drag1 + .drag2', bag.drop2), 'drag1 and drag2 got to the second container')
        ok(!Ink.s('.drag1, drag2', bag.drop1), 'could not find drag1 nor drag2 in the first container')
    });

    dragAndTest2Containers('Can drop an element in an empty container.', function (component, elm, bag) {
        bag.drop2.innerHTML = '';

        var drop2Rect = bag.drop2.getBoundingClientRect()

        InkEvent.fire(document, 'mousemove', { clientX: drop2Rect.left + 5, clientY: drop2Rect.top + 5 })
        InkEvent.fire(document, 'mouseup');

        strictEqual(bag.drop2.firstChild, bag.drag1, 'drag1 got to the second container');
    });
});
