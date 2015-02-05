/*globals equal,test*/
Ink.requireModules(['Ink.UI.TagField_1', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1', 'Ink.Util.Array_1', 'Ink.Dom.Event_1'], function (TagField, InkElement, Selector, InkArray, InkEvent) {
    'use strict';
    var body = document.getElementsByTagName('body')[0];
    /**
     * Runs a test using a local instance of TagField with a fresh input which is removed afterwards.
     * @method tfTest
     * @param name,func regular parameters for QUnit test()
     * @param [options] options for TagField
     */

    function tfTest(name, options, func) {
        test(name, function () {
            if (!func) {
                func = options; options = {};
            }
            var testInput = InkElement.create('input');
            body.appendChild(testInput);
            var tagField = new TagField(testInput, options);
            func(tagField, tagField._viewElm, tagField._element);
            tagField.destroy();
            InkElement.remove(testInput);
        });
    }

    function create(tagName, attrs, parent) {
        var elm = InkElement.create(tagName, attrs);
        (parent || body).appendChild(elm);
        toClear.push(elm);
        return elm;
    }
    var toClear = [];
    function clear() {
        for (var i = 0, len = toClear.length; i < len; i++) {
            InkElement.remove(toClear[i]);
        }
        toClear = [];
    }

    tfTest('_tagsFromMarkup', function (tagField) {
        var par = InkElement.create('select');
        var children = [
            InkElement.create('option'),
            InkElement.create('option'),
            InkElement.create('p')
        ];
        children[0].innerHTML = 'Option 1';
        children[1].innerHTML = 'Option &amp;';
        children[2].innerHTML = 'not an option';
        for (var i = 0, len = children.length; i < len; i++) {
            par.appendChild(children[i]);
        }
        var tags = tagField._tagsFromMarkup(par);
        deepEqual(tags, ['Option 1', 'Option &']);
    });

    tfTest('_readInput', {separator: /[;, ]+/g}, function (tagField) {
        deepEqual(
            tagField._readInput('tag1 tag2,tag3;tag4'),
            ['tag1', 'tag2', 'tag3', 'tag4']);
    });

    tfTest('splitting on input', function (tagField) {
        expect(3);  // Expect one ok(true) plus the function check, and that final check.
        var inpt = tagField._input;
        inpt.value = 'asd'
        ok(tagField._addTag, 'sanity check');
        tagField._addTag = function () {ok(false, 'should not be called now!')}
        tagField._onKeyUp(); // Trigger the change
        tagField._addTag = function () {ok(true, 'should be called now!')}

        tagField._input.value = 'asd,';
        tagField._onKeyUp();
        // Now _addTag is called, otherwise test breaks because of expect(1)
        
        equal(tagField._input.value, '');
    });

    tfTest('adding tags', function (tagField, view, elem) {
        tagField._input.value = 'tag1 tag2,tag3'
        var select = Ink.bind(Selector.select, Selector, '>.ink-tag', view);
        equal(select().length, 0);
        tagField._onKeyUp();
        equal(select().length, 2);  // not tag3, because the user might not have finished typing it.
    });

    test('has necessary tags when starting out', function () {
        var tf1 = new TagField(create('input', {value: 'asd1, asd2,  ,'}));
        var tf2 = new TagField(create('input'), {tags: ['lolcats', 'famous peepz']});
        var tf3 = new TagField(create('input', {value: 'asd1, asd2,  ,'}), {tags: ['lolcats', 'famous peepz']});
        deepEqual(tf1._tags, ['asd1', 'asd2']);
        deepEqual(tf2._tags, ['lolcats', 'famous peepz']);
        deepEqual(tf3._tags, ['lolcats', 'famous peepz', 'asd1', 'asd2']);
        tf1.destroy(); tf2.destroy(); tf3.destroy();
        clear();
    });

    test('tags created from strings on startup', function () {
        var tagField = new TagField(create('input'), {tags: '0asd,   a1sd   as3d;,'});
        deepEqual(tagField._tags, ['0asd', 'a1sd', 'as3d']);
        tagField.destroy();
        clear();
    });

    tfTest('Input tag created for user input', function (tf, view) {
        equal(view.getElementsByTagName('input').length, 1);
    });

    tfTest('Input tag monitored for changes', function (tf, view) {
        view.getElementsByTagName('input')[0].value = 'one two tags';
        tf._onKeyUp();
        equal(Selector.select('>.ink-tag', view).length, 2);
        deepEqual(tf._tags, ['one', 'two']);
        equal(view.getElementsByTagName('input')[0].value, 'tags');
    });

    tfTest('Input tag is placed before the tags', function (tf, view) {
        tf._input.value = 'asd ';
        tf._onKeyUp();
        deepEqual(InkArray.map(Selector.select('>.ink-tag,>input', view), function (elm) {
            return elm.tagName.toLowerCase();
        }), ['span', 'input']);
    });

    tfTest('Tag is removed when "remove" button for it is clicked', function (tf, view) {
        tf._input.value = 'tag1 ';
        tf._onKeyUp();
        InkEvent.fire(Selector.select('>.ink-tag > .remove', view)[0], 'click');
        equal(Selector.select('>.ink-tag', view).length, 0)
        deepEqual(tf._tags, []);
    });

    tfTest('Tag is removed when "remove" button for it is clicked (tricky order and stuff)', function (tf, view) {
        tf._input.value = 'tag1 tag2 tag3 tag4 tag5 tag6 ';
        tf._onKeyUp();

        InkEvent.fire(Selector.select('>.ink-tag > .remove', view)[0], 'click');

        equal(Selector.select('>.ink-tag', view).length, 5)
        deepEqual(tf._tags, ['tag2', 'tag3', 'tag4', 'tag5', 'tag6']);

        InkEvent.fire(Selector.select('>.ink-tag > .remove', view)[3], 'click');

        equal(Selector.select('>.ink-tag', view).length, 4)
        deepEqual(tf._tags, ['tag2', 'tag3', 'tag4', 'tag6']);

        InkEvent.fire(Selector.select('>.ink-tag > .remove', view)[3], 'click');

        equal(Selector.select('>.ink-tag', view).length, 3)
        deepEqual(tf._tags, ['tag2', 'tag3', 'tag4']);
    });

    tfTest('Not allow repeated tags', {allowRepeated: false}, function (tf, view) {
        tf._input.value = 'tag1 tag2 tag2 tag3 ';
        tf._onKeyUp();
        deepEqual(tf._tags, ['tag1', 'tag2', 'tag3']);
    });

    tfTest('Allow repeated tags when settings allow it', {allowRepeated: true}, function (tf, view) {
        tf._input.value = 'tag1 tag2 tag2 tag3 ';
        tf._onKeyUp();
        deepEqual(tf._tags, ['tag1', 'tag2', 'tag2', 'tag3']);
    });

    tfTest('Keep underlying input updated', function (tf) {
        tf._input.value = 'asd basd ';
        tf._onKeyUp();
        equal(tf._element.value, 'asd,basd');
    });

    test('Keep underlying select multi updated', function () {
        var multi = create('select', {multiple: true});
        var child = create('option', {}, multi);
        child.innerHTML = 'tag1';
        var tf = new TagField(multi);
        tf._input.value = 'more tags ';
        tf._onKeyUp();
        equal(Selector.select(
            '>option:contains(tag1),' +
            '>option:contains(more),' +
            '>option:contains(tags)', multi).length, 3);
        equal(Selector.select('>option:checked', multi).length, 3);
        clear();
        tf.destroy();
    });

    tfTest('you can remove the last tag you typed by typing backspace twice', {
        tags: 'tag1 tag2'
    }, function (tf) {
        tf._onKeyDown({which: 8});
        tf._onKeyDown({which: 8});
        equal(tf._element.value, 'tag1');
        deepEqual(tf._tags, ['tag1']);
        equal(tf._viewElm.children.length, 2);
        ok(Selector.select(
            ':contains(tag1) + input').length);
    });
});
