<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

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
		$this->load->library('skeletor','skeletor');
		$this->skeletor->doctype = 'html5';
	}

	public function index()
	{
		
		$data = $this->skeletor;
		$this->skeletor->insert_child();
		var_dump($data);
		// $this->load->view('welcome_message',$data);
	}

	public function new_document(){
		
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */