<?php

  // We need projects countroller
  use_controller('project', SYSTEM_MODULE);

  /**
  * Tags controller
  *
  * All things related with tags
  * 
  * @author Ilija Studen <ilija.studen@gmail.com>
  */
  class TagsController extends ProjectController {
  
    /**
    * Construct tags controller
    *
    * @param Request $request
    * @return TagsController
    */
    function __construct($request) {
      parent::__construct($request);
      
      $tags_url = Tags::getTagsUrl($this->active_project);
      $this->wireframe->addBreadCrumb(lang('Tags'), $tags_url);
      
      $this->wireframe->addPageAction(lang('Browse'), $tags_url);
      
      $this->smarty->assign(array(
        'tags_url' => $tags_url,
      ));
    } // __construct
    
    /**
    * List all tags
    *
    * @param void
    * @return null
    */
    function index() {
      $tags = Tags::buildIndex($this->active_project);
      
      if($this->request->getFormat() == FORMAT_HTML) {
        $this->smarty->assign('tags', $tags);
      } else {
      	$this->serveData($tags);
      } // if
    } // index
    
    /**
    * Show single tag
    *
    * @param void
    * @return null
    */
    function view() {
      $tag = urldecode($this->request->get('tag'));
      $tags = Tags::buildIndex($this->active_project);
      
      $per_page = 30;
      $page = (integer) $this->request->get('page');
      if($page < 1) {
        $page = 1;
      } // if
      
      list($objects, $pagination) = ProjectObjects::paginateByIds($tags[$tag]['objects'], $page, $per_page, STATE_VISIBLE, $this->logged_user->getVisibility());
      $this->smarty->assign(array(
        'tag' => $tag,
        'objects' => $objects,
        'pagination' => $pagination,
        'tag_url_pattern' => Tags::getTagUrl($tag, $this->active_project, '-PAGE-'),
      ));
    } // view
  
  } // TagsController

?>