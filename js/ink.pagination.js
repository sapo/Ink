(function(undefined) {

    'use strict';



    SAPO.namespace('Ink');



    // aliases
    var Aux      = SAPO.Ink.Aux,
        Css      = SAPO.Dom.Css,
        Selector = SAPO.Dom.Selector,
        Event    = SAPO.Dom.Event;



    var genAEl = function(inner) {
        var aEl = document.createElement('a');
        aEl.setAttribute('href', '#');
        aEl.innerHTML = inner;
        return aEl;
    };



    /**
     * @class SAPO.Ink.Pagination
     *
     * @since October 2012
     * @author jose.p.dias AT co.sapo.pt
     * @version 0.1
     *
     * Generic pagination component.
     */

    /**
     * @constructor SAPO.Ink.Pagination.?
     * @param {String|DOMElement} selector
     * @param {Object}            options
     * @... {Number}            size              number of pages
     * @... {optional Number}   maxSize           if passed, only shows at most maxSize items. displays also first|prev page and next page|last buttons
     * @... {optional Number}   start             start page. defaults to 1
     * @... {optional String}   previousLabel     label to display on previous page button
     * @... {optional String}   nextLabel         label to display on next page button
     * @... {optional String}   previousPageLabel label to display on previous page button
     * @... {optional String}   nextPageLabel     label to display on next page button
     * @... {optional String}   firstLabel        label to display on previous page button
     * @... {optional String}   lastLabel         label to display on next page button
     * @... {optional Function} onChange          optional callback
     * @... {optional Boolean}  setHash           if true, sets hashParameter on the location.hash. default is disabled
     * @... {optional String}   hashParameter     parameter to use on setHash. by default uses 'page'
     */
    var Pagination = function(selector, options) {

        this._options = SAPO.extendObj({
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
            click: this._onClick.bindObjEvent(this)
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

        _init: function() {
            // generate and apply DOM
            this._generateMarkup(this._element);
            this._updateItems();

            // subscribe events
            this._observe();

            Aux.registerInstance(this, this._element, 'pagination');
        },

        _observe: function() {
            Event.observe(this._element, 'click', this._handlers.click);
        },

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
         * @function {DOMElement} ? returns the top element for the gallery DOM representation
         * @param {DOMElement} el
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
         * @function ? click handler
         * @param {Event} ev
         */
        _onClick: function(ev) {
            Event.stop(ev);

            var tgtEl = Event.element(ev);
            if (tgtEl.nodeName.toLowerCase() !== 'a') { return; }

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
         * @function ? sets the number of pages
         * @param {Number} sz number of pages
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
         * @function ? sets the current page
         * @param {Number} nr sets the current page to given number
         * @param {Boolean} isRelative trueish to set relative change instead of absolute (default)
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
         * @function {Number} ? returns the number of pages
         */
        getSize: function() {
            return this._options.size;
        },

        /**
         * @function {Number} ? returns current page
         */
        getCurrent: function() {
            return this._current;
        },

        /**
         * @function {Boolean} ? returns true iif at first page
         */
        isFirst: function() {
            return this._current === 0;
        },

        /**
         * @function {Boolean} ? returns true iif at last page
         */
        isLast: function() {
            return this._current === this._options.size - 1;
        },

        /**
         * @function {Boolean} ? returns true iif has prior page(s)
         */
        hasPrevious: function() {
            return this._current > 0;
        },

        /**
         * @function {Boolean} ? returns true iif has page(s) ahead
         */
        hasNext: function() {
            return this._current < this._options.size - 1;
        },

        /**
         * @function {Boolean} ? returns true iif has prior set of page(s)
         */
        hasPreviousPage: function() {
            return this._options.maxSize && this._current > this._options.maxSize - 1;
        },

        /**
         * @function {Boolean} ? returns true iif has set of page(s) ahead
         */
        hasNextPage: function() {
            return this._options.maxSize && this._options.size - this._current >= this._options.maxSize + 1;
        },

        /**
         * @function ? unregisters the component and removes its markup from the DOM
         */
        destroy: Aux.destroyComponent
    };

    SAPO.Ink.Pagination = Pagination;

})();
