<?php

  /**
   * Select page version helper
   *
   * @package activeCollab.modules.pages
   * @subpackage helpers
   */

  /**
   * Render select page version select box
   * 
   * Params:
   * 
   * - page - Page object
   * - version - Version value, if not set page will be selected
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_page_version($params, &$smarty) {
    $page = array_var($params, 'page', null, true);
    if(!instance_of($page, 'Page')) {
      return new InvalidParamError('page', $page, '$page is exptected to be an instance of Page class', true);
    } // if
    
    $version = array_var($params, 'version', null, true);
    
    $options = array(option_tag(lang('Latest'), 'latest', array(
      'selected' => $version == 'latest'
    )));
    
    $page_versions = $page->getVersions();
    if(is_foreachable($page_versions)) {
      foreach($page_versions as $page_version) {
        $options[] = option_tag(lang('Version #:version', array('version' => $page_version->getVersion())), $page_version->getVersion(), array(
          'selected' => $version != 'latest' && $page_version->getVersion() == $version,
        ));
      } // foreach
    } // if
    
    return select_box($options, $params);
  } // smarty_function_select_page_version

?>