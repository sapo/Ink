var LoadedModule

Ink.requireModules(['Ink.Dom.Loaded_1'], function (Loaded) {
    sinon.stub(Loaded, 'run')  // Needed for the first test

    // Workaround for modules not loading in order
    setTimeout(runTests, 0)
})

function runTests() {
    function FakeModal() {
        this._init();
    }
    FakeModal.prototype._init = function () {}
    Ink.createModule('Ink.UI.Modal', 1, [], function () {
        return FakeModal;
    })

    Ink.requireModules(['Ink.Autoload_1'], function (Autoload) {
        var Loaded = Ink.getModule('Ink.Dom.Loaded_1')
        test('Loaded.run called by Autoload when it was created', function () {
            strictEqual(Loaded.run.calledOnce, true, 'Loaded.run was called once')
        })

        test('It initialises elements based on selectors', function () {
            sinon.spy(FakeModal.prototype, '_init')
            var elm = mkExampleElm()
            Autoload.run(elm)
            strictEqual(FakeModal.prototype._init.calledOnce, true, 'Fake modal initialised');
            FakeModal.prototype._init.restore()
        })

        test('Custom selectors are also okay', function () {
            sinon.spy(FakeModal.prototype, '_init')
            var elm = mkExampleElm({ cls: 'not-default' })
            ok(Ink.s('.not-default', elm), 'sanity check')
            Autoload.run(elm, { selectors: {'Modal_1': '.not-default'} })
            strictEqual(FakeModal.prototype._init.calledOnce, true, 'Fake modal initialised')
            FakeModal.prototype._init.restore()
        });
    })
}

function mkExampleElm(opt) {
    opt = opt || { cls: 'ink-modal' }
    var elm = document.createElement('div');
    elm.innerHTML = '<div class="'+opt.cls+'"></div>'
    return elm
}
