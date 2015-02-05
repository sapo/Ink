Ink.requireModules(['Ink.Dom.Event_1', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1', 'Ink.Dom.Browser_1'], function (InkEvent, InkElement, Selector, Browser) {

    module('custom', {
        setup: globalSetUp,
        teardown: globalTearDown
    });

    asyncTest('should be able to add single custom events', function(){
        var el = this.byId('input');
        var trigger = this.trigger();
        var spy = this.spy();

        trigger.after(function(){
            ok(spy.calledOnce, 'add single custom events');
            start();
        });

        InkEvent.on(el, 'partytime', trigger.wrap(spy));
        InkEvent.fire(el, 'partytime');
    });

    asyncTest('should bubble up dom like traditional events', function(){
        if(features.w3c){
            var el1 = this.byId('foo');
            var el2 = this.byId('bar');
            var trigger = this.trigger();
            var spy = this.spy();

            trigger.after(function(){
                ok(spy.calledOnce, 'bubbles up dom like traditional events');
                start();
            });

            InkEvent.on(el1, 'partytime', trigger.wrap(spy));
            InkEvent.fire(el2, 'partytime');
        } else {
            ok(true, 'onpropertychange bubbling not supported by this browser, test bypassed');
            start();
        }
    });

    asyncTest('should be able to add, fire and remove custom events to document', function(){
        var calls = 0;
        var trigger = this.trigger();

        this.removables.push(document);

        trigger.after(function(){
            equal(calls, 1, 'add custom events to document');
            start();
        });

        InkEvent.on(document, 'justloookatthat', trigger.wrap(function(){
            calls++;
            InkEvent.remove(document, 'justloookatthat');
        }));

        InkEvent.fire(document, 'justloookatthat');
        InkEvent.fire(document, 'justloookatthat');
    });

    asyncTest('should be able to add, fire and remove custom events to window', function(){
        var calls = 0;
        var trigger = this.trigger();

        this.removables.push(window);

        trigger.after(function(){
            equal(calls, 1, 'add custom events to window');
            start();
        });

        InkEvent.on(window, 'spiffy', trigger.wrap(function(){
            calls++;
            InkEvent.remove(window, 'spiffy');
        }));

        InkEvent.fire(window, 'spiffy');
        InkEvent.fire(window, 'spiffy');
    });

});