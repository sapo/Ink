<?php

$CI =& get_instance();
$base_url = $CI->config->base_url();
$config['ink_version_number'] = '1.0';
$config['latest_zip_url'] = 'http://css.ink.sapo.pt/v1/ink-v1.zip';


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
	),
    
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

	/**
	 * Grid Module Configuration
	 */
	'grid' => array(
		'label' => array(
			'text' => 'Layout',
			'for' => 'modules_grid'
		),
		'attributes' => array(
			'id' => 'modules_grid',
			'name' => 'modules[]',
			'checked' => 'checked'
		),
		'implicit_files' => array( 'less/large.less', 'less/medium.less', 'less/small.less', 'less/footer.less' )
	),
	'nav' => array(
		'label' => array(
			'text' => 'Navigation',
			'for' => 'modules_navigation'
		),
		'attributes' => array(
			'id' => 'modules_navigation',
			'name' => 'modules[]',
			'checked' => 'checked'
		),
        'implicit_files' => array( 'less/nav-pills.less', 'less/nav-breadcrumbs.less', 'less/nav-pagination.less' )
	),
	'typo' => array(
		'label' => array(
			'text' => 'Typography',
			'for' => 'modules_typography'
		),
		'attributes' => array(
			'id' => 'modules_typography',
			'name' => 'modules[]',
			'checked' => 'checked'
		),
		'implicit_files' => array('less/webfont.less')
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
		),
		'implicit_files' => array()
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
		),
		'implicit_files' => array()
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
		),
		'implicit_files' => array('less/buttons.less','less/buttons.less')
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
		),
		'implicit_files' => array()
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

    /* 
    Scaffolding
    @ink-site-width
    @ink-grid-gutter
    @body-background
    @headings-color
    @border-radius 
    */
    'Scaffolding' => array(
        'ink-site-width' => array(
            'label'             => '',
            'placeholder'       => '960px',
            'default_value'     => '',
            'type'              => 'measure',
            'required'          => FALSE
        ),
        'ink-grid-gutter' => array(
            'label'             => '',
            'placeholder'       => '32px',
            'default_value'     => '',
            'type'              => 'measure',
            'required'          => FALSE
        ),
        'body-background' => array(
            'label'             => '',
            'placeholder'       => '#f7f7f7',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
        'border-radius' => array(
            'label'             => '',
            'placeholder'       => '4px',
            'default_value'     => '',
            'type'              => 'measure',
            'required'          => FALSE
        ),
    ),

    /*
    Typography
    @headings-color
    @text-color
    @font-family
    @font-condensed
    @font-size
    @headings-color             
    @link-color
    @link-visited-color
    @link-active-color
    @link-hover-color
    @link-focus-color    
    */
    'Typography' => array(
		'font-family' => array(
            'label'             => '',
            'placeholder'       => "Ubuntu, 'Helvetica Neue', Helvetica, Arial, sans-serif",
            'default_value'     => '',
            'type'              => 'text',
            'required'          => FALSE
        ),
        'font-condensed' => array(
            'label'             => '',
            'placeholder'       => 'Ubuntu_condensed',
            'default_value'     => '',
            'type'              => 'text',
            'required'          => FALSE
        ),
        'font-size' => array(
            'label'             => '',
            'placeholder'       => '16px',
            'default_value'     => '',
            'type'              => 'measure',
            'required'          => FALSE
        ),
		'text-color' => array(
            'label'             => '',
            'placeholder'       => '#555',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
		'headings-color' => array(
            'label'             => '',
            'placeholder'       => '#404040',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
    ),
	
	/* 
    Scaffolding
    @ink-site-width
    @ink-grid-gutter
    @body-background
    @headings-color
    @border-radius 
    */
	'Links' => array(
	    'link-color' => array(
            'label'             => '',
            'placeholder'       => '#0069D6',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
        'link-visited-color' => array(
            'label'             => '',
            'placeholder'       => '#808080',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
        'link-active-color' => array(
            'label'             => '',
            'placeholder'       => '#FF0000',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
        'link-hover-color' => array(
            'label'             => '',
            'placeholder'       => '#007ED5',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
        'link-focus-color' => array(
            'label'             => '',
            'placeholder'       => '#0069D6',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
    ),

    
	/*
    Alerts & Errors
    @ink-alert-bg
    @ink-alert-error-bg
    @ink-alert-success-bg
    @ink-alert-info-bg
    @warning-bg
    @caution-bg
    @error-bg
    @success-bg
    @info-bg
    */
    'Alerts & Errors' => array(
        'ink-alert-bg' => array(
            'label'             => '',
            'placeholder'       => '#f9e0a4',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
        'ink-alert-error-bg' => array(
            'label'             => '',
            'placeholder'       => '#eb6363',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
        'ink-alert-success-bg' => array(
            'label'             => '',
            'placeholder'       => '#9dce62',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
        'ink-alert-info-bg' => array(
            'label'             => '',
            'placeholder'       => '#479cd8',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
        'warning-bg' => array(
            'label'             => '',
            'placeholder'       => '#FFA500',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
        'caution-bg' => array(
            'label'             => '',
            'placeholder'       => '#FF0000',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
        'error-bg' => array(
            'label'             => '',
            'placeholder'       => '#FF0000',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
        'success-bg' => array(
            'label'             => '',
            'placeholder'       => '#00FF00',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
        'info-bg' => array(
            'label'             => '',
            'placeholder'       => '#0000FF',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
    ),
	
	
	/*
    Forms
    @form-field-fontSize
    @form-field-borderColor
    @form-field-bg
    @focusColor                   // This value must be rbga
    @disabledColor
    @errorColor
    @warningColor
    */
    'Forms' => array(
        'form-field-fontSize' => array(
            'label'             => '',
            'placeholder'       => '14px',
            'default_value'     => '',
            'type'              => 'measure',
            'required'          => FALSE
        ),
        'form-field-borderColor' => array(
            'label'             => '',
            'placeholder'       => '#ddd',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
        'form-field-bg' => array(
            'label'             => '',
            'placeholder'       => '#fff',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
        'focusColor' => array(
            'label'             => '',
            'placeholder'       => 'rgba(153, 153, 153, 0.6)',
            'default_value'     => '',
            'type'              => 'rgba',
            'required'          => FALSE
        ),
        'disabledColor' => array(
            'label'             => '',
            'placeholder'       => '#eee',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
        'errorColor' => array(
            'label'             => '',
            'placeholder'       => 'rgba(200, 10, 16, 0.5)',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
        'warningColor' => array(
            'label'             => '',
            'placeholder'       => 'rgba(255, 156, 0, 0.6)',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
    ),

    
    /*
    Buttons
    @btn-fontSize
    @btn-bg
    */
    'Buttons' => array(
        'btn-fontSize' => array(
            'label'             => '',
            'placeholder'       => '0.938em',
            'default_value'     => '',
            'type'              => 'measure',
            'required'          => FALSE
        ),
        'btn-bg' => array(
            'label'             => '',
            'placeholder'       => '#eee',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
    ),


    /*
    Footer
    @footer-min-height
    @footer-bgColor
    */
    'Footer' => array(
        'footer-min-height' => array(
            'label'             => '',
            'placeholder'       => '8em',
            'default_value'     => '',
            'type'              => 'measure',
            'required'          => FALSE
        ),
        'footer-bgColor' => array(
            'label'             => '',
            'placeholder'       => '#f0f0f0',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
    ),

	/*
    Tables
    @table-cell-borderColor
    @table-zebra-rowColor
    @table-row-hoverColor
    */
    'Tables' => array(
        'table-cell-borderColor' => array(
            'label'             => '',
            'placeholder'       => '#ccc',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
        'table-zebra-rowColor' => array(
            'label'             => '',
            'placeholder'       => '#f9f9f9',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
        'table-row-hoverColor' => array(
            'label'             => '',
            'placeholder'       => '#fefbd6',
            'default_value'     => '',
            'type'              => 'color',
            'required'          => FALSE
        ),
    ),
);

$config['ui_components'] = array(
    'gallery' => array(
        'label'     => 'Gallery',
        'view'      => 'js/ui/gallery',
    ),
    'modal'         => array(
        'label'     => 'Modal',
        'view'      => 'js/ui/modal',
    ),
    'table' => array(
        'label'     => 'Table',
        'view'      => 'js/ui/table',
    ),
    'tree_view' => array(
        'label'     => 'Tree View',
        'view'      => 'js/ui/tree_view',
    ),
    'sortable_list' => array(
        'label'     => 'Sortable List',
        'view'      => 'js/ui/sortable_list',
    ),
    'date_picker'   => array(
        'label'     => 'Date Picker',
        'view'      => 'js/ui/date_picker',
    ),
    'tabs'         => array(
        'label'     => 'Tabs',
        'view'      => 'js/ui/tabs',
    ),
);

?>