    <div class="ink-section">
        <div class="ink-row ink-vspace">

            <div class="ink-l40">
                <div class="ink-gutter"> 
                    <h3 id="gallery">Gallery</h3>
                    <p>
                        The <i>Gallery</i> component allows you to show images in a &quot;carousel&quot; format.
                        Supports several <a href="#" class="modal">configurations</a> and touch events!
                    </p>
                </div>
            </div>

            <div class="ink-l60">
                <div class="ink-gutter ink-vspace">
                    <div id="gal"></div>
                </div> <!-- row -->
            </div>

			<script type="text/javascript">
				var g = new SAPO.Ink.Gallery('#gal', {
					layout: 0,
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
						}
					]
				});
			</script>

        </div>
    </div>
