<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Document extends REST_Controller
{

    public function new_get()
    {
        // $this->output->enable_profiler();
        if($document = $this->ce_html_document->new_document()){
            $this->response($document,200);
            // var_dump($document);
        }
    }

    public function insert_child_put()
    {
        // Create a new book
    }

    public function append_child_put()
    {
        // Create a new book
    }

    public function remove_element_delete()
    {
        // Create a new book
    }

}