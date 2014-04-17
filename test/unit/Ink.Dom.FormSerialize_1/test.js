Ink.requireModules(['Ink.Dom.FormSerialize_1', 'Ink.Dom.Selector_1'], function (FormSerialize, Selector) {
    function mkForm(whatForm) {
        var form = document.createElement('form')
        form.innerHTML = Ink.ss(whatForm || '.test-form-template')[0].innerHTML;
        document.body.appendChild(form)
        return form
    }

    module('serialize()');

    test('Example in docs', function () {
        var form = mkForm()
        form.textfield.value = 'foo'
        form.radio[1].checked = true
        form['check[]'][0].checked = true
        form['check[]'][1].checked = true
        deepEqual(FormSerialize.serialize(form), {
            textfield: 'foo',
            radio: '2',
            check: ['1', '2']
        })
        document.body.removeChild(form)
    })

    test('Multiple input[type="text"] with name ending in "[]"', function () {
        var form = document.createElement('form');
        form.innerHTML = '<input type="text" value="bar" name="foo[]">' +
            '<input type="text" value="bar" name="foo[]">';

        deepEqual(FormSerialize.serialize(form), {
            foo: ['bar', 'bar']
        })
    })

    test('Serializing <option>s', function () {
        var form = document.createElement('form');
        form.innerHTML = [
            '<select name="number">',
            '<option value="1" selected="selected">one</option>',
            '<option value="2">two</option>',
            '</select>'].join('\n');
        deepEqual(FormSerialize.serialize(form), {
            number: '1'
        });
    });

    test('Serializing <option multiple>s', function () {
        var form = document.createElement('form');
        form.innerHTML = [
            '<select name="numbers" multiple="multiple">',
            '<option value="1" selected="selected">one</option>',
            '<option value="2">two</option>',
            '<option value="3" selected="selected">three</option>',
            '</select>'].join('\n');
        deepEqual(FormSerialize.serialize(form), {
            numbers: ['1', '3']
        });

        form = document.createElement('form');
        form.innerHTML = [
            '<select name="numbers" multiple="multiple">',
            '<option value="1">one</option>',
            '<option value="2">two</option>',
            '<option value="3" selected="selected">three</option>',
            '</select>'].join('\n');
        deepEqual(FormSerialize.serialize(form), {
            numbers: ['3']
        });
    });

    test('serializing <textarea>s', function () {
        var form = document.createElement('form')
        form.innerHTML = '<textarea name="foo">bar</textarea>'
        deepEqual(FormSerialize.serialize(form), { foo: 'bar' })
    })

    // TODO Ink.warn() is called when multiple elements share same name and using serialize(), advises to use asPairs

    module('fillIn()');

    test('example in docs', function () {
        var form = mkForm();
        var toFillForm = {
            textfield: 'foobar',
            radio: "2",
            "check[]": ["1"]
        };
        FormSerialize.fillIn(form, toFillForm);
        equal(form.textfield.value, 'foobar')
        equal(form.radio[0].checked, false)
        equal(form.radio[1].checked, true)
        equal(form['check[]'][0].checked, true)
        equal(form['check[]'][1].checked, false)
        document.body.removeChild(form)
    });

    test('Filling in <select>s', function () {
        var form = document.createElement('form')
        form.innerHTML = '<select name="sel">' +
            '<option value="1"></option>' +
            '<option value="2"></option>' +
            '</select>'

        FormSerialize.fillIn(form, { sel: '1' });
        equal(form['sel'].children[0].selected, true)

        form.sel.setAttribute('multiple', 'multiple');
        FormSerialize.fillIn(form, { sel: ['1', '2'] })
        equal(form['sel'].children[0].selected, true)
        equal(form['sel'].children[1].selected, true)

        form.sel.setAttribute('multiple', 'multiple');
        FormSerialize.fillIn(form, { sel: ['1'] })
        equal(form['sel'].children[0].selected, true)
        equal(form['sel'].children[1].selected, false)
    });

    module('pairs');

    function mkMultiValueForm() {
        var form = mkForm('.test-form-template-multival')
        form.textfield[0].value = 'foo'
        form.textfield[1].value = 'foo'
        form.radio[1].checked = true
        form['check[]'][1].checked = true
        return form;
    }

    test('_getInputs()', function () {
        var form = mkMultiValueForm();
        deepEqual(FormSerialize._getInputs(form), [
            form.textfield[0],
            form.textfield[1],
            form.radio[1],
            form['check[]'][1]
        ])
        document.body.removeChild(form)
    })

    test('asPairs()', function () {
        var form = mkMultiValueForm();
        deepEqual(FormSerialize.asPairs(form), [
            ['textfield', 'foo'],
            ['textfield', 'foo'],
            ['radio', '2'],
            ['check[]', '2']
        ])
        document.body.removeChild(form)
    })

    // TODO fillIn works with pairs
})
