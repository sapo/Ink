Ink.requireModules(['Ink.Dom.Event_1', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1', 'Ink.Dom.Browser_1'], function (InkEvent, InkElement, Selector, Browser) {
    if (!document.querySelectorAll) {
        /*
        It's impossible to get QUnit to run one test at a time, and these tests alter and depend on InkEvent state (setSelectorEngine).
        When querySelectorAll exists, these tests pass, because QUnit's insanity can't affect them.
        */
        return;
    }

    module('delegate', {
        setup: function(spy, target){
            globalSetUp.call(this);
            this.verifySimpleDelegateSpy = function (spy, target) {
              equal(spy.callCount, 2, 'delegated on selector')
              strictEqual(spy.thisValues[0], target, 'context (this) was set to delegated element')
              strictEqual(spy.thisValues[1], target, 'context (this) was set to delegated element')
              ok(spy.firstCall.args[0], 'got an event object argument')
              ok(spy.secondCall && spy.secondCall.args[0], 'got an event object argument')
              strictEqual(spy.firstCall.args[0].currentTarget, target, 'delegated event has currentTarget property correctly set')
              strictEqual(spy.secondCall && spy.secondCall.args[0].currentTarget, target, 'delegated event has currentTarget property correctly set')
            }
        },
        teardown: function(){
            globalTearDown.call(this);
            InkEvent.setSelectorEngine();
        }
    });
    asyncTest('should be able to delegate on selectors', function(){
        var self = this;
        this.runTest = function(done, regFn){
            var el1     = self.byId('foo');
            var el2     = self.byId('bar');
            var el3     = self.byId('baz');
            var el4     = self.byId('bang');
            var trigger = self.trigger();
            var spy     = self.spy();

            regFn(el1, trigger.wrap(spy));

            Syn.click(el2);
            Syn.click(el3);
            Syn.click(el4);

            trigger.after(function () {
                self.verifySimpleDelegateSpy(spy, el2);
                start();
            })
        }
        this.runTest(start, function(el1, wrappedSpy){
            InkEvent.on(el1, 'click', '.bar', wrappedSpy);
        });

    });
    asyncTest('should be able to delegate multiple events', function(){
        var self = this;
        this.runTest = function(done, regFn){
            var el1     = self.byId('foo');
            var el2     = self.byId('bar');
            var el3     = self.byId('baz');
            var trigger = self.trigger();
            var spy     = self.spy();

            regFn(el1, trigger.wrap(spy));

            Syn.click(el2);
            Syn.click(el3);

            trigger.after(function () {
                self.verifySimpleDelegateSpy(spy, el2);
                start();
            }, 50)
        }
        this.runTest(start, function(el1, wrappedSpy){
            InkEvent.on(el1, 'mouseup mousedown', '.bar', wrappedSpy);
        });
    });

    asyncTest('should be able to delegate on array', function(){
        var self = this;
        this.runTest = function(done, regFn){
            var el1     = self.byId('foo');
            var el2     = self.byId('bar');
            var el3     = self.byId('baz');
            var el4     = self.byId('bang');
            var trigger = self.trigger();
            var spy     = self.spy();

            regFn(el1, el2, trigger.wrap(spy));

            Syn.click(el2);
            Syn.click(el3);
            Syn.click(el4);

            trigger.after(function () {
                self.verifySimpleDelegateSpy(spy, el2);
                start();
            });

        }
        this.runTest(start, function(el1, el2, wrappedSpy){
            InkEvent.on(el1, 'click', [el2], wrappedSpy);
        });
    });

    asyncTest('should be able to remove delegated handler', function(){
        var self = this;
        this.runTest = function(done, regFn){
            var el1     = self.byId('foo');
            var el2     = self.byId('bar');
            var calls = 0;
            var trigger = self.trigger();
            var fn     = function() {
                calls++;
                InkEvent.remove(el1, 'click', trigger.wrapped(fn));
            };

            regFn(el1, trigger.wrap(fn));

            Syn.click(el2);
            Syn.click(el2);

            trigger.after(function () {
                equal(calls, 1, 'delegated event triggered once');
                start();
            })
        }
        this.runTest(start, function(el1, wrappedSpy){
            InkEvent.on(el1, 'click', '.bar', wrappedSpy);
        });
    });

    asyncTest('should use qSA if available', function(){
        var self = this;
        this.runTest = function(done, regFn){
            if(!features.qSA){
                ok(true, 'qSA not available');
                return start();
            }

            var el1     = self.byId('foo');
            var el2     = self.byId('bar');
            var el3     = self.byId('baz');
            var el4     = self.byId('bang');
            var trigger = self.trigger();
            var spy     = self.spy();

            InkEvent.setSelectorEngine();
            regFn(el1, trigger.wrap(spy));

            Syn.click(el2);
            Syn.click(el3);
            Syn.click(el4);

            trigger.after(function () {
                self.verifySimpleDelegateSpy(spy, el2);
                start();
            })
        }
        this.runTest(start, function(el1, wrappedSpy){
            InkEvent.on(el1, 'click', '.bar', wrappedSpy);
        });
    });
    asyncTest('should throw error when no qSA available and no selector engine set', function(){
        var self = this;
        this.runTest = function(done, regFn){
            if(features.qSA){
                ok(true, 'qSA available in this browser, skipping test');
                return start();
            }

            var el1     = self.byId('foo');
            var el2     = self.byId('bar');
            var spy     = self.spy();

            InkEvent.setSelectorEngine();
            regFn(el1, spy);

            window.onerror = function (e){
                ok(e.toString(), /Bean/, 'threw Error on delegated event trigger without selector engine or qSA');
                window.onerror = null;
            };

            Syn.click(el2);

            defer(function(){
                equal(spy.callCount, 0, 'don\'t fire delegated event without selector engine or qSA');
                start();
            });
        }
        this.runTest(start, function(el1, wrappedSpy){
            InkEvent.on(el1, 'click', '.bar', wrappedSpy);
        });
    });
    asyncTest('should be able to set a default selector engine', function(){
        var self = this;
        this.runTest = function(done, regFn){
            var el1     = self.byId('foo');
            var el2     = self.byId('bar');
            var el3     = self.byId('baz');
            var el4     = self.byId('bang');
            var selector = 'SELECTOR? WE DON\'T NEED NO STINKIN\' SELECTOR!';
            var trigger = self.trigger();
            var stub = self.stub();
            var spy     = self.spy();

            stub.returns([el2]);

            InkEvent.setSelectorEngine(stub);

            regFn(el1, selector, trigger.wrap(spy));

            Syn.click(el2);
            Syn.click(el3);
            Syn.click(el4);

            trigger.after(function () {
                equal(stub.callCount, 6, 'selector engine called');
                strictEqual(stub.firstCall.args[0], selector, 'selector engine called with selector argument');
                strictEqual(stub.firstCall.args[1], el1, 'selector engine called with selector argument');
                self.verifySimpleDelegateSpy(spy, el2);
                InkEvent.setSelectorEngine(null);
                start();
            })
        }
        this.runTest(start, function(el1, selector, wrappedSpy){
            InkEvent.on(el1, 'click', selector, wrappedSpy);
        });
    });
});