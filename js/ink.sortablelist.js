(function(window, undefined) {

    'use strict';


    SAPO.namespace('Ink');



    // aliases
    var Aux      = SAPO.Ink.Aux,
        Css      = SAPO.Dom.Css,
        Element  = SAPO.Dom.Element,
        Event    = SAPO.Dom.Event,
        Selector = SAPO.Dom.Selector;



    /**
     * @class SAPO.Ink.SortableList
     *
     * @since October 2012
     * @author jose.p.dias AT co.sapo.pt
     * @version 0.1
     *
     * Adds sortable behaviour to any list!
     */

    /**
     * @constructor SAPO.Ink.SortableList.?
     * @param {String|DOMElement} selector
     * @param {Object}        options
     * @... {optional String} dragLabel what to display on the label. defaults to 'drag here'
     */
    var SortableList = function(selector, options) {

        this._element = Aux.elOrSelector(selector, '1st argument');

        if( !SAPO.Ink.Aux.isDOMElement(selector) && (typeof selector !== 'string') ){
            throw '[SAPO.Ink.SortableList] :: Invalid selector';
        } else if( typeof selector === 'string' ){
            this._element = SAPO.Dom.Selector.select( selector );

            if( this._element.length < 1 ){
                throw '[SAPO.Ink.SortableList] :: Selector has returned no elements';
            }
            this._element = this._element[0];

        } else {
            this._element = selector;
        }

        this._options = SAPO.extendObj({
            dragLabel: 'drag here'
        }, SAPO.Dom.Element.data(this._element));

        this._options = SAPO.extendObj( this._options, options || {});

        this._handlers = {
            down: this._onDown.bindObjEvent(this),
            move: this._onMove.bindObjEvent(this),
            up:   this._onUp.bindObjEvent(this)
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

        this._init();
    };

    SortableList.prototype = {

        _init: function() {
            // extract model
            if (this._createdFrom === 'DOM') {
                this._extractModelFromDOM();
                this._createdFrom = 'JSON';
            }

            // // generate and apply DOM
            // var el = this._generateMarkup();
            // var parentEl = this._element.parentNode;

            // if (!this._notFirstInit) {
            //     Aux.storeIdAndClasses(this._element, this);
            //     this._notFirstInit = true;
            // }

            // parentEl.insertBefore(el, this._element);
            // parentEl.removeChild(this._element);
            // this._element = el;

            // Aux.restoreIdAndClasses(this._element, this);

            var isTouch = 'ontouchstart' in document.documentElement;
            //var isTouch = true;

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

        _observe: function() {
            Event.observe(this._element, this._down, this._handlers.down);
        },

        /**
         * @function ? updates the model from the UL representation
         */
        _extractModelFromDOM: function() {
            this._model = [];
            var that = this;

            var liEls = Selector.select('> li', this._element);

            liEls.forEach(function(liEl) {
                //var t = Element.getChildrenText(liEl);
                var t = liEl.innerHTML;
                that._model.push(t);
            });
        },

        /**
         * @function {DOMElement} ? returns the top element for the gallery DOM representation
         */
        _generateMarkup: function() {
            var el = document.createElement('ul');
            el.className = 'unstyled ink-sortable-list';
            var that = this;

            this._model.forEach(function(label, idx) {
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
         * @function {Number} ? extracts the Y coordinate of the mouse from the given MouseEvent
         * @param  {Event} ev
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

        _refresh: function(skipObs) {
            var el = this._generateMarkup();
            this._element.parentNode.replaceChild(el, this._element);
            this._element = el;

            Aux.restoreIdAndClasses(this._element, this);

            // subscribe events
            if (!skipObs) { this._observe(); }
        },

        /**
         * @function ? mouse down handler
         * @param {Event} ev
         */
        _onDown: function(ev) {
            var tgtEl = Event.element(ev);

            if (tgtEl.nodeName.toLowerCase() === 'i') {
                tgtEl = tgtEl.parentNode;
            }
            if ((tgtEl.nodeName.toLowerCase() !== 'li' && ( tgtEl.nodeName.toLowerCase() !== 'span' || !Css.hasClassName(tgtEl, 'ink-label')) ) ) { return; }
            Event.stop(ev);

            var liEl;
            if( tgtEl.nodeName.toLowerCase() !== 'li' ){
                liEl = tgtEl.parentNode;
            } else {
                liEl = tgtEl;
            }
            this._index = Aux.childIndex(liEl);
            this._height = liEl.offsetHeight;
            this._startY = this._getY(ev);
            this._isMoving = true;

            document.body.style.cursor = 'move';

            this._refresh(false);

            return false;
        },

        /**
         * @function ? mouse move handler
         * @param {Event} ev
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
         * @function ? mouse up handler
         * @param {Event} ev
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
         * @function {String[]} ? returns a copy of the model
         */
        getModel: function() {
            return this._model.slice();
        },

        /**
         * @function ? unregisters the component and removes its markup from the DOM
         */
        destroy: Aux.destroyComponent

    };

    SAPO.Ink.SortableList = SortableList;

})(window);
