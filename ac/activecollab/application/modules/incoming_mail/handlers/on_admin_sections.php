<?php

  /**
   * Incoming Mail module on_admin_section event handler
   *
   * @package activeCollab.modules.incoming_mail
   * @subpackage handlers
   */

  /**
   * Register tool in administration mail section
   *
   * @param array $sections
   * @return null
   */
  function incoming_mail_handle_on_admin_sections(&$sections) {
    $sections[ADMIN_SECTION_MAIL][ADMIN_SECTION_MAIL] = array(
      array(
        'name'        => lang('Incoming Mail'),
        'description' => lang('Create tickets (or discussions) and comments from mailboxes'),
        'url'         => assemble_url('incoming_mail_admin'),
        'icon'        => get_image_url('icon.gif', INCOMING_MAIL_MODULE)
      ),
    );
  } // backup_handle_on_admin_sections

?>