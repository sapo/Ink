<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Skeletor extends CI_Controller {

	/*l*
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

	public function __construct()
	{
		parent::__construct();
		// Initialize the rest client
		$this->rest->initialize(array('server' => $this->config->item('api_url')));
	}

	public function index()
	{
		$tweets = $this->rest->get('');
		var_dump($tweets);
		// echo "beep";
	}

	public function new_document(){
		
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */