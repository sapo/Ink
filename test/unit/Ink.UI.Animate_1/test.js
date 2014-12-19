/*globals ok,module,test,sinon*/
Ink.requireModules(['Ink.Dom.Element_1', 'Ink.Dom.Css_1', 'Ink.UI.Animate_1', 'Ink.UI.Common_1'], function (InkElement, Css, Animate, Common) {
    'use strict';

    QUnit.testTimeout = 4000;

    module('Ink.UI.Animate_1 basic usage as class');

    if (Animate.animationSupported) {
        test('Animation happens when click event is fired on trigger element', function () {
            var animatedEl = InkElement.create('div');
            var triggerEl = InkElement.create('div');
            document.body.appendChild(animatedEl);
            document.body.appendChild(triggerEl);
            var animateInstance = new Animate(animatedEl, { trigger: triggerEl, animation: 'fadeOut' });

            var spy = sinon.spy(animateInstance, 'animate');

            stop();

            Syn.click(triggerEl, function () {
                ok(spy.called);
                document.body.removeChild(animatedEl);
                document.body.removeChild(triggerEl);
                start();
            });
        });

        test('Animation happens when calling instance.animate()', function () {
            var animatedEl = InkElement.create('div');
            document.body.appendChild(animatedEl);
            var animateInstance = new Animate(animatedEl, { animation: 'fadeOut' });

            var spy = sinon.spy(animateInstance, 'animate');

            stop();

            setTimeout(function () {
                ok(spy.notCalled);
                animateInstance.animate();
                ok(spy.called);
                document.body.removeChild(animatedEl);
                start();
            }, 100);
        });
    } else {
        test('When there is no support, create an Animate instance and don\'t crash', function () {
            var animatedEl = InkElement.create('div');
            var animateInstance = new Animate(animatedEl, { animation: 'foo' });
            ok(animateInstance instanceof Animate, 'calling Animate as a constructor works, returning a valid Animate instance');
        });
    }

    module('Ink.UI.Animate_1.animate');

    if (!Animate.animationSupported) {
        test('When there is no support, onEnd is called after a setTimeout(0)', function () {
            var spy = sinon.spy();
            var el = InkElement.create('div');
            Animate.animate(el, 'fadeOut', { duration: 100, onEnd: spy });
            ok(!Css.hasClassName(el, 'fadeOut'), 'no class names added');
            ok(!Css.hasClassName(el, 'animated'), 'no class names added');
            ok(spy.notCalled, 'not called immeditately...');
            stop();
            setTimeout(function () {
                ok(spy.called, 'only after a setTimeout(0)');
                start();
            }, 0);
        });
        test('Animation not supported in this browser. Skipping lots of tests!', 0, function () {});
        return;
    }

    test('(regression) Can take only two arguments, the element and animation name.', function () {
        var el = InkElement.create('div');
        Animate.animate(el, 'fadeIn');
        ok(Css.hasClassName(el, 'fadeIn'));
        ok(Css.hasClassName(el, 'animated'));
    });

    test('adds the "animated" class together with the animation name', function () {
        var el = InkElement.create('div');
        Animate.animate(el, 'fadeIn', { duration: 100 });
        ok(Css.hasClassName(el, 'fadeIn'));
        ok(Css.hasClassName(el, 'animated'));
    });

    test('when duration is a string, adds the class with the same name', function () {
        var el = InkElement.create('div');
        Animate.animate(el, 'fadeIn', { duration: 'slow' });
        ok(Css.hasClassName(el, 'slow'));
    });

    test('when duration is not a string, default to setting CSS animation-duration property', function () {
        var el = InkElement.create('div');
        Animate.animate(el, 'fadeIn', { duration: 100 });
        ok(Animate._animationPrefix, 'sanity check');
        if (/ms$/.test(el.style[Animate._animationPrefix + 'Duration'])) {  // In opera this is not the case.
            strictEqual(el.style[Animate._animationPrefix + 'Duration'], '100ms');
        } else {
            strictEqual(el.style[Animate._animationPrefix + 'Duration'], '0.1s');
        }
    });

    test('duration and onEnd can be passed as last arguments', function () {
        var el = InkElement.create('div');
        var el2 = InkElement.create('div');

        document.body.appendChild(el);

        // Just pass onEnd...
        Animate.animate(el, 'fadeIn', 200, function () {
            ok(true, 'onEnd called');
            start();
        });

        stop();

        // Just pass the duration...
        Animate.animate(el2, 'fadeOut', 100);

        // [todo] looks like this is untestable (?).
    });

    test('when animation is done, onEnd is called', function () {
        var spy = sinon.spy();
        var el = InkElement.create('div');
        document.body.appendChild(el);

        Animate.animate(el, 'fadeOut', { duration: 101, onEnd: spy });

        ok(!spy.called);

        el.addEventListener(Animate.animationEndEventName, function () {
            if (window.Event) { ok(spy.lastCall.args[0] instanceof window.Event, 'called with an event'); }
            ok(spy.calledOnce, 'spy has been called');
            start();
        });

        stop();
    });

    test('when animation is done, if options.removeClass, the class names are removed', function () {
        var spy = sinon.spy();
        var el = InkElement.create('div');
        document.body.appendChild(el);

        Animate.animate(el, 'fadeOut', { duration: 101, onEnd: spy, removeClass: true });

        ok(!spy.called);
        ok(Css.hasClassName(el, 'fadeOut'));

        el.addEventListener(Animate.animationEndEventName, function () {
            ok(spy.called, 'spy has been called');
            ok(!Css.hasClassName(el, 'fadeOut'));
            start();
        });

        stop();
    });

    test('several animations at once', function () {
        var first = sinon.spy();
        var second = sinon.spy();

        var el = InkElement.create('div');
        document.body.appendChild(el);

        Animate.animate(el, 'fadeIn', { duration: 100, onEnd: first, removeClass: true});
        Animate.animate(el, 'shake', { duration: 200, onEnd: second, removeClass: true});

        var firstCall = true;

        setTimeout(function () {
            if (first.called && second.called) {
                equal(first.lastCall.args[0].animationName, 'fadeIn');
                equal(second.lastCall.args[0].animationName, 'shake');
            } else { ok(false, 'not both callbacks were called as expected'); }
            start();
        }, 800)

        stop(1);
    });
});

