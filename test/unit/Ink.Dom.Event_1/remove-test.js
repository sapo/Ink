Ink.requireModules(['Ink.Dom.Event_1', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1', 'Ink.Dom.Browser_1'], function (InkEvent, InkElement, Selector, Browser) {

    module('remove', {
        setup: globalSetUp,
        teardown: globalTearDown
    });

    asyncTest('should return the element passed in', function( ){
        var el = this.byId('input');
        var handler = function(){};
        var returnedOn = InkEvent.on(el, 'click', handler);
        var returnedOff = InkEvent.off(el, 'click', handler);
        equal(returnedOff, el, 'returns the element passed in');
        start();
    });

    asyncTest('should be able to remove a single event', function( ){
        var el = this.byId('foo');
        var calls = 0;
        var trigger = this.trigger();
        var handler = trigger.wrap(function(){
            calls++;
            InkEvent.remove(el, 'click', handler);
            Syn.click(el);
        });

        trigger.after(function(){
            equal(calls, 1, 'remove a single event');
            start();
        });

        InkEvent.on(el, 'click', handler);
        Syn.click(el);
    });

    asyncTest('should be able to remove multiple events with an object literal', function( ){
        var el = this.byId('input');
        var calls = 0;
        var trigger = this.trigger();
        var handler1 = function() {
            calls++
            InkEvent.remove(el, {
                click   : trigger.wrapped(handler1)
              , keydown : trigger.wrapped(handler2)
            })
            Syn.click(el)
            Syn.key('j', el)
        };
        var handler2 = this.spy();
        var handler3 = this.spy();

        trigger.after(function(){
            equal(calls, 1, 'remove events with object literal');
            ok(!handler2.called, 'correct handler properly removed');
            equal(handler3.callCount, 1, 'non-matching handler should not be removed');
            start();
        }, 50);

        InkEvent.on(el, 'click', trigger.wrap(handler1));
        InkEvent.on(el, 'keydown', trigger.wrap(handler2));
        InkEvent.on(el, 'keydown', trigger.wrap(handler3));

        Syn.click(el);
    });

    asyncTest('should be able to remove all events of a specific type', function( ){
        var el = this.byId('input');
        var calls = 0;
        var trigger = this.trigger();
        var handler1 = this.spy();
        var handler2 = function(){
            calls++;
            InkEvent.remove(el, 'click');
            Syn.click(el);
        }

        trigger.after(function(){
            equal(calls, 1, 'removes all events of a specific type');
            equal(handler1.callCount, 1, 'removes all events of a specific type');
            start();
        }, 50);

        InkEvent.on(el, 'click', trigger.wrap(handler1));
        InkEvent.on(el, 'click', trigger.wrap(handler2));

        Syn.click(el);

    });
    asyncTest('should be able to remove all events of a specific type (multiple)', function( ){
        var el = this.byId('input');
        var calls = 0;
        var trigger = this.trigger();
        var handler1 = this.spy();
        var handler2 = function(){
            calls++;
            InkEvent.remove(el, 'mousedown mouseup');
            Syn.click(el);
        }

        trigger.after(function(){
            equal(calls, 1, 'removes all events of a specific type');
            equal(handler1.callCount, 1, 'removes all events of a specific type');
            start();
        }, 50);

        InkEvent.on(el, 'mousedown', trigger.wrap(handler1));
        InkEvent.on(el, 'mouseup', trigger.wrap(handler2));

        Syn.click(el);

    });

    asyncTest('should be able to remove all events', function( ){
        var el = this.byId('input');
        var calls = 0;
        var trigger = this.trigger();
        var handler1 = function(){
            calls++;
            InkEvent.remove(el);
            Syn.click(el);
            Syn.key('j', el);
        }
        var handler2 = this.spy();

        trigger.after(function(){
            equal(calls, 1, 'removes all events');
            equal(handler2.callCount, 0, 'removes all events');
            start();
        }, 50);

        InkEvent.on(el, 'click', trigger.wrap(handler1));
        InkEvent.on(el, 'keydown', trigger.wrap(handler2));

        Syn.click(el);

    });

    asyncTest('should only remove events of specified type', function( ){
        var el = this.byId('input');
        var calls = 0;
        var trigger = this.trigger();
        var handler1 = this.spy();
        var handler2 = function(e){
            calls++;
            InkEvent.remove(el, e.type);
        }

        trigger.after(function(){
            equal(calls, 2, 'removes all events of a specific type');
            equal(handler1.callCount, 2, 'removes all events of a specific type');
            start();
        }, 50);

        InkEvent.on(el, 'click', trigger.wrap(handler1));
        InkEvent.on(el, 'keyup', trigger.wrap(handler1));

        InkEvent.on(el, 'click', trigger.wrap(handler2));
        InkEvent.on(el, 'keyup', trigger.wrap(handler2));

        Syn.click(el);
        Syn.key(el, 'f');
        Syn.click(el);
        Syn.key(el, 'f');

    });

    asyncTest('should only remove events for specified handler', function( ){
        var el = this.byId('input');
        var trigger = this.trigger();
        var handler1 = this.spy();
        var handler2 = this.spy();

        trigger.after(function(){
            equal(handler1.callCount, 0, 'removes all events of a specific handler');
            equal(handler2.callCount, 2, 'removes all events of a specific handler');
            start();
        }, 50);

        InkEvent.on(el, 'click', trigger.wrap(handler1));
        InkEvent.on(el, 'keyup', trigger.wrap(handler1));
        InkEvent.on(el, 'click', trigger.wrap(handler2));
        InkEvent.on(el, 'keyup', trigger.wrap(handler2));
        InkEvent.remove(el, trigger.wrapped(handler1));

        Syn.click(el);
        Syn.key(el, 'f');

    });

    asyncTest('should be able to remove all events, including namespaced', function( ){
        var el = this.byId('input');
        var handler1 = this.spy();
        var handler2 = this.spy();

        InkEvent.on(el, 'click', handler1)
        InkEvent.on(el, 'click.foo', handler1)
        InkEvent.on(el, 'click', handler1)
        InkEvent.on(el, 'keyup', handler2)
        InkEvent.on(el, 'keyup.bar', handler2)
        InkEvent.on(el, 'keyup', handler2)
        InkEvent.remove(el)

        Syn.click(el);
        Syn.key(el, 'f');

        defer(function () {
          equal(handler1.callCount, 0, 'removes all events');
          equal(handler2.callCount, 0, 'removes all events');
          start();
        }, 100)

    });

    asyncTest('should be able to remove all events of a certain namespace', function( ){
        var el = this.byId('input');
        var calls = 0;
        var trigger = this.trigger();
        var handler1 = function () {
            calls++
            InkEvent.remove(el, '.foo')
            Syn.click(el)
            Syn.key('j', el)
        };
        var handler2 = this.spy();
        var handler3 = this.spy();

        trigger.after(function(){
            equal(calls, 1, 'removes all events of a certain namespace');
            equal(handler2.callCount, 0, 'removes all events of a certain namespace');
            equal(handler3.callCount, 2, 'removes all events of a certain namespace');
            start();
        }, 50);

        InkEvent.on(el, 'click.foo', trigger.wrap(handler1));
        InkEvent.on(el, 'keydown.foo', trigger.wrap(handler2));
        InkEvent.on(el, 'click.bar', trigger.wrap(handler3));

        Syn.click(el);
    });

    asyncTest('should only remove event if the remove namespaces is within the event namespace or if the event namespace is within the remove namespace', function(){
        var el = this.byId('foo');
        var trigger = this.trigger();
        var spy = this.spy();

        trigger.after(function(){
            equal(spy.callCount, 4, 'calls on appropriate namespaces');
            start();
        });

        InkEvent.remove(el);
        InkEvent.on(el, 'fat.test1.foo.ded fat.test2.foo fat.test1.foo', trigger.wrap(spy));
        InkEvent.fire(el, 'fat.test1.ded', ['1']);
        InkEvent.fire(el, 'fat.test2', ['2']);
        InkEvent.remove(el, '.foo.ded');
        InkEvent.fire(el, 'fat.foo', ['3']);
    });
});

