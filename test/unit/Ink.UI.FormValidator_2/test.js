/*globals equal,test*/
Ink.requireModules(['Ink.UI.FormValidator_2', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1', 'Ink.Util.Array_1', 'Ink.Dom.Event_1'], function (FormValidator, InkElement, Selector, InkArray, InkEvent) {
    'use strict';

    function makeForm(options) {
        options = Ink.extendObj({
            makeValidator: true
        }, options || {});

        var form = document.body.appendChild(InkElement.create('form', {
            className: 'ink-form',
            method: 'POST',
            action: 'http://local'
        }));

        var controlGroup = form.appendChild(InkElement.create('div', {
            id: 'element_26',
            className: 'control-group required',
            'data-rules': 'required'
        }));

        var control = controlGroup.appendChild(InkElement.create('div', {
            className: 'control'
        }));

        var newInput = control.appendChild(InkElement.create('input', {
            id: 'radio2',
            type: 'radio'
        }));

        if (options.makeValidator) {
            var validator = new FormValidator(form)
        }
        
        return {
            form: form,
            validator: validator || null,
            controlGroup: controlGroup,
            control: control
        }
    }

    // Reparse all the fields in the form validator
    function reparse(formVal) {
        var formElements = formVal.getElements()
        for (var key in formElements) {
            if (formElements.hasOwnProperty(key)) {
                for (var j = 0; j < formElements[key].length; j++) {
                    formElements[key][j]._parseRules(
                        formElements[key][j]._element.getAttribute('data-rules')
                    );
                }
            }
        }
    }

    test('(regression): should destroy FormElement instances after using them', sinon.test(function () {
        var o = makeForm()

        var myForm = o.form
        var validator = o.validator

        var initFunc = this.spy(FormValidator.FormElement.prototype, '_init');
        validator.validate();
        validator.validate();
        equal(initFunc.callCount, 1, 'FormElement#_init only called once');

        myForm.parentNode.removeChild(myForm);
    }));

    test('Skips validating matches fields just as long as their matched field is not required', function () {
        var oldStuff = makeForm();

        var myForm = oldStuff.form;
        var validator = oldStuff.validator;

        myForm.innerHTML = '';

        myForm.appendChild(InkElement.create('input', {
            type: 'text',
            name: 'password',
            'data-rules': 'required'
        }));

        myForm.appendChild(InkElement.create('input', {
            type: 'text',
            name: 'passwordconfirmation',
            'data-rules': 'matches[password]'
        }));

        var elms = validator.getElements();

        reparse(validator);

        equal(elms.passwordconfirmation[0].validate(), false, 'Empty password matching field is invalid because the password is required');

        elms.password[0]._element.setAttribute('data-rules', '');

        reparse(validator);

        equal(elms.passwordconfirmation[0].validate(), true, 'Empty password matching field is valid because the password is not required');
    });

    test('Skips validating fields when they\'re empty, but still validates them if required', function () {
        var oldStuff = makeForm();

        var myForm = oldStuff.form;
        var validator = oldStuff.validator;

        myForm.innerHTML = '';

        myForm.appendChild(InkElement.create('input', {
            type: 'text',
            name: 'number',
            'data-rules': 'integer'
        }));

        myForm.appendChild(InkElement.create('input', {
            type: 'text',
            name: 'requirednumber',
            'data-rules': 'integer|required'
        }));

        var elms = validator.getElements();

        reparse(validator);

        equal(elms.number[0].validate(), true, 'Empty number field is valid');
        equal(elms.requirednumber[0].validate(), false, 'Empty required number field is invalid');

        elms.number[0]._element.value = 'a';
        elms.requirednumber[0]._element.value = '1';

        reparse(validator);

        equal(elms.number[0].validate(), false, 'Sanity check');
        equal(elms.requirednumber[0].validate(), true, 'Sanity check');
    });


    test('(regression) Validation of a matches field fails if it doesn\'t have a [rules] attribute', function () {
        var oldStuff = makeForm();

        var myForm = oldStuff.form;
        var validator = oldStuff.validator;

        myForm.innerHTML = '';

        myForm.appendChild(InkElement.create('input', {
            type: 'text',
            name: 'password'
        }));

        myForm.appendChild(InkElement.create('input', {
            type: 'text',
            name: 'passwordconfirmation',
            'data-rules': 'matches[password]'
        }));

        var elms = validator.getElements();

        reparse(validator);

        equal(elms.passwordconfirmation[0].validate(), true,
            'Empty password matching field is valid because the password is not required');
    });

    test('Create new FormElements for each control-group in the form', sinon.test(function () {
        var oldStuff = makeForm();

        var myForm = oldStuff.form;
        var validator = oldStuff.validator;

        var initFunc = this.spy(FormValidator.FormElement.prototype, '_init');

        validator.validate();
        equal(initFunc.callCount, 1, 'FormElement#_init called for the existing control-group');

        strictEqual(initFunc.firstCall &&
            initFunc.firstCall.thisValue.getElement(),
            oldStuff.controlGroup,
            'FormElement was called with the correct element as argument');

        var newStuff = makeForm();

        newStuff.form.parentNode.removeChild(newStuff.form);
        newStuff.controlGroup.id = 'element_27';
        myForm.appendChild(newStuff.controlGroup);

        validator.validate();

        equal(initFunc.callCount, 2, 'FormElement#_init called again');

        strictEqual(initFunc.lastCall &&
            initFunc.lastCall.thisValue.getElement(),
            newStuff.controlGroup,
            'FormElement was now called with the new element as argument');

        myForm.parentNode.removeChild(myForm);
    }));
});
