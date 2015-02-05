Ink.requireModules(['Ink.UI.Tabs_1', 'Ink.UI.Common_1', 'Ink.Dom.Element_1', 'Ink.Dom.Event_1', 'Ink.Dom.Selector_1'], function (Tabs, Common, InkElement, InkEvent, Selector) {
    var fakeLayout;

    // Prevent state from other test runs from infecting this.
    var pathHere = (window.location + '').replace(/\#.*/g, '')
    window.location.hash = '#no-hash';

    function makeContainer() {
        return InkElement.create('div', {
            className: 'ink-tabs',
            setHTML: Ink.i('tab_html').innerHTML,
            style: 'display: none',
            insertBottom: document.body
        });
    }

    function testTabs(name, testBack, options) {
        test(name, function ()  {
            location.hash = '#no-hash';
            var container = makeContainer();
            var tabs = Ink.ss('.tabs-nav li', container);
            var tabComponent = new Tabs(container, options || {});
            testBack(tabComponent, container, tabs);
        });
    }

    testTabs('_findLinkByHref', function (tabComponent, container, tabs) {
        var link = tabs[0].children[0];
        var linkWithFullUrl = tabs[1].children[0];
        linkWithFullUrl.setAttribute('href', pathHere + '#someth');

        ok(link && linkWithFullUrl);
        strictEqual(tabComponent._findLinkByHref('someth'), linkWithFullUrl);
        strictEqual(tabComponent._findLinkByHref('home'), link);
    });

    testTabs('_changeTab', function (tabComponent, container, tabs) {
        var changeTo = tabs[1];  // because tab 0 is the active one now
        ok(changeTo);
        notStrictEqual(tabComponent._activeMenuTab, changeTo);

        tabComponent._changeTab(Ink.s('a', changeTo));

        ok(changeTo.className.match(/active/), 'tab has .active class!');
        strictEqual(tabComponent._activeMenuTab, changeTo, 'tab is active!');

        var activeTabs = Ink.ss('.tabs-nav .active', container);
        equal(activeTabs.length, 1, 'only ever one active tab');

        var activePanes = Ink.ss('div.active', container);
        equal(activePanes.length, 1, 'only ever one active pane');
    });

    testTabs('changeTab calls _changeTab with correct arguments', function (tabComponent, container) {
        var spy = sinon.stub(tabComponent, '_changeTab');
        tabComponent.changeTab('home');
        ok(spy.calledOnce);
        deepEqual(spy.lastCall.args, [Ink.s('a[href$="#home"]', container), true]);
    });

    testTabs('... but not when new tab is invalid', function (tabComponent) {
        var spy = sinon.stub(tabComponent, '_changeTab');
        tabComponent.changeTab('hoem');
        ok(!spy.called, 'spy was called');
    });

    testTabs('when clicking a tab, _changeTab is called with the target link', function (tabComponent, container) {
        stop();
        var spy = sinon.stub(tabComponent, '_changeTab');
        var theTabLink = Ink.s('a[href$="#news"]', container);
        Syn.click(theTabLink, function () {
            ok(spy.calledOnce);
            deepEqual(spy.lastCall && spy.lastCall.args, [theTabLink, true]);
            start();
        });
    });

    testTabs('regression: Clicking a tab\'s child element also works.', function (tabComponent, container) {
        stop();
        var spy = sinon.spy(tabComponent, '_changeTab');
        var theTabLink = Ink.s('a[href$="#news"]', container);
        var tabChild = InkElement.create('span', { insertBottom: theTabLink });
        Syn.click(tabChild, function () {
            ok(spy.calledOnce);
            deepEqual(spy.lastCall && spy.lastCall.args, [theTabLink, true]);
            start();
        });
    });

    test('regression test: tabs would crash if there were no menus to use', function () {
        var cont = makeContainer();
        Ink.s('.tabs-nav', cont).innerHTML = '';
        new Tabs(cont);
        ok(true, 'didn\'t throw');
    });

    test('regression test: #245', function () {
        var cont = makeContainer();
        var home = Ink.s('a[href$="#home"]', cont);
        home.setAttribute('href', '#hoem');
        new Tabs(cont, { active: 'hoem' });
        ok(true, 'creating a tabs object didn\'t raise an exception');
    });

    test('regression test: #257', function () {
        var cont = makeContainer();
        var tabs = Ink.s('.tabs-nav', cont);
        var invalidIDTab = InkElement.create('a', { href: '#invalid-id' });
        var invalidIDLi = InkElement.create('li');
        invalidIDLi.appendChild(invalidIDTab);
        tabs.appendChild(invalidIDLi);
        var tabComponent = new Tabs(cont);
        var changeTab = sinon.spy(tabComponent, '_changeTab');
        stop();
        Syn.click(invalidIDTab, function () {
            ok(changeTab.notCalled);
            start();
        });
    });

    testTabs('regression test: When Tabs is created, it hides all tabs except the active one', function (_, container) {
        ok(Ink.ss('.tabs-content.hide-all', container).length)
        equal(Ink.ss('.tabs-content:not(.hide-all)', container).length, 1)
    });

    test('creating a Tabs on an element without any .tabs-nav', sinon.test(function (container) {
        var cont = InkElement.create('div', {});
        this.spy(Ink, 'warn')
        new Tabs(cont);
        ok(Ink.warn.calledWith(sinon.match(/\.tabs-nav/)),
            'called Ink.error with a message including ".tabs-nav"');
    }));

    test('creating a Tabs on an element without any tabs in .tabs-nav', sinon.test(function (container) {
        var cont = InkElement.create('div', {});
        var tabsNav = InkElement.create('div', { insertBottom: cont });
        new Tabs(cont);
        ok(true, 'didn\'t throw an exception');
    }));
    
    module('change the hash in the URL', {
    });

    testTabs('clicking a tab changes window.location.hash', function (tabComponent, container) {
        stop();
        Syn.click(Ink.s('a[href$="#description"]', container), function () {
            equal(window.location.hash, '#description');
            start();
        });
    });

    testTabs('... except when options.preventUrlChange === true', function (tabComponent, container) {
        stop();
        Syn.click(Ink.s('a[href$="#home"]', container), function () {
            equal(window.location.hash, '#no-hash', 'location.hash shouldnt change if preventUrlChange === true.');
            start();
        });
    }, {preventUrlChange: true});

    testTabs('Changing the tab in the API changes window.location.hash', function (tabComponent) {
        tabComponent.changeTab('description');
        equal(window.location.hash, '#description')
    });

    testTabs('... except when options.preventUrlChange === true', function (tabComponent, container) {
        tabComponent.changeTab('description');
        equal(window.location.hash, '#no-hash', 'location.hash shouldnt change if preventUrlChange === true.');
    }, {preventUrlChange: true});

    module('Private API');

    testTabs('hashify/dehashify', function (tabs) {
        equal(
            tabs._hashify('wow'),
            '#wow');

        equal(
            tabs._hashify('#wow'),
            '#wow');

        equal(
            tabs._dehashify('#wow'),
            'wow');

        equal(
            tabs._dehashify('wow'),
            'wow');
    });

    testTabs('findLink', function (tabs, container) {
        var homeLink = Ink.s('[href$="#home"]', container);
        ok(homeLink, 'sanity check');
        ok(homeLink.parentNode.nodeName.toLowerCase() === 'li', 'sanity check');
        strictEqual(tabs._findLinkByHref('home'), homeLink, 'by name');
        strictEqual(tabs._findLinkByHref('#home'), homeLink, 'by hash');
        strictEqual(tabs._findLinkByHref(homeLink), homeLink, 'by itself');
        strictEqual(tabs._findLinkByHref(homeLink.parentNode), homeLink, 'by li containing the link');
        var homeSection = Ink.s('#home', container)
        strictEqual(tabs._findLinkByHref(homeSection), homeLink, 'by section');

        strictEqual(tabs._findLinkByHref(document.body), null, 'Wrong element');
        strictEqual(tabs._findLinkByHref('wrong-thing'), null, 'Wrong name');
        strictEqual(tabs._findLinkByHref(null), null, 'null');
    });
});
