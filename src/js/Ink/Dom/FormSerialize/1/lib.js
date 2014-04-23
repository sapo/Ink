/**
 * @module Ink.Dom.FormSerialize_1
 * Two way serialization of form data and javascript objects.
 * Valid applications are ad hoc AJAX/syndicated submission of forms, restoring form values from server side state, etc.
 */

Ink.createModule('Ink.Dom.FormSerialize', 1, ['Ink.UI.Common_1', 'Ink.Util.Array_1', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1'], function (Common, InkArray, InkElement, Selector) {
    'use strict';

    // Check whether something is not a string or a DOM element, but still has length.
    function isArrayIsh(obj) {
        return obj != null &&
            (!Common.isDOMElement(obj)) &&
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
         * It turns field names into keys and field values into values.
         *
         * note: Multi-select and checkboxes with multiple values will result in arrays
         *
         * @method serialize
         * @param {DOMElement|String}   form    Form element to extract data
         * @return {Object} Map of fieldName -> String|String[]|Boolean
         * @sample Ink_Dom_FormSerialize_serialize.html 
         */
        serialize: function(form) {
            var out = {};
            var emptySelectMultiToken = {};  // A hack so that empty select[multiple] elements appear although empty.

            var pairs = this.asPairs(form, { elements: true, emptySelectMulti: emptySelectMultiToken });
            if (pairs == null) { return pairs; }
            InkArray.forEach(pairs, function (pair) {
                var name = pair[0].replace(/\[\]$/, '');
                var value = pair[1];
                var el = pair[2];

                if (value === emptySelectMultiToken) {
                    out[name] = [];  // It's an empty select[multiple]
                } else if (!FormSerialize._resultsInArray(el)) {
                    out[name] = value;
                } else {
                    out[name] = out[name] || [];
                    out[name].push(value);
                }
            });

            return out;
        },

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
                if (el.nodeName.toLowerCase() === 'select' && el.multiple) {
                    var didEmit = false;
                    InkArray.forEach(Selector.select('option:checked', el), function (thisOption) {
                        emit(el.name, thisOption.value, el);
                        didEmit = true;
                    });
                    if (!didEmit && 'emptySelectMulti' in options) {
                        emit(el.name, options.emptySelectMulti, el);
                    }
                } else {
                    emit(el.name, el.value, el);
                }
            }

            if ((form = Ink.i(form))) {
                var inputs = InkArray.filter(form.elements, FormSerialize._isSerialized);
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
         * @param {DOMElement|String}   form    Form element to be populated
         * @param {Object}              map2    Map of fieldName -> String|String[]|Boolean
         * @sample Ink_Dom_FormSerialize_fillIn.html 
         */
        fillIn: function(form, map2) {
            if (!(form = Ink.i(form))) { return; }

            if (isArrayIsh(map2)) {
                return FormSerialize._fillInPairs(form, map2);
            } else if (map2 && typeof map2 === 'object') {
                return FormSerialize._fillInObj(form, map2);
            } else {
                Ink.error('FormSerialize.fillIn(): An invalid object was passed: ' + map2);
            }
        },

        _fillInObj: function (form, map2) {
            var inputs;
            var values;
            for (var name in map2) if (map2.hasOwnProperty(name)) {
                inputs = toArray(form[name] || form[name + '[]']);
                values = toArray(map2[name] || map2[name.replace(/\[\]$/, '')]);

                FormSerialize._fillInOne(name, inputs, values);
            }
        },

        _fillInPairs: function (form, pairs) {
            throw 'COVER ME TODO LOL'
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
                if (inputs.length > 1) {
                    throw 'COVER ME';
                    Ink.warn('Form had more than one <select> element with [name="' + name + '"] but they have the [multiple] attribute');
                }
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
            if (/\[\]$/.test(element.name)) { return true; }

            var type = element.getAttribute('type');
            var nodeName = element.nodeName.toLowerCase();

            return type === 'checkbox' ||
                (nodeName === 'select' && InkElement.hasAttribute(element, 'multiple'));
        },

        _isSerialized: function (element) {
            if (!Common.isDOMElement(element)) { return false; }
            if (!InkElement.hasAttribute(element, 'name')) { return false; }

            var nodeName = element.nodeName.toLowerCase();

            if (!nodeName || nodeName === 'fieldset') { return false; }

            if (element.type === 'checkbox' || element.type === 'radio') {
                return !!element.checked;
            }

            return true;
        }
    };

    return FormSerialize;
});
