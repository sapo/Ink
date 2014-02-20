Ink.createModule('Ink.UI.Drawer', '1', ['Ink.UI.Common_1', 'Ink.Dom.Loaded_1', 'Ink.Dom.Selector_1', 'Ink.Dom.Element_1', 'Ink.Dom.Event_1', 'Ink.Dom.Css_1'], function(Common, Loaded, Selector, Element, Event, Css) { 

  var Drawer = function(options) {
    this._init(options);
  }

  Drawer.prototype = {

    _init: function () {

      this._name = 'Ink Drawer',

      this._options = Common.options (
      {
        parentSelector: ['String','.ink-drawer'],
        leftDrawer: ['String','.left-drawer'],
        leftTrigger: ['String','.left-drawer-trigger'],
        rightDrawer: ['String','.right-drawer'],
        rightTrigger: ['String','.right-drawer-trigger'],
        contentDrawer: ['String','.content-drawer'],
        closeOnContentClick: ['Boolean',true],
        mode: ['String','push'],
        sides: ['String','both']
      }, 

      arguments[0] || {} );

      // make sure we have the required elements acording to the config options

      this._contentDrawers = Ink.ss(this._options.contentDrawer);

      this._leftDrawer = Ink.s(this._options.leftDrawer);
      this._leftTriggers = Ink.ss(this._options.leftTrigger);

      this._rightDrawer = Ink.s(this._options.rightDrawer);
      this._rightTriggers = Ink.ss(this._options.rightTrigger);



      if(this._contentDrawers.length == 0) {
        console.warn( this._name + ': Could not find any "' + this._options.contentDrawer + '" elements on this page. Please make sure you have at least one.' );
      }

      switch (this._options.sides) {

        case 'both':

        if( !this._leftDrawer ){
          console.warn( this._name + ': Could not find the "' + this._options.leftDrawer + '" element on this page. Please make sure it exists.' );            
        } 

        if(this._leftTriggers.length == 0){
          console.warn( this._name + ': Could not find the "' + this._options.leftTrigger + '" element on this page. Please make sure it exists.' );            
        } 

        if( !this._rightDrawer ){
          console.warn( this._name + ': Could not find the "' + this._options.rightDrawer + '" element on this page. Please make sure it exists.' );            
        } 

        if( this._rightTriggers.length == 0 ){
          console.warn( this._name + ': Could not find the "' + this._options.rightTrigger + '" element on this page. Please make sure it exists.' );            
        }
        this._triggers =  this._options.leftTrigger + ', ' + this._options.rightTrigger + ', ' + this._options.contentDrawer;
        break;

        case 'left':
        if( !this._leftDrawer ){
          console.warn( this._name + ': Could not find the "' + this._options.leftDrawer + '" element on this page. Please make sure it exists.' );            
        } 

        if(this._leftTriggers.length == 0){
          console.warn( this._name + ': Could not find the "' + this._options.leftTrigger + '" element on this page. Please make sure it exists.' );            
        }
        this._triggers = this._options.leftTrigger + ', ' + this._options.contentDrawer;
        break;

        case 'right':
        if( !this._rightDrawer ){
          console.warn( this._name + ': Could not find the "' + this._options.rightDrawer + '" element on this page. Please make sure it exists.' );            
        } 

        if( this._rightTriggers.length == 0 ){
          console.warn( this._name + ': Could not find the "' + this._options.rightTrigger + '" element on this page. Please make sure it exists.' );            
        } 
        this._triggers = this._options.rightTrigger + ', ' + this._options.contentDrawer;
        break;
      }


      this._isOpen = false;
      this._direction = undefined;

      this._handlers = {
        click:   Ink.bindEvent(this._onClick, this),
        afterTransition: Ink.bindEvent(this._afterTransition, this),
        touchmove: Ink.bindEvent(this._onTouchMove, this),
      };

      this._delay = 10;
      this._addEvents();

    },

    _onClick: function(ev){                

      if(Selector.matchesSelector(ev.currentTarget,this._options.leftTrigger)){
        if(this._isOpen) {
          this.close();
        } else {
          this.open('left');
        }            
      } else if(Selector.matchesSelector(ev.currentTarget,this._options.rightTrigger)){
        if(this._isOpen) {
          this.close();
        } else {
          this.open('right');
        }          
      } else if(Selector.matchesSelector(ev.currentTarget,this._options.contentDrawer)){
        if(this._options.closeOnContentClick && this._isOpen) {
          this.close();
        }
      }
    },

    _onTouchMove: function (ev) {

        // console.log(' you touched me');
        console.log(ev.currentTarget);

        if( this._isOpen ) {
          console.log('Drawer is open');


          if( ! Selector.matchesSelector(ev.currentTarget,'.left-drawer, .right-drawer') ){        
            ev.preventDefault();
            // console.log('Content drawer');
            console.log(ev.currentTarget);
          }

          // if( Selector.matchesSelector(ev.currentTarget, '.left-drawer') ) {
          //   // console.log('Body');
          //   // console.log(ev.currentTarget);
          // }

        }
    },

    _afterTransition: function() {
      if(!this._isOpen){
        if(this._direction == 'left') {
          Css.removeClassName(this._leftDrawer,'show');
        } else {
          Css.removeClassName(this._rightDrawer,'show');            
        }
      }
    },

    _addEvents: function () {
      Event.on( document.body, 'click', this._triggers, this._handlers.click);

      Event.on( document, 'touchmove', '.left-drawer, .right-drawer, body', this._handlers.touchmove );

      // for ( var i = 0; i < this._contentDrawers.length; i++ ) {
      //   Event.on(this._contentDrawers[i],'touchmove',this._handlers.touchmove);
      // }

      // var that = this;

      // Event.on(this._leftDrawer,'touchmove', function(ev){

      //   var height = that._leftDrawer.offetHeight;
      //   var scrollHeight = that._leftDrawer.scrollHeight;
      //   var offsetTop = that._leftDrawer.scrollTop;

      //   console.log('debug');

      //   if((offsetTop + height) >= scrollHeight ) {
      //     Event.stop(ev);
      //     console.log('debug');
      //   }

      // });

      // Event.on(document,'touchmove', this._handlers.touchmove);
    },

    open: function(direction) {

      this._isOpen = true;
      this._direction = direction;

      if(direction == 'left') {
        var open = this._leftDrawer;
      } else if (direction == 'right') {
        var open = this._rightDrawer;
      }

      Css.addClassName(open,'show');
      setTimeout(Ink.bind(function(){         
        Css.addClassName(document.body, this._options.mode + ' '  + direction);
      },this), this._delay);

    },

    close: function() {
      this._isOpen = false;   
      Event.one(document.body, 'transitionend oTransitionEnd transitionend webkitTransitionEnd', this._handlers.afterTransition);
      Css.removeClassName(document.body, this._options.mode + ' ' + this._direction);
    }

  };

  return Drawer;

});