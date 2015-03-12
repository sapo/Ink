Ink.requireModules(['Ink.UI.Sticky_1', 'Ink.UI.Common_1', 'Ink.Dom.Element_1'], function (Sticky, Common, InkElement) {
    var fakeLayout;
    var stickyEl;
    var sticky;

    function testSticky(name, testBack, options) {
        test(name, sinon.test(function ()  {
            fakeLayout = this.stub(Common, 'currentLayout').returns('large');
            stickyEl = InkElement.create('div', { insertBottom: InkElement.create('div') });
            sticky = new Sticky(stickyEl, options);

            testBack();

            fakeLayout.restore();
        }));
    }

    testSticky('sticky should be _isDisabledInLayout (small only)', function () {
        fakeLayout.returns('large');
        equal(sticky._isDisabledInLayout(), false);
        fakeLayout.returns('medium');
        equal(sticky._isDisabledInLayout(), false);
        fakeLayout.returns('small');
        equal(sticky._isDisabledInLayout(), true);
    }, {activateInLayouts: 'medium,large'});

    testSticky('sticky should be _isDisabledInLayout (medium, large)', function () {
        fakeLayout.returns('large');
        equal(sticky._isDisabledInLayout(), true);
        fakeLayout.returns('medium');
        equal(sticky._isDisabledInLayout(), true);
        fakeLayout.returns('small');
        equal(sticky._isDisabledInLayout(), false);
    }, {activateInLayouts: 'small'});
    
});
