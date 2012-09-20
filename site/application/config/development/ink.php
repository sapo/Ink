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

$config['build_path']	= '/home/pedro/work/ink/site/builds/';

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
	array(
		'label' => array(
			'text' => 'Layout',
			'for' => 'grid'
		),
		'attributes' => array(
			'id' => 'grid',
			'name' => 'grid',
			'value' => '1',
			'checked' => 'checked'
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
			'value' => '1',
			'checked' => 'checked'
		)
	),
	array(
		'label' => array(
			'text' => 'Typography',
			'for' => 'typography'
		),
		'attributes' => array(
			'id' => 'typography',
			'name' => 'typography',
			'value' => '1',
			'checked' => 'checked'
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
			'value' => '1',
			'checked' => 'checked'
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
			'value' => '1',
			'checked' => 'checked'
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
			'value' => '1',
			'checked' => 'checked'
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
			'value' => '1',
			'checked' => 'checked'
		)
	)
);

$config['ink_options'] = array(
	array(
		'label' => array(
			'text' => 'Include LESS files?',
			'for' => 'o-include-less'
		),
		'attributes' => array(
			'id' => 'o-include-less',
			'name' => 'o-include-less',
			'value' => '1'
		)
	),
	array(
		'label' => array(
			'text' => 'Minify css?',
			'for' => 'o-minify'
		),
		'attributes' => array(
			'id' => 'o-minify',
			'name' => 'o-minify',
			'value' => '1'
		)
	),
);

?>