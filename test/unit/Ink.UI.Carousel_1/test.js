
Ink.requireModules(['Ink.UI.Carousel_1', 'Ink.UI.Pagination', 'Ink.Dom.Element_1', 'Ink.Dom.Event_1', 'Ink.Dom.Selector_1'], function (Carousel, Pagination, InkElement, InkEvent, Selector) {
    'use strict';

    function makeContainer(options) {
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

    module('_setPage()');

    testCarousel('Sets the [left] of the stage so as to pull the slides into position.', function (carousel, _, stage) {
        carousel._setPage(1);
        equal(stage.style.left, '-100px');
    });

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
});
