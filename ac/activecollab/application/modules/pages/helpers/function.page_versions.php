<?php

  /**
   * page_revisions helper
   *
   * @package activeCollab.modules.pages
   * @subpackage helpers
   */
  
  /**
   * List page revisions
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_page_versions($params, &$smarty) {
    $page = array_var($params, 'page');
    if(!instance_of($page, 'Page')) {
      return new InvalidParamError('page', $page, '$page is expected to be an instance of Page class', true);
    } // if
    
    if(isset($params['versions'])) {
      $versions = $params['versions'];
      unset($params['versions']);
    } else {
      $versions = $page->getVersions();
    } // if
    
    $smarty->assign(array(
      '_versions_page' => $page,
      '_versions' => $versions,
    ));
    
    return $smarty->fetch(get_template_path('_versions', 'pages', PAGES_MODULE));
  } // smarty_function_page_versions

?>