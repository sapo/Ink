<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// include(APPPATH.'config/'.ENVIRONMENT.'/doctypes.php');

class Testing extends CI_Controller {

	var $device_list;

	public function __construct()
	{
		parent::__construct();
		$this->load->config('devices');
		$this->device_list = $this->config->item('devices');
	}

	public function index($device,$url)
	{
		$page = $this->get_page('http://'.$url);
		// echo $page;
		$data = $this->device_list[$device];
		$data['page'] = $page;
		$this->load->view('testdevices',$data);
	}

	public function new_document(){
		
	}


	private function parse_document($obj,$key = null)
	{

		
	}

	private function get_page($url,$useragent='')
	{
		$ch = curl_init();
	  $timeout = 15;
	 	// echo 'yep!';
	  curl_setopt($ch,CURLOPT_URL,$url);
	  curl_setopt($ch, CURLOPT_HEADER, 0);
	  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3');
	  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
	  curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
	  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	  curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
	  $data = curl_exec($ch);
	  var_dump(curl_getinfo($ch));
	  curl_close($ch);
	  return $data;
	}

	// private function translate($element,$value){
	// 	$dictionary = array(
	// 		'doctype' => "<".$value."></".$value.">"
	// 	);
	// 	return $dictionary[$element];
	// }

}

class testConfig {

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */