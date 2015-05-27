
Ink.requireModules(['Ink.UI.Carousel_1', 'Ink.UI.Pagination_1', 'Ink.Dom.Element_1', 'Ink.Dom.Event_1', 'Ink.Dom.Selector_1'], function (Carousel, Pagination, InkElement, InkEvent, Selector) {
    'use strict';

    function makeContainer(options) {
        options = options || {}
        var cont = InkElement.create('div', {
            className: 'ink-carousel',
            insertBottom: document.body
        });

        cont.style.width = '100px';

        var stage = cont.appendChild(InkElement.create('ul', {
            'class': 'unstyled stage'
        }));

        if (!options.animation) {
            stage.style.transitionDuration = '0s';
        }

        for (var i = 0; i < 5; i++) {
            var slide = stage.appendChild(InkElement.create('li', {
                'class': options.uneven ? 'slide all-60' : 'slide all-100',
                setTextContent: '' + i
            }));

            slide.style.minHeight = '50px';
        }

        return cont;
    }

    function testCarousel(name, testBack, options) {
        options = options || {};
        test(name, function ()  {
            var container = makeContainer(options);
            var stage = Ink.s('.stage', container);
            var slides = Ink.ss('.stage .slide', container);
            if (pag) {
                options.pagination = pag;
            }
            var carouselComponent = new Carousel(container, options);
            testBack(carouselComponent, container, stage, slides);
        });
    }


    test('(regression) creating a carousel doesn\'t call the onChange callback', function() {
        var onChange = sinon.spy()
        new Carousel(makeContainer(), { onChange: onChange });
        ok(onChange.notCalled, 'onChange not called')


        var onChange = sinon.spy()
        new Carousel(makeContainer(), { initialPage: 2, onChange: onChange });
        ok(onChange.notCalled, 'onChange not called even with an initialPage set')
    })

    module('_setPage()');

    testCarousel('Percentages are used to pull the slides left.', function (carousel, _, stage) {
        carousel._setPage(1);
        equal(stage.style.left, '-100%');
    });

    testCarousel('... even when the slide widths are uneven', function (carousel, _, stage) {
        carousel._setPage(1);
        equal(stage.style.left, '-60%');
    }, { uneven: true });

    var pagElm;
    var pag;
    module('Pagination integration', {
        setup: function () {
            pagElm = InkElement.create('div', { insertBottom: document.body });
            pag = new Pagination(pagElm, { size: 10 });
        },
        teardown: function () {
            pagElm = pag = null;
        }
    });

    testCarousel('Controlling the paginator sets the page on the Carousel', sinon.test(function (carousel) {
        this.stub(carousel, '_setPage');
        pag.setCurrent(2);
        equal(carousel._setPage.lastCall.args[0], 2);
    }));

    if ('ontouchstart' in document && !/PhantomJS/.test(navigator.userAgent)) {
        module('Touch');

        testCarousel('You can swipe the carousel sideways to trigger a page change', function (carousel, _, stage) {
            sinon.stub(carousel, 'setPage');
            utils.dispatchTouchEvent(stage, 'start', 101, 10);
            utils.dispatchTouchEvent(stage, 'move', 1, 10);
            ok(carousel.setPage.notCalled, 'setPage() not called because finger is not up');
            utils.dispatchTouchEvent(stage, 'end', 1, 10);
            ok(carousel.setPage.calledOnce);
            equal(carousel.setPage.lastCall.args[0], 1);
        });

        testCarousel('You can swipe the carousel sideways to trigger a page change (part 2: swipe left)', function (carousel, _, stage) {
            carousel.setPage(1);
            sinon.stub(carousel, 'setPage');
            utils.dispatchTouchEvent(stage, 'start', 1, 10);
            utils.dispatchTouchEvent(stage, 'move', 101, 10);
            utils.dispatchTouchEvent(stage, 'end', 101, 10);
            ok(carousel.setPage.calledOnce);
            equal(carousel.setPage.lastCall.args[0], 0);
        });

        testCarousel('Smaller swipes don\'t count.', function (carousel, _, stage) {
            sinon.stub(carousel, 'setPage');
            utils.dispatchTouchEvent(stage, 'start', 100, 10);
            utils.dispatchTouchEvent(stage, 'move', 98, 10);
            utils.dispatchTouchEvent(stage, 'end', 98, 10);
            ok(carousel.setPage.calledWith(0), 'Page hasn\'t changed');
        })

        testCarousel('But if you swipe about 1/3 of the way the page changes.', function (carousel, _, stage) {
            sinon.stub(carousel, 'setPage');
            utils.dispatchTouchEvent(stage, 'start', 100, 10);
            utils.dispatchTouchEvent(stage, 'move', 66, 10);
            utils.dispatchTouchEvent(stage, 'end', 66, 10);
            ok(carousel.setPage.calledWith(1), 'Page changed');
        })

        testCarousel('Regression: Page changes even if you swipe off the left of the carousel.', function (carousel, _, stage) {
            sinon.stub(carousel, '_setPage');
            utils.dispatchTouchEvent(stage, 'start', 1, 10);
            utils.dispatchTouchEvent(stage, 'move', 99, 10);
            utils.dispatchTouchEvent(stage, 'end', 99, 10);
            ok(carousel._setPage.calledOnce, 'Page changed');
            ok(carousel._setPage.calledWith(0), 'Page changed');
        })

        testCarousel('Regression: Page changes even if you swipe off the right of the carousel.', function (carousel, _, stage) {
            sinon.stub(carousel, '_setPage');
            utils.dispatchTouchEvent(stage, 'start', 99, 10);
            utils.dispatchTouchEvent(stage, 'move', 1, 10);
            utils.dispatchTouchEvent(stage, 'end', 1, 10);
            ok(carousel._setPage.calledOnce, 'Page changed');
            ok(carousel._setPage.calledWith(4), 'Page changed');
        }, { initialPage: 4 })
    }

    function testCarouselRefit(name, cb) {
        testCarousel(name, function (carousel, container, stage, slides) {
            var wrapper = InkElement.create('div', { style: 'width: 100px' });
            container.parentNode.appendChild(wrapper);
            wrapper.appendChild(container);
            cb(carousel, wrapper, container, stage, slides)
        })
    }
    module('refit()');

    testCarouselRefit('refit() won\'t call onChange unnecessarily', function (carousel, wrapper) {
        var onChange = sinon.stub()
        carousel.setOption('onChange', onChange);
        carousel.refit();
        ok(onChange.notCalled)
    });

    module('autoAdvance');

    test('autoAdvance', function () {
        var carousel = new Carousel(makeContainer(), { autoAdvance: 3000 });

        equal(typeof carousel._autoAdvanceSto, 'number', '_autoAdvanceSto has a setTimeout handle');

        carousel.stopAutoAdvance();

        equal(carousel._autoAdvanceSto, null, '_autoAdvanceSto is now null');
    });
});
