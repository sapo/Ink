/*globals ok,module,test,sinon*/
Ink.requireModules(['Ink.Dom.Element_1', 'Ink.Dom.Css_1', 'Ink.UI.Animate_1'], function (InkElement, Css, Animate) {
    'use strict';

    module('Ink.UI.Animate');

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
            }, 0)
        });
        test('Animation not supported in this browser. Skipping lots of tests!', 0, function () {});
        return;
    }

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
        strictEqual(el.style[Animate._animationPrefix + 'Duration'], '100ms');
    });

    test('duration and onEnd can be passed as last arguments', function () {
        var el = InkElement.create('div');
        var el2 = InkElement.create('div');

        document.body.appendChild(el)

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

    test('when animation is done, if options.revert, the class names are removed', function () {
        var spy = sinon.spy();
        var el = InkElement.create('div');
        document.body.appendChild(el);

        Animate.animate(el, 'fadeOut', { duration: 101, onEnd: spy, revert: true });

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

        Animate.animate(el, 'fadeIn', { duration: 100, onEnd: first });
        Animate.animate(el, 'fadeOut', { duration: 200, onEnd: second });

        var firstCall = true;

        el.addEventListener(Animate.animationEndEventName, function() {
            console.log(new Date - stat)
            if(firstCall){
                firstCall = false;
            } else {
                ok(first.called && second.called, 'both onEnd callbacks were called when the animations ended');
            }
            start();
        });
        stop(2);

        var stat = +new Date
    });
});

