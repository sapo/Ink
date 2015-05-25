/*globals equal,test*/
Ink.requireModules(['Ink.UI.FormValidator_2', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1', 'Ink.Util.Array_1', 'Ink.Dom.Event_1', 'Ink.Dom.Css_1'], function (FormValidator, InkElement, Selector, InkArray, InkEvent, Css) {
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
            var validator = new FormValidator(form, options)
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

    test('Error messages', function () {
        var formPack = makeForm();

        var form = formPack.form;
        var validator = formPack.validator;

        form.innerHTML = '';

        form.appendChild(InkElement.create('input', {
            type: 'text',
            name: 'FIELD1NAME',
            'data-rules': 'required'
        }));

        form.appendChild(InkElement.create('input', {
            type: 'text',
            name: 'field2name',
            'data-rules': 'required',
            'data-error': 'ERROR TEXT'
        }));

        ok(!validator.validate(), 'sanity check');

        var tips = Ink.ss('p.tip', form);

        equal(tips.length, 2,
            'there should be 2 error paras')

        ok(/FIELD1NAME/.test(
            InkElement.textContent(tips[0])))
        equal(InkElement.textContent(tips[1]), 'ERROR TEXT')
    });

    test('languages', function() {
        var form = makeForm();
        form.validator.setLanguage('pt_PT');
        equal(form.validator.getLanguage(), 'pt_PT');
        notEqual(FormValidator.getI18n(), form.validator.getI18n());
        equal(form.validator.getI18n().lang(), 'pt_PT');
    })

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

    test('[disabled] fields count as valid', function() {
        var bag = makeForm();

        InkElement.setHTML(bag.control, '');

        var elm = InkElement.create('input', {
            type: 'text',
            'data-rules': 'required',
            value: ''
        });

        bag.control.appendChild(elm);
        ok(!bag.validator.validate(), 'sanity check');

        elm.setAttribute('disabled', 'disabled');

        ok(bag.validator.validate(), 'now it should be valid');
    });

    test('regression: [disabled] has precedence over forceInvalid when inplace', function() {
        var bag = makeForm();

        InkElement.setHTML(bag.control, '');

        var elm = InkElement.create('input', {
            type: 'text',
            'data-rules': 'required',
            name: 'foo',
            value: ''
        });

        bag.control.appendChild(elm);

        bag.validator.getElements()['foo'][0].forceInvalid('invalid you are!')

        elm.setAttribute('disabled', 'disabled');

        ok(bag.validator.validate(), 'now it should be valid');
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

    test('When a form is invalid, _invalid is called with an array of errored elements', sinon.test(function () {
        var oldStuff = makeForm();
        var validator = oldStuff.validator;

        Ink.s('input', oldStuff.form).setAttribute('data-rules', 'required')

        var invalid = this.spy(validator, '_invalid');

        validator.validate();

        equal(invalid.callCount, 1, '_invalid() was called once, because form was invalid')
    }));

    test('_invalid puts a "form-error" class in the form', function () {
        var bag = makeForm()

        bag.validator._invalid();

        ok(Css.hasClassName(bag.form, 'form-error'), 'form-error class added');
    })

    test('extraValidation', function() {
        var extraValidation = sinon.stub();

        var bag = makeForm({
            extraValidation: extraValidation
        });

        extraValidation.returns(true);
        var ret = bag.validator.validate();

        equal(ret, true, 'sanity check: form was valid');

        ok(extraValidation.calledOnce);
        strictEqual(extraValidation.lastCall.thisValue, bag.validator);
        deepEqual(extraValidation.lastCall.args, [{
            'event': undefined,
            validator: bag.validator,
            elements: bag.validator.getElements(),
            errorCount: 0,
        }]);

        extraValidation.returns(false);
        var ret = bag.validator.validate();
        equal(ret, false, 'extraValidation can make a valid form invalid');
    })

    test('setRules', function () {
        var bag = makeForm();

        var elm = bag.validator.getElements()['element_26'][0];
        elm.setRules('foo|bar|baz');
        equal(elm._options.rules, 'foo|bar|baz');
    })

    test('setting data-rules when autoReparse:true should be equivalent to setRules when validate() is called', function () {
        var bag = makeForm({ autoReparse: true });

        var elm = bag.validator.getElements()['element_26'][0];
        elm._element.setAttribute('data-rules', 'foo|bar|baz');
        elm.validate();
        equal(elm._options.rules, 'foo|bar|baz');
    })

    test('removing data-rules should be equivalent to setting element as valid', function () {
        var bag = makeForm({ autoReparse: true })

        bag.form.innerHTML = '<input type="text" name="inpt" data-rules="required" >'

        var elm = bag.validator.getElements()['inpt'][0]
        elm._element.setAttribute('data-rules', 'required')
        ok(!elm.validate(), 'sanity check')
        elm._element.removeAttribute('data-rules')
        ok(elm.validate())
    })

    test('removing element removes it from the getElements result thing.', function () {
        var bag = makeForm()

        bag.form.innerHTML = '<input type="text" name="inpt" data-rules="required" >'

        var elm = bag.validator.getElements()['inpt'][0]

        ok(!bag.validator.validate())

        equal(bag.validator.getElements()['inpt'].length, 1)
        elm._element.parentNode.removeChild(elm._element);
        equal(bag.validator.getElements()['inpt'], undefined)

        ok(bag.validator.validate())
    })

    test('forceInvalid(), unforceInvalid()', function () {
        var bag = makeForm()

        var elm = bag.validator.getElements()['element_26'][0]
        ok(bag.validator.validate(), 'sanity check, form is initially valid')
        elm.forceInvalid('MESSAGE');
        ok(!bag.validator.validate(), 'Not valid any more, because we invalidated an element')
        elm.unforceInvalid();
        ok(bag.validator.validate(), 'Not valid any more, because we invalidated an element')
    })

    test('forceValid(), unforceValid()', function () {
        var bag = makeForm()

        var elm = bag.validator.getElements()['element_26'][0]
        elm.forceInvalid();
        ok(!bag.validator.validate(), 'sanity check, form is initially invalid')
        elm.forceValid();
        ok(bag.validator.validate(), 'Now we\'re valid')
        elm.unforceValid();
        ok(!bag.validator.validate(), 'Not valid any more, because we called unforceValid')
    })
});

