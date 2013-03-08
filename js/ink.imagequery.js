(function(){
    'use strict';


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

    var ImageQuery = function(selector, options){

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

        this._options = SAPO.extendObj({
            queries:[],
            allowFirstLoad: true
        },SAPO.Dom.Element.data(this._element));

        this._options = SAPO.extendObj(this._options, options || {});


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

            // Order the queries by width:
            this._options.queries = SAPO.Utility.Array.sortMulti(this._options.queries,'width').reverse();

            this._handlers = {
                resize: this._onResize.bindObjEvent(this),
                load: this._onLoad.bindObjEvent(this)
            };

            if( !this._options.allowFirstLoad ){
                SAPO.Dom.Event.observe(this._element, 'onload', this._handlers.load);
            }

            SAPO.Dom.Event.observe(window, 'resize', this._handlers.resize);
            this._handlers.resize.call(this);

        },

        _onResize: function(){


            clearTimeout(timeout);

            var timeout = setTimeout(function(){

                if( !this._options.queries || (this._options.queries === {}) ){
                    return;
                }

                // calculate map height and width

                var
                    query, selected,
                    viewportWidth
                ;

                if( typeof( window.innerWidth ) === 'number' ) {
                   viewportWidth = window.innerWidth;
                } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
                   viewportWidth = document.documentElement.clientWidth;
                } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
                   viewportWidth = document.body.clientWidth;
                }

                for( query=0; query < this._options.queries.length; query+=1 ){
                    if (this._options.queries[query].width <= viewportWidth){
                        selected = query;
                        break;
                    }
                }

                if( typeof selected === 'undefined' ){ selected = this._options.queries.length-1; }

                var src = this._options.queries[selected].src || this._options.src;
                if ( ("devicePixelRatio" in window && window.devicePixelRatio>1) && ('retina' in this._options ) ) {
                    src = this._options.queries[selected].retina || this._options.retina;
                }

                this._options.queries[selected].file = this._filename;

                if( typeof src === 'function' ){
                    src = src.apply(this,[this._element,this._options.queries[selected]]);
                }

                    
                var property;
                for( property in this._options.queries[selected] ){
                    if( ( property === 'src' ) || ( property === 'retina' ) ){ continue; }
                    src = src.replace("{:" + property + "}",this._options.queries[selected][property]);
                }
                this._element.src = src;

                timeout = undefined;

            }.bindObj(this),300);
        },

        _onLoad: function(){
            this._options.onLoad.call(this);
        }

    };

    SAPO.Ink.ImageQuery = ImageQuery;

})();























