Ink.requireModules(['Ink.Dom.Css_1', 'Ink.Dom.Element_1'], function (Css) {
    test('regression: removeClassName removes whitespace', function () {
        var d = document.createElement( 'div' );
        d.className = 'cenas coisas';
        Css.removeClassName( d , 'noClass' );//d.className === 'cenascoisas'
        equal(Css.hasClassName(d, 'cenas'), true);
        equal(Css.hasClassName(d, 'coisas'), true);
    });

    test('removing a class from an element without that class throws no error.', function(){
        var d = document.createElement( 'div' );
        Css.removeClassName( d , 'noClass' );//throw error
        Css.addClassName( d , 'coisas' );//throw error
        equal(Css.hasClassName( d , 'what' ), false);//throw error
    });
});
