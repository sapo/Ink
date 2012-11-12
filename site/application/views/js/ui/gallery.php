<?php
$js = <<<JS
<ul class="ink-gallery-source">
    <li class="hentry hmedia">
        <a rel="enclosure" href="http://ink.staging.sapo.pt/assets/imgs/ink-js-placeholders/1.1.png">
            <img alt="s1" src="http://ink.staging.sapo.pt/assets/imgs/ink-js-placeholders/thumb1.png">
        </a>
        <a class="bookmark" href="http://ink.staging.sapo.pt/assets/imgs/ink-js-placeholders/1.1.png">
            <span class="entry-title">s1</span>
        </a>
        <span class="entry-content"><p>hello world 1</p></span>
    </li>
    <li class="hentry hmedia">
        <a rel="enclosure" href="http://ink.staging.sapo.pt/assets/imgs/ink-js-placeholders/1.2.png">
            <img alt="s2" src="http://ink.staging.sapo.pt/assets/imgs/ink-js-placeholders/thumb2.png">
        </a>
        <a class="bookmark" href="http://ink.staging.sapo.pt/assets/imgs/ink-js-placeholders/1.2.png">
            <span class="entry-title">s2</span>
        </a>
        <span class="entry-content"><p>hello world 2</p></span>
    </li>
    <li class="hentry hmedia">
        <a rel="enclosure" href="http://ink.staging.sapo.pt/assets/imgs/ink-js-placeholders/1.3.png">
            <img alt="s3" src="http://ink.staging.sapo.pt/assets/imgs/ink-js-placeholders/thumb3.png">
        </a>
        <a class="bookmark" href="http://ink.staging.sapo.pt/assets/imgs/ink-js-placeholders/1.3.png">
            <span class="entry-title">s3</span>
        </a>
        <span class="entry-content"><p>hello world 3</p></span>
    </li>
    <li class="hentry hmedia">
        <a rel="enclosure" href="http://ink.staging.sapo.pt/assets/imgs/ink-js-placeholders/1.4.png">
            <img alt="s4" src="http://ink.staging.sapo.pt/assets/imgs/ink-js-placeholders/thumb4.png">
        </a>
        <a class="bookmark" href="http://ink.staging.sapo.pt/assets/imgs/ink-js-placeholders/1.4.png">
            <span class="entry-title">s4</span>
        </a>
        <span class="entry-content"><p>hello world 4</p></span>
    </li>
</ul>

<script type="text/javascript">
    var gal = new SAPO.Ink.Gallery('.ink-gallery-source', {layout:2});
</script>
JS;
?>
    <div class="ink-section">
        <div class="ink-row ink-vspace">

            <div class="ink-l30">
                <div class="ink-gutter"> 
                    <h3 id="gallery">Gallery</h3>
                    <p>
                        The <i>Gallery</i> component allows you to show images in a &quot;carousel&quot; format.
                        Supports several <a href="#" class="modal">configurations</a> and touch events!
                    </p>
                </div>
            </div>

            <div class="ink-l70">
                <div class="ink-gutter">
                    <div class="ink-row box">
                        <div class="ink-gutter"><?php echo $js; ?></div>
                        <br/>
                        <a href="#" data-target="gallery_sourcecode" class="toggleTrigger">View Source Code</a>
                        <pre id="gallery_sourcecode" style="display:none" class="prettyprint linenums ink-vspace"><?php echo(htmlentities( $js )); ?></pre>
                    </div>
                </div> <!-- row -->
            </div>
        </div>
    </div>
