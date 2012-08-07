<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Document extends REST_Controller
{    

    public function __construct(){
        parent::__construct();
           
    }

    private function new_document(){
        $defaultXMLdoc = "<?xml version='1.0' encoding='utf-8'?><doc></doc>";
        $newHTMLDoc = new SimpleXMLElement($defaultXMLdoc);
        $newHTMLDoc->addChild('doctype', "html5");
        $newHTMLDoc->addChild('html');
        $newHTMLDoc->html->addChild('head');
        $newHTMLDoc->html->head->addChild('metaTags');
        $newHTMLDoc->html->head->metaTags->addChild('charset','utf-8');
        $newHTMLDoc->html->head->metaTags->addChild('description','');
        $newHTMLDoc->html->head->metaTags->addChild('keywords','');
        $newHTMLDoc->html->head->addChild('linkTags');
        $newHTMLDoc->html->head->linkTags->addChild('ink');
        $newHTMLDoc->html->head->linkTags->ink->addAttribute('rel','stylesheet');
        $newHTMLDoc->html->head->linkTags->ink->addAttribute('href','something/something/css.css');
        $newHTMLDoc->html->addChild('body');
        return $newHTMLDoc;
    }    

    private function ceAddChild($parentXPath,$elementType,$attributes){

    }

    public function new_get()
    {        
        $HTML = $this->new_document();
        // var_dump($HTML->xpath('doctype'));
        $this->response(array('status'=>1,"document"=>$HTML));
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