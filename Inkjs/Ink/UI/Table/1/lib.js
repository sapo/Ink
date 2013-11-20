/**
 * @module Ink.UI.Table_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Table', '1', ['Ink.Util.Url_1','Ink.UI.Pagination_1','Ink.Net.Ajax_1','Ink.UI.Common_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1','Ink.Util.String_1'], function(InkUrl,Pagination, Ajax, Common, Event, Css, Element, Selector, InkArray, InkString ) {
    'use strict';

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
     *     @param {String|DomElement|Ink.UI.Pagination} [options.pagination] Pagination instance or element.
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
            pagination: ['Element', null],
            allowResetSorting: ['Boolean', false],  // Any idea of what this is?
            visibleFields: ['String', undefined]  // And this? These should be documented if they're useful.
        }, options || {}, this._rootElement);

        /**
         * Checking if it's in markup mode or endpoint mode
         */
        this._markupMode = !this._options.endpoint;

        if( !!this._options.visibleFields ){
            this._options.visibleFields = this._options.visibleFields.split(/[, ]+/g);
        }

        this._thead = Ink.s('thead', this._rootElement);

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

        this._handlers.thClick = Event.observeDelegated(this._thead, 'click',
                'th[data-sortable="true"]',
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
                this._getData( this._options.endpoint, true );
             } else /* Markup mode */ {
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
                paginated = ( ("pageSize" in this._options) && (typeof this._options.pageSize !== 'undefined') );

            Event.stop(event);
            
            var index = InkArray.keyValue(tgtEl, this._headers, true) ;

            var prop;

            if( !this._markupMode && paginated ){

                for( prop in this._sortableFields ){
                    if( prop !== ('col_' + index) ){
                        this._sortableFields[prop] = 'none';
                        this._headers[prop.replace('col_','')].innerHTML = InkString.stripTags(this._headers[prop.replace('col_','')].innerHTML);
                    }
                }

                if( this._sortableFields['col_'+index] === 'asc' ) {
                    this._sortableFields['col_'+index] = 'desc';
                    this._headers[index].innerHTML = InkString.stripTags(this._headers[index].innerHTML) + '<i class="icon-caret-down"></i>';
                } else {
                    this._sortableFields['col_'+index] = 'asc';
                    this._headers[index].innerHTML = InkString.stripTags(this._headers[index].innerHTML) + '<i class="icon-caret-up"></i>';

                }

                this._pagination.setCurrent(this._pagination._current);

            } else {

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

                    for( prop in this._sortableFields ){
                        if( prop !== ('col_' + index) ){
                            this._sortableFields[prop] = 'none';
                            this._headers[prop.replace('col_','')].innerHTML = InkString.stripTags(this._headers[prop.replace('col_','')].innerHTML);
                        }
                    }

                    this._sort(index);

                    if( this._sortableFields['col_'+index] === 'asc' ) {
                        this._data.reverse();
                        this._sortableFields['col_'+index] = 'desc';
                        this._headers[index].innerHTML = InkString.stripTags(this._headers[index].innerHTML) + '<i class="icon-caret-down"></i>';
                    } else {
                        this._sortableFields['col_'+index] = 'asc';
                        this._headers[index].innerHTML = InkString.stripTags(this._headers[index].innerHTML) + '<i class="icon-caret-up"></i>';
                    }
                }


                var tbody = Selector.select('tbody',this._rootElement)[0];
                Common.cleanChildren(tbody);
                InkArray.each(this._data, function(item){
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
                    aValue = Element.textContent(Selector.select('td',a)[index]),
                    bValue = Element.textContent(Selector.select('td',b)[index])
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
            var field, header,
                thead, tr, th;

            if( (thead = Selector.select('thead',this._rootElement)).length === 0 ){
                thead = this._rootElement.createTHead();
                tr = thead.insertRow(0);

                for( field in headers ){
                    if (headers.hasOwnProperty(field)) {

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
                            Element.setTextContent(th, header.label);
                        }

                        this._originalFields.push(field);
                        tr.appendChild(th);
                    }
                }
            } else {
                var firstLine = rows[0];

                for( field in firstLine ){
                    if (firstLine.hasOwnProperty(field)) {
                        if( !!this._options.visibleFields && (this._options.visibleFields.indexOf(field) === -1) ){
                            continue;
                        }

                        this._originalFields.push(field);
                    }
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
            if (!this._thead) { return; }


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
         * @method _setData
         * @param  {Object} rows Array of objects with the data to be showed
         * @private
         */
        _setData: function( rows ){

            var
                field,
                tbody, tr, td,
                trIndex,
                tdIndex,
                tdOptions
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
                if (rows.hasOwnProperty(trIndex)) {
                    tr = document.createElement('tr');
                    tbody.appendChild( tr );
                    tdIndex = 0;
                    for( field in rows[trIndex] ){
                        if (rows[trIndex].hasOwnProperty(field)) {

                            if( !!this._options.visibleFields && (this._options.visibleFields.indexOf(field) === -1) ){
                                continue;
                            }

                            td = tr.insertCell(tdIndex++);
                            td.innerHTML = rows[trIndex][field];

                            tdOptions = this._options.tdOptions[field];

                            if(typeof tdOptions !== "undefined" && tdOptions !== "") {
                             
                                if(tdOptions.class !== "undefined" && tdOptions.class !== "") {
                                    td.className = tdOptions.class;
                                }

                                if(tdOptions.attrs !== "undefined" && tdOptions.attrs !== "") { 
                                    var attrs = tdOptions.attrs;

                                    for (var attrName in attrs) {
                                         td.setAttribute(attrName, attrs[attrName]);
                                    };                                  
                                } 
                            }
                        }
                    }
                    this._data.push(tr);
                }
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
         * @param  {String} endpoint     Valid endpoint
         * @param  {Boolean} [firstRequest] If true, will make the request set the headers onSuccess
         * @private
         */
        _getData: function( endpoint ){
            var parsedURL = InkUrl.parseUrl( endpoint ),
                paginated = ( ("pageSize" in this._options) && (typeof this._options.pageSize !== 'undefined') ),
                pageNum = ((!!this._pagination) ? this._pagination._current+1 : 1);

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

                // var sortStr = '';
                for( var index in this._sortableFields ){
                    if( this._sortableFields[index] !== 'none' ){
                        parsedURL.query.push('sortField=' + this._originalFields[parseInt(index.replace('col_',''),10)]);
                        parsedURL.query.push('sortOrder=' + this._sortableFields[index]);
                        break;
                    }
                }

                // TODO BUG: if the endpoint already has '?', this adds another one.
                this._getDataViaAjax( endpoint + '?' + parsedURL.query.join('&') );
            }
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
            });
        }
    };

    return Table;

});
