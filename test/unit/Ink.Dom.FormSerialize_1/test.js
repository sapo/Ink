Ink.requireModules(['Ink.Dom.FormSerialize_1'], function (FormSerialize) {
    function mkForm() {
        var form = document.createElement('form')
        form.innerHTML = document.getElementsByClassName('test-form-template')[0].innerHTML
        return form
    }
    test('Testing serialize()', function () {
        var form = mkForm()
        document.body.appendChild(form)
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
        document.body.appendChild(form)
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
})
