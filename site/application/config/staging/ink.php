<?php

/*
|--------------------------------------------------------------------------
| Base Site PAth
|--------------------------------------------------------------------------
|
| Real application path| 
|
|	/var/www/someplace
|
| If this is not set then CodeIgniter will guess the protocol, domain and
| path to your installation.
|
*/


$config['base_path']	= '/home/pedro/work/ink/site/';

/*
|--------------------------------------------------------------------------
| Base Site PAth
|--------------------------------------------------------------------------
|
| Real application path| 
|
|	/var/www/someplace
|
| If this is not set then CodeIgniter will guess the protocol, domain and
| path to your installation.
|
*/


$config['ink_path']	= '/home/pedro/work/ink/ink/';

/*
|--------------------------------------------------------------------------
| Base Site PAth
|--------------------------------------------------------------------------
|
| Real application path| 
|
|	/var/www/someplace
|
| If this is not set then CodeIgniter will guess the protocol, domain and
| path to your installation.
|
*/

$config['ink_version_number'] = '1.0';

$config['ink_files_location'] = "";

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
		'url' => 'inkjs'
	)
);

$config['ink_modules'] = array(
	array(
		'label' => array(
			'text' => 'Layout',
			'for' => 'layout'
		),
		'attributes' => array(
			'id' => 'layout',
			'name' => 'layout',
			'value' => '1'
		)
	),
	array(
		'label' => array(
			'text' => 'Navigation',
			'for' => 'navigation'
		),
		'attributes' => array(
			'id' => 'navigation',
			'name' => 'navigation',
			'value' => '1'
		)
	),
	array(
		'label' => array(
			'text' => 'Typography',
			'for' => 'typo'
		),
		'attributes' => array(
			'id' => 'typo',
			'name' => 'typo',
			'value' => '1'
		)
	),
	array(
		'label' => array(
			'text' => 'Icons',
			'for' => 'icons'
		),
		'attributes' => array(
			'id' => 'icons',
			'name' => 'icons',
			'value' => '1'
		)
	),
	array(
		'label' => array(
			'text' => 'Forms',
			'for' => 'forms'
		),
		'attributes' => array(
			'id' => 'forms',
			'name' => 'forms',
			'value' => '1'
		)
	),
	array(
		'label' => array(
			'text' => 'Alerts',
			'for' => 'alerts'
		),
		'attributes' => array(
			'id' => 'alerts',
			'name' => 'alerts',
			'value' => '1'
		)
	),
	array(
		'label' => array(
			'text' => 'Tables',
			'for' => 'tables'
		),
		'attributes' => array(
			'id' => 'tables',
			'name' => 'tables',
			'value' => '1'
		)
	)
);

$config['ink_options'] = array(
	array(
		'label' => array(
			'text' => 'Include LESS files?',
			'for' => 'option-include-less'
		),
		'attributes' => array(
			'id' => 'option-include-less',
			'name' => 'option-include-less',
			'value' => '1'
		)
	),
	array(
		'label' => array(
			'text' => 'Minify css?',
			'for' => 'option-minify'
		),
		'attributes' => array(
			'id' => 'option-minify',
			'name' => 'option-minify',
			'value' => '1'
		)
	),
);

?>