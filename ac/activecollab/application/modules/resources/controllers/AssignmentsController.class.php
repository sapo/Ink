<?php

  /**
   * Assignments section controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class AssignmentsController extends ApplicationController {
    
    /**
     * Active assignments filter
     *
     * @var AssignmentFilter
     */
    var $active_filter = null;
  
    /**
     * Constructor
     *
     * @param void
     * @return AssignmentsController
     */
    function __construct($request) {
      parent::__construct($request);
      
      $filter_id = $this->request->getId('filter_id');
      if(!$filter_id) {
        $filter_id = UserConfigOptions::getValue('default_assignments_filter', $this->logged_user);
      } // if
      
      if($filter_id) {
        $this->active_filter = AssignmentFilters::findById($filter_id);
      } // if
      
      if(!instance_of($this->active_filter, 'AssignmentFilter')) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $this->smarty->assign('active_filter', $this->active_filter);
      
      if(AssignmentFilter::canAdd($this->logged_user)) {
        $this->wireframe->addPageAction(lang('New Filter'), assemble_url('assignments_filter_add'));
      } // if
      $this->wireframe->addBreadCrumb(lang('Assignments'), assemble_url('assignments'));
      $this->wireframe->current_menu_item = 'assignments';
    } // __construct
    
    /**
     * Show assignments index
     *
     * @param void
     * @return null
     */
    function index() {
      list($assignments, $pagination) = AssignmentFilters::executeFilter($this->logged_user, $this->active_filter, null, (integer) $this->request->get('page'));
      
      $this->wireframe->addRssFeed(
        $this->owner_company->getName() . ' - ' . $this->active_filter->getName(),
        $this->active_filter->getRssUrl($this->logged_user),
        FEED_RSS          
      );
      
      $this->smarty->assign(array(
        'assignments' => $assignments,
        'pagination' => $pagination,
        'grouped_filters' => AssignmentFilters::findGrouped($this->logged_user),
      ));
    } // index
  
  }

?>