var LoadedModule

Ink.requireModules(['Ink.Dom.Loaded_1', 'Ink.Util.Array_1'], function (Loaded, InkArray) {
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
        var InkArray = Ink.getModule('Ink.Util.Array_1')
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

        test('False, undefined, the empty string, and null don\'t break Autoload', function () {
            InkArray.forEach([false, undefined, '', null, NaN], function (sel) {
                sinon.spy(FakeModal.prototype, '_init')
                var elm = mkExampleElm({ cls: 'not-default' })
                Autoload.run(elm, { selectors: {'Modal_1': sel} })
                strictEqual(FakeModal.prototype._init.calledOnce, false, 'Fake modal not initialised')
                FakeModal.prototype._init.restore()
            });
        });

        test('add() adds new selectors', function () {
            delete Autoload.selectors['Modal_1']
            Autoload.add('Modal_1', '.test-adds-new-sel')
            equal(Autoload.selectors['Modal_1'], '.test-adds-new-sel')

            Autoload.add('Modal_1', '.another-sel')
            equal(Autoload.selectors['Modal_1'], '.test-adds-new-sel, .another-sel')
        })

        test('remove() removes selectors', function () {
            Autoload.selectors['Modal_1'] = '.some-sel, some.other.sel'
            Autoload.remove('Modal_1')
            ok(!Autoload.selectors['Modal_1'])
        })

        test('remove() doesn\'t break on unexisting selectors', function () {
            delete Autoload.selectors['Modal_1']
            Autoload.remove('Modal_1')
            ok(true);
        })
    })
}

function mkExampleElm(opt) {
    opt = opt || { cls: 'ink-modal' }
    var elm = document.createElement('div');
    elm.innerHTML = '<div class="'+opt.cls+'"></div>'
    return elm
}
