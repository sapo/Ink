<?php
$js = <<<JS
<div id="modalContent" style="display:none">
    <div class="ink-space">
        <h1>Some title</h1>
        <p><em>Hello modal!</em></p>
        <p>dismiss it pressing the close button or the escape key.</p>
    </div>
</div>
<button class="ink-button" id="bModal">Open modal</button>
<script type="text/javascript">
    SAPO.Dom.Event.observe('bModal', 'click', function(ev) {
        new SAPO.Ink.Modal('#modalContent', {
            width:  500,
            height: 250
        });
    });
</script>
JS;
?>
    <div class="ink-section">
        <div class="ink-row ink-vspace">
            <div class="ink-l40">
                <div class="ink-gutter"> 
                    <h3 id="modal">Modal</h3>
                    <p>
                        The <i>Modal</i> component was designed to replace the common, native, modal boxes that do not support HTML content, cannot be configured either on options/buttons available, themes, etc.<br/>
                        This component allows a set of <a href="#" class="modal">configurations</a> that will make the act of showing information on a modal box much more user-friendly.
                    </p>
                </div>
            </div>
            <div class="ink-l60">
				<div class="ink-gutter">
                    <div class="ink-row box">
    					<?php echo $js;?>
                        <br/>
                        <a href="#" data-target="modal_sourcecode" class="toggleTrigger">View Source Code</a>
                        <pre id="modal_sourcecode" style="display:none" class="ink-l100 prettyprint linenums"><?php echo(htmlentities( $js )); ?></pre>
                    </div>
				</div>
            </div>
        </div>
    </div>