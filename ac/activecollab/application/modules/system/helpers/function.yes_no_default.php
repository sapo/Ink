<?php

  /**
   * yes_no_default helper implementation
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render Yes / No / Default value helper
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_yes_no_default($params, &$smarty) {
    $value = array_var($params, 'value', null, true);
    $default_value = array_var($params, 'default', false, true) ? lang('Yes') : lang('No');
    
    return select_box(array(
      option_tag(lang('-- System Default (:default) --', array('default' => $default_value)), '', array(
        'selected' => $value === null,
      )),
      option_tag('', ''), 
      option_tag(lang('Yes'), 1, array(
        'selected' => $value === true,
      )),
      option_tag(lang('No'), 0, array(
        'selected' => $value === false,
      )),
    ), $params);
  } // smarty_function_yes_no_default

?>