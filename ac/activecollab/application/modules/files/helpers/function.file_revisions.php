<?php

  /**
   * file_revisions helper
   *
   * @package activeCollab.modules.files
   * @subpackage helpers
   */
  
  /**
   * Render file revisions resource block
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_file_revisions($params, &$smarty) {    
    $file = array_var($params, 'file');
    if(!instance_of($file, 'File')) {
      return new InvalidParamError('file', $file, '$file is expected to be an instance of File class', true);
    } // if
    
    $revisions = $file->getRevisions();
    if(is_foreachable($revisions)) {
      foreach($revisions as $revision) {
        ProjectObjectViews::log($revision, $smarty->get_template_vars('logged_user'));
      } // foreach
    } // if
    
    $smarty->assign(array(
      '_file' => $file,
      '_file_revisions' => $revisions,
      '_file_revisions_count' => is_array($revisions) ? count($revisions) : 1,
    ));
    
    return $smarty->fetch(get_template_path('_file_revisions', 'files', FILES_MODULE));
  } // smarty_function_file_revisions

?>