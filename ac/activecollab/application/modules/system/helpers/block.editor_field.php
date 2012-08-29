<?php

  /**
   * editor_field helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render HTML editor
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_editor_field($params, $content, &$smarty, &$repeat) {
    static $ids = array(), $files_included = false;
    
    if(isset($params['visual'])) {
      $visual = array_var($params, 'visual', true, true);
    } else {
      $logged_user = $smarty->get_template_vars('logged_user');
      if(instance_of($logged_user, 'User')) {
        $visual = UserConfigOptions::getValue('visual_editor', $logged_user); // if user is loaded $visual is set dependable of user config option
      } else {
        $visual = true;
      } // if
    } // if
    
    $id = array_var($params, 'id');
    if(empty($id)) {
      $counter = 1;
      do {
        $id = "visual_editor_$counter";
        $counter++;
      } while(in_array($id, $ids));
    } // if
    $ids[] = $id;
    
    $params['id'] = $id;
    $params['mce_editable'] = true;
    if (!isset($params['auto_expand'])) {
      $params['auto_expand'] = 'yes';
    } // if
   
    if($visual && !$files_included) {
      $page =& PageConstruction::instance();
      $disable_image_upload = array_var($params, 'disable_image_upload', false);
      
      $page->addScript(get_asset_url('javascript/tinymce/tiny_mce.js'), false);
      $page->addScript(get_asset_url('javascript/tinymce/tiny_mce_init.js'), false);
      
      echo "<script type='text/javascript'>";
      if ($disable_image_upload) {
        echo "App.widgets.EditorImagePicker.disable_image_upload = true;";
      } // if
      echo "</script>";
      
      $files_included = true;
    } // if
    
    if(isset($params['class'])) {
      $classes = explode(' ', $params['class']);
      $classes[] = 'editor';
      
      if(in_array('tiny_value_present', $classes) && !$visual) {
        $classes[] = 'required';
      } // if
      
      $params['class'] = implode(' ', $classes);
    } else {
      $params['class'] = 'editor';
    } // if
    
    $return_string = '';
    
    if (!$variable_name = array_var($params, 'variable_name')) {
      $name_parameter = array_var($params, 'name');
      $variable_name = substr($name_parameter, 0,strrpos($name_parameter, '['));
    } // if
    $variable_name.='[inline_attachments][]';
    
    $inline_attachments = array_var($params,'inline_attachments');
    if (is_foreachable($inline_attachments)) {
      foreach ($inline_attachments as $inline_attachment) {
      	$return_string.='<input type=hidden name="'.$variable_name.'" value="'.$inline_attachment.'" />'."\n";
      } // foreach
    } // if
    
    unset($params['inline_attachments']);
    unset($params['variable_name']);
    $params['inline_attachments_name'] = $variable_name;
       
    $return_string.=open_html_tag('textarea', $params) . clean($content) . '</textarea>';
    return $return_string;
  } // smarty_block_editor_field

?>