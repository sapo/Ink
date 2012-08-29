<?php

  /**
   * Resources on_dashboard_important_section handler
   *
   * @package activeCollab.modules.resources
   * @subpackage handlers
   */

  /**
   * Handle on_dashboard_important_section event
   *
   * @param NamedList $items
   * @param User $user
   * @return null
   */
  function resources_handle_on_dashboard_important_section(&$items, &$user) {
    if ($reminders_count = Reminders::countActiveByUser($user)) {
      $items->add('reminders', array(
        'label'       => $reminders_count > 1 ? lang('<strong>:count</strong>&nbsp;reminders', array('count' => $reminders_count)) : lang('<strong>:count</strong>&nbsp;reminder', array('count' => $reminders_count)),
        'class'       => 'reminders',
        'icon'        => get_image_url('important.gif'),
        'url'         => assemble_url('reminders'),
      ));
    } // if
  } // on_dashboard_important_section
?>
