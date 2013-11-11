/*
 * @module Ink.UI.TagField_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule("Ink.UI.TagField","1",["Ink.Dom.Element_1", "Ink.Dom.Event_1", "Ink.Dom.Css_1", "Ink.Dom.Browser_1", "Ink.UI.Droppable_1", "Ink.Util.Array_1", "Ink.Dom.Selector_1"],function( InkElement, InkEvent, Css, Browser, Droppable, InkArray, Selector) {
    /**
     * @class Ink.UI.TagField
     * @version 1
     * @constructor
     * @example
     */

    // Ink.UI.Aux was renamed to Ink.UI.Common on Ink@git
    var Common = Ink.getModule('Ink.UI.Common_1') || Ink.getModule('Ink.UI.Aux_1');

    // InkElement.create on steroids, new on Ink@git
    function createElement(tag, properties) {
        var el = document.createElement(tag);
        //Ink.extendObj(el, properties);
        for(var property in properties) {
            if(properties.hasOwnProperty(property)) {
                if (property in InkElement) {
                    InkElement[property](el, properties[property]);
                } else if (property === 'insertBottom') {
                    properties[property].appendChild(el);
                } else {
                    if(property === 'className' || property === 'class') {
                        el.className = properties.className || properties['class'];
                    } else {
                        el.setAttribute(property, properties[property]);
                    }
                }
            }
        }
        return el;
    };

    var isTruthy = function (val) {return !!val;};

    function TagField(element, options) {
        this.init(element, options);
    }

    TagField.prototype = {
        /**
         * Init function called by the constructor
         * 
         * @method _init
         * @param {String|DOMElement} Selector or DOM Element.
         * @param {Object} [options] Options object.
         * @private
         */
        init: function(element, options) {
            element = this._element = Common.elOrSelector(element);
            var o = this._options = Ink.extendObj({
                tags: [],
                tagQuery: null,
                tagQueryAsync: null,
                allowRepeated: false,
                outSeparator: ',',
                separator: /[,; ]+/g,
                autoSplit: true
            }, options || {}, InkElement.data(element));

            if (typeof o.tags === 'string') {
                o.tags = this._readInput(o.tags);
            }

            Css.addClassName(this._element, 'hide-all');

            this._viewElm = createElement('div', {
                className: 'ink-tagfield',
                insertAfter: this._element
            });

            this._input = createElement('input', {
                type: 'text',
                className: 'new-tag-input',
                insertBottom: this._viewElm
            });

            var tags = [].concat(o.tags, this._tagsFromMarkup(this._element));

            this._tags = [];

            InkArray.each(tags, Ink.bindMethod(this, '_addTag'));

            InkEvent.observe(this._input, 'keyup', Ink.bindEvent(this._onKeyUp, this));
            InkEvent.observe(this._input, 'change', Ink.bindEvent(this._onKeyUp, this));
            InkEvent.observe(this._input, 'keydown', Ink.bindEvent(this._onKeyDown, this));
            InkEvent.observe(this._input, 'blur', Ink.bindEvent(this._onBlur, this));
            InkEvent.observe(this._viewElm, 'click', Ink.bindEvent(this._refocus, this));
        },

        destroy: function () {
            InkElement.remove(this._viewElm);
            Css.removeClassName(this._element, 'hide-all');
        },

        _tagsFromMarkup: function (element) {
            var tagname = element.tagName.toLowerCase();
            if (tagname === 'input') {
                return this._readInput(element.value);
            } else if (tagname === 'select') {
                return InkArray.map(element.getElementsByTagName('option'), function (option) {
                    return InkElement.textContent(option);
                });
            } else {
                throw new Error('Cannot read tags from a ' + tagname + ' tag. Unknown tag');
            }
        },

        _tagsToMarkup: function (tags, element) {
            var tagname = element.tagName.toLowerCase();
            if (tagname === 'input') {
                if (this._options.separator) {
                    element.value = tags.join(this._options.outSeparator);
                }
            } else if (tagname === 'select') {
                element.innerHTML = '';
                InkArray.each(tags, function (tag) {
                    var opt = createElement('option', {selected: 'selected'});
                    InkElement.setTextContent(opt, tag);
                    element.appendChild(opt);
                });
            } else {
                throw new Error('TagField: Cannot read tags from a ' + tagname + ' tag. Unknown tag');
            }
        },

        _addTag: function (tag) {
            if ((!this._options.allowRepeated &&
                    InkArray.inArray(tag, this._tags, tag)) || !tag) {
                return false;
            }
            var elm = createElement('span', {
                className: 'ink-tag',
                setTextContent: tag + ' '
            });

            var remove = createElement('i', {
                className: 'remove icon-remove',
                insertBottom: elm
            });
            InkEvent.observe(remove, 'click', Ink.bindEvent(this._removeTag, this, null));

            var spc = document.createTextNode(' ');

            this._tags.push(tag);
            this._viewElm.insertBefore(elm, this._input);
            this._viewElm.insertBefore(spc, this._input);
            this._tagsToMarkup(this._tags, this._element);
        },

        _readInput: function (text) {
            if (this._options.separator) {
                return InkArray.filter(text.split(this._options.separator), isTruthy);
            } else {
                return [text];
            }
        },

        _onKeyUp: function () {  // TODO control input box size
            if (!this._options.autoSplit) {
                return;
            }
            var split = this._input.value.split(this._options.separator);
            if (split.length <= 1) {
                return;
            }
            var last = split[split.length - 1];
            split = split.splice(0, split.length - 1);
            split = InkArray.filter(split, isTruthy);
            
            InkArray.each(split, Ink.bind(this._addTag, this));
            this._input.value = last;
        },

        _onKeyDown: function (event) {

            if (event.which === 13 && this._input.value) {  // enter key
                this._addTag(this._input.value);
                this._input.value = '';
                InkEvent.stop(event);
                return false;
            } else if (event.which === 8 && !this._input.value) { // backspace key
                if (this._removeConfirm) {
                    this._unsetRemovingVisual(this._tags.length - 1);
                    this._removeTag(this._tags.length - 1);
                    this._removeConfirm = null;
                } else {
                    this._setRemovingVisual(this._tags.length - 1);
                }
            } else {
                if (this._removeConfirm) {  // pressed another key, cancelling removal
                    this._unsetRemovingVisual(this._tags.length - 1);
                }
            }
        },

        _onBlur: function (event) {
            this._addTag(this._input.value);
            this._input.value = '';
        },

        /* For when the user presses backspace.
         * Set the style of the tag so that it seems like it's going to be removed
         * if they press backspace again. */
        _setRemovingVisual: function (tagIndex) {
            var elm = this._viewElm.children[tagIndex];
            Css.addClassName(elm, 'tag-deleting');

            this._removeRemovingVisualTimeout = setTimeout(Ink.bindMethod(this, '_unsetRemovingVisual', tagIndex), 4000);
            InkEvent.observe(this._input, 'blur', Ink.bindMethod(this, '_unsetRemovingVisual', tagIndex));
            this._removeConfirm = true;
        },
        _unsetRemovingVisual: function (tagIndex) {
            var elm = this._viewElm.children[tagIndex];
            if (elm) {
                Css.removeClassName(elm, 'tag-deleting');
                clearTimeout(this._removeRemovingVisualTimeout);
            }
            this._removeConfirm = null;
        },

        _removeTag: function (event) {
            var index;
            if (typeof event === 'object') {  // click event on close button
                var elm = InkEvent.element(event).parentNode;
                index = InkElement.parentIndexOf(this._viewElm, elm);
            } else if (typeof event === 'number') {  // manual removal
                index = event;
            }
            this._tags = InkArray.remove(this._tags, index, 1);
            InkElement.remove(this._viewElm.children[index]);
            this._tagsToMarkup(this._tags, this._element);
        },

        _refocus: function (event) {
            this._input.focus();
            InkEvent.stop(event);
            return false;
        }
    };
    return TagField;
});
