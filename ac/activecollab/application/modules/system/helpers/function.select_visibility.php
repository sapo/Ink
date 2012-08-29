<?php

  /**
   * select_visibility helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select visibility control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_visibility($params, &$smarty) {
    static $counter = 1, $private_roles = false;
    
    $name = trim(array_var($params, 'name'));
    if($name == '') {
      return new InvalidParamError('name', $name, '$name value is required', true);
    } // if
    
    $id = trim(array_var($params, 'id'));
    if($id == '') {
      $id = "select_visiblity_$counter";
      $counter++;
    } // if
    
    if($private_roles === false) {
      $private_roles = who_can_see_private_objects(true, lang(' or '));
    } // if
    
    $value = array_var($params, 'value', VISIBILITY_NORMAL);
    
    $owner_company = $smarty->get_template_vars('owner_company');
    $project = array_var($params, 'project');
    
    $normal_caption = array_var($params, 'normal_caption', null, true);
    $private_caption = array_var($params, 'private_caption', null, true);
    $short_description = array_var($params, 'short_description', false);
    
    if(empty($normal_caption)) {
      if(instance_of($project, 'Project')) {
        $normal_title = lang('Anyone involved with :project project can see this', array('project' => $project->getName()));
      } else {
        $normal_title = lang('Anyone who can access this section');
      } // if
      
      $normal_caption = lang('Normal');
      if (!$short_description) {
        $normal_caption = $normal_caption . ' &mdash; <span class="details">' . $normal_title . '</span>';
      } // if
    } // if
    
    if(empty($private_caption)) {
      if(instance_of($project, 'Project')) {
        $private_title = lang('Only members with: :roles roles who are involved with :project project can see this', array('roles' => $private_roles, 'project' => $project->getName()));
      } else {
        $private_title = lang('Visible only to members with: :roles roles who can access this section', array('roles' => $private_roles));
      } // if
      
      $private_caption = lang('Private');
      if (!$short_description) {
        $private_caption = $private_caption . ' &mdash; <span class="details">' . $private_title . '</span>';
      } // if
    } // if
    
    $possibilities = array(
      VISIBILITY_NORMAL => $normal_caption,
      VISIBILITY_PRIVATE => $private_caption,
    );
    
    $result = "<div id=\"$id\">\n";
    foreach($possibilities as $visiblity => $text) {
      $radio = radio_field($name, $value == $visiblity, array('id' => $id . '_' . $visiblity, 'class' => 'inline', 'value' => $visiblity));
      $label = label_tag($text, $id . '_' . $visiblity, false, array('class' => 'inline'), '');
      
      if ($short_description) {
        if ($visiblity == VISIBILITY_NORMAL) {
          $result .= '<span class="block" title="' . $normal_title . '">' . $radio . ' ' . $label . "</span>\n";
        } else if ($visiblity == VISIBILITY_PRIVATE) {
          $result .= '<span class="block" title="' . $private_title . '">' . $radio . ' ' . $label . "</span>\n";
        } else {
          $result .= '<span class="block">' . $radio . ' ' . $label . "</span>\n"; 
        }
      } else {
        $result .= '<span class="block">' . $radio . ' ' . $label . "</span>\n";
      } // if
    } // if
    
    return $result . "</div>\n";
  } // smarty_function_select_visibility

?>