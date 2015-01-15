/**
 * @module Ink.Util.I18n_1
 * @author inkdev AT sapo.pt
 */

Ink.createModule( 'Ink.Util.Router' , '1' , [ 'Ink.Dom.Event_1' ] , function( InkEvent ) {
    'use strict';

    //var patternPath = /::([^:\/\[\]]+)|:([^:\*\{\+\/\[\]]+)((\+)|(\*)|(\{[^}]+}))?|(\[)|(\])|(\/)|([^:\/\[\]]+)/g;
    var patternPath = /::([^:\/\[\]]+)|:([^:\/\[\]]+)|(\[)|(\])|(\/)|([^:\/\[\]]+)/g;
    //var testInterval = /^\{0+(?:,|})/;
    var path2regex = function( _ , $ , $1 , $2 , $3 , $4 , $5 ) {
        //var c = ( this._options.params[ $1 ] || '[^\/]+' );
        //c + ( x ? '(?:\/' + c + ')' + ( p || m ? '*' : i.replace( /(\d+)/g , function( m , $1 ) { $1 = parseInt( $1 ); return $1 ? $1 - 1 : $1; } ) ) : '' )
        // + ( m || i && testInterval.test( i ) ? '?' : '' )

        return $  ? $ :
               $1 ? '(' + ( this._options.params[ $1 ] || '[^\/]+' ) + ')' :
               $2 ? '(?:' :
               $3 ? ')?' :
               $4 ? '\\/' :
                    '(' + $5 + ')';
    };

    /*var splitter = function( arr ) {
        var _ = [ ];

        for ( var i = 0, l = arr.length; i < l; i++ ) {
            _ = _.concat( arr[ i ] ? arr[ i ].split( '/' ) : [ arr[ i ] ] );
        }

        return _;
    };*/

    /**
     * @class Ink.Util.Router
     * @constructor
     * @version 1
     * @param {Object} [opt] Options
     *      @param {String}      [opt.baseURL='/']         base URL
     *      @param {String}      [opt.mode='hash']         Routing mode, default is hash, use URL hash part to save the page state and the path mode uses path part (using pushState) (see note)
     *      @param {Boolean}     [opt.compatibility=false] if using path mode with compatibility true, will use hash on browser that not support pushState (see note)
     *      @param {Function}    [opt.onLoad]              callback function to run after Router initiation
     *      @param {Function}    [opt.onChange]            callback function to run when URL changes
     *      @param {Function}    [opt.onFail]              callback function to run when fails to match paths inside this path
     *      @param {Array}       [opt.paths]               array of paths
     *          @param {String}           opt.paths.path            path to be match with URL
     *          @param {Function}         [opt.paths.init]          callback function to run when enters for the first time in this path
     *          @param {Function}         [opt.paths.enter]         callback function to run when enters in this path
     *          @param {Function}         [opt.paths.exit]          callback function to run when leaves in this path
     *          @param {Function}         [opt.paths.fail]          callback function to run when fails to match paths inside this path
     *          @param {Number|String}    [opt.paths.repeat]        use string '+' or a integer, if it's a number (integer) will be like using regex {1,n}
     *          @param {Array}            [opt.paths.paths]         array of paths
     *      @param {Object}      [opt.params]              pair of variable names and valid values (regex strings)
     *
     * @example
     *      <script>
     *          Ink.requireModules( [ 'Ink.Util.Router_1' ] , function( Router ) {
     *              var router = new Router({
     *                  params : {
     *                      id : '\\d+'
     *                  } ,
     *                  paths  : [{
     *                      path  : 'users' ,
     *                      init  : function( main , urlPArray , nextURL ) {
     *                          domManipulater.getHTML( main ); //'users'
     *                      } ,
     *                      enter : function( main , urlPArray , nextURL ) {
     *                          domManipulater.setMain( main ); //'users'
     *                      } ,
     *                      exit  : function( main , urlPArray ) {
     *                          domManipulater.delMain( main ); //'users'
     *                      } ,
     *                      paths : [{
     *                          path : '[:id]' ,
     *                          enter : function( id , urlPArray , nextURL ) {
     *                              if ( id !== undefined ) {
     *                                  domManipulater.setInfo( 'userID' , dbGetter.getUserInfo( id ) );
     *                              } else {
     *                                  domManipulater.setInfo( 'usersList' , dbGetter.getUsersList( ) );
     *                              }
     *                          } ,
     *                          exit  : function( id , urlPArray ) {
     *                              if ( id !== undefined ) {
     *                                  domManipulater.delInfo( 'userID' );
     *                              } else {
     *                                  domManipulater.delInfo( 'usersList' );
     *                              }
     *                          }
     *                      }]
     *                  }]
     *              });
     *          });
     *      </script>
     *
     * **NOTE:**
     * If the mode property is set to 'path'
     * you should use a simple rewriteRules in
     * your server configuration.
     * In Apache you can use something like this:
     * RewriteRule   ^(.*)$  index.php?$1 [L]
     *
     * If you use the settings from above and
     * compatibility property is set to true,
     * you should use some more rewriteRules.
     * Again, in Apache, you can use something like this:
     * RewriteCond %{HTTP_USER_AGENT} .*MSIE.\d\.\d*
     * RewriteCond %{REQUEST_URI} !index.php
     * RewriteRule ^(.+)$ http://%{HTTP_HOST}/testRouter/3/#$1 [NE,L,R]
     *
     */
    var Router = function( opt ) {
        if ( !( this instanceof Router ) ) { return new Router( opt ); }

        this._options = Ink.extendObj({
            baseURL       : '/' ,
            mode          : 'hash' ,
            compatibility : false ,
            paths         : [ ] ,
            params        : { }
        } , opt );

        return this._init( );
    };

    Router.prototype = {
        _init : function( ) {
            var o = this._options;

            var isPath = o.mode === 'path';

            if ( isPath && window.history && history.pushState ) { this._setPSlistenners( ); }
            else if ( isPath && o.compatibility || !isPath )     { this._setHlistenners( ); }
            else {
                this._url = location.pathname.replace( new RegExp( '^' + this._options.baseURL ) , '' );
            }

            this._runCB( this._options.onLoad , this , this.getPath( ) );

            var success = this._prepareRegex( )
                              ._verifyURL( );

            if ( !success ) {
                this._runCB( this._options.onFail , this , this.getPath( ) );
            }

            return this;
        } ,

        /**
        * return the pseudo path
        *
        * @method getPath
        * @return {String} pseudo path
        */
        getPath : function( ) { return this._url || ''; } ,
        /**
        * set the pseudo path
        *
        * @method setPath
        * @param {String}  path
        * @param {Boolean} [isReplace] if mode equal to path and history API is supported, and isReplace true, will be use replaceState insted of pushState
        * @chainable
        */
        setPath : function( path ) {
            if ( this.getPath( ) !== path ) {
                this._url = path;

                this._handlerListenner( );
            }

            return this;
        } ,

        _setPSlistenners : function( ) {
            this.getPath = function( ) {
                return location.pathname.replace( new RegExp( '^' + this._options.baseURL ) , '' );
            };
            this.setPath = function( path , isReplace ) {
                if ( this.getPath( ) !== path ) {
                    history[ isReplace ? 'replaceState' : 'pushState' ]( '' , '' , this._options.baseURL + path );

                    this._handlerListenner( );
                }

                return this;
            };

            if ( this._options.compatibility && location.pathname === this._options.baseURL && location.hash ) {
                history.replaceState( '' , '' , this._options.baseURL + location.hash.slice( 1 , Infinity ) );
            }

            InkEvent.observe( window , 'popstate' , Ink.bindEvent( this._handlerListenner , this ) );

            var self = this;
            setTimeout( function( ) {
                self._loaded = true;
            } , 0 );

            return this;
        } ,
        _setHlistenners : function( ) {
            this.getPath = function( ) {
                return location.hash.slice( 1 , Infinity );
            };
            this.setPath = function( path ) {
                if ( this.getPath( ) !== path ) {
                    location.hash = path;
                }

                return this;
            };

            if ( 'onhashchange' in window ) {
                window[ window.addEventListener ? 'addEventListener' : 'attachEvent' ]( window.addEventListener ? 'hashchange' : 'onhashchange' , Ink.bindEvent( this._handlerListenner , this ) );
            } else {
                var self = this;
                this._url = this.getPath( );

                setInterval( function( ) {
                    var newHash = location.hash.slice( 1 , Infinity );

                    if ( self._url !== newHash ) {
                        self._url = newHash;

                        self._handlerListenner( );
                    }
                } , 200 );
            }

            return this;
        } ,

        _prepareRegex : function( paths ) {
            paths = paths || this._options.paths;

            for ( var i = 0, l = paths.length; i < l; i++ ) {
                var path = paths[ i ];

                path.regex = new RegExp( '^' + path.path.replace( patternPath , Ink.bind( path2regex , this ) ) );

                this._prepareRegex( path.paths || [ ] );
            }

            return this;
        } ,

        _handlerListenner : function( e ) {
            if ( e && e.type === 'popstate' && !this._loaded ) {
                this._loaded = true;

                return ;
            }

            var success = this._verifyURL( );
            if ( !success ) {
                this._runCB( this._options.onFail , this , this.getPath( ) );
            }

            this._runCB( this._options.onChange , this , this.getPath( ) );
        } ,
        _verifyURL : function( url , paths , prevArgs ) {
            paths = paths || this._options.paths;

            prevArgs = prevArgs || [ ];

            url = ( url !== undefined ? url : decodeURI( this.getPath( ) ) ).replace( /^\/|\/$/g , '' );
            var nextUrl = url;

            var catched = false;

            var args;
            var joinArgs;
            var executePath;

            var nextUrlRplCB = function( ) {
                args = Array.prototype.slice.call( arguments , 1 , arguments.length - 2 );

                joinArgs = args.join( );

                executePath = path;

                catched = true;

                return '';
            };

            for ( var i = 0, l = paths.length; i < l; i++ ) {
                var path = paths[ i ];

                if ( !catched ) {
                    nextUrl = url.replace( path.regex , nextUrlRplCB );

                    if ( !catched && 'state' in path ) {
                        this._cleanState( path );
                    }
                } else if ( 'state' in path ) {
                    this._cleanState( path );
                }
            }

            if ( executePath ) {
                nextUrlRplCB = function( ) {
                    args = args.concat( Array.prototype.slice.call( arguments , 1 , arguments.length - 2 ) );

                    joinArgs = args.join( );

                    return '';
                };

                while ( executePath.repeat ) {
                    var _ = ( nextUrl = ( _ !== undefined ? _ : nextUrl ).replace( /^\/|\/$/g , '' ) );

                    _ = nextUrl.replace( executePath.regex , nextUrlRplCB );

                    if ( executePath.repeat === '+' && _ === nextUrl || typeof executePath.repeat === 'number' && --executePath.repeat === 1 ) {
                        nextUrl = _.replace( /^\/|\/$/g , '' );

                        break;
                    }
                }

                var nArr = args.concat( [ prevArgs ] );
                nArr.push( nextUrl );

                if ( executePath.state !== joinArgs ) {
                    if ( 'state' in executePath ) {
                        this._runCB( executePath.change , executePath.arr.concat( nArr ) ) !== false && this._runCB( executePath.exit , executePath.arr );
                    }

                    executePath.state = joinArgs;

                    this._runCB( executePath.init , nArr ) !== false && this._runCB( executePath.enter , nArr );
                }

                executePath.arr = nArr;

                delete executePath.init;

                var success;

                if ( executePath.paths ) {
                    success = this._verifyURL( nextUrl , executePath.paths , prevArgs.concat( args ) );
                }

                if ( !success && executePath.paths || nextUrl && !executePath.paths ) {
                    return !executePath.fail || executePath.fail.call( this , nextUrl ) ? false : true;
                } else {
                    return true;
                }
            }

            return false;
        } ,

        _cleanState : function( path ) {
            for ( var i = 0, l = ( path.paths || [ ] ).length; i < l; i++ ) {
                var _path = path.paths[ i ];
                if ( 'state' in _path ) {
                    this._cleanState( _path );

                    break;
                }
            }

            this._runCB( path.exit , path.arr );

            delete path.state;
            delete path.arr;
        } ,

        _runCB : function( cb , arr ) {
            if ( typeof cb === 'function' ) {
                return cb.apply( this , arr instanceof Array ? arr : Array.prototype.slice.call( arguments , 1 , Infinity ) );
            }

            return true;
        }
    };

    return Router;
});
