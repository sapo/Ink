Ink.requireModules(['Ink.Dom.Event_1', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1', 'Ink.Dom.Browser_1'], function (InkEvent, InkElement, Selector, Browser) {

    module('custom types', {
        setup: function(){
            var self = this;
            globalSetUp.call(this);
            this.testRemove = function (done, removeFn) {
              var html    = document.documentElement;
              var foo     = this.byId('foo');
              var trigger = self.trigger();
              var meSpy   = self.spy();
              var mlSpy   = self.spy();

              trigger.after(function () {
                equal(meSpy.callCount, 1, 'removes mouseenter event');
                equal(mlSpy.callCount, 1, 'removes mouseleave event');
                done();
            });

              InkEvent.on(foo, 'mouseenter', trigger.wrap(meSpy));
              InkEvent.on(foo, 'mouseleave', trigger.wrap(mlSpy));

              Syn.trigger('mouseover', { relatedTarget: html }, foo);
              Syn.trigger('mouseout', { relatedTarget: html }, foo);

              removeFn(foo, trigger.wrapped(meSpy), trigger.wrapped(mlSpy));

              Syn.trigger('mouseover', { relatedTarget: html }, foo);
              Syn.trigger('mouseout', { relatedTarget: html }, foo);
          }
      },
      teardown: globalTearDown
    });
    asyncTest('mouseenter/mouseleave should wrap simple mouseover/mouseout', function(){
        var html    = document.documentElement;
        var foo     = this.byId('foo');
        var bar     = this.byId('bar');
        var bang    = this.byId('bang');
        var trigger = this.trigger();
        var meSpy   = this.spy();
        var mlSpy   = this.spy();

        trigger.after(function () {
          equal(meSpy.callCount, 1, 'removes mouseenter event');
          equal(mlSpy.callCount, 1, 'removes mouseleave event');
          ok(meSpy.firstCall.args[0], 'has event object argument');
          ok(mlSpy.firstCall.args[0], 'has event object argument');
          strictEqual(meSpy.firstCall.args[0].currentTarget, foo, 'currentTarget property of event set correctly');
          strictEqual(mlSpy.firstCall.args[0].currentTarget, foo, 'currentTarget property of event set correctly');
          start();
        }, 50);

        InkEvent.on(foo, 'mouseenter', trigger.wrap(meSpy));
        InkEvent.on(foo, 'mouseleave', trigger.wrap(mlSpy));

        // relatedTarget is where the mouse came from for mouseover and where it's going to in mouseout
        Syn.trigger('mouseover', { relatedTarget: html }, foo);
        Syn.trigger('mouseover', { relatedTarget: foo } , bar);
        Syn.trigger('mouseover', { relatedTarget: bar } , bang);
        Syn.trigger('mouseout' , { relatedTarget: bar } , bang);
        Syn.trigger('mouseout' , { relatedTarget: foo } , bar);
        Syn.trigger('mouseout' , { relatedTarget: html }, foo);
    });
    asyncTest('custom events should be removable', function(){
        this.testRemove(start, function(foo, me, ml){
            InkEvent.remove(foo, 'mouseenter');
            InkEvent.remove(foo, 'mouseleave');
        })
    });
    asyncTest('custom events should be removable by type+handler', function(){
        this.testRemove(start, function(foo, me, ml){
            InkEvent.remove(foo, 'mouseenter', me);
            InkEvent.remove(foo, 'mouseleave', ml);
        })
    });
    asyncTest('custom events should be removable', function(){
        var html    = document.documentElement;
        var foo     = this.byId('foo');
        var bar     = this.byId('bar');
        var bang    = this.byId('bang');
        var trigger = this.trigger();
        var meSpy   = this.spy();
        var mlSpy   = this.spy();

        trigger.after(function () {
          equal(meSpy.callCount, 1, 'removes mouseenter event');
          equal(mlSpy.callCount, 1, 'removes mouseleave event');
          ok(meSpy.firstCall.args[0], 'has event object argument');
          ok(mlSpy.firstCall.args[0], 'has event object argument');
          strictEqual(meSpy.firstCall.args[0].currentTarget, bang, 'currentTarget property of event set correctly');
          strictEqual(mlSpy.firstCall.args[0].currentTarget, bang, 'currentTarget property of event set correctly');
          start();
        }, 50)

        InkEvent.on(foo, 'mouseenter', '.bang', trigger.wrap(meSpy));
        InkEvent.on(foo, 'mouseleave', '.bang', trigger.wrap(mlSpy));

        Syn.trigger('mouseover', { relatedTarget: html }, foo)
        Syn.trigger('mouseover', { relatedTarget: foo } , bar)
        Syn.trigger('mouseover', { relatedTarget: bar } , bang)
        Syn.trigger('mouseout' , { relatedTarget: bar } , bang)
        Syn.trigger('mouseout' , { relatedTarget: foo } , bar)
        Syn.trigger('mouseout' , { relatedTarget: html }, foo)
    });
});