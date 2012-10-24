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
		<?php
			if( $errors )
			{
				echo '<div class="block-alert-msg error"><button class="close">x</button><h4>The following errors have occurred:</h4>';
				foreach( $errors as $group => $errors_group )
				{
					echo '<ul><li>'.(is_array($errors_group) ? implode("</li><li>",$errors_group) : $errors_group).'</li></ul>';
				}

				echo '</div>';
			}
		?>
		<?php echo form_open('download/custom',array('class'=>'ink-labels-above')) ?>
		<?php echo form_fieldset('<h4>Modules</h4>',array('class'=>(($errors && in_array('modules',array_keys($errors))) ? 'error' : '') ) )?>
		<div class="ink-form-row ink-form-wrapper">
		<p class="ink-field-tip">lorem ipsum dolor sit amet...</p>
		<?php foreach($modules as $value => $module): ?>		
			<?php echo form_checkbox(array_merge($module['attributes'],array('value'=>$value,'checked'=>( ( isset($post) && ( isset($post['modules']) && in_array($value,$post['modules']) ) ) || (!isset($post) && $module['attributes']['checked']) )   ))); ?>
			<?php echo form_label($module['label']['text'], $module['label']['for']) ?>		
		<?php endforeach ?>
		</div>
		<?php echo form_fieldset_close() ?>
		<?php echo form_fieldset('<h4>Options</h4>') ?>
		<div class="ink-form-row ink-form-wrapper">
		<?php foreach($options as $option): ?>
			<?php echo form_checkbox(( $option['attributes'] + array('checked'=>(isset($post['options']) && in_array($option['attributes']['value'],$post['options'])) ) ) ); ?>
			<?php echo form_label($option['label']['text'], $option['label']['for']) ?>		
		<?php endforeach ?>
		</div>
		<?php echo form_fieldset_close() ?>
		<?php echo form_fieldset('<h4>Configuration</h4>') ?>
		<?php foreach($config as $group => $vars): ?>	
			<div class="ink-l33">
					<?php foreach($vars as $var_id => $var): ?>
					<div class="ink-rspace ink-form-wrapper <?php if( isset($errors['vars'][$var_id]) ) { ?>ink-required-field<?php }?>">
						<?php echo form_label($var_id,(!empty($var['label']) ? $var['label'] : $var_id));?>
						<?php echo form_input(array('type' => 'text','id'=>$var_id,'name'=>'vars[' . $var_id . ']','placeholder'=>$var['placeholder'], 'value' => ( ( isset($post) && ( isset($post['vars']) && in_array($var_id,array_keys($post['vars'])) ) ) ? $post['vars'][$var_id] : $var['default_value']), 'class' => (($var['type']=='color') ? 'colorPicker' : '')   )); ?>
					</div>
					<?php endforeach; ?>
			</div>
		<?php endforeach ;?>
		<?php echo form_fieldset_close() ?>
		<?php echo form_submit(array('name' => 'download', 'value' => 'download', 'class' => 'ink-button info')); ?>
		<?php echo form_close() ?>
	</div>
	<div class="modal"></div>
</div>