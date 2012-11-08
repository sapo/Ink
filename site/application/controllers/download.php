<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Download extends CI_Controller {

	protected $browserColors = array(
		"ALICEBLUE","ANTIQUEWHITE","AQUA","AQUAMARINE","AZURE","BEIGE","BISQUE","BLACK","BLANCHEDALMOND","BLUE","BLUEVIOLET","BROWN","BURLYWOOD","CADETBLUE","CHARTREUSE","CHOCOLATE","CORAL","CORNFLOWERBLUE","CORNSILK","CRIMSON","CYAN","DARKBLUE","DARKCYAN","DARKGOLDENROD","DARKGRAY","DARKGREY","DARKGREEN","DARKKHAKI","DARKMAGENTA","DARKOLIVEGREEN","DARKORANGE","DARKORCHID","DARKRED","DARKSALMON","DARKSEAGREEN","DARKSLATEBLUE","DARKSLATEGRAY","DARKSLATEGREY","DARKTURQUOISE","DARKVIOLET","DEEPPINK","DEEPSKYBLUE","DIMGRAY","DIMGREY","DODGERBLUE","FIREBRICK","FLORALWHITE","FORESTGREEN","FUCHSIA","GAINSBORO","GHOSTWHITE","GOLD","GOLDENROD","GRAY","GREY","GREEN","GREENYELLOW","HONEYDEW","HOTPINK","INDIANRED","INDIGO","IVORY","KHAKI","LAVENDER","LAVENDERBLUSH","LAWNGREEN","LEMONCHIFFON","LIGHTBLUE","LIGHTCORAL","LIGHTCYAN","LIGHTGOLDENRODYELLOW","LIGHTGRAY","LIGHTGREY","LIGHTGREEN","LIGHTPINK","LIGHTSALMON","LIGHTSEAGREEN","LIGHTSKYBLUE","LIGHTSLATEGRAY","LIGHTSLATEGREY","LIGHTSTEELBLUE","LIGHTYELLOW","LIME","LIMEGREEN","LINEN","MAGENTA","MAROON","MEDIUMAQUAMARINE","MEDIUMBLUE","MEDIUMORCHID","MEDIUMPURPLE","MEDIUMSEAGREEN","MEDIUMSLATEBLUE","MEDIUMSPRINGGREEN","MEDIUMTURQUOISE","MEDIUMVIOLETRED","MIDNIGHTBLUE","MINTCREAM","MISTYROSE","MOCCASIN","NAVAJOWHITE","NAVY","OLDLACE","OLIVE","OLIVEDRAB","ORANGE","ORANGERED","ORCHID","PALEGOLDENROD","PALEGREEN","PALETURQUOISE","PALEVIOLETRED","PAPAYAWHIP","PEACHPUFF","PERU","PINK","PLUM","POWDERBLUE","PURPLE","RED","ROSYBROWN","ROYALBLUE","SADDLEBROWN","SALMON","SANDYBROWN","SEAGREEN","SEASHELL","SIENNA","SILVER","SKYBLUE","SLATEBLUE","SLATEGRAY","SLATEGREY","SNOW","SPRINGGREEN","STEELBLUE","TAN","TEAL","THISTLE","TOMATO","TURQUOISE","VIOLET","WHEAT","WHITE","WHITESMOKE","YELLOW","YELLOWGREEN"
	);

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

		$this->load->helper('url');
		redirect($this->config->item('latest_zip_url'),'location');
		//$this->zip->read_dir($this->paths->latest,false);
		//$this->zip->download('ink-'.$this->ink_version_number.'.zip');
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
		$modules = array_merge(array('normalize','conf','mixins','common'),$post['modules']);
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
			/**
			 * Copies the default (ergo boilerplate) html file to the build folder
			 */
			
			if( !$this->_recursive_copy($this->paths->latest,$current_build_path,'ink/') ){
				die();
				$errors['build'] = "Could not create the configuration (ERRNUM 8)";
				$this->_errors( $errors, $post );
			}else{
				// Remove unnecessary folders and files:
				$this->_recursive_delete( $current_build_path.'ink/less/' );
				$this->_recursive_delete( $current_build_path.'ink/demo/' );
				$this->_recursive_delete( $current_build_path.'ink/Makefile' );
				$this->_recursive_delete( $current_build_path.'ink/my-cdn-page.html' );
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
					$qs .= "vars[]=".rawurlencode("@".$varName.": " . $varValue)."&";
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
				if( is_dir($current_build_path.'/ink/css/') && file_exists($current_build_path.'/ink/css/') && is_writable($current_build_path.'/ink/css/'))
				{
					# Normal
					$cssFile = fopen($current_build_path.'/ink/css/ink.css','w+');
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
						$cssFile = fopen($current_build_path.'/ink/css/ink-min.css','w+');
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
						# Copying less....
						if( !$this->_recursive_copy($this->paths->latest.'less',$current_build_path . 'ink/','less') )
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
					chmod($current_build_path."ink/", 0755);
					$this->zip->read_dir($current_build_path."ink/", FALSE);
					$this->_recursive_delete($current_build_path);
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

	private function _prepare_build_space()
	{
		$build_dir_name = md5(time().rand());
		mkdir($this->paths->builds.$build_dir_name);
		return $this->paths->builds.$build_dir_name.'/';
	}

	private function _generate_ink_config($build_site)
	{
		$configModules = $this->config->item('ink_modules');

		$new_ink_config = fopen($build_site.'/ink/ink.less','w+');

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
		$color = str_replace(" ","", $color);
		if( preg_match('/^#+[0-9a-f]{3}(?:[0-9a-f]{3})?$/iD', $color) ){
			return true;
		}
		elseif( substr($color,0,3) === 'rgb' ){
			$parts = explode(",",$color);
			$parts[count($parts)-1] = substr($parts[count($parts)-1],0,strlen($parts[count($parts)-1])-1);
			$parts[0] = str_replace( (( substr($color,0,4) === 'rgba' ) ? "rgba" : "rgb" )."(","",$parts[0]);

			if(
				ctype_digit($parts[0]) && ( ($parts[0]>=0) && ($parts[0]<=255) ) &&
				ctype_digit($parts[1]) && ( ($parts[1]>=0) && ($parts[1]<=255) ) &&
				ctype_digit($parts[2]) && ( ($parts[2]>=0) && ($parts[2]<=255) )
			){
				if( ( substr($color,0,4) === 'rgba' ) && (count($parts) === 4) && is_numeric($parts[3]) && ( ($parts[3]>=0) && ($parts[3]<=1) ) ){
					return true;
				}
			}
		}
		elseif(ctype_alpha($color) && in_array(strtoupper($color), $this->browserColors)
		){
			return true;
		}

		return false;
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
		return preg_match('/^\d+(\.\d+)?(cm|ch|em|ex|gd|in|pc|pt|px|rem|vh|vm|\%)?$/iD', $value);
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

	private function _recursive_copy($source, $dest, $diffDir = ''){ 
		$returnValue = TRUE;
	    $sourceHandle = opendir($source); 
	    // if(!$diffDir) 
	    //         $diffDir = $source; 
	    
	    mkdir($dest . '/' . $diffDir);
	    
	    while($res = readdir($sourceHandle)){ 
	        if($res == '.' || $res == '..') 
	            continue; 

	        if(is_dir($source . '/' . $res)){ 
	            $returnValue = $returnValue && $this->_recursive_copy($source . '/' . $res, $dest, $diffDir . '/' . $res); 
	        } else { 
	            $returnValue = $returnValue && @copy($source . '/' . $res, $dest . '/' . $diffDir . '/' . $res); 
	            
	        } 
	    }

	    return $returnValue;
	}

    /**
     * Delete a file or recursively delete a directory
     *
     * @param string $str Path to file or directory
     */
    private function _recursive_delete($str){
        if(is_file($str)){
            return @unlink($str);
        }
        elseif(is_dir($str)){
            $scan = glob(rtrim($str,'/').'/*');
            foreach($scan as $index=>$path){
                $this->_recursive_delete($path);
            }
            return @rmdir($str);
        }
    }


}