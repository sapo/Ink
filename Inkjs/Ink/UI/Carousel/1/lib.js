/**
 * @module Ink.UI.Carousel_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Carousel', '1',
    ['Ink.UI.Aux_1', 'Ink.Dom.Event_1', 'Ink.Dom.Css_1', 'Ink.Dom.Element_1', 'Ink.UI.Pagination_1', 'Ink.Dom.Selector_1'],
    function(Aux, InkEvent, Css, InkElement, Pagination/*, Selector*/) {
    'use strict';



    /*
     * TODO:
     *  keyboardSupport
     *  swipe
     */
    
    /**
     * @class Ink.UI.Carousel_1
     * @constructor
     *
     * @param {String|DOMElement} selector
     * @param {Object} [options]
     *  @param {String} [options.axis='x'] Can be `'x'` or `'y'`, for a horizontal or vertical carousel
     *  @param {Boolean} [options.center=false] Center the carousel.
     *  @TODO @param {Boolean} [options.keyboardSupport=false] Enable keyboard support
     *  @param {String|DOMElement|Ink.UI.Pagination_1} [options.pagination] Either an `<ul>` element to add pagination markup to, or an `Ink.UI.Pagination` instance to use.
     *  @param {Function} [options.onChange] Callback for when the page is changed.
     */
    var Carousel = function(selector, options) {
        this._handlers = {
            paginationChange: Ink.bind(this._onPaginationChange, this),
            windowResize:     Ink.bind(this._onWindowResize,     this)
        };

        InkEvent.observe(window, 'resize', this._handlers.windowResize);

        this._element = Aux.elOrSelector(selector, '1st argument');

        this._options = Ink.extendObj({
            axis:            'x',
            center:          false,
            keyboardSupport: false,
            pagination:      null,
            onChange:        null
        }, options || {}, InkElement.data(this._element));

        this._isY = (this._options.axis === 'y');

        var rEl = this._element;

        var ulEl = Ink.s('ul.stage', rEl);
        this._ulEl = ulEl;

        InkElement.removeTextNodeChildren(ulEl);

        var liEls = Ink.ss('li.slide', ulEl);
        this._liEls = liEls;



        // hider
        // TODO check if this is really needed

        // var hiderEl = document.createElement('div');
        // hiderEl.className = 'hider';
        // this._element.appendChild(hiderEl);
        // hiderEl.style[ this._isY ? 'width' : 'height' ] = '100%';
        // this._hiderEl = hiderEl;

        this.remeasure();

        if (this._options.center) {
            this._center();
        }
        else {
            this._justUpdateHider();
        }

        if (this._isY) {
            this._ulEl.style.whiteSpace = 'normal';
        }

        if (this._options.pagination) {
            if (Aux.isDOMElement(this._options.pagination) || typeof this._options.pagination === 'string') {
                // if dom element or css selector string...
                this._pagination = new Pagination(this._options.pagination, {
                    size:     this._numPages,
                    onChange: this._handlers.paginationChange
                });
            } else {
                // assumes instantiated pagination
                this._pagination = this._options.pagination;
                this._pagination._options.onChange = this._handlers.paginationChange;
                this._pagination.setSize(this._numPages);
                this._pagination.setCurrent(0);
            }
        }
    };

    Carousel.prototype = {
        /**
         * Measure the carousel once again, adjusting the involved elements'
         * sizes. Called automatically when the window resizes, in order to
         * cater for changes from responsive media queries.
         *
         * @method remeasure
         */
        remeasure: function() {
            var off = 'offset' + (this._options.axis === 'y' ? 'Height' : 'Width');
            var numItems = this._liEls.length;
            this._ctnLength = this._element[off];
            this._elLength = this._liEls[0][off];
            this._itemsPerPage = Math.floor( this._ctnLength / this._elLength  );
            this._numPages = Math.ceil( numItems / this._itemsPerPage );
            this._deltaLength = this._itemsPerPage * this._elLength;
            
            if (this._isY) {
                this._element.style.width = this._liEls[0].offsetWidth + 'px';
                this._ulEl.style.width  =  this._liEls[0].offsetWidth + 'px';
            }
            else {
                this._ulEl.style.height =  this._liEls[0].offsetHeight + 'px';
            }
        },

        _center: function() {
            var gap = Math.floor( (this._ctnLength - (this._elLength * this._itemsPerPage) ) / 2 );

            var pad;
            if (this._isY) {
                pad = [gap, 'px 0'];
            }
            else {
                pad = ['0 ', gap, 'px'];
            }
            this._ulEl.style.padding = pad.join('');

            // this._hiderEl.style[ this._isY ? 'height' : 'width' ] = gap + 'px';
        },

        _justUpdateHider: function() {
            var gap = Math.floor( this._ctnLength - (this._elLength * this._itemsPerPage) );
            // this._hiderEl.style[ this._isY ? 'height' : 'width' ] = gap + 'px';
        },

        _onPaginationChange: function(pgn) {
            var currPage = pgn.getCurrent();
            this._ulEl.style[ this._options.axis === 'y' ? 'top' : 'left'] = ['-', currPage * this._deltaLength, 'px'].join('');
            if (this._options.onChange) {
                this._options.onChange.call(this, currPage);
            }
        },

        _onWindowResize: function() {
            this.remeasure();

            if (this._pagination) {
                this._pagination.setSize(this._numPages);
                this._pagination.setCurrent(0);
            }

            if (this._options.center) {
                this._center();
            }
            else {
                this._justUpdateHider();
            }
        }
    };



    return Carousel;

});
