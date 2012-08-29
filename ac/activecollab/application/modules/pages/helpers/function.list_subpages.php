<?php

  /**
   * list_subpages helper
   *
   * @package activeCollab.modules.pages
   * @subpackage helpers
   */
  
  /**
   * List subpages
   * 
   * Parameters:
   * 
   * - parent - Page - Parent page
   * - subitems - array - Array of subpages. If subpages parameter is missing 
   *   subpages will be loaded from parent page
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */ 
  function smarty_function_list_subpages($params, &$smarty) {
    $parent = array_var($params, 'parent');
    if(!instance_of($parent, 'Page')) {
      return new InvalidParamError('parent', $parent, '$parent is expected to be an instance of Page class', true);
    } // if
    
    if(isset($params['subpages'])) {
      $subpages = $params['subpages'];
      unset($params['subpages']);
    } else {
      $subpages = $parent->getSubpages();
    } // if
    
    $smarty->assign(array(
      '_subpages_page' => $parent,
      '_subpages' => $subpages,
    ));
    
    return $smarty->fetch(get_template_path('_subpages', 'pages', PAGES_MODULE));
  } // smarty_function_list_subpages

?>