Ink.requireModules(['Ink.UI.SortableList_1', 'Ink.UI.Common_1', 'Ink.Dom.Element_1', 'Ink.Dom.Event_1', 'Ink.Dom.Selector_1'], function (SortableList, Common, InkElement, InkEvent, Selector) {
    function makeContainer() {
        var container = document.body.appendChild(InkElement.create('ul', {
            className: 'ink-sortable-list'
        }));

        container.appendChild(InkElement.create('li', {
            className: 'one',
            setTextContent: 'one'
        }));

        container.appendChild(InkElement.create('li', {
            className: 'two',
            setTextContent: 'two'
        }));

        return container
    }

    function testSortableList(name, testBack, options) {
        test(name, function ()  {
            var container = makeContainer();
            var lis = Ink.ss('li', container);
            var component = new SortableList(container, options || {});
            testBack(component, container, lis);
        });
    }

    testSortableList('Dragging with the mouse calls the correct functions on the correct elements', function (component, container, lis) {
        stop();

        sinon.spy(component, '_movePlaceholder')
        sinon.spy(component, '_addMovingClasses')
        sinon.spy(component, 'validateMove')
        sinon.spy(component, 'stopMoving')

        var startR = lis[0].getBoundingClientRect()
        var endR = lis[1].getBoundingClientRect()
        Syn.drag({
            from: { clientX: startR.left + 5, clientY: startR.top + 5},
            to: { clientX: endR.left + 5, clientY: endR.top + 5}
        }, container, function () {
            start();
            ok(component.stopMoving.calledOnce)
        })

        // The following happens during the drag
        setTimeout(function () {
            ok(component._movePlaceholder.calledOnce)
            ok(component._addMovingClasses.calledOnce)
            ok(component.validateMove.calledOnce)
        })
    });

    if ('ontouchstart' in window) {
        testSortableList('Dragging with the finger calls the same functions as dragging with the mouse', function (component, container, lis) {
            sinon.spy(component, '_movePlaceholder')
            sinon.spy(component, '_addMovingClasses')
            sinon.spy(component, 'validateMove')
            sinon.spy(component, 'stopMoving')

            var startR = lis[0].getBoundingClientRect()
            var endR = lis[1].getBoundingClientRect()

            utils.dispatchTouchEvent(lis[0], 'start', startR.left, startR.top);
            utils.dispatchTouchEvent(lis[0], 'move', startR.left, startR.top);
            ok(component._movePlaceholder.calledOnce)
            ok(component._addMovingClasses.calledOnce)
            ok(component.validateMove.calledOnce)
            utils.dispatchTouchEvent(lis[0], 'end', startR.left, startR.top);
            ok(component.stopMoving.calledOnce)
        });
    }
});
