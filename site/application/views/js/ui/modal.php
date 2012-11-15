<?php
$js = <<<JS
<div id="modalContent" style="display:none">
    <div class="ink-space">
        <h1>Some title</h1>
        <p><em>Hello modal!</em></p>
        <p>dismiss it pressing the close button or the escape key.</p>
    </div>
</div>
<a style="cursor:pointer;" id="bModal">Open modal</a>
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
	<div class="ink-vspace">
		<h3 id="modal">Modal</h3>
		<p>
			The <i>Modal</i> component was designed to replace the common/native modal boxes that browser have, providing some features:
            <ul>
                <li>HTML formatted messages</li>
                <li>Configuration of height and width of the modal</li>
                <li>Remove the dismissing through the 'x'/close button. Particularly useful if you want to define another button to do the dismissing</li>
            </ul> 
        </p>
        <p>
            Other options available and a more detailed explanation of the above ones are available in the <a target="_blank" href="http://js.sapo.pt/SAPO/Ink/Modal/doc.html">technical documentation</a>.
		</p>
        <br/>
		<p><?php echo $js;?> to view the component working.</p>
		<a data-target="modal_sourcecode" class="ink-button toggleTrigger">View Source Code</a>
		<pre id="modal_sourcecode" style="display:none" class="ink-l100 prettyprint linenums"><?php echo(htmlentities( $js )); ?></pre>
	</div>
</div>