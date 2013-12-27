/*globals equal,test*/
Ink.requireModules(['Ink.Dom.Element_1', 'Ink.Dom.Css_1', 'Ink.UI.Animate_1'], function (InkElement, Css, Animate) {
    module('Ink.UI.Animate');

    test('adds the "animated" class together with the animation name', function () {
        var el = InkElement.create('div');
        Animate.animate(el, 'someanimation', { duration: 100 });
        ok(Css.hasClassName(el, 'someanimation'));
        ok(Css.hasClassName(el, 'animate'));
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
    });

    test('when animation is done, if options.revert, the class names are removed', function () {
        var clock = sinon.useFakeTimers();
        var spy = sinon.spy();
        var el = InkElement.create('div');

        Animate.animate(el, 'someanimation', { duration: 101, onEnd: spy, revert: true });

        clock.tick(101);
        ok(!Css.hasClassName(el, 'someanimation'));
    });
});
