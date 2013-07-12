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
