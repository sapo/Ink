Ink.requireModules(['Ink.Dom.FormSerialize_1', 'Ink.Dom.Selector_1', 'Ink.Util.Array_1', 'Ink.Dom.Element_1'], function (FormSerialize, Selector, InkArray, InkElement) {
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

        form.removeChild(Ink.s('input:first', form));
        deepEqual(FormSerialize.serialize(form), {
            foo: ['bar']
        })

        Ink.s('input[name="foo[]"]', form).setAttribute('name', 'foo');
        deepEqual(FormSerialize.serialize(form), {
            foo: 'bar'
        });
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
    });

    test('regression: A serialized checkbox is not an array', function () {
        var form = document.createElement('form')
        form.innerHTML = '<input type="checkbox" name="foo" value=1>' +
            '<input type="checkbox" name="foo" checked value=2>';
        deepEqual(FormSerialize.serialize(form), { foo: "2" })
    });

    test('serializing radio buttons', function () {
        var form = document.createElement('form')
        form.innerHTML = '<input type="radio" name="foo" value=1>' +
            '<input type="radio" name="foo" value=2>' +
            '<input type="radio" name="foo" value=3>';

        form.children[0].checked = true;
        form.children[1].checked = false;
        form.children[2].checked = false;

        deepEqual(FormSerialize.serialize(form), { foo: "1" })
        deepEqual(FormSerialize.asPairs(form), [
            ['foo', '1']
        ])
    });

    test('serializing unchecked checkboxes', function () {
        var form = document.createElement('form')
        form.innerHTML = '<input type="checkbox" name="foo" value=1>' +
            '<input type="checkbox" name="foo" value=2>' +
            '<input type="checkbox" name="foo" value=3>';
        deepEqual(FormSerialize.serialize(form), {})
        deepEqual(FormSerialize.asPairs(form), [ ])
    });

    test('serializing unchecked checkboxes with outputUnchecked', function () {
        var form = document.createElement('form')
        //form.innerHTML = '<input type="checkbox" name="foo" value=1>' +
        //    '<input type="checkbox" name="foo" value=2>';
        //deepEqual(FormSerialize.serialize(form, { outputUnchecked: true }), { foo: [null, null] })
        //deepEqual(FormSerialize.asPairs(form, { outputUnchecked: true }), [
        //    ['foo', null],
        //    ['foo', null]
        //])

        form.innerHTML = '<input type="checkbox" name="foo" value=2>';
        deepEqual(FormSerialize.serialize(form, { outputUnchecked: true }), { foo: null })
    });

    test('serializing unselected radiobuttons yields nothing by default', function () {
        var form = document.createElement('form')
        form.innerHTML = '<input type="radio" name="foo" value=1>';
        deepEqual(FormSerialize.serialize(form), {})
        deepEqual(FormSerialize.asPairs(form), [])
    });

    test('serializing unselected radios with outputUnchecked', function () {
        var form = document.createElement('form')
        form.innerHTML = '<input type="radio" name="foo" value=1>' +
            '<input type="radio" name="foo" value=2>';
        deepEqual(FormSerialize.serialize(form, { outputUnchecked: true }), { foo: null })
        deepEqual(FormSerialize.asPairs(form, { outputUnchecked: true }), [
            ['foo', null],
            ['foo', null]
        ])

        form.innerHTML = '<input type="radio" name="foo" value=2>';
        deepEqual(FormSerialize.serialize(form, { outputUnchecked: true }), { foo: null })
    });

    test('serializing <textarea>s', function () {
        var form = document.createElement('form')
        form.innerHTML = '<textarea name="foo">bar</textarea>'
        deepEqual(FormSerialize.serialize(form), { foo: 'bar' })
    })

    module('fillIn()');

    test('calls _objToPairs if necessary, to convert to pairs', sinon.test(function () {
        var form = mkForm();

        this.stub(FormSerialize, '_objToPairs');
        this.stub(FormSerialize, '_fillInPairs');

        // Calling with legit pairs first
        FormSerialize.fillIn(form, [['I am an array of pairs', null]]);

        ok(FormSerialize._objToPairs.notCalled,
            '_objToPairs was not called because fillIn() received a legit list of pairs');

        var sentinelObj = { notAn: ['array of pairs']};
        FormSerialize.fillIn(form, sentinelObj)

        ok(FormSerialize._objToPairs.calledOnce)
        ok(FormSerialize._objToPairs.calledWith(sentinelObj))
    }));

    test('_fillInPairs is called with the return value of _objToPairs', sinon.test(function () {
        var form = mkForm();

        this.stub(FormSerialize, '_objToPairs').returns([['fake', 'pairs']]);
        this.stub(FormSerialize, '_fillInPairs');

        FormSerialize.fillIn(form, {})

        ok(FormSerialize._fillInPairs.calledOnce)
        deepEqual(FormSerialize._fillInPairs.lastCall.args, [form, [[ 'fake', 'pairs']] ])
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

    test('calls _fillInOne with the correct arguments', sinon.test(function () {
        var form = mkForm();
        var spy = this.stub(FormSerialize, '_fillInOne');

        FormSerialize._fillInPairs(form, [
            ['textfield', 2]
        ]);
        ok(spy.calledOnce);
        deepEqual(spy.lastCall.args, ['textfield', [form.textfield], [2]]);
        spy.reset();


        FormSerialize._fillInPairs(form, [
            ['textfield', 1],
            ['textfield', 2]
        ]);
        ok(spy.calledOnce);
        deepEqual(spy.lastCall.args, ['textfield', [form.textfield], [1, 2]]);

        spy.reset();
        FormSerialize._fillInPairs(form, [
            ['notexistfield', 1]
        ]);
        ok(spy.notCalled);

        spy.reset();
        FormSerialize._fillInPairs(form, [
            ['textfield', 1],
            ['check[]', 1],
            ['check[]', 2],
            ['textfield', 2]
        ]);
        ok(spy.calledThrice);

        deepEqual(spy.firstCall.args,  ['textfield', [form.textfield], [1]]);
        deepEqual(spy.secondCall.args[0], 'check[]');
        deepEqual(spy.secondCall.args[1][0], form['check[]'][0]);
        deepEqual(spy.secondCall.args[1][1], form['check[]'][1]);
        deepEqual(spy.secondCall.args[2], [1, 2]);
        deepEqual(spy.thirdCall.args,  ['textfield', [form.textfield], [2]]);
    }))

    test('When elements end in "[]", add it for the user, and still pass the correct element and element name onto _fillInOne', sinon.test(function () {
        var form = mkForm();
        var spy = this.stub(FormSerialize, '_fillInOne');

        FormSerialize._fillInPairs(form, [
            ['check', 1],
            ['check', 2]
        ]);

        FormSerialize._fillInPairs(form, [
            ['check[]', 1],
            ['check[]', 2]
        ]);
        ok(spy.calledTwice);

        InkArray.forEach([spy.firstCall.args, spy.secondCall.args], function (callArgs) {
            equal(callArgs[0], 'check[]');
            strictEqual(callArgs[1][0], form['check[]'][0]);
            strictEqual(callArgs[1][1], form['check[]'][1]);
            deepEqual(callArgs[2], [1, 2]);
        })
    }))

    test('Filling in <select>s', function () {
        var sel = document.createElement('select')
        sel.setAttribute('name', 'sel');

        function selected() {
            return [sel.children[0].selected, sel.children[1].selected,
                sel.children[2].selected];
        }

        sel.appendChild(InkElement.create('option', { value: 1 }));
        sel.appendChild(InkElement.create('option', { value: 2 }));
        sel.appendChild(InkElement.create('option', { value: 3 }));

        FormSerialize._fillInOne('sel', [sel], ['2']);
        equal(sel[1].selected, true);

        sel.setAttribute('multiple', 'multiple');
        FormSerialize._fillInOne('sel', [sel], ['1', '2']);
        deepEqual(selected(), [true, true, false]);

        sel[0].selected =
            sel[1].selected =
            sel[2].selected = true;
        FormSerialize._fillInOne('sel', [sel], [null]);
        deepEqual(selected(), [false, false, false]);

        sel[0].selected =
            sel[1].selected =
            sel[2].selected = true;
        FormSerialize._fillInOne('sel', [sel], []);
        deepEqual(selected(), [false, false, false]);
    });

    module('_fillInOne() (called by _fillInPairs)');

    test('Ink.warn() called when the number of elements mismatches the number of passed values', sinon.test(function () {
        var form = document.createElement('form')
        var el = '<input name="foo">'
        form.innerHTML = el + el + el;  // (3 "foo" elements)

        this.spy(Ink, 'warn');

        FormSerialize._fillInOne('foo', form.foo, ['val1', 'val2', 'val3']);
        ok(Ink.warn.notCalled);

        FormSerialize._fillInOne('foo', form.foo, ['val1', 'val2' /* no val3! */]);
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
