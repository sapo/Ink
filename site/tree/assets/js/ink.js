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
                'markup': '<div class="ink-space"><div id="colorwheel" style="width:218px;margin:0 auto;" class="ib"></div><div class="ib"><p><label for="hexColor">hex color</label><input type="text" id="hexColor" value="#ff0000" /></p><p><label>sample</label><div id="sample" style="position:relative;margin:0 auto;height:50px;width:50px;"></div></p></div><div style="width:218px;margin;0 auto;text-align:center"><button class="ink-button success">Done</button><button class="ink-button">Cancel</button></div></div>'
            });

            if( modal ){
                var element = SAPO.Dom.Selector.select('.ink-modal .ink-button');
                var hexColor = s$('hexColor');
                var sample = s$('sample');

                if( element.length > 0 ){
                    SAPO.Dom.Event.observe( element[0], 'click', function(event){
                        if( hexColor ){
                            self.value = hexColor.value;
                            self.style.color = hexColor.style.color;
                            self.style.backgroundColor = hexColor.value;
                            modal.dismiss();
                        }
                    });
                }

                SAPO.Dom.Event.observe( element[1], 'click', function(event){
                    modal.dismiss();
                });

                var cw = new SAPO.Component.ColorWheel({
                    container:  'colorwheel',
                    cssURI: 'http://js.sapo.pt/Assets/Images/ComponentColorWheel/style.css',
                    onChange:   function(cw) {
                        var hex = cw.getColorHex();
                        if( hexColor ){
                            hexColor.value = hex;
                            hexColor.style.color = ( cw._value>0.5 ) ? '#000' : '#FFF';
                            hexColor.style.backgroundColor = hex ;
                            sample.style.backgroundColor = hex;
                        }
                    },
                    onShow: function( cw ){
                        cw.setColor(self.value || '#000000');

                        SAPO.Dom.Event.observe('hexColor', 'change', function() {
                            if( hexColor )
                            {
                                var hex = hexColor.value;
                                cw.setColor(hex);
                                sample.style.backgroundColor = hex;
                                hexColor.style.color = ( cw._value>0.5 ) ? '#000' : '#FFF' ;
                                hexColor.style.backgroundColor = hex ;
                            }
                        });
                    }
                });
            }
        });
    }
}());