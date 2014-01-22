Ink.createModule('Ink.UI.Drawer', '1', ['Ink.UI.Common_1', 'Ink.Dom.Loaded_1', 'Ink.Dom.Selector_1', 'Ink.Dom.Element_1', 'Ink.Dom.Event_1', 'Ink.Dom.Css_1'], function(Common, Loaded, Selector, Element, Event, Css) { 
    
    var Drawer = function(options) {
      this._init(options);
    }

    Drawer.prototype = {

      _init: function () {

        this._name = 'Ink Drawer';

        this._options = Ink.extendObj(
          {
            parentSelector: '.ink-drawer',
            leftDrawer: '.left-drawer',
            leftTrigger: '.left-drawer-trigger',
            rightDrawer: '.right-drawer',
            rightTrigger: '.right-drawer-trigger',
            contentDrawer: '.content-drawer',
            closeOnContentClick: true,
            duration: 300,
            mode: 'push',
            sides: 'both',
          }, 
          arguments[0] || {}
        );

        // make sure we have the required elements acording to the config options

        if(Ink.ss(this._options.contentDrawer).length == 0) {
          console.warn( this._name + ': Could not find any "' + this._options.contentDrawer + '" elements on this page. Please make sure you have at least one.' );
        }

        if ( this._options.sides == 'both' ) {
          if(!Ink.s(this._options.leftDrawer)){
            console.warn( this._name + ': Could not find the "' + this._options.leftDrawer + '" element on this page. Please make sure it exists.' );            
          }
          if(!Ink.s(this._options.leftDrawerTrigger)){
            console.warn( this._name + ': Could not find the "' + this._options.leftTrigger + '" element on this page. Please make sure it exists.' );            
          }
          if(!Ink.s(this._options.rightDrawer)){
            console.warn( this._name + ': Could not find the "' + this._options.rightDrawer + '" element on this page. Please make sure it exists.' );            
          }
          if(!Ink.s(this._options.rightDrawerTrigger)){
            console.warn( this._name + ': Could not find the "' + this._options.rightTrigger + '" element on this page. Please make sure it exists.' );            
          }
        } else if ( this._options.sides == 'left' ) {
          if(!Ink.s(this._options.leftDrawer)){
            console.warn( this._name + ': Could not find the "' + this._options.leftDrawer + '" element on this page. Please make sure it exists.' );            
          }
          if(!Ink.s(this._options.leftTrigger)){
            console.warn( this._name + ': Could not find the "' + this._options.leftTrigger + '" element on this page. Please make sure it exists.' );            
          }
        } else if ( this._options.sides == 'right' ) {
          if(!Ink.s(this._options.rightDrawer)){
            console.warn( this._name + ': Could not find the "' + this._options.rightDrawer + '" element on this page. Please make sure it exists.' );            
          }
          if(!Ink.s(this._options.rightTrigger)){
            console.warn( this._name + ': Could not find the "' + this._options.rightTrigger + '" element on this page. Please make sure it exists.' );            
          }
        }


        this._isOpen = false;
        this._direction = undefined;

        this._handlers = {
            click:   Ink.bindEvent(this._onClick, this),
            afterTransition: Ink.bindEvent(this._afterTransition, this)
        };

        this._transitionEvent = this._whichTransitionEvent();
        this._addEvents();
        this._delay = 10;
      },

      _onClick: function(ev){
        
        if(Selector.matchesSelector(ev.currentTarget,this._options.leftTrigger)){
          if(this._isOpen) {
            this.closeDrawer('left');
          } else {
            this.openDrawer('left');
          }            
        } else if(Selector.matchesSelector(ev.currentTarget,this._options.rightTrigger)){
          if(this._isOpen) {
            this.closeDrawer('right');
          } else {
            this.openDrawer('right');
          }          
        } else if(Selector.matchesSelector(ev.currentTarget,this._options.contentDrawer)){
          if(this._options.closeOnContentClick && this._isOpen) {
            this.closeDrawer(this._direction);
          }
        }        
      },

      _whichTransitionEvent: function (){
          var t, r;
          var el = document.createElement('span');
          var transitions = {
            'transition':'transitionend',
            'OTransition':'oTransitionEnd',
            'MozTransition':'transitionend',
            'WebkitTransition':'webkitTransitionEnd'
          }

          for(t in transitions){
              if( el.style[t] !== undefined ){
                  return transitions[t];
              }
          }
      },

      _afterTransition: function(){
        if(this._isOpen){
          var allDrawers = Ink.ss(this._options.contentDrawer + ', ' + this._options.leftDrawer + ', ' + this._options.leftDrawer);
          for(var i = 0; i < allDrawers.length; i++ ){
            Css.removeClassName(allDrawers[i], 'move');
          }
          Css.addClassName(document.body,'open');        
        } else {
          var allDrawers = Ink.ss(this._options.leftDrawer + ', ' + this._options.leftDrawer);
          for(var i = 0; i < allDrawers.length; i++ ){
            Css.removeClassName(allDrawers[i], 'show');
          }
          Css.removeClassName(document.body, 'open');        
        }
      },
      
      _addEvents: function(){
        if(this._options.sides == 'both') {          
          Event.on(document.body, 'click', this._options.rightTrigger + ',' + this._options.leftTrigger + ',' + this._options.contentDrawer, this._handlers.click);          
        } else if (this._options.sides == 'left') {
          Event.on(document.body, 'click', this._options.leftTrigger + ',' + this._options.contentDrawer, this._handlers.click);
        } else if (this._options.sides == 'right') {
          Event.on(document.body, 'click', this._options.rightTrigger + ',' + this._options.contentDrawer, this._handlers.click);
        }
      },

      openDrawer: function(direction) {
        
        var content = Ink.ss(this._options.contentDrawer);
        this._isOpen = true;
        this._direction = direction;

        if(direction == 'left') {
          var open = Ink.s(this._options.leftDrawer);
        } else if (direction == 'right') {
          var open = Ink.s(this._options.rightDrawer);
        }

        Event.one(document.body, this._transitionEvent, this._handlers.afterTransition);        

        if(this._options.mode == 'push') {            
            Css.addClassName(open,'show');
            setTimeout(function(){
                Css.addClassName(open,'open');
                for (var i=0; i<content.length; i++){
                  Css.addClassName(content[i],'open-' + direction);
                }                                
            },this._delay);
        } else if (this._options.mode == 'over') {
          Css.addClassName(open,'show');
          setTimeout(function(){                
            Css.addClassName(open,'open');
          },this._delay);
        }
      },

      closeDrawer: function(direction) {
        
        var content = Ink.ss(this._options.contentDrawer);
        this._isOpen = false;
        this._direction = direction;

        if(direction == 'left') {
          var close = Ink.s(this._options.leftDrawer);
        } else if (direction == 'right') {
          var close = Ink.s(this._options.rightDrawer);
        }
        
        Event.one(document.body, this._transitionEvent, this._handlers.afterTransition);

        if(this._options.mode == 'push') {
          Css.removeClassName(close,'open');
          for (var i=0; i<content.length; i++){        
            Css.removeClassName(content[i],'open-' + direction);
          }
        } else if (this._options.mode == 'over'){
          Css.removeClassName(close,'open');
        }
      },
    };

    return Drawer;

});