<?php
$js = <<<JS
<label for="dPicker" class="ink-form-inline">A date field:</label>
<input id="dPicker" type="text"></input>
<script type="text/javascript">
    var picker = new SAPO.Ink.DatePicker('#dPicker');
</script>
JS;
?>
    <div class="ink-section">
        <div class="ink-row ink-vspace">
   			<div class="ink-l40">
                <div class="ink-gutter"> 
                    <h3 id="date_picker">Date Picker</h3>
                    <p>
                        As the name says, the <i>Date Picker</i> transforms a textbox into an element that, when in use, shows a calendar to help selecting a specific date.
                        It allows several <a href="#" class="modal">configurations</a>.
                    </p>
                </div>
            </div>
   			<div class="ink-l60">
                <div class="ink-gutter">
                    <div class="ink-row box">
    					<form>
							<div class="ink-l100"><?php echo $js;?></div>
                            <pre class="ink-vspace ink-l100 prettyprint linenums"><?php echo(htmlentities( $js )); ?></pre>
    					</form>
                    </div>
				</div>
			</div>
		</div>
    </div>