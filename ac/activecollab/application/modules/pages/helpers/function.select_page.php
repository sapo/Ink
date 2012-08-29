<?php

  /**
   * select_page helper
   *
   * @package activeCollab.modules.pages
   * @subpackage helpers
   */
  
  /**
   * Render select page control
   * 
   * Parameters:
   * 
   * - project - Parent project
   * - value - ID of selected page
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_page($params, &$smarty) {
    $project = array_var($params, 'project', null, true);
    if(!instance_of($project, 'Project')) {
      return new InvalidParamError('project', $project, '$project is expected to be an instance of Project class', true);
    } // if
    
    $user = array_var($params, 'user');
    if(!instance_of($user, 'User')) {
      return new InvalidParamError('user', $user, '$user is exepcted to be an instance of User class', true);
    } // if
    
    $options = array();
    
    $value = array_var($params, 'value', null, true);
    $skip = array_var($params, 'skip');
    
    $categories = Categories::findByModuleSection($project, PAGES_MODULE, 'pages');
    if(is_foreachable($categories)) {
      foreach($categories as $category) {
        $option_attributes = $category->getId() == $value ? array('selected' => true) : null;
        $options[] = option_tag($category->getName(), $category->getId(), $option_attributes);
        
        $pages = Pages::findByCategory($category, STATE_VISIBLE, $user->getVisibility());
        if(is_foreachable($pages)) {
          foreach($pages as $page) {
            smarty_function_select_page_populate_options($page, $value, $user, $skip, $options, '- ');
          } // foreach
        } // if
      } // foreach
    } // if
    
    return select_box($options, $params);
  } // smarty_function_select_page
  
  /**
   * Populate options array with options recursivly
   *
   * @param Page $page
   * @param integer $value
   * @param User $user
   * @param Page $skip
   * @param array $options
   * @param string $indent
   * @return null
   */
  function smarty_function_select_page_populate_options($page, $value, $user, $skip, &$options, $indent = '') {
    if(instance_of($skip, 'Page') && $skip->getId() == $page->getId()) {
      return;
    } // if
    
    $attributes = $value == $page->getId() ? array('selected' => true) : null;
    $options[] = option_tag($indent . $page->getName(), $page->getId(), $attributes);
    
    $subpages = $page->getSubpages($user->getVisibility());
    if(is_foreachable($subpages)) {
      foreach($subpages as $subpage) {
        smarty_function_select_page_populate_options($subpage, $value, $user, $skip, $options, $indent . '- ');
      } // foreach
    } // if
  } // smarty_function_select_page_populate

?>