<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Latest extends CI_Controller {


	// public function __construct()
 //    {
 //    	parent::__construct();
        
 //        $this->paths->latest = $this->config->item('latest_path');
 //        $this->ink_version_number = $this->config->item('ink_version_number');

 //     }

	public function index()
	{
		$this->zip->read_dir($this->config->item('latest_path'),false);
		$this->zip->download('ink-'.$this->config->item('ink_version_number').'.zip');
	}
	
}