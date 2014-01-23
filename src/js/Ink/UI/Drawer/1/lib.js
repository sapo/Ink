Ink.createModule('Ink.UI.Drawer', '1', ['Ink.UI.Common_1', 'Ink.Dom.Loaded_1', 'Ink.Dom.Selector_1', 'Ink.Dom.Element_1', 'Ink.Dom.Event_1', 'Ink.Dom.Css_1'], function(Common, Loaded, Selector, Element, Event, Css) { 

  var Drawer = function(options) {
    this._init(options);
  }

  Drawer.prototype = {

    _init: function () {

      this._options = Ink.extendObj(
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
        afterTransition: Ink.bindEvent(this._afterTransition, this)
      };
      this._delay = 10;
      this._addEvents();
    },

    _onClick: function(ev){                

      if(Selector.matchesSelector(ev.currentTarget,this._options.leftTrigger)){
        if(this._isOpen) {
          this.close('left');
          this.closeDrawer('left');
        } else {
          this.open('left');
          this.openDrawer('left');
        }            
      } else if(Selector.matchesSelector(ev.currentTarget,this._options.rightTrigger)){
        if(this._isOpen) {
          this.close('right');
          this.closeDrawer('right');
        } else {
          this.open('right');
          this.openDrawer('right');
        }          
      } else if(Selector.matchesSelector(ev.currentTarget,this._options.contentDrawer)){
        if(this._options.closeOnContentClick && this._isOpen) {
          this.close(this._direction);
          this.closeDrawer(this._direction);
        }
      }
    },

    _afterTransition: function(){
      if(!this._isOpen){
        if(this._direction == 'left') {
          Css.removeClassName(this._leftDrawer,'show');
        } else {
          Css.removeClassName(this._rightDrawer,'show');            
        }
      }
    },

    _addEvents: function(){
      Event.on(document.body, 'click', this._triggers, this._handlers.click); 
    },

    open: function(direction) {

      var content = Ink.s(this._options.contentDrawer);
      this._isOpen = true;
      this._direction = direction;

      if(direction == 'left') {
        var open = Ink.s(this._options.leftDrawer);
      } else if (direction == 'right') {
        var open = Ink.s(this._options.rightDrawer);
      }

      Css.addClassName(open,'show');
      setTimeout(Ink.bind(function(){         
        Css.addClassName(document.body, this._options.mode + ' '  + direction);
      },this), this._delay);

    },

    close: function(direction) {        
      Event.one(document.body, 'transitionend oTransitionEnd transitionend webkitTransitionEnd', this._handlers.afterTransition);
      Css.removeClassName(document.body, this._options.mode + ' ' + this._direction);
    }

  };

  return Drawer;

});