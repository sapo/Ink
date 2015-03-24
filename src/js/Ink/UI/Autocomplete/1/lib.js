/**
 * @module Ink.UI.FormValidator_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule( 'Ink.UI.Autocomplete' , '1', [ 'Ink.UI.Common_1' , 'Ink.Dom.Selector_1' , 'Ink.Dom.Event_1' , 'Ink.Dom.Element_1' , 'Ink.Dom.Css_1' , 'Ink.Util.String_1' ], function( Common , Selector , Ivent , Elem , Css , UString ) {
    'use strict';

    /**
     * @class Ink.UI.Autocomplete_1
     * @constructor
     *
     * @param {String|DOMElement}   selector                    DOM element or element id
     * @param {Object}              [options]                   Autocomplete Options
     * @param {Integer}             [options.minLength]         Default 2.
     * @param {Integer}             [options.maxItems]          Default 10.
     * @param {Object}              [options.suggestions]       Default [ ].
     * @param {Function}            [options.autocomplete]      Default function using suggestions.
     * @param {Function}            [options.getValue]          Default return argument.
     * @param {Function}            [options.formatter]         Default return argument.
     * @param {Function}            [options.normalize]         Default return word removing accented chars, lowercase and remove space and hyphen.
     * @param {Function}            [options.onSelect]          
     * @param {Boolean}             [options.forceValue]        Default false.
     *
     * @sample Ink_UI_Autocomplete_1.html
     */
    var Autocomplete = function( ) {
        Common.BaseUIComponent.apply( this , arguments );
    };

    Autocomplete._name = 'Autocomplete_1';

    Autocomplete._optionDefinition = {
        minLength     : [ 'Integer' , 2 ] ,
        maxItems      : [ 'Integer' , 10 ] ,
        suggestions   : [ 'Object'  , [ ] ] ,
        autocomplete  : [ 'Function' , function( word , normalizeWord , setSuggestions ) {
            var n = this._normalizeSuggestions;

            var o = this._options;
            var m = o.maxItems;
            var s = o.suggestions;

            var results = [ ];
            var hits = 0;

            for ( var i = 0, l = s.length; i < l && hits < m; ++i ) {
                if ( n[ i ].indexOf( normalizeWord ) >= 0 ) {
                    results.push( s[ i ] );

                    ++hits;
                }
            }

            return setSuggestions( results );
        }] ,
        getValue      : [ 'Function' , function( o ) { return o; } ] ,
        formatter     : [ 'Function' , function( o ) { return o; } ] ,
        normalize     : [ 'Function' , function( word ) { return UString.removeAccentedChars( word ).toLowerCase( ).replace( /(-| )/g , '' ); } ] ,
        onSelect      : [ 'Function' , function( ) { } ] ,
        forceValue    : [ 'Boolean'  , false ]
    };

    Autocomplete.prototype = {
        _init : function( ) {
            var o = this._options;
            var s = o.suggestions;

            var n = this._normalizeSuggestions = [ ];

            for ( var i = 0, l = s.length; i < l; ++i ) {
                n.push( o.normalize( o.getValue( s[ i ] ) ) );
            }

            this._input      = Ink.s( 'input' , this._element );
            this._suggestion = Ink.s( 'span'  , this._element );

            Css.addClassName( this._suggestion , 'hide-all' );

            this._results = [ ];

            Ivent.observe( this._input      , 'keyup'     , Ink.bind( this._onKeyup     , this ) );
            Ivent.observe( this._input      , 'keydown'   , Ink.bind( this._onKeydonw   , this ) );
            Ivent.observe( this._suggestion , 'mouseover' , Ink.bind( this._onMouseover , this ) );
            Ivent.observe( window           , 'click'     , Ink.bind( this._onClick     , this ) );

            return this;
        } ,

        cleanInput : function( ) {
            this._input.value = '';

            return this.cleanSuggestions( );
        } ,

        selectInput : function( ) {
            var l = this._results.length;

            if ( !this._isOpen || !l ) { return this; }

            var selected = this._results[ this._idx ];
            this._input.value = this._options.getValue( selected );

            this._options.onSelect.call( this , selected );

            return this;
        } ,

        getSuggestions : function( ) {
            if ( !this._isOpen ) { this.openSuggestions( ); }

            var o = this._options;

            o.autocomplete.call( this , this._word , this._nWord , Ink.bind( this._setSuggestions , this ) );

            return this;
        } ,

        openSuggestions : function( ) {
            Css.removeClassName( this._suggestion , 'hide-all' );

            this._isOpen = true;

            return this;
        } ,

        cleanSuggestions : function( ) {
            Common.cleanChildren( this._suggestion );

            return this;
        } ,

        closeSuggestions : function( ) {
            Css.addClassName( this._suggestion , 'hide-all' );

            this._isOpen = false;

            return this;
        } ,

        _onKeydonw : function( e ) {
            switch ( e.keyCode ) {
                case Ivent.KEY_ESC:
                    this.closeSuggestions( )
                        .cleanInput( );

                    Ivent.stop( e );
                    break;
                case Ivent.KEY_RETURN:
                case Ivent.KEY_TAB:
                    if ( this._isOpen ) {
                        this.selectInput( )
                            .closeSuggestions( );

                        Ivent.stop( e );
                    }
                    break;
                case Ivent.KEY_UP:
                case Ivent.KEY_DOWN:
                    this._moveSuggestion( e.keyCode === Ivent.KEY_DOWN );

                    Ivent.stop( e );
                    break;
            }
        } ,

        _onKeyup : function( e ) {
            var keycode = e.keyCode;

            if( keycode !== 20 && keycode !== 17 && keycode !== Ivent.KEY_HOME && keycode !== Ivent.KEY_END && keycode !== Ivent.KEY_INSERT && keycode !== Ivent.KEY_PAGEUP && keycode !== Ivent.KEY_PAGEDOWN && keycode !== Ivent.KEY_DOWN && keycode !== Ivent.KEY_UP && keycode !== Ivent.KEY_ESC && keycode !== Ivent.KEY_TAB && keycode !== Ivent.KEY_LEFT && keycode !== Ivent.KEY_RETURN && keycode !== Ivent.KEY_RIGHT ) {
                Ivent.stop( e );

                var o = this._options;
                this._word  = this._input.value;
                this._nWord = o.normalize( this._word );

                this._nWord.length >= o.minLength ?
                    this.getSuggestions( ) :
                    this.closeSuggestions( );
            }
        } ,

        _onMouseover : function( e ) {
            var el = Ivent.element( e );
            var newIdx = el.getAttribute( 'data-idx' );

            if ( !newIdx ) { return this; }

            var spans = Ink.ss( '> span' , this._suggestion );

            Css.removeClassName( spans[ this._idx ] , 'selected' );

            this._idx = parseInt( newIdx , 10 );

            Css.addClassName( spans[ this._idx ] , 'selected' );
        } ,

        _onClick : function( e ) {
            var el = Ivent.element( e );

            if ( Elem.descendantOf( this._suggestion , el ) ) {
                Ivent.stop( e );

                this.selectInput( )
                    .closeSuggestions( );
            } else if ( el === this._input ) {
                Ivent.stop( e );
            } else if ( this._isOpen ) {
                if ( this._options.forceValue ) {
                    var r = this._results;
                    var o = this._options;

                    for ( var i = 0, l = r.length; i < l; ++i ) {
                        if ( o.normalize( o.getValue( r[ i ] ) ) === this._nWord ) {
                            break;
                        }
                    }

                    if ( i !== l ) {
                        this._idx = i;

                        this.selectInput( );
                    }
                }

                this.closeSuggestions( );
            }
        } ,

        _setSuggestions : function( results ) {
            this._results = results;

            this.cleanSuggestions( );
            this._idx = 0;

            var l = results.length;

            if ( !l ) {
                return this.closeSuggestions( );
            }

            for ( var i = 0; i < l; i++ ) {
                var span = document.createElement( 'span' );
                span.setAttribute( 'data-idx' , i );

                span.innerHTML = this._options.formatter( results[ i ] );

                if ( !i ) { Css.addClassName( span , 'selected' ); }

                this._suggestion.appendChild( span );
            }

            return this;
        } ,

        _moveSuggestion : function( isDown ) {
            var o = this._options;

            var l = this._results.length;

            if ( !this._isOpen ) {
                if ( l && ( this._nWord || '' ).length >= o.minLength ) {
                    this.openSuggestions( );
                }

                return this;
            }

            var spans = Ink.ss( '> span' , this._suggestion );

            Css.removeClassName( spans[ isDown ? this._idx++ : this._idx-- ] , 'selected' );

            this._idx %= l;
            if ( this._idx < 0 ) {
                this._idx += l;
            }

            Css.addClassName( spans[ this._idx ] , 'selected' );

            return this;
        }
    };

    Common.createUIComponent( Autocomplete );

    return Autocomplete;
});