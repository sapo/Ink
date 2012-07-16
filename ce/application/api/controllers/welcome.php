<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Welcome extends REST_Controller
{

		

    public function index_get()
    {
        $this->response(array('element_1','element_2','element_3','element_4','element_5'),200);
    }

    public function index_post()
    {
        // Create a new book
    }
}