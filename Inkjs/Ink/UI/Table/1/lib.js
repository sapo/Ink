/**
 * @module Ink.UI.Table_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Table', '1', ['Ink.Util.Url_1','Ink.UI.Pagination_1','Ink.Net.Ajax_1','Ink.UI.Common_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1','Ink.Util.String_1', 'Ink.Util.Json_1'], function(InkUrl,Pagination, Ajax, Common, Event, Css, Element, Selector, InkArray, InkString, Json) {
    'use strict';

    var rNumber = new RegExp(/\d/g);
    // Turn into a number, if we can. For sorting data which could be numeric or not.
    function maybeTurnIntoNumber(value) {
        if( !isNaN(value) && rNumber.test(value) ){
            return parseInt(value, 10);
        } else if( !isNaN(value) ){
            return parseFloat(value);
        }
        return value;
    }
    // cmp function for comparing data which might be a number.
    function numberishEnabledCmp (index, a, b) {
        var aValue = Element.textContent(Selector.select('td',a)[index]),
            bValue = Element.textContent(Selector.select('td',b)[index]);

        aValue = maybeTurnIntoNumber(aValue);
        bValue = maybeTurnIntoNumber(bValue);

        if( aValue === bValue ){
            return 0;
        } else {
            return ( ( aValue > bValue ) ? 1 : -1 );
        }
    }
    // Object.keys polyfill
    function keys(obj) {
        if (typeof Object.keys !== 'undefined') {
            return Object.keys(obj);
        }
        var ret = [];
        for (var k in obj) if (obj.hasOwnProperty(k)) {
            ret.push(k);
        }
        return ret;
    }

    // Most processJSON* functions can just default to this.
    function sameSame(obj) { return obj; }
    /**
     * The Table component transforms the native/DOM table element into a
     * sortable, paginated component.
     * 
     * @class Ink.UI.Table
     * @constructor
     * @version 1
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options
     *     @param {Number}    [options.pageSize]      Number of rows per page. Omit to avoid paginating.
     *     @param {String}    [options.endpoint]      Endpoint to get the records via AJAX. Omit if you don't want to do AJAX
     *     @param {Function}  [options.createEndpointUrl] Callback to customise what URL the AJAX endpoint is at. Receives three arguments: base (the "endpoint" option), sort ({ order: 'asc' or 'desc', field: fieldname }) and page ({ page: page number, size: items per page })
     *     @param {Function}  [options.processJSONRows] TODO doc
     *     @param {Function}  [options.processJSONHeader] TODO doc
     *     @param {Function}  [options.processJSONHeaders] TODO doc
     *     @param {Function}  [options.processJSONRow] TODO doc
     *     @param {Function}  [options.processJSONField] TODO doc
     *     @param {Function}  [options.processJSONField.(field_name)] TODO doc
     *     @param {Function}  [options.processJSONTotalRows] TODO doc
     *     @param {String|DomElement|Ink.UI.Pagination} [options.pagination] Pagination instance or element.
     *     @param {Boolean}   [options.allowResetSorting] Allow sort order to be set to "none" in addition to "ascending" and "descending"
     *     @param {String|Array} [options.visibleFields] Set of fields which get shown on the table
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
        this._rootElement = Common.elOrSelector(selector, 'Ink.UI.Table :');

        if( this._rootElement.nodeName.toLowerCase() !== 'table' ){
            throw '[Ink.UI.Table] :: The element is not a table';
        }

        this._options = Common.options({
            pageSize: ['Integer', null],
            endpoint: ['String', null],
            createEndpointUrl: ['Function', null /* default func uses above option */],
            processJSONRows: ['Function', sameSame],
            processJSONRow: ['Function', sameSame],
            processJSONField: ['Function', sameSame],
            processJSONHeader: ['Function', sameSame],
            processJSONHeaders: ['Function', function (dt) { return keys(dt[0]); }],
            processJSONTotalRows: ['Function', function (dt) { return dt.length; }],
            pagination: ['Element', null],
            allowResetSorting: ['Boolean', false],
            visibleFields: ['String', undefined]
        }, options || {}, this._rootElement);

        /**
         * Checking if it's in markup mode or endpoint mode
         */
        this._markupMode = !this._options.endpoint;

        if( !!this._options.visibleFields ){
            this._options.visibleFields = this._options.visibleFields.split(/[, ]+/g);
        }

        this._thead = this._rootElement.tHead || this._rootElement.createTHead();

        /**
         * Initializing variables
         */
        this._handlers = {
            thClick: null
        };
        this._originalFields = [];
        this._sortableFields = {
            // Identifies which columns are sorted and how.
            // colIndex: 'none'|'asc'|'desc'
        };
        this._originalData = this._data = [];
        this._headers = [];
        this._pagination = null;
        this._totalRows = 0;

        this._handlers.thClick = Event.observeDelegated(this._rootElement, 'click',
                'thead th[data-sortable="true"]',
                Ink.bindMethod(this, '_onThClick'));

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
             if( !this._markupMode ) {
                 /* Endpoint mode */
                this._getData(  );
             } else /* Markup mode */ {
                this._resetSortOrder();

                /**
                 * Getting the table's data
                 */
                this._data = Selector.select('tbody tr', this._rootElement);
                this._originalData = this._data.slice(0);

                this._totalRows = this._data.length;

                /**
                 * Set pagination if options tell us to
                 */
                this._setPagination();
                if (this._pagination) {
                    this._paginate(1);
                }
             }
        },

        /**
         * Click handler. This will mainly handle the sorting (when you click in the headers)
         * 
         * @method _onThClick
         * @param {Event} event Event obj
         * @private
         */
        _onThClick: function( event ){
            var tgtEl = Event.element(event),
                paginated = this._options.pageSize !== undefined;

            Event.stop(event);

            var index = InkArray.keyValue(tgtEl, this._headers, true) ;

            if( index === false ){
                return;
            }

            if( !this._markupMode && paginated ){
                this._invertSortOrder(index, false);
            } else {
                if ( (this._sortableFields['col_'+index] === 'desc') && this._options.allowResetSorting ) {
                    this._setSortOrderOfColumn(index, null);
                    this._data = this._originalData.slice(0);
                } else {
                    this._invertSortOrder(index, true);
                }

                var tbody = Selector.select('tbody',this._rootElement)[0];
                Common.cleanChildren(tbody);
                InkArray.each(this._data, Ink.bindMethod(tbody, 'appendChild'));

                this._pagination.setCurrent(0);
                this._paginate(1);
            }
        },

        _invertSortOrder: function (index, sortAndReverse) {
            var isAscending = this._sortableFields['col_'+index] === 'asc';

            var len = keys(this._sortableFields).length;
            for (var i = 0; i < len; i++) {
                this._setSortOrderOfColumn(i, null);
            }

            if (sortAndReverse) {
                this._sort(index);
            }

            if( isAscending ) {
                if (sortAndReverse) {
                    this._data.reverse();
                }
                this._setSortOrderOfColumn(index, false);
            } else {
                this._setSortOrderOfColumn(index, true);
            }
        },

        _setSortOrderOfColumn: function(index, up) {
            var caretHtml = '';
            var order = 'none';
            if (up === true) {
                caretHtml = '<i class="icon-caret-up"></i>';
                order = 'asc';
            } else if (up === false) {
                caretHtml = '<i class="icon-caret-down"></i>';
                order = 'desc';
            }

            this._sortableFields['col_' + index] = order;
            this._headers[index].innerHTML = InkString.stripTags(
                    this._headers[index].innerHTML) + caretHtml;
        },

        /**
         * Applies and/or changes the CSS classes in order to show the right columns
         * 
         * @method _paginate
         * @param {Number} page Current page
         * @private
         */
        _paginate: function( page ){
            if (!this._pagination) { return; }

            var pageSize = this._options.pageSize;

            // Hide everything except the items between these indices
            var firstIndex = (page - 1) * pageSize;
            var lastIndex = firstIndex + pageSize;

            InkArray.each(this._data, function(item, index){
                if (index >= firstIndex && index < lastIndex) {
                    Css.removeClassName(item,'hide-all');
                } else {
                    Css.addClassName(item,'hide-all');
                }
            });

        },

        /* register fields into this._originalFields, whether they come from JSON or a table.
         * @method _registerFieldNames
         * @private
         * @param [names] The field names in an array
         **/
        _registerFieldNames: function (names) {
            this._originalFields = [];

            InkArray.forEach(names, Ink.bind(function (field) {
                if( !this._fieldIsVisible(field) ){
                    return;  // The user deems this not to be necessary to see.
                }
                this._originalFields.push(field);
            }, this));
        },

        _fieldIsVisible: function (field) {
            return !this._options.visibleFields ||
                (this._options.visibleFields.indexOf(field) !== -1);
        },

        /**
         * Sorts by a specific column.
         * 
         * @method _sort
         * @param {Number} index Column number (starting at 0)
         * @private
         */
        _sort: function( index ){
            this._data.sort(Ink.bind(numberishEnabledCmp, false, index));
        },

        /**
         * Assembles the headers markup
         *
         * @method _createHeadersFromJson
         * @param  {Object} headers Key-value object that contains the fields as keys, their configuration (label and sorting ability) as value
         * @private
         */
        _createHeadersFromJson: function( headers ){
            this._registerFieldNames(keys(headers));

            if (this._thead.children.length) { return; }

            var tr = this._thead.insertRow(0);
            var th;

            for (var k in headers) if (headers.hasOwnProperty(k)) {
                if (this._fieldIsVisible(headers[k])) {
                    th = Element.create('th');
                    th = this._createSingleHeaderFromJson(headers[k], th);
                    tr.appendChild(th);
                }
            }
        },

        _createSingleHeaderFromJson: function (header, th) {
            header = this._options.processJSONHeader(header);

            if (header.sortable) {
                th.setAttribute('data-sortable','true');
            }

            if (header.label){
                Element.setTextContent(th, header.label);
            }

            return th;
        },

        /**
         * Method that sets the event handlers for the TH headers
         *
         * @method _resetSortOrder
         * @private
         */
        _resetSortOrder: function(){
            /**
             * Setting the sortable columns and its event listeners
             */

            this._headers = Selector.select('th', this._thead);
            InkArray.each(this._headers, Ink.bind(function(item, index){
                var dataset = Element.data( item );
                if (dataset.sortable && dataset.sortable.toString() === 'true') {
                    this._sortableFields['col_' + index] = 'none';
                }
            }, this));
        },

        /**
         * This method gets the rows from AJAX and places them as <tr> and <td>
         *
         * @method _createRowsFromJSON
         * @param  {Object} rows Array of objects with the data to be showed
         * @private
         */
        _createRowsFromJSON: function( rows ){
            var tbody = Selector.select('tbody',this._rootElement);
            if( tbody.length === 0){
                tbody = document.createElement('tbody');
                this._rootElement.appendChild( tbody );
            } else {
                tbody = tbody[0];
                tbody.innerHTML = '';
            }

            this._data = [];
            var row;

            for( var trIndex in rows ){
                if (rows.hasOwnProperty(trIndex)) {
                    row = this._options.processJSONRow(rows[trIndex]);
                    this._createSingleRowFromJson(tbody, row, trIndex);
                }
            }

            this._originalData = this._data.slice(0);
        },

        _createSingleRowFromJson: function (tbody, row, rowIndex) {
            var tr = document.createElement('tr');
            tbody.appendChild( tr );
            for( var field in row ){
                if (row.hasOwnProperty(field)) {
                    this._createFieldFromJson(tr, row[field], field, rowIndex);
                }
            }
            this._data.push(tr);
        },

        _createFieldFromJson: function (tr, column, fieldName, rowIndex) {
            /* jshint unused: false */
            if (!this._fieldIsVisible(fieldName)) { return; }
            /* TODO ask callbacks how to */

            var processor = this._options.processJSONField[column] ||
                this._options.processJSONField;

            tr.appendChild(Element.create('td', {
                setHTML: processor(column)
            }));
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
                this._pagination.setCurrent((!!currentPage) ? parseInt(currentPage,10) : 0 );
            }
        },

        /**
         * Sets the instance's pagination, if necessary.
         *
         * Precondition: this._totalRows needs to be known.
         *
         * @method _setPagination
         * @private
         */
        _setPagination: function(){
            /* If user doesn't say they want pagination, bail. */
            if( this._options.pageSize == null ){ return; }

            /**
             * Fetch pagination from options. Can be a selector string, an element or a Pagination instance.
             */
            var paginationEl = this._options.pagination;

            if ( paginationEl instanceof Pagination ) {
                this._pagination = paginationEl;
                return;
            }

            if (!paginationEl) {
                paginationEl = Element.create('nav', {
                    className: 'ink-navigation',
                    insertAfter: this._rootElement
                });
                Element.create('ul', {
                    className: 'pagination',
                    insertBottom: paginationEl
                });  // TODO this element is pagination's responsibility.
            }

            this._pagination = new Pagination(paginationEl, {
                totalItemCount: this._totalRows,
                itemsPerPage: this._options.pageSize,
                onChange: Ink.bind(function (_, pageNo) {
                    this._paginate(pageNo + 1);
                }, this)
            });
        },

        /**
         * Method to choose which is the best way to get the data based on the endpoint:
         *     - AJAX
         *     - JSONP
         *
         * @method _getData
         * @private
         */
        _getData: function( ){
            var sortOrder = this._getSortOrder() || null;
            var page = null;

            if (this._pagination) {
                page = {
                    size: this._options.pageSize,
                    page: this._pagination.getCurrent() + 1
                };
            }

            this._getDataViaAjax( this._getUrl( sortOrder, page) );
        },

        /**
         * Return an object describing sort order { field: [field name] ,
         * order: either ["asc" or "desc"] }, or null if there is no sorting
         * going on.
         * @method _getSortOrder
         * @private
         */
        _getSortOrder: function () {
            var index;
            for( index in this._sortableFields ){
                if( this._sortableFields[index] !== 'none' ){
                    break;
                }
            }
            if (!index) {
                return null; // no sorting going on
            }
            return {
                field: this._originalFields[parseInt(index.replace('col_', ''), 10)],
                order: this._sortableFields[index]
            };
        },

        _getUrl: function (sort, page) {
            var urlCreator = this._options.createEndpointUrl ||
                function (endpoint, sort, page
                        /* TODO implement filters too */) {
                    endpoint = InkUrl.parseUrl(endpoint);
                    endpoint.query = endpoint.query || {};

                    if (sort) {
                        endpoint.query.sortOrder = sort.order;
                        endpoint.query.sortField = sort.field;
                    }

                    if (page) {
                        endpoint.query['rows_per_page'] = page.size;
                        endpoint.query['page'] = page.page;
                    }

                    return InkUrl.format(endpoint);
                };

            var ret = urlCreator(this._options.endpoint, sort, page);

            if (typeof ret !== 'string') {
                throw new TypeError('Ink.UI.Table_1: ' +
                    'createEndpointUrl did not return a string!');
            }

            return ret;
        },

        /**
         * Gets the data via AJAX and triggers the changes in the 
         * 
         * @param  {[type]} endpoint     [description]
         * @param  {[type]} firstRequest [description]
         * @return {[type]}              [description]
         */
        _getDataViaAjax: function( endpoint ){
            new Ajax( endpoint, {
                method: 'GET',
                contentType: 'application/json',
                sanitizeJSON: true,
                onSuccess: Ink.bind(function( response ){
                    if( response.status === 200 ){
                        this._onAjaxSuccess(Json.parse(response.responseText));
                    }
                }, this)
            });
        },

        _onAjaxSuccess: function (jsonResponse) {
            var paginated = this._options.pageSize != null;
            var rows = this._options.processJSONRows(jsonResponse);

            // If headers not in DOM, get from JSON
            if( this._headers.length === 0 ) {
                var headers = this._options.processJSONHeaders(
                    jsonResponse);
                if (!headers || !headers.length || !headers[0]) {
                    throw new Error('Ink.UI.Table: processJSONHeaders option must return an array of objects!');
                }
                this._createHeadersFromJson( headers );
                this._resetSortOrder();
            }

            this._createRowsFromJSON( rows );

            this._totalRows = this._rowLength = rows.length;

            if( paginated ){
                this._totalRows = this._options.processJSONTotalRows(jsonResponse);
                this._setPagination( );
            }
        }
    };

    return Table;

});
