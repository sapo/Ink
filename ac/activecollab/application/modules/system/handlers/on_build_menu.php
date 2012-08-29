<?php

  /**
   * System module on_build_menu event handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */
  
  /**
   * Build menu
   *
   * @param Menu $menu
   * @param User $user
   * @return array
   */
  function system_handle_on_build_menu(&$menu, &$user) {
    
    // ---------------------------------------------------
    //  Tools
    // ---------------------------------------------------
    
    $menu->addToGroup(array(
      new MenuItem('people', lang('People'), assemble_url('people'), get_image_url('navigation/people.gif')),
      new MenuItem('projects', lang('Projects'), assemble_url('projects'), get_image_url('navigation/projects.gif')),
    ), 'main');
    
    // ---------------------------------------------------
    //  Folders
    // ---------------------------------------------------
    
    $folders = array(
      new MenuItem('assignments', lang('Assignmt.'), assemble_url('assignments'), get_image_url('navigation/assignments.gif')),
      new MenuItem('search', lang('Search'), assemble_url('quick_search'), get_image_url('navigation/search.gif')),
      new MenuItem('starred_folder', lang('Starred'), assemble_url('starred'), get_image_url('navigation/starred.gif'))
    );
    
    if($user->isAdministrator() || $user->getSystemPermission('manage_trash')) {
      $folders[] = new MenuItem('trash', lang('Trash'), assemble_url('trash'), get_image_url('navigation/trash.gif'));
    } // if
    
    $folders[] = new MenuItem('quick_add', lang('Quick Add'), assemble_url('homepage'), get_image_url('navigation/quick_add.gif'), null, '+');
    
    $menu->addToGroup($folders, 'folders');
  } // system_handle_on_build_menu

?>