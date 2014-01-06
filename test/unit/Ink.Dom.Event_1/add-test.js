Ink.requireModules(['Ink.Dom.Event_1', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1', 'Ink.Dom.Browser_1'], function (InkEvent, InkElement, Selector, Browser) {

    module('add', {
        setup: globalSetUp,
        teardown: globalTearDown
    });
    asyncTest('should return the element passed in', function( ){
        var el = this.byId('input');
        var returnedOn = InkEvent.on(el, 'click', function(){});
        equal(returnedOn, el, 'returns the element passed in');
        start();
    });
    asyncTest('should be able to add single events to elements', function( ){
        var el = this.byId('input');
        var trigger = this.trigger();
        var spy = this.spy();

        trigger.after(function(){
            ok(spy.calledOnce, 'adds single events to elements');
            start();
        });

        InkEvent.on(el, 'click', trigger.wrap(spy));
        Syn.click(el);
    });
    asyncTest('should be able to add single events to objects', function( ){
        var el = this.newObj();
        InkEvent.on(el, 'complete', function(){
            ok(true, 'adds single events to objects');
            start();
        });
        InkEvent.fire(el, 'complete');
        InkEvent.remove(el);
        InkEvent.fire(el, 'complete');
    });
    asyncTest('scope should be equal to element', function( ){
        var el = this.byId('input');
        var trigger = this.trigger();
        var spy = this.spy();

        trigger.after(function(){
            equal(spy.callCount, 1, 'single call');
            ok(spy.calledOn(el), 'called with element as scope (this)');
            start();
        });

        InkEvent.on(el, 'click', trigger.wrap(spy));
        Syn.click(el);

    });
    asyncTest('should recieve an event method', function( ){
        var el = this.byId('input');
        var trigger = this.trigger();
        var spy = this.spy();

        trigger.after(function(){
            ok(spy.calledOnce, 'single call');
            equal(spy.firstCall.args.length, 1, 'called with an object');
            ok(!!spy.firstCall.args[0].stop, 'called with an event object');
            start();
        });
        InkEvent.on(el, 'click', trigger.wrap(spy));
        Syn.click(el);
    });
    asyncTest('should be able to pass x amount of additional arguments', function( ){
        var el = this.byId('input');
        var trigger = this.trigger();
        var spy = this.spy();

        trigger.after(function(){
            ok(spy.calledOnce, 'single call');
            equal(spy.firstCall.args.length, 4, 'called with an event object and 3 additional arguments');
            equal(spy.firstCall.args[1], 1, 'called with correct argument 1');
            equal(spy.firstCall.args[2], 2, 'called with correct argument 2');
            equal(spy.firstCall.args[3], 3, 'called with correct argument 3');
            start();
        });

        InkEvent.on(el, 'click', trigger.wrap(spy), 1, 2, 3);

       Syn.click(el); 
    });
    asyncTest('should be able to add multiple events by space seperating them', function( ){
        var el = this.byId('input');
        var trigger = this.trigger();
        var spy = this.spy();

        trigger.after(function () {
          equal(spy.callCount, 2, 'adds multiple events by space seperating them');
          start();
        });

        InkEvent.on(el, 'click keypress', trigger.wrap(spy));
        Syn.click(el).key('j');
    });
    asyncTest('should be able to add multiple events of the same type', function( ){
        var el = this.byId('input');
        var trigger = this.trigger();

        var spy1    = this.spy();
        var spy2    = this.spy();
        var spy3    = this.spy();


        trigger.after(function(){
            ok(spy1.calledOnce, 'adds multiple events of the same type (1)');
            ok(spy2.calledOnce, 'adds multiple events of the same type (2)');
            ok(spy3.calledOnce, 'adds multiple events of the same type (3)');
            start();
        });

        InkEvent.on(el, 'click', trigger.wrap(spy1));
        InkEvent.on(el, 'click', trigger.wrap(spy2));
        InkEvent.on(el, 'click', trigger.wrap(spy3));
        Syn.click(el);
    });
    asyncTest('should be able to add multiple events simultaneously with an object literal', function( ){
        var el = this.byId('input');
        var trigger = this.trigger();
        var clickSpy = this.spy();
        var keydownSpy = this.spy();

        trigger.after(function(){
            equal(clickSpy.callCount, 1, 'adds multiple events simultaneously with an object literal (click)');
            equal(keydownSpy.callCount, 1, 'adds multiple events simultaneously with an object literal (keydown)');
            start();
        });

        InkEvent.on(el, { click: trigger.wrap(clickSpy), keydown: trigger.wrap(keydownSpy) });

        Syn.click(el).key('j');
    });
    asyncTest('should bubble up dom', function( ){
        var el1 = this.byId('foo');
        var el2 = this.byId('bar');
        var trigger = this.trigger();
        var spy = this.spy();

        trigger.after(function() {
            ok(spy.calledOnce, 'bubbles up dom');
            start();
        });

        InkEvent.on(el1, 'click', trigger.wrap(spy));

        Syn.click(el2);
    });

    asyncTest('shouldn\'t trigger event when adding additional custom event listeners', function( ){
        var el = this.byId('input');
        var spy = this.spy();

        InkEvent.on(el, 'foo', spy);
        InkEvent.on(el, 'foo', spy);

        defer(function () {
          ok(!spy.called, 'additional custom event listeners trigger event');
          start();
        });
    });

    asyncTest('should bind onmessage to window', function( ){
        if (features.message) {
            var calls = 0;
            var trigger = this.trigger();
            this.removables.push(window);
            trigger.after(function(){
                equal(calls, 1, 'message event activated');
            });
            InkEvent.on(window, 'message', trigger.wrap(function(event){
                calls++;
                ok(event, 'has event object argument');
                equal(event.data, 'hello there', 'data should be copied');
                deepEqual(event.origin, event.originalEvent.origin, 'origin should be copied');
                deepEqual(event.source, event.originalEvent.source, 'source should be copied');
                start();
            }));
            window.postMessage('hello there', '*');
        } else {
            ok(true, 'message events not supported by this browser, test bypassed');
            start();
        }
    });
    asyncTest('one: should only trigger handler once', function( ){
        var el = this.byId('input');
        var trigger = this.trigger();
        var spy = this.spy();

        trigger.after(function(){
            equal(spy.callCount, 1, 'handler called exactly one time');
            start();
        });

        InkEvent.one(el, 'click', trigger.wrap(spy));

        Syn.click(el);
        Syn.click(el);
        Syn.click(el);
    });
    asyncTest('one: should be removable', function( ){
        var el = this.byId('input');
        var spy = this.spy();

        InkEvent.one(el, 'click', spy);
        InkEvent.remove(el, 'click', spy);
        Syn.click(el);
        Syn.click(el);
        defer(function () {
          ok(!spy.called, 'handler shouldn\'t be called');
          start();
        });
    });
});

