/**
 * CSS Utilities and toolbox
 * @module Ink.Dom.Css_1
 * @version 1
 */

Ink.createModule( 'Ink.Dom.Css', 1, [], function() {

    'use strict';

     // getComputedStyle feature detection.
     var getCs = ("defaultView" in document) && ("getComputedStyle" in document.defaultView) ? document.defaultView.getComputedStyle : window.getComputedStyle;

    /**
     * @namespace Ink.Dom.Css
     * @static
     */

    var Css = {
        /**
         * Adds of removes a class.
         * Depending on addRemState, this method either adds a class if it's true or removes if if false.
         *
         * @method addRemoveClassName
         * @param {DOMElement|string}   elm          DOM element or element id
         * @param {string}              className    class name to add or remove.
         * @param {boolean}             addRemState  Whether to add or remove. `true` to add, `false` to remove.
         * @return {void}
         * @public
         * @sample Ink_Dom_Css_addRemoveClassName.html 
         */
        addRemoveClassName: function(elm, className, addRemState) {
            if (addRemState) {
                return this.addClassName(elm, className);
            }
            this.removeClassName(elm, className);
        },

        /**
         * Adds a class to a given element
         *
         * @method addClassName
         * @param {Element|String}      elm          Element or element id
         * @param {String|Array}        className    Class or classes to add. Examples: 'my-class', ['my-class', 'other-class'], 'my-class other-class'
         * @return {void}
         * @public
         * @sample Ink_Dom_Css_addClassName.html
         */
        addClassName: function(elm, className) {
            elm = Ink.i(elm);
            if (!elm || !className) { return null; }
            className = ('' + className).split(/[, ]+/);
            var i = 0;
            var len = className.length;

            for (; i < len; i++) {
                // remove whitespace and ignore on empty string
                if (className[i].replace(/^\s+|\s+$/g, '')) {
                    if (typeof elm.classList !== "undefined") {
                        elm.classList.add(className[i]);
                    } else if (!Css.hasClassName(elm, className[i])) {
                        elm.className += (elm.className ? ' ' : '') + className[i];
                    }
                }
            }
        },

        /**
         * Removes a class from a given element
         *
         * @method removeClassName
         * @param {DOMElement|String}   elm        DOM element or element id
         * @param {String|Array}        className  Class names to remove. You can either use a space separated string of classnames, comma-separated list or an array
         * @return {void}
         * @public
         * @sample Ink_Dom_Css_removeClassName.html 
         */
        removeClassName: function(elm, className) {
            elm = Ink.i(elm);
            if (!elm || !className) { return null; }
            
            className = ('' + className).split(/[, ]+/);
            var i = 0;
            var len = className.length;

            if (typeof elm.classList !== "undefined"){
                for (; i < len; i++) {
                    elm.classList.remove(className[i]);
                }
            } else {
                var elmClassName = elm.className || '';
                var re;
                for (; i < len; i++) {
                    re = new RegExp("(^|\\s+)" + className[i] + "(\\s+|$)");
                    elmClassName = elmClassName.replace(re, ' ');
                }
                elm.className = (elmClassName
                    .replace(/^\s+/, '')
                    .replace(/\s+$/, ''));
            }
        },

        /**
         * Alias to addRemoveClassName. 
         * Utility function, saves many if/elses.
         *
         * @method setClassName
         * @uses addRemoveClassName
         * @param {DOMElement|String}  elm          DOM element or element id
         * @param {String|Array}       className    Class names to add\remove. Comma separated, space separated or simply an Array
         * @param {Boolean}            [add]=false  Flag to switch behavior from removal to addition. true to add, false to remove
         * @return {void}
         * @public
         */
        setClassName: function(elm, className, add) {
            this.addRemoveClassName(elm, className, add || false);
        },

        /**
         * Checks if an element has a class.
         * This method verifies if an element has ONE of a list of classes. If the last argument is flagged as true, instead checks if the element has ALL the classes
         * 
         * @method hasClassName
         * @param {DOMElement|String}  elm         DOM element or element id
         * @param {String|Array}       className   Class name(s) to test
         * @param {Boolean}            [all=false] Irrelevant if only one `className` is passed. If `true`, check if the element contains ALL the CSS classes. If `false`, check whether the element contains ANY of the given classes.
         * @return {Boolean} `true` if a given class is applied to a given element, `false` if it isn't.
         * @public
         * @sample Ink_Dom_Css_hasClassName.html 
         */
        hasClassName: function(elm, className, all) {
            elm = Ink.i(elm);
            if (!elm || !className) { return false; }

            className = ('' + className).split(/[, ]+/);
            var i = 0;
            var len = className.length;
            var has;
            var re;

            for ( ; i < len; i++) {
                if (typeof elm.classList !== "undefined"){
                    has = elm.classList.contains(className[i]);
                } else {
                    var elmClassName = elm.className;
                    if (elmClassName === className[i]) {
                        has = true;
                    } else {
                        re = new RegExp("(^|\\s)" + className[i] + "(\\s|$)");
                        has = re.test(elmClassName);
                    }
                }
                if (has && !all) { return true; }  // return if looking for any class
                if (!has && all) { return false; }  // return if looking for all classes
            }

            if (all) {
                // if we got here, all classes were found so far
                return true;
            } else {
                // if we got here with all == false, no class was found
                return false;
            }
        },

        /**
         * Blinks a class from an element
         * Add and removes the class from the element with a timeout, so it blinks
         *
         * @method blinkClass
         * @uses addRemoveClassName
         * @param {Element|String}     element    DOM element or element id
         * @param {String|Array}       className  Class name(s) to blink
         * @param {Number}             timeout    timeout in ms between adding and removing, default 100 ms
         * @param {Boolean}            negate     is true, class is removed then added
         * @return {void}
         * @public
         * @sample Ink_Dom_Css_blinkClass.html 
         */
        blinkClass: function(element, className, timeout, negate){
            element = Ink.i(element);
            Css.addRemoveClassName(element, className, !negate);
            setTimeout(function() {
                Css.addRemoveClassName(element, className, negate);
            }, Number(timeout) || 100);
        },

        /**
         * Toggles a class name from a given element
         *
         * @method toggleClassName
         * @param {DOMElement|String}  elm        DOM element or element id
         * @param {String}             className  Class name
         * @param {Boolean}            [forceAdd] Flag to force adding the the classe names if they don't exist yet.
         * @return {void}
         * @public
         * @sample Ink_Dom_Css_toggleClassName.html 
         */
        toggleClassName: function(elm, className, forceAdd) {
            if (!elm || !className) { return false; }

            if (typeof forceAdd !== 'undefined') {
                return Css.addRemoveClassName(elm, className, forceAdd);
            } else if (typeof elm.classList !== "undefined" && !/[, ]/.test(className)) {
                elm = Ink.i(elm);
                if (elm !== null){
                    elm.classList.toggle(className);
                }
            } else {
                if (Css.hasClassName(elm, className)) {
                    Css.removeClassName(elm, className);
                } else {
                    Css.addClassName(elm, className);
                }
            }
        },

        /**
         * Sets the opacity of given element 
         *
         * @method setOpacity
         * @param {DOMElement|String}  elm    DOM element or element id
         * @param {Number}             value  allows 0 to 1(default mode decimal) or percentage (warning using 0 or 1 will reset to default mode)
         * @return {void}
         * @public
         * @sample Ink_Dom_Css_setOpacity.html 
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
            return str ? str.replace(/-(\w)/g, function (_, $1) {
                return $1.toUpperCase();
            }) : str;
        },


        /**
         * Gets the value for an element's style attribute
         *
         * @method getStyle
         * @param {DOMElement|String}  elm    DOM element or element id
         * @param {String}             style  Which css attribute to fetch
         * @return {Mixed} Style value
         * @public
         * @sample Ink_Dom_Css_getStyle.html 
         */
         getStyle: function(elm, style) {
             elm = Ink.i(elm);
             if (elm !== null && elm.style) {
                 style = style === 'float' ? 'cssFloat': this._camelCase(style);

                 var value = elm.style[style];

                 if (getCs && (!value || value === 'auto')) {
                     var css = getCs(elm, null);
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
         * @return {void}
         * @public
         * @sample Ink_Dom_Css_setStyle.html 
         */
        setStyle: function(elm, style) {
            elm = Ink.i(elm);
            if (elm === null) { return; }
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
                        else if (prop === 'float' || prop === 'cssFloat') {
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
        },


        /**
         * Shows an element.
         * Internally it unsets the display property of an element. You can force a specific display property using forceDisplayProperty
         *
         * @method show
         * @param {DOMElement|String}  elm                      DOM element or element id
         * @param {String}             [forceDisplayProperty]   Css display property to apply on show
         * @return {void}
         * @public
         * @sample Ink_Dom_Css_show.html 
         */
        show: function(elm, forceDisplayProperty) {
            elm = Ink.i(elm);
            if (elm !== null) {
                elm.style.display = forceDisplayProperty || '';
            }
        },

        /**
         * Hides an element.
         *
         * @method hide
         * @param {DOMElement|String}  elm  DOM element or element id
         * @return {void}
         * @public
         * @sample Ink_Dom_Css_hide.html 
         */
        hide: function(elm) {
            elm = Ink.i(elm);
            if (elm !== null) {
                elm.style.display = 'none';
            }
        },

        /**
         * Shows or hides an element.
         * If the show parameter is true, it shows the element. Otherwise, hides it.
         *
         * @method showHide
         * @param {DOMElement|String}  elm          DOM element or element id
         * @param {boolean}            [show]=false Whether to show or hide `elm`.
         * @return {void}
         * @public
         * @sample Ink_Dom_Css_showHide.html 
         */
        showHide: function(elm, show) {
            elm = Ink.i(elm);
            if (elm) {
                elm.style.display = show ? '' : 'none';
            }
        },

        /**
         * Toggles an element visibility.
         * 
         * @method toggle
         * @param {DOMElement|String}  elm        DOM element or element id
         * @param {Boolean}            forceShow  Forces showing if element is hidden
         * @return {void}
         * @public
         * @sample Ink_Dom_Css_toggle.html 
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
                    if (this.getStyle(elm,'display').toLowerCase() === 'none') {
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
         * Injects style tags with rules to the page.
         *
         * @method appendStyleTag
         * @param {String}  selector  The css selector for the rule
         * @param {String}  style     The content of the style rule
         * @param {Object}  options   Options for the tag
         *    @param {String}  [options.type]='text/css'   File type
         *    @param {Boolean} [options.force]=false  If true, the style tag will be appended to end of head
         * @return {void}
         * @public
         * 
         * @sample Ink_Dom_Css_appendStyleTag.html 
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
         * Injects an external link tag.
         * This method add a stylesheet to the head of a page
         *
         * @method appendStylesheet
         * @param {String}  path     File path
         * @param {Object}  options  Options for the tag
         * @param {String}  [options.media='screen']    Media type
         * @param {String}  [options.type='text/css']   File type
         * @param {Boolean} [options.force=false]       If true, tag will be appended to end of head
         * @return {void}
         * @public
         * @sample Ink_Dom_Css_appendStylesheet.html 
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
         * Injects an external link tag.
         * Loads CSS via LINK element inclusion in HEAD (skips append if already there)
         *
         * Works similarly to appendStylesheet but:
         *   supports optional callback which gets invoked once the CSS has been applied
         *
         * @method appendStylesheetCb
         * @param {String}            cssURI      URI of the CSS to load, if empty ignores and just calls back directly
         * @param {Function(cssURI)}  [callback]  optional callback which will be called once the CSS is loaded
         * @return {void}
         * @public
         * @sample Ink_Dom_Css_appendStylesheetCb.html 
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
         * Converts decimal to hexadecimal values
         * Useful to convert colors to their hexadecimal representation.
         *
         * @method decToHex
         * @param {String} dec Either a single decimal value, an rgb(r, g, b) string or an Object with r, g and b properties
         * @return {String} Hexadecimal value
         * @sample Ink_Dom_Css_decToHex.html 
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
         * Converts hexadecimal values to decimal
         * Useful to use with CSS colors
         *
         * @method hexToDec
         * @param {String}  hex  hexadecimal Value with 6, 3, 2 or 1 characters
         * @return {Number} Object with properties r, g, b if length of number is >= 3 or decimal value instead.
         * @sample Ink_Dom_Css_hexToDec.html 
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
         * Get a single property from a stylesheet.
         * Use this to obtain the value of a CSS property (searched from loaded CSS documents)
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
         * Change the font size of elements.
         * Changes the font size of the elements which match the given CSS rule
         * For this function to work, the CSS file must be in the same domain than the host page, otherwise JS can't access it.
         *
         * @method changeFontSize
         * @param {String}  selector  CSS selector rule
         * @param {Number}  delta     Number of pixels to change on font-size
         * @param {String}  [op]      Supported operations are '+' and '*'. defaults to '+'
         * @param {Number}  [minVal]  If result gets smaller than minVal, change does not occurr
         * @param {Number}  [maxVal]  If result gets bigger  than maxVal, change does not occurr
         * @return {void}
         * @public
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

    return Css;

});
