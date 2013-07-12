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
