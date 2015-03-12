Ink.requireModules(['Ink.Dom.Event_1', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1', 'Ink.Dom.Browser_1'], function (InkEvent, InkElement, Selector, Browser) {

    module('clone', {
        setup: globalSetUp,
        teardown: globalTearDown
    });

    asyncTest('should be able to clone events of a specific type from one element to another', function( ){
        var el1 = this.byId('input');
        var el2 = this.byId('input2');
        var trigger = this.trigger();
        var spy1 = this.spy();
        var spy2 = this.spy();
        var spy3 = this.spy();

        trigger.after(function(){
            equal(spy1.callCount, 1, 'cloned first click handler');
            equal(spy2.callCount, 1, 'cloned second click handler');
            equal(spy3.callCount, 0, 'should not clone non-click handler');
            start();
        });

        InkEvent.on(el2, 'click', trigger.wrap(spy1));
        InkEvent.on(el2, 'click', trigger.wrap(spy2));
        InkEvent.on(el2, 'keydown', trigger.wrap(spy3));

        InkEvent.clone(el1, el2, 'click');

        Syn.click(el1);
        Syn.key('j', el1);
    });
    asyncTest('should be able to clone all events from one element to another', function(){
        if (Browser.IE && parseFloat(Browser.version) < 9) {
            ok(true, 'Skipping this test');
            return start();
        }

        var el1 = this.byId('input');
        var el2 = this.byId('input2');
        var trigger = this.trigger();
        var spy1 = this.spy();
        var spy2 = this.spy();
        var spy3 = this.spy();

        trigger.after(function(){
            equal(spy1.callCount, 1, 'cloned first click handler');
            equal(spy2.callCount, 1, 'cloned second click handler');
            equal(spy3.callCount, 1, 'cloned keydown handler');
            start();
        });

        InkEvent.on(el2, 'click', trigger.wrap(spy1));
        InkEvent.on(el2, 'click', trigger.wrap(spy2));
        InkEvent.on(el2, 'keydown', trigger.wrap(spy3));

        InkEvent.clone(el1, el2);

        Syn.click(el1);
        Syn.key('j', el1);
    });

    asyncTest('should fire cloned event in scope of new element', function(){
        var el1 = this.byId('input');
        var el2 = this.byId('input2');
        var trigger = this.trigger();
        var spy = this.spy();

        trigger.after(function(){
            equal(spy.callCount, 1, 'cloned click handler');
            strictEqual(spy.thisValues[0], el2, 'cloned handler gets correct context (this)');
            start();
        });

        InkEvent.on(el1, 'click', trigger.wrap(spy));
        InkEvent.clone(el2, el1);

        Syn.click(el2);
    });

    asyncTest('should work with delegated events', function(){
        var foo = this.createElement('div');
        var realfoo = this.byId('foo');
        var bang = this.byId('bang');
        var trigger = this.trigger();
        var spy1 = this.spy();
        var spy2 = this.spy();

        trigger.after(function(){
            equal(spy1.callCount, 1, 'cloned delegated event handler');
            strictEqual(spy1.thisValues[0], bang, 'context (this) was set to delegated element');
            ok(spy1.firstCall.args[0], 'got an event object argument');
            strictEqual(spy1.firstCall.args[0].currentTarget, bang, 'delegated event has currentTarget property correctly set');
            equal(spy2.callCount, 0, 'cloned delegated event handler retains delegation selector (should not call this)');
            start();
        });

        InkEvent.on(foo, 'click',  '.bang', trigger.wrap(spy1));
        InkEvent.on(foo, 'click', '.baz', trigger.wrap(spy2));

        InkEvent.clone(realfoo, foo);

        Syn.click(bang);
    });
});