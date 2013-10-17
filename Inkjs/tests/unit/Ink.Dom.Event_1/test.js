/*globals equal,test,asyncTest,stop,start,ok,expect*/
Ink.requireModules(['Ink.Dom.Event_1', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1'], function (InkEvent, InkElement, Selector) {
    var throttle = Ink.bind(InkEvent.throttle, InkEvent);
    var throttledFunc = throttle(function () {
        ok(true, 'called');
    }, 100);
    asyncTest('throttle (1)', function () {
        expect(2);
        throttledFunc();
        throttledFunc();
        throttledFunc();
        throttledFunc();
        throttledFunc(); // Call this a couple of times, assert called twice.
        setTimeout(start, 300);
    });
    asyncTest('throttle (2)', function () {
        expect(1);
        throttledFunc(); // Call this once, assert called once.
        setTimeout(start, 200);
    });
    asyncTest('throttle (context and arguments)', function () {
        expect(2);
        var withArgs = throttle(function (arg) {
            equal(arg, 'arg');
            equal(this, 'this');
        }, 0);
        withArgs.call('this', 'arg');
        setTimeout(start, 50);
    });
    asyncTest('throttle (called few times)', function () {
        expect(3);
        var fewTimes = throttle(function () { ok(true) }, 20);
        
        setTimeout(fewTimes, 1);
        setTimeout(fewTimes, 100);
        setTimeout(fewTimes, 200);

        setTimeout(start, 300);
    });
    asyncTest('observeDelegated', function () {
        var elem = InkElement.create('ul');
        var child = InkElement.create('li');
        var grandChild = InkElement.create('span');

        elem.appendChild(child);
        child.appendChild(grandChild);

        expect(1);
        InkEvent.observeDelegated(elem, 'click', 'li', function (event) {
            ok(this === child, '<this> is the selected tag');
            start();
        });

        InkEvent.fire(child, 'click');
    });

    asyncTest('observeDelegated', function () {
        var elem = InkElement.create('ul');
        var child = InkElement.create('li');

        elem.appendChild(child);

        expect(0);
        InkEvent.observeDelegated(elem, 'click', 'ul', function (event) {
            ok(false, 'should not fire event on delegation parent');
        });

        InkEvent.fire(child, 'click');
        setTimeout(start, 100);
    });

    asyncTest('observeDelegated can intercept an event from an <a> tag', function () {
        var elem = InkElement.create('ul');
        var child = InkElement.create('li');
        var a = InkElement.create('a');

        elem.appendChild(child);
        child.appendChild(a);

        a.href = "http://example.com";

        expect(2);
        InkEvent.observeDelegated(elem, 'click', 'a', function (event) {
            ok(this === a);
            ok(true, 'should detect click on a link all the same');
        });

        InkEvent.fire(a, 'click');
        setTimeout(start, 100);
    });

    asyncTest('observeDelegated + some selectors', function () {
        var elem = InkElement.create('ul');
        var child = InkElement.create('li');
        var grandChild = InkElement.create('span');

        elem.appendChild(child);
        child.appendChild(grandChild);

        grandChild.className = 'class-i-have';
        
        expect(1);
        InkEvent.observeDelegated(elem, 'click', 'li > span.classIDontHave', function () {
            ok(false, 'should not find this element');
        });

        InkEvent.observeDelegated(child, 'click', 'ul > li > span', function (event) {
            ok(false, 'should not be able to select through parents');
        });

        InkEvent.observeDelegated(elem, 'click', 'li > span', function () {
            ok(true, 'selected by class, correctly');
        });

        InkEvent.fire(grandChild, 'click');

        setTimeout(start, 100);
    });

    asyncTest
});

