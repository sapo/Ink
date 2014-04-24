Ink.requireModules(['Ink.Dom.FormSerialize_1', 'Ink.Dom.Selector_1'], function (FormSerialize, Selector) {
    function mkForm(whatForm) {
        var form = document.createElement('form')
        form.innerHTML = Ink.ss(whatForm || '.test-form-template')[0].innerHTML;
        document.body.appendChild(form)
        return form
    }


    test('_objToPairs', function () {
        var toPairs = FormSerialize._objToPairs
        deepEqual(toPairs({ foo: 'bar' }), [ ['foo', 'bar'] ])
        deepEqual(toPairs({ foo: ['bar', 'baz'] }), [ ['foo', 'bar'], ['foo', 'baz'] ])
        deepEqual(toPairs({ foo: [] }), [ ['foo', []] ])
    })

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

        deepEqual(FormSerialize.asPairs(form), [
            ['number', '1']
        ]);
    });

    test('Serializing empty <option>s', function () {
        var form = document.createElement('form');
        form.innerHTML = [
            '<select name="number">',
            '</select>'].join('\n');
        
        strictEqual(form.elements.number.value, '', 'sanity check.')

        deepEqual(FormSerialize.serialize(form), {
            number: ''
        });

        deepEqual(FormSerialize.asPairs(form), [
            ['number', '']
        ]);
    });

    test('Serializing unselected <option>s', function () {
        var form = document.createElement('form');
        form.innerHTML = [
            '<select name="number">',
            '<option value="1">one</option>',
            '<option value="2">two</option>',
            '</select>'].join('\n');

        deepEqual(FormSerialize.serialize(form), {
            number: '1'
        });

        deepEqual(FormSerialize.asPairs(form), [
            ['number', '1']
        ]);
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

        deepEqual(FormSerialize.asPairs(form), [
            ['numbers', '1'],
            ['numbers', '3']
        ]);

        form = document.createElement('form');
        form.innerHTML = [
            '<select name="numbers" multiple="multiple">',
            '<option value="1">one</option>',
            '<option value="2">two</option>',
            '</select>'].join('\n');


        deepEqual(FormSerialize.serialize(form), {
            numbers: []
        });

        deepEqual(FormSerialize.asPairs(form), [
        ]);
    });

    test('serializing checkboxes', function () {
        var form = document.createElement('form')
        form.innerHTML = '<input type="checkbox" name="foo" value=1>' +
            '<input type="checkbox" name="foo" checked value=2>' +
            '<input type="checkbox" name="foo" checked value=3>';
        deepEqual(FormSerialize.serialize(form), { foo: ["2", "3"] })
        deepEqual(FormSerialize.asPairs(form), [
            ['foo', '2'],
            ['foo', '3']
        ])
    })

    test('serializing <textarea>s', function () {
        var form = document.createElement('form')
        form.innerHTML = '<textarea name="foo">bar</textarea>'
        deepEqual(FormSerialize.serialize(form), { foo: 'bar' })
    })

    module('fillIn()');

    test('calls _objToPairs with the correct arguments, then _fillInPairs is called with the return value of _objToPairs', sinon.test(function () {
        var form = mkForm();
        var sentinel = [['I am an array of pairs', null]];

        this.stub(FormSerialize, '_objToPairs').returns(sentinel);
        this.stub(FormSerialize, '_fillInPairs');

        FormSerialize.fillIn(form, sentinel)

        ok(FormSerialize._objToPairs.notCalled);

        var sentinelObj = { notAn: ['array of pairs']};
        FormSerialize.fillIn(form, sentinelObj)

        ok(FormSerialize._objToPairs.calledOnce)
        ok(FormSerialize._objToPairs.calledWith(sentinelObj))

        ok(FormSerialize._fillInPairs.calledTwice)
        ok(FormSerialize._fillInPairs.alwaysCalledWith(form, sentinel))
    }));

    module('_fillInPairs (does the dirty work for fillIn())');

    test('example in docs', function () {
        var form = mkForm();

        FormSerialize._fillInPairs(form, [
            ['textfield', 'foobar'],
            ['radio', '2'],
            ['check', '1']
        ]);

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

        FormSerialize._fillInPairs(form, [
            ['sel', '1']
        ]);
        equal(form['sel'].children[0].selected, true)


        form.sel.setAttribute('multiple', 'multiple');
        FormSerialize._fillInPairs(form, [
            ['sel', '1'],
            ['sel', '2']
        ])
        equal(form['sel'].children[0].selected, true)
        equal(form['sel'].children[1].selected, true)

        form.sel.setAttribute('multiple', 'multiple');
        form.sel.children[0].selected = true;
        form.sel.children[1].selected = true;
        FormSerialize.fillIn(form, [
            ['sel', []]
        ])
        equal(form['sel'].children[0].selected, false)
        equal(form['sel'].children[1].selected, false)

        form.sel.setAttribute('multiple', 'multiple');
        form.sel.children[0].selected = true;
        form.sel.children[1].selected = false;
        FormSerialize.fillIn(form, [
        ])
        equal(form['sel'].children[0].selected, true)
        equal(form['sel'].children[1].selected, false)
    });

    test('Ink.warn() called when the number of elements mismatches the number of passed values', sinon.test(function () {
        var form = document.createElement('form')
        var el = '<input name="foo">'
        form.innerHTML = el + el + el;  // (3 "foo" elements)

        this.spy(Ink, 'warn');

        FormSerialize._fillInPairs(form, [
            ['foo', 'val1'],
            ['foo', 'val2'],
            ['foo', 'val3']
        ]);
        ok(Ink.warn.notCalled);

        FormSerialize._fillInPairs(form, [
            ['foo', 'val1'],
            ['foo', 'val2']
        ]);
        ok(Ink.warn.calledOnce);
        ok(Ink.warn.calledWithMatch(/3 inputs/), 'warning message included "3 inputs"')
        ok(Ink.warn.calledWithMatch(/2 values/), 'warning message included "2 values"')
    }));

    module('pairs');

    function mkMultiValueForm() {
        var form = mkForm('.test-form-template-multival')
        form.textfield[0].value = 'foo'
        form.textfield[1].value = 'foo'
        form.radio[1].checked = true
        form['check[]'][1].checked = true
        return form;
    }

    test('_isSerialized()', function () {
        ok(!FormSerialize._isSerialized(null))
        var form = mkMultiValueForm();
        ok(FormSerialize._isSerialized(form.textfield[0]))
        ok(FormSerialize._isSerialized(form.textfield[1]))
        ok(!FormSerialize._isSerialized(form.radio[0]))
        ok(FormSerialize._isSerialized(form.radio[1]))
        ok(!FormSerialize._isSerialized(form['check[]'][0]))
        ok(FormSerialize._isSerialized(form['check[]'][1]))
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
})
