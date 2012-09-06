<?php

add_action( 'after_setup_theme', 'ink_setup' );

if ( ! function_exists('ink_setup_styles')):

	function ink_setup_styles()
	{
		wp_register_style('ink_styles', get_template_directory_uri() . '/style.css', $ver = 1, $media = 'all');
		wp_enqueue_style('ink_styles');
	}

endif;


if ( ! function_exists('ink_setup_menus')):

	function ink_setup_menus()
	{
		register_nav_menu('primary', 'InK');
	}

endif;

if(!function_exists('ink_setup')):

	function ink_setup()
	{
		// handle the theme stylesheets
		ink_setup_styles();

		// handle the theme menu
		ink_setup_menus();
	}

endif;

?>