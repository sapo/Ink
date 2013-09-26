Ink.requireModules(['Ink.Dom.Css_1', 'Ink.Dom.Element_1'], function (Css) {
    test('regression: removeClassName removes whitespace', function () {
        var d = document.createElement( 'div' );
        d.setAttribute('class', 'cenas coisas');
        Css.removeClassName( d , 'noClass' );//d.className === 'cenascoisas'
        equal(Css.hasClassName(d, 'cenas'), true);
        equal(Css.hasClassName(d, 'coisas'), true);
    });

    test('removing a class from an element without class', function(){
        var d = document.createElement( 'div' );
        expect(0);  // just check if no errors thrown
        Css.removeClassName( d , 'noClass' );//throw error
        Css.addClassName( d , 'coisas' );//throw error
        Css.hasClassName( d , 'what' );//throw error
    });
});
