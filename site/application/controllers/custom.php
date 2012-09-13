<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Custom extends CI_Controller {

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
		$data['pages'] = $this->config->item('site-pages');
		$data['title'] = $this->config->item('site-title');
		$data['options'] = $this->config->item('ink-options');

		$this->load->view('common/document_top',$data);
		$this->load->view('common/main_navigation',$data);
		$this->load->view('custom',$data);
		$this->load->view('common/document_footer');
		$this->load->view('common/document_bottom');
	}

	public function download()
	{



	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */