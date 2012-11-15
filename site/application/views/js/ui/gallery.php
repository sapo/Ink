<?php
/**
 * Separated markup to be repeated in the different tabs
 * @var [type]
 */
$markup_for_tab = <<<HTML
<ul class="ink-gallery-source">
    <li class="hentry hmedia">
        <a rel="enclosure" href="http://imgs.sapo.pt/ink/assets/imgs/ink-js-placeholders/1.1.png">
            <img alt="s1" src="http://imgs.sapo.pt/ink/assets/imgs/ink-js-placeholders/thumb1.png">
        </a>
        <a class="bookmark" href="http://imgs.sapo.pt/ink/assets/imgs/ink-js-placeholders/1.1.png">
            <span class="entry-title">s1</span>
        </a>
        <span class="entry-content"><p>hello world 1</p></span>
    </li>
    <li class="hentry hmedia">
        <a rel="enclosure" href="http://imgs.sapo.pt/ink/assets/imgs/ink-js-placeholders/1.2.png">
            <img alt="s2" src="http://imgs.sapo.pt/ink/assets/imgs/ink-js-placeholders/thumb2.png">
        </a>
        <a class="bookmark" href="http://imgs.sapo.pt/ink/assets/imgs/ink-js-placeholders/1.2.png">
            <span class="entry-title">s2</span>
        </a>
        <span class="entry-content"><p>hello world 2</p></span>
    </li>
    <li class="hentry hmedia">
        <a rel="enclosure" href="http://imgs.sapo.pt/ink/assets/imgs/ink-js-placeholders/1.3.png">
            <img alt="s3" src="http://imgs.sapo.pt/ink/assets/imgs/ink-js-placeholders/thumb3.png">
        </a>
        <a class="bookmark" href="http://imgs.sapo.pt/ink/assets/imgs/ink-js-placeholders/1.3.png">
            <span class="entry-title">s3</span>
        </a>
        <span class="entry-content"><p>hello world 3</p></span>
    </li>
    <li class="hentry hmedia">
        <a rel="enclosure" href="http://imgs.sapo.pt/ink/assets/imgs/ink-js-placeholders/1.4.png">
            <img alt="s4" src="http://imgs.sapo.pt/ink/assets/imgs/ink-js-placeholders/thumb4.png">
        </a>
        <a class="bookmark" href="http://imgs.sapo.pt/ink/assets/imgs/ink-js-placeholders/1.4.png">
            <span class="entry-title">s4</span>
        </a>
        <span class="entry-content"><p>hello world 4</p></span>
    </li>
</ul>
HTML;

$markup_for_sourcecode = <<<HTML
<ul class="ink-gallery-source">
    <li class="hentry hmedia">
        <a rel="enclosure" href="http://imgs.sapo.pt/ink/assets/imgs_gal/1.1.png">
            <img alt="s1" src="http://imgs.sapo.pt/ink/assets/imgs_gal/thumb1.png">
        </a>
        <a class="bookmark" href="http://imgs.sapo.pt/ink/assets/imgs_gal/1.1.png">
            <span class="entry-title">s1</span>
        </a>
        <span class="entry-content"><p>hello world 1</p></span>
    </li>
    <li class="hentry hmedia">
        <a rel="enclosure" href="http://imgs.sapo.pt/ink/assets/imgs_gal/1.2.png">
            <img alt="s1" src="http://imgs.sapo.pt/ink/assets/imgs_gal/thumb2.png">
        </a>
        <a class="bookmark" href="http://imgs.sapo.pt/ink/assets/imgs_gal/1.2.png">
            <span class="entry-title">s2</span>
        </a>
        <span class="entry-content"><p>hello world 2</p></span>
    </li>
</ul>
HTML;

/**
 * Different vars for different layouts
 */
$js_0 = <<<JS
<script type="text/javascript">
    var gal0 = new SAPO.Ink.Gallery('.ink-gallery-source', {layout:0});
</script>
JS;

$js_1 = <<<JS
<script type="text/javascript">
    var gal1 = new SAPO.Ink.Gallery('.ink-gallery-source', {layout:1});
</script>
JS;

$js_2 = <<<JS
<script type="text/javascript">
    var gal2 = new SAPO.Ink.Gallery('.ink-gallery-source', {layout:2});
</script>
JS;

$js_3 = <<<JS
<script type="text/javascript">
    var gal3 = new SAPO.Ink.Gallery('.ink-gallery-source', {layout:3});
</script>
JS;
?>
<div class="ink-section">
	<div class="ink-row ink-vspace">

		<div class="ink-l40">
			<div class="ink-gutter"> 
				<h3 id="gallery">Gallery</h3>
				<p>
					The <i>Gallery</i> component provides you an easy way to show images in a &quot;carousel&quot; format.
                </p>
                <p>
                    Besides being a responsive component, it also allows you to set other configurations such as:
                    <ul>
                        <li>Captions</li>
                        <li>Thumbnails</li>
                        <li>Images' maximum width and height, in the full and thumbnail version</li>
                        <li>Type of Layout</li>
                    </ul>
                </p>
                <p>
                    Want to know more?
                </p>
                <p>
                    Check the <a target="_blank" href="http://js.sapo.pt/SAPO/Ink/Gallery/doc.html">technical documentation</a>.</p>
				</p>
			</div>
		</div>

		<div class="ink-l60">
			<div class="ink-gutter">
				<div class="ink-tabs-gallery">
                    <nav class="ink-navigation">
                        <ul class="ink-tabs-nav menu horizontal">
                            <li><a href="#ui_gallery_layout0">Layout 0</a></li>
                            <li><a href="#ui_gallery_layout1">Layout 1</a></li>
                            <li><a href="#ui_gallery_layout2">Layout 2</a></li>
                            <li><a href="#ui_gallery_layout3">Layout 3</a></li>
                        </ul>
                    </nav>
                    <div id="ui_gallery_layout0" class="ink-tabs-container">
                        <?php echo $markup_for_tab."\n".$js_0;?>
                        <a href="#" data-target="gallery_sourcecode_0" class="ink-button toggleTrigger">View Source Code</a>
                        <pre id="gallery_sourcecode_0" style="display:none" class="ink-l100 prettyprint linenums"><?php echo(htmlentities( $markup_for_sourcecode."\n".$js_0 )); ?></pre>
                    </div>
                    <div id="ui_gallery_layout1" class="ink-tabs-container">
                        <?php echo $markup_for_tab."\n".$js_1;?>
                        <a href="#" data-target="gallery_sourcecode_1" class="ink-button toggleTrigger">View Source Code</a>
                        <pre id="gallery_sourcecode_1" style="display:none" class="ink-l100 prettyprint linenums"><?php echo(htmlentities( $markup_for_sourcecode."\n".$js_1 )); ?></pre>
                    </div>
                    <div id="ui_gallery_layout2" class="ink-tabs-container">
                        <?php echo $markup_for_tab."\n".$js_2;?>
                        <a href="#" data-target="gallery_sourcecode_2" class="ink-button toggleTrigger">View Source Code</a>
                        <pre id="gallery_sourcecode_2" style="display:none" class="ink-l100 prettyprint linenums"><?php echo(htmlentities( $markup_for_sourcecode."\n".$js_2 )); ?></pre>
                    </div>
                    <div id="ui_gallery_layout3" class="ink-tabs-container">
                        <?php echo $markup_for_tab."\n".$js_3;?>
                        <a href="#" data-target="gallery_sourcecode_3" class="ink-button toggleTrigger">View Source Code</a>
                        <pre id="gallery_sourcecode_3" style="display:none" class="ink-l100 prettyprint linenums"><?php echo(htmlentities( $markup_for_sourcecode."\n".$js_3 )); ?></pre>
                    </div>
                </div>
                <script type="text/javascript">
                    var gallery_tabs = new SAPO.Ink.Tabs('.ink-tabs-gallery', {
                        disabled: ['#stuff', '#more_stuff'], 
                        active: '#ui_gallery_layout0',
                        onBeforeChange: function(tab){}, 
                        onChange: function(tab){}
                    });
                </script>
			</div>
		</div>
	</div>
</div>
