<div class="whatIs" id="nav-home">
   <div class="ink-container">
		<h2>Customize InK</h2>
		<p>Select the InK components that best suit your project needs and get a customized package ready to go.</p>
	</div>
</div>

<div>
	<div class="ink-container">
		<nav class="ink-navigation ink-collapsible ink-dockable" data-fixed-height="44">
			<ul class="menu horizontal black ink-l100 ink-m100 ink-s100">
				<li class="active"><a class="scrollableLink home" href="#nav-home">
					<i class="icon-chevron-up ink-for-l"></i>
					<span class="ink-for-m ink-for-s">Back to Top</span>
				</a></li>
				<li><a href="#modules">Modules</a></li>
				<li><a href="#options">Options</a></li>
				<li><a href="#configuration">Configuration</a></li>
			</ul>
		</nav>
	</div>
</div>

<?php echo form_open('download/custom',array('class'=>'ink-form block ink-container')) ?>

	<div class="ink-row ink-vspace">
		<?php
			if( $errors )
			{
				echo '<div class="ink-alert-block error"><button class="ink-close">×</button><h4>The following errors have occurred:</h4>';
				foreach( $errors as $group => $errors_group )
				{
					echo '<ul><li>'.(is_array($errors_group) ? implode("</li><li>",$errors_group) : $errors_group).'</li></ul>';
				}

				echo '</div>';
			}
		?>
		
		<div class="ink-l50">
			<div class="ink-gutter">
				<?php echo form_fieldset('<h3 id="modules">Modules</h3>',array('class'=>(($errors && in_array('modules',array_keys($errors))) ? 'error' : '') ) )?>
				<ul class="ink-form-wrapper unstyled">
				<p class="ink-field-tip">lorem ipsum dolor sit amet...</p>
				<?php foreach($modules as $value => $module): ?>		
					<li>
					<?php echo form_checkbox(array_merge($module['attributes'],array('value'=>$value,'checked'=>( ( isset($post) && ( isset($post['modules']) && in_array($value,$post['modules']) ) ) || (!isset($post) && $module['attributes']['checked']) )   ))); ?>
					<?php echo form_label($module['label']['text'], $module['label']['for']) ?>		
					</li>
				<?php endforeach ?>
				</ul>
				<?php echo form_fieldset_close() ?>
			</div>
		</div>		
		
		<div class="ink-l50">
			<div class="ink-gutter">
				<?php echo form_fieldset('<h3 id="options">Options</h3>') ?>
				<ul class="ink-form-wrapper unstyled"><p class="ink-field-tip">lorem ipsum dolor sit amet...</p>
				<?php foreach($options as $option): ?>
					<li>
					<?php echo form_checkbox(( $option['attributes'] + array('checked'=>(isset($post['options']) && in_array($option['attributes']['value'],$post['options'])) ) ) ); ?>
					<?php echo form_label($option['label']['text'], $option['label']['for']) ?>		
					</li>
				<?php endforeach ?>
				</ul>
				<?php echo form_fieldset_close() ?>
			</div>
		</div>		
	</div>
	
	<?php echo form_fieldset('<h3 id="configuration">Configuration</h3>') ?>
		<div class="ink-row">
		<?php foreach($config as $group => $vars): ?>
			<div class="ink-l33">
				<div class="ink-gutter ink-vspace">
				<h5><?php echo $group;?></h5>
				<?php foreach($vars as $var_id => $var): ?>
				<div class="control <?php if( isset($errors['vars'][$var_id]) || ( isset($var['required']) && ( $var['required'] === TRUE ) ) ) { ?>ink-required-field<?php }?>">
					<?php echo form_label('@'.(!empty($var['label']) ? $var['label'] : $var_id),$var_id);?>
					<?php echo form_input(array('type' => 'text','id'=>$var_id,'name'=>'vars[' . $var_id . ']','placeholder'=>$var['placeholder'], 'value' => ( ( isset($post) && ( isset($post['vars']) && in_array($var_id,array_keys($post['vars'])) ) ) ? $post['vars'][$var_id] : ((isset($var['default_value']) && !empty($var['default_value'])) ? $var['default_value'] : $var['placeholder'])), 'class' => (($var['type']=='color') ? 'colorPicker' : '')   )); ?>
				</div>
				<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach ;?>
		</div>
		<div class="ink-vspace ink-download">
		<?php echo form_submit(array('name' => 'download', 'value' => 'download', 'class' => 'ink-button info')); ?>
		</div>
	<?php echo form_fieldset_close() ?>
	
	</div>
	<?php echo form_close() ?>

	<div class="modal"></div>
</div>