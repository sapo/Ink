/**
 * @module Ink.UI.Table_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Table', '1', ['Ink.Net.Ajax_1','Ink.UI.Aux_1','Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Util.Array_1','Ink.UI.Pagination_1'], function(Ajax, Aux, Event, Css, Element, Selector, InkArray, Pagination ) {
    'use strict';

    /**
     * The Table component transforms the native/DOM table element into a
     * sortable, paginated component.
     * 
     * @class Ink.UI.Table
     * @constructor
     * @version 1
     * @uses Ink.UI.Aux
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Dom.Element
     * @uses Ink.Dom.Selector
     * @uses Ink.Util.Array
     * @uses Ink.UI.Pagination
     * @param {String|DOMElement} selector
     * @param {Object} [options] Options for the datepicker
     *     @param {Number}     options.pageSize       Number of rows per page.
     *     @param {String}     options.endpoint       Endpoint to get the records via AJAX
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
        this._rootElement = Aux.elOrSelector(selector, '1st argument');

        if( this._rootElement.nodeName.toLowerCase() !== 'table' ){
            throw '[Ink.UI.Table] :: The element is not a table';
        }

        this._options = Ink.extendObj({
            pageSize: undefined,
            endpoint: undefined,
            allowResetSorting: false
        },Element.data(this._rootElement));

        this._options = Ink.extendObj( this._options, options || {});

        /**
         * Checking if it's in markup mode or endpoint mode
         */
        this._markupMode = ( typeof this._options.endpoint === 'undefined' );

        /**
         * Initializing variables
         */
        this._handlers = {
            click: Ink.bindEvent(this._onClick,this)
        };
        this._sortableFields = {};
        this._originalData = this._data = [];
        this._headers = [];
        this._pagination = null;

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
             if( !this._markupMode ){
                this._getData( this._options.endpoint, true );
             }

            /**
             * Setting the sortable columns and its event listeners
             */
            Event.observe(Selector.select('thead',this._rootElement)[0],'click',this._handlers.click);
            this._headers = Selector.select('thead tr th',this._rootElement);
            InkArray.each(this._headers,Ink.bind(function(item, index){
                var dataset = Element.data( item );
                if( ('sortable' in dataset) && (dataset.sortable.toString() === 'true') ){
                    this._sortableFields['col_' + index] = 'none';
                }
            },this));

            /**
             * Getting the table's data
             */
            InkArray.each(Selector.select('tbody tr',this._rootElement),Ink.bind(function(tr){
                this._data.push(tr);
            },this));
                this._originalData = this._data.slice(0);

            if( ("pageSize" in this._options) && (typeof this._options.pageSize !== 'undefined') ){
                /**
                 * Applying the pagination
                 */
                this._pagination = this._rootElement.nextSibling;
                while(this._pagination.nodeType !== 1){
                    this._pagination = this._pagination.nextSibling;
                }

                if( this._pagination.nodeName.toLowerCase() !== 'nav' ){
                    throw '[Ink.UI.Table] :: Missing the pagination markup or is mis-positioned';
                }

                this._pagination = new Pagination( this._pagination, {
                    size: Math.ceil(this._data.length/this._options.pageSize),
                    onChange: Ink.bind(function( pagingObj ){
                        this._paginate( (pagingObj._current+1) );
                    },this)
                });

                this._paginate(1);
            }
        },

        /**
         * Click handler. This will mainly handle the sorting (when you click in the headers)
         * 
         * @method _onClick
         * @param {Event} event Event obj
         * @private
         */
        _onClick: function( event ){
            Event.stop(event);
            var
                tgtEl = Event.element(event),
                dataset = Element.data(tgtEl),
                index,i
            ;
            if( (tgtEl.nodeName.toLowerCase() !== 'th') || ( !("sortable" in dataset) || (dataset.sortable.toString() !== 'true') ) ){
                return;
            }

            index = -1;
            if( InkArray.inArray( tgtEl,this._headers ) ){
                for( i=0; i<this._headers.length; i++ ){
                    if( this._headers[i] === tgtEl ){
                        index = i;
                        break;
                    }
                }
            }

            if( index === -1){
                return;
            }

            if( (this._sortableFields['col_'+index] === 'desc') && (this._options.allowResetSorting && (this._options.allowResetSorting.toString() === 'true')) )
            {
                this._headers[index].innerHTML = this._headers[index].innerText;
                this._sortableFields['col_'+index] = 'none';

                // var found = false;
                // for(var prop in this._sortableFields ){
                //     if( this._sortableFields[prop] === 'asc' || this._sortableFields[prop] === 'desc' ){
                //         found = true;
                //         this._sort(prop.replace('col_',''));
                //         break;
                //     }
                // }

                // if( !found ){
                    this._data = this._originalData.slice(0);
                // }
            } else {

                for( var prop in this._sortableFields ){
                    if( prop !== ('col_' + index) ){
                        this._sortableFields[prop] = 'none';
                        this._headers[prop.replace('col_','')].innerHTML = this._headers[prop.replace('col_','')].innerText;
                    }
                }

                this._sort(index);

                if( this._sortableFields['col_'+index] === 'asc' )
                {
                    this._data.reverse();
                    this._sortableFields['col_'+index] = 'desc';
                    this._headers[index].innerHTML = this._headers[index].innerText + '<i class="icon-caret-down"></i>';
                } else {
                    this._sortableFields['col_'+index] = 'asc';
                    this._headers[index].innerHTML = this._headers[index].innerText + '<i class="icon-caret-up"></i>';

                }
            }


            var tbody = Selector.select('tbody',this._rootElement)[0];
            Aux.cleanChildren(tbody);
            InkArray.each(this._data,function(item){
                tbody.appendChild(item);
            });

            this._pagination.setCurrent(0);
            this._paginate(1);
        },

        /**
         * Applies and/or changes the CSS classes in order to show the right columns
         * 
         * @method _paginate
         * @param {Number} page Current page
         * @private
         */
        _paginate: function( page ){
            InkArray.each(this._data,Ink.bind(function(item, index){
                if( (index >= ((page-1)*parseInt(this._options.pageSize,10))) && (index < (((page-1)*parseInt(this._options.pageSize,10))+parseInt(this._options.pageSize,10)) ) ){
                    Css.removeClassName(item,'hide-all');
                } else {
                    Css.addClassName(item,'hide-all');
                }
            },this));
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
                    aValue = Selector.select('td',a)[index].innerText,
                    bValue = Selector.select('td',b)[index].innerText
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

        _getDataViaAjax: function( endpoint, firstRequest ){

            

            var req = new Ajax( endpoint, {
                method: 'GET'
            } );

        }
    };

    return Table;

});
