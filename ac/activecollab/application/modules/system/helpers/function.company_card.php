<?php

  /**
   * company_card helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render company card
   * 
   * Parameters:
   * 
   * - company - company instance
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_company_card($params, &$smarty) {
    $company = array_var($params, 'company');
    if(!instance_of($company, 'Company')) {
      return new InvalidParamError('company', $company, '$company is expected to be an valid Company instance', true);
    } // if
    
    $smarty->assign(array(
      '_card_company' => $company,
      '_card_options' => $company->getOptions(get_logged_user())
    ));
    return $smarty->fetch(get_template_path('_card', 'companies', SYSTEM_MODULE));
  } // smarty_function_company_card

?>