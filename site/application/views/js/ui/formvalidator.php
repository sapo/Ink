<?php
$js = <<<JS
<form id="myform" class="ink-form block" method="post" action="#" onsubmit="return SAPO.Ink.FormValidator.validate(this);">
    <fieldset>
        <div class="control required">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" class="ink-fv-required" />
        </div>
        <div class="control required">
            <label for="mail">Email:</label>
            <input type="text" name="mail" id="mail" class="ink-fv-required ink-fv-email" />
        </div>
        <div class="control required">
            <label for="pass">Password: </label>
            <input type="password" name="pass" id="pass" class="ink-fv-required ink-fv-confirm" />
        </div>
        <div class="control required">
            <label for="confpass">Confirm Password:</label>
            <input type="password" name="confpass" id="confpass" class="ink-fv-required ink-fv-confirm" />
        </div>
        <ul class="control-group required">
            <li><p class="label">Radio test: </p></li>
            <li><input type="radio" name="radio1" id="radio1_g" value="1" class="ink-fv-required" /> <label for="radio1_g">radio 1</label> </li>
            <li><input type="radio" name="radio1" id="radio2_g" value="2" class="ink-fv-required" /> <label for="radio2_g">radio 2</label> </li>
            <li><input type="radio" name="radio1" id="radio3_g" value="3" class="ink-fv-required" /> <label for="radio3_g">radio 3</label> </li>
        </ul>
    </fieldset>
    <div>
        <input type="submit" name="sub" value="Submit" class="ink-button success" />
    </div>
</form>
JS;
?>
<div class="ink-section">
	<div class="ink-row ink-vspace">
		<div class="ink-l40">
			<div class="ink-gutter"> 
				<h3 id="formvalidator">Form Validator</h3>
				<p>
					The <i>FormValidator</i> component provides an easy way to validate forms before submitting them.
					It can: 
                    <ul>
                        <li>Detect required fields</li>
                        <li>Validate some field types</li>
                        <li>Detect match with password and confirmation password</li>
                        <li>Use custom field types</li>
                    </ul>
                </p>
                <br/>
                <p>More examples and detailed configurations, in the <a target="_blank" href="http://js.sapo.pt/SAPO/Ink/SortableList/doc.html">technical documentation</a>.</p>
			</div>
		</div>
		<div class="ink-l60">
			<div class="ink-gutter">
				<div class="box">
					<?php echo $js;?>
				</div>
				<a href="#" data-target="formvalidator_sourcecode" class="ink-button toggleTrigger">View Source Code</a>
				<pre id="formvalidator_sourcecode" style="display:none" class="prettyprint linenums"><?php echo(htmlentities( $js )); ?></pre>
			</div>
		</div>
	</div>
</div>
