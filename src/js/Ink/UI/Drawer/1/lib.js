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
            drawerWidth: '300',
            duration: '200',
            easing: 'cubic-bezier(0.500, 0.000, 0.500, 1.000)',
            mode: 'over'
          }, 
          arguments[0] || {}
        );

        this._isOpen = false;
        this._direction = undefined;

        this._handlers = {
            click:   Ink.bindEvent(this._onClick, this)
        };
        this._addCssTransitions();
        this._addEvents();
      },

      _addCssTransitions: function(){
        var content = Ink.s(this._options.parentSelector + ' ' + this._options.contentDrawer);
        var left = Ink.s(this._options.parentSelector + ' ' + this._options.leftDrawer);
        var right = Ink.s(this._options.parentSelector + ' ' + this._options.rightDrawer);   

        var transition = 'transition: all ' + this._options.duration + 'ms ' + this._options.easing;

        Css.setStyle(content, transition);
        Css.setStyle(left, transition);
        Css.setStyle(right, transition);
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
        Event.on(document.body, 'click', this._options.rightTrigger + ',' + this._options.leftTrigger + ',' + this._options.contentDrawer, this._handlers.click);
      },

      openDrawer: function(direction) {
        
        var content = Ink.s(this._options.contentDrawer);
        this._isOpen = true;
        this._direction = direction;

        if(direction == 'left') {
          var open = Ink.s(this._options.leftDrawer);
        } else if (direction == 'right') {
          var open = Ink.s(this._options.rightDrawer);
        }

        if(this._options.mode == 'push') {
            Css.addClassName(content,'open-' + direction);
            Css.addClassName(open,'open');
        } else if (this._options.mode == 'over') {
          Css.addClassName(open,'open');
        }


      },

      closeDrawer: function(direction) {
        
        var content = Ink.s(this._options.contentDrawer);
        this._isOpen = false;
        this._direction = direction;

        if(direction == 'left') {
          var close = Ink.s(this._options.leftDrawer);
        } else if (direction == 'right') {
          var close = Ink.s(this._options.rightDrawer);
        }

        if(this._options.mode == 'push') {
            Css.addClassName(content,'open-' + direction);
            Css.removeClassName(close,'open');
        } else if (this._options.mode == 'over'){
          Css.removeClassName(close,'open');
        }

      },
    };

    return Drawer;

});