<?php include 'shared/header.php'; ?>

<!--
    title_link
        http://fotos.sapo.pt/gilreis/fotos/despedida/?uid=I6nrnIBgdyq3zP7oQfw5#grande
        http://fotos.sapo.pt/smobile/fotos/?uid=bqZHVnimp8Q9KROvv6iv#grande
        http://fotos.sapo.pt/viriatodias/fotos/ferias-gala-gal/?uid=uYT5azVkQH5uZZVCP7zS#grande
    
    title_text
        Despedida
        3
        ferias_2012_gala_galé 165

    image_full
        http://c3.quickcachr.fotos.sapo.pt/i/u21126828/13810303_Y4m1j.jpeg
        http://c3.quickcachr.fotos.sapo.pt/i/uea122928/13789518_V3Fa2.jpeg
        http://c6.quickcachr.fotos.sapo.pt/i/uad11be32/13793402_Np0cB.jpeg

    data-thumb-2x (260w)
        http://c3.quickcachr.fotos.sapo.pt/i/P5e12eca5/13810303_Ipc8z.jpeg
        http://c7.quickcachr.fotos.sapo.pt/i/Pfa1181a5/13789518_SuSGF.jpeg
        http://c4.quickcachr.fotos.sapo.pt/i/P661136f9/13793402_Z9PkD.jpeg

    image_thumb
        http://c4.quickcachr.fotos.sapo.pt/i/M49118df9/13810303_oKcJ6.jpeg
        http://c6.quickcachr.fotos.sapo.pt/i/M7b119f3f/13789518_O12ou.jpeg
        http://c5.quickcachr.fotos.sapo.pt/i/M3c128daf/13793402_hm3sI.jpeg

-->


<style type="text/css">
    .ink-gallery .pagination {
        z-index: 10;
    }

    .ink-gallery .slider > ul {
             -o-transition: margin-left 500ms;
            -ms-transition: margin-left 500ms;
           -moz-transition: margin-left 500ms;
        -webkit-transition: margin-left 500ms;
                transition: margin-left 500ms;
    }
</style>

<button onclick="location='widget-gallery2.php?layout=0'">layout 0</button>
<button onclick="location='widget-gallery2.php?layout=1'">layout 1</button>
<button onclick="location='widget-gallery2.php?layout=2'">layout 2</button>
<button onclick="location='widget-gallery2.php?layout=3'">layout 3</button>



<div class="ink-l70">   
    <div class="ink-space">
        <div id="REPLACEME"><div>
    </div>
</div>



<script type="text/javascript">
    var gal = new SAPO.Ink.Gallery('#REPLACEME', {
        layout: parseInt( location.search.substring( location.search.indexOf('layout=') + 7), 10),
        model:  [
            {
                image_full: "http://c3.quickcachr.fotos.sapo.pt/i/u21126828/13810303_Y4m1j.jpeg",
                image_thumb: "http://c4.quickcachr.fotos.sapo.pt/i/M49118df9/13810303_oKcJ6.jpeg",
                title_text: "Despedida",
                title_link: "http://fotos.sapo.pt/gilreis/fotos/despedida/?uid=I6nrnIBgdyq3zP7oQfw5#grande",
                description: "<p>1 asd asd asd</p>"
            },
            {
                image_full: "http://c3.quickcachr.fotos.sapo.pt/i/uea122928/13789518_V3Fa2.jpeg",
                image_thumb: "http://c6.quickcachr.fotos.sapo.pt/i/M7b119f3f/13789518_O12ou.jpeg",
                title_text: "3",
                title_link: "http://fotos.sapo.pt/smobile/fotos/?uid=bqZHVnimp8Q9KROvv6iv#grande",
                description: "<p>2 asd asd asd</p>"
            },
            {
                image_full: "http://c6.quickcachr.fotos.sapo.pt/i/uad11be32/13793402_Np0cB.jpeg",
                image_thumb: "http://c5.quickcachr.fotos.sapo.pt/i/M3c128daf/13793402_hm3sI.jpeg",
                title_text: "ferias_2012_gala_galé 165",
                title_link: "http://fotos.sapo.pt/viriatodias/fotos/ferias-gala-gal/?uid=uYT5azVkQH5uZZVCP7zS#grande",
                description: "<p>3 asd asd asd</p>"
            },
            {
                image_full: "http://c3.quickcachr.fotos.sapo.pt/i/u21126828/13810303_Y4m1j.jpeg",
                image_thumb: "http://c4.quickcachr.fotos.sapo.pt/i/M49118df9/13810303_oKcJ6.jpeg",
                title_text: "4 Despedida",
                title_link: "http://fotos.sapo.pt/gilreis/fotos/despedida/?uid=I6nrnIBgdyq3zP7oQfw5#grande",
                description: "<p>4 asd asd asd</p>"
            },
            {
                image_full: "http://c3.quickcachr.fotos.sapo.pt/i/uea122928/13789518_V3Fa2.jpeg",
                image_thumb: "http://c6.quickcachr.fotos.sapo.pt/i/M7b119f3f/13789518_O12ou.jpeg",
                title_text: "5 3",
                title_link: "http://fotos.sapo.pt/smobile/fotos/?uid=bqZHVnimp8Q9KROvv6iv#grande",
                description: "<p>5 asd asd asd</p>"
            },
            {
                image_full: "http://c6.quickcachr.fotos.sapo.pt/i/uad11be32/13793402_Np0cB.jpeg",
                image_thumb: "http://c5.quickcachr.fotos.sapo.pt/i/M3c128daf/13793402_hm3sI.jpeg",
                title_text: "6ferias_2012_gala_galé 165",
                title_link: "http://fotos.sapo.pt/viriatodias/fotos/ferias-gala-gal/?uid=uYT5azVkQH5uZZVCP7zS#grande",
                description: "<p>6 asd asd asd</p>"
            }
        ]
    });
</script>
