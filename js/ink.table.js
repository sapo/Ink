(function(){
    
    /**
     * Dependency check
     */
    var
        dependencies = ['SAPO.Dom.Selector', 'SAPO.Dom.Event', 'SAPO.Dom.Element', 'SAPO.Dom.Css', 'SAPO.Ink.Aux', 'SAPO.Utility.Array'],
        dependency, i, j,
        checking
    ;

    for( i = 0; i < dependencies.length; i+=1 ){
        dependency = dependencies[i].split(".");
        checking = window;
        for( j = 0; j < dependency.length; j+=1 ){
            if( !(dependency[j] in checking ) ){
                throw '[SAPO.Ink.Table] :: Missing dependency - ' . dependency.join(".");
            }

            checking = checking[dependency[j]];
        }
    }

    /**
     * Using variables for dependencies... Easier to change in the future
     */
    var
        Aux = SAPO.Ink.Aux,
        Selector = SAPO.Dom.Selector,
        Element = SAPO.Dom.Element,
        Util_Array = SAPO.Utility.Array,
        Event = SAPO.Dom.Event,
        Css = SAPO.Dom.Css
    ;

    var Table = function( selector, options ){

        /**
         * Get the root element
         */
        this._rootElement = Aux.elOrSelector(selector, '1st argument');

        if( this._rootElement.nodeName.toLowerCase() !== 'table' ){
            throw '[SAPO.Ink.Table] :: The element is not a table';
        }

        this._options = SAPO.extendObj({
        },Element.data(this._rootElement));

        this._options = SAPO.extendObj( this._options, options || {});

        /**
         * Initializing variables
         */
        this._handlers = {
            click: this._onClick.bindObjEvent(this)
        };
        this._sortableFields = {};
        this._originalData = this._data = [];
        this._headers = [];
        this._pagination = null;

        this._init();
    };

    Table.prototype = {

        _init: function(){
            /**
             * Setting the sortable columns and its event listeners
             */
            Event.observe(Selector.select('thead',this._rootElement)[0],'click',this._handlers.click);
            this._headers = Selector.select('thead tr th',this._rootElement);
            this._headers.forEach(function(item, index){
                var dataset = Element.data( item );
                if( ('sortable' in dataset) && (dataset.sortable.toString() === 'true') ){
                    this._sortableFields['col_' + index] = 'none';
                }
            }.bindObj(this));

            /**
             * Getting the table's data
             */
            Selector.select('tbody tr',this._rootElement).forEach(function(tr){
                this._data.push(tr);
            }.bindObj(this));
                this._originalData = this._data.slice(0);

            if( "pageSize" in this._options ){
                /**
                 * Applying the pagination
                 */
                this._pagination = this._rootElement.nextSibling;
                while(this._pagination.nodeType !== 1){
                    this._pagination = this._pagination.nextSibling;
                }

                if( this._pagination.nodeName.toLowerCase() !== 'nav' ){
                    throw '[SAPO.Ink.Table] :: Missing the pagination markup or is mis-positioned';
                }

                this._pagination = new SAPO.Ink.Pagination( this._pagination, {
                    size: Math.ceil(this._data.length/this._options.pageSize),
                    onChange: function( pagingObj ){
                        this._paginate( (pagingObj._current+1) );
                    }.bindObj(this)
                });

                var pagination_ul = Selector.select('.pagination',this._pagination._element)[0];
                Css.addClassName(pagination_ul,'rounded');
                Css.addClassName(pagination_ul,'shadowed');
                Css.addClassName(pagination_ul,'blue');

                this._paginate(1);
            }
        },

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
            if( Util_Array.inArray( tgtEl,this._headers ) ){
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

            if( this._sortableFields['col_'+index] === 'desc' )
            {
                this._headers[index].innerHTML = this._headers[index].innerText;
                this._sortableFields['col_'+index] = 'none';
                this._data = this._originalData.slice(0);
            } else {

                this._data.sort(function(a,b){
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
                }.bindObj(this));

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
            this._data.forEach(function(item){
                tbody.appendChild(item);
            });

            this._pagination.setCurrent(0);
            this._paginate(1);
        },

        _paginate: function( page ){
            var
                pagesVisible = Math.ceil(this._data.length/this._options.pageSize),
                i
            ;
            this._data.forEach(function(item, index){
                if( (index >= ((page-1)*parseInt(this._options.pageSize,10))) && (index < (((page-1)*parseInt(this._options.pageSize,10))+parseInt(this._options.pageSize,10)) ) ){
                    Css.removeClassName(item,'hide-all');
                } else {
                    Css.addClassName(item,'hide-all');
                }
            }.bindObj(this));
        }
    };

    SAPO.Ink.Table = Table;
})();