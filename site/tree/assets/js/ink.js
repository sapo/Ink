SAPO.Dom.Event.observe(document, "dom:loaded", function(){
	console.log(SAPO.Dom.Selector.select('.topbar ul'));
});

(function(){
    
}());

Scroller = {
    // control the speed of the scroller.
    // dont change it here directly, please use Scroller.speed=50;
    speed:10,

    // returns the Y position of the div
    gy: function (d) {
        gy = d.offsetTop
        if (d.offsetParent) while (d = d.offsetParent) gy += d.offsetTop
        return gy
    },

    // returns the current scroll position
    scrollTop: function (){
        body=document.body
        d=document.documentElement
        if (body && body.scrollTop) return body.scrollTop
        if (d && d.scrollTop) return d.scrollTop
        if (window.pageYOffset) return window.pageYOffset
        return 0
    },

    // attach an event for an element
    // (element, type, function)
    add: function(event, body, d) {
        if (event.addEventListener) return event.addEventListener(body, d,false)
        if (event.attachEvent) return event.attachEvent('on'+body, d)
    },

    // kill an event of an element
    end: function(e){
        if (window.event) {
            window.event.cancelBubble = true
            window.event.returnValue = false
            return;
        }
        if (e.preventDefault && e.stopPropagation) {
          e.preventDefault()
          e.stopPropagation()
        }
    },
    
    // move the scroll bar to the particular div.
    scroll: function(d){
        i = window.innerHeight || document.documentElement.clientHeight;
        h=document.body.scrollHeight;
        a = Scroller.scrollTop()
        if(d>a)
            if(h-d>i)
                a+=Math.ceil((d-a)/Scroller.speed)
            else
                a+=Math.ceil((d-a-(h-d))/Scroller.speed)
        else
            a = a+(d-a)/Scroller.speed;
        window.scrollTo(0,a-5)
        if(a==d || Scroller.offsetTop==a)clearInterval(Scroller.interval)
        Scroller.offsetTop=a
    },
    // initializer that adds the renderer to the onload function of the window
    init: function(){
        Scroller.add(window,'load', Scroller.render)
    },

    // this method extracts all the anchors and validates then as # and attaches the events.
    render: function(){
        return;
        a = document.getElementsByTagName('a');
        Scroller.end(this);
        window.onscroll
        for (i=0;i<a.length;i++) {
          l = a[i];
          if(l.href && l.href.indexOf('#') != -1 && ((l.pathname==location.pathname) || ('/'+l.pathname==location.pathname)) ){
            Scroller.add(l,'click',Scroller.end)
                l.onclick = function(){
                    Scroller.end(this);
                    l=this.hash.substr(1);
                     a = document.getElementsByTagName('a');
                     for (i=0;i<a.length;i++) {
                        if(a[i].name == l){
                            clearInterval(Scroller.interval);
                            Scroller.interval=setInterval('Scroller.scroll('+Scroller.gy(a[i])+')',10);
                        }
                    }
                }
            }
        }
    }
}
// invoke the initializer of the scroller
Scroller.init();

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