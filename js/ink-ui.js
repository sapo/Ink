
/**
 * @module Ink.UI.Aux_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Aux', '1', ['Ink.Net.Ajax_1','Ink.Dom.Css_1','Ink.Dom.Selector_1','Ink.Util.Url_1'], function(Ajax,Css,Selector,Url) {

    'use strict';

    var instances = {};
    var lastIdNum = 0;

    /**
     * The Aux class provides auxiliar methods to ease some of the most common/repetitive UI tasks.
     *
     * @class Ink.UI.Aux
     * @version 1
     * @uses Ink.Net.Ajax
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Selector
     * @uses Ink.Util.Url
     * @static
     */
    var Aux = {

        /**
         * Supported Ink Layouts
         *
         * @property Layouts
         * @type Object
         * @readOnly
         */
        Layouts: {
            SMALL:  'small',
            MEDIUM: 'medium',
            LARGE:  'large'
        },

        /**
         * Method to check if an item is a valid DOM Element.
         *
         * @method isDOMElement
         * @static
         * @param {Mixed} o     The object to be checked.
         * @return {Boolean}    True if it's a valid DOM Element.
         * @example
         *     var el = Ink.s('#element');
         *     if( Ink.UI.Aux.isDOMElement( el ) === true ){
         *         // It is a DOM Element.
         *     } else {
         *         // It is NOT a DOM Element.
         *     }
         */
        isDOMElement: function(o) {
            return (typeof o === 'object' && 'nodeType' in o && o.nodeType === 1);
        },

        /**
         * Method to check if an item is a valid integer.
         *
         * @method isInteger
         * @static
         * @param {Mixed} n     The value to be checked.
         * @return {Boolean}    True if 'n' is a valid integer.
         * @example
         *     var value = 1;
         *     if( Ink.UI.Aux.isInteger( value ) === true ){
         *         // It is an integer.
         *     } else {
         *         // It is NOT an integer.
         *     }
         */
        isInteger: function(n) {
            return (typeof n === 'number' && n % 1 === 0);
        },

        /**
         * Method to get a DOM Element. The first parameter should be either a DOM Element or a valid CSS Selector.
         * If not, then it will throw an exception. Otherwise, it returns a DOM Element.
         *
         * @method elOrSelector
         * @static
         * @param  {DOMElement|String} elOrSelector Valid DOM Element or CSS Selector
         * @param  {String}            fieldName    This field is used in the thrown Exception to identify the parameter.
         * @return {DOMElement} Returns the DOMElement passed or the first result of the CSS Selector. Otherwise it throws an exception.
         * @example
         *     // In case there are several .myInput, it will retrieve the first found
         *     var el = Ink.UI.Aux.elOrSelector('.myInput','My Input');
         */
        elOrSelector: function(elOrSelector, fieldName) {
            if (!this.isDOMElement(elOrSelector)) {
                var t = Selector.select(elOrSelector);
                if (t.length === 0) { throw new TypeError(fieldName + ' must either be a DOM Element or a selector expression!\nThe script element must also be after the DOM Element itself.'); }
                return t[0];
            }
            return elOrSelector;
        },


        /**
         * Method to make a deep copy (clone) of an object.
         * Note: The object cannot have loops.
         *
         * @method clone
         * @static
         * @param  {Object} o The object to be cloned/copied.
         * @return {Object} Returns the result of the clone/copy.
         * @example
         *     var originalObj = {
         *         key1: 'value1',
         *         key2: 'value2',
         *         key3: 'value3'
         *     };
         *     var cloneObj = Ink.UI.Aux.clone( originalObj );
         */
        clone: function(o) {
            try {
                if (typeof o !== 'object') { throw new Error('Given argument is not an object!'); }
                return JSON.parse( JSON.stringify(o) );
            } catch (ex) {
                throw new Error('Given object cannot have loops!');
            }
        },


        /**
         * Method to return the 'nth' position that an element occupies relatively to its parent.
         *
         * @method childIndex
         * @static
         * @param  {DOMElement} childEl Valid DOM Element.
         * @return {Number} Numerical position of an element relatively to its parent.
         * @example
         *     <!-- Imagine the following HTML: -->
         *     <ul>
         *       <li>One</li>
         *       <li>Two</li>
         *       <li id="test">Three</li>
         *       <li>Four</li>
         *     </ul>
         *
         *     <script>
         *         var testLi = Ink.s('#test');
         *         Ink.UI.Aux.childIndex( testLi ); // Returned value: 3
         *     </script>
         */
        childIndex: function(childEl) {
            if( Aux.isDOMElement(childEl) ){
                var els = Selector.select('> *', childEl.parentNode);
                for (var i = 0, f = els.length; i < f; ++i) {
                    if (els[i] === childEl) {
                        return i;
                    }
                }
            }
            throw 'not found!';
        },


        /**
         * This method provides a more convenient way to do an async AJAX request and expect a JSON response.
         * It offers a callback option, as third paramenter, for a better async handling.
         *
         * @method ajaxJSON
         * @static
         * @async
         * @param  {String} endpoint    Valid URL to be used as target by the request.
         * @param  {Object} params      This field is used in the thrown Exception to identify the parameter.
         * @example
         *     // In case there are several .myInput, it will retrieve the first found
         *     var el = Ink.UI.Aux.elOrSelector('.myInput','My Input');
         */
        ajaxJSON: function(endpoint, params, cb) {
            new Ajax(
                endpoint,
                {
                    evalJS:         'force',
                    method:         'POST',
                    parameters:     params,

                    onSuccess:  function( r) {
                        try {
                            r = r.responseJSON;
                            if (r.status !== 'ok') {
                                throw 'server error: ' + r.message;
                            }
                            cb(null, r);
                        } catch (ex) {
                            cb(ex);
                        }
                    },

                    onFailure: function() {
                        cb('communication failure');
                    }
                }
            );
        },


        /**
         * Method to get the current Ink layout applied.
         *
         * @method currentLayout
         * @static
         * @return {String}         Returns the value of one of the options of the property Layouts above defined.
         * @example
         *     var inkLayout = Ink.UI.Aux.currentLayout();
         */
        currentLayout: function() {
            var i, f, k, v, el, detectorEl = Selector.select('#ink-layout-detector')[0];
            if (!detectorEl) {
                detectorEl = document.createElement('div');
                detectorEl.id = 'ink-layout-detector';
                for (k in this.Layouts) {
                    if (this.Layouts.hasOwnProperty(k)) {
                        v = this.Layouts[k];
                        el = document.createElement('div');
                        el.className = 'show-' + v + ' hide-all';
                        el.setAttribute('data-ink-layout', v);
                        detectorEl.appendChild(el);
                    }
                }
                document.body.appendChild(detectorEl);
            }

            for (i = 0, f = detectorEl.childNodes.length; i < f; ++i) {
                el = detectorEl.childNodes[i];
                if (Css.getStyle(el, 'visibility') !== 'hidden') {
                    return el.getAttribute('data-ink-layout');
                }
            }
        },


        /**
         * Method to set the location's hash (window.location.hash).
         *
         * @method hashSet
         * @static
         * @param  {Object} o   Object with the info to be placed in the location's hash.
         * @example
         *     // It will set the location's hash like: <url>#key1=value1&key2=value2&key3=value3
         *     Ink.UI.Aux.hashSet({
         *         key1: 'value1',
         *         key2: 'value2',
         *         key3: 'value3'
         *     });
         */
        hashSet: function(o) {
            if (typeof o !== 'object') { throw new TypeError('o should be an object!'); }
            var hashParams = Url.getAnchorString();
            hashParams = Ink.extendObj(hashParams, o);
            window.location.hash = Url.genQueryString('', hashParams).substring(1);
        },

        /**
         * Method to remove children nodes from a given object.
         * This method was initially created to help solve a problem in Internet Explorer(s) that occurred when trying
         * to set the innerHTML of some specific elements like 'table'.
         *
         * @method cleanChildren
         * @static
         * @param  {DOMElement} parentEl Valid DOM Element
         * @example
         *     <!-- Imagine the following HTML: -->
         *     <ul id="myUl">
         *       <li>One</li>
         *       <li>Two</li>
         *       <li>Three</li>
         *       <li>Four</li>
         *     </ul>
         *
         *     <script>
         *     Ink.UI.Aux.cleanChildren( Ink.s( '#myUl' ) );
         *     </script>
         *
         *     <!-- After running it, the HTML changes to: -->
         *     <ul id="myUl"></ul>
         */
        cleanChildren: function(parentEl) {
            if( !Aux.isDOMElement(parentEl) ){
                throw 'Please provide a valid DOMElement';
            }
            var prevEl, el = parentEl.lastChild;
            while (el) {
                prevEl = el.previousSibling;
                parentEl.removeChild(el);
                el = prevEl;
            }
        },

        /**
         * This method stores the id and/or the classes of a given element in a given object.
         *
         * @method storeIdAndClasses
         * @static
         * @param  {DOMElement} fromEl    Valid DOM Element to get the id and classes from.
         * @param  {Object}     inObj     Object where the id and classes will be saved.
         * @example
         *     <div id="myDiv" class="aClass"></div>
         *
         *     <script>
         *         var storageObj = {};
         *         Ink.UI.Aux.storeIdAndClasses( Ink.s('#myDiv'), storageObj );
         *         // storageObj changes to:
         *         {
         *           _id: 'myDiv',
         *           _classes: 'aClass'
         *         }
         *     </script>
         */
        storeIdAndClasses: function(fromEl, inObj) {
            if( !Aux.isDOMElement(fromEl) ){
                throw 'Please provide a valid DOMElement as first parameter';
            }

            var id = fromEl.id;
            if (id) {
                inObj._id = id;
            }

            var classes = fromEl.className;
            if (classes) {
                inObj._classes = classes;
            }
        },

        /**
         * This method sets the id and className properties of a given DOM Element based on a given similar object
         * resultant of the previous function 'storeIdAndClasses'.
         *
         * @method restoreIdAndClasses
         * @static
         * @param  {DOMElement} toEl    Valid DOM Element to set the id and classes on.
         * @param  {Object}     inObj   Object where the id and classes to be set are.
         * @example
         *     <div></div>
         *
         *     <script>
         *         var storageObj = {
         *           _id: 'myDiv',
         *           _classes: 'aClass'
         *         };
         *
         *         Ink.UI.Aux.storeIdAndClasses( Ink.s('div'), storageObj );
         *     </script>
         *
         *     <!-- After the code runs the div element changes to: -->
         *     <div id="myDiv" class="aClass"></div>
         */
        restoreIdAndClasses: function(toEl, inObj) {

            if( !Aux.isDOMElement(toEl) ){
                throw 'Please provide a valid DOMElement as first parameter';
            }

            if (inObj._id && toEl.id !== inObj._id) {
                toEl.id = inObj._id;
            }

            if (inObj._classes && toEl.className.indexOf(inObj._classes) === -1) {
                if (toEl.className) { toEl.className += ' ' + inObj._classes; }
                else {                toEl.className  =       inObj._classes; }
            }

            if (inObj._instanceId && !toEl.getAttribute('data-instance')) {
                toEl.setAttribute('data-instance', inObj._instanceId);
            }
        },

        /**
         * This method saves a component's instance reference for later retrieval.
         *
         * @method registerInstance
         * @static
         * @param  {Object}     inst                Object that holds the instance.
         * @param  {DOMElement} el                  DOM Element to associate with the object.
         * @param  {Object}     [optionalPrefix]    Defaults to 'instance'
         */
        registerInstance: function(inst, el, optionalPrefix) {
            if (inst._instanceId) { return; }

            if (typeof inst !== 'object') { throw new TypeError('1st argument must be a JavaScript object!'); }

            if (inst._options && inst._options.skipRegister) { return; }

            if (!this.isDOMElement(el)) { throw new TypeError('2nd argument must be a DOM element!'); }
            if (optionalPrefix !== undefined && typeof optionalPrefix !== 'string') { throw new TypeError('3rd argument must be a string!'); }
            var id = (optionalPrefix || 'instance') + (++lastIdNum);
            instances[id] = inst;
            inst._instanceId = id;
            var dataInst = el.getAttribute('data-instance');
            dataInst = (dataInst !== null) ? [dataInst, id].join(' ') : id;
            el.setAttribute('data-instance', dataInst);
        },

        /**
         * This method deletes/destroy an instance with a given id.
         *
         * @method unregisterInstance
         * @static
         * @param  {String}     id       Id of the instance to be destroyed.
         */
        unregisterInstance: function(id) {
            delete instances[id];
        },

        /**
         * This method retrieves the registered instance(s) of a given element or instance id.
         *
         * @method getInstance
         * @static
         * @param  {String|DOMElement}      instanceIdOrElement      Instance's id or DOM Element from which we want the instances.
         * @return  {Object|Object[]}       Returns an instance or a collection of instances.
         */
        getInstance: function(instanceIdOrElement) {
            var ids;
            if (this.isDOMElement(instanceIdOrElement)) {
                ids = instanceIdOrElement.getAttribute('data-instance');
                if (ids === null) { throw new Error('argument is not a DOM instance element!'); }
            }
            else {
                ids = instanceIdOrElement;
            }

            ids = ids.split(' ');
            var inst, id, i, l = ids.length;

            var res = [];
            for (i = 0; i < l; ++i) {
                id = ids[i];
                if (!id) { throw new Error('Element is not a JS instance!'); }
                inst = instances[id];
                if (!inst) { throw new Error('Instance "' + id + '" not found!'); }
                res.push(inst);
            }

            return (l === 1) ? res[0] : res;
        },

        /**
         * This method retrieves the registered instance(s) of an element based on the given selector.
         *
         * @method getInstanceFromSelector
         * @static
         * @param  {String}             selector    CSS selector to define the element from which it'll get the instance(s).
         * @return  {Object|Object[]}               Returns an instance or a collection of instances.
         */
        getInstanceFromSelector: function(selector) {
            var el = Selector.select(selector)[0];
            if (!el) { throw new Error('Element not found!'); }
            return this.getInstance(el);
        },

        /**
         * This method retrieves the registered instances' ids of all instances.
         *
         * @method getInstanceIds
         * @static
         * @return  {String[]}     Id or collection of ids of all existing instances.
         */
        getInstanceIds: function() {
            var res = [];
            for (var id in instances) {
                if (instances.hasOwnProperty(id)) {
                    res.push( id );
                }
            }
            return res;
        },

        /**
         * This method retrieves all existing instances.
         *
         * @method getInstances
         * @static
         * @return  {Object[]}     Collection of existing instances.
         */
        getInstances: function() {
            var res = [];
            for (var id in instances) {
                if (instances.hasOwnProperty(id)) {
                    res.push( instances[id] );
                }
            }
            return res;
        },

        /**
         * This method is not to supposed to be invoked by the Aux component.
         * Components should copy this method as its destroy method.
         *
         * @method destroyComponent
         * @static
         */
        destroyComponent: function() {
            Ink.Util.Aux.unregisterInstance(this._instanceId);
            this._element.parentNode.removeChild(this._element);
        }

    };

    return Aux;

});

/**
 * @module Ink.UI.Modal_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Modal', '1', ['Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1'], function(Aux, Event, Css, Element, Selector, InkArray ) {
    'use strict';

    /**
     * @class Ink.UI.Modal
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @uses Ink.Util.Array
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *      @param {String}    [options.width]             Default/Initial width. Ex: '600px'
     *      @param {String}    [options.height]            Default/Initial height. Ex: '400px'
     *      @param {String}    [options.shadeClass]        Custom class to be added to the div.ink-shade
     *      @param {String}    [options.modalClass]        Custom class to be added to the div.ink-modal
     *      @param {String}    [options.trigger]           CSS Selector to target elements that will trigger the Modal.
     *      @param {String}    [options.triggerEvent]      Trigger's event to be listened. 'click' is the default value. Ex: 'mouseover', 'touchstart'...
     *      @param {String}    [options.markup]            Markup to be placed in the Modal when created
     *      @param {Function}  [options.onShow]            Callback function to run when the Modal is opened.
     *      @param {Function}  [options.onDismiss]         Callback function to run when the Modal is closed.
     *      @param {Boolean}   [options.closeOnClick]      Determines if the Modal should close when clicked outside of it. 'false' by default.
     *      @param {Boolean}   [options.responsive]        Determines if the Modal should behave responsively (adapt to smaller viewports).
     *      @param {Boolean}   [options.disableScroll]     Determines if the Modal should 'disable' the page's scroll (not the Modal's body).
     *
     * @example
     *      <div class="ink-shade fade">
     *          <div id="test" class="ink-modal fade" data-trigger="#bModal" data-width="800px" data-height="400px">
     *              <div class="modal-header">
     *                  <button class="modal-close ink-dismiss"></button>
     *                  <h5>Modal windows can have headers</h5>
     *              </div>
     *              <div class="modal-body" id="modalContent">
     *                  <h3>Please confirm your previous choice</h3>
     *                  <p>"No," said Peleg, "and he hasn't been baptized right either, or it would have washed some of that devil's blue off his face."</p>
     *                  <p>
     *                      <img src="http://placehold.it/800x400" style="width: 100%;" alt="">
     *                  </p>
     *                  <p>"Do tell, now," cried Bildad, "is this Philistine a regular member of Deacon Deuteronomy's meeting? I never saw him going there, and I pass it every Lord's day."</p>
     *                  <p>"I don't know anything about Deacon Deuteronomy or his meeting," said I; "all I know is, that Queequeg here is a born member of the First Congregational Church. He is a deacon himself, Queequeg is."</p>
     *              </div>
     *              <div class="modal-footer">
     *                  <div class="push-right">
     *                      <button class="ink-button info">Confirm</button>
     *                      <button class="ink-button caution ink-dismiss">Cancel</button>
     *                  </div>
     *              </div>
     *          </div>
     *      </div>
     *      <a href="#" id="bModal">Open modal</a>
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.Modal_1'], function( Selector, Modal ){
     *              var modalElement = Ink.s('#test');
     *              var modalObj = new Modal( modalElement );
     *          });
     *      </script>
     */
    var Modal = function(selector, options) {

        if( (typeof selector !== 'string') && (typeof selector !== 'object') && (typeof options.markup === 'undefined') ){
            throw 'Invalid Modal selector';
        } else if(typeof selector === 'string'){
            if( selector !== '' ){
                this._element = Selector.select(selector);
                if( this._element.length === 0 ){
                    /**
                     * From a developer's perspective this should be like it is...
                     * ... from a user's perspective, if it doesn't find elements, should just ignore it, no?
                     */
                    throw 'The Modal selector has not returned any elements';
                } else {
                    this._element = this._element[0];
                }
            }
        } else if( !!selector ){
            this._element = selector;
        }

        this._options = {
            /**
             * Width, height and markup really optional, as they can be obtained by the element
             */
            width:        undefined,
            height:       undefined,

            /**
             * To add extra classes
             */
            shadeClass: undefined,
            modalClass: undefined,

            /**
             * Optional trigger properties
             */
            trigger:      undefined,
            triggerEvent: 'click',

            /**
             * Remaining options
             */
            markup:       undefined,
            onShow:       undefined,
            onDismiss:    undefined,
            closeOnClick: false,
            responsive:    true,
            disableScroll: true
        };


        this._handlers = {
            click:   Ink.bindEvent(this._onClick, this),
            keyDown: Ink.bindEvent(this._onKeyDown, this),
            resize:  Ink.bindEvent(this._onResize, this)
        };

        this._wasDismissed = false;

        /**
         * Modal Markup
         */
        if( this._element ){
            this._markupMode = Css.hasClassName(this._element,'ink-modal'); // Check if the full modal comes from the markup
        } else {
            this._markupMode = false;
        }




        if( !this._markupMode ){


            this._modalShadow      = document.createElement('div');
            this._modalShadowStyle = this._modalShadow.style;

            this._modalDiv         = document.createElement('div');
            this._modalDivStyle    = this._modalDiv.style;

            if( !!this._element ){
                this._options.markup = this._element.innerHTML;
            }

            /**
             * Not in full markup mode, let's set the classes and css configurations
             */
            Css.addClassName( this._modalShadow,'ink-shade' );
            Css.addClassName( this._modalDiv,'ink-modal' );
            Css.addClassName( this._modalDiv,'ink-space' );

            /**
             * Applying the main css styles
             */
            // this._modalDivStyle.position = 'absolute';
            this._modalShadow.appendChild( this._modalDiv);
            document.body.appendChild( this._modalShadow );
        } else {
            this._modalDiv         = this._element;
            this._modalDivStyle    = this._modalDiv.style;
            this._modalShadow      = this._modalDiv.parentNode;
            this._modalShadowStyle = this._modalShadow.style;

            this._contentContainer = Selector.select(".modal-body",this._modalDiv);
            if( !this._contentContainer.length ){
                throw 'Missing div with class "modal-body"';
            }

            this._contentContainer = this._contentContainer[0];
            this._options.markup = this._contentContainer.innerHTML;

            /**
             * First, will handle the least important: The dataset
             */
            this._options = Ink.extendObj(this._options,Element.data(this._element));

        }

        /**
         * Now, the most important, the initialization options
         */
        this._options = Ink.extendObj(this._options,options || {});

        if( !this._markupMode ){
            this.setContentMarkup(this._options.markup);
        }

        if( typeof this._options.shadeClass === 'string' ){

            InkArray.each( this._options.shadeClass.split(' '), Ink.bind(function( item ){
                Css.addClassName( this._modalShadow, item.trim() );
            }, this));
        }

        if( typeof this._options.modalClass === 'string' ){
            InkArray.each( this._options.modalClass.split(' '), Ink.bind(function( item ){
                Css.addClassName( this._modalDiv, item.trim() );
            }, this));
        }

        if( ("trigger" in this._options) && ( typeof this._options.trigger !== 'undefined' ) ){
            var triggerElement,i;
            if( typeof this._options.trigger === 'string' ){
                triggerElement = Selector.select( this._options.trigger );
                if( triggerElement.length > 0 ){
                    for( i=0; i<triggerElement.length; i++ ){
                        Event.observe( triggerElement[i], this._options.triggerEvent, Ink.bindEvent(this._init, this) );
                    }
                }
            }
        } else {
            this._init();
        }
    };

    Modal.prototype = {

        /**
         * Init function called by the constructor
         * 
         * @method _init
         * @param {Event} [event] In case its fired by the trigger.
         * @private
         */
        _init: function(event) {

            if( event ){ Event.stop(event); }

            var elem = (document.compatMode === "CSS1Compat") ?  document.documentElement : document.body;

            this._resizeTimeout    = null;

            Css.addClassName( this._modalShadow,'ink-shade' );
            this._modalShadowStyle.display = this._modalDivStyle.display = 'block';
            setTimeout(Ink.bind(function(){
                Css.addClassName( this._modalShadow,'visible' );
                Css.addClassName( this._modalDiv,'visible' );
            }, this),100);

            /**
             * Fallback to the old one
             */
            this._contentElement = this._modalDiv;
            this._shadeElement   = this._modalShadow;

            /**
             * Setting the content of the modal
             */
            this.setContentMarkup( this._options.markup );

            /**
             * If any size has been user-defined, let's set them as max-width and max-height
             */
            if( typeof this._options.width !== 'undefined' ){
                this._modalDivStyle.width = this._options.width;
                if( this._options.width.indexOf('%') === -1 ){
                    this._modalDivStyle.maxWidth = Element.elementWidth(this._modalDiv) + 'px';
                }
            } else {
                this._modalDivStyle.maxWidth = this._modalDivStyle.width = Element.elementWidth(this._modalDiv)+'px';
            }

            if( parseInt(elem.clientWidth,10) <= parseInt(this._modalDivStyle.width,10) ){
                this._modalDivStyle.width = (~~(parseInt(elem.clientWidth,10)*0.9))+'px';
            }

            if( typeof this._options.height !== 'undefined' ){
                this._modalDivStyle.height = this._options.height;
                if( this._options.height.indexOf('%') === -1 ){
                    this._modalDivStyle.maxHeight = Element.elementHeight(this._modalDiv) + 'px';
                }
            } else {
                this._modalDivStyle.maxHeight = this._modalDivStyle.height = Element.elementHeight(this._modalDiv) + 'px';
            }

            if( parseInt(elem.clientHeight,10) <= parseInt(this._modalDivStyle.height,10) ){
                this._modalDivStyle.height = (~~(parseInt(elem.clientHeight,10)*0.9))+'px';
            }

            this.originalStatus = {
                viewportHeight:     parseInt(elem.clientHeight,10),
                viewportWidth:      parseInt(elem.clientWidth,10),
                width:              parseInt(this._modalDivStyle.maxWidth,10),
                height:             parseInt(this._modalDivStyle.maxHeight,10)
            };

            /**
             * Let's 'resize' it:
             */
            if(this._options.responsive) {
                this._onResize(true);
                Event.observe( window,'resize',this._handlers.resize );
            } else {
                this._resizeContainer();
                this._reposition();
            }

            if (this._options.onShow) {
                this._options.onShow(this);
            }

            if(this._options.disableScroll) {
                this._disableScroll();
            }

            // subscribe events
            Event.observe(this._shadeElement, 'click',   this._handlers.click);
            Event.observe(document,           'keydown', this._handlers.keyDown);

            Aux.registerInstance(this, this._shadeElement, 'modal');
        },

        /**
         * Responsible for repositioning the modal
         * 
         * @method _reposition
         * @private
         */
        _reposition: function(){

            this._modalDivStyle.top = this._modalDivStyle.left = '50%';

            this._modalDivStyle.marginTop = '-' + ( ~~( Element.elementHeight(this._modalDiv)/2) ) + 'px';
            this._modalDivStyle.marginLeft = '-' + ( ~~( Element.elementWidth(this._modalDiv)/2) ) + 'px';
        },

        /**
         * Responsible for resizing the modal
         * 
         * @method _onResize
         * @param {Boolean|Event} runNow Its executed in the begining to resize/reposition accordingly to the viewport. But usually it's an event object.
         * @private
         */
        _onResize: function( runNow ){

            if( typeof runNow === 'boolean' ){
                this._timeoutResizeFunction.call(this);
            } else if( !this._resizeTimeout && (typeof runNow === 'object') ){
                this._resizeTimeout = setTimeout(Ink.bind(this._timeoutResizeFunction, this),250);
            }
        },

        /**
         * Timeout Resize Function
         * 
         * @method _timeoutResizeFunction
         * @private
         */
        _timeoutResizeFunction: function(){
            /**
             * Getting the current viewport size
             */
            var
                elem = (document.compatMode === "CSS1Compat") ?  document.documentElement : document.body,
                currentViewportHeight = parseInt(elem.clientHeight,10),
                currentViewportWidth = parseInt(elem.clientWidth,10)
            ;

            if( ( currentViewportWidth > this.originalStatus.width ) /* && ( parseInt(this._modalDivStyle.maxWidth,10) >= Element.elementWidth(this._modalDiv) )*/ ){
                /**
                 * The viewport width has expanded
                 */
                this._modalDivStyle.width = this._modalDivStyle.maxWidth;

            } else {
                /**
                 * The viewport width has not changed or reduced
                 */
                //this._modalDivStyle.width = (( currentViewportWidth * this.originalStatus.width ) / this.originalStatus.viewportWidth ) + 'px';
                this._modalDivStyle.width = (~~( currentViewportWidth * 0.9)) + 'px';
            }

            if( (currentViewportHeight > this.originalStatus.height) && (parseInt(this._modalDivStyle.maxHeight,10) >= Element.elementHeight(this._modalDiv) ) ){

                /**
                 * The viewport height has expanded
                 */
                //this._modalDivStyle.maxHeight =
                this._modalDivStyle.height = this._modalDivStyle.maxHeight;

            } else {
                /**
                 * The viewport height has not changed, or reduced
                 */
                this._modalDivStyle.height = (~~( currentViewportHeight * 0.9)) + 'px';
            }

            this._resizeContainer();
            this._reposition();
            this._resizeTimeout = undefined;
        },

        /**
         * Navigation click handler
         * 
         * @method _onClick
         * @param {Event} ev
         * @private
         */
        _onClick: function(ev) {
            var tgtEl = Event.element(ev);

            if (Css.hasClassName(tgtEl, 'ink-close') || Css.hasClassName(tgtEl, 'ink-dismiss') ||
                (
                    this._options.closeOnClick &&
                    (!Element.descendantOf(this._shadeElement, tgtEl) || (tgtEl === this._shadeElement))
                )
            ) {
                var 
                    alertsInTheModal = Selector.select('.ink-alert',this._shadeElement),
                    alertsLength = alertsInTheModal.length
                ;
                for( var i = 0; i < alertsLength; i++ ){
                    if( Element.descendantOf(alertsInTheModal[i], tgtEl) ){
                        return;
                    }
                }

                Event.stop(ev);
                this.dismiss();
            }
        },

        /**
         * Responsible for handling the escape key pressing.
         *
         * @method _onKeyDown
         * @param  {Event} ev
         * @private
         */
        _onKeyDown: function(ev) {
            if (ev.keyCode !== 27 || this._wasDismissed) { return; }
            this.dismiss();
        },

        /**
         * Responsible for setting the size of the modal (and position) based on the viewport.
         * 
         * @method _resizeContainer
         * @private
         */
        _resizeContainer: function()
        {

            this._contentElement.style.overflow = this._contentElement.style.overflowX = this._contentElement.style.overflowY = 'hidden';
            var containerHeight = Element.elementHeight(this._modalDiv);

            this._modalHeader = Selector.select('.modal-header',this._modalDiv);
            if( this._modalHeader.length>0 ){
                this._modalHeader = this._modalHeader[0];
                containerHeight -= Element.elementHeight(this._modalHeader);
            }

            this._modalFooter = Selector.select('.modal-footer',this._modalDiv);
            if( this._modalFooter.length>0 ){
                this._modalFooter = this._modalFooter[0];
                containerHeight -= Element.elementHeight(this._modalFooter);
            }

            this._contentContainer.style.height = containerHeight + 'px';
            if( containerHeight !== Element.elementHeight(this._contentContainer) ){
                this._contentContainer.style.height = ~~(containerHeight - (Element.elementHeight(this._contentContainer) - containerHeight)) + 'px';
            }

            if( this._markupMode ){ return; }

            this._contentContainer.style.overflow = this._contentContainer.style.overflowX = 'hidden';
            this._contentContainer.style.overflowY = 'auto';
            this._contentElement.style.overflow = this._contentElement.style.overflowX = this._contentElement.style.overflowY = 'visible';
        },

        /**
         * Responsible for 'disabling' the page scroll
         * 
         * @method _disableScroll
         * @private
         */
        _disableScroll: function()
        {
            this._oldScrollPos = Element.scroll();
            this._onScrollBinded = Ink.bindEvent(function(event) {
                var tgtEl = Event.element(event);

                if( !Element.descendantOf(this._modalShadow, tgtEl) ){
                    Event.stop(event);
                    window.scrollTo(this._oldScrollPos[0], this._oldScrollPos[1]);
                }
            },this);
            Event.observe(window, 'scroll', this._onScrollBinded);
            Event.observe(document, 'touchmove', this._onScrollBinded);
        },

        /**************
         * PUBLIC API *
         **************/

        /**
         * Dismisses the modal
         * 
         * @method dismiss
         * @public
         */
        dismiss: function() {
            if (this._options.onDismiss) {
                this._options.onDismiss(this);
            }

            if(this._options.disableScroll) {
                Event.stopObserving(window, 'scroll', this._onScrollBinded);
                Event.stopObserving(document, 'touchmove', this._onScrollBinded);
            }

            if( this._options.responsive ){
                Event.stopObserving(window, 'resize', this._handlers.resize);
            }

            // this._modalShadow.parentNode.removeChild(this._modalShadow);

            if( !this._markupMode ){
                this._modalShadow.parentNode.removeChild(this._modalShadow);
                this.destroy();
            } else {
                Css.removeClassName( this._modalDiv, 'visible' );
                Css.removeClassName( this._modalShadow, 'visible' );

                var
                    dismissInterval,
                    transitionEndFn = Ink.bindEvent(function(){
                        if( !dismissInterval ){ return; }
                        this._modalShadowStyle.display = 'none';
                        Event.stopObserving(document,'transitionend',transitionEndFn);
                        Event.stopObserving(document,'oTransitionEnd',transitionEndFn);
                        Event.stopObserving(document,'webkitTransitionEnd',transitionEndFn);
                        clearInterval(dismissInterval);
                        dismissInterval = undefined;
                    }, this)
                ;

                Event.observe(document,'transitionend',transitionEndFn);
                Event.observe(document,'oTransitionEnd',transitionEndFn);
                Event.observe(document,'webkitTransitionEnd',transitionEndFn);

                if( !dismissInterval ){
                    dismissInterval = setInterval(Ink.bind(function(){
                        if( this._modalShadowStyle.opacity > 0 ){
                            return;
                        } else {
                            this._modalShadowStyle.display = 'none';
                            clearInterval(dismissInterval);
                            dismissInterval = undefined;
                        }

                    }, this),500);
                }
            }
        },

        /**
         * Removes the modal from the DOM
         * 
         * @method destroy
         * @public
         */
        destroy: function() {
            Aux.unregisterInstance(this._instanceId);

        },

        /**
         * Returns the content DOM element
         * 
         * @method getContentElement
         * @return {DOMElement} Modal main cointainer.
         * @public
         */
        getContentElement: function() {
            return this._contentContainer;
        },

        /**
         * Replaces the content markup
         * 
         * @method setContentMarkup
         * @param {String} contentMarkup
         * @public
         */
        setContentMarkup: function(contentMarkup) {
            if( !this._markupMode ){
                this._modalDiv.innerHTML = [contentMarkup].join('');
                this._contentContainer = Selector.select(".modal-body",this._modalDiv);
                if( !this._contentContainer.length ){
                    // throw 'Missing div with class "modal-body"';
                    var tempHeader = Selector.select(".modal-header",this._modalDiv);
                    var tempFooter = Selector.select(".modal-footer",this._modalDiv);

                    InkArray.each(tempHeader,Ink.bind(function( element ){ element.parentNode.removeChild(element); },this));
                    InkArray.each(tempFooter,Ink.bind(function( element ){ element.parentNode.removeChild(element); },this));

                    var body = document.createElement('div');
                    Css.addClassName(body,'modal-body');
                    body.innerHTML = this._modalDiv.innerHTML;
                    this._modalDiv.innerHTML = '';

                    InkArray.each(tempHeader,Ink.bind(function( element ){ this._modalDiv.appendChild(element); },this));
                    this._modalDiv.appendChild(body);
                    InkArray.each(tempFooter,Ink.bind(function( element ){ this._modalDiv.appendChild(element); },this));
                    
                    this._contentContainer = Selector.select(".modal-body",this._modalDiv);
                }
                this._contentContainer = this._contentContainer[0];
            } else {
                this._contentContainer.innerHTML = [contentMarkup].join('');
            }
            this._contentElement = this._modalDiv;
            this._resizeContainer();
        }

    };

    return Modal;

});

/**
 * @module Ink.UI.ProgressBar_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.ProgressBar', '1', ['Ink.Dom.Selector_1','Ink.Dom.Element_1'], function( Selector, Element ) {
    'use strict';

    /**
     * Associated to a .ink-progress-bar element, it provides the necessary
     * method - setValue() - for the user to change the element's value.
     * 
     * @class Ink.UI.ProgressBar
     * @constructor
     * @version 1
     * @uses Ink.Dom.Selector
     * @uses Ink.Dom.Element
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *     @param {Number}     [options.startValue]          Percentage of the bar that is filled. Range between 0 and 100. Default: 0
     *     @param {Function}   [options.onStart]             Callback that is called when a change of value is started
     *     @param {Function}   [options.onEnd]               Callback that is called when a change of value ends
     *
     * @example
     *      <div class="ink-progress-bar grey" data-start-value="70%">
     *          <span class="caption">I am a grey progress bar</span>
     *          <div class="bar grey"></div>
     *      </div>
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.ProgressBar_1'], function( Selector, ProgressBar ){
     *              var progressBarElement = Ink.s('.ink-progress-bar');
     *              var progressBarObj = new ProgressBar( progressBarElement );
     *          });
     *      </script>
     */
    var ProgressBar = function( selector, options ){

        if( typeof selector !== 'object' ){
            if( typeof selector !== 'string' ){
                throw '[Ink.UI.ProgressBar] :: Invalid selector';
            } else {
                this._element = Selector.select(selector);
                if( this._element.length < 1 ){
                    throw "[Ink.UI.ProgressBar] :: Selector didn't find any elements";
                }
                this._element = this._element[0];
            }
        } else {
            this._element = selector;
        }


        this._options = Ink.extendObj({
            'startValue': 0,
            'onStart': function(){},
            'onEnd': function(){}
        },Element.data(this._element));

        this._options = Ink.extendObj( this._options, options || {});
        this._value = this._options.startValue;

        this._init();
    };

    ProgressBar.prototype = {

        /**
         * Init function called by the constructor
         * 
         * @method _init
         * @private
         */
        _init: function(){
            this._elementBar = Selector.select('.bar',this._element);
            if( this._elementBar.length < 1 ){
                throw '[Ink.UI.ProgressBar] :: Bar element not found';
            }
            this._elementBar = this._elementBar[0];

            this._options.onStart = Ink.bind(this._options.onStart,this);
            this._options.onEnd = Ink.bind(this._options.onEnd,this);
            this.setValue( this._options.startValue );
        },

        /**
         * Sets the value of the Progressbar
         * 
         * @method setValue
         * @param {Number} newValue Numeric value, between 0 and 100, that represents the percentage of the bar.
         * @public
         */
        setValue: function( newValue ){
            this._options.onStart( this._value);

            newValue = parseInt(newValue,10);
            if( isNaN(newValue) || (newValue < 0) ){
                newValue = 0;
            } else if( newValue>100 ){
                newValue = 100;
            }
            this._value = newValue;
            this._elementBar.style.width =  this._value + '%';

            this._options.onEnd( this._value );
        }
    };

    return ProgressBar;

});

/**
 * @module Ink.UI.SmoothScroller_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.SmoothScroller', '1', ['Ink.Dom.Event_1','Ink.Dom.Selector_1','Ink.Dom.Loaded_1'], function(Event, Selector, Loaded ) {
    'use strict';

    /**
     * @class Ink.UI.SmoothScroller
     * @version 1
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Selector
     * @uses Ink.Dom.Loaded
     * @static
     */
    var SmoothScroller = {

        /**
         * Sets the speed of the scrolling
         *
         * @property
         * @type {Number}
         * @readOnly
         * @static
         */
        speed: 10,

        /**
         * Returns the Y position of the div
         *
         * @method gy
         * @param  {DOMElement} d DOMElement to get the Y position from
         * @return {Number}   Y position of div 'd'
         * @public
         * @static
         */
        gy: function(d) {
            var gy;
            gy = d.offsetTop;
            if (d.offsetParent){
                while ( (d = d.offsetParent) ){
                    gy += d.offsetTop;
                }
            }
            return gy;
        },


        /**
         * Returns the current scroll position
         *
         * @method scrollTop
         * @return {Number}  Current scroll position
         * @public
         * @static
         */
        scrollTop: function() {
            var
                body = document.body,
                d = document.documentElement
            ;
            if (body && body.scrollTop){
                return body.scrollTop;
            }
            if (d && d.scrollTop){
                return d.scrollTop;
            }
            if (window.pageYOffset)
            {
                return window.pageYOffset;
            }
            return 0;
        },

        /**
         * Attaches an event for an element
         *
         * @method add
         * @param  {DOMElement} el DOMElement to make the listening of the event
         * @param  {String} event Event name to be listened
         * @param  {DOMElement} fn Callback function to run when the event is triggered.
         * @public
         * @static
         */
        add: function(el, event, fn) {
            Event.observe(el,event,fn);
            return;
        },


        /**
         * Kill an event of an element
         *
         * @method end
         * @param  {String} e Event to be killed/stopped
         * @public
         * @static
         */
        // kill an event of an element
        end: function(e) {
            if (window.event) {
                window.event.cancelBubble = true;
                window.event.returnValue = false;
                return;
            }
            Event.stop(e);
        },


        /**
         * Moves the scrollbar to the target element
         *
         * @method scroll
         * @param  {Number} d Y coordinate value to stop
         * @public
         * @static
         */
        scroll: function(d) {
            var a = Ink.UI.SmoothScroller.scrollTop();
            if (d > a) {
                a += Math.ceil((d - a) / Ink.UI.SmoothScroller.speed);
            } else {
                a = a + (d - a) / Ink.UI.SmoothScroller.speed;
            }

            window.scrollTo(0, a);
            if ((a) === d || Ink.UI.SmoothScroller.offsetTop === a)
            {
                clearInterval(Ink.UI.SmoothScroller.interval);
            }
            Ink.UI.SmoothScroller.offsetTop = a;
        },


        /**
         * Initializer that adds the rendered to run when the page is ready
         *
         * @method init
         * @public
         * @static
         */
        // initializer that adds the renderer to the onload function of the window
        init: function() {
            Loaded.run(Ink.UI.SmoothScroller.render);
        },

        /**
         * This method extracts all the anchors and validates thenm as # and attaches the events
         *
         * @method render
         * @public
         * @static
         */
        render: function() {
            var a = Selector.select('a.scrollableLink');

            Ink.UI.SmoothScroller.end(this);

            for (var i = 0; i < a.length; i++) {
                var _elm = a[i];
                if (_elm.href && _elm.href.indexOf('#') !== -1 && ((_elm.pathname === location.pathname) || ('/' + _elm.pathname === location.pathname))) {
                    Ink.UI.SmoothScroller.add(_elm, 'click', Ink.UI.SmoothScroller.end);
                    Event.observe(_elm,'click', Ink.bindEvent(Ink.UI.SmoothScroller.clickScroll, this, _elm));
                }
            }
        },


        /**
         * Click handler
         *
         * @method clickScroll
         * @public
         * @static
         */
        clickScroll: function(event, _elm) {
            /*
            Ink.UI.SmoothScroller.end(this);
            var hash = this.hash.substr(1);
            var elm = Selector.select('a[name="' + hash + '"],#' + hash);

            if (typeof(elm[0]) !== 'undefined') {

                if (this.parentNode.className.indexOf('active') === -1) {
                    var ul = this.parentNode.parentNode,
                        li = ul.firstChild;
                    do {
                        if ((typeof(li.tagName) !== 'undefined') && (li.tagName.toUpperCase() === 'LI') && (li.className.indexOf('active') !== -1)) {
                            li.className = li.className.replace('active', '');
                            break;
                        }
                    } while ((li = li.nextSibling));
                    this.parentNode.className += " active";
                }
                clearInterval(Ink.UI.SmoothScroller.interval);
                Ink.UI.SmoothScroller.interval = setInterval('Ink.UI.SmoothScroller.scroll(' + Ink.UI.SmoothScroller.gy(elm[0]) + ')', 10);

            }
            */
            Ink.UI.SmoothScroller.end(_elm);
            if(_elm !== null && _elm.getAttribute('href') !== null) {
                var hashIndex = _elm.href.indexOf('#');
                if(hashIndex === -1) {
                    return;
                }
                var hash = _elm.href.substr((hashIndex + 1));
                var elm = Selector.select('a[name="' + hash + '"],#' + hash);

                if (typeof(elm[0]) !== 'undefined') {

                    if (_elm.parentNode.className.indexOf('active') === -1) {
                        var ul = _elm.parentNode.parentNode,
                            li = ul.firstChild;
                        do {
                            if ((typeof(li.tagName) !== 'undefined') && (li.tagName.toUpperCase() === 'LI') && (li.className.indexOf('active') !== -1)) {
                                li.className = li.className.replace('active', '');
                                break;
                            }
                        } while ((li = li.nextSibling));
                        _elm.parentNode.className += " active";
                    }
                    clearInterval(Ink.UI.SmoothScroller.interval);
                    Ink.UI.SmoothScroller.interval = setInterval('Ink.UI.SmoothScroller.scroll(' + Ink.UI.SmoothScroller.gy(elm[0]) + ')', 10);

                }
            }

        }
    };

    return SmoothScroller;

});

/**
 * @module Ink.UI.SortableList_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.SortableList', '1', ['Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1'], function(Aux, Event, Css, Element, Selector, InkArray ) {
    'use strict';

    /**
     * Adds sortable behaviour to any list!
     * 
     * @class Ink.UI.SortableList
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @uses Ink.Util.Array
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *     @param {String} [options.dragObject] CSS Selector. The element that will trigger the dragging in the list. Default is 'li'.
     * @example
     *      <ul class="unstyled ink-sortable-list" id="slist" data-instance="sortableList9">
     *          <li><span class="ink-label info"><i class="icon-reorder"></i>drag here</span>primeiro</li>
     *          <li><span class="ink-label info"><i class="icon-reorder"></i>drag here</span>segundo</li>
     *          <li><span class="ink-label info"><i class="icon-reorder"></i>drag here</span>terceiro</li>
     *      </ul>
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.SortableList_1'], function( Selector, SortableList ){
     *              var sortableListElement = Ink.s('.ink-sortable-list');
     *              var sortableListObj = new SortableList( sortableListElement );
     *          });
     *      </script>
     */
    var SortableList = function(selector, options) {

        this._element = Aux.elOrSelector(selector, '1st argument');

        if( !Aux.isDOMElement(selector) && (typeof selector !== 'string') ){
            throw '[Ink.UI.SortableList] :: Invalid selector';
        } else if( typeof selector === 'string' ){
            this._element = Ink.Dom.Selector.select( selector );

            if( this._element.length < 1 ){
                throw '[Ink.UI.SortableList] :: Selector has returned no elements';
            }
            this._element = this._element[0];

        } else {
            this._element = selector;
        }

        this._options = Ink.extendObj({
            dragObject: 'li'
        }, Ink.Dom.Element.data(this._element));

        this._options = Ink.extendObj( this._options, options || {});

        this._handlers = {
            down: Ink.bindEvent(this._onDown,this),
            move: Ink.bindEvent(this._onMove,this),
            up:   Ink.bindEvent(this._onUp,this)
        };

        this._model = [];
        this._index = undefined;
        this._isMoving = false;

        if (this._options.model instanceof Array) {
            this._model = this._options.model;
            this._createdFrom = 'JSON';
        }
        else if (this._element.nodeName.toLowerCase() === 'ul') {
            this._createdFrom = 'DOM';
        }
        else {
            throw new TypeError('You must pass a selector expression/DOM element as 1st option or provide a model on 2nd argument!');
        }


        this._dragTriggers = Selector.select( this._options.dragObject, this._element );
        if( !this._dragTriggers ){
            throw "[Ink.UI.SortableList] :: Drag object not found";
        }

        this._init();
    };

    SortableList.prototype = {

        /**
         * Init function called by the constructor.
         * 
         * @method _init
         * @private
         */
        _init: function() {
            // extract model
            if (this._createdFrom === 'DOM') {
                this._extractModelFromDOM();
                this._createdFrom = 'JSON';
            }

            var isTouch = 'ontouchstart' in document.documentElement;

            this._down = isTouch ? 'touchstart': 'mousedown';
            this._move = isTouch ? 'touchmove' : 'mousemove';
            this._up   = isTouch ? 'touchend'  : 'mouseup';

            // subscribe events
            var db = document.body;
            Event.observe(db, this._move, this._handlers.move);
            Event.observe(db, this._up,   this._handlers.up);
            this._observe();

            Aux.registerInstance(this, this._element, 'sortableList');
        },

        /**
         * Sets the event handlers.
         * 
         * @method _observe
         * @private
         */
        _observe: function() {
            Event.observe(this._element, this._down, this._handlers.down);
        },

        /**
         * Updates the model from the UL representation
         * 
         * @method _extractModelFromDOM
         * @private
         */
        _extractModelFromDOM: function() {
            this._model = [];
            var that = this;

            var liEls = Selector.select('> li', this._element);

            InkArray.each(liEls,function(liEl) {
                //var t = Element.getChildrenText(liEl);
                var t = liEl.innerHTML;
                that._model.push(t);
            });
        },

        /**
         * Returns the top element for the gallery DOM representation
         * 
         * @method _generateMarkup
         * @return {DOMElement}
         * @private
         */
        _generateMarkup: function() {
            var el = document.createElement('ul');
            el.className = 'unstyled ink-sortable-list';
            var that = this;

            InkArray.each(this._model,function(label, idx) {
                var liEl = document.createElement('li');
                if (idx === that._index) {
                    liEl.className = 'drag';
                }
                liEl.innerHTML = [
                    // '<span class="ink-label ink-info"><i class="icon-reorder"></i>', that._options.dragLabel, '</span>', label
                    label
                ].join('');
                el.appendChild(liEl);
            });

            return el;
        },

        /**
         * Extracts the Y coordinate of the mouse from the given MouseEvent
         * 
         * @method _getY
         * @param  {Event} ev
         * @return {Number}
         * @private
         */
        _getY: function(ev) {
            if (ev.type.indexOf('touch') === 0) {
                //console.log(ev.type, ev.changedTouches[0].pageY);
                return ev.changedTouches[0].pageY;
            }
            if (typeof ev.pageY === 'number') {
                return ev.pageY;
            }
            return ev.clientY;
        },

        /**
         * Refreshes the markup.
         * 
         * @method _refresh
         * @param {Boolean} skipObs True if needs to set the event handlers, false if not.
         * @private
         */
        _refresh: function(skipObs) {
            var el = this._generateMarkup();
            this._element.parentNode.replaceChild(el, this._element);
            this._element = el;

            Aux.restoreIdAndClasses(this._element, this);

            this._dragTriggers = Selector.select( this._options.dragObject, this._element );

            // subscribe events
            if (!skipObs) { this._observe(); }
        },

        /**
         * Mouse down handler
         * 
         * @method _onDown
         * @param {Event} ev
         * @return {Boolean|undefined} [description]
         * @private
         */
        _onDown: function(ev) {
            if (this._isMoving) { return; }
            var tgtEl = Event.element(ev);

            if( !InkArray.inArray(tgtEl,this._dragTriggers) ){
                while( !InkArray.inArray(tgtEl,this._dragTriggers) && (tgtEl.nodeName.toLowerCase() !== 'body') ){
                    tgtEl = tgtEl.parentNode;
                }

                if( tgtEl.nodeName.toLowerCase() === 'body' ){
                    return;
                }
            }

            Event.stop(ev);

            var liEl;
            if( tgtEl.nodeName.toLowerCase() !== 'li' ){
                while( (tgtEl.nodeName.toLowerCase() !== 'li') && (tgtEl.nodeName.toLowerCase() !== 'body') ){
                    tgtEl = tgtEl.parentNode;
                }
            }
            liEl = tgtEl;

            this._index = Aux.childIndex(liEl);
            this._height = liEl.offsetHeight;
            this._startY = this._getY(ev);
            this._isMoving = true;

            document.body.style.cursor = 'move';

            this._refresh(false);

            return false;
        },

        /**
         * Mouse move handler
         * 
         * @method _onMove
         * @param {Event} ev
         * @private
         */
        _onMove: function(ev) {
            if (!this._isMoving) { return; }
            Event.stop(ev);

            var y = this._getY(ev);
            //console.log(y);
            var dy = y - this._startY;
            var sign = dy > 0 ? 1 : -1;
            var di = sign * Math.floor( Math.abs(dy) / this._height );
            if (di === 0) { return; }
            di = di / Math.abs(di);
            if ( (di === -1 && this._index === 0) ||
                 (di === 1 && this._index === this._model.length - 1) ) { return; }

            var a = di > 0 ? this._index : this._index + di;
            var b = di < 0 ? this._index : this._index + di;
            //console.log(a, b);
            this._model.splice(a, 2, this._model[b], this._model[a]);
            this._index += di;
            this._startY = y;

            this._refresh(false);
        },

        /**
         * Mouse up handler
         * 
         * @method _onUp
         * @param {Event} ev
         * @private
         */
        _onUp: function(ev) {
            if (!this._isMoving) { return; }
            Event.stop(ev);

            this._index = undefined;
            this._isMoving = false;
            document.body.style.cursor = '';

            this._refresh();
        },



        /**************
         * PUBLIC API *
         **************/

        /**
         * Returns a copy of the model
         * 
         * @method getModel
         * @return {Array} Copy of the model
         * @public
         */
        getModel: function() {
            return this._model.slice();
        },

        /**
         * Unregisters the component and removes its markup from the DOM
         * 
         * @method destroy
         * @public
         */
        destroy: Aux.destroyComponent

    };

    return SortableList;

});

/**
 * @module Ink.UI.Spy_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Spy', '1', ['Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1'], function(Aux, Event, Css, Element, Selector, InkArray ) {
    'use strict';

    /**
     * Spy is a component that 'spies' an element (or a group of elements) and when they leave the viewport (through the top),
     * highlight an option - related to that element being spied - that resides in a menu, initially identified as target.
     * 
     * @class Ink.UI.Spy
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @uses Ink.Util.Array
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *     @param {DOMElement|String}     options.target          Target menu on where the spy will highlight the right option.
     * @example
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.Spy_1'], function( Selector, Spy ){
     *              var menuElement = Ink.s('#menu');
     *              var specialAnchorToSpy = Ink.s('#specialAnchor');
     *              var spyObj = new Spy( specialAnchorToSpy, {
     *                  target: menuElement
     *              });
     *          });
     *      </script>
     */
    var Spy = function( selector, options ){

        this._rootElement = Aux.elOrSelector(selector,'1st argument');

        /**
         * Setting default options and - if needed - overriding it with the data attributes
         */
        this._options = Ink.extendObj({
            target: undefined
        }, Element.data( this._rootElement ) );

        /**
         * In case options have been defined when creating the instance, they've precedence
         */
        this._options = Ink.extendObj(this._options,options || {});

        this._options.target = Aux.elOrSelector( this._options.target, 'Target' );

        this._scrollTimeout = null;
        this._init();
    };

    Spy.prototype = {

        /**
         * Stores the spy elements
         *
         * @property _elements
         * @type {Array}
         * @readOnly
         * 
         */
        _elements: [],

        /**
         * Init function called by the constructor
         * 
         * @method _init
         * @private
         */
        _init: function(){
            Event.observe( document, 'scroll', Ink.bindEvent(this._onScroll,this) );
            this._elements.push(this._rootElement);
        },

        /**
         * Scroll handler. Responsible for highlighting the right options of the target menu.
         * 
         * @method _onScroll
         * @private
         */
        _onScroll: function(){

            var scrollHeight = Element.scrollHeight(); 
            if( (scrollHeight < this._rootElement.offsetTop) ){
                return;
            } else {
                for( var i = 0, total = this._elements.length; i < total; i++ ){
                    if( (this._elements[i].offsetTop <= scrollHeight) && (this._elements[i] !== this._rootElement) && (this._elements[i].offsetTop > this._rootElement.offsetTop) ){
                        return;
                    }
                }
            }

            InkArray.each(
                Selector.select(
                    'a',
                    this._options.target
                ), Ink.bind(function(item){

                    var comparisonValue = ( ("name" in this._rootElement) && this._rootElement.name ?
                        '#' + this._rootElement.name : '#' + this._rootElement.id
                    );

                    if( item.href.substr(item.href.indexOf('#')) === comparisonValue ){
                        Css.addClassName(Element.findUpwardsByTag(item,'li'),'active');
                    } else {
                        Css.removeClassName(Element.findUpwardsByTag(item,'li'),'active');
                    }
                },this)
            );
        }

    };

    return Spy;

});

/**
 * @module Ink.UI.Sticky_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Sticky', '1', ['Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1'], function(Aux, Event, Css, Element, Selector ) {
    'use strict';

    /**
     * The Sticky component takes an element and transforms it's behavior in order to, when the user scrolls he sets its position
     * to fixed and maintain it until the user scrolls back to the same place.
     * 
     * @class Ink.UI.Sticky
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *     @param {Number}     options.offsetBottom       Number of pixels of distance from the bottomElement.
     *     @param {Number}     options.offsetTop          Number of pixels of distance from the topElement.
     *     @param {String}     options.topElement         CSS Selector that specifies a top element with which the component could collide.
     *     @param {String}     options.bottomElement      CSS Selector that specifies a bottom element with which the component could collide.
     * @example
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.Sticky_1'], function( Selector, Sticky ){
     *              var menuElement = Ink.s('#menu');
     *              var stickyObj = new Sticky( menuElement );
     *          });
     *      </script>
     */
    var Sticky = function( selector, options ){

        if( typeof selector !== 'object' && typeof selector !== 'string'){
            throw '[Sticky] :: Invalid selector defined';
        }

        if( typeof selector === 'object' ){
            this._rootElement = selector;
        } else {
            this._rootElement = Selector.select( selector );
            if( this._rootElement.length <= 0) {
                throw "[Sticky] :: Can't find any element with the specified selector";
            }
            this._rootElement = this._rootElement[0];
        }

        /**
         * Setting default options and - if needed - overriding it with the data attributes
         */
        this._options = Ink.extendObj({
            offsetBottom: 0,
            offsetTop: 0,
            topElement: undefined,
            bottomElement: undefined
        }, Element.data( this._rootElement ) );

        /**
         * In case options have been defined when creating the instance, they've precedence
         */
        this._options = Ink.extendObj(this._options,options || {});

        if( typeof( this._options.topElement ) !== 'undefined' ){
            this._options.topElement = Aux.elOrSelector( this._options.topElement, 'Top Element');
        } else {
            this._options.topElement = Aux.elOrSelector( 'body', 'Top Element');
        }

        if( typeof( this._options.bottomElement ) !== 'undefined' ){
            this._options.bottomElement = Aux.elOrSelector( this._options.bottomElement, 'Bottom Element');
        } else {
            this._options.bottomElement = Aux.elOrSelector( 'body', 'Top Element');
        }

        this._computedStyle = window.getComputedStyle ? window.getComputedStyle(this._rootElement, null) : this._rootElement.currentStyle;
        this._dims = {
            height: this._computedStyle.height,
            width: this._computedStyle.width
        };
        this._init();
    };

    Sticky.prototype = {

        /**
         * Init function called by the constructor
         * 
         * @method _init
         * @private
         */
        _init: function(){
            Event.observe( document, 'scroll', Ink.bindEvent(this._onScroll,this) );
            Event.observe( window, 'resize', Ink.bindEvent(this._onResize,this) );

            this._calculateOriginalSizes();

            this._calculateOffsets();

        },

        /**
         * Scroll handler.
         * 
         * @method _onScroll
         * @private
         */
        _onScroll: function(){


            var viewport = (document.compatMode === "CSS1Compat") ?  document.documentElement : document.body;

            if( 
                ( ( (Element.elementWidth(this._rootElement)*100)/viewport.clientWidth ) > 90 ) ||
                ( viewport.clientWidth<=649 )
            ){
                if( Element.hasAttribute(this._rootElement,'style') ){
                    this._rootElement.removeAttribute('style');
                }
                return;
            }


            if( this._scrollTimeout ){
                clearTimeout(this._scrollTimeout);
            }

            this._scrollTimeout = setTimeout(Ink.bind(function(){
                    
                var scrollHeight = Element.scrollHeight();

                if( Element.hasAttribute(this._rootElement,'style') ){
                    if( scrollHeight <= this._options.offsetTop){
                        this._rootElement.removeAttribute('style');
                    } else if( ((document.body.scrollHeight-(scrollHeight+parseInt(this._dims.height,10))) < this._options.offsetBottom) ){
                        this._rootElement.style.position = 'fixed';
                        this._rootElement.style.top = 'auto';
                        if( this._options.offsetBottom < parseInt(document.body.scrollHeight - (document.documentElement.clientHeight+scrollHeight),10) ){
                            this._rootElement.style.bottom = this._options.originalOffsetBottom + 'px';
                        } else {
                            this._rootElement.style.bottom = this._options.offsetBottom - parseInt(document.body.scrollHeight - (document.documentElement.clientHeight+scrollHeight),10) + 'px';
                        }
                        this._rootElement.style.width = this._options.originalWidth + 'px';
                    } else if( ((document.body.scrollHeight-(scrollHeight+parseInt(this._dims.height,10))) >= this._options.offsetBottom) ){
                        this._rootElement.style.position = 'fixed';
                        this._rootElement.style.bottom = 'auto';
                        this._rootElement.style.top = this._options.originalOffsetTop + 'px';
                        this._rootElement.style.width = this._options.originalWidth + 'px';
                    }
                } else {
                    if(scrollHeight <= this._options.offsetTop ){
                        return;
                    }

                    this._rootElement.style.position = 'fixed';
                    this._rootElement.style.bottom = 'auto';
                    this._rootElement.style.top = this._options.offsetTop + 'px';
                    this._rootElement.style.width = this._options.originalWidth + 'px';
                }

                this._scrollTimeout = undefined;
            },this), 0);
        },

        /**
         * Resize handler
         * 
         * @method _onResize
         * @private
         */
        _onResize: function(){

            if( this._resizeTimeout ){
                clearTimeout(this._resizeTimeout);
            }

            this._resizeTimeout = setTimeout(Ink.bind(function(){
                this._rootElement.removeAttribute('style');
                this._calculateOriginalSizes();
                this._calculateOffsets();
            }, this),0);

        },

        /**
         * On each resizing (and in the beginning) the component recalculates the offsets, since
         * the top and bottom element heights might have changed.
         * 
         * @method _calculateOffsets
         * @private
         */
        _calculateOffsets: function(){

            /**
             * Calculating the offset top
             */
            if( typeof this._options.topElement !== 'undefined' ){


                if( this._options.topElement.nodeName.toLowerCase() !== 'body' ){
                    var
                        topElementHeight = Element.elementHeight( this._options.topElement ),
                        topElementTop = Element.elementTop( this._options.topElement )
                    ;

                    this._options.offsetTop = ( parseInt(topElementHeight,10) + parseInt(topElementTop,10) ) + parseInt(this._options.originalOffsetTop,10);
                } else {
                    this._options.offsetTop = parseInt(this._options.originalOffsetTop,10);
                }
            }

            /**
             * Calculating the offset bottom
             */
            if( typeof this._options.bottomElement !== 'undefined' ){

                if( this._options.bottomElement.nodeName.toLowerCase() !== 'body' ){
                    var
                        bottomElementHeight = Element.elementHeight(this._options.bottomElement)
                    ;
                    this._options.offsetBottom = parseInt(bottomElementHeight,10) + parseInt(this._options.originalOffsetBottom,10);
                } else {
                    this._options.offsetBottom = parseInt(this._options.originalOffsetBottom,10);
                }
            }

            this._onScroll();

        },

        /**
         * Function to calculate the 'original size' of the element.
         * It's used in the begining (_init method) and when a scroll happens
         *
         * @method _calculateOriginalSizes
         * @private
         */
        _calculateOriginalSizes: function(){
            this._options.originalOffsetTop = parseInt(this._options.offsetTop,10);
            this._options.originalOffsetBottom = parseInt(this._options.offsetBottom,10);
            this._options.originalTop = parseInt(this._rootElement.offsetTop,10);
            if(isNaN(this._options.originalWidth = parseInt(this._dims.width,10))) {
                this._options.originalWidth = 0;
            }
            this._options.originalWidth = parseInt(this._computedStyle.width,10);
        }

    };

    return Sticky;

});

/**
 * @module Ink.UI.Table_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Table', '1', ['Ink.Net.Ajax_1','Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1','Ink.Util.String_1'], function(Ajax, Aux, Event, Css, Element, Selector, InkArray, InkString ) {
    'use strict';

    /**
     * The Table component transforms the native/DOM table element into a
     * sortable, paginated component.
     * 
     * @class Ink.UI.Table
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @uses Ink.Util.Array
     * @uses Ink.UI.Pagination
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *     @param {Number}     options.pageSize       Number of rows per page.
     *     @param {String}     options.endpoint       Endpoint to get the records via AJAX
     * @example
     *      <table class="ink-table alternating" data-page-size="6">
     *          <thead>
     *              <tr>
     *                  <th data-sortable="true" width="75%">Pepper</th>
     *                  <th data-sortable="true" width="25%">Scoville Rating</th>
     *              </tr>
     *          </thead>
     *          <tbody>
     *              <tr>
     *                  <td>Trinidad Moruga Scorpion</td>
     *                  <td>1500000</td>
     *              </tr>
     *              <tr>
     *                  <td>Bhut Jolokia</td>
     *                  <td>1000000</td>
     *              </tr>
     *              <tr>
     *                  <td>Naga Viper</td>
     *                  <td>1463700</td>
     *              </tr>
     *              <tr>
     *                  <td>Red Savina Habanero</td>
     *                  <td>580000</td>
     *              </tr>
     *              <tr>
     *                  <td>Habanero</td>
     *                  <td>350000</td>
     *              </tr>
     *              <tr>
     *                  <td>Scotch Bonnet</td>
     *                  <td>180000</td>
     *              </tr>
     *              <tr>
     *                  <td>Malagueta</td>
     *                  <td>50000</td>
     *              </tr>
     *              <tr>
     *                  <td>Tabasco</td>
     *                  <td>35000</td>
     *              </tr>
     *              <tr>
     *                  <td>Serrano Chili</td>
     *                  <td>27000</td>
     *              </tr>
     *              <tr>
     *                  <td>Jalapeño</td>
     *                  <td>8000</td>
     *              </tr>
     *              <tr>
     *                  <td>Poblano</td>
     *                  <td>1500</td>
     *              </tr>
     *              <tr>
     *                  <td>Peperoncino</td>
     *                  <td>500</td>
     *              </tr>
     *          </tbody>
     *      </table>
     *      <nav class="ink-navigation"><ul class="pagination"></ul></nav>
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.Table_1'], function( Selector, Table ){
     *              var tableElement = Ink.s('.ink-table');
     *              var tableObj = new Table( tableElement );
     *          });
     *      </script>
     */
    var Table = function( selector, options ){

        /**
         * Get the root element
         */
        this._rootElement = Aux.elOrSelector(selector, '1st argument');

        if( this._rootElement.nodeName.toLowerCase() !== 'table' ){
            throw '[Ink.UI.Table] :: The element is not a table';
        }

        this._options = Ink.extendObj({
            pageSize: undefined,
            endpoint: undefined,
            loadMode: 'full',
            allowResetSorting: false,
            visibleFields: undefined
        },Element.data(this._rootElement));

        this._options = Ink.extendObj( this._options, options || {});

        /**
         * Checking if it's in markup mode or endpoint mode
         */
        this._markupMode = ( typeof this._options.endpoint === 'undefined' );

        if( !!this._options.visibleFields ){
            this._options.visibleFields = this._options.visibleFields.split(',');
        }

        /**
         * Initializing variables
         */
        this._handlers = {
            click: Ink.bindEvent(this._onClick,this)
        };
        this._originalFields = [];
        this._sortableFields = {};
        this._originalData = this._data = [];
        this._headers = [];
        this._pagination = null;
        this._totalRows = 0;

        this._init();
    };

    Table.prototype = {

        /**
         * Init function called by the constructor
         * 
         * @method _init
         * @private
         */
        _init: function(){

            /**
             * If not is in markup mode, we have to do the initial request
             * to get the first data and the headers
             */
             if( !this._markupMode ){
                this._getData( this._options.endpoint, true );
             } else{
                this._setHeadersHandlers();

                /**
                 * Getting the table's data
                 */
                InkArray.each(Selector.select('tbody tr',this._rootElement),Ink.bind(function(tr){
                    this._data.push(tr);
                },this));
                this._originalData = this._data.slice(0);

                this._totalRows = this._data.length;

                /**
                 * Set pagination if defined
                 * 
                 */
                if( ("pageSize" in this._options) && (typeof this._options.pageSize !== 'undefined') ){
                    /**
                     * Applying the pagination
                     */
                    this._pagination = this._rootElement.nextSibling;
                    while(this._pagination.nodeType !== 1){
                        this._pagination = this._pagination.nextSibling;
                    }

                    if( this._pagination.nodeName.toLowerCase() !== 'nav' ){
                        throw '[Ink.UI.Table] :: Missing the pagination markup or is mis-positioned';
                    }

                    var Pagination = Ink.getModule('Ink.UI.Pagination',1);

                    this._pagination = new Pagination( this._pagination, {
                        size: Math.ceil(this._totalRows/this._options.pageSize),
                        onChange: Ink.bind(function( pagingObj ){
                            this._paginate( (pagingObj._current+1) );
                        },this)
                    });

                    this._paginate(1);
                }
             }

        },

        /**
         * Click handler. This will mainly handle the sorting (when you click in the headers)
         * 
         * @method _onClick
         * @param {Event} event Event obj
         * @private
         */
        _onClick: function( event ){
            Event.stop(event);
            var
                tgtEl = Event.element(event),
                dataset = Element.data(tgtEl),
                index,i,
                paginated = ( ("pageSize" in this._options) && (typeof this._options.pageSize !== 'undefined') )
            ;
            if( (tgtEl.nodeName.toLowerCase() !== 'th') || ( !("sortable" in dataset) || (dataset.sortable.toString() !== 'true') ) ){
                return;
            }

            index = -1;
            if( InkArray.inArray( tgtEl,this._headers ) ){
                for( i=0; i<this._headers.length; i++ ){
                    if( this._headers[i] === tgtEl ){
                        index = i;
                        break;
                    }
                }
            }

            if( !this._markupMode && paginated ){

                for( var prop in this._sortableFields ){
                    if( prop !== ('col_' + index) ){
                        this._sortableFields[prop] = 'none';
                        this._headers[prop.replace('col_','')].innerHTML = InkString.stripTags(this._headers[prop.replace('col_','')].innerHTML);
                    }
                }

                if( this._sortableFields['col_'+index] === 'asc' )
                {
                    this._sortableFields['col_'+index] = 'desc';
                    this._headers[index].innerHTML = InkString.stripTags(this._headers[index].innerHTML) + '<i class="icon-caret-down"></i>';
                } else {
                    this._sortableFields['col_'+index] = 'asc';
                    this._headers[index].innerHTML = InkString.stripTags(this._headers[index].innerHTML) + '<i class="icon-caret-up"></i>';

                }

                this._pagination.setCurrent(this._pagination._current);

            } else {

                if( index === -1){
                    return;
                }

                if( (this._sortableFields['col_'+index] === 'desc') && (this._options.allowResetSorting && (this._options.allowResetSorting.toString() === 'true')) )
                {
                    this._headers[index].innerHTML = InkString.stripTags(this._headers[index].innerHTML);
                    this._sortableFields['col_'+index] = 'none';

                    // if( !found ){
                        this._data = this._originalData.slice(0);
                    // }
                } else {

                    for( var prop in this._sortableFields ){
                        if( prop !== ('col_' + index) ){
                            this._sortableFields[prop] = 'none';
                            this._headers[prop.replace('col_','')].innerHTML = InkString.stripTags(this._headers[prop.replace('col_','')].innerHTML);
                        }
                    }

                    this._sort(index);

                    if( this._sortableFields['col_'+index] === 'asc' )
                    {
                        this._data.reverse();
                        this._sortableFields['col_'+index] = 'desc';
                        this._headers[index].innerHTML = InkString.stripTags(this._headers[index].innerHTML) + '<i class="icon-caret-down"></i>';
                    } else {
                        this._sortableFields['col_'+index] = 'asc';
                        this._headers[index].innerHTML = InkString.stripTags(this._headers[index].innerHTML) + '<i class="icon-caret-up"></i>';

                    }
                }


                var tbody = Selector.select('tbody',this._rootElement)[0];
                Aux.cleanChildren(tbody);
                InkArray.each(this._data,function(item){
                    tbody.appendChild(item);
                });

                this._pagination.setCurrent(0);
                this._paginate(1);
            }
        },

        /**
         * Applies and/or changes the CSS classes in order to show the right columns
         * 
         * @method _paginate
         * @param {Number} page Current page
         * @private
         */
        _paginate: function( page ){
            InkArray.each(this._data,Ink.bind(function(item, index){
                if( (index >= ((page-1)*parseInt(this._options.pageSize,10))) && (index < (((page-1)*parseInt(this._options.pageSize,10))+parseInt(this._options.pageSize,10)) ) ){
                    Css.removeClassName(item,'hide-all');
                } else {
                    Css.addClassName(item,'hide-all');
                }
            },this));
        },

        /**
         * Sorts by a specific column.
         * 
         * @method _sort
         * @param {Number} index Column number (starting at 0)
         * @private
         */
        _sort: function( index ){
            this._data.sort(Ink.bind(function(a,b){
                var
                    aValue = Selector.select('td',a)[index].innerText,
                    bValue = Selector.select('td',b)[index].innerText
                ;

                var regex = new RegExp(/\d/g);
                if( !isNaN(aValue) && regex.test(aValue) ){
                    aValue = parseInt(aValue,10);
                } else if( !isNaN(aValue) ){
                    aValue = parseFloat(aValue);
                }

                if( !isNaN(bValue) && regex.test(bValue) ){
                    bValue = parseInt(bValue,10);
                } else if( !isNaN(bValue) ){
                    bValue = parseFloat(bValue);
                }

                if( aValue === bValue ){
                    return 0;
                } else {
                    return ( ( aValue>bValue ) ? 1 : -1 );
                }
            },this));
        },

        /**
         * Assembles the headers markup
         *
         * @method _setHeaders
         * @param  {Object} headers Key-value object that contains the fields as keys, their configuration (label and sorting ability) as value
         * @private
         */
        _setHeaders: function( headers, rows ){
            var
                field, header,
                thead, tr, th,
                index = 0
            ;

            if( (thead = Selector.select('thead',this._rootElement)).length === 0 ){
                thead = this._rootElement.createTHead();
                tr = thead.insertRow(0);

                for( field in headers ){

                    if( !!this._options.visibleFields && (this._options.visibleFields.indexOf(field) === -1) ){
                        continue;
                    }

                    // th = tr.insertCell(index++);
                    th = document.createElement('th');
                    header = headers[field];

                    if( ("sortable" in header) && (header.sortable.toString() === 'true') ){
                        th.setAttribute('data-sortable','true');
                    }

                    if( ("label" in header) ){
                        th.innerText = header.label;
                    }

                    this._originalFields.push(field);
                    tr.appendChild(th);
                }
            } else {
                var firstLine = rows[0];

                for( field in firstLine ){
                    if( !!this._options.visibleFields && (this._options.visibleFields.indexOf(field) === -1) ){
                        continue;
                    }

                    this._originalFields.push(field);
                }
            }
        },

        /**
         * Method that sets the handlers for the headers
         *
         * @method _setHeadersHandlers
         * @private
         */
        _setHeadersHandlers: function(){

            /**
             * Setting the sortable columns and its event listeners
             */
            Event.observe(Selector.select('thead',this._rootElement)[0],'click',this._handlers.click);
            this._headers = Selector.select('thead tr th',this._rootElement);
            InkArray.each(this._headers,Ink.bind(function(item, index){
                var dataset = Element.data( item );
                if( ('sortable' in dataset) && (dataset.sortable.toString() === 'true') ){
                    this._sortableFields['col_' + index] = 'none';
                }
            },this));

        },

        /**
         * This method gets the rows from AJAX and places them as <tr> and <td>
         *
         * @method _setData
         * @param  {Object} rows Array of objects with the data to be showed
         * @private
         */
        _setData: function( rows ){

            var
                field,
                tbody, tr, td,
                trIndex,
                tdIndex
            ;

            tbody = Selector.select('tbody',this._rootElement);
            if( tbody.length === 0){
                tbody = document.createElement('tbody');
                this._rootElement.appendChild( tbody );
            } else {
                tbody = tbody[0];
                tbody.innerHTML = '';
            }

            this._data = [];


            for( trIndex in rows ){
                tr = document.createElement('tr');
                tbody.appendChild( tr );
                tdIndex = 0;
                for( field in rows[trIndex] ){

                    if( !!this._options.visibleFields && (this._options.visibleFields.indexOf(field) === -1) ){
                        continue;
                    }

                    td = tr.insertCell(tdIndex++);
                    td.innerHTML = rows[trIndex][field];
                }
                this._data.push(tr);
            }

            this._originalData = this._data.slice(0);
        },

        /**
         * Sets the endpoint. Useful for changing the endpoint in runtime.
         *
         * @method _setEndpoint
         * @param {String} endpoint New endpoint
         */
        setEndpoint: function( endpoint, currentPage ){
            if( !this._markupMode ){
                this._options.endpoint = endpoint;
                this._pagination.setCurrent( (!!currentPage) ? parseInt(currentPage,10) : 0 );
            }
        },

        /**
         * Checks if it needs the pagination and creates the necessary markup to have pagination
         *
         * @method _setPagination
         * @private
         */
        _setPagination: function(){
            var paginated = ( ("pageSize" in this._options) && (typeof this._options.pageSize !== 'undefined') );
            /**
             * Set pagination if defined
             */
            if( ("pageSize" in this._options) && (typeof this._options.pageSize !== 'undefined') ){
                /**
                 * Applying the pagination
                 */
                if( !this._pagination ){
                    this._pagination = document.createElement('nav');
                    this._pagination.className = 'ink-navigation';
                    this._rootElement.parentNode.insertBefore(this._pagination,this._rootElement.nextSibling);
                    this._pagination.appendChild( document.createElement('ul') ).className = 'pagination';

                    var Pagination = Ink.getModule('Ink.UI.Pagination',1);

                    this._pagination = new Pagination( this._pagination, {
                        size: Math.ceil(this._totalRows/this._options.pageSize),
                        onChange: Ink.bind(function( ){
                            this._getData( this._options.endpoint );
                        },this)
                    }); 
                }
            }
        },

        /**
         * Method to choose which is the best way to get the data based on the endpoint:
         *     - AJAX
         *     - JSONP
         *
         * @method _getData
         * @param  {String} endpoint     Valid endpoint
         * @param  {Boolean} [firstRequest] If true, will make the request set the headers onSuccess
         * @private
         */
        _getData: function( endpoint ){

            Ink.requireModules(['Ink.Util.Url_1'],Ink.bind(function( InkURL ){

                var
                    parsedURL = InkURL.parseUrl( endpoint ),
                    paginated = ( ("pageSize" in this._options) && (typeof this._options.pageSize !== 'undefined') ),
                    pageNum = ((!!this._pagination) ? this._pagination._current+1 : 1)
                ;

                if( parsedURL.query ){
                    parsedURL.query = parsedURL.query.split("&");
                } else {
                    parsedURL.query = [];
                }

                if( !paginated ){            
                    this._getDataViaAjax( endpoint );
                } else {

                    parsedURL.query.push( 'rows_per_page=' + this._options.pageSize );
                    parsedURL.query.push( 'page=' + pageNum );

                    var sortStr = '';
                    for( var index in this._sortableFields ){
                        if( this._sortableFields[index] !== 'none' ){
                            parsedURL.query.push('sortField=' + this._originalFields[parseInt(index.replace('col_',''),10)]);
                            parsedURL.query.push('sortOrder=' + this._sortableFields[index]);
                            break;
                        }
                    }

                    this._getDataViaAjax( endpoint + '?' + parsedURL.query.join('&') );
                }

            },this));

        },

        /**
         * Gets the data via AJAX and triggers the changes in the 
         * 
         * @param  {[type]} endpoint     [description]
         * @param  {[type]} firstRequest [description]
         * @return {[type]}              [description]
         */
        _getDataViaAjax: function( endpoint ){

            var paginated = ( ("pageSize" in this._options) && (typeof this._options.pageSize !== 'undefined') );

            new Ajax( endpoint, {
                method: 'GET',
                contentType: 'application/json',
                sanitizeJSON: true,
                onSuccess: Ink.bind(function( response ){
                    if( response.status === 200 ){

                        var jsonResponse = JSON.parse( response.responseText );

                        if( this._headers.length === 0 ){
                            this._setHeaders( jsonResponse.headers, jsonResponse.rows );
                            this._setHeadersHandlers();
                        }

                        this._setData( jsonResponse.rows );

                        if( paginated ){
                            if( !!this._totalRows && (parseInt(jsonResponse.totalRows,10) !== parseInt(this._totalRows,10)) ){ 
                                this._totalRows = jsonResponse.totalRows;
                                this._pagination.setSize( Math.ceil(this._totalRows/this._options.pageSize) );
                            } else {
                                this._totalRows = jsonResponse.totalRows;
                            }
                        } else {
                            if( !!this._totalRows && (jsonResponse.rows.length !== parseInt(this._totalRows,10)) ){ 
                                this._totalRows = jsonResponse.rows.length;
                                this._pagination.setSize( Math.ceil(this._totalRows/this._options.pageSize) );
                            } else {
                                this._totalRows = jsonResponse.rows.length;
                            }
                        }

                        this._setPagination( );
                    }

                },this)
            } );
        }
    };

    return Table;

});

/**
 * @module Ink.UI.Tabs_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Tabs', '1', ['Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1'], function(Aux, Event, Css, Element, Selector, InkArray ) {
    'use strict';

    /**
     * Tabs component
     * 
     * @class Ink.UI.Tabs
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @uses Ink.Util.Array
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *     @param {Boolean}      [options.preventUrlChange]        Flag that determines if follows the link on click or stops the event
     *     @param {String}       [options.active]                  ID of the tab to activate on creation
     *     @param {Array}        [options.disabled]                IDs of the tabs that will be disabled on creation
     *     @param {Function}     [options.onBeforeChange]          callback to be executed before changing tabs
     *     @param {Function}     [options.onChange]                callback to be executed after changing tabs
     * @example
     *      <div class="ink-tabs top"> <!-- replace 'top' with 'bottom', 'left' or 'right' to place navigation -->
     *          
     *          <!-- put navigation first if using top, left or right positioning -->
     *          <ul class="tabs-nav">
     *              <li><a href="#home">Home</a></li>
     *              <li><a href="#news">News</a></li>
     *              <li><a href="#description">Description</a></li>
     *              <li><a href="#stuff">Stuff</a></li>
     *              <li><a href="#more_stuff">More stuff</a></li>
     *          </ul>
     *          
     *          <!-- Put your content second if using top, left or right navigation -->
     *          <div id="home" class="tabs-content"><p>Content</p></div>
     *          <div id="news" class="tabs-content"><p>Content</p></div>
     *          <div id="description" class="tabs-content"><p>Content</p></div>
     *          <div id="stuff" class="tabs-content"><p>Content</p></div>
     *          <div id="more_stuff" class="tabs-content"><p>Content</p></div>
     *          <!-- If you're using bottom navigation, switch the nav block with the content blocks -->
     *       
     *      </div>
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.Tabs_1'], function( Selector, Tabs ){
     *              var tabsElement = Ink.s('.ink-tabs');
     *              var tabsObj = new Tabs( tabsElement );
     *          });
     *      </script>
     */
    var Tabs = function(selector, options) {

        if (!Aux.isDOMElement(selector)) {
            selector = Selector.select(selector);
            if (selector.length === 0) { throw new TypeError('1st argument must either be a DOM Element or a selector expression!'); }
            this._element = selector[0];
        } else {
            this._element = selector;
        }


        this._options = Ink.extendObj({
            preventUrlChange: false,
            active: undefined,
            disabled: [],
            onBeforeChange: undefined,
            onChange: undefined
        }, Element.data(selector));

        this._options = Ink.extendObj(this._options,options || {});

        this._handlers = {
            tabClicked: Ink.bindEvent(this._onTabClicked,this),
            disabledTabClicked: Ink.bindEvent(this._onDisabledTabClicked,this),
            resize: Ink.bindEvent(this._onResize,this)
        };

        this._init();
    };

    Tabs.prototype = {

        /**
         * Init function called by the constructor
         * 
         * @method _init
         * @private
         */
        _init: function() {
            this._menu = Selector.select('.tabs-nav', this._element)[0];
            this._menuTabs = this._getChildElements(this._menu);
            this._contentTabs = Selector.select('.tabs-content', this._element);

            //initialization of the tabs, hides all content before setting the active tab
            this._initializeDom();

            // subscribe events
            this._observe();

            //sets the first active tab
            this._setFirstActive();

            //shows the active tab
            this._changeTab(this._activeMenuLink);

            this._handlers.resize();

            Aux.registerInstance(this, this._element, 'tabs');
        },

        /**
         * Initialization of the tabs, hides all content before setting the active tab
         * 
         * @method _initializeDom
         * @private
         */
        _initializeDom: function(){
            for(var i = 0; i < this._contentTabs.length; i++){
                Css.hide(this._contentTabs[i]);
            }
        },

        /**
         * Subscribe events
         * 
         * @method _observe
         * @private
         */
        _observe: function() {
            InkArray.each(this._menuTabs,Ink.bind(function(elem){
                var link = Selector.select('a', elem)[0];
                if(InkArray.inArray(link.getAttribute('href'), this._options.disabled)){
                    this.disable(link);
                } else {
                    this.enable(link);
                }
            },this));

            Event.observe(window, 'resize', this._handlers.resize);
        },

        /**
         * Run at instantiation, to determine which is the first active tab
         * fallsback from window.location.href to options.active to the first not disabled tab
         * 
         * @method _setFirstActive
         * @private
         */
        _setFirstActive: function() {
            var hash = window.location.hash;
            this._activeContentTab = Selector.select(hash, this._element)[0] ||
                                     Selector.select(this._hashify(this._options.active), this._element)[0] ||
                                     Selector.select('.tabs-content', this._element)[0];

            this._activeMenuLink = this._findLinkByHref(this._activeContentTab.getAttribute('id'));
            this._activeMenuTab = this._activeMenuLink.parentNode;
        },

        /**
         * Changes to the desired tab
         * 
         * @method _changeTab
         * @param {DOMElement} link             anchor linking to the content container
         * @param {boolean}    runCallbacks     defines if the callbacks should be run or not
         * @private
         */
        _changeTab: function(link, runCallbacks){
            if(runCallbacks && typeof this._options.onBeforeChange !== 'undefined'){
                this._options.onBeforeChange(this);
            }

            var selector = link.getAttribute('href');
            Css.removeClassName(this._activeMenuTab, 'active');
            Css.removeClassName(this._activeContentTab, 'active');
            Css.addClassName(this._activeContentTab, 'hide-all');

            this._activeMenuLink = link;
            this._activeMenuTab = this._activeMenuLink.parentNode;
            this._activeContentTab = Selector.select(selector.substr(selector.indexOf('#')), this._element)[0];

            Css.addClassName(this._activeMenuTab, 'active');
            Css.addClassName(this._activeContentTab, 'active');
            Css.removeClassName(this._activeContentTab, 'hide-all');
            Css.show(this._activeContentTab);

            if(runCallbacks && typeof(this._options.onChange) !== 'undefined'){
                this._options.onChange(this);
            }
        },

        /**
         * Tab clicked handler
         * 
         * @method _onTabClicked
         * @param {Event} ev
         * @private
         */
        _onTabClicked: function(ev) {
            Event.stop(ev);

            var target = Event.findElement(ev, 'A');
            if(target.nodeName.toLowerCase() !== 'a') {
                return;
            }

            if( this._options.preventUrlChange.toString() !== 'true'){
                window.location.hash = target.getAttribute('href').substr(target.getAttribute('href').indexOf('#'));
            }

            if(target === this._activeMenuLink){
                return;
            }
            this.changeTab(target);
        },

        /**
         * Disabled tab clicked handler
         * 
         * @method _onDisabledTabClicked
         * @param {Event} ev
         * @private
         */
        _onDisabledTabClicked: function(ev) {
            Event.stop(ev);
        },

        /**
         * Resize handler
         * 
         * @method _onResize
         * @private
         */
        _onResize: function(){
            var currentLayout = Aux.currentLayout();
            if(currentLayout === this._lastLayout){
                return;
            }

            if(currentLayout === Aux.Layouts.SMALL || currentLayout === Aux.Layouts.MEDIUM){
                Css.removeClassName(this._menu, 'menu');
                Css.removeClassName(this._menu, 'horizontal');
                // Css.addClassName(this._menu, 'pills');
            } else {
                Css.addClassName(this._menu, 'menu');
                Css.addClassName(this._menu, 'horizontal');
                // Css.removeClassName(this._menu, 'pills');
            }
            this._lastLayout = currentLayout;
        },

        /*****************
         * Aux Functions *
         *****************/

        /**
         * Allows the hash to be passed with or without the cardinal sign
         * 
         * @method _hashify
         * @param {String} hash     the string to be hashified
         * @return {String} Resulting hash
         * @private
         */
        _hashify: function(hash){
            if(!hash){
                return "";
            }
            return hash.indexOf('#') === 0? hash : '#' + hash;
        },

        /**
         * Returns the anchor with the desired href
         * 
         * @method _findLinkBuHref
         * @param {String} href     the href to be found on the returned link
         * @return {String|undefined} [description]
         * @private
         */
        _findLinkByHref: function(href){
            href = this._hashify(href);
            var ret;
            InkArray.each(this._menuTabs,Ink.bind(function(elem){
                var link = Selector.select('a', elem)[0];
                if( (link.getAttribute('href').indexOf('#') !== -1) && ( link.getAttribute('href').substr(link.getAttribute('href').indexOf('#')) === href ) ){
                    ret = link;
                }
            },this));
            return ret;
        },

        /**
         * Returns the child elements of a given parent element
         * 
         * @method _getChildElements
         * @param {DOMElement} parent  DOMElement to fetch the child elements from.
         * @return {Array}  Child elements of the given parent.
         * @private
         */
        _getChildElements: function(parent){
            var childNodes = [];
            var children = parent.children;
            for(var i = 0; i < children.length; i++){
                if(children[i].nodeType === 1){
                    childNodes.push(children[i]);
                }
            }
            return childNodes;
        },

        /**************
         * PUBLIC API *
         **************/

        /**
         * Changes to the desired tag
         * 
         * @method changeTab
         * @param {String|DOMElement} selector      the id of the desired tab or the link that links to it
         * @public
         */
        changeTab: function(selector) {
            var element = (selector.nodeType === 1)? selector : this._findLinkByHref(this._hashify(selector));
            if(!element || Css.hasClassName(element, 'ink-disabled')){
                return;
            }
            this._changeTab(element, true);
        },

        /**
         * Disables the desired tag
         * 
         * @method disable
         * @param {String|DOMElement} selector      the id of the desired tab or the link that links to it
         * @public
         */
        disable: function(selector){
            var element = (selector.nodeType === 1)? selector : this._findLinkByHref(this._hashify(selector));
            if(!element){
                return;
            }
            Event.stopObserving(element, 'click', this._handlers.tabClicked);
            Event.observe(element, 'click', this._handlers.disabledTabClicked);
            Css.addClassName(element, 'ink-disabled');
        },

         /**
         * Enables the desired tag
         * 
         * @method enable
         * @param {String|DOMElement} selector      the id of the desired tab or the link that links to it
         * @public
         */
        enable: function(selector){
            var element = (selector.nodeType === 1)? selector : this._findLinkByHref(this._hashify(selector));
            if(!element){
                return;
            }
            Event.stopObserving(element, 'click', this._handlers.disabledTabClicked);
            Event.observe(element, 'click', this._handlers.tabClicked);
            Css.removeClassName(element, 'ink-disabled');
        },

        /***********
         * Getters *
         ***********/

        /**
         * Returns the active tab id
         * 
         * @method activeTab
         * @return {String} ID of the active tab.
         * @public
         */
        activeTab: function(){
            return this._activeContentTab.getAttribute('id');
        },

        /**
         * Returns the current active Menu LI
         * 
         * @method activeMenuTab
         * @return {DOMElement} Active menu LI.
         * @public
         */
        activeMenuTab: function(){
            return this._activeMenuTab;
        },

        /**
         * Returns the current active Menu anchorChanges to the desired tag
         * 
         * @method activeMenuLink
         * @return {DOMElement} Active menu link
         * @public
         */
        activeMenuLink: function(){
            return this._activeMenuLink;
        },

        /**
         * Returns the current active Content Tab
         * 
         * @method activeContentTab
         * @return {DOMElement} Active Content Tab
         * @public
         */
        activeContentTab: function(){
            return this._activeContentTab;
        },

        /**
         * Unregisters the component and removes its markup from the DOM
         * 
         * @method destroy
         * @public
         */
        destroy: Aux.destroyComponent
    };

    return Tabs;

});

/**
 * @module Ink.UI.ImageQuery_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.ImageQuery', '1', ['Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1'], function(Aux, Event, Css, Element, Selector, InkArray ) {
    'use strict';

    /**
     * @class Ink.UI.ImageQuery
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @uses Ink.Util.Array
     *
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *      @param {String|Function}    [options.src]             String or Callback function (that returns a string) with the path to be used to get the images.
     *      @param {String|Function}    [options.retina]          String or Callback function (that returns a string) with the path to be used to get RETINA specific images.
     *      @param {Array}              [options.queries]         Array of queries
     *          @param {String}              [options.queries.label]         Label of the query. Ex. 'small'
     *          @param {Number}              [options.queries.width]         Min-width to use this query
     *      @param {Function}           [options.onLoad]          Date format string
     *
     * @example
     *      <div class="imageQueryExample large-100 medium-100 small-100 content-center clearfix vspace">
     *          <img src="/assets/imgs/imagequery/small/image.jpg" />
     *      </div>
     *      <script type="text/javascript">
     *      Ink.requireModules( ['Ink.Dom.Selector_1', 'Ink.UI.ImageQuery_1'], function( Selector, ImageQuery ){
     *          var imageQueryElement = Ink.s('.imageQueryExample img');
     *          var imageQueryObj = new ImageQuery('.imageQueryExample img',{
     *              src: '/assets/imgs/imagequery/{:label}/{:file}',
     *              queries: [
     *                  {
     *                      label: 'small',
     *                      width: 480
     *                  },
     *                  {
     *                      label: 'medium',
     *                      width: 640
     *                  },
     *                  {
     *                      label: 'large',
     *                      width: 1024
     *                  }   
     *              ]
     *          });
     *      } );
     *      </script>
     */
    var ImageQuery = function(selector, options){

        /**
         * Selector's type checking
         */
        if( !Aux.isDOMElement(selector) && (typeof selector !== 'string') ){
            throw '[ImageQuery] :: Invalid selector';
        } else if( typeof selector === 'string' ){
            this._element = Selector.select( selector );

            if( this._element.length < 1 ){
                throw '[ImageQuery] :: Selector has returned no elements';
            } else if( this._element.length > 1 ){
                var i;
                for( i=1;i<this._element.length;i+=1 ){
                    new Ink.UI.ImageQuery(this._element[i],options);
                }
            }
            this._element = this._element[0];

        } else {
            this._element = selector;
        }


        /**
         * Default options and they're overrided by data-attributes if any.
         * The parameters are:
         * @param {array} queries Array of objects that determine the label/name and its min-width to be applied.
         * @param {boolean} allowFirstLoad Boolean flag to allow the loading of the first element.
         */
        this._options = Ink.extendObj({
            queries:[],
            onLoad: null
        },Element.data(this._element));

        this._options = Ink.extendObj(this._options, options || {});

        /**
         * Determining the original basename (with the querystring) of the file.
         */
        var pos;
        if( (pos=this._element.src.lastIndexOf('?')) !== -1 ){
            var search = this._element.src.substr(pos);
            this._filename = this._element.src.replace(search,'').split('/').pop()+search;
        } else {
            this._filename = this._element.src.split('/').pop();
        }

        this._init();
    };

    ImageQuery.prototype = {

        /**
         * Init function called by the constructor
         * 
         * @method _init
         * @private
         */
        _init: function(){

            /**
             * Sort queries by width, in descendant order.
             */
            this._options.queries = InkArray.sortMulti(this._options.queries,'width').reverse();

            /**
             * Declaring the event handlers, in this case, the window.resize and the (element) load.
             * @type {Object}
             */
            this._handlers = {
                resize: Ink.bindEvent(this._onResize,this),
                load: Ink.bindEvent(this._onLoad,this)
            };

            if( typeof this._options.onLoad === 'function' ){
                Event.observe(this._element, 'onload', this._handlers.load);
            }

            Event.observe(window, 'resize', this._handlers.resize);

            // Imediate call to apply the right images based on the current viewport
            this._handlers.resize.call(this);

        },

        /**
         * Handles the resize event (as specified in the _init function)
         *
         * @method _onResize
         * @private
         */
        _onResize: function(){

            clearTimeout(timeout);

            var timeout = setTimeout(Ink.bind(function(){

                if( !this._options.queries || (this._options.queries === {}) ){
                    clearTimeout(timeout);
                    return;
                }

                var
                    query, selected,
                    viewportWidth
                ;

                /**
                 * Gets viewport width
                 */
                if( typeof( window.innerWidth ) === 'number' ) {
                   viewportWidth = window.innerWidth;
                } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
                   viewportWidth = document.documentElement.clientWidth;
                } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
                   viewportWidth = document.body.clientWidth;
                }

                /**
                 * Queries are in a descendant order. We want to find the query with the highest width that fits
                 * the viewport, therefore the first one.
                 */
                for( query=0; query < this._options.queries.length; query+=1 ){
                    if (this._options.queries[query].width <= viewportWidth){
                        selected = query;
                        break;
                    }
                }

                /**
                 * If it doesn't find any selectable query (because they don't meet the requirements)
                 * let's select the one with the smallest width
                 */
                if( typeof selected === 'undefined' ){ selected = this._options.queries.length-1; }

                /**
                 * Choosing the right src. The rule is:
                 *
                 *   "If there is specifically defined in the query object, use that. Otherwise uses the global src."
                 *
                 * The above rule applies to a retina src.
                 */
                var src = this._options.queries[selected].src || this._options.src;
                if ( ("devicePixelRatio" in window && window.devicePixelRatio>1) && ('retina' in this._options ) ) {
                    src = this._options.queries[selected].retina || this._options.retina;
                }

                /**
                 * Injects the file variable for usage in the 'templating system' below
                 */
                this._options.queries[selected].file = this._filename;

                /**
                 * Since we allow the src to be a callback, let's run it and get the results.
                 * For the inside, we're passing the element (img) being processed and the object of the selected
                 * query.
                 */
                if( typeof src === 'function' ){
                    src = src.apply(this,[this._element,this._options.queries[selected]]);
                    if( typeof src !== 'string' ){
                        throw '[ImageQuery] :: "src" callback does not return a string';
                    }
                }

                /**
                 * Replace the values of the existing properties on the query object (except src and retina) in the
                 * defined src and/or retina.
                 */
                var property;
                for( property in this._options.queries[selected] ){
                    if( ( property === 'src' ) || ( property === 'retina' ) ){ continue; }
                    src = src.replace("{:" + property + "}",this._options.queries[selected][property]);
                }
                this._element.src = src;

                // Removes the injected file property
                delete this._options.queries[selected].file;

                timeout = undefined;

            },this),300);
        },

        /**
         * Handles the element loading (img onload) event
         *
         * @method _onLoad
         * @private
         */
        _onLoad: function(){

            /**
             * Since we allow a callback for this let's run it.
             */
            this._options.onLoad.call(this);
        }

    };

    return ImageQuery;

});

/**
 * @module Ink.UI.TreeView_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.TreeView', '1', ['Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1'], function(Aux, Event, Css, Element, Selector, InkArray ) {
    'use strict';

    /**
     * TreeView is an Ink's component responsible for presenting a defined set of elements in a tree-like hierarchical structure
     * 
     * @class Ink.UI.TreeView
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @uses Ink.Util.Array
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *     @param {String} options.node        CSS selector that identifies the elements that are considered nodes.
     *     @param {String} options.child       CSS selector that identifies the elements that are children of those nodes.
     * @example
     *      <ul class="ink-tree-view">
     *        <li class="open"><span></span><a href="#">root</a>
     *          <ul>
     *            <li><a href="">child 1</a></li>
     *            <li><span></span><a href="">child 2</a>
     *              <ul>
     *                <li><a href="">grandchild 2a</a></li>
     *                <li><span></span><a href="">grandchild 2b</a>
     *                  <ul>
     *                    <li><a href="">grandgrandchild 1bA</a></li>
     *                    <li><a href="">grandgrandchild 1bB</a></li>
     *                  </ul>
     *                </li>
     *              </ul>
     *            </li>
     *            <li><a href="">child 3</a></li>
     *          </ul>
     *        </li>
     *      </ul>
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.TreeView_1'], function( Selector, TreeView ){
     *              var treeViewElement = Ink.s('.ink-tree-view');
     *              var treeViewObj = new TreeView( treeViewElement );
     *          });
     *      </script>
     */
    var TreeView = function(selector, options){

        /**
         * Gets the element
         */
        if( !Aux.isDOMElement(selector) && (typeof selector !== 'string') ){
            throw '[Ink.UI.TreeView] :: Invalid selector';
        } else if( typeof selector === 'string' ){
            this._element = Selector.select( selector );
            if( this._element.length < 1 ){
                throw '[Ink.UI.TreeView] :: Selector has returned no elements';
            }
            this._element = this._element[0];
        } else {
            this._element = selector;
        }

        /**
         * Default options and they're overrided by data-attributes if any.
         * The parameters are:
         * @param {string} node Selector to define which elements are seen as nodes. Default: li
         * @param {string} child Selector to define which elements are represented as childs. Default: ul
         */
        this._options = Ink.extendObj({
            node:   'li',
            child:  'ul'
        },Element.data(this._element));

        this._options = Ink.extendObj(this._options, options || {});

        this._init();
    };

    TreeView.prototype = {

        /**
         * Init function called by the constructor. Sets the necessary event handlers.
         * 
         * @method _init
         * @private
         */
        _init: function(){

            this._handlers = {
                click: Ink.bindEvent(this._onClick,this)
            };

            Event.observe(this._element, 'click', this._handlers.click);

            var
                nodes = Selector.select(this._options.node,this._element),
                children
            ;
            InkArray.each(nodes,Ink.bind(function(item){
                if( Css.hasClassName(item,'open') )
                {
                    return;
                }

                if( !Css.hasClassName(item, 'closed') ){
                    Css.addClassName(item,'closed');
                }

                children = Selector.select(this._options.child,item);
                InkArray.each(children,Ink.bind(function( inner_item ){
                    if( !Css.hasClassName(inner_item, 'hide-all') ){
                        Css.addClassName(inner_item,'hide-all');
                    }
                },this));
            },this));

        },

        /**
         * Handles the click event (as specified in the _init function).
         * 
         * @method _onClick
         * @param {Event} event
         * @private
         */
        _onClick: function(event){

            /**
             * Summary:
             * If the clicked element is a "node" as defined in the options, will check if it has any "child".
             * If so, will show it or hide it, depending on its current state. And will stop the event's default behavior.
             * If not, will execute the event's default behavior.
             *
             */
            var tgtEl = Event.element(event);

            if( this._options.node[0] === '.' ) {
                if( !Css.hasClassName(tgtEl,this._options.node.substr(1)) ){
                    while( (!Css.hasClassName(tgtEl,this._options.node.substr(1))) && (tgtEl.nodeName.toLowerCase() !== 'body') ){
                        tgtEl = tgtEl.parentNode;
                    }
                }
            } else if( this._options.node[0] === '#' ){
                if( tgtEl.id !== this._options.node.substr(1) ){
                    while( (tgtEl.id !== this._options.node.substr(1)) && (tgtEl.nodeName.toLowerCase() !== 'body') ){
                        tgtEl = tgtEl.parentNode;
                    }
                }
            } else {
                if( tgtEl.nodeName.toLowerCase() !== this._options.node ){
                    while( (tgtEl.nodeName.toLowerCase() !== this._options.node) && (tgtEl.nodeName.toLowerCase() !== 'body') ){
                        tgtEl = tgtEl.parentNode;
                    }
                }
            }

            if(tgtEl.nodeName.toLowerCase() === 'body'){ return; }

            var child = Selector.select(this._options.child,tgtEl);
            if( child.length > 0 ){
                Event.stop(event);
                child = child[0];
                if( Css.hasClassName(child,'hide-all') ){ Css.removeClassName(child,'hide-all'); Css.addClassName(tgtEl,'open'); Css.removeClassName(tgtEl,'closed'); }
                else { Css.addClassName(child,'hide-all'); Css.removeClassName(tgtEl,'open'); Css.addClassName(tgtEl,'closed'); }
            }

        }

    };

    return TreeView;

});

/**
 * @module Ink.UI.Gallery_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Gallery', '1', ['Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1','Ink.Util.Swipe_1'], function(Aux, Event, Css, Element, Selector, InkArray, Swipe ) {
    'use strict';
    
    /**
     * Function to calculate the size based on a given max. size and image size.
     * 
     * @function maximizeBox
     * @param  {Number} maxSz
     * @param  {Number} imageSz
     * @param  {Boolean} forceMaximize
     * @return {Number}
     * @private
     */
    var maximizeBox = function(maxSz, imageSz, forceMaximize) {
        var w = imageSz[0];
        var h = imageSz[1];

        if (forceMaximize || (w > maxSz[0] || h > maxSz[1]) ) {
            var arImg = w / h;
            var arMax = maxSz[0] / maxSz[1];
            var s = (arImg > arMax) ? maxSz[0] / w : maxSz[1] / h;
            return [parseInt(w * s + 0.5, 10), parseInt(h * s + 0.5, 10)];
        }

        return imageSz;
    };
    
    /**
     * @function maximizeBox
     * @param  {Object} o
     * @param  {Function} cb Callback function to run on each image loaded.
     * @private
     */
    var getDimsAsync = function(o, cb) {
        cb = Ink.bind(cb,o);

        var dims = [o.img.offsetWidth, o.img.offsetHeight];
        if (dims[0] && dims[1]) {
            cb(dims);
        }
        o.img.onload = Ink.bindEvent(function() {
            cb([this.img.offsetWidth, this.img.offsetHeight]);
        },o);
    };

    /**
     * @class Ink.UI.Gallery
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @uses Ink.Util.Array
     * @uses Ink.Util.Swipe
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options for the gallery
     *      @param {Number}   [options.fullImageMaxWidth]       Default value is 400.
     *      @param {Number}   [options.thumbnailMaxWidth]       Max. width of the thumbnail. Default value is 106.
     *      @param {Number}   [options.layout]                  This determines what layout the gallery will have. Numeric value between 0 and 3.
     *      @param {Boolean}  [options.circular]                Determines if the gallery behaves in a circular never ending cycle.
     *      @param {Boolean}  [options.fixImageSizes]           Specifies if the images should be forced to have the gallery size.
     * @example
     *     <ul class="ink-gallery-source">
     *         <li class="hentry hmedia">
     *             <a rel="enclosure" href="http://imgs.sapo.pt/ink/assets/imgs_gal/1.1.png">
     *                 <img alt="s1" src="http://imgs.sapo.pt/ink/assets/imgs_gal/thumb1.png">
     *             </a>
     *             <a class="bookmark" href="http://imgs.sapo.pt/ink/assets/imgs_gal/1.1.png">
     *                 <span class="entry-title">s1</span>
     *             </a>
     *             <span class="entry-content"><p>hello world 1</p></span>
     *         </li>
     *         <li class="hentry hmedia">
     *             <a rel="enclosure" href="http://imgs.sapo.pt/ink/assets/imgs_gal/1.2.png">
     *                 <img alt="s1" src="http://imgs.sapo.pt/ink/assets/imgs_gal/thumb2.png">
     *             </a>
     *             <a class="bookmark" href="http://imgs.sapo.pt/ink/assets/imgs_gal/1.2.png">
     *                 <span class="entry-title">s2</span>
     *             </a>
     *             <span class="entry-content"><p>hello world 2</p></span>
     *         </li>
     *     </ul>
     *     <script>
     *         Ink.requireModules(['Ink.Dom.Selector_1','Ink.UI.Gallery_1'],function( Selector, Gallery ){
     *             var galleryElement = Ink.s('ul.ink-gallery-source');
     *             var galleryObj = new Gallery( galleryElement );
     *         });
     *     </script>
     */
    var Gallery = function(selector, options) {

        this._element = Aux.elOrSelector(selector, '1st argument');

        this._options = Ink.extendObj({
            fullImageMaxWidth:   600,
            fullImageMaxHeight:  400,
            thumbnailMaxWidth:   106,
            layout:              0,
            circular:            false,
            fixImageSizes:       false
        }, Element.data(this._element));

        this._options = Ink.extendObj(this._options, options || {});

        this._handlers = {
            navClick:        Ink.bindEvent(this._onNavClick,this),
            paginationClick: Ink.bindEvent(this._onPaginationClick,this),
            thumbsClick:     Ink.bindEvent(this._onThumbsClick,this),
            focusBlur:       Ink.bindEvent(this._onFocusBlur,this),
            keyDown:         Ink.bindEvent(this._onKeyDown,this)
        };

        this._isFocused = false;
        this._model = [];

        if (this._options.model instanceof Array) {
            this._model = this._options.model;
            this._createdFrom = 'JSON';
        }
        else if (this._element.nodeName.toLowerCase() === 'ul') {
            this._createdFrom = 'DOM';
        }
        else {
            throw new TypeError('You must pass a selector expression/DOM element as 1st option or provide a model on 2nd argument!');
        }

        this._index      = 0;
        this._thumbIndex = 0;

        if( !isNaN(this._options.layout) ){

            this._options.layout = parseInt(this._options.layout,10);
            if (this._options.layout === 0) {
                this._showThumbs            = false;
                this._showDescription       = false;
                this._paginationHasPrevNext = false;
            }
            else if (this._options.layout === 1 || this._options.layout === 2 || this._options.layout === 3) {
                this._showThumbs            = true;
                this._showDescription       = true;
                this._paginationHasPrevNext = true;
            }
            else {
                throw new TypeError('supported layouts are 0-3!');
            }
        }

        if (this._element.getAttribute('data-fix-image-sizes') !== null) {
            this._options.fixImageSizes = true;
        }

        this._init();
    };

    Gallery.prototype = {

        /**
         * Init function called from the constructor.
         *
         * @method  _init
         * @private
         */
        _init: function() {
            // extract model
            if (this._createdFrom === 'DOM') {
                this._extractModelFromDOM();
            }

            // generate and apply DOM
            var el = this._generateMarkup();
            var parentEl = this._element.parentNode;

            if (!this._notFirstInit) {
                Aux.storeIdAndClasses(this._element, this);
                this._notFirstInit = true;
            }

            parentEl.insertBefore(el, this._element);
            parentEl.removeChild(this._element);
            this._element = el;

            Aux.restoreIdAndClasses(this._element, this);

            // subscribe events
            Event.observe(this._paginationEl, 'click',     this._handlers.paginationClick);
            Event.observe(this._navEl,        'click',     this._handlers.navClick);

            if (this._showThumbs) {
                Event.observe(this._thumbsUlEl,   'click',     this._handlers.thumbsClick);
            }

            Event.observe(this._element,      'mouseover', this._handlers.focusBlur);
            Event.observe(this._element,      'mouseout',  this._handlers.focusBlur);
            Event.observe(document,           'keydown',   this._handlers.keyDown);

            Aux.registerInstance(this, this._element, 'gallery');
        },

        /**
         * Updates the model from the UL representation
         *
         * @method _extractModelFromDOM
         * @private
         */
        _extractModelFromDOM: function() {
            /*global console:false */
            var m = [];
            var dims;

            var liEls = Selector.select('> li', this._element);
            InkArray.each(liEls,function(liEl) {
                try {
                    var d = {
                        image_full:  '',
                        image_thumb: '',
                        title_text:  '',
                        title_link:  '',
                        description: '',
                        content_overlay: document.createDocumentFragment()
                    };

                    var enclosureAEl       = Selector.select('> a[rel=enclosure]',          liEl)[0];
                    var thumbImgEl         = Selector.select('> img',                       enclosureAEl)[0];
                    var bookmarkAEl        = Selector.select('> a[class=bookmark]',         liEl)[0];
                    var titleSpanEl        = Selector.select('span[class=entry-title]',     liEl)[0];
                    var entryContentSpanEl = Selector.select('> span[class=entry-content]', liEl)[0];
                    var contentOverlayEl   = Selector.select('> .content-overlay',          liEl)[0];

                    dims = enclosureAEl.getAttribute('data-dims');
                    if (dims !== null) {
                        dims = dims.split(',');
                        dims[0] = parseInt(dims[0], 10);
                        dims[1] = parseInt(dims[1], 10);
                    }
                    if (dims && !isNaN(dims[0]) && !isNaN(dims[1])) { d.dims = dims; }

                    d.image_full  = enclosureAEl.getAttribute('href');
                    d.image_thumb = thumbImgEl.getAttribute('src');
                    if (bookmarkAEl) {
                        d.title_link  = bookmarkAEl.getAttribute('href');
                    }
                    d.title_text  = titleSpanEl.innerHTML;
                    if (entryContentSpanEl) {
                        d.description = entryContentSpanEl.innerHTML;
                    }

                    if(contentOverlayEl){
                        d.content_overlay.appendChild(contentOverlayEl);
                    }

                    m.push(d);
                } catch(ex) {
                    console.error('problematic element:');
                    console.error(liEl);
                    console.error(ex);
                    throw new Error('Problem parsing gallery data from DOM!');
                }
            });

            this._model = m;
        },

        /**
         * Returns the top element for the gallery DOM representation
         *
         * @method _generateMarkup
         * @private
         * @return {DOMElement} Returns the Gallery element totally rendered.
         */
        _generateMarkup: function() {
            /*jshint maxstatements:80 */
            var el = document.createElement('div');
            el.className = 'ink-gallery';

            var stageEl = document.createElement('div');
            stageEl.className = 'stage';

            // nav
            var navEl = document.createElement('nav');
            navEl.innerHTML = [
                '<ul class="unstyled">',
                    '<li><a href="#" class="next"></a></li>',
                    '<li><a href="#" class="previous"></a></li>',
                '</ul>'
            ].join('');
            this._navEl = navEl;

            // slider
            var sliderEl = document.createElement('div');
            sliderEl.className = 'slider';

            var ulEl = document.createElement('ul');
            this._sliderUlEl = ulEl;

            var that = this;

            var W = this._options.fullImageMaxWidth;
            var H = this._options.fullImageMaxHeight;

            InkArray.each(this._model,function(d, i) {
                var liEl = document.createElement('li');
                var imgEl = document.createElement('img');
                imgEl.setAttribute('name', 'image ' + (i + 1));
                imgEl.setAttribute('src',  d.image_full);
                imgEl.setAttribute('alt',  d.title_text);
                //imgEl.style.maxWidth = that._options.fullImageMaxWidth + 'px';
                //imgEl.setAttribute('width', that._options.fullImageMaxWidth);       // TODO?
                liEl.appendChild(imgEl);

                if(d.content_overlay){
                    if(d.content_overlay.nodeType === 1 || d.content_overlay.nodeType === 11){
                        d.content_overlay = liEl.appendChild(d.content_overlay);
                    } else if(typeof d.content_overlay === 'string'){
                        var contentOverlayEl = document.createElement('div');

                        contentOverlayEl.className = 'content-overlay';
                        contentOverlayEl.innerHTML = d.content_overlay;

                        d.content_overlay = liEl.appendChild(contentOverlayEl);
                    }
                }

                ulEl.appendChild(liEl);

                if (that._options.fixImageSizes) {
                    var dimsCb = function(dims) {
                        //console.log(this, dims);
                        var imgEl = this.img;
                        var data  = this.data;

                        if (!data.dims) { data.dims = dims; }

                        var dims2 = maximizeBox([W, H], dims);

                        var w = dims2[0];
                        var h = dims2[1];
                        var dw = Math.floor( (W - w)/2 );
                        var dh = Math.floor( (H - h)/2 );

                        if (w !== W || h !== H) {
                            imgEl.setAttribute('width',  w);
                            imgEl.setAttribute('height', h);

                            var s = imgEl.style;
                            if (dw > 0) { s.paddingLeft   = dw + 'px'; }
                            if (dh > 0) { s.paddingBottom = dh + 'px'; }
                        }
                    };

                    if (d.dims) { dimsCb.call( {img:imgEl, data:d}, d.dims); }
                    else {        getDimsAsync({img:imgEl, data:d}, dimsCb); }
                }
            });

            sliderEl.appendChild(ulEl);
            this._sliderEl = sliderEl;

            // description
            var articleTextDivEl;
            if (this._showDescription) {
                var d = this._model[this._index];
                articleTextDivEl = document.createElement('div');
                articleTextDivEl.className = ['article_text', 'example' + (this._options.layout === 3 ? 2 : this._options.layout)].join(' ');
                if (d.title_link) {
                    articleTextDivEl.innerHTML = ['<h1><a href="', d.title_link, '">', d.title_text, '</a></h1>', d.description].join('');
                }
                else {
                    articleTextDivEl.innerHTML = ['<h1>', d.title_text, '</h1>', d.description].join('');
                }
                this._articleTextDivEl = articleTextDivEl;
            }

            // thumbs
            var thumbsDivEl;
            if (this._showThumbs) {
                thumbsDivEl = document.createElement('div');
                thumbsDivEl.className = 'thumbs';
                ulEl = document.createElement('ul');
                ulEl.className = 'unstyled';

                InkArray.each(this._model,function(d, i) {
                    var liEl = document.createElement('li');
                    var aEl = document.createElement('a');
                    aEl.setAttribute('href', '#');
                    var imgEl = document.createElement('img');
                    imgEl.setAttribute('name', 'thumb ' + (i + 1));
                    imgEl.setAttribute('src', d.image_thumb);
                    imgEl.setAttribute('alt', (i + 1));
                    var spanEl = document.createElement('span');
                    spanEl.innerHTML = d.title_text;
                    aEl.appendChild(imgEl);
                    aEl.appendChild(spanEl);
                    liEl.appendChild(aEl);
                    ulEl.appendChild(liEl);
                });
                thumbsDivEl.appendChild(ulEl);

                this._thumbsDivEl = thumbsDivEl;
                this._thumbsUlEl = ulEl;
            }


            // pagination
            var paginationEl = document.createElement('div');
            paginationEl.className = 'pagination';

            var aEl;
            if (this._paginationHasPrevNext) {
                aEl = document.createElement('a');
                aEl.setAttribute('href', '#');
                aEl.className = 'previous';
                paginationEl.appendChild(aEl);
            }

            InkArray.each(this._model,function(d, i) {
                var aEl = document.createElement('a');
                aEl.setAttribute('href', '#');
                aEl.setAttribute('data-index', i);
                if (i === that._index) { aEl.className = 'active'; }
                paginationEl.appendChild(aEl);
            });

            if (this._paginationHasPrevNext) {
                aEl = document.createElement('a');
                aEl.setAttribute('href', '#');
                aEl.className = 'next';
                paginationEl.appendChild(aEl);
            }

            this._paginationEl = paginationEl;

            // last appends...
            if (this._options.layout === 0) {
                stageEl.appendChild(navEl);
                stageEl.appendChild(sliderEl);
                stageEl.appendChild(paginationEl);
                el.appendChild(stageEl);
            }
            else if (this._options.layout === 1 || this._options.layout === 2 || this._options.layout === 3) {
                stageEl.appendChild(navEl);
                stageEl.appendChild(sliderEl);
                stageEl.appendChild(articleTextDivEl);
                el.appendChild(stageEl);

                if (this._options.layout === 3) {
                    //this._thumbsUlEl.appendChild(paginationEl);
                    this._thumbsUlEl.className = 'thumbs unstyled';
                    Css.addClassName(el, 'rightNav');
                    el.appendChild(this._thumbsUlEl);
                }
                else {
                    thumbsDivEl.appendChild(paginationEl);
                    el.appendChild(thumbsDivEl);
                }
            }

            this._swipeDir = 'x';
            this._swipeThumbsDir = this._options.layout === 0 ? '' : (this._options.layout === 3 ? 'y' : 'x');

            if (Swipe._supported) {
                new Swipe(el, {
                    callback:    Ink.bind(function(sw, o) {
                        var th =              this._isMeOrParent(o.target, this._thumbsUlEl);
                        var sl = th ? false : this._isMeOrParent(o.target, el);//this._sliderUlEl);
                        if ( (!th && !sl) || (th && !this._swipeThumbsDir) ) { return; }
                        if ( (sl && o.axis !== this._swipeDir) || (th && o.axis !== this._swipeThumbsDir) ) { return; }
                        if (o.dr[0] < 0) { if (th) { this.thumbNext();     } else { this.next();     } }
                        else {             if (th) { this.thumbPrevious(); } else { this.previous(); } }
                    },this),
                    maxDuration: 0.4,
                    minDist:     50
                });
            }

            return el;
        },

        /**
         * Verifies if a given element is equals to its parent
         *
         * @method _isMeOrParent
         * @param  {DOMElement}  el       Element to be compared with the parent element
         * @param  {DOMElement}  parentEl Parent element to be compared with the element
         * @return {Boolean|undefined}          In case the 'el' variable is not defined, it returns undefined. Otherwise, it will return true or false depending on the comparison.
         * @private
         */
        _isMeOrParent: function(el, parentEl) {
            if (!el) {return;}
            do {
                if (el === parentEl) { return true; }
                el = el.parentNode;
            } while (el);
            return false;
        },

        /**
         * Navigation click handler
         *
         * @method _onNavClick
         * @param {Event} ev
         * @private
         */
        _onNavClick: function(ev) {
            var tgtEl = Event.element(ev);
            var delta;
            if      (Css.hasClassName(tgtEl, 'previous')) { delta = -1; }
            else if (Css.hasClassName(tgtEl, 'next')) {     delta =  1; }
            else { return; }

            Event.stop(ev);
            this.goTo(delta, true);
        },

        /**
         * Pagination click handler
         *
         * @method _onPaginationClick
         * @param {Event} ev
         * @private
         */
        _onPaginationClick: function(ev) {
            var tgtEl = Event.element(ev);
            var i = tgtEl.getAttribute('data-index');
            var isRelative = false;
            if      (Css.hasClassName(tgtEl, 'previous')) { i = -1; isRelative = true; }
            else if (Css.hasClassName(tgtEl, 'next')) {     i =  1; isRelative = true; }
            else if (i === null) { return; }
            else { i = parseInt(i, 10); }
            Event.stop(ev);

            if (isRelative) { this.thumbGoTo(i, true); }
            else {            this.goTo(i);            }
        },

        /**
         * Thumbs click handler
         *
         * @method _onThumbsClick
         * @param {Event} ev
         * @private
         */
        _onThumbsClick: function(ev) {
            var tgtEl = Event.element(ev);
            if      (tgtEl.nodeName.toLowerCase() === 'img') {}
            else if (tgtEl.nodeName.toLowerCase() === 'span') {
                tgtEl = Selector.select('> img', tgtEl.parentNode)[0];
            }
            else { return; }

            Event.stop(ev);
            var i = parseInt(tgtEl.getAttribute('alt'), 10) - 1;
            this.goTo(i);
        },

        /**
         * Focus handler
         *
         * @method _onFocusBlur
         * @param  {Event} ev
         * @private
         */
        _onFocusBlur: function(ev) {
            this._isFocused = (ev.type === 'mouseover');
        },

        /**
         * Key handler
         *
         * @method _onKeyDown
         * @param  {Event} ev
         * @private
         */
        _onKeyDown: function(ev) {
            if (!this._isFocused) { return; }
            var kc = ev.keyCode;
            if      (kc === 37) { this.previous(); }
            else if (kc === 39) { this.next();     }
            else { return; }
            Event.stop(ev);
        },

        /**
         * Validates the number of the item against the gallery items.
         *
         * @method _validateValue
         * @param  {Number}  i  The number of the item being validated
         * @param  {Boolean} [isRelative]
         * @param  {Boolean} [isThumb]
         * @return {Number|Boolean}
         * @private
         */
        _validateValue: function(i, isRelative, isThumb) {
            // check arguments
            if (!Aux.isInteger(i)) {
                throw new TypeError('1st parameter must be an integer number!');
            }
            if ( isRelative !== undefined &&
                 isRelative !== false     &&
                 isRelative !== true ) {
                throw new TypeError('2nd parameter must either be boolean or ommitted!');
            }

            var val = isThumb ? this._thumbIndex : this._index;

            // compute new index
            if (isRelative) { i += val; }

            if (this._options.circular) {
                if      (i < 0) {                   i = this._model.length - 1; }
                else if (i >= this._model.length) { i = 0;                      }
            }
            else {
                if (i < 0 || i >= this._model.length || i === val) { return false; }
            }

            return i;
        },



        /**************
         * PUBLIC API *
         **************/

        /**
         * Returns the index of the current image
         *
         * @method getIndex
         * @return {Number} Index of the current image
         * @public
         */
        getIndex: function() {
            return this._index;
        },

        /**
         * Returns the number of images in the gallery
         *
         * @method getLength
         * @return {Number} Number of images in the gallery
         * @public
         */
        getLength: function() {
            return this._model.length;
        },

        /**
         * Moves gallery to the nth - 1 image
         *
         * @method goTo
         * @param  {Number} i Absolute or relative index
         * @param  {Boolean} [isRelative] pass true for relative movement, otherwise absolute
         * @public
         */
        goTo: function(i, isRelative) {
            i = this._validateValue(i, isRelative, false);
            if (i === false) { return; }
            this._index = i;

            // update DOM representation
            var paginationAEls = Selector.select('> a', this._paginationEl);
            var that = this;
            InkArray.each(paginationAEls,function(aEl, i) {
                Css.setClassName(aEl, 'active', (i - (that._paginationHasPrevNext ? 1 : 0)) === that._index);
            });

            this._sliderUlEl.style.marginLeft = ['-', this._options.fullImageMaxWidth * this._index, 'px'].join('');

            if (this._showDescription) {
                var d = this._model[this._index];
                if (d.title_link) {
                    this._articleTextDivEl.innerHTML = ['<h1><a href="', d.title_link, '">', d.title_text, '</a></h1>', d.description].join('');
                }
                else {
                    this._articleTextDivEl.innerHTML = ['<h1>', d.title_text, '</h1>', d.description].join('');
                }
            }
        },

        /**
         * Moves gallery to the nth - 1 thumb
         *
         * @method thumbGoTo
         * @param  {Number} i Absolute or relative index
         * @param  {Boolean} [isRelative] pass true for relative movement, otherwise absolute
         * @public
         */
        thumbGoTo: function(i, isRelative) {
            i = this._validateValue(i, isRelative, true);
            if (i === false) { return; }
            this._thumbIndex = i;

            // update DOM representation
            var prop = 'margin' + (this._swipeThumbsDir === 'x' ? 'Left' : 'Top');
            this._thumbsUlEl.style[prop] = ['-', this._options.thumbnailMaxWidth * this._thumbIndex, 'px'].join('');
        },

        /**
         * Move to the previous image
         *
         * @method previous
         * @public
         */
        previous: function() {
            this.goTo(-1, true);
        },

        /**
         * Move to the next image
         *
         * @method next
         * @public
         */
        next: function() {
            this.goTo(1, true);
        },

        /**
         * Move to the previous thumb
         *
         * @method thumbPrevious
         * @public
         */
        thumbPrevious: function() {
            this.thumbGoTo(-1, true);
        },

        /**
         * Move to the next thumb
         *
         * @method thumbNext
         * @public
         */
        thumbNext: function() {
            this.thumbGoTo(1, true);
        },

        /**
         * Unregisters the component and removes its markup from the DOM
         *
         * @method destroy
         * @public
         */
        destroy: Aux.destroyComponent

    };

    return Gallery;

});

/**
 * @module Ink.UI.FormValidator_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.FormValidator', '1', ['Ink.Dom.Css_1','Ink.Util.Validator_1'], function( Css, InkValidator ) {
    'use strict';

    /**
     * @class Ink.UI.FormValidator
     * @version 1
     * @uses Ink.Dom.Css
     * @uses Ink.Util.Validator
     */
    var FormValidator = {

        /**
         * Specifies the version of the component
         *
         * @property version
         * @type {String}
         * @readOnly
         * @public
         */
        version: '1',

        /**
         * Available flags to use in the validation process.
         * The keys are the 'rules', and their values are objects with the key 'msg', determining
         * what is the error message.
         *
         * @property _flagMap
         * @type {Object}
         * @readOnly
         * @private
         */
        _flagMap: {
            //'ink-fv-required': {msg: 'Campo obrigat&oacute;rio'},
            'ink-fv-required': {msg: 'Required field'},
            //'ink-fv-email': {msg: 'E-mail inv&aacute;lido'},
            'ink-fv-email': {msg: 'Invalid e-mail address'},
            //'ink-fv-url': {msg: 'URL inv&aacute;lido'},
            'ink-fv-url': {msg: 'Invalid URL'},
            //'ink-fv-number': {msg: 'N&uacute;mero inv&aacute;lido'},
            'ink-fv-number': {msg: 'Invalid number'},
            //'ink-fv-phone_pt': {msg: 'N&uacute;mero de telefone inv&aacute;lido'},
            'ink-fv-phone_pt': {msg: 'Invalid phone number'},
            //'ink-fv-phone_cv': {msg: 'N&uacute;mero de telefone inv&aacute;lido'},
            'ink-fv-phone_cv': {msg: 'Invalid phone number'},
            //'ink-fv-phone_mz': {msg: 'N&uacute;mero de telefone inv&aacute;lido'},
            'ink-fv-phone_mz': {msg: 'Invalid phone number'},
            //'ink-fv-phone_ao': {msg: 'N&uacute;mero de telefone inv&aacute;lido'},
            'ink-fv-phone_ao': {msg: 'Invalid phone number'},
            //'ink-fv-date': {msg: 'Data inv&aacute;lida'},
            'ink-fv-date': {msg: 'Invalid date'},
            //'ink-fv-confirm': {msg: 'Confirma&ccedil;&atilde;o inv&aacute;lida'},
            'ink-fv-confirm': {msg: 'Confirmation does not match'},
            'ink-fv-custom': {msg: ''}
        },

        /**
         * This property holds all form elements for later validation
         *
         * @property elements
         * @type {Object}
         * @public
         */
        elements: {},

        /**
         * This property holds the objects needed to cross-check for the 'confirm' rule
         *
         * @property confirmElms
         * @type {Object}
         * @public
         */
        confirmElms: {},

        /**
         * This property holds the previous elements in the confirmElms property, but with a
         * true/false specifying if it has the class ink-fv-confirm.
         *
         * @property hasConfirm
         * @type {Object}
         */
        hasConfirm: {},

        /**
         * Defined class name to use in error messages label
         *
         * @property _errorClassName
         * @type {String}
         * @readOnly
         * @private
         */
        _errorClassName: 'tip',

        /**
         * @property _errorValidationClassName
         * @type {String}
         * @readOnly
         * @private
         */
        _errorValidationClassName: 'validaton',

        /**
         * @property _errorTypeWarningClassName
         * @type {String}
         * @readOnly
         * @private
         */
        _errorTypeWarningClassName: 'warning',

        /**
         * @property _errorTypeErrorClassName
         * @type {String}
         * @readOnly
         * @private
         */
        _errorTypeErrorClassName: 'error',

        /**
         * Check if a form is valid or not
         * 
         * @method validate
         * @param {DOMElement|String} elm DOM form element or form id
         * @param {Object} options Options for
         *      @param {Function} [options.onSuccess] function to run when form is valid
         *      @param {Function} [options.onError] function to run when form is not valid
         *      @param {Array} [options.customFlag] custom flags to use to validate form fields
         * @public
         * @return {Boolean}
         */
        validate: function(elm, options)
        {
            this._free();

            options = Ink.extendObj({
                onSuccess: false,
                onError: false,
                customFlag: false,
                confirmGroup: []
            }, options || {});

            if(typeof(elm) === 'string') {
                elm = document.getElementById(elm);
            }
            if(elm === null){
                return false;
            }
            this.element = elm;

            if(typeof(this.element.id) === 'undefined' || this.element.id === null || this.element.id === '') {
                // generate a random ID
                this.element.id = 'ink-fv_randomid_'+(Math.round(Math.random() * 99999));
            }

            this.custom = options.customFlag;

            this.confirmGroup = options.confirmGroup;

            var fail = this._validateElements();

            if(fail.length > 0) {
                if(options.onError) {
                    options.onError(fail);
                } else {
                    this._showError(elm, fail);
                }
                return false;
            } else {
                if(!options.onError) {
                    this._clearError(elm);
                }
                this._clearCache();
                if(options.onSuccess) {
                    options.onSuccess();
                }
                return true;
            }

        },

        /**
         * Reset previously generated validation errors
         * 
         * @method reset
         * @public
         */
        reset: function()
        {
            this._clearError();
            this._clearCache();
        },

        /**
         * Cleans the object
         * 
         * @method _free
         * @private
         */
        _free: function()
        {
            this.element = null;
            //this.elements = [];
            this.custom = false;
            this.confirmGroup = false;
        },

        /**
         * Cleans the properties responsible for caching
         * 
         * @method _clearCache
         * @private
         */
        _clearCache: function()
        {
            this.element = null;
            this.elements = [];
            this.custom = false;
            this.confirmGroup = false;
        },

        /**
         * Gets the form elements and stores them in the caching properties
         * 
         * @method _getElements
         * @private
         */
        _getElements: function()
        {
            //this.elements = [];
            // if(typeof(this.elements[this.element.id]) !== 'undefined') {
            //     return;
            // }

            this.elements[this.element.id] = [];
            this.confirmElms[this.element.id] = [];
            //console.log(this.element);
            //console.log(this.element.elements);
            var formElms = this.element.elements;
            var curElm = false;
            for(var i=0, totalElm = formElms.length; i < totalElm; i++) {
                curElm = formElms[i];

                if(curElm.getAttribute('type') !== null && curElm.getAttribute('type').toLowerCase() === 'radio') {
                    if(this.elements[this.element.id].length === 0 ||
                            (
                             curElm.getAttribute('type') !== this.elements[this.element.id][(this.elements[this.element.id].length - 1)].getAttribute('type') &&
                            curElm.getAttribute('name') !== this.elements[this.element.id][(this.elements[this.element.id].length - 1)].getAttribute('name')
                            )) {
                        for(var flag in this._flagMap) {
                            if(Css.hasClassName(curElm, flag)) {
                                this.elements[this.element.id].push(curElm);
                                break;
                            }
                        }
                    }
                } else {
                    for(var flag2 in this._flagMap) {
                        if(Css.hasClassName(curElm, flag2) && flag2 !== 'ink-fv-confirm') {
                            /*if(flag2 == 'ink-fv-confirm') {
                                this.confirmElms[this.element.id].push(curElm);
                                this.hasConfirm[this.element.id] = true;
                            }*/
                            this.elements[this.element.id].push(curElm);
                            break;
                        }
                    }

                    if(Css.hasClassName(curElm, 'ink-fv-confirm')) {
                        this.confirmElms[this.element.id].push(curElm);
                        this.hasConfirm[this.element.id] = true;
                    }

                }
            }
            //debugger;
        },

        /**
         * Runs the validation for each element
         * 
         * @method _validateElements
         * @private
         */
        _validateElements: function()
        {
            var oGroups;
            this._getElements();
            //console.log('HAS CONFIRM', this.hasConfirm);
            if(typeof(this.hasConfirm[this.element.id]) !== 'undefined' && this.hasConfirm[this.element.id] === true) {
                oGroups = this._makeConfirmGroups();
            }

            var errors = [];

            var curElm = false;
            var customErrors = false;
            var inArray;
            for(var i=0, totalElm = this.elements[this.element.id].length; i < totalElm; i++) {
                inArray = false;
                curElm = this.elements[this.element.id][i];

                if(!curElm.disabled) {
                    for(var flag in this._flagMap) {
                        if(Css.hasClassName(curElm, flag)) {

                            if(flag !== 'ink-fv-custom' && flag !== 'ink-fv-confirm') {
                                if(!this._isValid(curElm, flag)) {

                                    if(!inArray) {
                                        errors.push({elm: curElm, errors:[flag]});
                                        inArray = true;
                                    } else {
                                        errors[(errors.length - 1)].errors.push(flag);
                                    }
                                }
                            } else if(flag !== 'ink-fv-confirm'){
                                customErrors = this._isCustomValid(curElm);
                                if(customErrors.length > 0) {
                                    errors.push({elm: curElm, errors:[flag], custom: customErrors});
                                }
                            } else if(flag === 'ink-fv-confirm'){
                            }
                        }
                    }
                }
            }
            errors = this._validateConfirmGroups(oGroups, errors);
            //console.log(InkDumper.returnDump(errors));
            return errors;
        },

        /**
         * Runs the 'confirm' validation for each group of elements
         * 
         * @method _validateConfirmGroups
         * @param {Array} oGroups Array/Object that contains the group of confirm objects
         * @param {Array} errors Array that will store the errors
         * @private
         * @return {Array} Array of errors that was passed as 2nd parameter (either changed, or not, depending if errors were found).
         */
        _validateConfirmGroups: function(oGroups, errors)
        {
            //console.log(oGroups);
            var curGroup = false;
            for(var i in oGroups) {
                curGroup = oGroups[i];
                if(curGroup.length === 2) {
                    if(curGroup[0].value !== curGroup[1].value) {
                        errors.push({elm:curGroup[1], errors:['ink-fv-confirm']});
                    }
                }
            }
            return errors;
        },

        /**
         * Creates the groups of 'confirm' objects
         * 
         * @method _makeConfirmGroups
         * @private
         * @return {Array|Boolean} Returns the array of confirm elements or false on error.
         */
        _makeConfirmGroups: function()
        {
            var oGroups;
            if(this.confirmGroup && this.confirmGroup.length > 0) {
                oGroups = {};
                var curElm = false;
                var curGroup = false;
                //this.confirmElms[this.element.id];
                for(var i=0, total=this.confirmElms[this.element.id].length; i < total; i++) {
                    curElm = this.confirmElms[this.element.id][i];
                    for(var j=0, totalG=this.confirmGroup.length; j < totalG; j++) {
                        curGroup =  this.confirmGroup[j];
                        if(Css.hasClassName(curElm, curGroup)) {
                            if(typeof(oGroups[curGroup]) === 'undefined') {
                                oGroups[curGroup] = [curElm];
                            } else {
                                oGroups[curGroup].push(curElm);
                            }
                        }
                    }
                }
                return oGroups;
            } else {
                if(this.confirmElms[this.element.id].length === 2) {
                    oGroups = {
                        "ink-fv-confirm": [
                                this.confirmElms[this.element.id][0],
                                this.confirmElms[this.element.id][1]
                            ]
                    };
                }
                return oGroups;
            }
            return false;
        },

        /**
         * Validates an element with a custom validation
         * 
         * @method _isCustomValid
         * @param {DOMElemenmt} elm Element to be validated
         * @private
         * @return {Array} Array of errors. If no errors are found, results in an empty array.
         */
        _isCustomValid: function(elm)
        {
            var customErrors = [];
            var curFlag = false;
            for(var i=0, tCustom = this.custom.length; i < tCustom; i++) {
                curFlag = this.custom[i];
                if(Css.hasClassName(elm, curFlag.flag)) {
                    if(!curFlag.callback(elm, curFlag.msg)) {
                        customErrors.push({flag: curFlag.flag, msg: curFlag.msg});
                    }
                }
            }
            return customErrors;
        },

        /**
         * Runs the normal validation functions for a specific element
         * 
         * @method :_isValid
         * @param {DOMElement} elm DOMElement that will be validated
         * @param {String} fieldType Rule to be validated. This must be one of the keys present in the _flagMap property.
         * @private
         * @return {Boolean} The result of the validation.
         */
        _isValid: function(elm, fieldType)
        {
            /*jshint maxstatements:50, maxcomplexity:50 */
            switch(fieldType) {
                case 'ink-fv-required':
                    if(elm.nodeName.toLowerCase() === 'select') {
                        if(elm.selectedIndex > 0) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                    if(elm.getAttribute('type') !== 'checkbox' && elm.getAttribute('type') !== 'radio') {
                        if(this._trim(elm.value) !== '') {
                            return true;
                        }
                    } else if(elm.getAttribute('type') === 'checkbox') {
                        if(elm.checked === true) {
                            return true;
                        }
                    } else if(elm.getAttribute('type') === 'radio') { // get top radio
                        var aFormRadios = elm.form[elm.name];
                        if(typeof(aFormRadios.length) === 'undefined') {
                            aFormRadios = [aFormRadios];
                        }
                        var isChecked = false;
                        for(var i=0, totalRadio = aFormRadios.length; i < totalRadio; i++) {
                            if(aFormRadios[i].checked === true) {
                                isChecked = true;
                            }
                        }
                        return isChecked;
                    }
                    break;

                case 'ink-fv-email':
                    if(this._trim(elm.value) === '') {
                        if(Css.hasClassName(elm, 'ink-fv-required')) {
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        if(InkValidator.mail(elm.value)) {
                            return true;
                        }
                    }
                    break;
                case 'ink-fv-url':
                    if(this._trim(elm.value) === '') {
                        if(Css.hasClassName(elm, 'ink-fv-required')) {
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        if(InkValidator.url(elm.value)) {
                            return true;
                        }
                    }
                    break;
                case 'ink-fv-number':
                    if(this._trim(elm.value) === '') {
                        if(Css.hasClassName(elm, 'ink-fv-required')) {
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        if(!isNaN(Number(elm.value))) {
                            return true;
                        }
                    }
                    break;
                case 'ink-fv-phone_pt':
                    if(this._trim(elm.value) === '') {
                        if(Css.hasClassName(elm, 'ink-fv-required')) {
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        if(InkValidator.isPTPhone(elm.value)) {
                            return true;
                        }
                    }
                    break;
                case 'ink-fv-phone_cv':
                    if(this._trim(elm.value) === '') {
                        if(Css.hasClassName(elm, 'ink-fv-required')) {
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        if(InkValidator.isCVPhone(elm.value)) {
                            return true;
                        }
                    }
                    break;
                case 'ink-fv-phone_ao':
                    if(this._trim(elm.value) === '') {
                        if(Css.hasClassName(elm, 'ink-fv-required')) {
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        if(InkValidator.isAOPhone(elm.value)) {
                            return true;
                        }
                    }
                    break;
                case 'ink-fv-phone_mz':
                    if(this._trim(elm.value) === '') {
                        if(Css.hasClassName(elm, 'ink-fv-required')) {
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        if(InkValidator.isMZPhone(elm.value)) {
                            return true;
                        }
                    }
                    break;
                case 'ink-fv-date':
                    if(this._trim(elm.value) === '') {
                        if(Css.hasClassName(elm, 'ink-fv-required')) {
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        var Element = Ink.getModule('Ink.Dom.Element',1);
                        var dataset = Element.data( elm );
                        var validFormat = 'yyyy-mm-dd';

                        if( Css.hasClassName(elm, 'ink-datepicker') && ("format" in dataset) ){
                            validFormat = dataset.format;
                        } else if( ("validFormat" in dataset) ){
                            validFormat = dataset.validFormat;
                        }

                        if( !(validFormat in InkValidator._dateParsers ) ){
                            var validValues = [];
                            for( var val in InkValidator._dateParsers ){
                                validValues.push(val);
                            }
                            throw "The attribute data-valid-format must be one of the following values: " + validValues.join(',');
                        }
                        
                        return InkValidator.isDate( validFormat, elm.value );
                    }
                    break;
                case 'ink-fv-custom':
                    break;
            }

            return false;
        },

        /**
         * Makes the necessary changes to the markup to show the errors of a given element
         * 
         * @method _showError
         * @param {DOMElement} formElm The form element to be changed to show the errors
         * @param {Array} aFail An array with the errors found.
         * @private
         */
        _showError: function(formElm, aFail)
        {
            this._clearError(formElm);

            //ink-warning-field

            //console.log(aFail);
            var curElm = false;
            for(var i=0, tFail = aFail.length; i < tFail; i++) {
                curElm = aFail[i].elm;

                if(curElm.getAttribute('type') !== 'radio') {

                    var newLabel = document.createElement('p');
                    //newLabel.setAttribute('for',curElm.id);
                    //newLabel.className = this._errorClassName;
                    //newLabel.className += ' ' + this._errorTypeErrorClassName;
                    Css.addClassName(newLabel, this._errorClassName);
                    Css.addClassName(newLabel, this._errorTypeErrorClassName);
                    if(aFail[i].errors[0] !== 'ink-fv-custom') {
                        newLabel.innerHTML = this._flagMap[aFail[i].errors[0]].msg;
                    } else {
                        newLabel.innerHTML = aFail[i].custom[0].msg;
                    }

                    if(curElm.getAttribute('type') !== 'checkbox') {
                        curElm.nextSibling.parentNode.insertBefore(newLabel, curElm.nextSibling);
                        if(Css.hasClassName(curElm.parentNode, 'control')) {
                            Css.addClassName(curElm.parentNode.parentNode, 'validation');
                            if(aFail[i].errors[0] === 'ink-fv-required') {
                                Css.addClassName(curElm.parentNode.parentNode, 'error');
                            } else {
                                Css.addClassName(curElm.parentNode.parentNode, 'warning');
                            }
                        }
                    } else {
                        /* // TODO checkbox... does not work with this CSS
                        curElm.parentNode.appendChild(newLabel);
                        if(Css.hasClassName(curElm.parentNode.parentNode, 'control-group')) {
                            Css.addClassName(curElm.parentNode.parentNode, 'control');
                            Css.addClassName(curElm.parentNode.parentNode, 'validation');
                            Css.addClassName(curElm.parentNode.parentNode, 'error');
                        }*/
                    }
                } else {
                    if(Css.hasClassName(curElm.parentNode.parentNode, 'control-group')) {
                        Css.addClassName(curElm.parentNode.parentNode, 'validation');
                        Css.addClassName(curElm.parentNode.parentNode, 'error');
                    }
                }
            }
        },

        /**
         * Clears the error of a given element. Normally executed before any validation, for all elements, as a reset.
         * 
         * @method _clearErrors
         * @param {DOMElement} formElm Form element to be cleared.
         * @private
         */
        _clearError: function(formElm)
        {
            //return;
            var aErrorLabel = formElm.getElementsByTagName('p');

            var curElm = false;
            for(var i = (aErrorLabel.length - 1); i >= 0; i--) {
                curElm = aErrorLabel[i];
                if(Css.hasClassName(curElm, this._errorClassName)) {
                    if(Css.hasClassName(curElm.parentNode, 'control')) {
                        Css.removeClassName(curElm.parentNode.parentNode, 'validation');
                        Css.removeClassName(curElm.parentNode.parentNode, 'error');
                        Css.removeClassName(curElm.parentNode.parentNode, 'warning');
                    }

                    if(Css.hasClassName(curElm,'tip') && Css.hasClassName(curElm,'error')){
                        curElm.parentNode.removeChild(curElm);
                    }
                }
            }

            var aErrorLabel2 = formElm.getElementsByTagName('ul');
            for(i = (aErrorLabel2.length - 1); i >= 0; i--) {
                curElm = aErrorLabel2[i];
                if(Css.hasClassName(curElm, 'control-group')) {
                    Css.removeClassName(curElm, 'validation');
                    Css.removeClassName(curElm, 'error');
                }
            }
        },

        /**
         * Removes unnecessary spaces to the left or right of a string
         * 
         * @method _trim
         * @param {String} stri String to be trimmed
         * @private
         * @return {String|undefined} String trimmed.
         */
        _trim: function(str)
        {
            if(typeof(str) === 'string')
            {
                return str.replace(/^\s+|\s+$|\n+$/g, '');
            }
        }
    };

    return FormValidator;

});

/**
 * @module Ink.UI.Droppable_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule("Ink.UI.Droppable","1",["Ink.Dom.Element_1", "Ink.Dom.Event_1", "Ink.Dom.Css_1"], function( Element, Event, Css) {

    /**
     * @class Ink.UI.Droppable
     * @version 1
     * @static
     */
	var Droppable = {
		/**
		 * Flag that determines if it's in debug mode or not
		 *
		 * @property debug
		 * @type {Boolean}
		 * @private
		 */
		debug: false,

        /**
         * Associative array with the elements that are droppable
         * 
         * @property _elements
         * @type {Object}
         * @private
         */
		_elements: {}, // indexed by id

        /**
		 * Makes an element droppable and adds it to the stack of droppable elements.
		 * Can consider it a constructor of droppable elements, but where no Droppable object is returned.
         * 
         * @method add
		 * @param {String|DOMElement}       element    - target element
		 * @param {optional Object}         options    - options object
		 *     @param {String}       [options.hoverclass] - Classname applied when an acceptable draggable element is hovering the element
		 *     @param {Array|String} [options.accept]     - Array or comma separated string of classnames for elements that can be accepted by this droppable
		 *     @param {Function}     [options.onHover]    - callback called when an acceptable draggable element is hovering the droppable. Gets the draggable and the droppable element as parameters.
		 *     @param {Function}     [options.onDrop]     - callback called when an acceptable draggable element is dropped. Gets the draggable, the droppable and the event as parameterse.
         * @public
         */
		add: function(element, options) {
			var opt = Ink.extendObj( {
				hoverclass:		false,
				accept:			false,
				onHover:		false,
				onDrop:			false,
				onDropOut:		false				
			}, options || {});

			element = Ink.i(element);

			if (opt.accept && opt.accept.constructor === Array) {
				opt.accept = opt.accept.join();
			}

			this._elements[element.id] = {options: opt};
			this.update(element.id);
		},

        /**
		 * Invoke every time a drag starts
         * 
         * @method updateAll
         * @public
         */
		/**
		 */
		updateAll: function() {
			for (var id in this._elements) {
				if (!this._elements.hasOwnProperty(id)) {	continue;	}
				this.update(Ink.i(id));
			}
		},

        /**
		 * Updates location and size of droppable element
         * 
         * @method update
		 * @param {String|DOMElement} element - target element
         * @public
         */
		update: function(element) {
			element = Ink.i(element);
			var data = this._elements[element.id];
			if (!data) {
				return; /*throw 'Data about element with id="' + element.id + '" was not found!';*/
			}

			data.left	= Element.offsetLeft(element);
			data.top	= Element.offsetTop( element);
			data.right	= data.left + Element.elementWidth( element);
			data.bottom	= data.top  + Element.elementHeight(element);

			// if (this.debug) {
			// 	// for debugging purposes
			// 	if (!data.rt) {		data.rt = SAPO.Utility.Debug.addRect(document.body,	[data.left, data.top], [data.right-data.left+1, data.bottom-data.top+1]);	}
			// 	else {				SAPO.Utility.Debug.updateRect(data.rt,				[data.left, data.top], [data.right-data.left+1, data.bottom-data.top+1]);	}
			// }
		},

        /**
		 * Removes an element from the droppable stack and removes the droppable behavior
		 * 
         * @method remove
		 * @param {String|DOMElement} el - target element
         * @public
         */
		remove: function(el) {
			el = Ink.i(el);
			delete this._elements[el.id];
		},

        /**
		 * Method called by a draggable to execute an action on a droppable
         * 
         * @method action
		 * @param {Object} coords    - coordinates where the action happened
		 * @param {String} type      - type of action. drag or drop.
		 * @param {Object} ev        - Event object
		 * @param {Object} draggable - draggable element
         * @public
         */
		action: function(coords, type, ev, draggable) {
			var opt, classnames, accept, el, element;

			// check all droppable elements
			for (var elId in this._elements) {
				if (!this._elements.hasOwnProperty(elId)) {	continue;	}
				el = this._elements[elId];
				opt = el.options;
				accept = false;
				element = Ink.i(elId);

				// check if our draggable is over our droppable
				if (coords.x >= el.left && coords.x <= el.right && coords.y >= el.top && coords.y <= el.bottom) {

					// INSIDE

					// check if the droppable accepts the draggable
					if (opt.accept) {
						classnames = draggable.className.split(' ');
						for ( var j = 0, lj = classnames.length; j < lj; j++) {
							if (opt.accept.search(classnames[j]) >= 0 && draggable !== element) {
								accept = true;
							}
						}
					}
					else {
						accept = true;
					}

					if (accept) {
						if (type === 'drag') {
							if (opt.hoverclass) {
								Css.addClassName(element, opt.hoverclass);
							}
							if (opt.onHover) {
								opt.onHover(draggable, element);
							}
						}
						else {
							if (type === 'drop' && opt.onDrop) {
								if (opt.hoverclass) {
									Css.removeClassName(element, opt.hoverclass);
								}
								if (opt.onDrop) {
									opt.onDrop(draggable, element, ev);
								}
							}
                        }
					}
				}
				else {
					// OUTSIDE
					if (type === 'drag' && opt.hoverclass) {
						Css.removeClassName(element, opt.hoverclass);
					}
					if(type === 'drop'){
						if(opt.onDropOut){
							opt.onDropOut(draggable, element, ev);
						}
					}
				}
			}
		}
	};

	return Droppable;
});

/**
 * @module Ink.UI.Draggable_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule("Ink.UI.Draggable","1",["Ink.Dom.Element_1", "Ink.Dom.Event_1", "Ink.Dom.Css_1", "Ink.Dom.Browser_1", "Ink.UI.Droppable_1"],function( Element, Event, Css, Browser, Droppable) {

    /**
     * @class Ink.UI.Draggable
     * @version 1
     * @constructor
     * @param {String|DOMElement} selector Either a CSS Selector string, or the form's DOMElement
     * @param {Object} [opptions] Optional object for configuring the component
	 *     @param {String}            [options.constraint]     - Movement constraint. None by default. Can be either vertical or horizontal.
	 *     @param {Number}            [options.top]            - top limit for the draggable area
	 *     @param {Number}            [options.right]          - right limit for the draggable area
	 *     @param {Number}            [options.bottom]         - bottom limit for the draggable area
	 *     @param {Number}            [options.left]           - left limit for the draggable area
	 *     @param {String|DOMElement} [options.handler]        - if specified, only this element will be used for dragging instead of the whole target element
	 *     @param {Boolean}           [options.revert]         - if true, reverts the draggable to the original position when dragging stops
	 *     @param {String}            [options.cursor]         - cursor type used over the draggable object
	 *     @param {Number}            [options.zindex]         - zindex applied to the draggable element while dragged
	 *     @param {Number}            [options.fps]            - if defined, on drag will run every n frames per second only
	 *     @param {DomElement}        [options.droppableProxy] - if set, a shallow copy of the droppableProxy will be put on document.body with transparent bg
	 *     @param {String}            [options.mouseAnchor]    - defaults to mouse cursor. can be 'left|center|right top|center|bottom'
	 *     @param {Function}          [options.onStart]        - callback called when dragging starts
	 *     @param {Function}          [options.onEnd]          - callback called when dragging stops
	 *     @param {Function}          [options.onDrag]         - callback called while dragging, prior to position updates
	 *     @param {Function}          [options.onChange]       - callback called while dragging, after position updates
     * @example
     *     Ink.requireModules( ['Ink.UI.Draggable_1'], function( Draggable ){
     *         new Draggable( 'myElementId' );
     *     });
     */
	var Draggable = function(element, options) {
		this.init(element, options);
	};

	Draggable.prototype = {

        /**
         * Init function called by the constructor
         * 
         * @method _init
         * @param {String|DOMElement} element ID of the element or DOM Element.
         * @param {Object} [options] Options object for configuration of the module.
         * @private
         */
		init: function(element, options) {
			var o = Ink.extendObj( {
				constraint:			false,
				top:				0,
				right:				Element.pageWidth(),
				bottom:				Element.pageHeight(),
				left:				0,
				handler:			false,
				revert:				false,
				cursor:				'move',
				zindex:				9999,
				onStart:			false,
				onEnd:				false,
				onDrag:				false,
				onChange:			false,
				droppableProxy:		false,
				mouseAnchor:		undefined,
				skipChildren:		true,
				debug:				false
			}, options || {});

			this.options = o;
			this.element = Ink.i(element);

			this.handle				= false;
			this.elmStartPosition	= false;
			this.active				= false;
			this.dragged			= false;
			this.prevCoords			= false;
			this.placeholder		= false;

			this.position			= false;
			this.zindex				= false;
			this.firstDrag			= true;

			if (o.fps) {
				this.deltaMs = 1000 / o.fps;
				this.lastRanAt = 0;
			}

			this.handlers = {};
			this.handlers.start			= Ink.bindEvent(this._onStart,this);
			this.handlers.dragFacade	= Ink.bindEvent(this._onDragFacade,this);
			this.handlers.drag			= Ink.bindEvent(this._onDrag,this);
			this.handlers.end			= Ink.bindEvent(this._onEnd,this);
			this.handlers.selectStart	= function(event) {	Event.stop(event);	return false;	};

			// set handler
			this.handle = (this.options.handler) ? Ink.i(this.options.handler) : this.element;
			this.handle.style.cursor = o.cursor;

			if (o.right  !== false) {	this.options.right	= o.right  - Element.elementWidth( element);	}
			if (o.bottom !== false) {	this.options.bottom	= o.bottom - Element.elementHeight(element);	}

			Event.observe(this.handle, 'touchstart', this.handlers.start);
			Event.observe(this.handle, 'mousedown', this.handlers.start);

			if (Browser.IE) {
				Event.observe(this.element, 'selectstart', this.handlers.selectStart);
			}
		},

        /**
		 * Removes the ability of the element of being dragged
         * 
         * @method destroy
         * @public
         */
		destroy: function() {
			Event.stopObserving(this.handle, 'touchstart', this.handlers.start);
			Event.stopObserving(this.handle, 'mousedown', this.handlers.start);

			if (Browser.IE) {
				Event.stopObserving(this.element, 'selectstart', this.handlers.selectStart);
			}
		},

        /**
		 * Browser-independant implementation of page scroll
         * 
         * @method _getPageScroll
         * @return {Array} Array where the first position is the scrollLeft and the second position is the scrollTop
         * @private
         */
		_getPageScroll: function() {

			if (typeof self.pageXOffset !== "undefined") {
				return [ self.pageXOffset, self.pageYOffset ];
			}
			if (typeof document.documentElement !== "undefined" && typeof document.documentElement.scrollLeft !== "undefined") {
				return [ document.documentElement.scrollLeft, document.documentElement.scrollTop ];
			}
			return [ document.body.scrollLeft, document.body.scrollTop ];
		},

        /**
		 * Gets coordinates for a given event (with added page scroll)
         * 
         * @method _getCoords
         * @param {Object} e window.event object.
         * @return {Array} Array where the first position is the x coordinate, the second is the y coordinate
         * @private
         */
		_getCoords: function(e) {
			var ps = this._getPageScroll();
			return {
				x: (e.touches ? e.touches[0].clientX : e.clientX) + ps[0],
				y: (e.touches ? e.touches[0].clientY : e.clientY) + ps[1]
			};
		},

        /**
		 * Clones src element's relevant properties to dst
         * 
         * @method _cloneStyle
         * @param {DOMElement} src Element from where we're getting the styles
         * @param {DOMElement} dst Element where we're placing the styles.
         * @private
         */
		_cloneStyle: function(src, dst) {
			dst.className = src.className;
			dst.style.borderWidth	= '0';
			dst.style.padding		= '0';
			dst.style.position		= 'absolute';
			dst.style.width			= Element.elementWidth(src)		+ 'px';
			dst.style.height		= Element.elementHeight(src)	+ 'px';
			dst.style.left			= Element.elementLeft(src)		+ 'px';
			dst.style.top			= Element.elementTop(src)		+ 'px';
			dst.style.cssFloat		= Css.getStyle(src, 'float');
			dst.style.display		= Css.getStyle(src, 'display');
		},

        /**
         * onStart event handler
         * 
         * @method _onStart
         * @param {Object} e window.event object
         * @return {Boolean|void} In some cases return false. Otherwise is void
         * @private
         */
		_onStart: function(e) {
			if (!this.active && Event.isLeftClick(e) || typeof e.button === 'undefined') {

				var tgtEl = e.target || e.srcElement;
				if (this.options.skipChildren && tgtEl !== this.element) {	return;	}

				Event.stop(e);

				this.elmStartPosition = [
					Element.elementLeft(this.element),
					Element.elementTop( this.element)
				];

				var pos = [
					parseInt(Css.getStyle(this.element, 'left'), 10),
					parseInt(Css.getStyle(this.element, 'top'),  10)
				];

				var dims = [
					Element.elementWidth( this.element),
					Element.elementHeight(this.element)
				];

				this.originalPosition = [ pos[0] ? pos[0]: null, pos[1] ? pos[1] : null ];
				this.delta = this._getCoords(e); // mouse coords at beginning of drag

				this.active = true;
				this.position = Css.getStyle(this.element, 'position');
				this.zindex = Css.getStyle(this.element, 'zIndex');

				var div = document.createElement('div');
				div.style.position		= this.position;
				div.style.width			= dims[0] + 'px';
				div.style.height		= dims[1] + 'px';
				div.style.marginTop		= Css.getStyle(this.element, 'margin-top');
				div.style.marginBottom	= Css.getStyle(this.element, 'margin-bottom');
				div.style.marginLeft	= Css.getStyle(this.element, 'margin-left');
				div.style.marginRight	= Css.getStyle(this.element, 'margin-right');
				div.style.borderWidth	= '0';
				div.style.padding		= '0';
				div.style.cssFloat		= Css.getStyle(this.element, 'float');
				div.style.display		= Css.getStyle(this.element, 'display');
				div.style.visibility	= 'hidden';

				this.delta2 = [ this.delta.x - this.elmStartPosition[0], this.delta.y - this.elmStartPosition[1] ]; // diff between top-left corner of obj and mouse
				if (this.options.mouseAnchor) {
					var parts = this.options.mouseAnchor.split(' ');
					var ad = [dims[0], dims[1]];	// starts with 'right bottom'
					if (parts[0] === 'left') {	ad[0] = 0;	} else if(parts[0] === 'center') {	ad[0] = parseInt(ad[0]/2, 10);	}
					if (parts[1] === 'top') {	ad[1] = 0;	} else if(parts[1] === 'center') {	ad[1] = parseInt(ad[1]/2, 10);	}
					this.applyDelta = [this.delta2[0] - ad[0], this.delta2[1] - ad[1]];
				}

				this.placeholder = div;

				if (this.options.onStart) {		this.options.onStart(this.element, e);		}

				if (this.options.droppableProxy) {	// create new transparent div to optimize DOM traversal during drag
					this.proxy = document.createElement('div');
					dims = [
						window.innerWidth	|| document.documentElement.clientWidth		|| document.body.clientWidth,
						window.innerHeight	|| document.documentElement.clientHeight	|| document.body.clientHeight
					];
					var fs = this.proxy.style;
					fs.width			= dims[0] + 'px';
					fs.height			= dims[1] + 'px';
					fs.position			= 'fixed';
					fs.left				= '0';
					fs.top				= '0';
					fs.zIndex			= this.options.zindex + 1;
					fs.backgroundColor	= '#FF0000';
					Css.setOpacity(this.proxy, 0);

					var firstEl = document.body.firstChild;
					while (firstEl && firstEl.nodeType !== 1) {	firstEl = firstEl.nextSibling;	}
					document.body.insertBefore(this.proxy, firstEl);

					Event.observe(this.proxy, 'mousemove', this.handlers[this.options.fps ? 'dragFacade' : 'drag']);
					Event.observe(this.proxy, 'touchmove', this.handlers[this.options.fps ? 'dragFacade' : 'drag']);
				}
				else {
					Event.observe(document, 'mousemove', this.handlers[this.options.fps ? 'dragFacade' : 'drag']);
				}

				this.element.style.position = 'absolute';
				this.element.style.zIndex = this.options.zindex;
				this.element.parentNode.insertBefore(this.placeholder, this.element);

				this._onDrag(e);

				Event.observe(document, 'mouseup',	this.handlers.end);
				Event.observe(document, 'touchend',	this.handlers.end);

				return false;
			}
		},

        /**
         * Function that gets the timestamp of the current run from time to time. (FPS)
         * 
         * @method _onDragFacade
         * @param {Object} window.event object.
         * @private
         */
		_onDragFacade: function(e) {
			var now = new Date().getTime();
			if (!this.lastRanAt || now > this.lastRanAt + this.deltaMs) {
				this.lastRanAt = now;
				this._onDrag(e);
			}
		},

        /**
         * Function that handles the dragging movement
         * 
         * @method _onDrag
         * @param {Object} window.event object.
         * @private
         */
		_onDrag: function(e) {
			if (this.active) {
				Event.stop(e);
				this.dragged = true;
				var mouseCoords	= this._getCoords(e),
					mPosX		= mouseCoords.x,
					mPosY		= mouseCoords.y,
					o			= this.options,
					newX		= false,
					newY		= false;

				if (!this.prevCoords) {		this.prevCoords = {x: 0, y: 0};		}

				if (mPosX !== this.prevCoords.x || mPosY !== this.prevCoords.y) {
					if (o.onDrag) {		o.onDrag(this.element, e);		}
					this.prevCoords = mouseCoords;

					newX = this.elmStartPosition[0] + mPosX - this.delta.x;
					newY = this.elmStartPosition[1] + mPosY - this.delta.y;

					if (o.constraint === 'horizontal' || o.constraint === 'both') {
						if (o.right !== false && newX > o.right) {		newX = o.right;		}
						if (o.left  !== false && newX < o.left) {		newX = o.left;		}
					}
					if (o.constraint === 'vertical' || o.constraint === 'both') {
						if (o.bottom !== false && newY > o.bottom) {	newY = o.bottom;	}
						if (o.top    !== false && newY < o.top) {		newY = o.top;		}
					}

					if (this.firstDrag) {
						if (Droppable) {	Droppable.updateAll();	}
						/*this.element.style.position = 'absolute';
						this.element.style.zIndex = this.options.zindex;
						this.element.parentNode.insertBefore(this.placeholder, this.element);*/
						this.firstDrag = false;
					}

					if (newX) {		this.element.style.left = newX + 'px';		}
					if (newY) {		this.element.style.top  = newY + 'px';		}

					if (Droppable) {
						// apply applyDelta defined on drag init
						var mouseCoords2 = this.options.mouseAnchor ? {x: mPosX - this.applyDelta[0], y: mPosY - this.applyDelta[1]} : mouseCoords;

						// for debugging purposes
						// if (this.options.debug) {
						// 	if (!this.pt) {
						// 		this.pt = Debug.addPoint(document.body, [mouseCoords2.x, mouseCoords2.y], '#0FF', 9);
						// 		this.pt.style.zIndex = this.options.zindex + 1;
						// 	}
						// 	else {
						// 		Debug.movePoint(this.pt, [mouseCoords2.x, mouseCoords2.y]);
						// 	}
						// }

						Droppable.action(mouseCoords2, 'drag', e, this.element);
					}
					if (o.onChange) {	o.onChange(this);	}
				}
			}
		},

        /**
         * Function that handles the end of the dragging process
         * 
         * @method _onEnd
         * @param {Object} window.event object.
         * @private
         */
		_onEnd: function(e) {
			Event.stopObserving(document, 'mousemove', this.handlers.drag);
			Event.stopObserving(document, 'touchmove', this.handlers.drag);

			if (this.options.fps) {
				this._onDrag(e);
			}

			if (this.active && this.dragged) {

				if (this.options.droppableProxy) {	// remove transparent div...
					document.body.removeChild(this.proxy);
				}

				if (this.pt) {	// remove debugging element...
					this.pt.parentNode.removeChild(this.pt);
					this.pt = undefined;
				}

	            /*if (this.options.revert) {
					this.placeholder.parentNode.removeChild(this.placeholder);
				}*/

	            if(this.placeholder) {
	                this.placeholder.parentNode.removeChild(this.placeholder);
	            }

				if (this.options.revert) {
					this.element.style.position = this.position;
					if (this.zindex !== null) {
						this.element.style.zIndex = this.zindex;
					}
					else {
						this.element.style.zIndex = 'auto';
					} // restore default zindex of it had none

					this.element.style.left = (this.originalPosition[0]) ? this.originalPosition[0] + 'px' : '';
					this.element.style.top  = (this.originalPosition[1]) ? this.originalPosition[1] + 'px' : '';
				}

				if (this.options.onEnd) {
					this.options.onEnd(this.element, e);
				}

				if (Droppable) {
					Droppable.action(this._getCoords(e), 'drop', e, this.element);
				}

				this.position	= false;
				this.zindex		= false;
				this.firstDrag	= true;
			}

			this.active			= false;
			this.dragged		= false;
		}
	};

	return Draggable;

});

/**
 * @module Ink.UI.DatePicker_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.DatePicker', '1', ['Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1','Ink.Util.Date_1'], function(Aux, Event, Css, Element, Selector, InkArray, InkDate ) {
    'use strict';    

    /**
     * @class Ink.UI.DatePicker
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @uses Ink.Util.Array
     * @uses Ink.Util.Date
     *
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *      @param {String}   [options.instance]         unique id for the datepicker
     *      @param {String}   [options.format]           Date format string
     *      @param {String}   [options.cssClass]         CSS class to be applied to the datepicker
     *      @param {String}   [options.position]         position the datepicker. Accept right or bottom, default is right
     *      @param {Boolean}  [options.onFocus]          if the datepicker should open when the target element is focused
     *      @param {Function} [options.onYearSelected]   callback function to execute when the year is selected
     *      @param {Function} [options.onMonthSelected]  callback function to execute when the month is selected
     *      @param {Function} [options.validDayFn]       callback function to execute when 'rendering' the day (in the month view)
     *      @param {String}   [options.startDate]        Date to define init month. Must be in yyyy-mm-dd format
     *      @param {Function} [options.onSetDate]        callback to execute when set date
     *      @param {Boolean}  [options.displayInSelect]  whether to display the component in a select. defaults to false.
     *      @param {Boolean}  [options.showClose]        whether to display the close button or not. defaults to true.
     *      @param {Boolean}  [options.showClean]        whether to display the clean button or not. defaults to true.
     *      @param {String}   [options.yearRange]        enforce limits to year for the Date, ex: '1990:2020' (deprecated)
     *      @param {String}   [options.dateRange]        enforce limits to year, month and day for the Date, ex: '1990-08-25:2020-11'
     *      @paran {Number}   [options.startWeekDay]     day to use as first column on the calendar view. Defaults to Monday (1)
     *      @param {String}   [options.closeText]        text to display on close button. defaults to 'Fechar'
     *      @param {String}   [options.cleanText]        text to display on clean button. defaults to 'Limpar'
     *      @param {String}   [options.prevLinkText]     text to display on the previous button. defaults to '«'
     *      @param {String}   [options.nextLinkText]     text to display on the previous button. defaults to '«'
     *      @param {String}   [options.ofText]           text to display between month and year. defaults to ' de '
     *      @param {Object}   [options.month]            Hash of month names. Defaults to portuguese month names. January is 1.
     *      @param {Object}   [options.wDay]             Hash of weekdays. Defaults to portuguese month names. Sunday is 0.
     *
     * @example
     *     <input type="text" id="dPicker" />
     *     <script>
     *         Ink.requireModules(['Ink.Dom.Selector_1','Ink.UI.DatePicker_1'],function( Selector, DatePicker ){
     *             var datePickerElement = Ink.s('#dPicker');
     *             var datePickerObj = new DatePicker( datePickerElement );
     *         });
     *     </script>
     */
    var DatePicker = function(selector, options) {

        if (selector) {
            this._dataField = Aux.elOrSelector(selector, '1st argument');
        }

        this._options = Ink.extendObj({
            instance:        'scdp_' + Math.round(99999*Math.random()),
            format:          'yyyy-mm-dd',
            cssClass:        'sapo_component_datepicker',
            position:        'right',
            onFocus:         true,
            onYearSelected:  undefined,
            onMonthSelected: undefined,
            validDayFn:      undefined,
            startDate:       false, // format yyyy-mm-dd
            onSetDate:       false,
            displayInSelect: false,
            showClose:       true,
            showClean:       true,
            yearRange:       false,
            dateRange:       false,
            startWeekDay:    1,
            closeText:       'Close',
            cleanText:       'Clear',
            prevLinkText:    '«',
            nextLinkText:    '»',
            ofText:          '&nbsp;de&nbsp;',
            month: {
                 1:'January',
                 2:'February',
                 3:'March',
                 4:'April',
                 5:'May',
                 6:'June',
                 7:'July',
                 8:'August',
                 9:'September',
                10:'October',
                11:'November',
                12:'December'
            },
            wDay: {
                0:'Sunday',
                1:'Monday',
                2:'Tuesday',
                3:'Wednesday',
                4:'Thursday',
                5:'Friday',
                6:'Saturday'
            }
        }, Element.data(this._dataField) || {});

        this._options = Ink.extendObj(this._options, options || {});

        this._options.format = this._dateParsers[ this._options.format ] || this._options.format;

        this._hoverPicker = false;

        this._picker = null;
        if (this._options.pickerField) {
            this._picker = Aux.elOrSelector(this._options.pickerField, 'pickerField');
        }

        this._today = new Date();
        this._day   = this._today.getDate( );
        this._month = this._today.getMonth( );
        this._year  = this._today.getFullYear( );

        this._setMinMax( this._options.dateRange || this._options.yearRange );
        this._data = new Date( Date.UTC.apply( this , this._checkDateRange( this._year , this._month , this._day ) ) );

        if(this._options.startDate && typeof this._options.startDate === 'string' && /\d\d\d\d\-\d\d\-\d\d/.test(this._options.startDate)) {
            this.setDate( this._options.startDate );
        }

        this._init();

        this._render();

        if ( !this._options.startDate ){
            if( this._dataField && typeof this._dataField.value === 'string' && this._dataField.value){
                this.setDate( this._dataField.value );
            }
        }

        Aux.registerInstance(this, this._containerObject, 'datePicker');
    };

    DatePicker.prototype = {
        version: '0.1',

        /**
         * Initialization function. Called by the constructor and
         * receives the same parameters.
         *
         * @method _init
         * @private
         */
        _init: function(){
            Ink.extendObj(this._options,this._lang || {});
        },

        /**
         * Renders the DatePicker's markup
         *
         * @method _render
         * @private
         */
        _render: function() {
            /*jshint maxstatements:100, maxcomplexity:30 */
            this._containerObject = document.createElement('div');

            this._containerObject.id = this._options.instance;

            this._containerObject.className = 'sapo_component_datepicker';
            var dom = document.getElementsByTagName('body')[0];

            if(this._options.showClose || this._options.showClean){
                this._superTopBar = document.createElement("div");
                this._superTopBar.className = 'sapo_cal_top_options';
                if(this._options.showClean){
                    var clean = document.createElement('a');
                    clean.className = 'clean';
                    clean.innerHTML = this._options.cleanText;
                    this._superTopBar.appendChild(clean);
                }
                if(this._options.showClose){
                    var close = document.createElement('a');
                    close.className = 'close';
                    close.innerHTML = this._options.closeText;
                    this._superTopBar.appendChild(close);
                }
                this._containerObject.appendChild(this._superTopBar);
            }


            var calendarTop = document.createElement("div");
            calendarTop.className = 'sapo_cal_top';

            this._monthDescContainer = document.createElement("div");
            this._monthDescContainer.className = 'sapo_cal_month_desc';

            this._monthPrev = document.createElement('div');
            this._monthPrev.className = 'sapo_cal_prev';
            this._monthPrev.innerHTML ='<a href="#prev" class="change_month_prev">' + this._options.prevLinkText + '</a>';

            this._monthNext = document.createElement('div');
            this._monthNext.className = 'sapo_cal_next';
            this._monthNext.innerHTML ='<a href="#next" class="change_month_next">' + this._options.nextLinkText + '</a>';

            calendarTop.appendChild(this._monthPrev);
            calendarTop.appendChild(this._monthDescContainer);
            calendarTop.appendChild(this._monthNext);

            this._monthContainer = document.createElement("div");
            this._monthContainer.className = 'sapo_cal_month';

            this._containerObject.appendChild(calendarTop);
            this._containerObject.appendChild(this._monthContainer);

            this._monthSelector = document.createElement('ul');
            this._monthSelector.className = 'sapo_cal_month_selector';

            var ulSelector;
            var liMonth;
            for(var i=1; i<=12; i++){
                if ((i-1) % 4 === 0) {
                    ulSelector = document.createElement('ul');
                }
                liMonth = document.createElement('li');
                liMonth.innerHTML = '<a href="#" class="sapo_calmonth_' + ( (String(i).length === 2) ? i : "0" + i) + '">' + this._options.month[i].substring(0,3) + '</a>';
                ulSelector.appendChild(liMonth);
                if (i % 4 === 0) {
                    this._monthSelector.appendChild(ulSelector);
                }
            }

            this._containerObject.appendChild(this._monthSelector);

            this._yearSelector = document.createElement('ul');
            this._yearSelector.className = 'sapo_cal_year_selector';

            this._containerObject.appendChild(this._yearSelector);

            if(!this._options.onFocus || this._options.displayInSelect){
                if(!this._options.pickerField){
                    this._picker = document.createElement('a');
                    this._picker.href = '#open_cal';
                    this._picker.innerHTML = 'open';
                    this._picker.style.position = 'absolute';
                    this._picker.style.top = Element.elementTop(this._dataField);
                    this._picker.style.left = Element.elementLeft(this._dataField) + (Element.elementWidth(this._dataField) || 0) + 5 + 'px';
                    this._dataField.parentNode.appendChild(this._picker);
                    this._picker.className = 'sapo_cal_date_picker';
                } else {
                    this._picker = Aux.elOrSelector(this._options.pickerField, 'pickerField');
                }
            }

            if(this._options.displayInSelect){
                if (this._options.dayField && this._options.monthField && this._options.yearField || this._options.pickerField) {
                    this._options.dayField   = Aux.elOrSelector(this._options.dayField,   'dayField');
                    this._options.monthField = Aux.elOrSelector(this._options.monthField, 'monthField');
                    this._options.yearField  = Aux.elOrSelector(this._options.yearField,  'yearField');
                }
                else {
                    throw "To use display in select you *MUST* to set dayField, monthField, yearField and pickerField!";
                }
            }

            dom.insertBefore(this._containerObject, dom.childNodes[0]);
            // this._dataField.parentNode.appendChild(this._containerObject, dom.childNodes[0]);

            if (!this._picker) {
                Event.observe(this._dataField,'focus',Ink.bindEvent(function(){
                    this._containerObject = Element.clonePosition(this._containerObject, this._dataField);

                    if ( this._options.position === 'bottom' )
                    {
                        this._containerObject.style.top = Element.elementHeight(this._dataField) + Element.offsetTop(this._dataField) + 'px';
                        this._containerObject.style.left = Element.offset2(this._dataField)[0] +'px';
                    }
                    else
                    {
                        this._containerObject.style.top = Element.offset2(this._dataField)[1] +'px';
                        this._containerObject.style.left = Element.elementWidth(this._dataField) + Element.offset2(this._dataField)[0] +'px';
                    }
                    //dom.appendChild(this._containerObject);
                    this._updateDate();
                    this._showMonth();
                    this._containerObject.style.display = 'block';
                },this));
            }
            else {
                Event.observe(this._picker,'click',Ink.bindEvent(function(e){
                    Event.stop(e);
                    this._containerObject = Element.clonePosition(this._containerObject,this._picker);
                    this._updateDate();
                    this._showMonth();
                    this._containerObject.style.display = 'block';
                },this));
            }

            if(!this._options.displayInSelect){
                Event.observe(this._dataField,'change', Ink.bindEvent(function() {
                        this._updateDate( );
                        this._showDefaultView( );
                        this.setDate( );
                        if ( !this._hoverPicker )
                        {
                            this._containerObject.style.display = 'none';
                        }
                    },this));
                Event.observe(this._dataField,'blur', Ink.bindEvent(function() {
                        if ( !this._hoverPicker )
                        {
                            this._containerObject.style.display = 'none';
                        }
                    },this));
            }
            else {
                Event.observe(this._options.dayField,'change', Ink.bindEvent(function(){
                        var yearSelected = this._options.yearField[this._options.yearField.selectedIndex].value;
                        if(yearSelected !== '' && yearSelected !== 0) {
                            this._updateDate();
                            this._showDefaultView();
                        }
                    },this));
               Event.observe(this._options.monthField,'change', Ink.bindEvent(function(){
                        var yearSelected = this._options.yearField[this._options.yearField.selectedIndex].value;
                        if(yearSelected !== '' && yearSelected !== 0){
                            this._updateDate();
                            this._showDefaultView();
                        }
                    },this));
                Event.observe(this._options.yearField,'change', Ink.bindEvent(function(){
                        this._updateDate();
                        this._showDefaultView();
                    },this));
            }

            Event.observe(document,'click',Ink.bindEvent(function(e){
                if (e.target === undefined) {   e.target = e.srcElement;    }
                if (!Element.descendantOf(this._containerObject, e.target) && e.target !== this._dataField) {
                    if (!this._picker) {
                        this._containerObject.style.display = 'none';
                    }
                    else if (e.target !== this._picker &&
                             (!this._options.displayInSelect ||
                              (e.target !== this._options.dayField && e.target !== this._options.monthField && e.target !== this._options.yearField) ) ) {
                        if (!this._options.dayField ||
                                (!Element.descendantOf(this._options.dayField,   e.target) &&
                                 !Element.descendantOf(this._options.monthField, e.target) &&
                                 !Element.descendantOf(this._options.yearField,  e.target)      ) ) {
                            this._containerObject.style.display = 'none';
                        }
                    }
                }
            },this));

            this._showMonth();

            this._monthChanger = document.createElement('a');
            this._monthChanger.href = '#monthchanger';
            this._monthChanger.className = 'sapo_cal_link_month';
            this._monthChanger.innerHTML = this._options.month[this._month + 1];

            this._deText = document.createElement('span');
            this._deText.innerHTML = this._options._deText;


            this._yearChanger = document.createElement('a');
            this._yearChanger.href = '#yearchanger';
            this._yearChanger.className = 'sapo_cal_link_year';
            this._yearChanger.innerHTML = this._year;
            this._monthDescContainer.innerHTML = '';
            this._monthDescContainer.appendChild(this._monthChanger);
            this._monthDescContainer.appendChild(this._deText);
            this._monthDescContainer.appendChild(this._yearChanger);

            Event.observe(this._containerObject,'mouseover',Ink.bindEvent(function(e)
            {
                Event.stop( e );
                this._hoverPicker = true;
            },this));

            Event.observe(this._containerObject,'mouseout',Ink.bindEvent(function(e)
            {
                Event.stop( e );
                this._hoverPicker = false;
            },this));

            Event.observe(this._containerObject,'click',Ink.bindEvent(function(e){
                    if(typeof(e.target) === 'undefined'){
                        e.target = e.srcElement;
                    }
                    var className = e.target.className;
                    var isInactive  = className.indexOf( 'sapo_cal_off' ) !== -1;

                    Event.stop(e);

                    if( className.indexOf('sapo_cal_') === 0 && !isInactive ){
                            var day = className.substr( 9 , 2 );
                            if( Number( day ) ) {
                                this.setDate( this._year + '-' + ( this._month + 1 ) + '-' + day );
                                this._containerObject.style.display = 'none';
                            } else if(className === 'sapo_cal_link_month'){
                                this._monthContainer.style.display = 'none';
                                this._yearSelector.style.display = 'none';
                                this._monthPrev.childNodes[0].className = 'action_inactive';
                                this._monthNext.childNodes[0].className = 'action_inactive';
                                this._setActiveMonth();
                                this._monthSelector.style.display = 'block';
                            } else if(className === 'sapo_cal_link_year'){
                                this._monthPrev.childNodes[0].className = 'action_inactive';
                                this._monthNext.childNodes[0].className = 'action_inactive';
                                this._monthSelector.style.display = 'none';
                                this._monthContainer.style.display = 'none';
                                this._showYearSelector();
                                this._yearSelector.style.display = 'block';
                            }
                    } else if( className.indexOf("sapo_calmonth_") === 0 && !isInactive ){
                            var month=className.substr(14,2);
                            if(Number(month)){
                                this._month = month - 1;
                                // if( typeof this._options.onMonthSelected === 'function' ){
                                //     this._options.onMonthSelected(this, {
                                //         'year': this._year,
                                //         'month' : this._month
                                //     });
                                // }
                                this._monthSelector.style.display = 'none';
                                this._monthPrev.childNodes[0].className = 'change_month_prev';
                                this._monthNext.childNodes[0].className = 'change_month_next';

                                if ( this._year < this._yearMin || this._year === this._yearMin && this._month <= this._monthMin ){
                                    this._monthPrev.childNodes[0].className = 'action_inactive';
                                }
                                else if( this._year > this._yearMax || this._year === this._yearMax && this._month >= this._monthMax ){
                                    this._monthNext.childNodes[0].className = 'action_inactive';
                                }

                                this._updateCal();
                                this._monthContainer.style.display = 'block';
                            }
                    } else if( className.indexOf("sapo_calyear_") === 0 && !isInactive ){
                            var year=className.substr(13,4);
                            if(Number(year)){
                                this._year = year;
                                if( typeof this._options.onYearSelected === 'function' ){
                                    this._options.onYearSelected(this, {
                                        'year': this._year
                                    });
                                }
                                this._monthPrev.childNodes[0].className = 'action_inactive';
                                this._monthNext.childNodes[0].className = 'action_inactive';
                                this._yearSelector.style.display='none';
                                this._setActiveMonth();
                                this._monthSelector.style.display='block';
                            }
                    } else if( className.indexOf('change_month_') === 0 && !isInactive ){
                            if(className === 'change_month_next'){
                                this._updateCal(1);
                            } else if(className === 'change_month_prev'){
                                this._updateCal(-1);
                            }
                    } else if( className.indexOf('change_year_') === 0 && !isInactive ){
                            if(className === 'change_year_next'){
                                this._showYearSelector(1);
                            } else if(className === 'change_year_prev'){
                                this._showYearSelector(-1);
                            }
                    } else if(className === 'clean'){
                        if(this._options.displayInSelect){
                            this._options.yearField.selectedIndex = 0;
                            this._options.monthField.selectedIndex = 0;
                            this._options.dayField.selectedIndex = 0;
                        } else {
                            this._dataField.value = '';
                        }
                    } else if(className === 'close'){
                        this._containerObject.style.display = 'none';
                    }

                    this._updateDescription();
                },this));

        },

        /**
         * Sets the range of dates allowed to be selected in the Date Picker
         *
         * @method _setMinMax
         * @param {String} dateRange Two dates separated by a ':'. Example: 2013-01-01:2013-12-12
         * @private
         */
        _setMinMax : function( dateRange )
        {
            var auxDate;
            if( dateRange )
            {
                var dates = dateRange.split( ':' );
                var pattern = /^(\d{4})((\-)(\d{1,2})((\-)(\d{1,2}))?)?$/;
                if ( dates[ 0 ] )
                {
                    if ( dates[ 0 ] === 'NOW' )
                    {
                        this._yearMin   = this._today.getFullYear( );
                        this._monthMin  = this._today.getMonth( ) + 1;
                        this._dayMin    = this._today.getDate( );
                    }
                    else if ( pattern.test( dates[ 0 ] ) )
                    {
                        auxDate = dates[ 0 ].split( '-' );

                        this._yearMin   = Math.floor( auxDate[ 0 ] );
                        this._monthMin  = Math.floor( auxDate[ 1 ] ) || 1;
                        this._dayMin    = Math.floor( auxDate[ 2 ] ) || 1;

                        if ( 1 < this._monthMin && this._monthMin > 12 )
                        {
                            this._monthMin = 1;
                            this._dayMin = 1;
                        }

                        if ( 1 < this._dayMin && this._dayMin > this._daysInMonth( this._yearMin , this._monthMin ) )
                        {
                            this._dayMin = 1;
                        }
                    }
                    else
                    {
                        this._yearMin   = Number.MIN_VALUE;
                        this._monthMin  = 1;
                        this._dayMin    = 1;
                    }
                }

                if ( dates[ 1 ] )
                {
                    if ( dates[ 1 ] === 'NOW' )
                    {
                        this._yearMax   = this._today.getFullYear( );
                        this._monthMax  = this._today.getMonth( ) + 1;
                        this._dayMax    = this._today.getDate( );
                    }
                    else if ( pattern.test( dates[ 1 ] ) )
                    {
                        auxDate = dates[ 1 ].split( '-' );

                        this._yearMax   = Math.floor( auxDate[ 0 ] );
                        this._monthMax  = Math.floor( auxDate[ 1 ] ) || 12;
                        this._dayMax    = Math.floor( auxDate[ 2 ] ) || this._daysInMonth( this._yearMax , this._monthMax );

                        if ( 1 < this._monthMax && this._monthMax > 12 )
                        {
                            this._monthMax = 12;
                            this._dayMax = 31;
                        }

                        var MDay = this._daysInMonth( this._yearMax , this._monthMax );
                        if ( 1 < this._dayMax && this._dayMax > MDay )
                        {
                            this._dayMax = MDay;
                        }
                    }
                    else
                    {
                        this._yearMax   = Number.MAX_VALUE;
                        this._monthMax  = 12;
                        this._dayMax   = 31;
                    }
                }

                if ( !( this._yearMax >= this._yearMin && (this._monthMax > this._monthMin || ( (this._monthMax === this._monthMin) && (this._dayMax >= this._dayMin) ) ) ) )
                {
                    this._yearMin   = Number.MIN_VALUE;
                    this._monthMin  = 1;
                    this._dayMin    = 1;

                    this._yearMax   = Number.MAX_VALUE;
                    this._monthMax  = 12;
                    this._dayMaXx   = 31;
                }
            }
            else
            {
                this._yearMin   = Number.MIN_VALUE;
                this._monthMin  = 1;
                this._dayMin    = 1;

                this._yearMax   = Number.MAX_VALUE;
                this._monthMax  = 12;
                this._dayMax   = 31;
            }
        },

        /**
         * Checks if a date is between the valid range.
         * Starts by checking if the date passed is valid. If not, will fallback to the 'today' date.
         * Then checks if the all params are inside of the date range specified. If not, it will fallback to the nearest valid date (either Min or Max).
         *
         * @method _checkDateRange
         * @param  {Number} year  Year with 4 digits (yyyy)
         * @param  {Number} month Month
         * @param  {Number} day   Day
         * @return {Array}       Array with the final processed date.
         * @private
         */
        _checkDateRange : function( year , month , day )
        {
            if ( !this._isValidDate( year , month + 1 , day ) )
            {
                year  = this._today.getFullYear( );
                month = this._today.getMonth( );
                day   = this._today.getDate( );
            }

            if ( year > this._yearMax )
            {
                year  = this._yearMax;
                month = this._monthMax - 1;
                day   = this._dayMax;
            }
            else if ( year < this._yearMin )
            {
                year  = this._yearMin;
                month = this._monthMin - 1;
                day   = this._dayMin;
            }

            if ( year === this._yearMax && month + 1 > this._monthMax )
            {
                month = this._monthMax - 1;
                day   = this._dayMax;
            }
            else if ( year === this._yearMin && month + 1 < this._monthMin )
            {
                month = this._monthMin - 1;
                day   = this._dayMin;
            }

            if ( year === this._yearMax && month + 1 === this._monthMax && day > this._dayMax ){ day = this._dayMax; }
            else if ( year === this._yearMin && month + 1 === this._monthMin && day < this._dayMin ){ day = this._dayMin; }
            else if ( day > this._daysInMonth( year , month + 1 ) ){ day = this._daysInMonth( year , month + 1 ); }

            return [ year , month , day ];
        },

        /**
         * Sets the markup in the default view mode (showing the days).
         * Also disables the previous and next buttons in case they don't meet the range requirements.
         *
         * @method _showDefaultView
         * @private
         */
        _showDefaultView: function(){
            this._yearSelector.style.display = 'none';
            this._monthSelector.style.display = 'none';
            this._monthPrev.childNodes[0].className = 'change_month_prev';
            this._monthNext.childNodes[0].className = 'change_month_next';

            if ( this._year < this._yearMin || this._year === this._yearMin && this._month + 1 <= this._monthMin ){
                this._monthPrev.childNodes[0].className = 'action_inactive';
            }
            else if( this._year > this._yearMax || this._year === this._yearMax && this._month + 1 >= this._monthMax ){
                this._monthNext.childNodes[0].className = 'action_inactive';
            }

            this._monthContainer.style.display = 'block';
        },

        /**
         * Updates the date shown on the datepicker
         *
         * @method _updateDate
         * @private
         */
        _updateDate: function(){
            var dataParsed;
             if(!this._options.displayInSelect){
                 if(this._dataField.value !== ''){
                    if(this._isDate(this._options.format,this._dataField.value)){
                        dataParsed = this._getDataArrayParsed(this._dataField.value);
                        dataParsed = this._checkDateRange( dataParsed[ 0 ] , dataParsed[ 1 ] - 1 , dataParsed[ 2 ] );

                        this._year  = dataParsed[ 0 ];
                        this._month = dataParsed[ 1 ];
                        this._day   = dataParsed[ 2 ];
                    }else{
                        this._dataField.value = '';
                        this._year  = this._data.getFullYear( );
                        this._month = this._data.getMonth( );
                        this._day   = this._data.getDate( );
                    }
                    this._data.setFullYear( this._year , this._month , this._day );
                    this._dataField.value = this._writeDateInFormat( );
                }
            } else {
                dataParsed = [];
                if(this._isValidDate(
                    dataParsed[0] = this._options.yearField[this._options.yearField.selectedIndex].value,
                    dataParsed[1] = this._options.monthField[this._options.monthField.selectedIndex].value,
                    dataParsed[2] = this._options.dayField[this._options.dayField.selectedIndex].value
                )){
                    dataParsed = this._checkDateRange( dataParsed[ 0 ] , dataParsed[ 1 ] - 1 , dataParsed[ 2 ] );

                    this._year  = dataParsed[ 0 ];
                    this._month = dataParsed[ 1 ];
                    this._day   = dataParsed[ 2 ];
                } else {
                    dataParsed = this._checkDateRange( dataParsed[ 0 ] , dataParsed[ 1 ] - 1 , 1 );
                    if(this._isValidDate( dataParsed[ 0 ], dataParsed[ 1 ] + 1 ,dataParsed[ 2 ] )){
                        this._year  = dataParsed[ 0 ];
                        this._month = dataParsed[ 1 ];
                        this._day   = this._daysInMonth(dataParsed[0],dataParsed[1]);

                        this.setDate();
                    }
                }
            }
            this._updateDescription();
            this._showMonth();
        },

        /**
         * Updates the date description shown at the top of the datepicker
         *
         * @method  _updateDescription
         * @private
         */
        _updateDescription: function(){
            this._monthChanger.innerHTML = this._options.month[ this._month + 1 ];
            this._deText.innerHTML = this._options.ofText;
            this._yearChanger.innerHTML = this._year;
        },

        /**
         * Renders the year selector view of the datepicker
         *
         * @method _showYearSelector
         * @private
         */
        _showYearSelector: function(){
            if (arguments.length){
                var year = + this._year + arguments[0]*10;
                year=year-year%10;
                if ( year>this._yearMax || year+9<this._yearMin ){
                    return;
                }
                this._year = + this._year + arguments[0]*10;
            }

            var str = "<li>";
            var ano_base = this._year-(this._year%10);

            for (var i=0; i<=11; i++){
                if (i % 4 === 0){
                    str+='<ul>';
                }

                if (!i || i === 11){
                    if ( i && (ano_base+i-1)<=this._yearMax && (ano_base+i-1)>=this._yearMin ){
                        str+='<li><a href="#year_next" class="change_year_next">' + this._options.nextLinkText + '</a></li>';
                    } else if( (ano_base+i-1)<=this._yearMax && (ano_base+i-1)>=this._yearMin ){
                         str+='<li><a href="#year_prev" class="change_year_prev">' + this._options.prevLinkText + '</a></li>';
                    } else {
                        str +='<li>&nbsp;</li>';
                    }
                } else {
                    if ( (ano_base+i-1)<=this._yearMax && (ano_base+i-1)>=this._yearMin ){
                        str+='<li><a href="#" class="sapo_calyear_' + (ano_base+i-1)  + (((ano_base+i-1) === this._data.getFullYear()) ? ' sapo_cal_on' : '') + '">' + (ano_base+i-1) +'</a></li>';
                    } else {
                        str+='<li><a href="#" class="sapo_cal_off">' + (ano_base+i-1) +'</a></li>';

                    }
                }

                if ((i+1) % 4 === 0) {
                    str+='</ul>';
                }
            }

            str += "</li>";

            this._yearSelector.innerHTML = str;
        },

        /**
         * This function returns the given date in an array format
         *
         * @method _getDataArrayParsed
         * @param {String} dateStr A date on a string.
         * @private
         * @return {Array} The given date in an array format
         */
        _getDataArrayParsed: function(dateStr){
            var arrData = [];
            var data = InkDate.set( this._options.format , dateStr );
            if (data) {
                arrData = [ data.getFullYear( ) , data.getMonth( ) + 1 , data.getDate( ) ];
            }
            return arrData;
        },

        /**
         * Checks if a date is valid
         *
         * @method _isValidDate
         * @param {Number} year
         * @param {Number} month
         * @param {Number} day
         * @private
         * @return {Boolean} True if the date is valid, false otherwise
         */
        _isValidDate: function(year, month, day){
            var yearRegExp = /^\d{4}$/;
            var validOneOrTwo = /^\d{1,2}$/;
            return (
                yearRegExp.test(year)     &&
                validOneOrTwo.test(month) &&
                validOneOrTwo.test(day)   &&
                month >= 1  &&
                month <= 12 &&
                day   >= 1  &&
                day   <= this._daysInMonth(year,month)
            );
        },

        /**
         * Checks if a given date is an valid format.
         *
         * @method _isDate
         * @param {String} format A date format.
         * @param {String} dateStr A date on a string.
         * @private
         * @return {Boolean} True if the given date is valid according to the given format
         */
        _isDate: function(format, dateStr){
            try {
                if (typeof format === 'undefined'){
                    return false;
                }
                var data = InkDate.set( format , dateStr );
                if( data && this._isValidDate( data.getFullYear( ) , data.getMonth( ) + 1 , data.getDate( ) ) ){
                    return true;
                }
            } catch (ex) {}

            return false;
        },


        /**
         * This method returns the date written with the format specified on the options
         *
         * @method _writeDateInFormat
         * @private
         * @return {String} Returns the current date of the object in the specified format
         */
       _writeDateInFormat:function(){
            return InkDate.get( this._options.format , this._data );
        },

        /**
         * This method allows the user to set the DatePicker's date on run-time.
         *
         * @method setDate
         * @param {String} dateString A date string in yyyy-mm-dd format.
         * @public
         */
        setDate : function( dateString )
        {
            if ( typeof dateString === 'string' && /\d{4}-\d{1,2}-\d{1,2}/.test( dateString ) )
            {
                var auxDate = dateString.split( '-' );
                this._year  = auxDate[ 0 ];
                this._month = auxDate[ 1 ] - 1;
                this._day   = auxDate[ 2 ];
            }

            this._setDate( );
        },

        /**
         * Sets the chosen date on the target input field
         *
         * @method _setDate
         * @param {DOMElement} objClicked Clicked object inside the DatePicker's calendar.
         * @private
         */
        _setDate : function( objClicked ){
            if( typeof objClicked !== 'undefined' && objClicked.className && objClicked.className.indexOf('sapo_cal_') === 0 )
            {
                this._day = objClicked.className.substr( 9 , 2 );
            }
            this._data.setFullYear.apply( this._data , this._checkDateRange( this._year , this._month , this._day ) );

            if(!this._options.displayInSelect){
                this._dataField.value = this._writeDateInFormat();
            } else {
                this._options.dayField.value   = this._data.getDate();
                this._options.monthField.value = this._data.getMonth()+1;
                this._options.yearField.value  = this._data.getFullYear();
            }
            if(this._options.onSetDate) {
                this._options.onSetDate( this , { date : this._data } );
            }
        },

        /**
         * Makes the necessary work to update the calendar
         * when choosing a different month
         *
         * @method _updateCal
         * @param {Number} inc Indicates previous or next month
         * @private
         */
        _updateCal: function(inc){
            
            if( typeof this._options.onMonthSelected === 'function' ){
                this._options.onMonthSelected(this, {
                    'year': this._year,
                    'month' : this._month
                });
            }
            this._updateMonth(inc);
            this._showMonth();
        },

        /**
         * Function that returns the number of days on a given month on a given year
         *
         * @method _daysInMonth
         * @param {Number} _y - year
         * @param {Number} _m - month
         * @private
         * @return {Number} The number of days on a given month on a given year
         */
        _daysInMonth: function(_y,_m){
            var nDays = 31;

            switch (_m) {
                case 2:
                    nDays = ((_y % 400 === 0) || (_y % 4 === 0 && _y % 100 !== 0)) ? 29 : 28;
                    break;

                case 4:
                case 6:
                case 9:
                case 11:
                    nDays = 30;
                    break;
            }

            return nDays;
        },


        /**
         * Updates the calendar when a different month is chosen
         *
         * @method _updateMonth
         * @param {Number} incValue - indicates previous or next month
         * @private
         */
        _updateMonth: function(incValue){
            if(typeof incValue === 'undefined') {
                incValue = "0";
            }

            var mes = this._month + 1;
            var ano = this._year;
            switch(incValue){
                case -1:
                    if (mes===1){
                        if(ano === this._yearMin){ return; }
                        mes=12;
                        ano--;
                    }
                    else {
                        mes--;
                    }
                    this._year  = ano;
                    this._month = mes - 1;
                    break;
                case 1:
                    if(mes === 12){
                        if(ano === this._yearMax){ return; }
                        mes=1;
                        ano++;
                    }
                    else{
                        mes++;
                    }
                    this._year  = ano;
                    this._month = mes - 1;
                    break;
                default:

            }
        },

        /**
         * Key-value object that (for a given key) points to the correct parsing format for the DatePicker
         * @property _dateParsers
         * @type {Object}
         * @readOnly
         */
        _dateParsers: {
            'yyyy-mm-dd' : 'Y-m-d' ,
            'yyyy/mm/dd' : 'Y/m/d' ,
            'yy-mm-dd'   : 'y-m-d' ,
            'yy/mm/dd'   : 'y/m/d' ,
            'dd-mm-yyyy' : 'd-m-Y' ,
            'dd/mm/yyyy' : 'd/m/Y' ,
            'dd-mm-yy'   : 'd-m-y' ,
            'dd/mm/yy'   : 'd/m/y' ,
            'mm/dd/yyyy' : 'm/d/Y' ,
            'mm-dd-yyyy' : 'm-d-Y'
        },

        /**
         * Renders the current month
         *
         * @method _showMonth
         * @private
         */
        _showMonth: function(){
            /*jshint maxstatements:100, maxcomplexity:20 */
            var i, j;
            var mes = this._month + 1;
            var ano = this._year;
            var maxDay = this._daysInMonth(ano,mes);

            var wDayFirst = (new Date( ano , mes - 1 , 1 )).getDay();

            var startWeekDay = this._options.startWeekDay || 0;

            this._monthPrev.childNodes[0].className = 'change_month_prev';
            this._monthNext.childNodes[0].className = 'change_month_next';

            if ( ano < this._yearMin || ano === this._yearMin && mes <= this._monthMin ){
                this._monthPrev.childNodes[0].className = 'action_inactive';
            }
            else if( ano > this._yearMax || ano === this._yearMax && mes >= this._monthMax ){
                this._monthNext.childNodes[0].className = 'action_inactive';
            }

            if(startWeekDay && Number(startWeekDay)){
                if(startWeekDay > wDayFirst) {
                    wDayFirst = 7 + startWeekDay - wDayFirst;
                } else {
                    wDayFirst += startWeekDay;
                }
            }

            var html = '';

            html += '<ul class="sapo_cal_header">';

            for(i=0; i<7; i++){
                html+='<li>' + this._options.wDay[i + (((startWeekDay+i)>6) ? startWeekDay-7 : startWeekDay )].substring(0,1)  + '</li>';
            }
            html+='</ul>';

            var counter = 0;
            html+='<ul>';
            if(wDayFirst){
                for(j = startWeekDay; j < wDayFirst - startWeekDay; j++) {
                    if (!counter){
                        html+='<ul>';
                    }
                    html+='<li class="sapo_cal_empty">&nbsp;</li>';
                    counter++;
                }
            }

            for (i = 1; i <= maxDay; i++) {
                if (counter === 7){
                    counter=0;
                    html+='<ul>';
                }
                var idx = 'sapo_cal_' + ((String(i).length === 2) ? i : "0" + i);
                idx += ( ano === this._yearMin && mes === this._monthMin && i < this._dayMin ||
                    ano === this._yearMax && mes === this._monthMax && i > this._dayMax ||
                    ano === this._yearMin && mes < this._monthMin ||
                    ano === this._yearMax && mes > this._monthMax ||
                    ano < this._yearMin || ano > this._yearMax || ( this._options.validDayFn && !this._options.validDayFn.call( this, new Date( ano , mes - 1 , i) ) ) ) ? " sapo_cal_off" :
                    (this._data.getFullYear( ) === ano && this._data.getMonth( ) === mes - 1 && i === this._day) ? " sapo_cal_on" : "";
                html+='<li><a href="#" class="' + idx + '">' + i + '</a></li>';

                counter++;
                if(counter === 7){
                    html+='</ul>';
                }
            }
            if (counter !== 7){
                for(i = counter; i < 7; i++){
                    html+='<li class="sapo_cal_empty">&nbsp;</li>';
                }
                html+='</ul>';
            }
            html+='</ul>';


            this._monthContainer.innerHTML = html;

        },

        /**
         * This method sets the active month
         *
         * @method _setActiveMonth
         * @param {DOMElement} parent DOMElement where all the months are.
         * @private
         */
        _setActiveMonth: function(parent){
            if (typeof parent === 'undefined') {
                parent = this._monthSelector;
            }

            var length = parent.childNodes.length;

            if (parent.className && parent.className.match(/sapo_calmonth_/)) {
                var year = this._year;
                var month = parent.className.substr( 14 , 2 );

                if ( year === this._data.getFullYear( ) && month === this._data.getMonth( ) + 1 )
                {
                    Css.addClassName( parent , 'sapo_cal_on' );
                    Css.removeClassName( parent , 'sapo_cal_off' );
                }
                else
                {
                    Css.removeClassName( parent , 'sapo_cal_on' );
                    if ( year === this._yearMin && month < this._monthMin ||
                        year === this._yearMax && month > this._monthMax ||
                        year < this._yearMin ||
                        year > this._yearMax )
                    {
                        Css.addClassName( parent , 'sapo_cal_off' );
                    }
                    else
                    {
                        Css.removeClassName( parent , 'sapo_cal_off' );
                    }
                }
            }
            else if (length !== 0){
                for (var i = 0; i < length; i++) {
                    this._setActiveMonth(parent.childNodes[i]);
                }
            }
        },

        /**
         * Prototype's method to allow the 'i18n files' to change all objects' language at once.
         * @param  {Object} options Object with the texts' configuration.
         *     @param {String} closeText Text of the close anchor
         *     @param {String} cleanText Text of the clean text anchor
         *     @param {String} prevLinkText "Previous" link's text
         *     @param {String} nextLinkText "Next" link's text
         *     @param {String} ofText The text "of", present in 'May of 2013'
         *     @param {Object} month An object with keys from 1 to 12 that have the full months' names
         *     @param {Object} wDay An object with keys from 0 to 6 that have the full weekdays' names
         * @public
         */
        lang: function( options ){
            this._lang = options;
        },

        /**
         * This calls the rendering of the selected month.
         *
         * @method showMonth
         * @public
         */
        showMonth: function(){
            this._showMonth();
        },

        /**
         * Returns true if the calendar sceen is in 'select day' mode
         * 
         * @return {Boolean} True if the calendar sceen is in 'select day' mode
         * @public
         */
        isMonthRendered: function(){
            var header = Selector.select('.sapo_cal_header',this._containerObject)[0];

            return ( (Css.getStyle(header.parentNode,'display') !== 'none') && (Css.getStyle(header.parentNode.parentNode,'display') !== 'none') );
        }
    };

    return DatePicker;

});

/**
 * @module Ink.UI.Close_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Close', '1', ['Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Util.Array_1'], function(Event, Css, InkArray ) {
    'use strict';

    /**
     * Subscribes clicks on the document.body. If and only if you clicked on an element
     * having class "ink-close" or "ink-dismiss", will go up the DOM hierarchy looking for an element with any
     * of the following classes: "ink-alert", "ink-alert-block".
     * If it is found, it is removed from the DOM.
     * 
     * One should call close once per page (full page refresh).
     * 
     * @class Ink.UI.Close
     * @constructor
     * @version 1
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Util.Array
     * @example
     *     <script>
     *         Ink.requireModules(['Ink.UI.Close_1'],function( Close ){
     *             new Close();
     *         });
     *     </script>
     */
    var Close = function() {

        Event.observe(document.body, 'click', function(ev) {
            var el = Event.element(ev);
            if (!Css.hasClassName(el, 'ink-close') && !Css.hasClassName(el, 'ink-dismiss')) { return; }

            var classes;
            do { 
                if (!el.className) { continue; }
                classes = el.className.split(' ');
                if (!classes) { continue; }
                if ( InkArray.inArray('ink-alert',       classes) ||
                     InkArray.inArray('ink-alert-block', classes) ) { break; }
            } while ((el = el.parentNode));

            if (el) {
                Event.stop(ev);
                el.parentNode.removeChild(el);
            }
        });
    };

    return Close;

});

/**
00 * @module Ink.UI.Toggle_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Toggle', '1', ['Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1'], function(Aux, Event, Css, Element, Selector ) {
    'use strict';

    /**
     * Toggle component
     * 
     * @class Ink.UI.Toggle
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *     @param {String}       options.target                    CSS Selector that specifies the elements that will toggle
     *     @param {String}       [options.triggerEvent]            Event that will trigger the toggling. Default is 'click'
     *     @param {Boolean}      [options.closeOnClick]            Flag that determines if, when clicking outside of the toggled content, it should hide it. Default: true.
     * @example
     *      <div class="ink-dropdown">
     *          <button class="ink-button toggle" data-target="#dropdown">Dropdown <span class="icon-caret-down"></span></button>
     *          <ul id="dropdown" class="dropdown-menu">
     *              <li class="heading">Heading</li>
     *              <li class="separator-above"><a href="#">Option</a></li>
     *              <li><a href="#">Option</a></li>
     *              <li class="separator-above disabled"><a href="#">Disabled option</a></li>
     *              <li class="submenu">
     *                  <a href="#" class="toggle" data-target="#submenu1">A longer option name</a>
     *                  <ul id="submenu1" class="dropdown-menu">
     *                      <li class="submenu">
     *                          <a href="#" class="toggle" data-target="#ultrasubmenu">Sub option</a>
     *                          <ul id="ultrasubmenu" class="dropdown-menu">
     *                              <li><a href="#">Sub option</a></li>
     *                              <li><a href="#" data-target="ultrasubmenu">Sub option</a></li>
     *                              <li><a href="#">Sub option</a></li>
     *                          </ul>
     *                      </li>
     *                      <li><a href="#">Sub option</a></li>
     *                      <li><a href="#">Sub option</a></li>
     *                  </ul>
     *              </li>
     *              <li><a href="#">Option</a></li>
     *          </ul>
     *      </div>
     *      <script>
     *          Ink.requireModules( ['Ink.Dom.Selector_1','Ink.UI.Toggle_1'], function( Selector, Toggle ){
     *              var toggleElement = Ink.s('.toggle');
     *              var toggleObj = new Toggle( toggleElement );
     *          });
     *      </script>
     */
    var Toggle = function( selector, options ){

        if( typeof selector !== 'string' && typeof selector !== 'object' ){
            throw '[Ink.UI.Toggle] Invalid CSS selector to determine the root element';
        }

        if( typeof selector === 'string' ){
            this._rootElement = Selector.select( selector );
            if( this._rootElement.length <= 0 ){
                throw '[Ink.UI.Toggle] Root element not found';
            }

            this._rootElement = this._rootElement[0];
        } else {
            this._rootElement = selector;
        }

        this._options = Ink.extendObj({
            target : undefined,
            triggerEvent: 'click',
            closeOnClick: true
        },Element.data(this._rootElement));

        this._options = Ink.extendObj(this._options,options || {});

        if( typeof this._options.target === 'undefined' ){
            throw '[Ink.UI.Toggle] Target option not defined';
        }

        this._childElement = Aux.elOrSelector( this._options.target, 'Target' );
        // this._childElement = Selector.select( this._options.target, this._rootElement );
        // if( this._childElement.length <= 0 ){
        //     if( this._childElement.length <= 0 ){
        //         this._childElement = Selector.select( this._options.target, this._rootElement.parentNode );
        //     }

        //     if( this._childElement.length <= 0 ){
        //         this._childElement = Selector.select( this._options.target );
        //     }

        //     if( this._childElement.length <= 0 ){
        //         return;
        //     }
        // }
        // this._childElement = this._childElement[0];

        this._init();

    };

    Toggle.prototype = {

        /**
         * Init function called by the constructor
         * 
         * @method _init
         * @private
         */
        _init: function(){

            this._accordion = ( Css.hasClassName(this._rootElement.parentNode,'accordion') || Css.hasClassName(this._childElement.parentNode,'accordion') );

            Event.observe( this._rootElement, this._options.triggerEvent, Ink.bindEvent(this._onTriggerEvent,this) );
            if( this._options.closeOnClick.toString() === 'true' ){
                Event.observe( document, 'click', Ink.bindEvent(this._onClick,this));
            }
        },

        /**
         * Event handler. It's responsible for handling the <triggerEvent> defined in the options.
         * This will trigger the toggle.
         * 
         * @method _onTriggerEvent
         * @param {Event} event
         * @private
         */
        _onTriggerEvent: function( event ){

            if( this._accordion ){
                var elms, i, accordionElement;
                if( Css.hasClassName(this._childElement.parentNode,'accordion') ){
                    accordionElement = this._childElement.parentNode;
                } else {
                    accordionElement = this._childElement.parentNode.parentNode;
                }
                elms = Selector.select('.toggle',accordionElement);
                for( i=0; i<elms.length; i+=1 ){
                    var
                        dataset = Element.data( elms[i] ),
                        targetElm = Selector.select( dataset.target,accordionElement )
                    ;
                    if( (targetElm.length > 0) && (targetElm[0] !== this._childElement) ){
                            targetElm[0].style.display = 'none';
                    }
                }
            }

            var finalClass = ( Css.getStyle(this._childElement,'display') === 'none') ? 'show-all' : 'hide-all';
            var finalDisplay = ( Css.getStyle(this._childElement,'display') === 'none') ? 'block' : 'none';
            Css.removeClassName(this._childElement,'show-all');
            Css.removeClassName(this._childElement, 'hide-all');
            Css.addClassName(this._childElement, finalClass);
            this._childElement.style.display = finalDisplay;

            if( finalClass === 'show-all' ){
                Css.addClassName(this._rootElement,'active');
            } else {
                Css.removeClassName(this._rootElement,'active');
            }
        },

        /**
         * Click handler. Will handle clicks outside the toggle component.
         * 
         * @method _onClick
         * @param {Event} event
         * @private
         */
        _onClick: function( event ){
            var
                tgtEl = Event.element(event),
                shades
            ;

            if( (this._rootElement === tgtEl) || Element.isAncestorOf( this._rootElement, tgtEl ) || Element.isAncestorOf( this._childElement, tgtEl ) ){
                return;
            } else if( (shades = Ink.ss('.ink-shade')).length ) {
                var
                    shadesLength = shades.length
                ;

                for( var i = 0; i < shadesLength; i++ ){
                    if( Element.isAncestorOf(shades[i],tgtEl) && Element.isAncestorOf(shades[i],this._rootElement) ){
                        return;
                    }
                }
            }
            
            this._dismiss( this._rootElement );
        },

        /**
         * Dismisses the toggling.
         * 
         * @method _dismiss
         * @private
         */
        _dismiss: function( ){
            if( ( Css.getStyle(this._childElement,'display') === 'none') ){
                return;
            }
            Css.removeClassName(this._childElement, 'show-all');
            Css.removeClassName(this._rootElement,'active');
            Css.addClassName(this._childElement, 'hide-all');
            this._childElement.style.display = 'none';
        }
    };

    return Toggle;

});

/**
 * @module Ink.UI.Pagination_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Pagination', '1', ['Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1'], function(Aux, Event, Css, Element, Selector ) {
    'use strict';

    /**
     * Function to create the pagination anchors
     *
     * @method genAel
     * @param  {String} inner HTML to be placed inside the anchor.
     * @return {DOMElement}  Anchor created
     */
    var genAEl = function(inner) {
        var aEl = document.createElement('a');
        aEl.setAttribute('href', '#');
        aEl.innerHTML = inner;
        return aEl;
    };

    /**
     * @class Ink.UI.Pagination
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @param {String|DOMElement} selector
     * @param {Object} options Options
     * @param {Number}   options.size                number of pages
     * @param {Number}   [options.maxSize]           if passed, only shows at most maxSize items. displays also first|prev page and next page|last buttons
     * @param {Number}   [options.start]             start page. defaults to 1
     * @param {String}   [options.previousLabel]     label to display on previous page button
     * @param {String}   [options.nextLabel]         label to display on next page button
     * @param {String}   [options.previousPageLabel] label to display on previous page button
     * @param {String}   [options.nextPageLabel]     label to display on next page button
     * @param {String}   [options.firstLabel]        label to display on previous page button
     * @param {String}   [options.lastLabel]         label to display on next page button
     * @param {Function} [options.onChange]          optional callback
     * @param {Boolean}  [options.setHash]           if true, sets hashParameter on the location.hash. default is disabled
     * @param {String}   [options.hashParameter]     parameter to use on setHash. by default uses 'page'
     */
    var Pagination = function(selector, options) {

        this._options = Ink.extendObj({
            size:          undefined,
            start:         1,
            firstLabel:    'First',
            lastLabel:     'Last',
            previousLabel: 'Previous',
            nextLabel:     'Next',
            onChange:      undefined,
            setHash:       false,
            hashParameter: 'page'
        }, options || {});

        if (!this._options.previousPageLabel) {
            this._options.previousPageLabel = 'Previous ' + this._options.maxSize;
        }

        if (!this._options.nextPageLabel) {
            this._options.nextPageLabel = 'Next ' + this._options.maxSize;
        }


        this._handlers = {
            click: Ink.bindEvent(this._onClick,this)
        };

        this._element = Aux.elOrSelector(selector, '1st argument');

        if (!Aux.isInteger(this._options.size)) {
            throw new TypeError('size option is a required integer!');
        }

        if (!Aux.isInteger(this._options.start) && this._options.start > 0 && this._options.start <= this._options.size) {
            throw new TypeError('start option is a required integer between 1 and size!');
        }

        if (this._options.maxSize && !Aux.isInteger(this._options.maxSize) && this._options.maxSize > 0) {
            throw new TypeError('maxSize option is a positive integer!');
        }

        else if (this._options.size < 0) {
            throw new RangeError('size option must be equal or more than 0!');
        }

        if (this._options.onChange !== undefined && typeof this._options.onChange !== 'function') {
            throw new TypeError('onChange option must be a function!');
        }

        this._current = this._options.start - 1;
        this._itemLiEls = [];

        this._init();
    };

    Pagination.prototype = {

        /**
         * Init function called by the constructor
         * 
         * @method _init
         * @private
         */
        _init: function() {
            // generate and apply DOM
            this._generateMarkup(this._element);
            this._updateItems();

            // subscribe events
            this._observe();

            Aux.registerInstance(this, this._element, 'pagination');
        },

        /**
         * Responsible for setting listener in the 'click' event of the Pagination element.
         * 
         * @method _observe
         * @private
         */
        _observe: function() {
            Event.observe(this._element, 'click', this._handlers.click);
        },

        /**
         * Updates the markup everytime there's a change in the Pagination object.
         * 
         * @method _updateItems
         * @private
         */
        _updateItems: function() {
            var liEls = this._itemLiEls;

            var isSimpleToggle = this._options.size === liEls.length;

            var i, f, liEl;

            if (isSimpleToggle) {
                // just toggle active class
                for (i = 0, f = this._options.size; i < f; ++i) {
                    Css.setClassName(liEls[i], 'active', i === this._current);
                }
            }
            else {
                // remove old items
                for (i = liEls.length - 1; i >= 0; --i) {
                    this._ulEl.removeChild(liEls[i]);
                }

                // add new items
                liEls = [];
                for (i = 0, f = this._options.size; i < f; ++i) {
                    liEl = document.createElement('li');
                    liEl.appendChild( genAEl( i + 1 ) );
                    Css.setClassName(liEl, 'active', i === this._current);
                    this._ulEl.insertBefore(liEl, this._nextEl);
                    liEls.push(liEl);
                }
                this._itemLiEls = liEls;
            }

            if (this._options.maxSize) {
                // toggle visible items
                var page = Math.floor( this._current / this._options.maxSize );
                var pi = this._options.maxSize * page;
                var pf = pi + this._options.maxSize - 1;

                for (i = 0, f = this._options.size; i < f; ++i) {
                    liEl = liEls[i];
                    Css.setClassName(liEl, 'hide-all', i < pi || i > pf);
                }

                this._pageStart = pi;
                this._pageEnd = pf;
                this._page = page;

                Css.setClassName(this._prevPageEl, 'disabled', !this.hasPreviousPage());
                Css.setClassName(this._nextPageEl, 'disabled', !this.hasNextPage());

                Css.setClassName(this._firstEl, 'disabled', this.isFirst());
                Css.setClassName(this._lastEl, 'disabled', this.isLast());
            }

            // update prev and next
            Css.setClassName(this._prevEl, 'disabled', !this.hasPrevious());
            Css.setClassName(this._nextEl, 'disabled', !this.hasNext());
        },

        /**
         * Returns the top element for the gallery DOM representation
         * 
         * @method _generateMarkup
         * @param {DOMElement} el
         * @private
         */
        _generateMarkup: function(el) {
            Css.addClassName(el, 'ink-navigation');

            var
                ulEl,liEl,
                hasUlAlready = false
            ;
            if( ( ulEl = Selector.select('ul.pagination',el)).length < 1 ){
                ulEl = document.createElement('ul');
                Css.addClassName(ulEl, 'pagination');
            } else {
                hasUlAlready = true;
                ulEl = ulEl[0];
            }

            if (this._options.maxSize) {
                liEl = document.createElement('li');
                liEl.appendChild( genAEl(this._options.firstLabel) );
                this._firstEl = liEl;
                Css.addClassName(liEl, 'first');
                ulEl.appendChild(liEl);

                liEl = document.createElement('li');
                liEl.appendChild( genAEl(this._options.previousPageLabel) );
                this._prevPageEl = liEl;
                Css.addClassName(liEl, 'previousPage');
                ulEl.appendChild(liEl);
            }

            liEl = document.createElement('li');
            liEl.appendChild( genAEl(this._options.previousLabel) );
            this._prevEl = liEl;
            Css.addClassName(liEl, 'previous');
            ulEl.appendChild(liEl);

            liEl = document.createElement('li');
            liEl.appendChild( genAEl(this._options.nextLabel) );
            this._nextEl = liEl;
            Css.addClassName(liEl, 'next');
            ulEl.appendChild(liEl);

            if (this._options.maxSize) {
                liEl = document.createElement('li');
                liEl.appendChild( genAEl(this._options.nextPageLabel) );
                this._nextPageEl = liEl;
                Css.addClassName(liEl, 'nextPage');
                ulEl.appendChild(liEl);

                liEl = document.createElement('li');
                liEl.appendChild( genAEl(this._options.lastLabel) );
                this._lastEl = liEl;
                Css.addClassName(liEl, 'last');
                ulEl.appendChild(liEl);
            }

            if( !hasUlAlready ){
                el.appendChild(ulEl);
            }

            this._ulEl = ulEl;
        },

        /**
         * Click handler
         * 
         * @method _onClick
         * @param {Event} ev
         * @private
         */
        _onClick: function(ev) {
            Event.stop(ev);

            var tgtEl = Event.element(ev);
            if (tgtEl.nodeName.toLowerCase() !== 'a') {
                do{
                    tgtEl = tgtEl.parentNode;
                }while( (tgtEl.nodeName.toLowerCase() !== 'a') && (tgtEl !== this._element) );
                
                if( tgtEl === this._element){
                    return;
                }
            }

            var liEl = tgtEl.parentNode;
            if (liEl.nodeName.toLowerCase() !== 'li') { return; }

            if ( Css.hasClassName(liEl, 'active') ||
                 Css.hasClassName(liEl, 'disabled') ) { return; }

            var isPrev = Css.hasClassName(liEl, 'previous');
            var isNext = Css.hasClassName(liEl, 'next');
            var isPrevPage = Css.hasClassName(liEl, 'previousPage');
            var isNextPage = Css.hasClassName(liEl, 'nextPage');
            var isFirst = Css.hasClassName(liEl, 'first');
            var isLast = Css.hasClassName(liEl, 'last');

            if (isFirst) {
                this.setCurrent(0);
            }
            else if (isLast) {
                this.setCurrent(this._options.size - 1);
            }
            else if (isPrevPage || isNextPage) {
                this.setCurrent( (isPrevPage ? -1 : 1) * this._options.maxSize, true);
            }
            else if (isPrev || isNext) {
                this.setCurrent(isPrev ? -1 : 1, true);
            }
            else {
                var nr = parseInt( tgtEl.innerHTML, 10) - 1;
                this.setCurrent(nr);
            }
        },



        /**************
         * PUBLIC API *
         **************/

        /**
         * Sets the number of pages
         * 
         * @method setSize
         * @param {Number} sz number of pages
         * @public
         */
        setSize: function(sz) {
            if (!Aux.isInteger(sz)) {
                throw new TypeError('1st argument must be an integer number!');
            }

            this._options.size = sz;
            this._updateItems();
            this._current = 0;
        },

        /**
         * Sets the current page
         * 
         * @method setCurrent
         * @param {Number} nr sets the current page to given number
         * @param {Boolean} isRelative trueish to set relative change instead of absolute (default)
         * @public
         */
        setCurrent: function(nr, isRelative) {
            if (!Aux.isInteger(nr)) {
                throw new TypeError('1st argument must be an integer number!');
            }

            if (isRelative) {
                nr += this._current;
            }

            if (nr < 0) {
                nr = 0;
            }
            else if (nr > this._options.size - 1) {
                nr = this._options.size - 1;
            }
            this._current = nr;
            this._updateItems();

            /*if (this._options.setHash) {
                var o = {};
                o[this._options.hashParameter] = nr;
                Aux.setHash(o);
            }*/

            if (this._options.onChange) { this._options.onChange(this); }
        },

        /**
         * Returns the number of pages
         * 
         * @method getSize
         * @return {Number} Number of pages
         * @public
         */
        getSize: function() {
            return this._options.size;
        },

        /**
         * Returns current page
         * 
         * @method getCurrent
         * @return {Number} Current page
         * @public
         */
        getCurrent: function() {
            return this._current;
        },

        /**
         * Returns true iif at first page
         * 
         * @method isFirst
         * @return {Boolean} True if at first page
         * @public
         */
        isFirst: function() {
            return this._current === 0;
        },

        /**
         * Returns true iif at last page
         * 
         * @method isLast
         * @return {Boolean} True if at last page
         * @public
         */
        isLast: function() {
            return this._current === this._options.size - 1;
        },

        /**
         * Returns true iif has prior pages
         * 
         * @method hasPrevious
         * @return {Boolean} True if has prior pages
         * @public
         */
        hasPrevious: function() {
            return this._current > 0;
        },

        /**
         * Returns true iif has pages ahead
         * 
         * @method hasNext
         * @return {Boolean} True if has pages ahead
         * @public
         */
        hasNext: function() {
            return this._current < this._options.size - 1;
        },

        /**
         * Returns true iif has prior set of page(s)
         * 
         * @method hasPreviousPage
         * @return {Boolean} Returns true iif has prior set of page(s)
         * @public
         */
        hasPreviousPage: function() {
            return this._options.maxSize && this._current > this._options.maxSize - 1;
        },

        /**
         * Returns true iif has set of page(s) ahead
         * 
         * @method hasNextPage
         * @return {Boolean} Returns true iif has set of page(s) ahead
         * @public
         */
        hasNextPage: function() {
            return this._options.maxSize && this._options.size - this._current >= this._options.maxSize + 1;
        },

        /**
         * Unregisters the component and removes its markup from the DOM
         * 
         * @method destroy
         * @public
         */
        destroy: Aux.destroyComponent
    };

    return Pagination;

});
