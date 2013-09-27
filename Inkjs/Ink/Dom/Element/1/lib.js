/**
 * @author inkdev AT sapo.pt
 */

Ink.createModule('Ink.Dom.Element', 1, [], function() {

    'use strict';

    /**
     * @module Ink.Dom.Element_1
     */

    /**
     * @class Ink.Dom.Element
     */

    var Element = {

        /**
         * Shortcut for `document.getElementById`
         *
         * @method get
         * @param {String|DOMElement} elm   Either an ID of an element, or an element.
         * @return {DOMElement|null} The DOM element with the given id or null when it was not found
         */
        get: function(elm) {
            if(typeof elm !== 'undefined') {
                if(typeof elm === 'string') {
                    return document.getElementById(elm);
                }
                return elm;
            }
            return null;
        },

        /**
         * Creates a DOM element
         *
         * @method create
         * @param {String} tag        tag name
         * @param {Object} properties  object with properties to be set on the element
         */
        create: function(tag, properties) {
            var el = document.createElement(tag);
            //Ink.extendObj(el, properties);
            for(var property in properties) {
                if(properties.hasOwnProperty(property)) {
                    if(property === 'className') {
                        property = 'class';
                    }
                    el.setAttribute(property, properties[property]);
                }
            }
            return el;
        },

        /**
         * Removes a DOM Element from the DOM
         *
         * @method remove
         * @param {DOMElement} elm  The element to remove
         */
        remove: function(el) {
            var parEl;
            if (el && (parEl = el.parentNode)) {
                parEl.removeChild(el);
            }
        },

        /**
         * Scrolls the window to an element
         *
         * @method scrollTo
         * @param {DOMElement|String} elm  Element where to scroll
         */
        scrollTo: function(elm) {
            elm = this.get(elm);
            if(elm) {
                if (elm.scrollIntoView) {
                    return elm.scrollIntoView();
                }

                var elmOffset = {},
                    elmTop = 0, elmLeft = 0;

                do {
                    elmTop += elm.offsetTop || 0;
                    elmLeft += elm.offsetLeft || 0;

                    elm = elm.offsetParent;
                } while(elm);

                elmOffset = {x: elmLeft, y: elmTop};

                window.scrollTo(elmOffset.x, elmOffset.y);
            }
        },

        /**
         * Gets the top cumulative offset for an element
         *
         * Requires Ink.Dom.Browser
         *
         * @method offsetTop
         * @param {DOMElement|String} elm  target element
         * @return {Number} Offset from the target element to the top of the document
         */
        offsetTop: function(elm) {
            return this.offset(elm)[1];
        },

        /**
         * Gets the left cumulative offset for an element
         *
         * Requires Ink.Dom.Browser
         *
         * @method offsetLeft
         * @param {DOMElement|String} elm  target element
         * @return {Number} Offset from the target element to the left of the document
         */
        offsetLeft: function(elm) {
            return this.offset(elm)[0];
        },

        /**
        * Gets the element offset relative to its closest positioned ancestor
        *
        * @method positionedOffset
        * @param {DOMElement|String} elm  target element
        * @return {Array} Array with the element offsetleft and offsettop relative to the closest positioned ancestor
        */
        positionedOffset: function(element) {
            var valueTop = 0, valueLeft = 0;
            element = this.get(element);
            do {
                valueTop  += element.offsetTop  || 0;
                valueLeft += element.offsetLeft || 0;
                element = element.offsetParent;
                if (element) {
                    if (element.tagName.toLowerCase() === 'body') { break;  }

                    var value = element.style.position;
                    if (!value && element.currentStyle) {
                        value = element.currentStyle.position;
                    }
                    if ((!value || value === 'auto') && typeof getComputedStyle !== 'undefined') {
                        var css = getComputedStyle(element, null);
                        value = css ? css.position : null;
                    }
                    if (value === 'relative' || value === 'absolute') { break;  }
                }
            } while (element);
            return [valueLeft, valueTop];
        },

        /**
         * Gets the cumulative offset for an element
         *
         * Returns the top left position of the element on the page
         *
         * Requires Ink.Dom.Browser
         *
         * @method offset
         * @param {DOMElement|String}   elm     Target element
         * @return {[Number, Number]}   Array with pixel distance from the target element to the top left corner of the document
         */
        offset: function(el) {
            /*jshint boss:true */
            el = Ink.i(el);
            var bProp = ['border-left-width', 'border-top-width'];
            var res = [0, 0];
            var dRes, bRes, parent, cs;
            var getPropPx = this._getPropPx;

            var InkBrowser = Ink.getModule('Ink.Dom.Browser', 1);

            do {
                cs = window.getComputedStyle ? window.getComputedStyle(el, null) : el.currentStyle;
                dRes = [el.offsetLeft | 0, el.offsetTop | 0];

                bRes = [getPropPx(cs, bProp[0]), getPropPx(cs, bProp[1])];
                if( InkBrowser.OPERA ){
                    res[0] += dRes[0];
                    res[1] += dRes[1];
                } else {
                    res[0] += dRes[0] + bRes[0];
                    res[1] += dRes[1] + bRes[1];
                }
                parent = el.offsetParent;
            } while (el = parent);

            bRes = [getPropPx(cs, bProp[0]), getPropPx(cs, bProp[1])];

            if (InkBrowser.GECKO) {
                res[0] += bRes[0];
                res[1] += bRes[1];
            }
            else if( !InkBrowser.OPERA ) {
                res[0] -= bRes[0];
                res[1] -= bRes[1];
            }

            return res;
        },

        /**
         * Gets the scroll of the element
         *
         * @method scroll
         * @param {DOMElement|String} [elm] target element or document.body
         * @returns {Array} offset values for x and y scroll
         */
        scroll: function(elm) {
            elm = elm ? Ink.i(elm) : document.body;
            return [
                ( ( !window.pageXOffset ) ? elm.scrollLeft : window.pageXOffset ),
                ( ( !window.pageYOffset ) ? elm.scrollTop : window.pageYOffset )
            ];
        },

        _getPropPx: function(cs, prop) {
            var n, c;
            var val = cs.getPropertyValue ? cs.getPropertyValue(prop) : cs[prop];
            if (!val) { n = 0; }
            else {
                c = val.indexOf('px');
                if (c === -1) { n = 0; }
                else {
                    n = parseInt(val, 10);
                }
            }

            //console.log([prop, ' "', val, '" ', n].join(''));

            return n;
        },

        /**
         * Alias for offset()
         *
         * @method offset2
         * @deprecated Kept for historic reasons. Use offset() instead.
         */
        offset2: function(el) {
            return this.offset(el);
        },

        /**
         * Verifies the existence of an attribute
         *
         * @method hasAttribute
         * @param {Object} elm   target element
         * @param {String} attr  attribute name
         * @return {Boolean} Boolean based on existance of attribute
         */
        hasAttribute: function(elm, attr){
            return elm.hasAttribute ? elm.hasAttribute(attr) : !!elm.getAttribute(attr);
        },
        /**
         * Inserts a element immediately after a target element
         *
         * @method insertAfter
         * @param {DOMElement}         newElm     element to be inserted
         * @param {DOMElement|String}  targetElm  key element
         */
        insertAfter: function(newElm, targetElm) {
            /*jshint boss:true */
            if (targetElm = this.get(targetElm)) {
                targetElm.parentNode.insertBefore(newElm, targetElm.nextSibling);
            }
        },

        /**
         * Inserts a element at the top of the childNodes of a target element
         *
         * @method insertTop
         * @param {DOMElement}         newElm     element to be inserted
         * @param {DOMElement|String}  targetElm  key element
         */
        insertTop: function(newElm,targetElm) {  // TODO check first child exists
            /*jshint boss:true */
            if (targetElm = this.get(targetElm)) {
                targetElm.insertBefore(newElm, targetElm.firstChild);
            }
        },

        /**
         * Retreives textContent from node
         *
         * @method textContent
         * @param {DOMNode} node from which to retreive text from. Can be any node type.
         * @return {String} the text
         */
        textContent: function(node){
            node = Ink.i(node);
            var text, k, cs, m;

            switch(node && node.nodeType) {
            case 9: /*DOCUMENT_NODE*/
                // IE quirks mode does not have documentElement
                return this.textContent(node.documentElement || node.body && node.body.parentNode || node.body);

            case 1: /*ELEMENT_NODE*/
                text = node.innerText;
                if (typeof text !== 'undefined') {
                    return text;
                }
                /* falls through */
            case 11: /*DOCUMENT_FRAGMENT_NODE*/
                text = node.textContent;
                if (typeof text !== 'undefined') {
                    return text;
                }

                if (node.firstChild === node.lastChild) {
                    // Common case: 0 or 1 children
                    return this.textContent(node.firstChild);
                }

                text = [];
                cs = node.childNodes;
                for (k = 0, m = cs.length; k < m; ++k) {
                    text.push( this.textContent( cs[k] ) );
                }
                return text.join('');

            case 3: /*TEXT_NODE*/
            case 4: /*CDATA_SECTION_NODE*/
                return node.nodeValue;
            }
            return '';
        },

        /**
         * Removes all nodes children and adds the text
         *
         * @method setTextContent
         * @param {DOMNode} node    node to add the text to. Can be any node type.
         * @param {String}  text    text to be appended to the node.
         */
        setTextContent: function(node, text){
            node = Ink.i(node);
            switch(node && node.nodeType)
            {
            case 1: /*ELEMENT_NODE*/
                if ('innerText' in node) {
                    node.innerText = text;
                    break;
                }
                /* falls through */
            case 11: /*DOCUMENT_FRAGMENT_NODE*/
                if ('textContent' in node) {
                    node.textContent = text;
                    break;
                }
                /* falls through */
            case 9: /*DOCUMENT_NODE*/
                while(node.firstChild) {
                    node.removeChild(node.firstChild);
                }
                if (text !== '') {
                    var doc = node.ownerDocument || node;
                    node.appendChild(doc.createTextNode(text));
                }
                break;

            case 3: /*TEXT_NODE*/
            case 4: /*CDATA_SECTION_NODE*/
                node.nodeValue = text;
                break;
            }
        },

        /**
         * Tells if element is a clickable link
         *
         * @method isLink
         * @param {DOMNode} node    node to check if it's link
         * @return {Boolean}
         */
        isLink: function(element){
            var b = element && element.nodeType === 1 && ((/^a|area$/i).test(element.tagName) ||
                element.hasAttributeNS && element.hasAttributeNS('http://www.w3.org/1999/xlink','href'));
            return !!b;
        },

        /**
         * Tells if ancestor is ancestor of node
         *
         * @method isAncestorOf
         * @param {DOMNode} ancestor  ancestor node
         * @param {DOMNode} node      descendant node
         * @return {Boolean}
         */
        isAncestorOf: function(ancestor, node){
            /*jshint boss:true */
            if (!node || !ancestor) {
                return false;
            }
            if (node.compareDocumentPosition) {
                return (ancestor.compareDocumentPosition(node) & 0x10) !== 0;/*Node.DOCUMENT_POSITION_CONTAINED_BY*/
            }
            while (node = node.parentNode){
                if (node === ancestor){
                    return true;
                }
            }
            return false;
        },

        /**
         * Tells if descendant is descendant of node
         *
         * @method descendantOf
         * @param {DOMNode} node        the ancestor
         * @param {DOMNode} descendant  the descendant
         * @return {Boolean} true if 'descendant' is descendant of 'node'
         */
        descendantOf: function(node, descendant){
            return node !== descendant && this.isAncestorOf(node, descendant);
        },

        /**
         * Get first child in document order of node type 1
         * @method firstElementChild
         * @param {DOMNode} elm parent node
         * @return {DOMNode} the element child
         */
        firstElementChild: function(elm){
            if(!elm) {
                return null;
            }
            if ('firstElementChild' in elm) {
                return elm.firstElementChild;
            }
            var child = elm.firstChild;
            while(child && child.nodeType !== 1) {
                child = child.nextSibling;
            }
            return child;
        },

        /**
         * Get last child in document order of node type 1
         * @method lastElementChild
         * @param {DOMNode} elm parent node
         * @return {DOMNode} the element child
         */
        lastElementChild: function(elm){
            if(!elm) {
                return null;
            }
            if ('lastElementChild' in elm) {
                return elm.lastElementChild;
            }
            var child = elm.lastChild;
            while(child && child.nodeType !== 1) {
                child = child.previousSibling;
            }
            return child;
        },

        /**
         * Get the first element sibling after the node
         *
         * @method nextElementSibling
         * @param {DOMNode} node  current node
         * @return {DOMNode|Null} the first element sibling after node or null if none is found
         */
        nextElementSibling: function(node){
            var sibling = null;

            if(!node){ return sibling; }

            if("nextElementSibling" in node){
                return node.nextElementSibling;
            } else {
                sibling = node.nextSibling;

                // 1 === Node.ELEMENT_NODE
                while(sibling && sibling.nodeType !== 1){
                    sibling = sibling.nextSibling;
                }

                return sibling;
            }
        },

        /**
         * Get the first element sibling before the node
         *
         * @method previousElementSibling
         * @param {DOMNode}        node  current node
         * @return {DOMNode|Null} the first element sibling before node or null if none is found
         */
        previousElementSibling: function(node){
            var sibling = null;

            if(!node){ return sibling; }

            if("previousElementSibling" in node){
                return node.previousElementSibling;
            } else {
                sibling = node.previousSibling;

                // 1 === Node.ELEMENT_NODE
                while(sibling && sibling.nodeType !== 1){
                    sibling = sibling.previousSibling;
                }

                return sibling;
            }
        },

        /**
         * Returns the width of the given element, in pixels
         *
         * @method elementWidth
         * @param {DOMElement|string} element target DOM element or target ID
         * @return {Number} the element's width
         */
        elementWidth: function(element) {
            if(typeof element === "string") {
                element = document.getElementById(element);
            }
            return element.offsetWidth;
        },

        /**
         * Returns the height of the given element, in pixels
         *
         * @method elementHeight
         * @param {DOMElement|string} element target DOM element or target ID
         * @return {Number} the element's height
         */
        elementHeight: function(element) {
            if(typeof element === "string") {
                element = document.getElementById(element);
            }
            return element.offsetHeight;
        },

        /**
         * Returns the element's left position in pixels
         *
         * @method elementLeft
         * @param {DOMElement|string} element target DOM element or target ID
         * @return {Number} element's left position
         */
        elementLeft: function(element) {
            if(typeof element === "string") {
                element = document.getElementById(element);
            }
            return element.offsetLeft;
        },

        /**
         * Returns the element's top position in pixels
         *
         * @method elementTop
         * @param {DOMElement|string} element target DOM element or target ID
         * @return {Number} element's top position
         */
        elementTop: function(element) {
            if(typeof element === "string") {
                element = document.getElementById(element);
            }
            return element.offsetTop;
        },

        /**
         * Returns the dimensions of the given element, in pixels
         *
         * @method elementDimensions
         * @param {element} element target element
         * @return {Array} array with element's width and height
         */
        elementDimensions: function(element) {
            element = Ink.i(element);
            return [element.offsetWidth, element.offsetHeight];
        },

        /**
         * Returns the outer (width + margin + padding included) dimensions of an element, in pixels.
         *
         * Requires Ink.Dom.Css
         *
         * @method uterDimensions
         * @param {DOMElement} element Target element
         * @return {Array} Array with element width and height.
         */
        outerDimensions: function (element) {
            var bbox = Element.elementDimensions(element);

            var Css = Ink.getModule('Ink.Dom.Css_1');
            
            return [
                bbox[0] + parseFloat(Css.getStyle(element, 'marginLeft') || 0) + parseFloat(Css.getStyle(element, 'marginRight') || 0),  // w
                bbox[1] + parseFloat(Css.getStyle(element, 'marginTop') || 0) + parseFloat(Css.getStyle(element, 'marginBottom') || 0)  // h
            ];
        },

        /**
         * Check whether an element is inside the viewport
         *
         * @method inViewport
         * @param {DOMElement} element Element to check
         * @param {Boolean} [partial=false] Return `true` even if it is only partially visible.
         * @return {Boolean}
         */
        inViewport: function (element, partial) {
            var rect = Ink.i(element).getBoundingClientRect();
            if (partial) {
                return  rect.bottom > 0                        && // from the top
                        rect.left < Element.viewportWidth()    && // from the right
                        rect.top < Element.viewportHeight()    && // from the bottom
                        rect.right  > 0;                          // from the left
            } else {
                return  rect.top > 0                           && // from the top
                        rect.right < Element.viewportWidth()   && // from the right
                        rect.bottom < Element.viewportHeight() && // from the bottom
                        rect.left  > 0;                           // from the left
            }
        },

        /**
         * Applies the cloneFrom's dimensions to cloneTo
         *
         * @method clonePosition
         * @param {DOMElement} cloneTo    element to be position cloned
         * @param {DOMElement} cloneFrom  element to get the cloned position
         * @return {DOMElement} the element with positionClone
         */
        clonePosition: function(cloneTo, cloneFrom){
            var pos = this.offset(cloneFrom);
            cloneTo.style.left = pos[0]+'px';
            cloneTo.style.top = pos[1]+'px';

            return cloneTo;
        },

        /**
         * Slices off a piece of text at the end of the element and adds the ellipsis
         * so all text fits in the element.
         *
         * @method ellipsizeText
         * @param {DOMElement} element     which text is to add the ellipsis
         * @param {String}     [ellipsis]  String to append to the chopped text
         */
        ellipsizeText: function(element, ellipsis){
            /*jshint boss:true */
            if (element = Ink.i(element)){
                while (element && element.scrollHeight > (element.offsetHeight + 8)) {
                    element.textContent = element.textContent.replace(/(\s+\S+)\s*$/, ellipsis || '\u2026');
                }
            }
        },

        /**
         * Searches up the DOM tree for an element fulfilling the boolTest function (returning trueish)
         *
         * @method findUpwardsHaving
         * @param {HtmlElement} element
         * @param {Function}    boolTest
         * @return {HtmlElement|false} the matched element or false if did not match
         */
        findUpwardsHaving: function(element, boolTest) {
            while (element && element.nodeType === 1) {
                if (boolTest(element)) {
                    return element;
                }
                element = element.parentNode;
            }
            return false;
        },

        /**
         * Śearches up the DOM tree for an element of specified class name
         *
         * @method findUpwardsByClass
         * @param {HtmlElement} element
         * @param {String}      className
         * @returns {HtmlElement|false} the matched element or false if did not match
         */
        findUpwardsByClass: function(element, className) {
            var re = new RegExp("(^|\\s)" + className + "(\\s|$)");
            var tst = function(el) {
                var cls = el.className;
                return cls && re.test(cls);
            };
            return this.findUpwardsHaving(element, tst);
        },

        /**
         * Śearches up the DOM tree for an element of specified tag
         *
         * @method findUpwardsByTag
         * @param {HtmlElement} element
         * @param {String}      tag
         * @returns {HtmlElement|false} the matched element or false if did not match
         */
        findUpwardsByTag: function(element, tag) {
            tag = tag.toUpperCase();
            var tst = function(el) {
                return el.nodeName && el.nodeName.toUpperCase() === tag;
            };
            return this.findUpwardsHaving(element, tst);
        },

        /**
         * Śearches up the DOM tree for an element of specified id
         *
         * @method findUpwardsById
         * @param {HtmlElement} element
         * @param {String}      id
         * @returns {HtmlElement|false} the matched element or false if did not match
         */
        findUpwardsById: function(element, id) {
            var tst = function(el) {
                return el.id === id;
            };
            return this.findUpwardsHaving(element, tst);
        },

        /**
         * Śearches up the DOM tree for an element matching the given selector
         *
         * @method findUpwardsBySelector
         * @param {HtmlElement} element
         * @param {String}      sel
         * @returns {HtmlElement|false} the matched element or false if did not match
         */
        findUpwardsBySelector: function(element, sel) {
            if (typeof Ink.Dom === 'undefined' || typeof Ink.Dom.Selector === 'undefined') {
                throw new Error('This method requires Ink.Dom.Selector');
            }
            var tst = function(el) {
                return Ink.Dom.Selector.matchesSelector(el, sel);
            };
            return this.findUpwardsHaving(element, tst);
        },

        /**
         * Returns trimmed text content of descendants
         *
         * @method getChildrenText
         * @param {DOMElement}  el          element being seeked
         * @param {Boolean}     [removeIt]  whether to remove the found text nodes or not
         * @return {String} text found
         */
        getChildrenText: function(el, removeIt) {
            var node,
                j,
                part,
                nodes = el.childNodes,
                jLen = nodes.length,
                text = '';

            if (!el) {
                return text;
            }

            for (j = 0; j < jLen; ++j) {
                node = nodes[j];
                if (!node) {    continue;   }
                if (node.nodeType === 3) {  // TEXT NODE
                    part = this._trimString( String(node.data) );
                    if (part.length > 0) {
                        text += part;
                        if (removeIt) { el.removeChild(node);   }
                    }
                    else {  el.removeChild(node);   }
                }
            }

            return text;
        },

        /**
         * String trim implementation
         * Used by getChildrenText
         *
         * function _trimString
         * param {String} text
         * return {String} trimmed text
         */
        _trimString: function(text) {
            return (String.prototype.trim) ? text.trim() : text.replace(/^\s*/, '').replace(/\s*$/, '');
        },

        /**
         * Returns the values of a select element
         *
         * @method getSelectValues
         * @param {DomElement|String} select element
         * @return {Array} selected values
         */
        getSelectValues: function (select) {
            var selectEl = Ink.i(select);
            var values = [];
            for (var i = 0; i < selectEl.options.length; ++i) {
                values.push( selectEl.options[i].value );
            }
            return values;
        },


        /* used by fills */
        _normalizeData: function(data) {
            var d, data2 = [];
            for (var i = 0, f = data.length; i < f; ++i) {
                d = data[i];

                if (!(d instanceof Array)) {    // if not array, wraps primitive twice:     val -> [val, val]
                    d = [d, d];
                }
                else if (d.length === 1) {      // if 1 element array:                      [val] -> [val, val]
                    d.push(d[0]);
                }
                data2.push(d);
            }
            return data2;
        },


        /**
         * Fills select element with choices
         *
         * @method fillSelect
         * @param {DomElement|String}  container       select element which will get filled
         * @param {Array}              data            data which will populate the component
         * @param {Boolean}            [skipEmpty]     true to skip empty option
         * @param {String|Number}      [defaultValue]  primitive value to select at beginning
         */
        fillSelect: function(container, data, skipEmpty, defaultValue) {
            var containerEl = Ink.i(container);
            if (!containerEl) {   return; }

            containerEl.innerHTML = '';
            var d, optionEl;

            if (!skipEmpty) {
                // add initial empty option
                optionEl = document.createElement('option');
                optionEl.setAttribute('value', '');
                containerEl.appendChild(optionEl);
            }

            data = this._normalizeData(data);

            for (var i = 0, f = data.length; i < f; ++i) {
                d = data[i];

                optionEl = document.createElement('option');
                optionEl.setAttribute('value', d[0]);
                if (d.length > 2) {
                    optionEl.setAttribute('extra', d[2]);
                }
                optionEl.appendChild( document.createTextNode(d[1]) );

                if (d[0] === defaultValue) {
                    optionEl.setAttribute('selected', 'selected');
                }

                containerEl.appendChild(optionEl);
            }
        },


        /**
         * Select element on steroids - allows the creation of new values
         *
         * @method fillSelect2
         * @param {DomElement|String} ctn select element which will get filled
         * @param {Object} opts
         * @param {Array}                      [opts.data]               data which will populate the component
         * @param {Boolean}                    [opts.skipEmpty]          if true empty option is not created (defaults to false)
         * @param {String}                     [opts.emptyLabel]         label to display on empty option
         * @param {String}                     [opts.createLabel]        label to display on create option
         * @param {String}                     [opts.optionsGroupLabel]  text to display on group surrounding value options
         * @param {String}                     [opts.defaultValue]       option to select initially
         * @param {Function(selEl, addOptFn)}  [opts.onCreate]           callback that gets called once user selects the create option
         */
        fillSelect2: function(ctn, opts) {
            ctn = Ink.i(ctn);
            ctn.innerHTML = '';

            var defs = {
                skipEmpty:              false,
                skipCreate:             false,
                emptyLabel:             'none',
                createLabel:            'create',
                optionsGroupLabel:      'groups',
                emptyOptionsGroupLabel: 'none exist',
                defaultValue:           ''
            };
            if (!opts) {      throw 'param opts is a requirement!';   }
            if (!opts.data) { throw 'opts.data is a requirement!';    }
            opts = Ink.extendObj(defs, opts);

            var optionEl, d;

            var optGroupValuesEl = document.createElement('optgroup');
            optGroupValuesEl.setAttribute('label', opts.optionsGroupLabel);

            opts.data = this._normalizeData(opts.data);

            if (!opts.skipCreate) {
                opts.data.unshift(['$create$', opts.createLabel]);
            }

            if (!opts.skipEmpty) {
                opts.data.unshift(['', opts.emptyLabel]);
            }

            for (var i = 0, f = opts.data.length; i < f; ++i) {
                d = opts.data[i];

                optionEl = document.createElement('option');
                optionEl.setAttribute('value', d[0]);
                optionEl.appendChild( document.createTextNode(d[1]) );

                if (d[0] === opts.defaultValue) {   optionEl.setAttribute('selected', 'selected');  }

                if (d[0] === '' || d[0] === '$create$') {
                    ctn.appendChild(optionEl);
                }
                else {
                    optGroupValuesEl.appendChild(optionEl);
                }
            }

            var lastValIsNotOption = function(data) {
                var lastVal = data[data.length-1][0];
                return (lastVal === '' || lastVal === '$create$');
            };

            if (lastValIsNotOption(opts.data)) {
                optionEl = document.createElement('option');
                optionEl.setAttribute('value', '$dummy$');
                optionEl.setAttribute('disabled', 'disabled');
                optionEl.appendChild(   document.createTextNode(opts.emptyOptionsGroupLabel)    );
                optGroupValuesEl.appendChild(optionEl);
            }

            ctn.appendChild(optGroupValuesEl);

            var addOption = function(v, l) {
                var optionEl = ctn.options[ctn.options.length - 1];
                if (optionEl.getAttribute('disabled')) {
                    optionEl.parentNode.removeChild(optionEl);
                }

                // create it
                optionEl = document.createElement('option');
                optionEl.setAttribute('value', v);
                optionEl.appendChild(   document.createTextNode(l)  );
                optGroupValuesEl.appendChild(optionEl);

                // select it
                ctn.options[ctn.options.length - 1].setAttribute('selected', true);
            };

            if (!opts.skipCreate) {
                ctn.onchange = function() {
                    if ((ctn.value === '$create$') && (typeof opts.onCreate === 'function')) {  opts.onCreate(ctn, addOption);  }
                };
            }
        },


        /**
         * Creates set of radio buttons, returns wrapper
         *
         * @method fillRadios
         * @param {DomElement|String}  insertAfterEl   element which will precede the input elements
         * @param {String}             name            name to give to the form field ([] is added if not as suffix already)
         * @param {Array}              data            data which will populate the component
         * @param {Boolean}            [skipEmpty]     true to skip empty option
         * @param {String|Number}      [defaultValue]  primitive value to select at beginning
         * @param {String}             [splitEl]       name of element to add after each input element (example: 'br')
         * @return {DOMElement} wrapper element around radio buttons
         */
        fillRadios: function(insertAfterEl, name, data, skipEmpty, defaultValue, splitEl) {
            var afterEl = Ink.i(insertAfterEl);
            afterEl = afterEl.nextSibling;
            while (afterEl && afterEl.nodeType !== 1) {
                afterEl = afterEl.nextSibling;
            }
            var containerEl = document.createElement('span');
            if (afterEl) {
                afterEl.parentNode.insertBefore(containerEl, afterEl);
            } else {
                Ink.i(insertAfterEl).appendChild(containerEl);
            }

            data = this._normalizeData(data);

            if (name.substring(name.length - 1) !== ']') {
                name += '[]';
            }

            var d, inputEl;

            if (!skipEmpty) {
                // add initial empty option
                inputEl = document.createElement('input');
                inputEl.setAttribute('type', 'radio');
                inputEl.setAttribute('name', name);
                inputEl.setAttribute('value', '');
                containerEl.appendChild(inputEl);
                if (splitEl) {  containerEl.appendChild( document.createElement(splitEl) ); }
            }

            for (var i = 0; i < data.length; ++i) {
                d = data[i];

                inputEl = document.createElement('input');
                inputEl.setAttribute('type', 'radio');
                inputEl.setAttribute('name', name);
                inputEl.setAttribute('value', d[0]);
                containerEl.appendChild(inputEl);
                containerEl.appendChild( document.createTextNode(d[1]) );
                if (splitEl) {  containerEl.appendChild( document.createElement(splitEl) ); }

                if (d[0] === defaultValue) {
                    inputEl.checked = true;
                }
            }

            return containerEl;
        },


        /**
         * Creates set of checkbox buttons, returns wrapper
         *
         * @method fillChecks
         * @param {DomElement|String}  insertAfterEl   element which will precede the input elements
         * @param {String}             name            name to give to the form field ([] is added if not as suffix already)
         * @param {Array}              data            data which will populate the component
         * @param {Boolean}            [skipEmpty]     true to skip empty option
         * @param {String|Number}      [defaultValue]  primitive value to select at beginning
         * @param {String}             [splitEl]       name of element to add after each input element (example: 'br')
         * @return {DOMElement} wrapper element around checkboxes
         */
        fillChecks: function(insertAfterEl, name, data, defaultValue, splitEl) {
            var afterEl = Ink.i(insertAfterEl);
            afterEl = afterEl.nextSibling;
            while (afterEl && afterEl.nodeType !== 1) {
                afterEl = afterEl.nextSibling;
            }
            var containerEl = document.createElement('span');
            if (afterEl) {
                afterEl.parentNode.insertBefore(containerEl, afterEl);
            } else {
                Ink.i(insertAfterEl).appendChild(containerEl);
            }

            data = this._normalizeData(data);

            if (name.substring(name.length - 1) !== ']') {
                name += '[]';
            }

            var d, inputEl;

            for (var i = 0; i < data.length; ++i) {
                d = data[i];

                inputEl = document.createElement('input');
                inputEl.setAttribute('type', 'checkbox');
                inputEl.setAttribute('name', name);
                inputEl.setAttribute('value', d[0]);
                containerEl.appendChild(inputEl);
                containerEl.appendChild( document.createTextNode(d[1]) );
                if (splitEl) {  containerEl.appendChild( document.createElement(splitEl) ); }

                if (d[0] === defaultValue) {
                    inputEl.checked = true;
                }
            }

            return containerEl;
        },


        /**
         * Returns index of element from parent, -1 if not child of parent...
         *
         * @method parentIndexOf
         * @param {DOMElement}  parentEl  Element to parse
         * @param {DOMElement}  childEl   Child Element to look for
         * @return {Number}
         */
        parentIndexOf: function(parentEl, childEl) {
            var node, idx = 0;
            for (var i = 0, f = parentEl.childNodes.length; i < f; ++i) {
                node = parentEl.childNodes[i];
                if (node.nodeType === 1) {  // ELEMENT
                    if (node === childEl) { return idx; }
                    ++idx;
                }
            }
            return -1;
        },


        /**
         * Returns an array of elements - the next siblings
         *
         * @method nextSiblings
         * @param {String|DomElement} elm element
         * @return {Array} Array of next sibling elements
         */
        nextSiblings: function(elm) {
            if(typeof(elm) === "string") {
                elm = document.getElementById(elm);
            }
            if(typeof(elm) === 'object' && elm !== null && elm.nodeType && elm.nodeType === 1) {
                var elements = [],
                    siblings = elm.parentNode.children,
                    index    = this.parentIndexOf(elm.parentNode, elm);

                for(var i = ++index, len = siblings.length; i<len; i++) {
                    elements.push(siblings[i]);
                }

                return elements;
            }
            return [];
        },


        /**
         * Returns an array of elements - the previous siblings
         *
         * @method previousSiblings
         * @param {String|DomElement} elm element
         * @return {Array} Array of previous sibling elements
         */
        previousSiblings: function(elm) {
            if(typeof(elm) === "string") {
                elm = document.getElementById(elm);
            }
            if(typeof(elm) === 'object' && elm !== null && elm.nodeType && elm.nodeType === 1) {
                var elements    = [],
                    siblings    = elm.parentNode.children,
                    index       = this.parentIndexOf(elm.parentNode, elm);

                for(var i = 0, len = index; i<len; i++) {
                    elements.push(siblings[i]);
                }

                return elements;
            }
            return [];
        },


        /**
         * Returns an array of elements - its siblings
         *
         * @method siblings
         * @param {String|DomElement} elm element
         * @return {Array} Array of sibling elements
         */
        siblings: function(elm) {
            if(typeof(elm) === "string") {
                elm = document.getElementById(elm);
            }
            if(typeof(elm) === 'object' && elm !== null && elm.nodeType && elm.nodeType === 1) {
                var elements   = [],
                    siblings   = elm.parentNode.children;

                for(var i = 0, len = siblings.length; i<len; i++) {
                    if(elm !== siblings[i]) {
                        elements.push(siblings[i]);
                    }
                }

                return elements;
            }
            return [];
        },

        /**
         * fallback to elem.childElementCount
         *
         * @method childElementCount
         * @param {String|DomElement} elm element
         * @return {Number} number of child elements
         */
        childElementCount: function(elm) {
            elm = Ink.i(elm);
            if ('childElementCount' in elm) {
                return elm.childElementCount;
            }
            if (!elm) { return 0; }
            return this.siblings(elm).length + 1;
        },

       /**
        * parses and appends an html string to a container, not destroying its contents
        *
        * @method appendHTML
        * @param {String|DomElement} elm   element
        * @param {String}            html  markup string
        */
        appendHTML: function(elm, html){
            var temp = document.createElement('div');
            temp.innerHTML = html;
            var tempChildren = temp.children;
            for (var i = 0; i < tempChildren.length; i++){
                elm.appendChild(tempChildren[i]);
            }
        },

        /**
         * parses and prepends an html string to a container, not destroying its contents
         *
         * @method prependHTML
         * @param {String|DomElement} elm   element
         * @param {String}            html  markup string
         */
        prependHTML: function(elm, html){
            var temp = document.createElement('div');
            temp.innerHTML = html;
            var first = elm.firstChild;
            var tempChildren = temp.children;
            for (var i = tempChildren.length - 1; i >= 0; i--){
                elm.insertBefore(tempChildren[i], first);
                first = elm.firstChild;
            }
        },

        /**
         * Removes direct children on type text.
         * Useful to remove nasty layout gaps generated by whitespace on the markup.
         *
         * @method removeTextNodeChildren
         * @param  {DOMElement} el
         */
        removeTextNodeChildren: function(el) {
            var prevEl, toRemove, parent = el;
            el = el.firstChild;
            while (el) {
                toRemove = (el.nodeType === 3);
                prevEl = el;
                el = el.nextSibling;
                if (toRemove) {
                    parent.removeChild(prevEl);
                }
            }
        },

        /**
         * Pass an HTML string and receive a documentFragment with the corresponding elements
         * @method htmlToFragment
         * @param  {String} html  html string
         * @return {DocumentFragment} DocumentFragment containing all of the elements from the html string
         */
        htmlToFragment: function(html){
            /*jshint boss:true */
            /*global Range:false */
            if(typeof document.createRange === 'function' && typeof Range.prototype.createContextualFragment === 'function'){
                this.htmlToFragment = function(html){
                    var range;

                    if(typeof html !== 'string'){ return document.createDocumentFragment(); }

                    range = document.createRange();

                    // set the context to document.body (firefox does this already, webkit doesn't)
                    range.selectNode(document.body);

                    return range.createContextualFragment(html);
                };
            } else {
                this.htmlToFragment = function(html){
                    var fragment = document.createDocumentFragment(),
                        tempElement,
                        current;

                    if(typeof html !== 'string'){ return fragment; }

                    tempElement = document.createElement('div');
                    tempElement.innerHTML = html;

                    // append child removes elements from the original parent
                    while(current = tempElement.firstChild){ // intentional assignment
                        fragment.appendChild(current);
                    }

                    return fragment;
                };
            }

            return this.htmlToFragment.call(this, html);
        },

        _camelCase: function(str)
        {
            return str ? str.replace(/-(\w)/g, function (_, $1){
                    return $1.toUpperCase();
            }) : str;
        },

        /**
         * Gets all of the data attributes from an element
         *
         * @method data
         * @param {String|DomElement} selector Element or CSS selector
         * @return {Object} Object with the data-* properties. If no data-attributes are present, an empty object is returned.
        */
        data: function(selector) {
            var el;
            if (typeof selector !== 'object' && typeof selector !== 'string') {
                throw '[Ink.Dom.Element.data] :: Invalid selector defined';
            }

            if (typeof selector === 'object') {
                el = selector;
            }
            else {
                var InkDomSelector = Ink.getModule('Ink.Dom.Selector', 1);
                if (!InkDomSelector) {
                    throw "[Ink.Dom.Element.data] :: This method requires Ink.Dom.Selector - v1";
                }
                el = InkDomSelector.select(selector);
                if (el.length <= 0) {
                    throw "[Ink.Dom.Element.data] :: Can't find any element with the specified selector";
                }
                el = el[0];
            }

            var dataset = {};
            var attrs = el.attributes || [];

            var curAttr, curAttrName, curAttrValue;
            if (attrs) {
                for (var i = 0, total = attrs.length; i < total; ++i) {
                    curAttr = attrs[i];
                    curAttrName = curAttr.name;
                    curAttrValue = curAttr.value;
                    if (curAttrName && curAttrName.indexOf('data-') === 0) {
                        dataset[this._camelCase(curAttrName.replace('data-', ''))] = curAttrValue;
                    }
                }
            }

            return dataset;
        },

        /**
         * @method moveCursorTo
         * @param  {Input|Textarea}  el
         * @param  {Number}          t
         */
        moveCursorTo: function(el, t) {
            if (el.setSelectionRange) {
                el.setSelectionRange(t, t);
                //el.focus();
            }
            else {
                var range = el.createTextRange();
                range.collapse(true);
                range.moveEnd(  'character', t);
                range.moveStart('character', t);
                range.select();
            }
        },

        /**
         * @method pageWidth
         * @return {Number} page width
         */
        pageWidth: function() {
            var xScroll;

            if (window.innerWidth && window.scrollMaxX) {
                xScroll = window.innerWidth + window.scrollMaxX;
            } else if (document.body.scrollWidth > document.body.offsetWidth){
                xScroll = document.body.scrollWidth;
            } else {
                xScroll = document.body.offsetWidth;
            }

            var windowWidth;

            if (window.self.innerWidth) {
                if(document.documentElement.clientWidth){
                    windowWidth = document.documentElement.clientWidth;
                } else {
                    windowWidth = window.self.innerWidth;
                }
            } else if (document.documentElement && document.documentElement.clientWidth) {
                windowWidth = document.documentElement.clientWidth;
            } else if (document.body) {
                windowWidth = document.body.clientWidth;
            }

            if(xScroll < windowWidth){
                return xScroll;
            } else {
                return windowWidth;
            }
        },

        /**
         * @method pageHeight
         * @return {Number} page height
         */
        pageHeight: function() {
            var yScroll;

            if (window.innerHeight && window.scrollMaxY) {
                yScroll = window.innerHeight + window.scrollMaxY;
            } else if (document.body.scrollHeight > document.body.offsetHeight){
                yScroll = document.body.scrollHeight;
            } else {
                yScroll = document.body.offsetHeight;
            }

            var windowHeight;

            if (window.self.innerHeight) {
                windowHeight = window.self.innerHeight;
            } else if (document.documentElement && document.documentElement.clientHeight) {
                windowHeight = document.documentElement.clientHeight;
            } else if (document.body) {
                windowHeight = document.body.clientHeight;
            }

            if(yScroll < windowHeight){
                return windowHeight;
            } else {
                return yScroll;
            }
        },

       /**
         * @method viewportWidth
         * @return {Number} viewport width
         */
        viewportWidth: function() {
            if(typeof window.innerWidth !== "undefined") {
                return window.innerWidth;
            }
            if (document.documentElement && typeof document.documentElement.offsetWidth !== "undefined") {
                return document.documentElement.offsetWidth;
            }
        },

        /**
         * @method viewportHeight
         * @return {Number} viewport height
         */
        viewportHeight: function() {
            if (typeof window.innerHeight !== "undefined") {
                return window.innerHeight;
            }
            if (document.documentElement && typeof document.documentElement.offsetHeight !== "undefined") {
                return document.documentElement.offsetHeight;
            }
        },

        /**
         * @method scrollWidth
         * @return {Number} scroll width
         */
        scrollWidth: function() {
            if (typeof window.self.pageXOffset !== 'undefined') {
                return window.self.pageXOffset;
            }
            if (typeof document.documentElement !== 'undefined' && typeof document.documentElement.scrollLeft !== 'undefined') {
                return document.documentElement.scrollLeft;
            }
            return document.body.scrollLeft;
        },

        /**
         * @method scrollHeight
         * @return {Number} scroll height
         */
        scrollHeight: function() {
            if (typeof window.self.pageYOffset !== 'undefined') {
                return window.self.pageYOffset;
            }
            if (typeof document.documentElement !== 'undefined' && typeof document.documentElement.scrollTop !== 'undefined') {
                return document.documentElement.scrollTop;
            }
            return document.body.scrollTop;
        }
    };

    return Element;

});
