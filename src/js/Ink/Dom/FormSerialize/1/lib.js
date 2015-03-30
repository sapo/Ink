/**
 * @module Ink.Dom.FormSerialize_1
 * Two way serialization of form data and javascript objects.
 * Valid applications are ad hoc AJAX/syndicated submission of forms, restoring form values from server side state, etc.
 */

Ink.createModule('Ink.Dom.FormSerialize', 1, ['Ink.Util.Array_1', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1'], function (InkArray, InkElement, Selector) {
    'use strict';

    // Check whether something is not a string or a DOM element, but still has length.
    function isArrayIsh(obj) {
        return obj != null &&
            (!InkElement.isDOMElement(obj)) &&
            (InkArray.isArray(obj) || (typeof obj !== 'string' && typeof obj.length === 'number'));
    }

    function toArray(obj) {
        if (isArrayIsh(obj)) { return obj; }
        else { return [obj]; }
    }

    /**
     * @namespace Ink.Dom.FormSerialize
     * @static
     **/
    var FormSerialize = {

        /**
         * Serializes a form element into a JS object
         * It turns field *names* (not IDs!) into keys and field values into values.
         *
         * note: Multi-select and checkboxes with multiple values will result in arrays
         *
         * @method serialize
         * @param {DOMElement|String}   form    Form element to extract data
         * @param {Object} [options] Options object, containing:
         * @param {Boolean} [options.outputUnchecked=false] Whether to emit unchecked checkboxes and unselected radio buttons.
         * @return {Object} Map of fieldName -> String|String[]|Boolean
         * @sample Ink_Dom_FormSerialize_serialize.html 
         */
        serialize: function(form, options) {
            options = options || {};
            var out = {};
            var emptyArrayToken = {};  // A hack so that empty select[multiple] elements appear although empty.

            var pairs = this.asPairs(form, { elements: true, emptyArray: emptyArrayToken, outputUnchecked: options.outputUnchecked });
            if (pairs == null) { return pairs; }
            InkArray.forEach(pairs, function (pair) {
                var phpArray = /\[\]$/.test(pair[0]);
                var name = pair[0].replace(/\[\]$/, '');
                var value = pair[1];
                var el = pair[2];

                if (value === emptyArrayToken) {
                    out[name] = [];  // It's an empty select[multiple]
                } else if (!(FormSerialize._resultsInArray(el) || phpArray)) {
                    out[name] = value;
                } else {
                    if (name in out) {
                        if (!(out[name] instanceof Array)) {
                            out[name] = [out[name]];
                        }
                        out[name].push(value);
                    } else if (phpArray) {
                        out[name] = [value];
                    } else {
                        out[name] = value;
                    }
                }
            });

            return out;
        },

        /**
         * Like `serialize`, but returns an array of [fieldName, value] pairs.
         *
         * @method asPairs
         * @param {DOMElement|String} form  Form element
         * @param {Object} [options] Options object, containing:
         * @param {Boolean} [options.elements] Instead of returning an array of [fieldName, value] pairs, return an array of [fieldName, value, fieldElement] triples.
         * @param {Boolean} [options.emptyArray] What to emit as the value of an empty select[multiple]. If you don't pass this option, nothing comes out.
         * @param {Boolean} [options.outputUnchecked=false] Whether to emit unchecked checkboxes and unselected radio buttons.
         * @return {Array} Array of [fieldName, value] pairs.
         **/
        asPairs: function (form, options) {
            var out = [];
            options = options || {};

            function emit(name, val, el) {
                if (options.elements) {
                    out.push([name, val, el]);
                } else {
                    out.push([name, val]);
                }
            }

            function serializeEl(el) {
                var elNodeName = el.nodeName.toLowerCase();
                var elType = (el.type + '').toLowerCase();

                if (elNodeName === 'select' && el.multiple) {
                    var didEmit = false;
                    InkArray.forEach(Selector.select('option:checked', el), function (thisOption) {
                        emit(el.name, thisOption.value, el);
                        didEmit = true;
                    });
                    if (!didEmit && 'emptyArray' in options) {
                        emit(el.name, options.emptyArray, el);
                    }
                } else if (elNodeName === 'input' && (elType === 'checkbox' || elType === 'radio') && options.outputUnchecked) {
                    // It's an empty checkbox and we wouldn't emit it otherwise but the user asked for it using outputUnchecked
                    emit(el.name, null, el);
                } else {
                    emit(el.name, el.value, el);
                }
            }

            if ((form = Ink.i(form))) {
                var inputs = InkArray.filter(form.elements, function (elm) {
                    return FormSerialize._isSerialized(elm, options);
                });
                for (var i = 0, len = inputs.length; i < len; i++) {
                    serializeEl(inputs[i]);
                }
                return out;
            }

            return null;
        },

        /**
         * Sets form elements' values with values from an object
         *
         * Note: You can't set the values of an input with `type="file"` (browser prohibits it)
         *
         * @method fillIn 
         * @param {Element|String} form Form element to be populated
         * @param {Object|Array}   map2 Mapping of fields to values contained in fields. Can be a hash (keys as names, strings or arrays for values), or an array of [name, value] pairs.
         * @return {void}
         * @sample Ink_Dom_FormSerialize_fillIn.html 
         */
        fillIn: function(form, map2) {
            if (!(form = Ink.i(form))) { return null; }

            var pairs;

            if (typeof map2 === 'object' && !isArrayIsh(map2)) {
                pairs = FormSerialize._objToPairs(map2);
            } else if (isArrayIsh(map2)) {
                pairs = map2;
            } else {
                return null;
            }

            return FormSerialize._fillInPairs(form, pairs);
        },

        _objToPairs: function (obj) {
            var pairs = [];
            var val;
            for (var name in obj) if (obj.hasOwnProperty(name)) {
                val = toArray(obj[name]);
                for (var i = 0, len = val.length; i < len; i++) {
                    pairs.push([name, val[i]]);
                }
                if (len === 0) {
                    pairs.push([name, []]);
                }
            }
            return pairs;
        },

        _fillInPairs: function (form, pairs) {
            pairs = InkArray.groupBy(pairs, {
                key: function (pair) { return pair[0].replace(/\[\]$/, ''); },
                adjacentGroups: true
            });

            // For each chunk...
            pairs = InkArray.map(pairs, function (pair) {
                // Join the items in the chunk by concatenating the values together and leaving the names alone
                var values = InkArray.reduce(pair, function (left, right) {
                    return [null, left[1].concat([right[1]])];
                }, [null, []])[1];
                return [pair[0][0], values];
            });

            var name;
            var inputs;
            var values;
            for (var i = 0, len = pairs.length; i < len; i++) {
                name = pairs[i][0];

                if (name in form) {
                    inputs = form[name];
                } else if ((name + '[]') in form) {
                    inputs = form[name + '[]'];
                    name = name + '[]';
                } else {
                    continue;
                }

                inputs = toArray(inputs);
                values = pairs[i][1];

                FormSerialize._fillInOne(name, inputs, values);
            }
        },

        _fillInOne: function (name, inputs, values) {
            var firstOne = inputs[0];
            var firstNodeName = firstOne.nodeName.toLowerCase();
            var firstType = firstOne.getAttribute('type');
            firstType = firstType && firstType.toLowerCase();
            var isSelectMulti = firstNodeName === 'select' && InkElement.hasAttribute(firstOne, 'multiple');

            if (firstType === 'checkbox' || firstType === 'radio') {
                FormSerialize._fillInBoolean(inputs, values, 'checked');
            } else if (isSelectMulti) {
                FormSerialize._fillInBoolean(inputs[0].options, values, 'selected');
            } else {
                if (inputs.length !== values.length) {
                    Ink.warn('Form had ' + inputs.length + ' inputs named "' + name + '", but received ' + values.length + ' values.');
                }

                for (var i = 0, len = Math.min(inputs.length, values.length); i < len; i += 1) {
                    inputs[i].value = values[i];
                }
            }
        },

        _fillInBoolean: function (inputs, values, checkAttr /* 'selected' or 'checked' */) {
            InkArray.forEach(inputs, function (input) {
                var isChecked = InkArray.inArray(input.value, values);
                input[checkAttr] = isChecked;
            });
        },

        /**
         * Whether FormSerialize.serialize() should produce an array when looking at this element.
         * @method _resultsInArray
         * @private
         * @param element
         **/
        _resultsInArray: function (element) {
            var type = element.getAttribute('type');
            var nodeName = element.nodeName.toLowerCase();

            return type === 'checkbox' ||
                (nodeName === 'select' && InkElement.hasAttribute(element, 'multiple'));
        },

        _isSerialized: function (element, options) {
            options = options || {};
            if (!InkElement.isDOMElement(element)) { return false; }
            if (!InkElement.hasAttribute(element, 'name')) { return false; }

            var nodeName = element.nodeName.toLowerCase();

            if (!nodeName || nodeName === 'fieldset') { return false; }

            if (element.type === 'checkbox' || element.type === 'radio') {
                if (options.outputUnchecked) { return true; }
                return !!element.checked;
            }

            return true;
        }
    };

    return FormSerialize;
});
