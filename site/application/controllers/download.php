<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Download extends CI_Controller {


	public function __construct()
    {
    	parent::__construct();
        
        //    
        $this->paths->latest = $this->config->item('latest_path');
        $this->paths->builds = $this->config->item('build_path');
        $this->ink_version_number = $this->config->item('ink_version_number');

     }

	public function index()
	{
		$this->load->library('session');
		if( !$errors = $this->session->flashdata('errors') )
		{
			$errors = array();
		}
		else
		{
			$data['post'] = $this->session->flashdata('post');
		}
		$data['errors'] = $errors;
		$data['pages'] = $this->config->item('site_pages');
		$data['title'] = $this->config->item('site_title');
		$data['modules'] = $this->config->item('ink_modules');
		$data['options'] = $this->config->item('ink_options');
		$data['config'] = $this->config->item('ink_config_vars');

		$this->load->view('common/document_top',$data);
		$this->load->view('common/main_navigation',$data);
		$this->load->view('download',$data);
		$this->load->view('common/document_footer');
		$this->load->view('common/document_bottom');
	}

	public function latest()
	{
		$this->zip->read_dir($this->paths->latest,false);
		$this->zip->download('ink-'.$this->ink_version_number.'.zip');
	}

	/**
	 * Function to generate the custom download for InK
	 * @author Ricardo Machado <ricardo-s-machado@telecom.pt>
	 * @return void
	 */
	public function custom()
	{
		$post = $this->input->post(NULL,TRUE);
		$errors = array();
		if( $post ) # Has been posted anything?
		{

			/**
			 * Checks if the parameters posted were available in the options listed.
			 */
			if( !isset($post['modules']) || (!is_array($post['modules']) || ( array_intersect($post['modules'],array_keys($this->config->item('ink_modules')))!=$post['modules']  ) ) )
			{
				$errors['modules'] = "Please select one of the available modules.";
			}

			if( isset($post['options']) && (!is_array( $post['options'] ) || ( array_intersect($post['options'],array_keys($this->config->item('ink_options')))!=$post['options']  ) ) )
			{
				$errors['options'] = "Please select one of the options.";
			}

			$options = array();
			foreach( $this->config->item('ink_config_vars') as $group => $vars)
				$options += $vars;

			if( isset($post['vars']) && ( !is_array( $post['vars']) || ( array_intersect(array_keys($post['vars']), array_keys($options) ) != array_keys($post['vars']) ) ) )
			{
				$errors['vars'] = "An error has occurred please check if you entered your data correctly.";
			}
			else
			{
				$errors['vars'] = array();
				$this->load->library('form_validation');
				foreach( $post['vars'] as $key => $value )
				{
					if( empty($value) && isset($options[$key]['required']) && ( $options[$key]['required'] === TRUE ) )
					{
						$errors['vars'][$key] = "This field is required.";
						$errors['vars'][$key] = "Please fill in the field " . ( !empty($options[$key]['label']) ? $options[$key]['label'] : $key) . ".";
						continue;
					}
					elseif( !empty($value) && isset($options[$key]['type']) )
					{
						switch( $options[$key]['type'] )
						{
							case 'color':
								if( !$this->check_color( $value ) )
								{
									$errors['vars'][$key] = "Please select a valid color in the " . ( !empty($options[$key]['label']) ? $options[$key]['label'] : $key) . " field.";
								}
								break;
							case 'measure':
								if( !$this->check_measure( $value ) )
								{
									$errors['vars'][$key] = "Please insert a valid value in the " . ( !empty($options[$key]['label']) ? $options[$key]['label'] : $key) . " field.";
								}
								break;
							case 'digit':
							case 'decimal':
							case 'alpha_dash':
								if( $this->form_validation->{$options[$key]['type']}( $value ) === FALSE )
								{
									$errors['vars'][$key] = "Please insert a valid value in the " . ( !empty($options[$key]['label']) ? $options[$key]['label'] : $key) . " field.";
								}
							default:
								/**
								 * Ignore not defined rules
								 */
						}
					}
				}
			}

			# Clean empty values			
			foreach($errors as $key => $value )
			{
				if( !$value )
				{
					unset($errors[$key]);
				}
			}

			if( $errors )
			{
				# Load the session to make a flashdata to pass the errors
				$this->load->library('session');
				$this->session->set_flashdata('errors',$errors);
				$this->session->set_flashdata('post',$post);

				# Load the helper to redirect
				$this->load->helper('url');
				redirect('/download','location');
				return;
			}
		}
		
		$configModules = $this->config->item('ink_modules');
		$modules = array_merge(array('normalize','conf','lib','common'),$post['modules']);
		foreach($this->input->post('modules') as $module){
			if( isset($configModules[$module]['implicit_files']) && count($configModules[$module]['implicit_files']) ) {
				$implicit = array();
				foreach( $configModules[$module]['implicit_files'] as $file ){
					$info = pathinfo($file);
					$implicit[] = str_replace(".".$info['extension'],"",$info['basename']);
				}
				$modules = array_merge( $modules, $implicit );
			}
		}

		// if( in_array('grid',$post['modules']) ) {
		// 	$modules = array_merge( $post['modules'], array( "large","medium","small" ) );
		// }


		/**
		 * Making a request to the NodeJS webserver (with the posts made)
		 * for generating the ZIP file with all things in it
		 */
		$current_build_path = $this->_prepare_build_space();
		if( $current_build_path && is_dir($current_build_path) && file_exists($current_build_path) )
		{
			if( !$this->_include_less( $current_build_path ) )
			{
				$errors['build'] = "Could not create the configuration with the specified options (ERRNUM 7)";
				$this->_errors( $errors, $post );
			}


			/**
			 * Copies the default (ergo boilerplate) html file to the build folder, saving it as index.html
			 */
			$copy_boilerplate  = "cp -R " . $this->paths->latest . "my-page.html " . $current_build_path . "ink/index.html";

			exec($copy_boilerplate,$result,$status_code);

			if($status_code != 0){
				$errors['build'] = "Could not create the configuration (ERRNUM 8)";
				$this->_errors( $errors, $post );
			}

			# Generates the configuration in the build path
			$this->_generate_ink_config( $current_build_path );

			/**
			 * Getting the normal css
			 */
			$qs = "";
			foreach( $modules as $value ){
				$qs .= "modules[]=".$value."&";
			}

			foreach( $this->input->post('vars') as $varName => $varValue ){
				if( $varValue ){
					$qs .= "vars[]=".rawurlencode("@".$varName." : " . $varValue)."&";
				}
			}
			$qs = substr($qs,0,strlen($qs)-1);

			$request = curl_init( $this->config->item('build_normal_css_url') );
			curl_setopt( $request, CURLOPT_TIMEOUT, 5);
			curl_setopt( $request, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt( $request, CURLOPT_FOLLOWLOCATION, TRUE);
			curl_setopt( $request, CURLOPT_POST,TRUE);

			#$qs = ("modules[]=" . implode("&modules[]=",$post['modules']));
			curl_setopt( $request, CURLOPT_POSTFIELDS, $qs );
			$normalCSS = curl_exec($request);
			$normalHttpStatus = curl_getinfo($request, CURLINFO_HTTP_CODE);
			curl_close($request);
			unset($request);


			$options = $this->input->post('options',TRUE);

			if( $options && in_array('minify_css',$options) )
			{
				/**
				 * Getting the minimized css
				 */
				$request = curl_init( $this->config->item('build_minimized_css_url') );
				curl_setopt( $request, CURLOPT_TIMEOUT, 5);
				curl_setopt( $request, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt( $request, CURLOPT_FOLLOWLOCATION, TRUE);
				curl_setopt( $request, CURLOPT_POST,TRUE);
				curl_setopt( $request, CURLOPT_POSTFIELDS, $qs."&compress=1" );
				$minimizedCSS = curl_exec($request);
				$minimizedHttpStatus = curl_getinfo($request, CURLINFO_HTTP_CODE);
				curl_close($request);
				unset($request);
			}

			if( ($normalHttpStatus == 200) && ( !$options || !in_array('minify_css',$options) || ($minimizedHttpStatus == 200))	)
			{
				/**
				 * Storing the CSS Files
				 */
				if( is_dir($current_build_path.'/ink/assets/css/') && file_exists($current_build_path.'/ink/assets/css/') && is_writable($current_build_path.'/ink/assets/css/'))
				{
					# Normal
					$cssFile = fopen($current_build_path.'/ink/assets/css/ink.css','w+');
					if( $cssFile )
					{
						fwrite($cssFile,$normalCSS);
						fclose($cssFile);
					}
					else
					{	
						$errors['build'] = "Could not create the stylesheet (ERRNUM 6)";
						$this->_errors( $errors, $post );
					}


					# Minimized
					if( $options && in_array('minify_css',$options) )
					{
						$cssFile = fopen($current_build_path.'/ink/assets/css/ink-min.css','w+');
						if( $cssFile )
						{
							fwrite($cssFile,$minimizedCSS);
							fclose($cssFile);
						}
						else
						{
							$errors['build'] = "Could not create the stylesheet (ERRNUM 5)";
							$this->_errors( $errors, $post );
						}
					}

					if( $options && in_array('include_less',$options) )
					{
						if( !$this->_include_less( $current_build_path ) )
						{
							$errors['build'] = "Could not create the configuration with the specified options (ERRNUM 7)";
							$this->_errors( $errors, $post );
						}
					}


					/**
					 * Reads the directory and adds it to the zip to send
					 * Removes the directory from the filesystem
					 * Sends it to the user
					 */
					chmod($current_build_path."ink/", 0777);
					$this->zip->read_dir($current_build_path."/ink/", FALSE);
					$this->_cleanup($current_build_path);
					$this->zip->download('ink-custom-'.$this->ink_version_number.'.zip');
				}
				else
				{
					$errors['build'] = "Could not create the configuration (ERRNUM 4)";
					$this->_errors( $errors, $post );
				}
			}
			else
			{
				$errors['build'] = "Could not process the configuration (ERRNUM 3)";
				$this->_errors( $errors, $post );
			}
		}
		elseif( !$current_build_path || !is_dir($current_build_path) || !file_exists($current_build_path) )
		{
			$errors['build'] = "Could not create the configuration (ERRNUM 2)";
			$this->_errors( $errors, $post );
		}	
		elseif( !is_writable($current_build_path) )
		{
			$errors['build'] = "Could not create the configuration (ERRNUM 1)";
			$this->_errors( $errors, $post );
		}
	}


	private function _include_less($build_site) 
	{
		$copy_less  = "cp -R " . $this->paths->latest . "less " . $build_site . "ink/";

		exec($copy_less,$result,$status_code);

		if($status_code == 0){
			return true;
		} else {
			return false;
		}
	}


	private function _include_common_files($build_site)
	{

		$this->zip->read_dir($this->paths->latest . "demo/",false);
		$this->zip->read_file($this->paths->latest . "my-page.html");
		
	}


	private function _prepare_build_space()
	{

		$build_dir_name = md5(time().rand());
		
		mkdir($this->paths->builds.$build_dir_name);
		mkdir($this->paths->builds.$build_dir_name.'/ink');
		mkdir($this->paths->builds.$build_dir_name.'/ink/assets');
		mkdir($this->paths->builds.$build_dir_name.'/ink/assets/css');
		mkdir($this->paths->builds.$build_dir_name.'/ink/assets/js');
		mkdir($this->paths->builds.$build_dir_name.'/ink/assets/images');
		mkdir($this->paths->builds.$build_dir_name.'/ink/assets/fonts');
		mkdir($this->paths->builds.$build_dir_name.'/ink/less');

		return $this->paths->builds.$build_dir_name.'/';

	}

	private function _generate_ink_config($build_site)
	{

		$configModules = $this->config->item('ink_modules');

		$new_ink_config = fopen($build_site.'/ink.less','w+');

		fwrite($new_ink_config, "@import \"".$this->paths->latest."less/normalize.less\";\n");
		fwrite($new_ink_config, "@import \"".$this->paths->latest."less/conf.less\";\n");
		fwrite($new_ink_config, "@import \"".$this->paths->latest."less/lib.less\";\n");
		fwrite($new_ink_config, "@import \"".$this->paths->latest."less/common.less\";\n");

		foreach($this->input->post('modules') as $moduleValue)
		{
			fwrite($new_ink_config, "@import \"".$this->paths->latest."less/".$moduleValue.".less\";\n");

			if( isset( $configModules[$moduleValue]) && isset($configModules[$moduleValue]['implicit_files']) && count($configModules[$moduleValue]['implicit_files']) ) {

				foreach($configModules[$moduleValue]['implicit_files'] as $file){
					fwrite($new_ink_config, "@import \"".$this->paths->latest.$file.";\n");
				}
			}
		}

		foreach($this->input->post('vars') as $var_name => $var_value)
		{
			fwrite($new_ink_config, str_replace('var-', '@', $var_name) . ': ' . $var_value . ";\n");
		}

		fclose($new_ink_config);

	}

	private function _generate_ink_makefile($build_site)
	{

		/**
		 * Compiled but not minified
		 */
		shell_exec($this->parths->builds.'recess ' . escapeshellarg($build_site . 'ink.less') . ' --compile > ' . escapeshellarg($build_site . 'ink/assets/css/ink.css') );
		shell_exec($this->parths->builds.'recess ' . escapeshellarg($this->paths->latest.'less/ie6.less') . ' --compile > ' . escapeshellarg($build_site . 'ink/assets/css/ink-ie6.css') );
		shell_exec($this->parths->builds.'recess ' . escapeshellarg($this->paths->latest.'less/ie7.less') . ' --compile > ' . escapeshellarg($build_site . 'ink/assets/css/ink-ie7.css') );


		$options = $this->input->post('options');
		if( isset($options) && is_array( $options ) && in_array('minify_css',$options ) )
		{
			/**
			 * Compiled and minified
			 */
			shell_exec($this->parths->builds.'recess ' . escapeshellarg($build_site . 'ink.less') . ' --compile --compress > ' . escapeshellarg($build_site . 'ink/assets/css/ink-min.css') );
			shell_exec($this->parths->builds.'recess ' . escapeshellarg($this->paths->latest.'less/ie6.less') . ' --compile --compress > ' . escapeshellarg($build_site . 'ink/assets/css/ink-ie6-min.css') );
			shell_exec($this->parths->builds.'recess ' . escapeshellarg($this->paths->latest.'less/ie7.less') . ' --compile --compress > ' . escapeshellarg($build_site . 'ink/assets/css/ink-ie7-min.css') );
		}


		/*
		$make_filename = 'Makefile';
		$new_ink_makefile = fopen($build_site.'/'.$make_filename,'w+');
		$build_path = $this->config->item('build_path');

		// LESS FILES
		fwrite($new_ink_makefile, "INK_LESS = " . $build_site . "ink.less\n");
		fwrite($new_ink_makefile, "INK_IE6_LESS = ".$this->paths->latest."less/ie6.less\n");
		fwrite($new_ink_makefile, "INK_IE7_LESS = ".$this->paths->latest."less/ie7.less\n\n");

		// COMPILED CSS FILES
		fwrite($new_ink_makefile, "INK = " . $build_site . "ink/assets/css/ink.css\n");
		fwrite($new_ink_makefile, "INK_IE6 = " . $build_site . "ink/assets/css/ink-ie6.css\n");
		fwrite($new_ink_makefile, "INK_IE7 = " . $build_site . "ink/assets/css/ink-ie7.css\n\n");

		// COMPILED AND MINIFIED CSS FILES
		fwrite($new_ink_makefile, "INK_MIN = " . $build_site . "ink/assets/css/ink-min.css\n");
		fwrite($new_ink_makefile, "INK_IE6_MIN = " . $build_site . "ink/assets/css/ink-ie6-min.css\n");
		fwrite($new_ink_makefile, "INK_IE7_MIN = " . $build_site . "ink/assets/css/ink-ie7-min.css\n\n");

		// SOME VISUAL CRAP TO ENTERTAIN THE BUILDERS
		fwrite($new_ink_makefile,"\nDATE=$(shell date +%I:%M%p)\n");
		fwrite($new_ink_makefile,"CHECK=\\033[32m✔\\033[39m\n");
		fwrite($new_ink_makefile,"HR=\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\n\n");

		// THE TARGETS
		fwrite($new_ink_makefile, "all:\n");
		fwrite($new_ink_makefile, "\t@recess \${INK_LESS} --compile > \${INK}\n");
		fwrite($new_ink_makefile, "\t@recess \${INK_LESS} --compile --compress > \${INK_MIN}\n");
		fwrite($new_ink_makefile, "\t@recess \${INK_IE6_LESS} --compile > \${INK_IE6}\n");
		fwrite($new_ink_makefile, "\t@recess \${INK_IE6_LESS} --compile --compress > \${INK_IE6_MIN}\n");
		fwrite($new_ink_makefile, "\t@recess \${INK_IE7_LESS} --compile > \${INK_IE7}\n");
		fwrite($new_ink_makefile, "\t@recess \${INK_IE7_LESS} --compile --compress > \${INK_IE7_MIN}\n");
		
		// CLOSE THE MAKEFILE
		fclose($new_ink_makefile);
		 */
	}

	private function _build_ink($build_site)
	{

		$make_command = "make -f ".$build_site."/Makefile";

		exec($make_command, $result, $status_code);

		if($status_code == 0) {
			return true;
		} else {
			return array($status_code,$result);
		}
	}

	private function _cleanup($build_site) 
	{
		$command = "rm -rf " . $build_site;
		exec($command,$return,$status_code);
		if($status_code == 0){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Validates if a string has a CSS color pattern (ex: #FFFFFF)
	 * It forces the string to have a # in it. It must be an hexadecimal.
	 * 
	 * @param  	string 	$color 	Value to check
	 * @return 	bool 			Returns true case it meets the css color pattern.
	 * @author  Ricardo Machado	ricardo-s-machado@telecom.pt
	 */
	private function check_color( $color )
	{
		return preg_match('/^#+[0-9a-f]{3}(?:[0-9a-f]{3})?$/iD', $color);
	}

	/**
	 * Validates if a string has a CSS measure pattern (ex: 10px)
	 * 
	 * @link 	http://demosthenes.info/blog/48/CSS-Measurement-Units
	 * @param  	string 	$value 	Value to check
	 * @return 	bool 			Returns true case it meets the css measure pattern.
	 * @author  Ricardo Machado	ricardo-s-machado@telecom.pt
	 */
	private function check_measure( $value )
	{
		return preg_match('/^\d+(cm|ch|em|ex|gd|in|pc|pt|px|rem|vh|vm|\%)?$/iD', $value);
	}

	private function _errors( $errors, $post )
	{
		if( $errors )
		{
			# Load the session to make a flashdata to pass the errors
			$this->load->library('session');
			$this->session->set_flashdata('errors',$errors);
			$this->session->set_flashdata('post',$post);

			# Load the helper to redirect
			$this->load->helper('url');
			redirect('/download');
			exit();
		}
	}


}