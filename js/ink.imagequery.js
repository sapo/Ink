(function(){
    'use strict';


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
                throw '[SAPO.Ink.ImageQuery] :: Missing dependency - ' . dependency.join(".");
            }

            checking = checking[dependency[j]];
        }
    }

    SAPO.namespace('Ink');

    /**
     * ImageQuery is an Ink's component responsible for loading images based on the viewport width.
     * For that, the component accepts an array of (media) queries in the options.
     * 
     * @param {string|DOMElement} selector CSS Selector or DOMElement
     * @param {object} options  Options' object for configuring the instance. These options can also be set through
     * data-attributes
     */
    var ImageQuery = function(selector, options){

        /**
         * Selector's type checking
         */
        if( !SAPO.Ink.Aux.isDOMElement(selector) && (typeof selector !== 'string') ){
            throw '[SAPO.Ink.ImageQuery] :: Invalid selector';
        } else if( typeof selector === 'string' ){
            this._element = SAPO.Dom.Selector.select( selector );

            if( this._element.length < 1 ){
                throw '[SAPO.Ink.ImageQuery] :: Selector has returned no elements';
            } else if( this._element.length > 1 ){
                var i;
                for( i=1;i<this._element.length;i+=1 ){
                    new SAPO.Ink.ImageQuery(this._element[i],options);
                }
            }
            this._element = this._element[0];

        } else {
            this._element = selector;
        }


        /**
         * Default options and their override based on data-attributes if any.
         * The parameters are:
         * @param {array} queries Array of objects that determine the label/name and its min-width to be applied.
         * @param {boolean} allowFirstLoad Boolean flag to allow the loading of the first element.
         */
        this._options = SAPO.extendObj({
            queries:[],
            onLoad: null
        },SAPO.Dom.Element.data(this._element));

        this._options = SAPO.extendObj(this._options, options || {});

        /**
         * Determining the original basename (with the querystring) of the file.
         */
        var pos;
        if( (pos=this._element.src.lastIndexOf('?')) !== -1 ){
            var search = this._element.src.substr(pos);
            this._filename = this._element.src.replace(search,'').split('/').pop()+search;
        } else {
            this._filename = this._element.src.split('/').pop();
        }

        this._init();
    };

    ImageQuery.prototype = {

        _init: function(){

            /**
             * Sort queries by width, in descendant order.
             */
            this._options.queries = SAPO.Utility.Array.sortMulti(this._options.queries,'width').reverse();

            /**
             * Declaring the event handlers, in this case, the window.resize and the (element) load.
             * @type {Object}
             */
            this._handlers = {
                resize: this._onResize.bindObjEvent(this),
                load: this._onLoad.bindObjEvent(this)
            };

            if( typeof this._options.onLoad === 'function' ){
                SAPO.Dom.Event.observe(this._element, 'onload', this._handlers.load);
            }

            SAPO.Dom.Event.observe(window, 'resize', this._handlers.resize);

            // Imediate call to apply the right images based on the current viewport
            this._handlers.resize.call(this);

        },

        /**
         * @function {void} ? Handles the resize event (as specified in the _init function)
         * @return {void}
         */
        _onResize: function(){

            clearTimeout(timeout);

            var timeout = setTimeout(function(){

                if( !this._options.queries || (this._options.queries === {}) ){
                    clearTimeout(timeout);
                    return;
                }

                var
                    query, selected,
                    viewportWidth
                ;

                /**
                 * Gets viewport width
                 */
                if( typeof( window.innerWidth ) === 'number' ) {
                   viewportWidth = window.innerWidth;
                } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
                   viewportWidth = document.documentElement.clientWidth;
                } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
                   viewportWidth = document.body.clientWidth;
                }

                /**
                 * Queries are in a descendant order. We want to find the query with the highest width that fits
                 * the viewport, therefore the first one.
                 */
                for( query=0; query < this._options.queries.length; query+=1 ){
                    if (this._options.queries[query].width <= viewportWidth){
                        selected = query;
                        break;
                    }
                }

                /**
                 * If it doesn't find any selectable query (because they don't meet the requirements)
                 * let's select the one with the smallest width
                 */
                if( typeof selected === 'undefined' ){ selected = this._options.queries.length-1; }

                /**
                 * Choosing the right src. The rule is:
                 * 
                 *   "If there is specifically defined in the query object, use that. Otherwise uses the global src."
                 *
                 * The above rule applies to a retina src.
                 */
                var src = this._options.queries[selected].src || this._options.src;
                if ( ("devicePixelRatio" in window && window.devicePixelRatio>1) && ('retina' in this._options ) ) {
                    src = this._options.queries[selected].retina || this._options.retina;
                }

                /**
                 * Injects the file variable for usage in the 'templating system' below
                 */
                this._options.queries[selected].file = this._filename;

                /**
                 * Since we allow the src to be a callback, let's run it and get the results.
                 * For the inside, we're passing the element (img) being processed and the object of the selected
                 * query.
                 */
                if( typeof src === 'function' ){
                    src = src.apply(this,[this._element,this._options.queries[selected]]);
                    if( typeof src !== 'string' ){
                        throw '[SAPO.Ink.ImageQuery] :: "src" callback does not return a string';
                    }
                }

                /**
                 * Replace the values of the existing properties on the query object (except src and retina) in the
                 * defined src and/or retina.
                 */
                var property;
                for( property in this._options.queries[selected] ){
                    if( ( property === 'src' ) || ( property === 'retina' ) ){ continue; }
                    src = src.replace("{:" + property + "}",this._options.queries[selected][property]);
                }
                this._element.src = src;

                // Removes the injected file property
                delete this._options.queries[selected].file;

                timeout = undefined;

            }.bindObj(this),300);
        },

        /**
         * @function {void} ? Handles the element loading (img onload) event
         * @return {void}
         */
        _onLoad: function(){

            /**
             * Since we allow a callback for this let's run it.
             */
            this._options.onLoad.call(this);
        }

    };

    SAPO.Ink.ImageQuery = ImageQuery;

})();























