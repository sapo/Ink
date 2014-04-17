Ink.requireModules(['Ink.Dom.FormSerialize_1'], function (FormSerialize) {
    function mkForm(whatForm) {
        var form = document.createElement('form')
        form.innerHTML = document.getElementsByClassName(whatForm || 'test-form-template')[0].innerHTML
        document.body.appendChild(form)
        return form
    }
    test('Testing serialize()', function () {
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

    test('testing fillIn', function () {
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

    test('_getInputs()', function () {
        var form = mkForm('test-form-template-multival')
        form.textfield[0].value = 'foo'
        form.textfield[1].value = 'foo'
        form.radio[1].checked = true
        form['check[]'][1].checked = true
        deepEqual(FormSerialize._getInputs(form), [
            form.textfield[0],
            form.textfield[1],
            form.radio[1],
            form['check[]'][1],
        ])
        document.body.removeChild(form)
    })

    test('asPairs()', function () {
        var form = mkForm('test-form-template-multival')
        form.textfield[0].value = 'foo'
        form.textfield[1].value = 'foo'
        form.radio[1].checked = true
        form['check[]'][1].checked = true
        equal(typeof FormSerialize.asPairs(form), 'object')
        ok(FormSerialize.asPairs(form).length)
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
