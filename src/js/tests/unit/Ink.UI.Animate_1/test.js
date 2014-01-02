/*globals ok,module,test,sinon*/
Ink.requireModules(['Ink.Dom.Element_1', 'Ink.Dom.Css_1', 'Ink.UI.Animate_1'], function (InkElement, Css, Animate) {
    'use strict';

    module('Ink.UI.Animate');

    test('adds the "animated" class together with the animation name', function () {
        var el = InkElement.create('div');
        Animate.animate(el, 'fadeIn', { duration: 100 });
        ok(Css.hasClassName(el, 'fadeIn'));
        ok(Css.hasClassName(el, 'animate'));
    });

    test('duration can be a string ending in "ms" or "s"', function () {
        var clock = sinon.useFakeTimers();
        var spy1 = sinon.spy();
        var spy2 = sinon.spy();
        var el = InkElement.create('div');

        Animate.animate(el, 'someanimation', { duration: '120ms', onEnd: spy1 });
        Animate.animate(el, 'someanimation', { duration: '0.120s', onEnd: spy2 });
        
        clock.tick(100);
        ok(spy1.notCalled);
        ok(spy2.notCalled);
        clock.tick(21);
        ok(spy1.called);
        ok(spy2.called);

        clock.restore();
    });

    test('when animation is done, onEnd is called', function () {
        var clock = sinon.useFakeTimers();
        var spy = sinon.spy();
        var el = InkElement.create('div');

        Animate.animate(el, 'someanimation', { duration: 101, onEnd: spy });

        clock.tick(100);
        ok(spy.notCalled);

        clock.tick(1);
        ok(spy.calledOnce);

        clock.restore();
    });

    test('when animation is done, if options.revert, the class names are removed', function () {
        var clock = sinon.useFakeTimers();
        var spy = sinon.spy();
        var el = InkElement.create('div');

        Animate.animate(el, 'someanimation', { duration: 101, onEnd: spy, revert: true });

        clock.tick(100);
        ok(Css.hasClassName(el, 'someanimation'));
        clock.tick(101);
        ok(!Css.hasClassName(el, 'someanimation'));

        clock.restore();
    });

    test('several animations at once', function () {
        var clock = sinon.useFakeTimers();
        var firstExpectedOnEnd = sinon.spy();
        var secondExpectedOnEnd = sinon.spy();

        var el = InkElement.create('div');

        Animate.animate(el, 'fadeIn', { duration: 100, onEnd: firstExpectedOnEnd });
        Animate.animate(el, 'fadeIn', { duration: 200, onEnd: secondExpectedOnEnd });

        clock.tick(99);
        ok(!firstExpectedOnEnd.called && !secondExpectedOnEnd.called, 'neither onEnd callback was called before animations ended');
        clock.tick(201);

        ok(firstExpectedOnEnd.called && secondExpectedOnEnd.called, 'both onEnd callbacks we gave were called when the animations ended');
        ok(firstExpectedOnEnd.calledBefore(secondExpectedOnEnd), 'first expected onEnd callback was called before the second expected callback');

        clock.restore();
    });
});
