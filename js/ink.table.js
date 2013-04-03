(function(undefined) {

    'use strict';



    SAPO.namespace('Ink');



    // aliases
    var Aux      = SAPO.Ink.Aux,
        Css      = SAPO.Dom.Css,
        Element  = SAPO.Dom.Element,
        Event    = SAPO.Dom.Event,
        Selector = SAPO.Dom.Selector,
        Arr      = SAPO.Utility.Array;



    /**
     * @class SAPO.Ink.Table
     *
     * @since October 2012
     * @author jose.p.dias AT co.sapo.pt
     * @version 0.1
     *
     * <pre>
     * Shows data as table.
     * Supports sorting and pagination either on the
     * client-side (if model is supplied) or server-side (if endpoint responding according to the "spec" is supplied).
     * Pagination is optional (pass on the pageSize option).
     * It passed, a Pagination component is instanced on the pagionation option or immediately after the table (if pagination is ommitted).
     * The columns displayed and their order are defined in the fields option. Your object can have more than these fields, they simply won't appear.
     * Fieldnames allows one to define custom column names.
     * SortableFields allows one to choose which columns can be sortable. By default none is. Use '*' to allow all, otherwise pass an array of fields.
     * Formatters allows custom display of cells. At the function call only the table cell is set, no content or classes.
     * There are callback for
     * </pre>
     *
     * references:
     *   http://workshop.andr3.net/datatables/
     *   http://tantek.com/presentations/2012/09/microformats2/
     *   http://www.w3.org/TR/WCAG20-TECHS/H43
     */

    /**
     * @constructor SAPO.Ink.Table.?
     * @param {String|DOMElement} selector
     * @param {Object}            options
     * @... {optional Object[]}         model          the model to use, for client-side data. either this option or model _must be provided_
     * @... {optional String}           endpoint       an URI to use to get list and count information, for server-side data. either this option or model _must be provided_
     * @... {String[]}                  fields         fields to display on the table
     * @... {optional Object}           fieldNames     column names to display on the table. if ommitted
     * @... {optional Object}           formatters     optional hash of field -> formatter fn. formatter receives (fieldValue, item, tdEl)
     * @... {optional String[]|String}  sortableFields by default no columns support sorting. if you want all columns to be sortable pass '*', otherwise pass an array of fields
     * @... {optional Number}           pageSize       if defined, pagination is applied
     * @... {optional Function(SAPO.Ink.Table, Object)} onHeaderClick callback that gets called when the user clicks on a column header. Relevant data is passed on the object o.
     * @... {optional Function(SAPO.Ink.Table, Object)} onCellClick   callback that gets called when the user clicks on a cell. Relevant data is passed on the object o.
     */
    var Table = function(selector, options) {
        /*jshint maxstatements:50, maxcomplexity:20 */

        this._handlers = {
            headerclick: this._onHeaderClick.bindObjEvent(this),
            cellclick:   this._onCellClick.bindObjEvent(this),
            updatecount: this._onUpdateCount.bindObj(this)
        };

        this._element = Aux.elOrSelector(selector, '1st argument');

        this._options = SAPO.extendObj({
            model:          undefined,
            endpoint:       undefined,
            fields:         undefined,
            fieldNames:     {},
            formatters:     {},
            sortableFields: [],
            pageSize:       undefined,
            pagination:     undefined,
            onCellClick:    undefined,
            onHeaderClick:  undefined
        }, Element.data(this._element));

        this._options = SAPO.extendObj(this._options, options || {});

        if (this._options.model && this._options.endpoint) {
            throw new TypeError('This component requires only _one_ of the following options: model, endpoint or be given a table element.');
        }
        else if (!this._options.model && !this._options.endpoint) {
            if (this._element.nodeName.toLowerCase() !== 'table') {
                throw new TypeError('This component requires one of the following options: model, endpoint or the selector pointing to a table element with datatable format.');
            }
            else {
                this._extractModelFromDOM();
            }
        }

        if (this._options.model) {
            if (this._options.model instanceof Array) {
                this._model = this._options.model;
            }
            else {
                throw new TypeError('model option must be passed as an array of objects!');
            }
        }
        else if (this._options.endpoint) {
            if (typeof this._options.endpoint !== 'string') {
                throw new TypeError('endpoint option should be a server URI!');
            }
        }

        if (this._options.formatters) {
            if (typeof this._options.formatters !== 'object') {
                throw new TypeError('formatters option expected an object of field -> function(fieldValue, item, tdEl)!');
            }
        }

        if (!(this._options.fields instanceof Array)) {
            throw new TypeError('fields option expects an array of strings!');
        }

        // TODO
        /*if ( this._options.sortableFields !== undefined &&
             ( (typeof this._options.sortableFields === 'string' && this._options.sortableFields !== '*') ||
               !(this._options.sortableFields instanceof Array) ) ) {
            //console.log(typeof this._options.sortableFields, this._options.sortableFields, this._options.sortableFields === '*');
            throw new TypeError('sortableFields option expects an array of strings or the string "*"!');
        }*/

        if (this._options.onCellClick && typeof this._options.onCellClick !== 'function') {
            throw new TypeError('onCellClick options expects a function!');
        }

        if (this._options.onHeaderClick && typeof this._options.onHeaderClick !== 'function') {
            throw new TypeError('onHeaderClick options expects a function!');
        }

        this._orderBy  = undefined;
        this._orderDir = 1;

        if((typeof this._options.sortableFields === 'string') && (this._options.sortableFields !== '*')){
            this._options.sortableFields = this._options.sortableFields.split(',');
        }

        if (this._options.pageSize) {
            if( !isNaN(this._options.pageSize) ){
                this._options.pageSize = parseInt(this._options.pageSize,10);
            }

            if (!Aux.isInteger(this._options.pageSize)) {
                throw new TypeError('pageSize option must be an integer number!');
            }

            var pagEl;
            if (this._options.pagination) {
                pagEl = Aux.elOrSelector(this._options.pagination, 'pagination option');
            }
            else {
                pagEl = document.createElement('nav');
                Element.insertAfter(pagEl, this._element);
            }
            this._pagEl = pagEl;

            if (this._options.model) { this._onUpdateCount(null, this._options.model.length);                          }
            else {                     Aux.ajaxJSON(this._options.endpoint, {op:'count'}, this._handlers.updatecount); }
        }
        else {
            this._init();
            this._handlers.updatecount();
        }
    };

    Table.prototype = {

        _init: function() {
            // generate and apply DOM
            this._generateMarkup(this._element);

            // subscribe events
            this._observe();

            Aux.registerInstance(this, this._element, 'table');
        },

        _extractModelFromDOM: function() {
            /*global console:false */
            var thEls = Selector.select('> thead > tr > th', this._element);
            var trEls = Selector.select('> tbody > tr', this._element);

            this._options.fields = [];
            this._options.fieldNames = {};
            this._options.model = [];

            var name, label, o, tdEls, that = this;
            thEls.forEach(function(thEl) {
                try {
                    label = thEl.innerHTML; // is innerText supported?

                    if (label.match(/^\-?\d+\.?\d*$/)) {
                        label = parseFloat(label);
                    }

                    name = thEl.getAttribute('id');
                    if (name) {
                        if (name.indexOf('th_') === 0) {
                            name = name.substring(3);
                        }
                    }
                    else {
                        name = label.toLowerCase().replace(/ /g, '_');
                    }

                    that._options.fields.push(name);
                    that._options.fieldNames[name] = label;
                } catch (ex) {
                    console.error('problematic element:');
                    console.error(thEl);
                    throw new Error('Problem parsing table data from DOM!');
                }
            });

            trEls.forEach(function(trEl) {
                tdEls = Selector.select('> td', trEl);
                o = {};
                tdEls.forEach(function(tdEl, idx) {
                    try {
                        name = tdEl.getAttribute('headers');    // TODO WHAT TO DO IF OMMITTED
                        if (name) {
                            if (name.indexOf('th_') === 0) {
                                name = name.substring(3);
                            }
                        }
                        else {
                            name = that._options.fields[idx];
                        }

                        label = tdEl.innerHTML; // is innerText supported?
                        o[name] = label;
                    } catch (ex) {
                        console.error('problematic element:');
                        console.error(tdEl);
                        throw new Error('Problem parsing table data from DOM!');
                    }
                });
                that._options.model.push(o);
            });

            //console.log(this._options);
            //throw new Error();
        },

        _observe: function() {
            Event.observe(this._theadEl, 'click', this._handlers.headerclick);
            Event.observe(this._tbodyEl, 'click', this._handlers.cellclick);
        },

        _whichRow: function(el) {
            while (el.nodeName.toLowerCase() !== 'tr') { el = el.parentNode; }
            if (this._pagination) {
                return Aux.childIndex(el);
            }
        },

        _whichColumn: function(el) {
            return Aux.childIndex(el);
        },

        _remoteQuery: function(cb) {
            Aux.ajaxJSON(this._options.endpoint, {op:'count'}, function(err, data1) {
                if (err) { return cb(err); }


                // length
                var count = data1.count;
                if (this._pagination && count !== this._modelLength) {
                    var sz = Math.ceil( count / this._options.pageSize);
                    if (this._pagination.getCurrent() > sz - 1) {
                        console.log('setCurrent', sz - 1);
                        return this._pagination.setCurrent(sz - 1);
                    }
                    this._onUpdateCount(null, count);
                }



                var params =  {
                    op:       'list',
                    orderDir: this._orderDir
                };

                if (this._options.pageSize) {
                    params.pageSz = this._options.pageSize;
                    params.pageNr = this._pagination.getCurrent();
                }

                if (this._orderBy) { params.orderBy = this._orderBy; }

                Aux.ajaxJSON(
                    this._options.endpoint,
                    params,
                    function(err, data2) {
                        if (err) { return cb(err); }
                        cb(null, data2.items);
                    }.bindObj(this)
                );
            }.bindObj(this));
        },

        _localQuery: function(cb) {
            var items = this._model.slice();


            // length
            var sz;
            var count = items.length;
            if (this._pagination && count !== this._modelLength) {
                sz = Math.ceil( count / this._options.pageSize);
                if (this._pagination.getCurrent() > sz - 1) {
                    console.log('setCurrent', sz - 1);
                    return this._pagination.setCurrent(sz - 1);
                }
                this._onUpdateCount(null, count);
            }


            // sort
            var field = this._orderBy;
            if (field) {
                var val = items[0][field];

                var getterFn = function(item) { return item[field]; };
                var sorterFn;
                if (typeof val === 'number') {
                    sorterFn = this._orderDir > 0 ?
                        function(a, b) { return getterFn(a) - getterFn(b); } :
                        function(b, a) { return getterFn(a) - getterFn(b); };
                }
                else {
                    sorterFn = this._orderDir > 0 ?
                        function(a, b) { var A = getterFn(a), B = getterFn(b); return (A < B ? -1 : (A > B ? 1 : 0)); } :
                        function(b, a) { var A = getterFn(a), B = getterFn(b); return (A < B ? -1 : (A > B ? 1 : 0)); };
                }

                items.sort( sorterFn );
            }


            // paginate
            sz = this._options.pageSize;
            var start;
            if (sz !== undefined) {
                start = sz * this._pagination.getCurrent();
                //console.log(start, start + sz);
                items = items.slice(start, start + sz);
            }

            cb(null, items);
        },

        _query: function(cb) {
            this[ this._model ? '_localQuery' : '_remoteQuery' ](function(err, items) {
                if (err) { throw err; }
                this._visibleItems = items;
                cb(null, items);
            }.bindObj(this));
        },

        _updateHeaders: function(theadEl) {
            if (!theadEl) { theadEl = this._theadEl; }
            //theadEl.innerHTML = '';
            Aux.cleanChildren(theadEl);

            var i, f, field, label, tdEl, trEl = document.createElement('tr');
            for (i = 0, f = this._options.fields.length; i < f; ++i) {
                field = this._options.fields[i];
                tdEl = document.createElement('th');
                label = this._options.fieldNames[field] || field;
                if (this._orderBy === field) {
                    label = [label, ' <i class="icon-caret-', this._orderDir > 0 ? 'down' : 'up', '"></i>'].join('');
                }
                tdEl.innerHTML = label;
                trEl.appendChild(tdEl);
            }
            theadEl.appendChild(trEl);
        },

        /**
         * @function {DOMElement} ? returns the top element for the gallery DOM representation
         */
        _generateMarkup: function(el) {
            Css.addClassName(el, 'ink-table');


            var theadEl = document.createElement('thead');
            this._updateHeaders(theadEl);


            var tbodyEl = document.createElement('tbody');


            //el.innerHTML = '';
            Aux.cleanChildren(el);

            el.appendChild(theadEl);
            el.appendChild(tbodyEl);

            this._theadEl = theadEl;
            this._tbodyEl = tbodyEl;

            this.refresh();
        },

        _onPaginationChange: function() {
            this.refresh();
        },


        _onHeaderClick: function(ev) {
            var el = Event.element(ev);
            var colNr = this._whichColumn(el);
            var field = this._options.fields[ colNr ];
            var orderDir;

            if (this._options.sortableFields === '*' || Arr.inArray(field, this._options.sortableFields)) {
                if (this._orderBy === field) {
                    this._orderDir *= -1;
                }
                else {
                    this._orderBy = field;
                    this._orderDir = 1;
                }
                orderDir = this._orderDir;
                this._updateHeaders();

                if (this._pagination) {
                    this._pagination.setCurrent(0);
                }
                else {
                    this.refresh();
                }
            }

            if (this._options.onHeaderClick) {
                this._options.onHeaderClick(this, {
                    col:      colNr,
                    field:    field,
                    orderDir: orderDir
                });
            }
        },


        _onCellClick: function(ev) {
            var el = Event.element(ev);
            var rowNr = this._whichRow(el);
            var colNr = this._whichColumn(el);
            var field = this._options.fields[ colNr ];
            var item  = this._visibleItems[rowNr];

            if (this._options.onCellClick) {
                this._options.onCellClick(this, {
                    item:  item,
                    row:   rowNr,
                    col:   colNr,
                    field: field
                });
            }
        },

        _onUpdateCount: function(err, modelLength) {
            if (!this._options.pageSize) {
                return this.refresh();
            }

            if (err) { throw err; }

            if (typeof modelLength === 'object') { modelLength = modelLength.count; }

            this._modelLength = modelLength;

            var sz = Math.ceil( modelLength / this._options.pageSize);

            if (!this._pagination) {
                this._pagination = new SAPO.Ink.Pagination(this._pagEl, {
                    size:     sz,
                    onChange: this._onPaginationChange.bindObj(this)
                });
                this._init();
            }
            else {
                this._pagination.setSize(sz);
                this.refresh();
            }
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
         * @function ? updates the model. only valid if model was used before, not endpoint!
         */
        setModel: function(mdl) {
            if (!this._options.model) {
                throw new Error('Component has\'t been instanced in model mode.');
            }
            this._model = mdl;
            this.refresh();
        },

        /**
         * @function {Object[]} ? returns the array of visible items
         */
        getVisibleItems: function() {
            return this._visibleItems.slice();
        },

        /**
         * @function ? refreshes the content of the table
         */
        refresh: function() {
            this._query(function(err, items) {
                // for now err does not occurr at this step

                var tbodyEl = document.createElement('tbody');

                var i, I, j, J, trEl, tdEl, item, field, value, formatter;
                J = this._options.fields.length;

                for (i = 0, I = items.length; i < I; ++i) {
                    item = items[i];
                    trEl = document.createElement('tr');

                    for (j = 0; j < J; ++j) {
                        field = this._options.fields[j];
                        value = item[field];
                        formatter = this._options.formatters[field];
                        tdEl = document.createElement('td');

                        if (formatter) {
                            formatter(value, item, tdEl);
                        }
                        else {
                            tdEl.innerHTML = value || '';
                        }

                        trEl.appendChild(tdEl);
                    }
                    tbodyEl.appendChild(trEl);
                }

                this._element.replaceChild(tbodyEl, this._tbodyEl);

                this._tbodyEl = tbodyEl;

                Event.observe(this._tbodyEl, 'click', this._handlers.cellclick);
            }.bindObj(this));
        },

        /**
         * @function ? unregisters the component and removes its markup from the DOM
         */
        destroy: Aux.destroyComponent

    };

    SAPO.Ink.Table = Table;

})();