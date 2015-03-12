var LoadedModule

Ink.requireModules(['Ink.Dom.Loaded_1', 'Ink.Util.Array_1'], function (Loaded, InkArray) {
    sinon.stub(Loaded, 'run')  // Needed for the first test

    // Workaround for modules not loading in order
    setTimeout(runTests, 0)
})

function runTests() {
    var FakeModal;
    Ink.createModule('Ink.UI.Modal', 1, [], function () {
        FakeModal = function() {
            this._init();
        }
        FakeModal.prototype._init = function () {}
        return FakeModal;
    })

    Ink.requireModules(['Ink.Autoload_1'], function (Autoload) {
        var Loaded = Ink.getModule('Ink.Dom.Loaded_1')
        var InkArray = Ink.getModule('Ink.Util.Array_1')

        module('', {
            setup: function () { sinon.spy(FakeModal.prototype, '_init'); },
            teardown: function () { FakeModal.prototype._init.restore(); }
        });

        test('Loaded.run called by Autoload when it was created', function () {
            strictEqual(Loaded.run.calledOnce, true, 'Loaded.run was called once')
        })

        test('It initialises elements based on selectors', function () {
            var elm = mkExampleElm()
            Autoload.run(elm)
            strictEqual(FakeModal.prototype._init.calledOnce, true, 'Fake modal initialised');
        })

        test('Nothing is called if data-autoload is "false"', function () {
            var elm = mkExampleElm();
            elm.children[0].setAttribute('data-autoload', 'false');
            Autoload.run(elm);
            strictEqual(FakeModal.prototype._init.called, false, 'Fake modal not initialized because data-autoload was true');
        });

        test('Custom selectors are also okay', function () {
            var elm = mkExampleElm({ cls: 'not-default' })
            ok(Ink.s('.not-default', elm), 'sanity check')
            Autoload.run(elm, { selectors: {'Modal_1': '.not-default'} })
            strictEqual(FakeModal.prototype._init.calledOnce, true, 'Fake modal initialised')
        });

        test('False, undefined, the empty string, and null don\'t break Autoload', function () {
            InkArray.forEach([false, undefined, '', null, NaN], function (sel) {
                var elm = mkExampleElm({ cls: 'not-default' })
                Autoload.run(elm, { selectors: {'Modal_1': sel} })
                strictEqual(FakeModal.prototype._init.calledOnce, false, 'Fake modal not initialised')
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
