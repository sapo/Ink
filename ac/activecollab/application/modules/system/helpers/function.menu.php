<?php

  /**
   * menu helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render main menu
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_menu($params, &$smarty) {
    require SYSTEM_MODULE_PATH . '/models/menu/Menu.class.php';
    require SYSTEM_MODULE_PATH . '/models/menu/MenuGroup.class.php';
    require SYSTEM_MODULE_PATH . '/models/menu/MenuItem.class.php';
    
    $logged_user = $smarty->get_template_vars('logged_user');
    
    $menu = new Menu();
    event_trigger('on_build_menu', array(&$menu, &$logged_user));
    $smarty->assign('_menu', $menu);
    
    return $smarty->fetch(get_template_path('_menu', null, SYSTEM_MODULE));
  } // smarty_function_menu

?>