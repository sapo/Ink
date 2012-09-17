<div class="ink-container whatIs">
	<div class="ink-vspace">
		<h2>Customize InK</h2>
		<p>Select the InK components that you'll need for your project to get a customized package.</p>
		<p>Package... hehehe... package...</p>
	</div>
</div>


<div class="ink-container">
	<div class="ink-l40">
		asds
	</div>
	<div class="ink-l60">
		<?php echo form_open('download/custom',array('class'=>'ink-labels-above')) ?>
		<?php echo form_fieldset('Modules') ?>
		<?php echo form_error('empty_form') ?>
		<div class="ink-form-row">
		<p class="ink-field-tip">lorem ipsum dolor sit amet...</p>
		<?php foreach($modules as $module): ?>		
			<?php echo form_checkbox($module['attributes']); ?>
			<?php echo form_label($module['label']['text'], $module['label']['for']) ?>		
		<?php endforeach ?>
		</div>
		<?php echo form_fieldset_close() ?>
		<?php echo form_fieldset('Options') ?>
		<div class="ink-form-row">
		<?php foreach($options as $option): ?>		
			<?php echo form_checkbox($option['attributes']); ?>
			<?php echo form_label($option['label']['text'], $option['label']['for']) ?>		
		<?php endforeach ?>
		</div>
		<?php echo form_fieldset_close() ?>
		<?php echo form_submit(array('name' => 'download', 'value' => 'download', 'class' => 'button ink-info')); ?>
		<?php echo form_close() ?>
	</div>
</div>