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
		return $result;
	}

	public function custom()
	{

		
		$ink = $this->config->item('ink_path');
		$ink_version_number = $this->config->item('ink_version_number');

		$package_options = $this->input->post();
		$required_files = array('normalize','common','ie6','ie7');

		foreach ($required_files as $file){

			$filename = $ink.'css/'.$file.'.css';

			if(file_exists($filename)){				
				$module = fopen($filename, "r");
				$file_content = fread($module,filesize($filename));
				$this->zip->add_data('css/'.$file.'.css',$file_content);
				fclose($module);
			}
		}

		foreach($package_options as $option => $value) 
		{
			if(preg_match('/option/i', $option)) {

				if($option == 'option-include-less'){
					$less_folder = $ink.'less/';
					$this->zip->read_dir($less_folder,false);
				}
				if($option == 'option-minify'){
					$minify = true;
				}

			} else {
				// $inkmodules = array();
				// $inkmodules[] = $option;
			}
		}

		foreach($package_options as $option => $value) {

			if ( $option == "layout" ) {
				
				$files = array('large.css','medium.css','small.css');

				foreach ($files as $file) {

					$filename = $ink.'css/grids/'.$file;

						if(file_exists($filename)){								

							if($minify){
								//echo 'asjdh9032';
								$file_content = $this->minify_css($filename);
								$this->zip->add_data('css/grids/'.$file,$file_content[0]);
							} else {
								$module = fopen($filename, "r");
								$file_content = fread($module,filesize($filename));
								$this->zip->add_data('css/grids/'.$file,$file_content);
								fclose($module);
							}

							
							
						}

				}

				$grid = $ink.'css/grid.css';				

				if($minify){
					//echo 'asjdh9032';
					$file_content = $this->minify_css($grid);
					$this->zip->add_data('css/grid.css',$file_content[0]);
				} else {
					$module = fopen($filename, "r");
					$file_content = fread($module,filesize($filename));
					$this->zip->add_data('css/grid.css',$file_content);
					fclose($module);
				}

			} elseif ($value == 1 ) {

				$filename = $ink . 'css/' . $option . '.css';

				if(file_exists($filename)){	
					if($minify) {
						$file_content = $this->minify_css($filename);
						$this->zip->add_data('css/' . $option . '.css',$file_content[0]);
						//echo $option .'='.($file_content[0]);
					} else {
						$module = fopen($filename, "r");
						$file_content = fread($module,filesize($filename));
						$this->zip->add_data('css/' . $option . '.css',$file_content);
						fclose($module);
					}
				}

			}

		}

		$this->zip->download('ink-'.$ink_version_number.'-custom.zip');

	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */