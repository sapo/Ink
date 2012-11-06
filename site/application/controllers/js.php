<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Js extends CI_Controller {

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
	public function ui()
	{
		$data['pages'] 		= $this->config->item('site_pages');
		$data['title'] 		= $this->config->item('site_title');
		$data['components']	= $this->config->item('ui_components');

		foreach( $data['components'] as $key => $value ){
			$data['components'][$key]['view'] = $this->load->view($value['view'],array(),TRUE);
		}

		$this->load->view('common/document_top',$data);
		$this->load->view('common/main_navigation',$data);
		$this->load->view('js/ui',$data);
		$this->load->view('common/document_footer');
		$this->load->view('common/document_bottom');
	}
	public function core()
	{
		$data['pages'] = $this->config->item('site_pages');
		$data['title'] = $this->config->item('site_title');

		$this->load->view('common/document_top',$data);
		$this->load->view('common/main_navigation',$data);
		$this->load->view('js/core',$data);
		$this->load->view('common/document_footer');
		$this->load->view('common/document_bottom');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */