<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Collection extends REST_Controller
{


    public function index_post()
    {
        // GET AN EXISTING COLLECTION FROM STORAGE
    }

    public function create_post()
    {
        // CREATE A NEW COLLECTION, SAVE IT TO THE DATABASE, AND RETURN THE COLLECTION ID
    }

    public function update_put()
    {
        // SAVE A NEW DOCUMENT TO AN EXISTING COLLECTION
    }

    public function remove_delete()
    {
        // DELETE A COLLECTION AND ALL IT'S ENCLOSED DOCUMENTS
    }


}