<?php

  /**
   * config_option helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Return value of a given config option
   * 
   * Output of this helper is not cleaned!
   *
   * @param array $params
   * @param Smarty $smarty
   * @return mixed
   */
  function smarty_function_config_option($params, &$smarty) {
    $name = array_var($params, 'name');
    if(empty($name)) {
      return new InvalidParamError('name', $name, '$name value is required');
    } // if
    
    if(isset($params['user']) && instance_of($params['user'], 'User')) {
      return UserConfigOptions::getValue($name, $params['user']);
//    } elseif(isset($params['project']) && instance_of($params['project'], 'Project')) {
//      return ProjectConfigOptions::getValue($name, $params['project']);
    } elseif(isset($params['company']) && instance_of($params['company'], 'Company')) {
      return CompanyConfigOptions::getValue($name, $params['company']);
    } else {
      return ConfigOptions::getValue($name);
    } // if
  } // smarty_function_config_option

?>