<?php

  /**
   * attach_file helper
   *
   * @package activeCollab.modules.resources
   * @subpackage helpers
   */
  
  /**
   * Render attach file to an object control
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_attach_files($params, &$smarty) {
    static $ids = array();
    
    $id = array_var($params, 'id');
    if(empty($id)) {
      $counter = 1;
      do {
        $id = 'attach_files_' . $id++;
      } while(in_array($id, $ids));
    } // if
    $ids[] = $id;
    
    $max_files = (integer) array_var($params, 'max_files', 1, true);
    if($max_files < 1) {
      $max_files = 1;
    } // if
    
    require_once SMARTY_PATH . '/plugins/modifier.filesize.php';
    
    if($max_files == 1) {
      $max_upload_size_message = lang('Max size of file that you can upload is :size', array('size' => smarty_modifier_filesize(get_max_upload_size())));
    } else {
      $max_upload_size_message = lang('Max total size of files you can upload is :size', array('size' => smarty_modifier_filesize(get_max_upload_size())));
    } // if
    
    return '<div class="attach_files" id="' . $id . '" max_files="' . $max_files . '"><p class="attach_files_max_size details">' . $max_upload_size_message . '</p></div><script type="text/javascript">App.resources.AttachFiles.init("' . $id . '")</script>';
  } // smarty_function_attach_files
?>