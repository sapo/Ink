<?php

  /**
   * Backup module on_admin_section event handler
   *
   * @package activeCollab.modules.backup
   * @subpackage handlers
   */

  /**
   * Register tool in administration tools section
   *
   * @param array $sections
   * @return null
   */
  function backup_handle_on_admin_sections(&$sections) {
    $sections[ADMIN_SECTION_TOOLS][BACKUP_MODULE] = array(
      array(
        'name'        => lang('Backup'),
        'description' => lang('Automatic backup of your activeCollab installation'),
        'url'         => assemble_url('backup_admin'),
        'icon'        => get_image_url('icon_big.gif', BACKUP_MODULE)
      ),
    );
  } // backup_handle_on_admin_sections

?>