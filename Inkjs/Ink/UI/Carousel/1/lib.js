/**
 * @module Ink.UI.Carousel_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Carousel', '1',
    ['Ink.UI.Aux_1', 'Ink.Dom.Event_1', 'Ink.Dom.Css_1', 'Ink.Dom.Element_1', 'Ink.UI.Pagination_1', 'Ink.Dom.Browser_1', 'Ink.Dom.Selector_1'],
    function(Aux, InkEvent, Css, InkElement, Pagination, Browser/*, Selector*/) {
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
            windowResize:     Ink.bind(this.refit, this)
        };

        InkEvent.observe(window, 'resize', this._handlers.windowResize);

        this._element = Aux.elOrSelector(selector, '1st argument');

        this._options = Ink.extendObj({
            axis:           'x',
            hideLast:       false,
            center:         false,
            keyboardSupport:false,
            pagination:     null,
            onChange:       null
        }, options || {}, InkElement.data(this._element));

        this._isY = (this._options.axis === 'y');

        var rEl = this._element;

        var ulEl = Ink.s('ul.stage', rEl);
        this._ulEl = ulEl;

        InkElement.removeTextNodeChildren(ulEl);



        if (this._options.hideLast) {
            var hiderEl = document.createElement('div');
            hiderEl.className = 'hider';
            this._element.appendChild(hiderEl);
            hiderEl.style.position = 'absolute';
            hiderEl.style[ this._isY ? 'left' : 'top' ] = '0';  // fix to top..
            hiderEl.style[ this._isY ? 'right' : 'bottom' ] = '0';  // and bottom...
            hiderEl.style[ this._isY ? 'bottom' : 'right' ] = '0';  // and move to the end.
            this._hiderEl = hiderEl;
        }

        this.refit();

        if (this._isY) {
            // Override white-space: no-wrap which is only necessary to make sure horizontal stuff stays horizontal, but breaks stuff intended to be vertical.
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
         * cater for changes from responsive media queries, for instance.
         *
         * @method refit
         */
        refit: function() {
            this._liEls = Ink.ss('li.slide', this._ulEl);
            var numItems = this._liEls.length;
            this._ctnLength = this._size(this._element);
            this._elLength = this._size(this._liEls[0]);
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

            this._center();
            this._updateHider();
            
            if (this._pagination) {
                this._pagination.setSize(this._numPages);
                this._pagination.setCurrent(0);
            }
        },

        _size: function (elm) {
            try {
                var rect = elm.getBoundingClientRect();
                return this._isY ?
                    rect.bottom - rect.top :
                    rect.right - rect.left;
            } catch(e) {
                return elm[this._isY ? 'offsetHeight' : 'offsetWidth'];
            }
        },

        _center: function() {
            if (!this._options.center) { return; }
            var gap = Math.floor( (this._ctnLength - (this._elLength * this._itemsPerPage) ) / 2 );

            var pad;
            if (this._isY) {
                pad = [gap, 'px 0'];
            }
            else {
                pad = ['0 ', gap, 'px'];
            }
            this._ulEl.style.padding = pad.join('');
        },

        _updateHider: function() {
            if (!this._hiderEl) { return; }
            var gap = Math.floor( this._ctnLength - (this._elLength * this._itemsPerPage) );
            if (this._options.center) {
                gap /= 2;
            }
            this._hiderEl.style[ this._isY ? 'height' : 'width' ] = gap + 'px';
        },

        _onPaginationChange: function(pgn) {
            var currPage = pgn.getCurrent();
            this._ulEl.style[ this._options.axis === 'y' ? 'top' : 'left'] = ['-', currPage * this._deltaLength, 'px'].join('');
            if (this._options.onChange) {
                this._options.onChange.call(this, currPage);
            }
        },
    };



    return Carousel;

});
