<?php

  /**
   * Mobile Access Starred controller
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage controllers
   */
  class MobileAccessStarredController extends MobileAccessController {
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'mobile_access_starred';
    
    /**
     * Constructor
     *
     * @param Request $request
     * @return MobileAccessController extends ApplicationController 
     */
    function __construct($request) {
      parent::__construct($request);
    } // __construct
    
    /**
     * Display starred items
     *
     */
    function index() {
    } // index
    
  } // MobileAccessStarredController
?>