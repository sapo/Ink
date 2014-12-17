Ink.requireModules(['Ink.UI.Modal_1', 'Ink.Dom.Element_1', 'Ink.Dom.Css_1'], function (Modal, InkElement, Css) {
    function makeContainer() {
        var cont = document.body.appendChild(InkElement.create('div', {
            className: 'ink-shade fade'
        }))

        var modal = cont.appendChild(InkElement.create('div', {
            className: 'ink-modal'
        }))

        var body = modal.appendChild(InkElement.create('div', {
            className: 'modal-body'
        }));

        return cont;
    }

    function modalTest(name, testBack, options) {
        test(name, function () {
            var els = makeContainer();
            var modal = new Modal(Ink.s('.ink-modal', els), options || {});
            testBack(modal, els);
        })
    }

    modalTest('Modal opens automatically', function(modal, els) {
        ok(modal.isOpen(), 'Modal is open');
        modal.dismiss();  // go away!
    });

    (function (trigger) {
        modalTest('creating a modal with a trigger doesn\'t auto-open it', function(modal, cont) {
            ok(!modal.isOpen(), 'Modal is closed');
        }, { trigger: trigger })
    }(InkElement.create('a', { href: '#' })));

    modalTest('Neither does it open if you set autoOpen:false as an option', function(modal, els) {
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
    }, { autoDisplay: true })
})
