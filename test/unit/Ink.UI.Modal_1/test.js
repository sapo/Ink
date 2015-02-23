Ink.requireModules(['Ink.UI.Modal_1', 'Ink.Dom.Element_1', 'Ink.Dom.Css_1'], function (Modal, InkElement, Css) {
    function makeContainer(opt) {
        var cont = document.body.appendChild(InkElement.create('div', {
            className: 'ink-shade fade'
        }))

        var modal = cont.appendChild(InkElement.create('div', {
            className: 'ink-modal'
        }))

        if (opt && opt.makeHeader) {
            var header = modal.appendChild(InkElement.create('div', { className: 'modal-header' }))
        }

        var body = modal.appendChild(InkElement.create('div', {
            className: 'modal-body'
        }));

        if (opt && opt.makeFooter) {
            var footer = modal.appendChild(InkElement.create('div', { className: 'modal-footer' }))
        }

        return cont;
    }

    function modalTest(name, testBack, options) {
        test(name, function () {
            var els = makeContainer(options);
            var modal = new Modal(Ink.s('.ink-modal', els), options || {});
            testBack(modal, els);
        })
    }

    modalTest('Modal opens automatically when autoDisplay: true', function(modal, els) {
        ok(modal.isOpen(), 'Modal is open');
        modal.dismiss();  // go away!
    }, { autoDisplay: true });

    (function (trigger) {
        modalTest('creating a modal with a trigger doesn\'t auto-open it', function(modal, cont) {
            ok(!modal.isOpen(), 'Modal is closed');
        }, { trigger: trigger })
    }(InkElement.create('a', { href: '#' })));

    modalTest('Neither does it open if you set autoDisplay:false as an option', function(modal, els) {
        ok(!modal.isOpen(), 'Modal is closed');
    }, { autoDisplay: false });

    (function (trigger) {
        modalTest('clicking on the trigger makes the modal open', function(modal, els) {
            document.body.appendChild(trigger);  // IE needs the element to be in the DOM to fire events on it
            ok(!modal.isOpen(), 'Modal is closed');

            stop();
            Syn.click(trigger, function () {
                ok(modal.isOpen());
                document.body.removeChild(trigger);
                modal.dismiss();  // Go away!
                start();
            });
        }, { trigger: trigger })
    }(InkElement.create('a', { href: '#', setHTML: 'trigger' })));

    modalTest('Clicking the shade makes the modal close', function (modal, els) {
        var shade = modal._modalShadow;

        ok(modal.isOpen(), 'modal is initially open');
        stop();
        Syn.click({}, shade, function () {
            ok(!modal.isOpen(), 'clicking the shade causes it to close');
            start();
        });
    }, { autoDisplay: true });

    modalTest('A new modal has style.height, style.width set to its height, width options\' values', function (modal, els) {
        equal(modal._modalDiv.style.height, '100px');
        equal(modal._modalDiv.style.width, '90%');
        modal.dismiss();
    }, { height: '100px', width: '90%', autoDisplay: true });

    modalTest('_reposition, called on construction and resize, repositions the modal by setting the marginTop and marginLeft style properties to negative values', function (modal) {
        sinon.stub(InkElement, 'elementHeight').returns(200);
        sinon.stub(InkElement, 'elementWidth').returns(100);
        modal._reposition();
        equal(modal._element.style.marginTop, '-100px');
        equal(modal._element.style.marginLeft, '-50px');
        modal.dismiss();
        InkElement.elementHeight.restore();
        InkElement.elementWidth.restore();
    });
    modalTest('_reposition, called on construction and resize, repositions the modal by setting the marginTop and marginLeft style properties to negative values, part 1', function (modal) {
        sinon.stub(InkElement, 'elementHeight').returns(200);
        sinon.stub(InkElement, 'elementWidth').returns(100);
        modal._reposition();
        equal(modal._element.style.marginTop, '-100px');
        equal(modal._element.style.marginLeft, '-50px');
        modal.dismiss();
        InkElement.elementHeight.restore();
        InkElement.elementWidth.restore();
        // debugger
    }, { height: '90%', width: '80%' });


    modalTest('_resizeContainer, called on construction and resize, sets the height of the modal container to be the height of the modal, minus that of the header and footer', function (modal, els) {
        // sinon.stub(InkElement, 'viewportWidth').returns(800);
        // sinon.stub(InkElement, 'viewportHeight').returns(600);
        var height = sinon.stub(InkElement, 'elementHeight', function (elm) {
            return elm === modal._modalDiv ? 500 :
                elm === modal._modalHeader ? 100 :
                elm === modal._modalFooter ? 150 : ok(false)
        })

        modal._resizeContainer()

        equal(modal._contentContainer.style.height, '250px')
        modal.dismiss();
        height.restore();
    }, { makeHeader: true, makeFooter: true });

    test('_onResize makes sure fixed-size modals aren\'t larger than the screen. When the screen is sized down, the size is limited. When it goes back up, the size goes back to where it started. Then it calls _reposition() and _resizeContainer()', function () {
        sinon.stub(InkElement, 'viewportWidth').returns(300);
        sinon.stub(InkElement, 'viewportHeight').returns(300);

        var els = makeContainer();
        var modal = new Modal(Ink.s('.ink-modal', els), { autoDisplay: true, height: '200px', width: '200px' });

        sinon.stub(modal, '_reposition');  // Don't call these
        sinon.stub(modal, '_resizeContainer');  // Don't call these

        modal._onResize();
        ok(modal._reposition.calledOnce, '_onResize() calls modal._reposition() once')
        ok(modal._resizeContainer.calledOnce, '_onResize() calls modal._resizeContainer() once')

        equal(modal._modalDiv.style.height, '200px');
        equal(modal._modalDiv.style.width, '200px');

        InkElement.viewportWidth.returns(100)
        InkElement.viewportHeight.returns(100)

        modal._onResize();

        equal(modal._modalDiv.style.height, '90px');
        equal(modal._modalDiv.style.width, '90px');

        InkElement.viewportWidth.returns(400)
        InkElement.viewportHeight.returns(400)

        modal._onResize();

        equal(modal._modalDiv.style.height, '200px');
        equal(modal._modalDiv.style.width, '200px');

        modal.dismiss();
    });

/*
    modalTest('Resizing the modal sets the body\'s style.height', function (modal, els) {
    });*/
})
