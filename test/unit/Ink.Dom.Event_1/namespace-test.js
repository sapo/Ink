Ink.requireModules(['Ink.Dom.Event_1', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1', 'Ink.Dom.Browser_1'], function (InkEvent, InkElement, Selector, Browser) {
    module('namespace', {
        setup: globalSetUp,
        teardown: globalTearDown
    });

    asyncTest('should be able to name handlers', function(){
        var el1 = this.byId('foo');
        var trigger = this.trigger();
        var spy = this.spy();

        trigger.after(function(){
            equal(spy.callCount, 1, 'triggered click event');
            start();
        });

        InkEvent.on(el1, 'click.fat', trigger.wrap(spy));

        Syn.click(el1);
    });
    
    asyncTest('should be able to add multiple handlers under the same namespace to the same element', function(){
        var el1 = this.byId('foo');
        var trigger = this.trigger();
        var spy1 = this.spy();
        var spy2 = this.spy();

        trigger.after(function(){
            equal(spy1.callCount, 1, 'triggered click event');
            equal(spy2.callCount, 1, 'triggered click event');
            start();
        });

        InkEvent.on(el1, 'click.fat', trigger.wrap(spy1));
        InkEvent.on(el1, 'click.fat', trigger.wrap(spy2));

        Syn.click(el1);
    });
    
    asyncTest('should be able to fire an event without handlers', function(){
        var el1 = this.byId('foo');
        
        InkEvent.fire(el1, 'click.fat');

        ok(true, 'fire namespaced event with no handlers (no exception)');
        start();
    });
    
    asyncTest('should be able to target namespaced event handlers with fire', function(){
        var el1 = this.byId('foo');
        var trigger = this.trigger();
        var spy1 = this.spy();
        var spy2 = this.spy();

        trigger.after(function(){
            equal(spy1.callCount, 1, 'triggered click event (namespaced)');
            equal(spy2.callCount, 0, 'should not trigger click event (plain)');
            start();
        });

        InkEvent.on(el1, 'click.fat', trigger.wrap(spy1));
        InkEvent.on(el1, 'click', trigger.wrap(spy2));

        InkEvent.fire(el1, 'click.fat');
    });
    
    asyncTest('should not be able to target multiple namespaced event handlers with fire', function(){
        var el1 = this.byId('foo');
        var trigger = this.trigger();
        var spy1 = this.spy();
        var spy2 = this.spy();
        var spy3 = this.spy();


        defer(function(){
            equal(spy1.callCount, 0, 'should not trigger click event (namespaced)');
            equal(spy2.callCount, 0, 'should not trigger click event (namespaced)');
            equal(spy3.callCount, 0, 'should not trigger click event (plain)');
            start();
        });

        InkEvent.on(el1, 'click.fat', trigger.wrap(spy1));
        InkEvent.on(el1, 'click.ded', trigger.wrap(spy2));
        InkEvent.on(el1, 'click', trigger.wrap(spy3));

    });
    
    asyncTest('should be able to remove handlers based on name', function(){
        var el1 = this.byId('foo');
        var trigger = this.trigger();
        var spy1 = this.spy();
        var spy2 = this.spy();

        trigger.after(function(){
            equal(spy1.callCount, 0, 'should not trigger click event (namespaced)');
            equal(spy2.callCount, 1, 'triggered click event (plain)');
            start();
        });

        InkEvent.on(el1, 'click.ded', trigger.wrap(spy1));
        InkEvent.on(el1, 'click', trigger.wrap(spy2));

        InkEvent.remove(el1, 'click.ded');

        Syn.click(el1);
    });
    
    asyncTest('should not be able to remove multiple handlers based on name', function(){
        var el1 = this.byId('foo');
        var trigger = this.trigger();
        var spy1 = this.spy();
        var spy2 = this.spy();
        var spy3 = this.spy();

        trigger.after(function(){
            equal(spy1.callCount, 1, 'triggered click event (namespaced)');
            equal(spy2.callCount, 1, 'triggered click event (namespaced)');
            equal(spy3.callCount, 1, 'triggered click event (plain)');
            start();
        });

        InkEvent.on(el1, 'click.fat', trigger.wrap(spy1));
        InkEvent.on(el1, 'click.ded', trigger.wrap(spy2));
        InkEvent.on(el1, 'click', trigger.wrap(spy3));

        InkEvent.remove(el1, 'click.ded.fat');

        Syn.click(el1);
    });

    asyncTest('should be able to add multiple custom events to a single handler and call them individually', function(){
        var el1 = this.byId('foo');
        var trigger = this.trigger();
        var spy = this.spy();

        trigger.after(function(){
            equal(spy.callCount, 2, 'triggered custom event');
            equal(spy.firstCall.args[0], '1', 'expected array argument');
            equal(spy.secondCall.args[0], '2', 'expected array argument');
            start();
        });

        InkEvent.on(el1, 'fat.test1 fat.test2', trigger.wrap(spy));

        InkEvent.fire(el1, 'fat.test1', ['1']);
        InkEvent.fire(el1, 'fat.test2', ['2']);
    });
   
    asyncTest('should be able to fire an event if the fired namespace is within the event namespace range', function(){
        var el1 = this.byId('foo');
        var trigger = this.trigger();
        var spy = this.spy();

        trigger.after(function(){
            equal(spy.callCount, 4, 'triggered custom event');
            equal(spy.firstCall.args[0], '1', 'expected array argument');
            equal(spy.secondCall.args[0], '2', 'expected array argument');
            equal(spy.thirdCall.args[0], '3', 'expected array argument');
            equal(spy.lastCall.args[0], '3', 'expected array argument');
            start();
        });

        InkEvent.on(el1, 'fat.test1.foo fat.test2.foo', trigger.wrap(spy));

        InkEvent.fire(el1, 'fat.test1', ['1']);
        InkEvent.fire(el1, 'fat.test2', ['2']);
        InkEvent.fire(el1, 'fat.foo', ['3']);
    });
    
    asyncTest('should be able to fire multiple events and fire them regardless of the order of the namespaces', function(){
        var el1 = this.byId('foo');
        var trigger = this.trigger();
        var spy = this.spy();

        trigger.after(function(){
            equal(spy.callCount, 4, 'triggered custom event');
            equal(spy.firstCall.args[0], '1', 'expected array argument');
            equal(spy.secondCall.args[0], '1', 'expected array argument');
            equal(spy.thirdCall.args[0], '2', 'expected array argument');
            equal(spy.lastCall.args[0], '2', 'expected array argument');
            start();
        });

        InkEvent.on(el1, 'fat.test.foo fat.foo.test', trigger.wrap(spy));

        InkEvent.fire(el1, 'fat.test.foo', ['1']);
        InkEvent.fire(el1, 'fat.foo.test', ['2']);
    });
    
    asyncTest('should only fire an event if the fired namespaces is within the event namespace or if the event namespace is within the fired namespace', function(){
        var el1 = this.byId('foo');
        var trigger = this.trigger();
        var spy = this.spy();

        trigger.after(function(){
            equal(spy.callCount, 5, 'triggered custom event');
            equal(spy.firstCall.args[0], '1', 'expected array argument');
            equal(spy.secondCall.args[0], '1', 'expected array argument');
            equal(spy.thirdCall.args[0], '2', 'expected array argument');
            equal(spy.getCall(3).args[0], '2', 'expected array argument');
            equal(spy.getCall(4).args[0], '3', 'expected array argument');
            start();
        });

        InkEvent.on(el1, 'fat.test.foo.ded fat.foo.test fat.ded', trigger.wrap(spy));

        InkEvent.fire(el1, 'fat.test.foo', ['1']);
        InkEvent.fire(el1, 'fat.foo.test', ['2']);
        InkEvent.fire(el1, 'fat.test.ded', ['3']);
    });

});