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

	private function prepare_build_space(){

		$build_path = $this->config->item('build_path');
		$build_dir_name = md5(time().rand());
		mkdir($build_path.$build_dir_name);
		mkdir($build_path.$build_dir_name.'/ink');
		mkdir($build_path.$build_dir_name.'/ink/assets');
		mkdir($build_path.$build_dir_name.'/ink/assets/css');
		mkdir($build_path.$build_dir_name.'/ink/assets/js');
		mkdir($build_path.$build_dir_name.'/ink/assets/images');
		mkdir($build_path.$build_dir_name.'/ink/assets/fonts');
		mkdir($build_path.$build_dir_name.'/ink/less');
		return array('build_site'=>$build_path.$build_dir_name,'build_name'=>$build_dir_name);

	}

	private function generate_ink_config($build_site,$build_name) {

		$new_ink_config = fopen($build_site.'/'.$build_name.'.less','w+');

		fwrite($new_ink_config, "@import \"../latest/less/normalize.less\";\n");
		fwrite($new_ink_config, "@import \"../latest/less/conf.less\";\n");
		fwrite($new_ink_config, "@import \"../latest/less/lib.less\";\n");
		fwrite($new_ink_config, "@import \"../latest/less/common.less\";\n");

		foreach($this->input->post() as $module_name => $module_value)
		{
			if($module_name != 'download'){
				fwrite($new_ink_config, "@import \"../latest/less/".$module_name.".less\";\n");
			}
		}
	}

// INK = ./css/ink.css
// INK_MIN = ./css/ink-min.css
// INK_LESS = ./less/ink.less
// INK_LARGE = ./css/large.css
// INK_LARGE_MIN = ./css/large-min.css
// INK_LARGE_LESS = ./less/large.less
// INK_MEDIUM = ./css/medium.css
// INK_MEDIUM_MIN = ./css/medium-min.css
// INK_MEDIUM_LESS = ./less/medium.less
// INK_SMALL = ./css/small.css
// INK_SMALL_MIN = ./css/small-min.css
// INK_SMALL_LESS = ./less/small.less

// DATE=$(shell date +%I:%M%p)
// CHECK=\033[32m✔\033[39m
// HR=\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#

// ink:
// 	@recess ${INK_LESS} --compile > ${INK}
// 	@recess ${INK_LARGE_LESS} --compile > ${INK_LARGE}
// 	@recess ${INK_MEDIUM_LESS} --compile > ${INK_MEDIUM}
// 	@recess ${INK_SMALL_LESS} --compile > ${INK_SMALL}	
// 	@echo "${HR}"
// 	@echo "Compiling InK...            ${CHECK} Done"
// 	@echo "${HR}"
// 	@rm -f ./Makefile2


// minified:
// 	@recess ${INK_LESS} --compile --compress > ${INK_MIN}
// 	@recess ${INK_LARGE_LESS} --compile --compress > ${INK_LARGE_MIN}
// 	@recess ${INK_MEDIUM_LESS} --compile --compress > ${INK_MEDIUM_MIN}
// 	@recess ${INK_SMALL_LESS} --compile --compress > ${INK_SMALL_MIN}	
// 	@echo "${HR}"
// 	@echo "Compiling minified version of InK...            ${CHECK} Done"
// 	@echo "${HR}"

// clean: 
// 	@rm -f 

	private function generate_ink_makefile($build_site,$ink_modules) {

		$make_filename = 'Makefile';
		$new_ink_makefile = fopen($build_site.'/'.$make_filename,'w+');
		$build_path = $this->config->item('build_path');

		// LESS FILES
		fwrite($new_ink_makefile, "INK_LESS = " . $build_site . "/".$ink_modules.".less\n");
		fwrite($new_ink_makefile, "INK_LARGE_LESS = ".$build_path."latest/less/large.less\n");
		fwrite($new_ink_makefile, "INK_MEDIUM_LESS = ".$build_path."latest/less/medium.less\n");
		fwrite($new_ink_makefile, "INK_SMALL_LESS = ".$build_path."latest/less/small.less\n");
		fwrite($new_ink_makefile, "INK_IE6_LESS = ".$build_path."latest/less/ie6.less\n");
		fwrite($new_ink_makefile, "INK_IE7_LESS = ".$build_path."latest/less/ie7.less\n\n");

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
		

		// fwrite($new_ink_makefile, "\t@echo \${HR}\n");
		// fwrite($new_ink_makefile, "build-minified:\n");
		// fwrite($new_ink_makefile, "clean:\n");		

		// CLOSE THE MAKEFILE
		fclose($new_ink_makefile);
	}

	private function build_custom_ink($build_site){

		$make_command = "make -f ".$build_site."/Makefile";

		exec($make_command, $result, $status_code);

		if($status_code == 0) {
			return true;
		} else {
			return false;
		}
	}

	public function latest()
	{
		$ink = $this->config->item('ink_path');
		$ink_version_number = $this->config->item('ink_version_number');
		$this->zip->read_dir($ink,false);
		$this->zip->download('ink-'.$ink_version_number.'.zip');
	}

	private function cleanup($build_site) {
		$command = "rm -rf " . $build_site;
		exec($command,$return,$status_code);
		if($status_code == 0){
			return true;
		} else {
			return false;
		}
	}

	public function custom()
	{
		$build = $this->prepare_build_space();
		// var_dump($build);
		$this->generate_ink_config($build['build_site'],$build['build_name']);
		$this->generate_ink_makefile($build['build_site'],$build['build_name']);

		$build_status = $this->build_custom_ink($build['build_site']);
		


		$this->zip->read_dir($build['build_site']."/ink/",false);

		// var_dump($this->cleanup($build['build_site']));

		$this->zip->download('ink-custom.zip');

		
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */