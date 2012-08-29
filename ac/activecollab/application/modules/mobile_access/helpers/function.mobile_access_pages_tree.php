<?php

  /**
   * Render mobile access pages tree
   * 
   * Params:
   * 
   * - pages
   * - user
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_mobile_access_pages_tree($params, &$smarty) {
    $user = array_var($params, 'user');
    if(!instance_of($user, 'User')) {
      return new InvalidParamError('user', $user, '$user is expected to be an instance of User class', true);
    } // if
    
    $smarty->assign('_show_visibility', array_var($params, 'show_visibility', true));
       
    $pages = array_var($params, 'pages');
    if(is_foreachable($pages)) {
      $result = '';
      foreach($pages as $page) {
        $result .= _mobile_access_pages_tree_render_cell($page, $user, $smarty);
      } // foreach
      return $result;
    } // if
  } // smarty_function_mobile_access_pages_tree
  
  /**
   * Render single page tree cell
   *
   * @param Page $page
   * @param User $user
   * @param Smarty $smarty
   * @param string $indent
   * @return string
   */
  function _mobile_access_pages_tree_render_cell($page, $user, &$smarty, $indent = '') {
    $smarty->assign(array(
      '_page' => $page,
      '_indent' => $indent,
    ));
    
    $result = $smarty->fetch(get_template_path('_page_tree_row', 'mobile_access_project_pages', MOBILE_ACCESS_MODULE));
    
    $subpages = $page->getSubpages($user->getVisibility());
    if(is_foreachable($subpages)) {
      foreach($subpages as $subpage) {
        $result .= _mobile_access_pages_tree_render_cell($subpage, $user, $smarty, $indent . '&middot;&middot;');
      } // foreach
    } // if
    return $result;
  } // _pages_tree_render_cell

?>