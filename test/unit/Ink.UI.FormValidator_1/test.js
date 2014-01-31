/*globals equal,test*/
Ink.requireModules(['Ink.UI.FormValidator_1', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1', 'Ink.Util.Array_1', 'Ink.Dom.Event_1'], function (FormValidator, InkElement, Selector, InkArray, InkEvent) {
    'use strict';
    var body = document.getElementsByTagName('body')[0];

    test('required text', function () {
        var txt = InkElement.create('input', { type: 'text', className: 'ink-fv-required' });
        ok(!FormValidator._isValid(txt, 'ink-fv-required'));
        txt.value = 'some text';
        ok(FormValidator._isValid(txt, 'ink-fv-required'));
    });

    test('(regression) not required email', function () {
        var txt = InkElement.create('input', { type: 'text', className: 'ink-fv-email' });
        txt.value = '';
        ok(FormValidator._isValid(txt, 'ink-fv-email'));
        txt.value = 'my@email.com';
        ok(FormValidator._isValid(txt, 'ink-fv-email'));
    });

    test('required to select at least one radio button', function () {
        var radios = [
            InkElement.create('input', { type: 'radio', className: 'ink-fv-required', name: 'radioinpt', value: '1' }),
            InkElement.create('input', { type: 'radio', className: 'ink-fv-required', name: 'radioinpt', value: '2' }),
            InkElement.create('input', { type: 'radio', className: 'ink-fv-required', name: 'radioinpt', value: '3' })
        ];

        var form = InkElement.create('form');

        for (var i = 0, len = radios.length; i < len; i++) {
            form.appendChild(radios[i]);
        }
        body.appendChild(form);

        for (i = 0, len = radios.length; i < len; i++) {
            ok(!FormValidator._isValid(radios[i], 'ink-fv-required'));
        }

        radios[1].checked = true;

        for (i = 0, len = radios.length; i < len; i++) {
            ok(FormValidator._isValid(radios[i], 'ink-fv-required'));
        }

        body.removeChild(form);
    });

    test('required to check that one checkbox', function () {
        var check = InkElement.create('input', { type: 'checkbox', className: 'ink-fv-required', name: 'checkinpt', value: '1' });

        var form = InkElement.create('form');
        form.appendChild(check);
        body.appendChild(form);

        ok(!FormValidator._isValid(check, 'ink-fv-required'));

        check.checked = true;

        ok(FormValidator._isValid(check, 'ink-fv-required'));

        body.removeChild(form);
    });

    test('required to select at least one checkbox', function () {
        var checks = [
            InkElement.create('input', { type: 'checkbox', className: 'ink-fv-required', name: 'checkinpt', value: '1' }),
            InkElement.create('input', { type: 'checkbox', className: 'ink-fv-required', name: 'checkinpt', value: '2' }),
            InkElement.create('input', { type: 'checkbox', className: 'ink-fv-required', name: 'checkinpt', value: '3' })
        ];

        var form = InkElement.create('form');

        for (var i = 0, len = checks.length; i < len; i++) {
            form.appendChild(checks[i]);
        }
        body.appendChild(form);

        for (i = 0, len = checks.length; i < len; i++) {
            ok(!FormValidator._isValid(checks[i], 'ink-fv-required'));
        }

        checks[1].checked = true;

        for (i = 0, len = checks.length; i < len; i++) {
            ok(FormValidator._isValid(checks[i], 'ink-fv-required'));
        }

        body.removeChild(form);
    });
});
