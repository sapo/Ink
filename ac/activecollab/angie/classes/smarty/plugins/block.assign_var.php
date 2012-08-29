<?php

  /**
   * Assign generated value to template
   * 
   * Params:
   * 
   * - name - Variable name
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return null
   */
  function smarty_block_assign_var($params, $content, &$smarty, &$repeat) {
    $name = trim(array_var($params, 'name'));
    if($name == '') {
      return new InvalidParamError('name', $name, 'name value is missing', true);
    } // if
    
    $smarty->assign($name, $content);
  } // smarty_block_assign

?>