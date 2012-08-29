<?php

  /**
   * Render pages tree
   * 
   * Params:
   * 
   * - pages
   * - visibility
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_project_exporter_pages_tree($params, &$smarty) {
    $visibility = array_var($params, 'visibility',null);
    if ($visibility === null) {
      return new InvalidParamError('visibility is required parameter', true);
    } // if
    
    $smarty->assign('_show_visibility', array_var($params, 'show_visibility', true));
    
    $pages = array_var($params, 'objects');
    if(is_foreachable($pages)) {
      $result = "<table class=\"common_table\">\n";
      foreach($pages as $page) {
        $result .= _project_exporter_pages_tree_render_cell($page, $visibility, $smarty);
      } // foreach
      return "$result\n</table>";
    } // if
  } // smarty_function_pages_tree
  
  /**
   * Render single page tree cell
   *
   * @param Page $page
   * @param integer $visibility
   * @param Smarty $smarty
   * @param string $indent
   * @return string
   */
  function _project_exporter_pages_tree_render_cell($page, $visibility, &$smarty, $indent = '') {
    $smarty->assign(array(
      '_page' => $page,
      '_indent' => $indent,
    ));
    
    $result = $smarty->fetch(get_template_path('export/_page_tree_row', 'pages', PAGES_MODULE));
    
    $subpages = $page->getSubpages($visibility);
    if(is_foreachable($subpages)) {
      foreach($subpages as $subpage) {
        $result .= _project_exporter_pages_tree_render_cell($subpage, $visibility, $smarty, $indent . '&middot;&middot;');
      } // foreach
    } // if
    return $result;
  } // _pages_tree_render_cell

?>