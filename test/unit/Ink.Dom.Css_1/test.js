Ink.requireModules(['Ink.Dom.Css_1', 'Ink.Dom.Element_1'], function (Css) {
    var classNames = (function (fallBack) {
        var d;
        QUnit.module('class names' + (fallBack ? '(className)' : '(classList)'), {
            setup: function () {
                d = document.createElement('div');
                d.className = 'cenas coisas';
                if (fallBack) {
                    d = { className: 'cenas coisas' };
                }
            }
        });
        test('regression: removeClassName removes whitespace properly', function () {
            Css.removeClassName( d , 'noClass' );//d.className === 'cenascoisas'
            ok(/cenas/.test(d.className));
            ok(/cenas/.test(d.className));
        });

        test('regression: removing a class from an element without that class throws no error.', function(){
            expect(0);
            Css.removeClassName( d , 'noClass' );//throw error
            Css.addClassName( d , 'coisas' );//throw error
        });

        test('hasClassName', function () {
            ok(Css.hasClassName(d, 'coisas'));
            ok(!Css.hasClassName(d, 'oisas'));
        });

        test('hasClassName multi, any class', function () {
            ok(Css.hasClassName(d, 'coisas', false));
            ok(Css.hasClassName(d, 'cenas coisas', false));
            ok(Css.hasClassName(d, 'coisas and classes which we dont have', false));
        });
        test('hasClassName multi, all classes', function () {
            ok(Css.hasClassName(d, 'coisas', true));
            ok(Css.hasClassName(d, 'cenas coisas', true));
            ok(!Css.hasClassName(d, 'cenas coisas and classes which we dont have', true));
        });

        test('removing classes', function () {
            Css.removeClassName(d, 'cenas');
            equal(d.className, 'coisas');
        });

        test('removing multiple classes', function () {
            Css.removeClassName(d, 'cenas, coisas');
            equal(d.className, '');
        });

        test('adding multiple classes', function () {
            Css.addClassName(d, 'cenas2, coisas2');
            ok(/cenas2/.test(d.className));
            ok(/coisas2/.test(d.className));
            ok(/cenas/.test(d.className));
            ok(/coisas/.test(d.className));
        });

        test('adding empty classes is ignored', function () {
            Css.addClassName(d, ['foo', '']);
            Css.addClassName(d, []);
            Css.addClassName(d, ['']);
            Css.addClassName(d, '');
            ok(true);
            equal(d.className, 'cenas coisas foo');
        });

        test('toggleClassName', function () {
            Css.toggleClassName(d, 'cenas');
            equal(d.className, 'coisas');
            Css.toggleClassName(d, 'cenas');
            ok(/cenas/.test(d.className));
            ok(/coisas/.test(d.className));
        });

        test('toggleClassName(, forceAdd=true)', function () {
            Css.toggleClassName(d, 'cenas', true);
            ok(/cenas/.test(d.className));
            ok(/coisas/.test(d.className));
        });

        test('toggling many classes', function () {
            Css.toggleClassName(d, 'cenas coisas');
            equal(d.className, '');
            Css.toggleClassName(d, 'cenas coisas');
            ok(/cenas/.test(d.className));
            ok(/coisas/.test(d.className));
        });
    });
    if (document.documentElement.classList) {
        classNames(false);  // test with classList
        classNames(true);  // use className
    } else {
        classNames(false); // test with className
    }

    module('getStyle');

    test('regression: doesn\'t crash if given a text node', function () {
        var t = document.createTextNode('foo!');
        Css.getStyle(t, 'height');
        ok(true);
    });
});
