Ink.requireModules(['Ink.Dom.Event_1', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1', 'Ink.Dom.Browser_1'], function (InkEvent, InkElement, Selector, Browser) {

    module('fire', {
        setup: globalSetUp,
        teardown: globalTearDown
    });

    asyncTest('should be able to fire an event', function( ){
        var el = this.byId('input');
        var trigger = this.trigger();
        var spy = this.spy();

        trigger.after(function(){
            ok(spy.calledOnce, 'fires an event');
            start();
        });

        InkEvent.on(el, 'click', trigger.wrap(spy));
        InkEvent.fire(el, 'click');
    });

    asyncTest('should be able to fire multiple events by space seperation', function( ){
        var el = this.byId('input');
        var trigger = this.trigger();
        var mouseDownSpy = this.spy();
        var mouseUpSpy = this.spy();

        trigger.after(function(){
            ok(mouseDownSpy.calledOnce, 'fires multiple events by space seperation (mousedown)');
            ok(mouseUpSpy.calledOnce, 'fires multiple events by space seperation (mouseup)');
            start();
        });

        InkEvent.on(el, 'mousedown', trigger.wrap(mouseDownSpy));
        InkEvent.on(el, 'mouseup', trigger.wrap(mouseUpSpy));
        InkEvent.fire(el, 'mousedown mouseup');
    });
    asyncTest('should be able to pass multiple argument to custom event', function(){
        var el = this.byId('input');
        var trigger = this.trigger();
        var spy = this.spy();

        trigger.after(function () {
          ok(spy.callCount, 1, 'single call');
          ok(spy.firstCall.args.length, 3, 'called with 3 arguments');
          ok(spy.firstCall.args[0], 1, 'called with correct argument 1');
          ok(spy.firstCall.args[1], 2, 'called with correct argument 2');
          ok(spy.firstCall.args[2], 3, 'called with correct argument 3');
          start();
        })

        InkEvent.on(el, 'foo', trigger.wrap(spy));
        InkEvent.fire(el, 'foo', [1,2,3]);
    });
});