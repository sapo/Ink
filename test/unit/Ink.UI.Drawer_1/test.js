Ink.requireModules(['Ink.UI.Drawer_1', 'Ink.Dom.Element_1'], function (Drawer, InkElement) {

    var leftTrigger = document.body.appendChild(InkElement.create('div', {
        className: 'left-drawer-trigger'
    }))
    var rightTrigger = document.body.appendChild(InkElement.create('div', {
        className: 'right-drawer-trigger'
    }))

    var contentDrawer = document.body.appendChild(InkElement.create('div', {
        className: 'content-drawer'
    }))

    var leftDrawer = contentDrawer.appendChild(InkElement.create('div', {
        className: 'left-drawer'
    }))
    var rightDrawer = contentDrawer.appendChild(InkElement.create('div', {
        className: 'right-drawer'
    }))


    var drawer = new Drawer(document.body)

    test('Clicking the drawer buttons results in a call to Drawer\'s _onTriggerClicked(ev, side)', function () {
        var triggerClicked = sinon.stub(drawer, '_onTriggerClicked')
        stop(2)
        Syn.click(leftTrigger, function () {
            start()
            ok(triggerClicked.calledOnce)
            strictEqual(triggerClicked.lastCall.args[1], 'left')

            Syn.click(rightTrigger, function () {
                start()
                ok(triggerClicked.calledTwice)
                triggerClicked.restore()
                strictEqual(triggerClicked.lastCall.args[1], 'right')
            })
        })
    })

    test('Clicking the content drawer when the drawer is open leads to a close() call', function () {
        drawer.open('left')
        sinon.spy(drawer, 'close')
        stop()
        Syn.click(contentDrawer, function () {
            ok(drawer.close.calledOnce)
            start()
        })
    })
})
