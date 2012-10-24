SAPO.Dom.Event.observe(document, "dom:loaded", function(){
	console.log(SAPO.Dom.Selector.select('.topbar ul'));
});

(function(){
    var colorPickers = SAPO.Dom.Selector.select('.colorPicker');


    for( i=0; i<colorPickers.length; i++ )
    {

        SAPO.Dom.Event.observe(colorPickers[i],'click',function(event){

            var self = this;

            var modal = new SAPO.Ink.Modal( undefined, {
                'width': 300,
                'height': 500,
                'markup': '<div class="ink-space"><div id="colorwheel" style="width:218px;margin:0 auto;" class="ib"></div><div class="ib"><p><label for="hexColor">hex color</label><input type="text" id="hexColor" value="#ff0000" /></p><p><label>sample</label><div id="sample" style="position:relaative;margin:0 auto;height:50px;width:50px;"></div></p></div><div style="width:218px;margin;0 auto;text-align:center"><button class="ink-button success">Done</button><button class="ink-button">Cancel</button></div></div>'
            });

            SAPO.Dom.Event.observe( SAPO.Dom.Selector.select('.ink-modal .ink-button.success')[0], 'click', function(event){
                self.value = s$('hexColor').value;
                self.style.color = s$('hexColor').style.color;
                self.style.backgroundColor = s$('hexColor').value;
                modal.dismiss();
            });

            SAPO.Dom.Event.observe( SAPO.Dom.Selector.select('.ink-modal .ink-button')[1], 'click', function(event){
                modal.dismiss();
            });



            var cw = new SAPO.Component.ColorWheel({
                container:  'colorwheel',
                cssURI: 'http://js.sapo.pt/Assets/Images/ComponentColorWheel/style.css',
                onChange:   function(cw) {
                    var hex = cw.getColorHex();
                    s$('hexColor').value = hex;
                    s$('hexColor').style.color = ( cw._value>0.5 ) ? '#000' : '#FFF';
                    s$('hexColor').style.backgroundColor = hex ;
                    s$('sample').style.backgroundColor = hex;
                }
            });

            SAPO.Dom.Event.observe('hexColor', 'change', function() {
                var hex = s$('hexColor').value;
                s$('sample').style.backgroundColor = hex;
                cw.setColor(hex);
                s$('hexColor').style.backgroundColor = ( cw._value>0.5 ) ? '#000' : '#FFF' ;
            });
        });
    }
}());