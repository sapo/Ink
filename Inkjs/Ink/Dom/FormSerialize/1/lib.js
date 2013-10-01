/**
 * @module Ink.Dom.FormSerialize
 * @author inkdev AT sapo.pt
 */

Ink.createModule('Ink.Dom.FormSerialize', 1, [], function () {
    'use strict';
    /**
     * Supports serialization of form data to/from javascript Objects.
     *
     * Valid applications are ad hoc AJAX/syndicated submission of forms, restoring form values from server side state, etc.
     *
     * @class Ink.Dom.FormSerialize
     *
     */
    var FormSerialize = {

        /**
         * Serializes a form into an object, turning field names into keys, and field values into values.
         *
         * note: Multi-select and checkboxes with multiple values will yield arrays
         *
         * @method serialize
         * @return {Object} map of fieldName -> String|String[]|Boolean
         * @param {DomElement|String}   form    form element from which the extraction is to occur
         *
         * @example
         *     <form id="frm">
         *         <input type="text" name="field1">
         *         <button type="submit">Submit</button>
         *     </form>
         *     <script type="text/javascript">
         *         Ink.requireModules(['Ink.Dom.FormSerialize_1', 'Ink.Dom.Event_1'], function (FormSerialize, InkEvent) {
         *             InkEvent.observe('frm', 'submit', function (event) {
         *                 var formData = FormSerialize.serialize('frm'); // -> {field1:"123"}
         *                 InkEvent.stop(event);
         *             });
         *         });
         *     </script>
         */
        serialize: function(form) {
            form = Ink.i(form);
            var map = this._getFieldNameInputsMap(form);

            var map2 = {};
            for (var k in map) if (map.hasOwnProperty(k)) {
                if(k !== null) {
                    var tmpK = k.replace(/\[\]$/, '');
                    map2[tmpK] = this._getValuesOfField( map[k] );
                } else {
                    map2[k] = this._getValuesOfField( map[k] );
                }
            }

            delete map2['null'];    // this can occur. if so, delete it...
            return map2;
        },




        /**
         * Sets form elements's values with values given from object
         *
         * One cannot restore the values of an input with `type="file"` (browser prohibits it)
         *
         * @method fillIn 
         * @param {DomElement|String}   form    form element which is to be populated
         * @param {Object}              map2    map of fieldName -> String|String[]|Boolean
         * @example
         *     <form id="frm">
         *         <input type="text" name="field1">
         *         <button type="submit">Submit</button>
         *     </form>
         *     <script type="text/javascript">
         *         Ink.requireModules(['Ink.Dom.FormSerialize_1'], function (FormSerialize) {
         *             var values = {field1: 'CTHULHU'};
         *             FormSerialize.fillIn('frm', values);
         *             // At this point the form is pre-filled with the values above.
         *         });
         *     </script>
         */
        fillIn: function(form, map2) {
            form = Ink.i(form);
            var map = this._getFieldNameInputsMap(form);
            delete map['null']; // this can occur. if so, delete it...

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
            return map;
        },



        _getValuesOfField: function(fieldInputs) {
            var nodeName = fieldInputs[0].nodeName.toLowerCase();
            var type = fieldInputs[0].getAttribute('type');
            var value = fieldInputs[0].value;
            var i, f, j, o, el, m, res = [];

            switch(nodeName) {
                case 'select':
                    for (i = 0, f = fieldInputs.length; i < f; ++i) {
                        res[i] = [];
                        m = fieldInputs[i].getAttribute('multiple');
                        for (j = 0, o = fieldInputs[i].options.length; j < o; ++j) {
                            el = fieldInputs[i].options[j];
                            if (el.selected) {
                                if (m) {
                                    res[i].push(el.value);
                                } else {
                                    res[i] = el.value;
                                    break;
                                }
                            }
                        }
                    }
                    return ((fieldInputs.length > 0 && /\[[^\]]*\]$/.test(fieldInputs[0].getAttribute('name'))) ? res : res[0]);

                case 'textarea':
                case 'input':
                    if (type === 'checkbox' || type === 'radio') {
                        for (i = 0, f = fieldInputs.length; i < f; ++i) {
                            el = fieldInputs[i];
                            if (el.checked) {
                                res.push(    el.value    );
                            }
                        }
                        if (type === 'checkbox') {
                            return (fieldInputs.length > 1) ? res : !!(res.length);
                        }
                        return (fieldInputs.length > 1) ? res[0] : !!(res.length);    // on radios only 1 option is selected at most
                    }
                    else {
                        //if (fieldInputs.length > 1) {    throw 'Got multiple input elements with same name!';    }
                        if(fieldInputs.length > 0 && /\[[^\]]*\]$/.test(fieldInputs[0].getAttribute('name'))) {
                            var tmpValues = [];
                            for(i=0, f = fieldInputs.length; i < f; ++i) {
                                tmpValues.push(fieldInputs[i].value);
                            }
                            return tmpValues;
                        } else {
                            return value;
                        }
                    }
                    break;    // to keep JSHint happy...  (reply to this comment by gamboa: - ROTFL)

                default:
                    //throw 'Unsupported element: "' + nodeName + '"!';
                    return undefined;
            }
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
                    if (fieldInputs.length > 1) {    throw 'Got multiple select elements with same name!';    }
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
                        if (fieldInputs.length > 1) {    throw 'Got multiple input elements with same name!';    }
                        if (type !== 'file') {
                            fieldInputs[0].value = fieldValues;
                        }
                    }
                    break;

                default:
                    throw 'Unsupported element: "' + nodeName + '"!';
            }
        }
    };

    return FormSerialize;
});
