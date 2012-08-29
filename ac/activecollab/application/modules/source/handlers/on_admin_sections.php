<?php

  /**
   * Source module on_admin_section event handler
   *
   * @package activeCollab.modules.source
   * @subpackage handlers
   */

  /**
   * Register tool in administration mail section
   *
   * @param array $sections
   * @return null
   */
  function source_handle_on_admin_sections(&$sections) {
    $sections[ADMIN_SECTION_TOOLS][SOURCE_MODULE] = array(
      array(
        'name'        => lang('Source Settings'),
        'description' => lang('Modify default source module settings'),
        'url'         => assemble_url('admin_source'),
        'icon'        => get_image_url('icon_big.gif', SOURCE_MODULE)
      ),
    );
  } // backup_handle_on_admin_sections

?>