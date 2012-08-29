<?php

  /**
   * Public Submit module on_admin_section event handler
   *
   * @package activeCollab.modules.public_submit
   * @subpackage handlers
   */

  /**
   * Register tool in administration tools section
   *
   * @param array $sections
   * @return null
   */
  function public_submit_handle_on_admin_sections(&$sections) {
    $sections[ADMIN_SECTION_TOOLS][PUBLIC_SUBMIT_MODULE] = array(
      array(
        'name'        => lang('Public Submit'),
        'description' => lang('Information about Public Submit module'),
        'url'         => assemble_url('admin_settings_public_submit'),
        'icon'        => get_image_url('icon_big.gif', PUBLIC_SUBMIT_MODULE)
      ),
    );
  } // public_submit_handle_on_admin_sections

?>