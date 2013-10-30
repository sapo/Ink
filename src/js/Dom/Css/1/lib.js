/**
 * @author inkdev AT sapo.pt
 */

Ink.createModule( 'Ink.Dom.Css', 1, [], function() {

    'use strict';

    /**
     * @module Ink.Dom.Css_1
     */

    /**
     * @class Ink.Dom.Css
     * @static
     */

    var DomCss = {
        /**
         * adds or removes a class to the given element according to addRemState
         *
         * @method addRemoveClassName
         * @param {DOMElement|string}   elm          DOM element or element id
         * @param {string}              className    class name to add or remove.
         * @param {boolean}             addRemState  Whether to add or remove. `true` to add, `false` to remove.
         *
         * @example
         *      Ink.requireModules(['Ink.Dom.Css_1'], function (Css) {
         *          Css.addRemoveClassName(myElm, 'classss', true);  // Adds the `classss` class.
         *          Css.addRemoveClassName(myElm, 'classss', false);  // Removes the `classss` class.
         *      });
         */
        addRemoveClassName: function(elm, className, addRemState) {
            if (addRemState) {
                return this.addClassName(elm, className);
            }
            this.removeClassName(elm, className);
        },

        /**
         * add a class to a given element
         *
         * @method addClassName
         * @param {DOMElement|String}  elm        DOM element or element id
         * @param {String}             className
         */
        addClassName: function(elm, className) {
            elm = Ink.i(elm);
            if (elm && className) {
                if (typeof elm.classList !== "undefined"){
                    elm.classList.add(className);
                }
                else if (!this.hasClassName(elm, className)) {
                    elm.className += (elm.className ? ' ' : '') + className;
                }
            }
        },

        /**
         * removes a class from a given element
         *
         * @method removeClassName
         * @param {DOMElement|String} elm        DOM element or element id
         * @param {String}            className
         */
        removeClassName: function(elm, className) {
            elm = Ink.i(elm);
            if (elm && className) {
                if (typeof elm.classList !== "undefined"){
                    elm.classList.remove(className);
                } else {
                    if (typeof elm.className === "undefined") {
                        return false;
                    }
                    var elmClassName = elm.className,
                        re = new RegExp("(^|\\s+)" + className + "(\\s+|$)");
                    elmClassName = elmClassName.replace(re, ' ');
                    elmClassName = elmClassName.replace(/^\s+/, '').replace(/\s+$/, '');

                    elm.className = elmClassName;
                }
            }
        },

        /**
         * Alias to addRemoveClassName. Utility function, saves many if/elses.
         *
         * @method setClassName
         * @param {DOMElement|String}  elm        DOM element or element id
         * @param {String}             className
         * @param {Boolean}            add        true to add, false to remove
         */
        setClassName: function(elm, className, add) {
            this.addRemoveClassName(elm, className, add || false);
        },

        /**
         * @method hasClassName
         * @param {DOMElement|String}  elm        DOM element or element id
         * @param {String}             className
         * @return {Boolean} true if a given class is applied to a given element
         */
        hasClassName: function(elm, className) {
            elm = Ink.i(elm);
            if (elm && className) {
                if (typeof elm.classList !== "undefined"){
                    return elm.classList.contains(className);
                }
                else {
                    if (typeof elm.className === "undefined") {
                        return false;
                    }
                    var elmClassName = elm.className;

                    if (typeof elmClassName.length === "undefined") {
                        return false;
                    }

                    if (elmClassName.length > 0) {
                        if (elmClassName === className) {
                            return true;
                        }
                        else {
                            var re = new RegExp("(^|\\s)" + className + "(\\s|$)");
                            if (re.test(elmClassName)) {
                                return true;
                            }
                        }
                    }
                }
            }
            return false;
        },

        /**
         * Add and removes the class from the element with a timeout, so it blinks
         *
         * @method blinkClass
         * @param {DOMElement|String}  elm        DOM element or element id
         * @param {String}             className  class name
         * @param {Boolean}            timeout    timeout in ms between adding and removing, default 100 ms
         * @param {Boolean}            negate     is true, class is removed then added
         */
        blinkClass: function(element, className, timeout, negate){
            element = Ink.i(element);
            this.addRemoveClassName(element, className, !negate);
            setTimeout(Ink.bind(function() {
                this.addRemoveClassName(element, className, negate);
            }, this), Number(timeout) || 100);
            /*
            var _self = this;
            setTimeout(function() {
                    console.log(_self);
                _self.addRemoveClassName(element, className, negate);
            }, Number(timeout) || 100);
            */
        },

        /**
         * Add or remove a class name from a given element
         *
         * @method toggleClassName
         * @param {DOMElement|String}  elm        DOM element or element id
         * @param {String}             className  class name
         * @param {Boolean}            forceAdd   forces the addition of the class if it doesn't exists
         */
        toggleClassName: function(elm, className, forceAdd) {
            if (elm && className){
                if (typeof elm.classList !== "undefined"){
                    elm = Ink.i(elm);
                    if (elm !== null){
                        elm.classList.toggle(className);
                    }
                    return true;
                }
            }

            if (typeof forceAdd !== 'undefined') {
                if (forceAdd === true) {
                    this.addClassName(elm, className);
                }
                else if (forceAdd === false) {
                    this.removeClassName(elm, className);
                }
            } else {
                if (this.hasClassName(elm, className)) {
                    this.removeClassName(elm, className);
                }
                else {
                    this.addClassName(elm, className);
                }
            }
        },

        /**
         * sets the opacity of given client a given element
         *
         * @method setOpacity
         * @param {DOMElement|String}  elm    DOM element or element id
         * @param {Number}             value  allows 0 to 1(default mode decimal) or percentage (warning using 0 or 1 will reset to default mode)
         */
        setOpacity: function(elm, value) {
            elm = Ink.i(elm);
            if (elm !== null){
                var val = 1;

                if (!isNaN(Number(value))){
                    if      (value <= 0) {   val = 0;           }
                    else if (value <= 1) {   val = value;       }
                    else if (value <= 100) { val = value / 100; }
                    else {                   val = 1;           }
                }

                if (typeof elm.style.opacity !== 'undefined') {
                    elm.style.opacity = val;
                }
                else {
                    elm.style.filter = "alpha(opacity:"+(val*100|0)+")";
                }
            }
        },

        /**
         * Converts a css property name to a string in camelcase to be used with CSSStyleDeclaration.
         * @method _camelCase
         * @private
         * @param {String} str  String to convert
         * @return {String} Converted string
         */
        _camelCase: function(str) {
            return str ? str.replace(/-(\w)/g, function (_, $1){
                return $1.toUpperCase();
            }) : str;
        },


        /**
         * Gets the value for an element's style attribute
         *
         * @method getStyle
         * @param {DOMElement|String}  elm    DOM element or element id
         * @param {String}             style  Which css attribute to fetch
         * @return Style value
         */
         getStyle: function(elm, style) {
             elm = Ink.i(elm);
             if (elm !== null) {
                 style = style === 'float' ? 'cssFloat': this._camelCase(style);

                 var value = elm.style[style];

                 if (window.getComputedStyle && (!value || value === 'auto')) {
                     var css = window.getComputedStyle(elm, null);

                     value = css ? css[style] : null;
                 }
                 else if (!value && elm.currentStyle) {
                      value = elm.currentStyle[style];
                      if (value === 'auto' && (style === 'width' || style === 'height')) {
                        value = elm["offset" + style.charAt(0).toUpperCase() + style.slice(1)] + "px";
                      }
                 }

                 if (style === 'opacity') {
                     return value ? parseFloat(value, 10) : 1.0;
                 }
                 else if (style === 'borderTopWidth'   || style === 'borderBottomWidth' ||
                          style === 'borderRightWidth' || style === 'borderLeftWidth'       ) {
                      if      (value === 'thin') {      return '1px';   }
                      else if (value === 'medium') {    return '3px';   }
                      else if (value === 'thick') {     return '5px';   }
                 }

                 return value === 'auto' ? null : value;
             }
         },


        /**
         * Adds CSS rules to an element's style attribute.
         *
         * @method setStyle
         * @param {DOMElement|String}  elm    DOM element or element id
         * @param {String}             style  Which css attribute to set
         *
         * @example
         *     <a href="#" class="change-color">Change his color</a>
         *     <p class="him">"He" is me</p>
         *     <script type="text/javascript">
         *         Ink.requireModules(['Ink.Dom.Css_1', 'Ink.Dom.Event_1', 'Ink.Dom.Selector_1'], function (Css, InkEvent, Selector) {
         *             var btn = Selector.select('.change-color')[0];
         *             var other = Selector.select('.him')[0];
         *             InkEvent.observe(btn, 'click', function () {
         *                 Css.setStyle(other, 'background-color: black');
         *                 Css.setStyle(other, 'color: white');
         *             });
         *         });
         *     </script>
         *
         */
        setStyle: function(elm, style) {
            elm = Ink.i(elm);
            if (elm !== null) {
                if (typeof style === 'string') {
                    elm.style.cssText += '; '+style;

                    if (style.indexOf('opacity') !== -1) {
                        this.setOpacity(elm, style.match(/opacity:\s*(\d?\.?\d*)/)[1]);
                    }
                }
                else {
                    for (var prop in style) {
                        if (style.hasOwnProperty(prop)){
                            if (prop === 'opacity') {
                                this.setOpacity(elm, style[prop]);
                            }
                            else {
                                if (prop === 'float' || prop === 'cssFloat') {
                                    if (typeof elm.style.styleFloat === 'undefined') {
                                        elm.style.cssFloat = style[prop];
                                    }
                                    else {
                                        elm.style.styleFloat = style[prop];
                                    }
                                } else {
                                    elm.style[prop] = style[prop];
                                }
                            }
                        }
                    }
                }
            }
        },


        /**
         * Makes an element visible
         *
         * @method show
         * @param {DOMElement|String}  elm                   DOM element or element id
         * @param {String}             forceDisplayProperty  Css display property to apply on show
         */
        show: function(elm, forceDisplayProperty) {
            elm = Ink.i(elm);
            if (elm !== null) {
                elm.style.display = (forceDisplayProperty) ? forceDisplayProperty : '';
            }
        },

        /**
         * Hides an element
         *
         * @method hide
         * @param {DOMElement|String}  elm  DOM element or element id
         */
        hide: function(elm) {
            elm = Ink.i(elm);
            if (elm !== null) {
                elm.style.display = 'none';
            }
        },

        /**
         * shows or hides according to param show
         *
         * @method showHide
         * @param {DOMElement|String}  elm          DOM element or element id
         * @param {boolean}            [show=false] Whether to show or hide `elm`.
         */
        showHide: function(elm, show) {
            elm = Ink.i(elm);
            if (elm) {
                elm.style.display = show ? '' : 'none';
            }
        },

        /**
         * Shows or hides an element depending on current state
         * @method toggle
         * @param {DOMElement|String}  elm        DOM element or element id
         * @param {Boolean}            forceShow  Forces showing if element is hidden
         */
        toggle: function(elm, forceShow) {
            elm = Ink.i(elm);
            if (elm !== null) {
                if (typeof forceShow !== 'undefined') {
                    if (forceShow === true) {
                        this.show(elm);
                    } else {
                        this.hide(elm);
                    }
                } else {
                    if (elm.style.display === 'none') {
                        this.show(elm);
                    }
                    else {
                        this.hide(elm);
                    }
                }
            }
        },

        _getRefTag: function(head){
            if (head.firstElementChild) {
                return head.firstElementChild;
            }

            for (var child = head.firstChild; child; child = child.nextSibling){
                if (child.nodeType === 1){
                    return child;
                }
            }
            return null;
        },

        /**
         * Adds css style tags to the head section of a page
         *
         * @method appendStyleTag
         * @param {String}  selector  The css selector for the rule
         * @param {String}  style     The content of the style rule
         * @param {Object}  options   Options for the tag
         *    @param {String}  [options.type]   file type
         *    @param {Boolean} [options.force]  if true, style tag will be appended to end of head
         */
        appendStyleTag: function(selector, style, options){
            options = Ink.extendObj({
                type: 'text/css',
                force: false
            }, options || {});

            var styles = document.getElementsByTagName("style"),
                oldStyle = false, setStyle = true, i, l;

            for (i=0, l=styles.length; i<l; i++) {
                oldStyle = styles[i].innerHTML;
                if (oldStyle.indexOf(selector) >= 0) {
                    setStyle = false;
                }
            }

            if (setStyle) {
                var defStyle = document.createElement("style"),
                    head = document.getElementsByTagName("head")[0],
                    refTag = false, styleStr = '';

                defStyle.type  = options.type;

                styleStr += selector +" {";
                styleStr += style;
                styleStr += "} ";

                if (typeof defStyle.styleSheet !== "undefined") {
                    defStyle.styleSheet.cssText = styleStr;
                } else {
                    defStyle.appendChild(document.createTextNode(styleStr));
                }

                if (options.force){
                    head.appendChild(defStyle);
                } else {
                    refTag = this._getRefTag(head);
                    if (refTag){
                        head.insertBefore(defStyle, refTag);
                    }
                }
            }
        },

        /**
         * Adds a link tag for a stylesheet to the head section of a page
         *
         * @method appendStylesheet
         * @param {String}  path     File path
         * @param {Object}  options  Options for the tag
         *    @param {String}   [options.media='screen']    media type
         *    @param {String}   [options.type='text/css']   file type
         *    @param {Boolean}  [options.force=false]       if true, tag will be appended to end of head
         */
        appendStylesheet: function(path, options){
            options = Ink.extendObj({
                media: 'screen',
                type: 'text/css',
                force: false
            }, options || {});

            var refTag,
                style = document.createElement("link"),
                head = document.getElementsByTagName("head")[0];

            style.media = options.media;
            style.type = options.type;
            style.href = path;
            style.rel = "Stylesheet";

            if (options.force){
                head.appendChild(style);
            }
            else {
                refTag = this._getRefTag(head);
                if (refTag){
                    head.insertBefore(style, refTag);
                }
            }
        },

        /**
         * Loads CSS via LINK element inclusion in HEAD (skips append if already there)
         *
         * Works similarly to appendStylesheet but:
         *   a) supports all browsers;
         *   b) supports optional callback which gets invoked once the CSS has been applied
         *
         * @method appendStylesheetCb
         * @param {String}            cssURI      URI of the CSS to load, if empty ignores and just calls back directly
         * @param {Function(cssURI)}  [callback]  optional callback which will be called once the CSS is loaded
         */
        _loadingCSSFiles: {},
        _loadedCSSFiles:  {},
        appendStylesheetCb: function(url, callback) {
            if (!url) {
                return callback(url);
            }

            if (this._loadedCSSFiles[url]) {
                return callback(url);
            }

            var cbs = this._loadingCSSFiles[url];
            if (cbs) {
                return cbs.push(callback);
            }

            this._loadingCSSFiles[url] = [callback];

            var linkEl = document.createElement('link');
            linkEl.type = 'text/css';
            linkEl.rel  = 'stylesheet';
            linkEl.href = url;

            var headEl = document.getElementsByTagName('head')[0];
            headEl.appendChild(linkEl);

            var imgEl = document.createElement('img');
            /*
            var _self = this;
            (function(_url) {
                imgEl.onerror = function() {
                    //var url = this;
                    var url = _url;
                    _self._loadedCSSFiles[url] = true;
                    var callbacks = _self._loadingCSSFiles[url];
                    for (var i = 0, f = callbacks.length; i < f; ++i) {
                        callbacks[i](url);
                    }
                    delete _self._loadingCSSFiles[url];
                };
            })(url);
            */
            imgEl.onerror = Ink.bindEvent(function(event, _url) {
                //var url = this;
                var url = _url;
                this._loadedCSSFiles[url] = true;
                var callbacks = this._loadingCSSFiles[url];
                for (var i = 0, f = callbacks.length; i < f; ++i) {
                    callbacks[i](url);
                }
                delete this._loadingCSSFiles[url];
            }, this, url);
            imgEl.src = url;
        },

        /**
         * Converts decimal to hexadecimal values, for use with colors
         *
         * @method decToHex
         * @param {String} dec Either a single decimal value,
         * an rgb(r, g, b) string or an Object with r, g and b properties
         * @return Hexadecimal value
         */
        decToHex: function(dec) {
            var normalizeTo2 = function(val) {
                if (val.length === 1) {
                    val = '0' + val;
                }
                val = val.toUpperCase();
                return val;
            };

            if (typeof dec === 'object') {
                var rDec = normalizeTo2(parseInt(dec.r, 10).toString(16));
                var gDec = normalizeTo2(parseInt(dec.g, 10).toString(16));
                var bDec = normalizeTo2(parseInt(dec.b, 10).toString(16));
                return rDec+gDec+bDec;
            }
            else {
                dec += '';
                var rgb = dec.match(/\((\d+),\s?(\d+),\s?(\d+)\)/);
                if (rgb !== null) {
                    return  normalizeTo2(parseInt(rgb[1], 10).toString(16)) +
                            normalizeTo2(parseInt(rgb[2], 10).toString(16)) +
                            normalizeTo2(parseInt(rgb[3], 10).toString(16));
                }
                else {
                    return normalizeTo2(parseInt(dec, 10).toString(16));
                }
            }
        },

        /**
         * Converts hexadecimal values to decimal, for use with colors
         *
         * @method hexToDec
         * @param {String}  hex  hexadecimal value with 6, 3, 2 or 1 characters
         * @return {Number} Object with properties r, g, b if length of number is >= 3 or decimal value instead.
         */
        hexToDec: function(hex){
            if (hex.indexOf('#') === 0) {
                hex = hex.substr(1);
            }
            if (hex.length === 6) { // will return object RGB
                return {
                    r: parseInt(hex.substr(0,2), 16),
                    g: parseInt(hex.substr(2,2), 16),
                    b: parseInt(hex.substr(4,2), 16)
                };
            }
            else if (hex.length === 3) { // will return object RGB
                return {
                    r: parseInt(hex.charAt(0) + hex.charAt(0), 16),
                    g: parseInt(hex.charAt(1) + hex.charAt(1), 16),
                    b: parseInt(hex.charAt(2) + hex.charAt(2), 16)
                };
            }
            else if (hex.length <= 2) { // will return int
                return parseInt(hex, 16);
            }
        },

        /**
         * use this to obtain the value of a CSS property (searched from loaded CSS documents)
         *
         * @method getPropertyFromStylesheet
         * @param {String}  selector  a CSS rule. must be an exact match
         * @param {String}  property  a CSS property
         * @return {String} value of the found property, or null if it wasn't matched
         */
        getPropertyFromStylesheet: function(selector, property) {
            var rule = this.getRuleFromStylesheet(selector);
            if (rule) {
                return rule.style[property];
            }
            return null;
        },

        getPropertyFromStylesheet2: function(selector, property) {
            var rules = this.getRulesFromStylesheet(selector);
            /*
            rules.forEach(function(rule) {
                var x = rule.style[property];
                if (x !== null && x !== undefined) {
                    return x;
                }
            });
            */
            var x;
            for(var i=0, t=rules.length; i < t; i++) {
                x = rules[i].style[property];
                if (x !== null && x !== undefined) {
                    return x;
                }
            }
            return null;
        },

        getRuleFromStylesheet: function(selector) {
            var sheet, rules, ri, rf, rule;
            var s = document.styleSheets;
            if (!s) {
                return null;
            }

            for (var si = 0, sf = document.styleSheets.length; si < sf; ++si) {
                sheet = document.styleSheets[si];
                rules = sheet.rules ? sheet.rules : sheet.cssRules;
                if (!rules) { return null; }

                for (ri = 0, rf = rules.length; ri < rf; ++ri) {
                    rule = rules[ri];
                    if (!rule.selectorText) { continue; }
                    if (rule.selectorText === selector) {
                        return rule;
                    }
                }
            }

            return null;
        },

        getRulesFromStylesheet: function(selector) {
            var res = [];
            var sheet, rules, ri, rf, rule;
            var s = document.styleSheets;
            if (!s) { return res; }

            for (var si = 0, sf = document.styleSheets.length; si < sf; ++si) {
                sheet = document.styleSheets[si];
                rules = sheet.rules ? sheet.rules : sheet.cssRules;
                if (!rules) {
                    return null;
                }

                for (ri = 0, rf = rules.length; ri < rf; ++ri) {
                    rule = rules[ri];
                    if (!rule.selectorText) { continue; }
                    if (rule.selectorText === selector) {
                        res.push(rule);
                    }
                }
            }

            return res;
        },

        getPropertiesFromRule: function(selector) {
            var rule = this.getRuleFromStylesheet(selector);
            var props = {};
            var prop, i, f;

            /*if (typeof rule.style.length === 'snumber') {
                for (i = 0, f = rule.style.length; i < f; ++i) {
                    prop = this._camelCase( rule.style[i]   );
                    props[prop] = rule.style[prop];
                }
            }
            else {  // HANDLES IE 8, FIREFOX RULE JOINING... */
                rule = rule.style.cssText;
                var parts = rule.split(';');
                var steps, val, pre, pos;
                for (i = 0, f = parts.length; i < f; ++i) {
                    if (parts[i].charAt(0) === ' ') {
                        parts[i] = parts[i].substring(1);
                    }
                    steps = parts[i].split(':');
                    prop = this._camelCase( steps[0].toLowerCase()  );
                    val = steps[1];
                    if (val) {
                        val = val.substring(1);

                        if (prop === 'padding' || prop === 'margin' || prop === 'borderWidth') {

                            if (prop === 'borderWidth') {   pre = 'border'; pos = 'Width';  }
                            else {                          pre = prop;     pos = '';       }

                            if (val.indexOf(' ') !== -1) {
                                val = val.split(' ');
                                props[pre + 'Top'   + pos]  = val[0];
                                props[pre + 'Bottom'+ pos]  = val[0];
                                props[pre + 'Left'  + pos]  = val[1];
                                props[pre + 'Right' + pos]  = val[1];
                            }
                            else {
                                props[pre + 'Top'   + pos]  = val;
                                props[pre + 'Bottom'+ pos]  = val;
                                props[pre + 'Left'  + pos]  = val;
                                props[pre + 'Right' + pos]  = val;
                            }
                        }
                        else if (prop === 'borderRadius') {
                            if (val.indexOf(' ') !== -1) {
                                val = val.split(' ');
                                props.borderTopLeftRadius       = val[0];
                                props.borderBottomRightRadius   = val[0];
                                props.borderTopRightRadius      = val[1];
                                props.borderBottomLeftRadius    = val[1];
                            }
                            else {
                                props.borderTopLeftRadius       = val;
                                props.borderTopRightRadius      = val;
                                props.borderBottomLeftRadius    = val;
                                props.borderBottomRightRadius   = val;
                            }
                        }
                        else {
                            props[prop] = val;
                        }
                    }
                }
            //}
            //console.log(props);

            return props;
        },

        /**
         * Changes the font size of the elements which match the given CSS rule
         * For this function to work, the CSS file must be in the same domain than the host page, otherwise JS can't access it.
         *
         * @method changeFontSize
         * @param {String}  selector  CSS selector rule
         * @param {Number}  delta     number of pixels to change on font-size
         * @param {String}  [op]      supported operations are '+' and '*'. defaults to '+'
         * @param {Number}  [minVal]  if result gets smaller than minVal, change does not occurr
         * @param {Number}  [maxVal]  if result gets bigger  than maxVal, change does not occurr
         */
        changeFontSize: function(selector, delta, op, minVal, maxVal) {
            var that = this;
            Ink.requireModules(['Ink.Dom.Selector_1'], function(Selector) {
                var e;
                if      (typeof selector !== 'string') { e = '1st argument must be a CSS selector rule.'; }
                else if (typeof delta    !== 'number') { e = '2nd argument must be a number.'; }
                else if (op !== undefined && op !== '+' && op !== '*') { e = '3rd argument must be one of "+", "*".'; }
                else if (minVal !== undefined && (typeof minVal !== 'number' || minVal <= 0)) { e = '4th argument must be a positive number.'; }
                else if (maxVal !== undefined && (typeof maxVal !== 'number' || maxVal < maxVal)) { e = '5th argument must be a positive number greater than minValue.'; }
                if (e) { throw new TypeError(e); }

                var val, el, els = Selector.select(selector);
                if (minVal === undefined) { minVal = 1; }
                op = (op === '*') ? function(a,b){return a*b;} : function(a,b){return a+b;};
                for (var i = 0, f = els.length; i < f; ++i) {
                    el = els[i];
                    val = parseFloat( that.getStyle(el, 'fontSize'));
                    val = op(val, delta);
                    if (val < minVal) { continue; }
                    if (typeof maxVal === 'number' && val > maxVal) { continue; }
                    el.style.fontSize = val + 'px';
                }
            });
        }

    };

    return DomCss;

});
