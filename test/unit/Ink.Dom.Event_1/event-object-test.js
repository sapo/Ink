Ink.requireModules(['Ink.Dom.Event_1', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1', 'Ink.Dom.Browser_1'], function (InkEvent, InkElement, Selector, Browser) {
    module('event object', {
        setup: function(){
            globalSetUp.call(this);
            var self = this
            this.runTest = function (custom, start, verifyFn) {
                var el      = self.byId('foo');
                var trigger = self.trigger();
                var spy     = self.spy();

                trigger.after(function() {
                    equal(spy.callCount, 1, 'called once');
                    ok(spy.firstCall.args.length, 'has argument');
                    verifyFn(spy.firstCall.args[0]);
                    start();
                });

                InkEvent.on(el, custom ? 'customEvent' : 'click', trigger.wrap(spy));

                if (custom){
                    InkEvent.fire(el, 'customEvent');
                }
                else {
                    Syn.click(el);
                }
            };
        },
        teardown: globalTearDown
    });

    asyncTest('should have correct target', function(){
        var el1     = this.byId('foo');
        var el2     = this.byId('bar');
        var trigger = this.trigger();
        var spy     = this.spy();

        InkEvent.on(el1, 'click', trigger.wrap(spy));

        Syn.click(el2);

        trigger.after(function() {
            equal(spy.callCount, 1, 'called once');
            ok(spy.firstCall.args.length, 'has argument');
            strictEqual(spy.firstCall.args[0].target, el2, 'event object has correct property');
            start();
        });
    });

    asyncTest('should have stopPropagation method', function(){
        this.runTest(false, start, function(event){
            equal(typeof event.stopPropagation, 'function', 'event object has stopPropagation method');
        });
    });

    asyncTest('should have preventDefault method', function(){
        this.runTest(false, start, function(event){
            equal(typeof event.preventDefault, 'function', 'event object has preventDefault method');
        });
    });

    asyncTest('should have stopImmediatePropagation method', function(){
        this.runTest(false, start, function(event){
            equal(typeof event.stopImmediatePropagation, 'function', 'event object has stopImmediatePropagation method');
        });
    });

    asyncTest('should have stopPropagation method on custom event', function(){
        this.runTest(false, start, function(event){
            equal(typeof event.stopPropagation, 'function', 'event object has stopPropagation method');
        });
    });

    asyncTest('should have preventDefault method on custom event', function(){
        this.runTest(false, start, function(event){
            equal(typeof event.preventDefault, 'function', 'event object has preventDefault method');
        });
    });

    asyncTest('should have stopImmediatePropagation method on custom event', function(){
        this.runTest(false, start, function(event){
            equal(typeof event.stopImmediatePropagation, 'function', 'event object has stopImmediatePropagation method');
        });
    });

    module('stop()', {
        setup: function(){
            globalSetUp.call(this);
            var self = this;
            this.runTest = function (delegate, start) {
                var txt = self.byId('txt');
                var parent  = self.byId('stopper');
                var fixture = self.byId('fixtures');
                var trigger = self.trigger();
                var parentSpy     = self.spy();
                var txtHandler = function(event){
                    event.stop();
                };

                trigger.after(function() {
                    ok(!parentSpy.called, 'parent should not receive event');
                    ok(!txt.value.length, 'input is has no text after keypress');
                    start();
                });

                txt.value = ''
                if (delegate) {
                    InkEvent.on(parent  , 'keypress', '*', trigger.wrap(txtHandler));
                    InkEvent.on(fixture , 'keypress', trigger.wrap(parentSpy));
                } else {
                    InkEvent.on(txt   , 'keypress', trigger.wrap(txtHandler));
                    InkEvent.on(parent, 'keypress', trigger.wrap(parentSpy));
                }

                Syn.key(txt, 'f');
            };
        },
        teardown: globalTearDown
    });
    asyncTest('should preventDefault and stopPropagation', function(){
        this.runTest(true, start);
    });
    if (document.querySelectorAll) {
        asyncTest('should preventDefault and stopPropagation on delegatedEvents', function(){
            this.runTest(true, start);
        });
    }


    module('stopImmediatePropagation()', {
        setup: function(){
            globalSetUp.call(this);
            var self = this;
            this.runTest = function (delegate, start) {
                var stopper  = self.byId('stopper');
                var txt = self.byId('txt');
                var trigger = self.trigger();
                var spy1     = self.spy();
                var spy2     = self.spy();
                var spy3     = self.spy();

                var stopHandler = function(event){
                    event.stopImmediatePropagation();
                };

                trigger.after(function() {
                    equal(spy1.callCount, 1, 'first spy should be called');
                    equal(spy2.callCount, 0, 'second spy should not be called');
                    equal(spy3.callCount, 0, 'third spy should not be called');
                    start();
                });

                if (delegate) {
                    InkEvent.on(stopper , 'click', '[type=text]', trigger.wrap(spy1));
                    InkEvent.on(stopper , 'click', '[type=text]', trigger.wrap(stopHandler));
                    InkEvent.on(stopper , 'click', '[type=text]', trigger.wrap(spy2));
                    InkEvent.on(stopper , 'click', '[type=text]', trigger.wrap(spy3));
                    Syn.click(txt);
                } else {
                    InkEvent.on(stopper , 'click', trigger.wrap(spy1));
                    InkEvent.on(stopper , 'click', trigger.wrap(stopHandler));
                    InkEvent.on(stopper , 'click', trigger.wrap(spy2));
                    InkEvent.on(stopper , 'click', trigger.wrap(spy3));
                    Syn.click(stopper);
                }
            };
        },
        tearDown: globalTearDown
    });

    asyncTest('should stop immediate propagation', function(){
        this.runTest(false, start);
    });

    if (document.querySelectorAll) {
        asyncTest('should stop immediate propagation on delegated events', function(){
            this.runTest(true, start);
        });
    }

    asyncTest('should have keyCode', function(){
        var el = this.byId('input');
        var trigger = this.trigger();
        var spy = this.spy();

        trigger.after(function(){
            equal(spy.callCount, 1, 'called once');
            ok(spy.firstCall.args.length, 'has argument');
            ok(spy.firstCall.args[0].keyCode, 'event object has keyCode');
            start();
        });

        InkEvent.on(el, 'keypress', trigger.wrap(spy));

        Syn.key(el, 'f');
    });

    module('event object properties', {
        setup: function(){
            globalSetUp.call(this);
            var commonIgnorables = ('cancelBubble clipboardData defaultPrevented explicitOriginalTarget getPreventDefault initEvent initUIEvent isChar ' +
            'originalTarget preventCapture preventBubble rangeOffset rangeParent returnValue stopImmediatePropagation synthetic initPopStateEvent ' +
            'preventDefault stopPropagation').split(' ');

                // stuff from IE8 and below
            var oldIEIgnorables = ('recordset altLeft repeat reason data behaviorCookie source contentOverflow behaviorPart url shiftLeft dataFld ' +
            'qualifier wheelDelta bookmarks srcFilter nextPage srcUrn origin boundElements propertyName ctrlLeft state').split(' ');

            var clickIgnorables = commonIgnorables.concat(oldIEIgnorables).concat(('charCode defaultPrevented initMouseEvent keyCode layerX layerY ' +
            'initNSMouseEvent x y state webkitMovementY webkitMovementX').split(' '));

            var oldIEKeyIgnorables = 'fromElement toElement dataTransfer button x y screenX screenY clientX clientY offsetX offsetY state'.split(' ');
            var keyIgnorables = this.keyIgnorables = commonIgnorables.concat(oldIEIgnorables).concat(oldIEKeyIgnorables).concat('initKeyEvent layerX layerY pageX pageY state'.split(' '));

            var el = this.byId('input');

            var getEventObject = this.getEventObject = function (evType, elType, trigger, callback) {
                var handler = function (e) {
                    InkEvent.remove(el);
                    callback(e);
                };
                el = elType === window ? elType : el;
                InkEvent.on(el, evType, handler);
                trigger(el);
            };

            var contains = function (arr, e) {
                var i = arr.length;
                while (i--) {
                    if (arr[i] === e) return true;
                }
                return false;
            };

            var verifyEventObject = this.verifyEventObject = function (event, type, ignorables) {
                var p, orig = event.originalEvent;
                ok(event, 'has event object');
                ok(event.originalEvent, 'has reference to originalEvent');
                equal(event.type, type, 'correct event type');

                for (p in orig) {
                    ok(!(
                        !event.hasOwnProperty(p)
                        && !contains(ignorables, p)
                        && !/^[A-Z_\d]+$/.test(p) // STUFF_LIKE_THIS
                        && !/^moz[A-Z]/.test(p) // Mozilla prefixed properties
                        ), 'additional, uncopied property: "' + p + '" (may need to be added to event-object-test.js)'
                    );
                }
            };

            this.testMouseEvent = function (type, syn, start) {
                getEventObject(
                    type
                    , 'button'
                    , function (el) { Syn[syn || type](el) }
                    , function (event) {
                      verifyEventObject(event, type, clickIgnorables);
                      start();
                  }
                );
            };

            this.testStateEvent = function (type, start) {
                if (!features.history) {
                    ok(true, 'no history API in this browser, not testing state events');
                    return start();
                }
                getEventObject(
                    type
                    , window
                    , function () {
                        window.history.pushState({}, 'test state', '#test-state');
                        window.history.go(-1);
                    }
                    , function (event) {
                        try {
                            verifyEventObject(event, type, commonIgnorables);
                        } catch (e) { }
                        start();
                    }
                    )
            };

            this.testKeyEvent = function (type, start) {
                getEventObject(
                    type
                    , 'input'
                    , function (el) { Syn.key(el, 'f') }
                    , function (event) {
                        verifyEventObject(event, type, keyIgnorables);
                        start();
                    }
                );
            };
        },
        teardown: globalTearDown
    });

    if (document.addEventListener /* If we can has standard events, then we think about this */) {
        asyncTest('click: has correct properties', function(){
            this.testMouseEvent('click', null, start);
        });

        asyncTest('dblclick: has correct properties', function(){
            this.testMouseEvent('dblclick', null, start);
        });

        asyncTest('mousedown: has correct properties', function(){
            this.testMouseEvent('mousedown', 'click', start);
        });

        asyncTest('mouseup: has correct properties', function(){
            this.testMouseEvent('mouseup', 'click', start);
        });

        asyncTest('popstate: has correct properties', function(){
            this.testStateEvent('popstate', start);
        });

        asyncTest('keyup: has correct properties', function(){
            this.testKeyEvent('keyup', start);
        });

        asyncTest('keydown: has correct properties', function(){
            this.testKeyEvent('keydown', start);
        });

        asyncTest('keypress: has correct properties', function(){
            this.testKeyEvent('keypress', start);
        });
    }

    // see https://github.com/fat/bean/pull/61 & https://github.com/fat/bean/issues/76

    if (Browser.model === 'firefox') {
        test('(skipped) key events prefer "keyCode" rather than "which"', function () {
            ok(true, 'Skipped in firefox. See https://github.com/fat/bean/issues/76');
        });
    } else if (document.addEventListener) {
        asyncTest('key events prefer "keyCode" rather than "which"', function(){
            var verifyEventObject = this.verifyEventObject;
            var keyIgnorables = this.keyIgnorables;

            this.getEventObject(
                'keyup'
                , 'input'
                , function (el) { Syn.trigger('keyup', { which: 'g', keyCode: 'f' }, el) }
                , function (event) {
                    verifyEventObject(event, 'keyup', keyIgnorables);
                    equal(event.keyCode, 'f', 'correct keyCode');
                    start();
                }
            );
        });
    }
});