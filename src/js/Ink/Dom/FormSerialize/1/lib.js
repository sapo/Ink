/**
 * @module Ink.Dom.FormSerialize_1
 * Two way serialization of form data and javascript objects.
 * Valid applications are ad hoc AJAX/syndicated submission of forms, restoring form values from server side state, etc.
 */

Ink.createModule('Ink.Dom.FormSerialize', 1, ['Ink.UI.Common_1', 'Ink.Util.Array_1', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1'], function (Common, InkArray, InkElement, Selector) {
    'use strict';

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
            form = Ink.i(form);
            if (!form) { return; }

            var out = {};
            var emptySelectMultiToken = {};

            var pairs = this.asPairs(form, { elements: true, emptySelectMulti: emptySelectMultiToken });
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
            form = Ink.i(form);
            var map = this._getFieldNameInputsMap(form);

            for (var k in map2) if (map2.hasOwnProperty(k)) {
                this._setValuesOfField( map[k], map2[k] );
            }
        },



        _getFieldNameInputsMap: function(formEl) {
            var name, nodeName, el, map = {};
            for (var i = 0, f = formEl.elements.length; i < f; ++i) {
                el = formEl.elements[i];
                name = el.getAttribute('name');
                nodeName = el.nodeName.toLowerCase();
                if (nodeName === 'fieldset') {
                    continue;
                } else if (map[name] === undefined) {
                    map[name] = [el];
                } else {
                    map[name].push(el);
                }
            }
            delete map['null'];
            return map;
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
                (nodeName === 'select' && element.hasAttribute('multiple'));
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
        },


        _valInArray: function(val, arr) {
            for (var i = 0, f = arr.length; i < f; ++i) {
                if (arr[i] === val) {    return true;    }
            }
            return false;
        },



        _setValuesOfField: function(fieldInputs, fieldValues) {
            if (!fieldInputs) {    return;    }
            var nodeName = fieldInputs[0].nodeName.toLowerCase();
            var type = fieldInputs[0].getAttribute('type');
            var i, f, el;

            switch(nodeName) {
                case 'select':
                    if (fieldInputs.length > 1) {    
                        Ink.warn('FormSerialize - Got multiple select elements with same name!');
                    }
                    for (i = 0, f = fieldInputs[0].options.length; i < f; ++i) {
                        el = fieldInputs[0].options[i];
                        el.selected = (fieldValues instanceof Array) ? this._valInArray(el.value, fieldValues) : el.value === fieldValues;
                    }
                    break;
                case 'textarea':
                case 'input':
                    if (type === 'checkbox' || type === 'radio') {
                        for (i = 0, f = fieldInputs.length; i < f; ++i) {
                            el = fieldInputs[i];
                            //el.checked = (fieldValues instanceof Array) ? this._valInArray(el.value, fieldValues) : el.value === fieldValues;
                            el.checked = (fieldValues instanceof Array) ? this._valInArray(el.value, fieldValues) : (fieldInputs.length > 1 ? el.value === fieldValues : !!fieldValues);
                        }
                    }
                    else {
                        if (fieldInputs.length > 1) {
                            Ink.warn('FormSerialize - Got multiple input elements with same name!'); 
                        }

                        if (type !== 'file') {
                            fieldInputs[0].value = fieldValues;
                        }
                    }
                    break;

                default:
                    Ink.warn('FormSerialize - Unsupported element: "' + nodeName + '"!');
            }
        }
    };

    return FormSerialize;
});
