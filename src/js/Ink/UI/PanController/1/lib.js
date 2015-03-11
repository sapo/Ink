Ink.createModule( 'Ink.UI.PanController', '1' ,
    [ 'Ink.Util.Array_1' , 'Ink.Dom.Event_1' , 'Ink.UI.Common_1' ] ,
    function( Irray , Ivent , Common ) {
    'use strict';

    var dom2events = typeof addEventListener === 'function';

    var validateNoModifiers = function( e ) { return !e.ctrlKey && !e.altKey && !e.shiftKey && !e.metaKey; };
    var validateMouseButtonNoModifiers = function( e ) { return validateNoModifiers( e ) && ( e.button === 0 || e.button === 1 || ( e.type.substring( 0 , 5 ) === 'touch' && e.button === null ) ); };

    var DateNow = Date.now ? Date.now : function() { return new Date().getTime(); };

    var ownerDocument = function( node ) {
        return node && node.nodeType && node.nodeType !== 9 ? node.ownerDocument || node.document : node;
    };

    /**
     * @class Ink.UI.PanController_1
     * @constructor
     *
     * @param {String|Element}      selector                    DOM element or element id
     * @param {Object}              [options]                   Carousel Options
     * @param {Integer}             [options.autoAdvance]       Milliseconds to wait before auto-advancing pages. Set to 0 to disable auto-advance. Defaults to 0.
     * @param {String}              [options.axis]              Axis of the carousel. Set to 'y' for a vertical carousel. Defaults to 'x'.
     * @param {Number}              [options.initialPage]       Initial index page of the carousel. Defaults to 0.
     * @param {Boolean}             [options.spaceAfterLastSlide=true] If there are not enough slides to fill the full width of the last page, leave white space. Defaults to `true`.
     * @param {Boolean}             [options.swipe]             Enable swipe support if available. Defaults to true.
     * @param {Mixed}               [options.pagination]        Either an ul element to add pagination markup to or an `Ink.UI.Pagination` instance to use.
     * @param {Function}            [options.onChange]          Callback to be called when the page changes.
     *
     * @sample Ink_UI_Carousel_1.html
     */
    var PanController = function( ) {
        Common.BaseUIComponent.apply( this , arguments );
    };

    PanController._name = 'PanController_1';

    PanController._optionDefinition = {
        requestTranslationMinUpdate : [ 'Number'   , 10   ] ,
        dragThreshold               : [ 'Number'   , 4    ] ,
        doEaseOutAnimation          : [ 'Boolean'  , true ] ,
        speedEaseOutDecrease        : [ 'Number'   , 0.9  ] ,
        onDragStart                 : [ 'Function' , function( ) { } ] ,
        onDragEnd                   : [ 'Function' , function( ) { } ] ,
        requestTranslation          : [ 'Function' , function( dx , dy ) {
            var elm = this._element;

            var sl = elm.scrollLeft
            var st = elm.scrollTop;

            elm.scrollLeft -= dx;
            elm.scrollTop  -= dy;

            return sl !== elm.scrollLeft || st !== elm.scrollTop;
        }]
    };

    PanController.prototype = {
        _init : function( ) {
            this._options.speedEaseOutDecrease = Math.min( Math.max( this._options.speedEaseOutDecrease , 0 ) , 0.999 );

            this._doc = ownerDocument( this._element );

            this.dragDownListener = Ink.bind( this.dragDownListener , this );
            this.dragMoveListener = Ink.bind( this.dragMoveListener , this );
            this.dragUpListener   = Ink.bind( this.dragUpListener   , this );
            this.preClickListener = Ink.bind( this.preClickListener , this );

            Ivent.one( this._element , 'mousedown touchstart' , this.dragDownListener );
        } ,

        destroy : function( ){
            Ivent.off( this._element , 'mousedown touchstart' , this.dragDownListener );

            this.allowClick( );

            this.stopDrag( false );

            clearTimeout( this.easeOutTimeout );

            this._element = this._options.requestTranslation = null;
        } ,

        dragDownListener: function( e ) {
            this._allowClick( );

            if ( !validateMouseButtonNoModifiers( e ) ) { return; }
            this.stopDrag( false );
            clearTimeout( this.easeOutTimeout );

            if ( e.type === 'mousedown' ) { events.prevent(e); }

            Ivent.one( this._doc , 'mousemove touchmove' , this.dragMoveListener );
            Ivent.one( this._doc , 'mouseup touchend'    , this.dragUpListener   );

            this._lastTarget = Ivent.element( e ); // for IE

            var posInfo = e.touches && e.touches[ 0 ] || e;

            this._dragState = {
                started : false ,
                x       : posInfo.clientX || posInfo.screenX ,
                y       : posInfo.clientY || posInfo.screenY ,
                dx      : 0 ,
                dy      : 0 ,
                t       : null ,
                dots    : [{ dx : 0 , dy : 0 , time: DateNow( ) }] ,
                event   : { type : '' , target : null , detail : null } ,
                timer   : Ink.bind( this._timer , this )
            };

            setTimeout( Ink.bind( Ivent.one , Ivent , this._element , 'mousedown touchstart' , this.dragDownListener ) , 0 );
        } ,

        dragMoveListener : function( e ) {
            if ( !validateMouseButtonNoModifiers( e ) ) { return; }

            var state = this._dragState;
            var posInfo = e.touches && e.touches[ 0 ] || e;

            state.dx = ( posInfo.clientX || posInfo.screenX ) - state.x;
            state.dy = ( posInfo.clientY || posInfo.screenY ) - state.y;

            if ( !state.started && Math.abs( state.dx ) < this._options.dragThreshold && Math.abs( state.dy ) < this._options.dragThreshold ) { return; }

            Ivent.stopDefault( e );

            if ( !state.started ) {
                this._preventClick( );

                state.event.type   = 'dragstart';
                state.event.target = Ivent.element( e );

                this._options.onDragStart.call( this , state.event );
            }

            state.started = true;

            if ( !state.t && ( state.dx || state.dy ) ) {
                state.t = setTimeout( state.timer , this._options.requestTranslationMinUpdate );
            }

            setTimeout( Ink.bind( Ivent.one , Ivent , this._doc , 'mousemove touchmove' , this.dragMoveListener ) , 0 );
        } ,

        dragUpListener: function( e ) {
            if ( !validateMouseButtonNoModifiers( e ) ) { return; }

            this.stopDrag( true ,  e );
        } ,

        stopDrag: function( doEaseOut , e ) {
            Ivent.off( this._doc , 'mousemove touchmove' , this.dragMoveListener );
            Ivent.off( this._doc , 'mouseup touchend'    , this.dragUpListener   );

            if ( this._dragState ) {
                var state = this._dragState;

                if ( e && state.started ) {
                    Ivent.stopDefault( e );

                    state.event.type   = 'dragend';
                    state.event.target = Ivent.element( e );

                    this._options.onDragEnd.call( this , state.event );
                }

                clearTimeout( state.t );

                state.timer( );

                this._dragState = null;

                if ( doEaseOut && this._options.doEaseOutAnimation ) {
                    var now = DateNow( );

                    var last_dots = Irray.filter( state.dots , function( i ) { return i.time > now - 250; });

                    // not enough events or last event more than 80 msecs ago, bail out
                    if ( last_dots.length < 3 || last_dots[ last_dots.length - 1 ].time < now - 80 ) { return; }

                    // pixel / millisecond
                    var sp_x = 0;
                    var sp_y = 0;
                    var tt   = 0;
                    var tx   = 0;
                    var ty   = 0;

                    Irray.forEach( last_dots , function( o , i ) {
                        if ( i ) {
                            tt += o.time - last_dots[ i - 1 ].time;
                            tx += o.dx;
                            ty += o.dy;
                        }
                    });

                    sp_x = tx / tt;
                    sp_y = ty / tt;

                    this._tick = DateNow( );

                    this._easeOutTimeout = setTimeout( Ink.bind( this._post_animate , this ) , this._options.requestTranslationMinUpdate );
                }
            }
        } ,

        _allowClick : function( ) {
            Ivent.off( dom2events ? this._doc : this._lastTarget , 'click' , this.preClickListener );

            this._lastTarget = null;
        } ,
        _preventClick : function( ) {
            Ivent.on( dom2events ? this._doc : this._lastTarget , 'click' , this.preClickListener );
        } ,
        _preClickListener : function( e ) {
            if ( e.type === 'click' ){ Ivent.stop( e ); }

            this.allowClick( );
        } ,

        _timer : function() {
            var state = this._dragState;

            if ( state.dx || state.dy ) {
                this._options.requestTranslation.call( this , state.dx , state.dy );

                state.dots.push({ dx : state.dx , dy : state.dy , time : DateNow( ) });

                state.x += state.dx;
                state.y += state.dy;

                state.dx = 0;
                state.dy = 0;
            }

            state.t = null;
        } ,

        _post_animate : function( ) {
            this.easeOutTimeout = null;
            var diff = DateNow( ) - this._tick;

            var dx = sp_x * diff;
            var dy = sp_y * diff;

            if ( ( 0.5 <= Math.abs( dx ) || 0.5 <= Math.abs( dy ) ) && this._options.requestTranslation.call( this , dx , dy ) ) {
                sp_x *= this._options.speedEaseOutDecrease;
                sp_y *= this._options.speedEaseOutDecrease;

                this._easeOutTimeout = setTimeout( Ink.bind( this._post_animate , this ) , this._options.requestTranslationMinUpdate );
                this._tick = DateNow( );
            }
        }
    };

    Common.createUIComponent( PanController );

    return PanController;
});
