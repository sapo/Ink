<?php

  /**
   * string_list widget definition
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render string list widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_string_list($params, &$smarty) {
    static $counter = 1;
    
    $name = array_var($params, 'name');
    if($name == '') {
      return new InvalidParamError('name', $name, '$name value is required', true);
    } // if
    
  	$id = array_var($params, 'id');
    if(empty($id)) {
      $id = 'string_list_' . $counter;
      $counter++;
    } // if
    
    $value = array_var($params, 'value');
    
    $smarty->assign(array(
      '_string_list_name' => $name,
      '_string_list_id' => $id,
      '_string_list_value' => $value,
    ));
    
    return $smarty->fetch(get_template_path('_string_list', null, SYSTEM_MODULE));
  } // smarty_function_string_list

?>