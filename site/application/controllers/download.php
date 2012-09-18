<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Download extends CI_Controller {

	//var $minify;

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$data['pages'] = $this->config->item('site_pages');
		$data['title'] = $this->config->item('site_title');
		$data['modules'] = $this->config->item('ink_modules');
		$data['options'] = $this->config->item('ink_options');

		$this->load->view('common/document_top',$data);
		$this->load->view('common/main_navigation',$data);
		$this->load->view('download',$data);
		$this->load->view('common/document_footer');
		$this->load->view('common/document_bottom');
	}

	public function latest()
	{

		$ink = $this->config->item('ink_path');
		$ink_version_number = $this->config->item('ink_version_number');
		$this->zip->read_dir($ink,false);
		$this->zip->download('ink-'.$ink_version_number.'.zip');
	}

	public function minify_css($f) 
	{
		$command = 'yui-compressor '.$f;
		exec($command,$result,$stc);
		if($stc == 0){
			return $result;
		} else {
			throw new Exception('Minification failed');
		}
	}

	public function custom()
	{

		// PHYSICAL PATH TO INK FILES
		$ink_path = $this->config->item('ink_path');

		// CURRENT VERSION NUMBER
		$ink_version_number = $this->config->item('ink_version_number');

		// THE INK FILES 
		$ink_files = $this->config->item('ink_files');

		// REQUIRED FILES 
		$ink_required_files = $ink_files['required'];

		// MODULE FILES
		$ink_modules = $ink_files['modules'];

		// IE MODULE FILES
		$ink_ie = $ink_files['ie'];
		
		// GET THE USER SELECTIONS FROM THE POSTED FORM 
		$form_fields = $this->input->post();


		// SEPARATE OPTIONS LIKE MINIFY AND INCLUDE LESS FROM THE SELECTED MODULES
		// INTO THEIR OWN ARRAYS TO BE USED TO GENERATE THE APROPRIATE ZIP FILES.
		
		foreach ($form_fields as $field => $value) 
		{
			// IS THIS AN OPTION?
			if(preg_match('/option/i', $field)) 
			{
				$ink_selected_options[$field] = $value;
			}
			// IS THIS A MODULES?
			elseif( !preg_match('/download/i', $field) ) 
			{
				$ink_selected_modules[$field] =  $value;
			}

		}

		// PREPARE THE COMMON AND REQUIRED FILES

		// CHECK IF THE USER WANT'S THE LESS FILES AND INCLUDE THEM IN THE ZIP PACKAGE

		if (isset($ink_selected_options['option-include-less']) && $ink_selected_options['option-include-less'] == 1){

			$file_type = 'less';

			// var_dump($ink_modules[$file_type]);

			foreach ( $ink_required_files[$file_type] as $ink_required_file ) {
				$filename = $ink_path . $file_type . '/' . $ink_required_file . '.' . $file_type;
				$package_file = fopen($filename, 'r');
				$file_content = fread($package_file,filesize($filename));
				$this->zip->add_data($file_type . '/' . $ink_required_file . '.' . $file_type, $file_content);
			}

			foreach ( $ink_ie[$file_type] as $ink_ie_module ) {
				$filename = $ink_path . $file_type . '/' . $ink_ie_module . '.' . $file_type;
				$package_file = fopen($filename, 'r');
				$file_content = fread($package_file,filesize($filename));
				$this->zip->add_data($file_type . '/' . $ink_ie_module . '.' . $file_type, fread($package_file,filesize($filename)));
			}

			foreach ( $ink_selected_modules as $ink_selected_module => $value ) {

				if(is_array($ink_modules[$ink_selected_module][$file_type]))
				{

					$this->zip->add_dir('less/grids');

					foreach($ink_modules[$ink_selected_module][$file_type] as $index => $module)
					{
						$filename = $ink_path . $file_type . '/' . $ink_modules[$ink_selected_module][$file_type][$index] . '.' . $file_type;
						$package_file = fopen($filename, 'r');
						$file_content = fread($package_file,filesize($filename));
						$archive_file_name = $file_type . '/' . $ink_modules[$ink_selected_module][$file_type][$index] . '.' . $file_type;
						$this->zip->add_data($archive_file_name, $file_content);
					}
				} else {
					$filename = $ink_path . $file_type . '/' . $ink_modules[$ink_selected_module][$file_type] . '.' . $file_type;
					$package_file = fopen($filename, 'r');
					$file_content = fread($package_file,filesize($filename));
					$this->zip->add_data($file_type . '/' . $ink_modules[$ink_selected_module][$file_type] . '.' . $file_type, $file_content);
				}
			}		

		} 

		// INCLUDE THE REQUIRED CSS FILES IN THE ZIP PACKAGE

		$file_type = 'css';

		// CHECK IF THE USER WANT'S THE CSS TO BE MINIFIED

		if(isset($ink_selected_options['option-minify']) && $ink_selected_options['option-minify'] == 1)
		{
		
			$INK_MINIFIED = '';

			foreach ( $ink_required_files[$file_type] as $ink_required_file ) {
				$filename = $ink_path . $file_type . '/' . $ink_required_file . '.' . $file_type;
				$package_file = $this->minify_css($filename);
				$INK_MINIFIED .= ' '.$package_file[0];
			}

			foreach ( $ink_selected_modules as $ink_selected_module => $value ) {								

				if(is_array($ink_modules[$ink_selected_module][$file_type]))
				{
					foreach($ink_modules[$ink_selected_module][$file_type] as $index => $module)
					{
						$filename = $ink_path . $file_type . '/' . $ink_modules[$ink_selected_module][$file_type][$index] . '.' . $file_type;
						$package_file = $this->minify_css($filename);
						$INK_MINIFIED .= ' '.$package_file[0];
					}
				} else {
					$filename = $ink_path . $file_type . '/' . $ink_modules[$ink_selected_module][$file_type] . '.' . $file_type;
					$package_file = $this->minify_css($filename);
					$INK_MINIFIED .= ' '.$package_file[0];
				}

			}

			foreach ( $ink_ie[$file_type] as $ink_ie_module ) {
				$filename = $ink_path . $file_type . '/' . $ink_ie_module . '.' . $file_type;
				$package_file = fopen($filename, 'r');
				$this->zip->add_data($file_type . '/' . $ink_ie_module . '.' . $file_type);
			}

			$this->zip->add_data($file_type . '/' .'ink-min.' . $file_type, $INK_MINIFIED);
		} 

		else 
		{
			foreach ( $ink_required_files[$file_type] as $ink_required_file ) {
				$filename = $ink_path . $file_type . '/' . $ink_required_file . '.' . $file_type;
				$package_file = fopen($filename,'r');
				$file_content = fread($package_file,filesize($filename));
				$this->zip->add_data($file_type . '/' . $ink_required_file . '.' . $file_type, $file_content);
			}

			foreach ( $ink_selected_modules as $ink_selected_module => $value ) {								

				if(is_array($ink_modules[$ink_selected_module][$file_type]))
				{
					foreach($ink_modules[$ink_selected_module][$file_type] as $index => $module)
					{
						$filename = $ink_path . $file_type . '/' . $ink_modules[$ink_selected_module][$file_type][$index] . '.' . $file_type;
						$package_file = fopen($filename,'r');
						$file_content = fread($package_file,filesize($filename));
						$this->zip->add_data($file_type . '/' . $ink_modules[$ink_selected_module][$file_type][$index] . '.' . $file_type, $file_content);
					}
				} else {
					$filename = $ink_path . $file_type . '/' . $ink_modules[$ink_selected_module][$file_type] . '.' . $file_type;
					$package_file = fopen($filename, 'r');
					$file_content = fread($package_file,filesize($filename));
					$this->zip->add_data($file_type . '/' . $ink_modules[$ink_selected_module][$file_type] . '.' . $file_type, $file_content);
				}

			}

			foreach ( $ink_ie[$file_type] as $ink_ie_module ) {
				$filename = $ink_path . $file_type . '/' . $ink_ie_module . '.' . $file_type;
				$package_file = fopen($filename, 'r');
				$file_content = fread($package_file,filesize($filename));
				$this->zip->add_data($file_type . '/' . $ink_ie_module . '.' . $file_type, $file_content);
			}
		}
	
		$this->zip->download('ink-'.$ink_version_number.'-custom.zip');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */