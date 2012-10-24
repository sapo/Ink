<?php

$CI =& get_instance();
$base_url = $CI->config->base_url();

$config['base_path']	= '/var/www/ink/site/';
$config['build_path']	= '/FASMOUNT/SAPO/VMNODE/';
$config['latest_path']	= '/var/www/ink/ink/';
$config['build_normal_css_url']	= 'http://1.135.7.199:8081/getcss';
$config['build_minimized_css_url']	= 'http://1.135.7.199:8081/getcss';
$config['ink_version_number'] = '1.0';

$config['assets_url'] = $base_url.'assets/';
$config['assets_js'] = $base_url.'assets/js/';
$config['assets_css'] = $base_url.'assets/css/';
$config['assets_imgs'] = $base_url.'assets/imgs/';


$config['site_title'] = 'InK - Interface Kit';

$config['site_pages'] = array(
	array(
		'text' => 'Layout',
		'url' => 'layout'
	),
	array(
		'text' => 'Navigation',
		'url' => 'navigation'
	),
	array(
		'text' => 'Typography',
		'url' => 'typography'
	),
	array(
		'text' => 'Icons',
		'url' => 'icons'
	),
	array(
		'text' => 'Forms',
		'url' => 'forms'
	),
	array(
		'text' => 'Alerts',
		'url' => 'alerts'
	),
	array(
		'text' => 'Tables',
		'url' => 'tables'
	),
	array(
		'text' => 'InK JS',
		'url' => 'inkjs',
		'submenu' => array(
			'ui' => array(
				'text' => 'UI',
				'url' => 'js/ui'
			),
			'core' => array(
				'text' => 'Core',
				'url' => 'js/core'
			)
		)
	)
);


$config['ink_files'] = array(
	
	'required' 	=>	array(

		'css' => array(
			'normalize',
			'common'
		),

		'less' => array(
			'conf',
			'lib',
			'common'
		)

	),
	
	'modules' => array(

		'layout' => array(

			'css' => array(
				'grid',
				'grids/small',
				'grids/medium',
				'grids/large'
			),

			'less' => array(
				'grid',
				'grids/small',
				'grids/medium',
				'grids/large'
			)

		),

		'navigation' => array(
			'css' => 'navigation',
			'less' => 'navigation'
		),

		'typography' => array(
			'css' => 'typo',
			'less' => 'typo'
		),

		'icons' => array(
			'css' => 'icons',
			'less' => 'icons'
		),

		'forms' => array(
			'css' => 'forms',
			'less' => 'forms'
		), 

		'alerts' => array(
			'css' => 'alerts',
			'less' => 'alerts'
		),

		'tables' => array(
			'css' => 'tables',
			'less' => 'tables'
		)
	),
	'ie' => array(
		'css' => array(
			'ie6',
			'ie7'
		),
		'less' => array(
			'ie6',
			'ie7'
		)
	)
);


$config['ink_modules'] = array(
	'grid' => array(
		'label' => array(
			'text' => 'Layout',
			'for' => 'modules_grid'
		),
		'attributes' => array(
			'id' => 'modules_grid',
			'name' => 'modules[]',
			'checked' => 'checked'
		)
	),
	'navigation' => array(
		'label' => array(
			'text' => 'Navigation',
			'for' => 'modules_navigation'
		),
		'attributes' => array(
			'id' => 'modules_navigation',
			'name' => 'modules[]',
			'checked' => 'checked'
		)
	),
	'typography' => array(
		'label' => array(
			'text' => 'Typography',
			'for' => 'modules_typography'
		),
		'attributes' => array(
			'id' => 'modules_typography',
			'name' => 'modules[]',
			'checked' => 'checked'
		)
	),
	'icons' => array(
		'label' => array(
			'text' => 'Icons',
			'for' => 'modules_icons'
		),
		'attributes' => array(
			'id' => 'modules_icons',
			'name' => 'modules[]',
			'checked' => 'checked'
		)
	),
	'forms' => array(
		'label' => array(
			'text' => 'Forms',
			'for' => 'modules_forms'
		),
		'attributes' => array(
			'id' => 'modules_forms',
			'name' => 'modules[]',
			'checked' => 'checked'
		)
	),
	'alerts' => array(
		'label' => array(
			'text' => 'Alerts',
			'for' => 'modules_alerts'
		),
		'attributes' => array(
			'id' => 'modules_alerts',
			'name' => 'modules[]',
			'checked' => 'checked'
		)
	),
	'tables' => array(
		'label' => array(
			'text' => 'Tables',
			'for' => 'modules_tables'
		),
		'attributes' => array(
			'id' => 'modules_tables',
			'name' => 'modules[]',
			'checked' => 'checked'
		)
	)
);

$config['ink_options'] = array(
	'include_less' => array(
		'label' => array(
			'text' => 'Include LESS files?',
			'for' => 'include_less'
		),
		'attributes' => array(
			'id' => 'include_less',
			'value' => 'include_less',
			'name' => 'options[]'
		)
	),
	'minify_css' => array(
		'label' => array(
			'text' => 'Minify css?',
			'for' => 'minify_css'
		),
		'attributes' => array(
			'id' => 'minify_css',
			'name' => 'options[]',
			'value' => 'minify_css',
		)
	),
);


/**
 * Vars' configuration
 *
 * 	Currently there are 3 configuration groups: grid, font and color.
 * 	Each group as an associative array where the index is the name of the field and points to a children's associative array
 * with 2 required field and 2 optional: label, default_value (required), type and required (optional).
 * 	The "label" and "default_value" indexes say it all...
 * 	The "type" is a field that will help to validate the value sent by the user, correctly.
 * 	It can have one of this values:
 * 		-> color 		- This will indicate that the field must have an hexadecimal value.
 * 		-> measure 		- This will indicate that the field must have an int or float value, followed by it's measure unit.
 * 		-> text 		- This is the type by default, if not specified. Basically, the code will only validate if it's empty.
 * 		-> digit 		- This will indicate that the field must have an integer as value.
 * 		-> decimal 		- This will indicate that the field must have a float as value.
 * 		-> alpha_dash	- This will indicate that the field must have a value that contains only  alpha-numeric characters,
 * 		underscores or dashes.
 *
 * 	The "required" is a field that can either be true or false (TRUE or FALSE, is the same) and will indicate that a field
 * 	has to be filled, or not.
 */

$config['ink_config_vars'] = array(
	'group_grid' => array(
		'var-ink-grid-gutter' => array(
			'label'				=> '',
			'placeholder' 		=> '32px',
			'default_value' 	=> '',
			'type' 				=> 'measure',
			'required' 			=> TRUE
		),
		'var-site-width' => array(
			'label'				=> '',
			'placeholder' 	=> '',
			'default_value' 	=> '',
			'type' 				=> 'measure',
		)
	),
	'group_font' => array(
		'var-font-family' => array(
			'label'				=> '',
			'placeholder' 		=> '',
			'default_value' 	=> '',
			'type'				=> ''
		),
		'var-font-size' => array(
			'label'				=> '',
			'placeholder' 		=> '',
			'default_value' 	=> '',
			'type'				=> ''
		)
	),
	'group_color' => array(
		'var-body-background' => array(
			'label'				=> '',
			'placeholder' 		=> '#fff',
			'default_value' 	=> '',
			'type'				=> 'color'
		),
		'var-text-color' => array(
			'label'				=> '',
			'placeholder' 		=> '#555',
			'default_value' 	=> '',
			'type'				=> 'color'
		),
		'var-link-color' => array(
			'label'				=> '',
			'placeholder' 		=> '#0069D6',
			'default_value' 	=> '',
			'type'				=> 'color'
		),
		'var-link-visited-color' => array(
			'label'				=> '',
			'placeholder' 		=> '#808080',
			'default_value' 	=> '',
			'type'				=> 'color'
			),
		'var-link-active-color' => array(
			'label'				=> '',
			'placeholder' 		=> '#ff0000',
			'default_value' 	=> '',
			'type'				=> 'color'
		),
		'var-link-hover-color' => array(
			'label'				=> '',
			'placeholder' 		=> '#007ED5',
			'default_value' 	=> '',
			'type'				=> 'color'
		),
		'var-link-focus-color' => array(
			'label'				=> '',
			'placeholder' 		=> '#007ED5',
			'default_value' 	=> '',
			'type'				=> 'color'
		)
	)
);

?>