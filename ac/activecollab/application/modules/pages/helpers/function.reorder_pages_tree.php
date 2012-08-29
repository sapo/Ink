<?php

  /**
   * Render reorder pages tree
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
  function smarty_function_reorder_pages_tree($params, &$smarty) {
    $user = array_var($params, 'user');
    if(!instance_of($user, 'User')) {
      return new InvalidParamError('user', $user, '$user is expected to be an instance of User class', true);
    } // if
    
    $smarty->assign('_show_visibility', array_var($params, 'show_visibility', true));
    
    $pages = array_var($params, 'pages');
    if(is_foreachable($pages)) {
      $result = "<ul class=\"reorder_pages_tree\">\n";
      foreach($pages as $page) {
        $result .= _pages_reorder_tree_render_cell($page, $user, $smarty);
      } // foreach
      return "$result\n</ul>";
    } // if
  } // smarty_function_pages_tree
  
  /**
   * Render single page tree cell
   *
   * @param Page $page
   * @param User $user
   * @param Smarty $smarty
   * @param string $indent
   * @return string
   */
  function _pages_reorder_tree_render_cell($page, $user, &$smarty, $indent = '') {
    $smarty->assign('_page',$page);

    
    $subpages = $page->getSubpages($user->getVisibility());
    if(is_foreachable($subpages)) {
      $result.= '<li class="folder " id="page_'.$page->getId().'">' . $smarty->fetch(get_template_path('_page_reorder_tree_row', 'pages', PAGES_MODULE));
      $result.= '<ul>';
      foreach($subpages as $subpage) {
        $result .= _pages_reorder_tree_render_cell($subpage, $user, $smarty, $indent . '&middot;&middot;');
      } // foreach
      $result.= '</ul>';
    } else {
      $result.= '<li class="file " id="page_'.$page->getId().'">' . $smarty->fetch(get_template_path('_page_reorder_tree_row', 'pages', PAGES_MODULE));
    } // if
    $result.= '</li>';
    return $result;
  } // _pages_tree_render_cell

?>