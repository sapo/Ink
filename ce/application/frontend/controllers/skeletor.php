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
		$document = $this->rest->get('document/new/format/serialize');
		$this->parse_document($document);
	}

	public function new_document(){
		
	}


	private function parse_document($obj,$key = null)
	{

			// if (is_array($obj))
			// {
			// 	foreach ($obj as $key => $item) {
			// 		echo "array\n";
			// 		echo $key;
			// 		echo $item;
			// 	}
			// }
			
			if(is_object($obj))
			{
				foreach ($obj as $var => $value)
				{
					$this->parse_document($value,$var);
				}
			} 
			else
			{
				var_dump($obj);
			}

		
	}

	// private function translate($element,$value){
	// 	$dictionary = array(
	// 		'doctype' => "<".$value."></".$value.">"
	// 	);
	// 	return $dictionary[$element];
	// }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */