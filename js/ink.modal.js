(function(undefined) {

    'use strict';


    SAPO.namespace('Ink');



    // aliases
    var Aux      = SAPO.Ink.Aux,
        Css      = SAPO.Dom.Css,
        Element  = SAPO.Dom.Element,
        Event    = SAPO.Dom.Event;



    /**
     * @class SAPO.Ink.Modal
     *
     * @since October 2012
     * @author jose.p.dias AT co.sapo.pt
     * @version 0.1
     *
     * <pre>
     * Displays a "window-like" container over the page and waits for dismiss.
     * Can grab its contents from the selector or the markup option.
     * By default the modal measures 600x400, but these dimensions can be overridden by options too.
     * Supports the escape key for dismissal too.
     * </pre>
     */

    /**
     * @constructor SAPO.Ink.Modal.?
     * @param {String|DOMElement} selector
     * @param {Object}            options
     * @... {optional Number}   width          modal width in pixels. defaults to 600
     * @... {optional Number}   height         modal height in pixels. defaults to 400
     * @... {optional Number}   markup         HTML markup string. if passed, populates the modal, otherwise the selector is used to fetch the content.
     * @... {optional Function} onShow         callback to call when the modal is shown
     * @... {optional Function} onDismiss      callback to call when the modal is dismissed
     * @... {optional Boolean}  closeOnClick   defaults to false. if trueish, a click anywhere dismissed the modal.
     */
    var Modal = function(selector, options) {

        if( (typeof selector !== 'string') && (typeof selector !== 'object') ){
            throw 'Invalid Modal selector';
        } else if(typeof selector === 'string'){
            if( selector !== '' ){
                this._element = SAPO.Dom.Selector.select(selector);
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
            click:   this._onClick.bindObjEvent(this),
            keyDown: this._onKeyDown.bindObjEvent(this),
            resize:  this._onResize.bindObjEvent(this)
        };

        this._wasDismissed = false;

        /**
         * Modal Markup
         */
        if( this._element ){
            this._markupMode = SAPO.Dom.Css.hasClassName(this._element,'ink-modal'); // Check if the full modal comes from the markup
        } else {
            this._markupMode = false;
        }

        /**
         * First, will handle the least important: The dataset
         */
        this._options = SAPO.extendObj(this._options,SAPO.Dom.Element.data(this._element));

        /**
         * Now, the most important, the initialization options
         */
        this._options = SAPO.extendObj(this._options,options || {});

        if( ("trigger" in this._options) && ( typeof this._options.trigger !== 'undefined' ) ){
            var triggerElement,i;
            if( typeof this._options.trigger === 'string' ){
                triggerElement = SAPO.Dom.Selector.select( this._options.trigger );
                if( triggerElement.length > 0 ){
                    for( i=0; i<triggerElement.length; i++ ){
                        SAPO.Dom.Event.observe( triggerElement[i], this._options.triggerEvent, this._init.bindObjEvent(this) );
                    }
                }
            }
        } else {
            this._init();
        }
    };

    Modal.prototype = {

        _init: function(event) {

            if( event ){ SAPO.Dom.Event.stop(event); }

            var elem = (document.compatMode === "CSS1Compat") ?  document.documentElement : document.body;

            this._resizeTimeout    = null;


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
                SAPO.Dom.Css.addClassName( this._modalShadow,'ink-shade' );
                SAPO.Dom.Css.addClassName( this._modalDiv,'ink-modal' );
                SAPO.Dom.Css.addClassName( this._modalDiv,'ink-space' );

                /**
                 * Applying the main css styles
                 */
                // this._modalDivStyle.position = 'absolute';


                this._contentContainer = document.createElement('div');
                this._contentContainer.className = 'ink-modal-content';
                this._contentContainer.innerHTML = [(this._options.skipClose ? '' : '<a href="#" class="ink-close">×</a>'), this._options.markup].join('');
                
                this._modalDiv.appendChild( this._contentContainer );
                this._modalShadow.appendChild( this._modalDiv);
                document.body.appendChild( this._modalShadow );
            } else {
                this._modalDiv         = this._element;
                this._modalDivStyle    = this._modalDiv.style;
                this._modalShadow      = this._modalDiv.parentNode;
                this._modalShadowStyle = this._modalShadow.style;

                this._contentContainer = SAPO.Dom.Selector.select(".modal-body",this._modalDiv);
                if( !this._contentContainer.length ){
                    throw 'Missing div with class "modal-body"';
                }

                this._contentContainer = this._contentContainer[0];
                this._options.markup = this._contentContainer.innerHTML;

            }

            SAPO.Dom.Css.addClassName( this._modalShadow,'ink-shade' );
            this._modalShadowStyle.display = this._modalDivStyle.display = 'block';
            setTimeout(function(){
                SAPO.Dom.Css.addClassName( this._modalShadow,'visible' );
                SAPO.Dom.Css.addClassName( this._modalDiv,'visible' );
            }.bindObj(this),100);

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
                this._modalDivStyle.maxWidth = SAPO.Dom.Element.elementWidth(this._modalDiv) + 'px';
            } else {
                this._modalDivStyle.maxWidth = this._modalDivStyle.width = SAPO.Dom.Element.elementWidth(this._modalDiv)+'px';
            }

            if( parseInt(elem.clientWidth,10) <= parseInt(this._modalDivStyle.width,10) ){
                this._modalDivStyle.width = (~~(parseInt(elem.clientWidth,10)*0.9))+'px';
            }

            if( typeof this._options.height !== 'undefined' ){
                this._modalDivStyle.height = this._options.height;
                this._modalDivStyle.maxHeight = SAPO.Dom.Element.elementHeight(this._modalDiv) + 'px';
            } else {
                this._modalDivStyle.maxHeight = this._modalDivStyle.height = SAPO.Dom.Element.elementHeight(this._modalDiv) + 'px';
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
         * _reposition: function responsible for repositioning the modal
         * @return void
         */
        _reposition: function(){

            this._modalDivStyle.top = this._modalDivStyle.left = '50%';

            this._modalDivStyle.marginTop = '-' + ( ~~( SAPO.Dom.Element.elementHeight(this._modalDiv)/2) ) + 'px';
            this._modalDivStyle.marginLeft = '-' + ( ~~( SAPO.Dom.Element.elementWidth(this._modalDiv)/2) ) + 'px';
        },

        /**
         * _resize: function responsible for resizing the modal
         * @return void
         */
        _onResize: function( runNow ){

            if( typeof runNow === 'boolean' ){
                this._timeoutResizeFunction.call(this);
            } else if( !this._resizeTimeout && (typeof runNow === 'object') ){
                this._resizeTimeout = setTimeout(this._timeoutResizeFunction.bindObj(this),250);
            }
        },

        /**
         * Timeout Resize Function
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

            if( ( currentViewportWidth > this.originalStatus.width ) /* && ( parseInt(this._modalDivStyle.maxWidth,10) >= SAPO.Dom.Element.elementWidth(this._modalDiv) )*/ ){
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

            if( (currentViewportHeight > this.originalStatus.height) && (parseInt(this._modalDivStyle.maxHeight,10) >= SAPO.Dom.Element.elementHeight(this._modalDiv) ) ){

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
         * @function ? navigation click handler
         * @param {Event} ev
         */
        _onClick: function(ev) {
            var tgtEl = Event.element(ev);

            if (Css.hasClassName(tgtEl, 'ink-close') || Css.hasClassName(tgtEl, 'ink-dismiss') ||
                 (this._options.closeOnClick &&
                  !Element.descendantOf(this._shadeElement, tgtEl)) ||
                 (tgtEl === this._shadeElement)) {
                Event.stop(ev);
                this.dismiss();
            }
        },

        /**
         * [_onKeyDown description]
         * @param  {[type]} ev [description]
         * @return {[type]}    [description]
         */
        _onKeyDown: function(ev) {
            if (ev.keyCode !== 27 || this._wasDismissed) { return; }
            this.dismiss();
        },

        _resizeContainer: function()
        {

            this._contentElement.style.overflow = this._contentElement.style.overflowX = this._contentElement.style.overflowY = 'hidden';
            var containerHeight = SAPO.Dom.Element.elementHeight(this._modalDiv);

            this._modalHeader = SAPO.Dom.Selector.select('.modal-header',this._modalDiv);
            if( this._modalHeader.length>0 ){
                this._modalHeader = this._modalHeader[0];
                containerHeight -= SAPO.Dom.Element.elementHeight(this._modalHeader);
            }

            this._modalFooter = SAPO.Dom.Selector.select('.modal-footer',this._modalDiv);
            if( this._modalFooter.length>0 ){
                this._modalFooter = this._modalFooter[0];
                containerHeight -= SAPO.Dom.Element.elementHeight(this._modalFooter);
            }

            this._contentContainer.style.height = containerHeight + 'px';
            if( containerHeight !== SAPO.Dom.Element.elementHeight(this._contentContainer) ){
                this._contentContainer.style.height = ~~(containerHeight - (SAPO.Dom.Element.elementHeight(this._contentContainer) - containerHeight)) + 'px';
            }

            if( this._markupMode ){ return; }

            this._contentContainer.style.overflow = this._contentContainer.style.overflowX = 'hidden';
            this._contentContainer.style.overflowY = 'auto';
            this._contentElement.style.overflow = this._contentElement.style.overflowX = this._contentElement.style.overflowY = 'visible';
        },

        _disableScroll: function()
        {
            this._oldScrollPos = SAPO.Dom.Element.scroll();
            this._onScrollBinded = function(event) {
                var tgtEl = SAPO.Dom.Event.element(event);

                if( !Element.descendantOf(this._modalShadow, tgtEl) ){
                    SAPO.Dom.Event.stop(event);
                    window.scrollTo(this._oldScrollPos[0], this._oldScrollPos[1]);
                }
            }.bindObjEvent(this);
            SAPO.Dom.Event.observe(window, 'scroll', this._onScrollBinded);
            SAPO.Dom.Event.observe(document, 'touchmove', this._onScrollBinded);
        },



        /**************
         * PUBLIC API *
         **************/

        /**
         * @function ? dismisses the modal
         */
        dismiss: function() {
            if (this._options.onDismiss) {
                this._options.onDismiss(this);
            }

            if(this._options.disableScroll) {
                SAPO.Dom.Event.stopObserving(window, 'scroll', this._onScrollBinded);
                SAPO.Dom.Event.stopObserving(document, 'touchmove', this._onScrollBinded);
            }

            if( this._options.responsive ){
                SAPO.Dom.Event.stopObserving(window, 'resize', this._handlers.resize);
            }

            // this._modalShadow.parentNode.removeChild(this._modalShadow);

            if( !this._markupMode ){
                this._modalShadow.parentNode.removeChild(this._modalShadow);
                this.destroy();
            } else {
                SAPO.Dom.Css.removeClassName( this._modalDiv, 'visible' );
                SAPO.Dom.Css.removeClassName( this._modalShadow, 'visible' );

                var
                    dismissInterval,
                    transitionEndFn = function(){
                        if( !dismissInterval ){ return; }
                        this._modalShadowStyle.display = 'none';
                        SAPO.Dom.Event.stopObserving(document,'transitionend',transitionEndFn);
                        SAPO.Dom.Event.stopObserving(document,'oTransitionEnd',transitionEndFn);
                        SAPO.Dom.Event.stopObserving(document,'webkitTransitionEnd',transitionEndFn);
                        clearInterval(dismissInterval);
                        dismissInterval = undefined;
                    }.bindObjEvent(this)
                ;

                SAPO.Dom.Event.observe(document,'transitionend',transitionEndFn);
                SAPO.Dom.Event.observe(document,'oTransitionEnd',transitionEndFn);
                SAPO.Dom.Event.observe(document,'webkitTransitionEnd',transitionEndFn);

                if( !dismissInterval ){
                    dismissInterval = setInterval(function(){
                        if( this._modalShadowStyle.opacity > 0 ){
                            return;
                        } else {
                            this._modalShadowStyle.display = 'none';
                            clearInterval(dismissInterval);
                            dismissInterval = undefined;
                        }

                    }.bindObj(this),500);
                }
            }
        },

        /**
         * @function ? removes the modal from the DOM
         */
        destroy: function() {
            Aux.unregisterInstance(this._instanceId);
        },

        /**
         * @function {DOMElement} ? returns the content DOM element
         */
        getContentElement: function() {
            return this._contentContainer;
        },

        /**
         * @function ? replaces the content markup
         * @param {String} contentMarkup
         */
        setContentMarkup: function(contentMarkup) {
            if( this._markupMode ){
                this._contentContainer.innerHTML = [contentMarkup].join('');
            } else {
                this._modalDiv.innerHTML = '';
                this._modalDiv.appendChild(this._contentContainer);
                this._contentContainer.innerHTML = [this._options.skipClose ? '' : '<a href="#" class="ink-close">×</a>', contentMarkup].join('');
            }
            this._resizeContainer();
        }

    };

    SAPO.Ink.Modal = Modal;

})();
