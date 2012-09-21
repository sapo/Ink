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

	public function custom()
	{
		// create the build directory
		$current_build_path = $this->_prepare_build_space();

		// create a ink.less file including the chosen moduiles
		$this->_generate_ink_config($current_build_path);

		// create a makefile to setup compilation, minification and desencaralhation
		$this->_generate_ink_makefile($current_build_path);

		// Try to build and get the status code from the shell
		$build_status = $this->_build_ink($current_build_path);
		
		// react to errors
		if($build_status){
			// add the ink code to the zip archive object
			$this->zip->read_dir($current_build_path."/ink/", FALSE);
			// remove the build files
			// $this->_cleanup($build['build_site']);
			// send the archive to the browser
			$this->zip->download('ink-custom.zip');
		} else {
			// show narly error and make stupid excuses
		}		
		
	}


	private function _prepare_build_space(){

		$build_dir_name = md5(time().rand());
		
		mkdir($this->paths->builds.$build_dir_name);
		mkdir($this->paths->builds.$build_dir_name.'/ink');
		mkdir($this->paths->builds.$build_dir_name.'/ink/assets');
		mkdir($this->paths->builds.$build_dir_name.'/ink/assets/css');
		mkdir($this->paths->builds.$build_dir_name.'/ink/assets/js');
		mkdir($this->paths->builds.$build_dir_name.'/ink/assets/images');
		mkdir($this->paths->builds.$build_dir_name.'/ink/assets/fonts');
		mkdir($this->paths->builds.$build_dir_name.'/ink/less');

		return $this->paths->builds.$build_dir_name;

	}

	private function _generate_ink_config($build_site,$build_name) {

		$new_ink_config = fopen($this->paths->builds.'ink.less','w+');

		fwrite($new_ink_config, "@import \"".$this->paths->latest."less/normalize.less\";\n");
		fwrite($new_ink_config, "@import \"".$this->paths->latest."less/conf.less\";\n");
		fwrite($new_ink_config, "@import \"".$this->paths->latest."less/lib.less\";\n");
		fwrite($new_ink_config, "@import \"".$this->paths->latest."less/common.less\";\n");

		foreach($this->input->post() as $module_name => $module_value)
		{
			if($module_name != 'download' && !preg_match('/var-[\w-]+/i', $module_name)){
				fwrite($new_ink_config, "@import \"".$this->paths->latest."less/".$module_name.".less\";\n");
			} elseif(preg_match('/var-[\w-]+/i', $module_name) && $module_name != 'download' && !empty($module_value)){
				fwrite($new_ink_config, str_replace('var-', '@', $module_name) . ': ' . $module_value . ";\n");
			}
		}

		fclose($new_ink_config);

	}

	private function _generate_ink_makefile($build_site,$ink_modules) {

		$make_filename = 'Makefile';
		$new_ink_makefile = fopen($build_site.'/'.$make_filename,'w+');
		$build_path = $this->config->item('build_path');

		// LESS FILES
		fwrite($new_ink_makefile, "INK_LESS = " . $build_site . "/ink.less\n");
		fwrite($new_ink_makefile, "INK_LARGE_LESS = ".$this->paths->latest."less/large.less\n");
		fwrite($new_ink_makefile, "INK_MEDIUM_LESS = ".$this->paths->latest."less/medium.less\n");
		fwrite($new_ink_makefile, "INK_SMALL_LESS = ".$this->paths->latest."less/small.less\n");
		fwrite($new_ink_makefile, "INK_IE6_LESS = ".$this->paths->latest."less/ie6.less\n");
		fwrite($new_ink_makefile, "INK_IE7_LESS = ".$this->paths->latest."less/ie7.less\n\n");

		// COMPILED CSS FILES
		fwrite($new_ink_makefile, "INK = " . $build_site . "/ink/assets/css/ink.css\n");
		fwrite($new_ink_makefile, "INK_LARGE = " . $build_site . "/ink/assets/css/large.css\n");
		fwrite($new_ink_makefile, "INK_MEDIUM = " . $build_site . "/ink/assets/css/medium.css\n");
		fwrite($new_ink_makefile, "INK_SMALL = " . $build_site . "/ink/assets/css/small.css\n");
		fwrite($new_ink_makefile, "INK_IE6 = " . $build_site . "/ink/assets/css/ink-ie6.css\n");
		fwrite($new_ink_makefile, "INK_IE7 = " . $build_site . "/ink/assets/css/ink-ie7.css\n\n");

		// COMPILED AND MINIFIED CSS FILES
		fwrite($new_ink_makefile, "INK_MIN = " . $build_site . "/ink/assets/css/ink-min.css\n");
		fwrite($new_ink_makefile, "INK_LARGE_MIN = " . $build_site . "/ink/assets/css/large-min.css\n");
		fwrite($new_ink_makefile, "INK_MEDIUM_MIN = " . $build_site . "/ink/assets/css/medium-min.css\n");
		fwrite($new_ink_makefile, "INK_SMALL_MIN = " . $build_site . "/ink/assets/css/small-min.css\n");
		fwrite($new_ink_makefile, "INK_IE6_MIN = " . $build_site . "/ink/assets/css/ink-ie6-min.css\n");
		fwrite($new_ink_makefile, "INK_IE7_MIN = " . $build_site . "/ink/assets/css/ink-ie7-min.css\n\n");

		// SOME VISUAL CRAP TO ENTERTAIN THE BUILDERS
		fwrite($new_ink_makefile,"\nDATE=$(shell date +%I:%M%p)\n");
		fwrite($new_ink_makefile,"CHECK=\\033[32m✔\\033[39m\n");
		fwrite($new_ink_makefile,"HR=\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\n\n");

		// THE TARGETS
		fwrite($new_ink_makefile, "all:\n");
		fwrite($new_ink_makefile, "\t@recess \${INK_LESS} --compile > \${INK}\n");
		fwrite($new_ink_makefile, "\t@recess \${INK_LESS} --compile --compress > \${INK_MIN}\n");
		fwrite($new_ink_makefile, "\t@recess \${INK_LARGE_LESS} --compile > \${INK_LARGE}\n");
		fwrite($new_ink_makefile, "\t@recess \${INK_LARGE_LESS} --compile --compress > \${INK_LARGE_MIN}\n");
		fwrite($new_ink_makefile, "\t@recess \${INK_MEDIUM_LESS} --compile > \${INK_MEDIUM}\n");
		fwrite($new_ink_makefile, "\t@recess \${INK_MEDIUM_LESS} --compile --compress > \${INK_MEDIUM_MIN}\n");
		fwrite($new_ink_makefile, "\t@recess \${INK_SMALL_LESS} --compile > \${INK_SMALL}\n");
		fwrite($new_ink_makefile, "\t@recess \${INK_SMALL_LESS} --compile --compress > \${INK_SMALL_MIN}\n");
		fwrite($new_ink_makefile, "\t@recess \${INK_IE6_LESS} --compile > \${INK_IE6}\n");
		fwrite($new_ink_makefile, "\t@recess \${INK_IE6_LESS} --compile --compress > \${INK_IE6_MIN}\n");
		fwrite($new_ink_makefile, "\t@recess \${INK_IE7_LESS} --compile > \${INK_IE7}\n");
		fwrite($new_ink_makefile, "\t@recess \${INK_IE7_LESS} --compile --compress > \${INK_IE7_MIN}\n");
		
		// CLOSE THE MAKEFILE
		fclose($new_ink_makefile);
	}

	private function _build_ink($build_site){

		$make_command = "make -f ".$build_site."/Makefile";

		exec($make_command, $result, $status_code);

		if($status_code == 0) {
			return true;
		} else {
			return array($status_code,$result);
		}
	}

	private function _cleanup($build_site) {
		$command = "rm -rf " . $build_site;
		exec($command,$return,$status_code);
		if($status_code == 0){
			return true;
		} else {
			return false;
		}
	}


}