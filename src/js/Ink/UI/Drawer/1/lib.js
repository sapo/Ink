Ink.createModule('Ink.UI.Drawer', '1', ['Ink.UI.Common_1', 'Ink.Dom.Loaded_1', 'Ink.Dom.Selector_1', 'Ink.Dom.Element_1', 'Ink.Dom.Event_1', 'Ink.Dom.Css_1'], function(Common, Loaded, Selector, Element, Event, Css) { 
    
    var Drawer = function(options) {
      this._init(options);
    }

    Drawer.prototype = {

      _init: function () {

        this._options = Ink.extendObj(
          {
            parentSelector: '.ink-drawer',
            leftDrawer: '.left-drawer',
            leftTrigger: '.left-drawer-trigger',
            rightDrawer: '.right-drawer',
            rightTrigger: '.right-drawer-trigger',
            contentDrawer: '.content-drawer',
            closeOnContentClick: true,
            mode: 'push',
            sides: 'both',
          }, 
          arguments[0] || {}
        );

        this._isOpen = false;
        this._direction = undefined;

        this._handlers = {
            click:   Ink.bindEvent(this._onClick, this)
        };
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

        if(this._options.mode == 'push') {            
            Css.addClassName(open,'show');
            setTimeout(function(){
                for (var i=0; i<content.length; i++){
                  Css.addClassName(content[i],'open-' + direction);
                  content[i].style.overflowY = 'auto';
                  content[i].style.overflowY = '';
                }                
                Css.addClassName(open,'open');
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

        if(this._options.mode == 'push') {
          Css.removeClassName(close,'open');
          for (var i=0; i<content.length; i++){
            Css.removeClassName(content[i],'open-' + direction);
          }
          setTimeout(function(){
            Css.removeClassName(close,'show');            
          },this._options.duration);
        } else if (this._options.mode == 'over'){
          Css.removeClassName(close,'open');
          setTimeout(function(){                
            Css.removeClassName(close,'show');              
          },this._options.duration);
        }

      },
    };

    return Drawer;

});