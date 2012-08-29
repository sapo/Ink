<?php

  /**
   * Company link helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Company link helper
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_company_link($params, &$smarty) {
    static $cache = array();
    
    $company = array_var($params, 'company');
    if(!instance_of($company, 'Company')) {
      return new InvalidParamError('company', $company, '$company is required attribute and it needs to be instance of Company class', true);
    } // if
    
    if(!isset($cache[$company->getId()])) {
      $cache[$company->getId()] = '<a href="' . clean($company->getViewUrl()) . '" class="company_link">' . clean($company->getName()) . '</a>';
    } // if
    
    return $cache[$company->getId()];
  } // smarty_function_company_link

?>