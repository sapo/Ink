/*globals equal,test*/
Ink.requireModules(['Ink.UI.FormValidator_2', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1', 'Ink.Util.Array_1', 'Ink.Dom.Event_1'], function (FormValidator, InkElement, Selector, InkArray, InkEvent) {
    'use strict';

    test('(regression): FormValidator_2 should destroy FormElement instances after using them', function () {
        document.body.insertAdjacentHTML('beforeend',
              '<form id="myform" class="ink-form" method="post" action="http://local">'
                + '<div id="element_25" class="control-group required validation error" data-rules="">'
                    + '<div class="control">'
                        + '<input id="radio1" type="ttradio">'
                    + '</div>'
                + '</div>'
            + '</form>'
        );

        var myForm = document.getElementById('myform')
        var validator = new FormValidator(myForm)

        var initFunc = sinon.spy(FormValidator.FormElement.prototype, '_init');
        validator.validate();
        validator.validate();
        equal(initFunc.callCount, 1, 'FormElement#_init only called once');

        myForm.parentNode.removeChild(myForm)
    });
});
