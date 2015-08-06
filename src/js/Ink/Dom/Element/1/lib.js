/**
 * DOM Traversal and manipulation
 * @module Ink.Dom.Element_1
 * @version 1
 */

Ink.createModule('Ink.Dom.Element', 1, [], function() {

    'use strict';

    var createContextualFragmentSupport = (
        typeof document.createRange === 'function' &&
        typeof window.Range.prototype.createContextualFragment === 'function');

    var deleteThisTbodyToken = 'Ink.Dom.Element tbody: ' + Math.random();
    var browserCreatesTbodies = (function () {
        var div = document.createElement('div');
        div.innerHTML = '<table>';
        return div.getElementsByTagName('tbody').length !== 0;
    }());

    function rect(elem){
        var dimensions = {};
        try {
            dimensions = elem.getBoundingClientRect();
        } catch(e){
            dimensions = { top: elem.offsetTop, left: elem.offsetLeft };
        }
        return dimensions;
    }

    /**
     * @namespace Ink.Dom.Element_1
     */

    var InkElement = {

        /**
         * Checks if something is a DOM Element.
         *
         * @method isDOMElement
         * @static
         * @param   {Mixed}     o   The object to be checked.
         * @return  {Boolean}       True if it's a valid DOM Element.
         * @public
         * @example
         *     var el = Ink.s('#element');
         *     if( InkElement.isDOMElement( el ) === true ){
         *         // It is a DOM Element.
         *     } else {
         *         // It is NOT a DOM Element.
         *     }
         */
        isDOMElement: function(o) {
            return o !== null && typeof o === 'object' && 'nodeType' in o && o.nodeType === 1;
        },

        /**
         * Shortcut for `document.getElementById`
         *
         * @method get
         * @param {String|DOMElement} elm   Either an ID of an element, or an element.
         * @return {DOMElement|null} The DOM element with the given id or null when it was not found
         * @public
         * @sample Ink_Dom_Element_1_get.html
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
         * Creates a DOM element.
         *
         * Just a shortcut for `document.createElement(tag)`, but with the second argument you can call additional functions present in Ink.Dom.Element.
         *
         * @method create
         * @param {String} tag        Tag name
         * @param {Object} properties Object with properties to be set on the element. You can also call other functions in Ink.Dom.Element like this
         * @return {Element} The newly created element.
         * @public
         * @sample Ink_Dom_Element_1_create.html
         */
        create: function(tag, properties) {
            var el = document.createElement(tag);
            //Ink.extendObj(el, properties);
            if (properties) {
                for(var property in properties) {
                    if(properties.hasOwnProperty(property)) {
                        if (property in InkElement) {
                            InkElement[property](el, properties[property]);
                        } else {
                            if(property === 'className' || property === 'class') {
                                el.className = properties.className || properties['class'];
                            } else {
                                el.setAttribute(property, properties[property]);
                            }
                        }
                    }
                }
            }
            return el;
        },

        /**
         * Removes a DOM Element
         *
         * @method remove
         * @param {Element} elm The element to remove
         * @return {void}
         * @public
         * @sample Ink_Dom_Element_1_remove.html
         */
        remove: function(elm) {
            elm = Ink.i(elm);
            var parEl;
            if (elm && (parEl = elm.parentNode)) {
                parEl.removeChild(elm);
            }
        },

        /**
         * Scrolls the window to an element
         *
         * @method scrollTo
         * @param {DOMElement|String} elm  Element where to scroll
         * @return {void}
         * @public
         * @sample Ink_Dom_Element_1_scrollTo.html
         */
        scrollTo: function(elm) {
            elm = InkElement.get(elm);
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
         * Gets the top offset of an element
         *
         * @method offsetTop
         * @uses Ink.Dom.Browser
         *
         * @param {DOMElement|String} elm  Target element
         * @return {Number} Offset from the target element to the top of the document.
         * @public
         * @sample Ink_Dom_Element_1_offsetTop.html
         */
        offsetTop: function(elm) {
            return InkElement.offset(elm)[1];
        },

        /**
         * Gets the left offset of an element
         *
         * @method offsetLeft
         * @uses Ink.Dom.Browser
         *
         * @param {DOMElement|String} elm  Target element
         * @return {Number} Offset from the target element to the left of the document
         * @public
         * @sample Ink_Dom_Element_1_offsetLeft.html
         */
        offsetLeft: function(elm) {
            return InkElement.offset(elm)[0];
        },

        /**
        * Gets the relative offset of an element
        *
        * @method positionedOffset
        * @param {Element|String} element Target element
        * @return {Array} Array with the element offsetleft and offsettop relative to the closest positioned ancestor
        * @public
        * @sample Ink_Dom_Element_1_positionedOffset.html
        */
        positionedOffset: function(element) {
            var valueTop = 0, valueLeft = 0;
            element = InkElement.get(element);
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
         * @method offset
         * @uses Ink.Dom.Browser
         *
         * @method offset
         * @param {DOMElement|String}   elm     Target element
         * @return {[Number, Number]}   Array with pixel distance from the target element to the top left corner of the document
         * @public
         * @sample Ink_Dom_Element_1_offset.html
         */
        offset: function(elm) {
            /*jshint boss:true */
            elm = Ink.i(elm);
            var res = [0, 0];
            var doc = elm.ownerDocument,
                docElem = doc.documentElement,
                box = rect(elm),
                body = doc.body,
                clientTop  = docElem.clientTop  || body.clientTop  || 0,
                clientLeft = docElem.clientLeft || body.clientLeft || 0,
                scrollTop  = doc.pageYOffset || docElem.scrollTop  || body.scrollTop,
                scrollLeft = doc.pageXOffset || docElem.scrollLeft || body.scrollLeft,
                top  = box.top  + scrollTop  - clientTop,
                left = box.left + scrollLeft - clientLeft;
            res = [left, top];
            return res;
        },

        /**
         * Gets the scroll of the element
         *
         * @method scroll
         * @param {DOMElement|String} [elm] Target element or document.body
         * @returns {Array} offset values for x and y scroll
         * @public
         * @sample Ink_Dom_Element_1_scroll.html
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
                    n = parseFloat(val, 10);
                }
            }

            //console.log([prop, ' "', val, '" ', n].join(''));

            return n;
        },

        /**
         * Alias for offset()
         *
         * @method offset2
         * @param {Element} el Element to be passed to `offset()`
         * @return {void}
         * @public
         * @deprecated Kept for historic reasons. Use offset() instead.
         */
        offset2: function(el) {
            return InkElement.offset(el);
        },

        /**
         * Checks if an element has an attribute
         *
         * @method hasAttribute
         * @param {Object} elm   Target element
         * @param {String} attr  Attribute name
         * @return {Boolean} Boolean based on existance of attribute
         * @sample Ink_Dom_Element_1_hasAttribute.html
         */
        hasAttribute: function(elm, attr){
            elm = Ink.i(elm);
            return elm.hasAttribute ? elm.hasAttribute(attr) : !!elm.getAttribute(attr);
        },
        /**
         * Inserts an element right after another
         *
         * @method insertAfter
         * @param {DOMElement}         newElm     Element to be inserted
         * @param {DOMElement|String}  targetElm  Key element
         * @return {void}
         * @public
         * @sample Ink_Dom_Element_1_insertAfter.html
         */
        insertAfter: function(newElm, targetElm) {
            /*jshint boss:true */
            if (targetElm = InkElement.get(targetElm)) {
                if (targetElm.nextSibling !== null) {
                    targetElm.parentNode.insertBefore(newElm, targetElm.nextSibling);
                } else {
                    targetElm.parentNode.appendChild(newElm);
                }
            }
        },

        /**
         * Inserts an element before another
         *
         * @method insertBefore
         * @param {DOMElement}         newElm     Element to be inserted
         * @param {DOMElement|String}  targetElm  Key element
         * @return {void}
         * @public
         * @sample Ink_Dom_Element_1_insertBefore.html
         */
        insertBefore: function (newElm, targetElm) {
            /*jshint boss:true */
            if ( (targetElm = InkElement.get(targetElm)) ) {
                targetElm.parentNode.insertBefore(newElm, targetElm);
            }
        },

        /**
         * Inserts an element as the first child of another
         *
         * @method insertTop
         * @param {DOMElement}         newElm     Element to be inserted
         * @param {DOMElement|String}  targetElm  Key element
         * @return {void}
         * @public
         * @sample Ink_Dom_Element_1_insertTop.html
         */
        insertTop: function(newElm,targetElm) {
            /*jshint boss:true */
            if (targetElm = InkElement.get(targetElm)) {
                if (targetElm.firstChild) {
                    targetElm.insertBefore(newElm, targetElm.firstChild);
                } else {
                    targetElm.appendChild(newElm);
                }
            }
        },

        /**
         * Inserts an element as the last child of another
         *
         * @method insertBottom
         * @param {DOMElement}         newElm     Element to be inserted
         * @param {DOMElement|String}  targetElm  Key element
         * @return {void}
         * @public
         * @sample Ink_Dom_Element_1_insertBottom.html
         */
        insertBottom: function(newElm, targetElm) {
            /*jshint boss:true */
            targetElm = Ink.i(targetElm);
            targetElm.appendChild(newElm);
        },

        /**
         * Retrieves textContent from node
         *
         * @method textContent
         * @param {DOMNode} node Where to retreive text from. Can be any node type.
         * @return {String} The text
         * @public
         * @sample Ink_Dom_Element_1_textContent.html
         */
        textContent: function(node){
            node = Ink.i(node);
            var text, k, cs, m;

            switch(node && node.nodeType) {
            case 9: /*DOCUMENT_NODE*/
                // IE quirks mode does not have documentElement
                return InkElement.textContent(node.documentElement || node.body && node.body.parentNode || node.body);

            case 1: /*ELEMENT_NODE*/
                text = ('textContent' in node) ? node.textContent : node.innerText;
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
                    return InkElement.textContent(node.firstChild);
                }

                text = [];
                cs = node.childNodes;
                for (k = 0, m = cs.length; k < m; ++k) {
                    text.push( InkElement.textContent( cs[k] ) );
                }
                return text.join('');

            case 3: /*TEXT_NODE*/
            case 4: /*CDATA_SECTION_NODE*/
                return node.nodeValue;
            }
            return '';
        },

        /**
         * Replaces text content of a DOM Node
         * This method removes any child node previously present
         *
         * @method setTextContent
         * @param {Element} node Target node where the text will be added.
         * @param {String}  text Text to be added on the node.
         * @return {void}
         * @public
         * @sample Ink_Dom_Element_1_setTextContent.html
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
         * Checks if an element is a link
         *
         * @method isLink
         * @param {Element} element Element to check if it's a link.
         * @return {Boolean} Whether the element is a link.
         * @public
         * @sample Ink_Dom_Element_1_isLink.html
         */
        isLink: function(element){
            var b = element && element.nodeType === 1 && ((/^a|area$/i).test(element.tagName) ||
                element.hasAttributeNS && element.hasAttributeNS('http://www.w3.org/1999/xlink','href'));
            return !!b;
        },

        /**
         * Checks if a node is an ancestor of another
         *
         * @method isAncestorOf
         * @param {DOMNode} ancestor  Ancestor node
         * @param {DOMNode} node      Descendant node
         * @return {Boolean} Whether `ancestor` is an ancestor of `node`
         * @public
         * @sample Ink_Dom_Element_1_isAncestorOf.html
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
         * Checks if a node is descendant of another
         *
         * @method descendantOf
         * @param {DOMNode} node        The ancestor
         * @param {DOMNode} descendant  The descendant
         * @return {Boolean} `true` if 'descendant' is descendant of 'node'
         * @public
         * @sample Ink_Dom_Element_1_descendantOf.html
         */
        descendantOf: function(node, descendant){
            return node !== descendant && InkElement.isAncestorOf(node, descendant);
        },

        /**
         * Get first child element of another
         * @method firstElementChild
         * @param {DOMElement} elm Parent node
         * @return {DOMElement} the Element child
         * @public
         * @sample Ink_Dom_Element_1_firstElementChild.html
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
         * Get the last child element of another
         * @method lastElementChild
         * @param {DOMElement} elm Parent node
         * @return {DOMElement} the Element child
         * @public
         * @sample Ink_Dom_Element_1_lastElementChild.html
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
         * Get the first sibling element after the node
         *
         * @method nextElementSibling
         * @param {DOMNode} node  The current node
         * @return {DOMElement|Null} The first sibling element after node or null if none is found
         * @public
         * @sample Ink_Dom_Element_1_nextElementSibling.html 
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
         * Get the first sibling element before the node
         *
         * @method previousElementSibling
         * @param {DOMNode}        node The current node
         * @return {DOMElement|Null} The first element sibling before node or null if none is found
         * @public
         * @sample Ink_Dom_Element_1_previousElementSibling.html 
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
         * Get an element's width in pixels.
         *
         * @method elementWidth
         * @param {DOMElement|String} element Target DOM element or target ID
         * @return {Number} The element's width
         * @public
         * @sample Ink_Dom_Element_1_elementWidth.html 
         */
        elementWidth: function(element) {
            if(typeof element === "string") {
                element = document.getElementById(element);
            }
            return element.offsetWidth;
        },

        /**
         * Get an element's height in pixels.
         *
         * @method elementHeight
         * @param {DOMElement|String} element DOM element or target ID
         * @return {Number} The element's height
         * @public
         * @sample Ink_Dom_Element_1_elementHeight.html 
         */
        elementHeight: function(element) {
            if(typeof element === "string") {
                element = document.getElementById(element);
            }
            return element.offsetHeight;
        },

        /**
         * Deprecated. Alias for offsetLeft()
         *
         * @method elementLeft
         * @param {DOMElement|String}       element     DOM element or target ID
         * @return {Number} Element's left position
         */
        elementLeft: function(element) {
            return InkElement.offsetLeft(element);
        },

        /**
         * Deprecated. Alias for offsetTop()
         *
         * @method elementTop
         * @param {DOMElement|string}   element     Target DOM element or target ID
         * @return {Number} element's top position
         */
        elementTop: function(element) {
            return InkElement.offsetTop(element);
        },

        /**
         * Get an element's dimensions in pixels.
         *
         * @method elementDimensions
         * @param {DOMElement|string}   element     DOM element or target ID
         * @return {Array} Array with element's width and height
         * @sample Ink_Dom_Element_1_elementDimensions.html 
         */
        elementDimensions: function(element) {
            element = Ink.i(element);
            return [element.offsetWidth, element.offsetHeight];
        },

        /**
         * Get the outer dimensions of an element in pixels.
         *
         * @method outerDimensions
         * @uses Ink.Dom.Css
         *
         * @param {DOMElement} element Target element
         * @return {Array} Array with element width and height.
         * @sample Ink_Dom_Element_1_outerDimensions.html 
         */
        outerDimensions: function (element) {
            var bbox = rect(element);

            var Css = Ink.getModule('Ink.Dom.Css_1');
            var getStyle = Ink.bindMethod(Css, 'getStyle', element);

            return [
                bbox.right - bbox.left + parseFloat(getStyle('marginLeft') || 0) + parseFloat(getStyle('marginRight') || 0),  // w
                bbox.bottom - bbox.top + parseFloat(getStyle('marginTop') || 0) + parseFloat(getStyle('marginBottom') || 0)  // h
            ];
        },

        /**
         * Check if an element is inside the viewport
         *
         * @method inViewport
         * @param {DOMElement} element DOM Element
         * @param {Object}  [options]  Options object. If you pass a Boolean value here, it is interpreted as `options.partial`
         * @param {Boolean} [options.partial]=false    Return `true` even if it is only partially visible.
         * @param {Number}  [options.margin]=0         Consider a margin all around the viewport with `opts.margin` width a dead zone.
         * @return {Boolean} Whether the element is inside the viewport.
         * @public
         * @sample Ink_Dom_Element_1_inViewport.html 
         */
        inViewport: function (element, options) {
            var dims = rect(Ink.i(element));
            if (typeof options === 'boolean') {
                options = {partial: options, margin: 0};
            }
            options = options || {};
            options.margin = options.margin || 0
            if (options.partial) {
                return  dims.bottom + options.margin > 0                           && // from the top
                        dims.left   - options.margin < InkElement.viewportWidth()  && // from the right
                        dims.top    - options.margin < InkElement.viewportHeight() && // from the bottom
                        dims.right  + options.margin > 0;                             // from the left
            } else {
                return  dims.top    + options.margin > 0                           && // from the top
                        dims.right  - options.margin < InkElement.viewportWidth()  && // from the right
                        dims.bottom - options.margin < InkElement.viewportHeight() && // from the bottom
                        dims.left   + options.margin > 0;                             // from the left
            }
        },

        /**
         * Check if an element is hidden.
         * Taken from Mootools Element extras ( https://gist.github.com/cheeaun/73342 )
         * Does not take into account visibility:hidden
         * @method isHidden
         * @param {DOMElement} element Element to check
         * @return {Boolean} Whether the element is hidden
         * @sample Ink_Dom_Element_1_isHidden.html 
         */
        isHidden: function (element) {
            var w = element.offsetWidth, 
                h = element.offsetHeight,
                force = (element.tagName.toLowerCase() === 'tr');

            var Css = Ink.getModule('Ink.Dom.Css_1');

            return (w===0 && h===0 && !force) ? true :
                (w!==0 && h!==0 && !force) ? false :
                Css.getStyle(element, 'display').toLowerCase() === 'none';
         },

        /**
         * Check if an element is visible 
         *
         * @method isVisible
         * @uses isHidden
         * @param {DOMElement} element Element to check
         * @return {Boolean} Whether the element is visible
         * @sample Ink_Dom_Element_1_isVisible.html 
         */
        isVisible: function (element) {
            return !this.isHidden(element);
        },

        /**
         * Clones an element's position to another
         *
         * @method clonePosition
         * @param {Element} cloneTo    element to be position cloned
         * @param {Element} cloneFrom  element to get the cloned position
         * @return {Element} The element with positionClone
         * @public
         * @sample Ink_Dom_Element_1_clonePosition.html 
         */
        clonePosition: function(cloneTo, cloneFrom){
            var pos = InkElement.offset(cloneFrom);
            cloneTo.style.left = pos[0]+'px';
            cloneTo.style.top = pos[1]+'px';

            return cloneTo;
        },

        /**
         * Text-overflow: ellipsis emulation
         * Slices off a piece of text at the end of the element and adds the ellipsis so all text fits inside.
         *
         * @method ellipsizeText
         * @param {Element} element             Element to modify text content
         * @param {String}  [ellipsis='\u2026'] String to append to the chopped text
         * @return {void}
         * @public
         */
        ellipsizeText: function(element/*, ellipsis*/){
            if ((element = Ink.i(element))) {
                element.style.overflow = 'hidden';
                element.style.whiteSpace = 'nowrap';
                element.style.textOverflow = 'ellipsis';
            }
        },

        /**
         * Finds the closest ancestor element matching your test function
         *
         * @method findUpwardsHaving
         * @param {Element}     element  Element to base the search from
         * @param {Function}    boolTest Testing function
         * @return {Element|false}  The matched element or false if did not match
         * @public
         * @sample Ink_Dom_Element_1_findUpwardsHaving.html 
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
         * Finds the closest ancestor by class name
         *
         * @method findUpwardsByClass
         * @uses findUpwardsHaving
         * @param {DOMElement}  element     Element to base the search from
         * @param {String}      className   Class name to search
         * @returns {DOMElement|false} The matched element or false if did not match
         * @public
         * @sample Ink_Dom_Element_1_findUpwardsByClass.html 
         */
        findUpwardsByClass: function(element, className) {
            var re = new RegExp("(^|\\s)" + className + "(\\s|$)");
            var tst = function(el) {
                var cls = el.className;
                return cls && re.test(cls);
            };
            return InkElement.findUpwardsHaving(element, tst);
        },

        /**
         * Finds the closest ancestor by tag name
         *
         * @method findUpwardsByTag
         * @param {DOMElement} element  Element to base the search from
         * @param {String}      tag     Tag to search
         * @returns {DOMElement|false} the matched element or false if did not match
         * @sample Ink_Dom_Element_1_findUpwardsByTag.html 
         */
        findUpwardsByTag: function(element, tag) {
            tag = tag.toUpperCase();
            var tst = function(el) {
                return el.nodeName && el.nodeName.toUpperCase() === tag;
            };
            return InkElement.findUpwardsHaving(element, tst);
        },

        /**
         * Finds the closest ancestor by id
         *
         * @method findUpwardsById
         * @param {HtmlElement} element     Element to base the search from
         * @param {String}      id          ID to search
         * @returns {HtmlElement|false} The matched element or false if did not match
         * @sample Ink_Dom_Element_1_findUpwardsById.html 
         */
        findUpwardsById: function(element, id) {
            var tst = function(el) {
                return el.id === id;
            };
            return InkElement.findUpwardsHaving(element, tst);
        },

        /**
         * Finds the closest ancestor by CSS selector
         *
         * @method findUpwardsBySelector
         * @param {HtmlElement} element     Element to base the search from
         * @param {String}      sel         CSS selector
         * @returns {HtmlElement|false} The matched element or false if did not match
         * @sample Ink_Dom_Element_1_findUpwardsBySelector.html 
         */
        findUpwardsBySelector: function(element, sel) {
            var Selector = Ink.getModule('Ink.Dom.Selector', '1');
            if (!Selector) {
                throw new Error('This method requires Ink.Dom.Selector');
            }
            var tst = function(el) {
                return Selector.matchesSelector(el, sel);
            };
            return InkElement.findUpwardsHaving(element, tst);
        },

        /**
         * Gets the trimmed text of an element
         *
         * @method getChildrenText
         * @param {DOMElement}  el          Element to base the search from
         * @param {Boolean}     [removeIt]  Flag to remove the text from the element
         * @return {String} Text found
         * @sample Ink_Dom_Element_1_getChildrenText.html 
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
                    part = InkElement._trimString( String(node.data) );
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
         * Gets value of a select element
         *
         * @method getSelectValues
         * @param {DOMElement|String} select element
         * @return {Array} The selected values
         * @sample Ink_Dom_Element_1_getSelectValues.html 
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
         * Fills a select element with options
         *
         * @method fillSelect
         * @param {DOMElement|String}  container       Select element which will get filled
         * @param {Array}              data            Data to populate the component
         * @param {Boolean}            [skipEmpty]     Flag to skip empty option
         * @param {String|Number}      [defaultValue]  Initial selected value
         * @return {void}
         * @public
         *
         * @sample Ink_Dom_Element_1_fillSelect.html 
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

            data = InkElement._normalizeData(data);

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
         * Creates a set of radio buttons from an array of data
         *
         * @method fillRadios
         * @param {Element|String} insertAfterEl  Element after which the input elements will be created
         * @param {String}         name           Name for the form field ([] is added if not present as a suffix)
         * @param {Array}          data           Data to populate the component
         * @param {Boolean}        [skipEmpty]    Flag to skip creation of empty options
         * @param {String|Number}  [defaultValue] Initial selected value
         * @param {String}         [splitEl]      Name of element to add after each input element (example: 'br')
         * @return {DOMElement} Wrapper element around the radio buttons
         */
        fillRadios: function(insertAfterEl, name, data, skipEmpty, defaultValue, splitEl) {
            insertAfterEl = Ink.i(insertAfterEl);
            var containerEl = document.createElement('span');
            InkElement.insertAfter(containerEl, insertAfterEl);

            data = InkElement._normalizeData(data);

            /*
            if (name.substring(name.length - 1) !== ']') {
                name += '[]';
            }
            */

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
         * Creates set of checkbox buttons
         *
         * @method fillChecks
         * @param {Element|String} insertAfterEl  Element after which the input elements will be created
         * @param {String}         name           Name for the form field ([] is added if not present as a suffix)
         * @param {Array}          data           Data to populate the component
         * @param {String|Number}  [defaultValue] Initial selected value
         * @param {String}         [splitEl]      Name of element to add after each input element (example: 'br')
         * @return {Element} Wrapper element around the checkboxes
         * @public
         */
        fillChecks: function(insertAfterEl, name, data, defaultValue, splitEl) {
            insertAfterEl = Ink.i(insertAfterEl);
            var containerEl = document.createElement('span');
            InkElement.insertAfter(containerEl, insertAfterEl);

            data = InkElement._normalizeData(data);

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
         * Gets the index of an element relative to a parent
         *
         * @method parentIndexOf
         * @param {Element} [parentEl] childEl's parent. Deprecated.
         * @param {Element} childEl    Child Element to look for
         * @return {Number} The index of the childEl inside parentEl. Returns -1 if it's not a direct child
         * @public
         * @sample Ink_Dom_Element_1_parentIndexOf.html 
         */
        parentIndexOf: function(parentEl, childEl) {
            if (!childEl) {
                // one argument form
                childEl = parentEl;
                parentEl = parentEl.parentNode;
            }
            if (!parentEl) { return false; }
            for (var i = 0, f = parentEl.children.length; i < f; ++i) {
                if (parentEl.children[i] === childEl) {
                    return i;
                }
            }
            return false;
        },


        /**
         * Gets the next siblings of an element
         *
         * @method nextSiblings
         * @param {String|DOMElement} elm Element
         * @return {Array} Array of next sibling elements
         * @sample Ink_Dom_Element_1_nextSiblings.html 
         */
        nextSiblings: function(elm) {
            elm = Ink.i(elm);
            if(typeof(elm) === 'object' && elm !== null && elm.nodeType && elm.nodeType === 1) {
                var elements = [],
                    siblings = elm.parentNode.children,
                    index    = InkElement.parentIndexOf(elm.parentNode, elm);

                for(var i = ++index, len = siblings.length; i<len; i++) {
                    elements.push(siblings[i]);
                }

                return elements;
            }
            return [];
        },


        /**
         * Gets the previous siblings of an element
         *
         * @method previousSiblings
         * @param {String|DOMElement} elm Element
         * @return {Array} Array of previous sibling elements
         * @sample Ink_Dom_Element_1_previousSiblings.html 
         */
        previousSiblings: function(elm) {
            elm = Ink.i(elm);
            if(typeof(elm) === 'object' && elm !== null && elm.nodeType && elm.nodeType === 1) {
                var elements    = [],
                    siblings    = elm.parentNode.children,
                    index       = InkElement.parentIndexOf(elm.parentNode, elm);

                for(var i = 0, len = index; i<len; i++) {
                    elements.push(siblings[i]);
                }

                return elements;
            }
            return [];
        },


        /**
         * Gets the all siblings of an element
         *
         * @method siblings
         * @param {String|DOMElement} elm Element
         * @return {Array} Array of sibling elements
         * @sample Ink_Dom_Element_1_siblings.html 
         */
        siblings: function(elm) {
            elm = Ink.i(elm);
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
         * Counts the number of children of an element
         *
         * @method childElementCount
         * @param {String|DOMElement} elm element
         * @return {Number} number of child elements
         * @sample Ink_Dom_Element_1_childElementCount.html 
         */
        childElementCount: function(elm) {
            elm = Ink.i(elm);
            if ('childElementCount' in elm) {
                return elm.childElementCount;
            }
            if (!elm) { return 0; }
            return InkElement.siblings(elm).length + 1;
        },

        _wrapElements: {
            TABLE: function (div, html) {
                /* If we don't create a tbody, IE7 does that for us. Adding a tbody with a random string and then filtering for that random string is the only way to avoid double insertion of tbodies. */
                if (browserCreatesTbodies) {
                    div.innerHTML = "<table>" + html + "<tbody><tr><td>" + deleteThisTbodyToken + "</tr></td></tbody></table>";
                } else {
                    div.innerHTML = "<table>" + html + "</table>";
                }
                return div.firstChild;
            },
            TBODY: function (div, html) {
                div.innerHTML = '<table><tbody>' + html + '</tbody></table>';
                return div.firstChild.getElementsByTagName('tbody')[0];
            },
            THEAD: function (div, html) {
                div.innerHTML = '<table><thead>' + html + '</thead><tbody></tbody></table>';
                return div.firstChild.getElementsByTagName('thead')[0];
            },
            TFOOT: function (div, html) {
                div.innerHTML = '<table><tfoot>' + html + '</tfoot><tbody></tbody></table>';
                return div.firstChild.getElementsByTagName('tfoot')[0];
            },
            TR: function (div, html) {
                div.innerHTML = '<table><tbody><tr>' + html + '</tr></tbody></table>';
                return div.firstChild.firstChild.firstChild;
            }
        },

        /**
         * Gets a wrapper DIV with a certain HTML content to be inserted inside another element.
         * This is necessary for appendHTML,prependHTML functions, because they need a container element to copy the children from.
         *
         * Works around IE table quirks
         * @method _getWrapper
         * @private
         * @param elm
         * @param html
         */
        _getWrapper: function (elm, html) {
            var nodeName = elm.nodeName && elm.nodeName.toUpperCase();
            var wrapper = document.createElement('div');
            var wrapFunc = InkElement._wrapElements[nodeName];

            if ( !wrapFunc ) {
                wrapper.innerHTML = html;
                return wrapper;
            }
            // special cases
            wrapper = wrapFunc(wrapper, html);
            // worst case: tbody auto-creation even when our HTML has a tbody.
            if (browserCreatesTbodies && nodeName === 'TABLE') {
                // terrible case. Deal with tbody creation too.
                var tds = wrapper.getElementsByTagName('td');
                for (var i = 0, len = tds.length; i < len; i++) {
                    if (tds[i].innerHTML === deleteThisTbodyToken) {
                        var tbody = tds[i].parentNode.parentNode;
                        tbody.parentNode.removeChild(tbody);
                    }
                }
            }
            return wrapper;
        },

        /**
         * Appends HTML to an element.
         * This method parses the html string and doesn't modify its contents
         *
         * @method appendHTML
         * @param {String|DOMElement} elm   Element
         * @param {String}            html  Markup string
         * @return {void}
         * @public
         * @sample Ink_Dom_Element_1_appendHTML.html 
         */
        appendHTML: function(elm, html){
            elm = Ink.i(elm);
            if(elm !== null) {
                var wrapper = InkElement._getWrapper(elm, html);
                while (wrapper.firstChild) {
                    elm.appendChild(wrapper.firstChild);
                }
            }
        },

        /**
         * Prepends HTML to an element.
         * This method parses the html string and doesn't modify its contents
         *
         * @method prependHTML
         * @param {String|Element} elm   Element
         * @param {String}         html  Markup string to prepend
         * @return {void}
         * @public
         * @sample Ink_Dom_Element_1_prependHTML.html 
         */
        prependHTML: function(elm, html){
            elm = Ink.i(elm);
            if(elm !== null) {
                var wrapper = InkElement._getWrapper(elm, html);
                while (wrapper.lastChild) {
                    elm.insertBefore(wrapper.lastChild, elm.firstChild);
                }
            }
        },

        /**
         * Sets the inner HTML of an element.
         *
         * @method setHTML
         * @param {String|DOMElement} elm   Element
         * @param {String}            html  Markup string
         * @return {void}
         * @public
         * @sample Ink_Dom_Element_1_setHTML.html 
         */
        setHTML: function (elm, html) {
            elm = Ink.i(elm);
            if(elm !== null) {
                try {
                    elm.innerHTML = html;
                } catch (e) {
                    // Tables in IE7
                    InkElement.clear( elm );

                    InkElement.appendHTML(elm, html);
                }
            }
        },

        /**
         * Wraps an element inside a container.
         *
         * The container may or may not be in the document yet.
         *
         * @method wrap
         * @param {String|Element} target    Element to be wrapped
         * @param {String|Element} container Element to wrap the target
         * @return {Element} Container element
         * @public
         * @sample Ink_Dom_Element_1_wrap.html 
         *
         * @example
         * before:
         *
         *     <div id="target"></div>
         *
         * call this function to wrap #target with a wrapper div.
         *
         *     InkElement.wrap('target', InkElement.create('div', {id: 'container'});
         * 
         * after: 
         *
         *     <div id="container"><div id="target"></div></div>
         */
        wrap: function (target, container) {
            target = Ink.i(target);
            container = Ink.i(container);
            
            var nextNode = target.nextSibling;
            var parent = target.parentNode;

            container.appendChild(target);

            if (nextNode !== null) {
                parent.insertBefore(container, nextNode);
            } else {
                parent.appendChild(container);
            }

            return container;
        },

        /**
         * Places an element outside a wrapper.
         *
         * @method unwrap
         * @param {DOMElement}  elem                The element you're trying to unwrap. This should be an ancestor of the wrapper.
         * @param {String}      [wrapperSelector]   CSS Selector for the ancestor. Use this if your wrapper is not the direct parent of elem.
         * @return {void}
         * @sample Ink_Dom_Element_1_unwrap.html 
         *
         * @example
         *
         * When you have this:
         *
         *      <div id="wrapper">
         *          <div id="unwrapMe"></div>
         *      </div>
         *
         * If you do this:
         *
         *      InkElement.unwrap('unwrapMe');
         *
         * You get this:
         *
         *      <div id="unwrapMe"></div>
         *      <div id="wrapper"></div>
         *      
         **/
        unwrap: function (elem, wrapperSelector) {
            elem = Ink.i(elem);
            var wrapper;
            if (typeof wrapperSelector === 'string') {
                wrapper = InkElement.findUpwardsBySelector(elem, wrapperSelector);
            } else if (typeof wrapperSelector === 'object' && wrapperSelector.tagName) {
                wrapper = InkElement.findUpwardsHaving(elem, function (ancestor) {
                    return ancestor === wrapperSelector;
                });
            } else {
                wrapper = elem.parentNode;
            }
            if (!wrapper || !wrapper.parentNode) { return; }

            InkElement.insertBefore(elem, wrapper);
        },

        /**
         * Replaces an element with another.
         *
         * @method replace
         * @param {Element} element       The element to be replaced.
         * @param {Element} replacement   The new element.
         * @return {void}
         * @public
         * @sample Ink_Dom_Element_1_replace.html 
         *
         * @example
         *       var newelement1 = InkElement.create('div');
         *       // ...
         *       replace(Ink.i('element1'), newelement1);
         */
        replace: function (element, replacement) {
            element = Ink.i(element);
            if(element !== null) {
                element.parentNode.replaceChild(replacement, element);
            }
        },

        /**
         * Removes direct text children.
         * Useful to remove nasty layout gaps generated by whitespace on the markup.
         *
         * @method removeTextNodeChildren
         * @param  {Element} el          Element to remove text from
         * @return {void}
         * @public
         * @sample Ink_Dom_Element_1_removeTextNodeChildren.html 
         */
        removeTextNodeChildren: function(el) {
            el = Ink.i(el);
            if(el !== null) {
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
            }
        },

        /**
         * Creates a documentFragment from an HTML string.
         *
         * @method htmlToFragment
         * @param  {String} html  HTML string
         * @return {DocumentFragment} DocumentFragment containing all of the elements from the html string
         * @sample Ink_Dom_Element_1_htmlToFragment.html 
         */
        htmlToFragment: (createContextualFragmentSupport ?
            function(html){
                var range;

                if(typeof html !== 'string'){ return document.createDocumentFragment(); }

                range = document.createRange();

                // set the context to document.body (firefox does this already, webkit doesn't)
                range.selectNode(document.body);

                return range.createContextualFragment(html);
            } : function (html) {
                var fragment = document.createDocumentFragment(),
                    tempElement,
                    current;

                if(typeof html !== 'string'){ return fragment; }

                tempElement = document.createElement('div');
                tempElement.innerHTML = html;

                // append child removes elements from the original parent
                while( (current = tempElement.firstChild) ){ // intentional assignment
                    fragment.appendChild(current);
                }

                return fragment;
            }),

        _camelCase: function(str)
        {
            return str ? str.replace(/-(\w)/g, function (_, $1){
                return $1.toUpperCase();
            }) : str;
        },

        /**
         * Gets data attributes from an element
         *
         * @method data
         * @param {String|DOMElement} selector Element or CSS selector
         * @return {Object} Object with the data-* properties. If no data-attributes are present, an empty object is returned.
         * @sample Ink_Dom_Element_1_data.html 
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
                    throw "[Ink.Dom.Element.data] :: this method requires Ink.Dom.Selector - v1";
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
                        dataset[InkElement._camelCase(curAttrName.replace('data-', ''))] = curAttrValue;
                    }
                }
            }

            return dataset;
        },

        clear : function( elem , child ) {
            while ( ( child = elem.lastChild ) ) {
                elem.removeChild( child );
            }
        } ,

        /**
         * Move the cursor on an input or textarea element.
         * @method moveCursorTo
         * @param  {Element}    el  Input or Textarea element
         * @param  {Number}     t   Index of the character to move the cursor to
         * @return {void}
         * @public
         * @sample Ink_Dom_Element_1_moveCursorTo.html 
         */
        moveCursorTo: function(el, t) {
            el = Ink.i(el);
            if(el !== null) {
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
            }
        },

        /**
         * Get the page's width.
         * @method pageWidth
         * @return {Number} Page width in pixels
         * @sample Ink_Dom_Element_1_pageWidth.html 
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
         * Get the page's height.
         * @method pageHeight
         * @return {Number} Page height in pixels
         * @sample Ink_Dom_Element_1_pageHeight.html 
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
         * Get the viewport's width.
         * @method viewportWidth
         * @return {Number} Viewport width in pixels
         * @sample Ink_Dom_Element_1_viewportWidth.html 
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
         * Get the viewport's height.
         * @method viewportHeight
         * @return {Number} Viewport height in pixels
         * @sample Ink_Dom_Element_1_viewportHeight.html 
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
         * Returns how much pixels the page was scrolled from the left side of the document.
         * @method scrollWidth
         * @return {Number} Scroll width
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
         * Returns how much pixels the page was scrolled from the top of the document.
         * @method scrollHeight
         * @return {Number} Scroll height
         */
        scrollHeight: function() {
            if (typeof window.self.pageYOffset !== 'undefined') {
                return window.self.pageYOffset;
            }
            if (typeof document.body !== 'undefined' && typeof document.body.scrollTop !== 'undefined' && typeof document.documentElement !== 'undefined' && typeof document.documentElement.scrollTop !== 'undefined') {
                return document.body.scrollTop || document.documentElement.scrollTop;
            }
            if (typeof document.documentElement !== 'undefined' && typeof document.documentElement.scrollTop !== 'undefined') {
                return document.documentElement.scrollTop;
            }
            return document.body.scrollTop;
        }
    };

    return InkElement;

});
