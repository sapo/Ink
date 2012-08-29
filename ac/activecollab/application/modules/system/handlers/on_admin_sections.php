<?php

  /**
   * System module on_admin_sections event handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */
  
  /**
   * Add system admin tools sections
   *
   * @param array $sections
   * @return null
   */
  function system_handle_on_admin_sections(&$sections) {
    $sections[ADMIN_SECTION_SYSTEM][SYSTEM_MODULE] = array(
      array(
        'name'        => lang('General'),
        'description' => lang('General activeCollab settings'),
        'url'         => assemble_url('admin_settings_general'),
        'icon'        => get_image_url('settings/general.gif', SYSTEM_MODULE),
      ),
      array(
        'name'        => lang('Modules'),
        'description' => lang('Module administration tool. You can use it to install/uninstall modules, configure them and so on'),
        'url'         => assemble_url('admin_modules'),
        'icon'        => get_image_url('admin/modules.gif'),
      ),
      array(
        'name'        => lang('Roles'),
        'description' => lang('Roles administration tool. Use it to configure system level permissions for users of the system'),
        'url'         => assemble_url('admin_roles'),
        'icon'        => get_image_url('admin/roles.gif'),
      ),
      array(
        'name'        => lang('Date and Time'),
        'description' => lang('Set system timezone, daylight saving time, first day of the week etc'),
        'url'         => assemble_url('admin_settings_date_time'),
        'icon'        => get_image_url('settings/date_time.gif', SYSTEM_MODULE),
      ),
      array(
        'name'        => lang('Master Categories'),
        'description' => lang('Manage default categories that are added when new project is created'),
        'url'         => assemble_url('admin_settings_categories'),
        'icon'        => get_image_url('settings/categories.gif', SYSTEM_MODULE),
      ),
      array(
        'name'        => lang('Maintenance Mode'),
        'description' => lang('Put system in maintenance mode'),
        'url'         => assemble_url('admin_settings_maintenance'),
        'icon'        => get_image_url('admin/maintenance.gif'),
      ),
    );
    
    if(LOCALIZATION_ENABLED) {
      $sections[ADMIN_SECTION_SYSTEM][SYSTEM_MODULE][] = array(
        'name'        => lang('Languages'),
        'description' => lang('Tools for managing available languages and translations'),
        'url'         => assemble_url('admin_languages'),
        'icon'        => get_image_url('admin/languages.gif'),
      );
    } // if
    
    $sections[ADMIN_SECTION_MAIL][SYSTEM_MODULE] = array(
      array(
        'name'        => lang('Mailing'),
        'description' => lang('Set up how activeCollab will send emails - you can use your default PHP settings or SMTP server'),
        'url'         => assemble_url('admin_settings_mailing'),
        'icon'        => get_image_url('settings/mailing.gif', SYSTEM_MODULE),
      ),
      array(
        'name'        => lang('Email Templates'),
        'description' => lang('Browse and change email templates that are used to generate emails that users receive as notifications'),
        'url'         => assemble_url('admin_settings_email_templates'),
        'icon'        => get_image_url('settings/email_templates.gif', SYSTEM_MODULE),
      ),
      array(
        'name'        => lang('Test Mail Settings'),
        'description' => lang('Use this simple tool to send test emails to check if activeCollab mailer is well configured'),
        'url'         => assemble_url('admin_tools_test_email'),
        'icon'        => get_image_url('tools/test_mail_settings.gif', SYSTEM_MODULE)
      ),
    );
    
    if (MASS_MAILER_ENABLED) {
      $sections[ADMIN_SECTION_MAIL][SYSTEM_MODULE][] = array(
        'name'        => lang('Mass Mailer'),
        'description' => lang('Simple tool that let you send plain text messages to any group of users registered to the system'),
        'url'         => assemble_url('admin_tools_mass_mailer'),
        'icon'        => get_image_url('tools/mass_mailer.gif', SYSTEM_MODULE)
      );
    } // if
   
    $sections[ADMIN_SECTION_TOOLS][SYSTEM_MODULE] = array(
      array(
        'name'        => lang('Scheduled Tasks'),
        'description' => lang('Log of scheduled tasks last activity time'),
        'url'         => assemble_url('admin_other_scheduled_tasks'),
        'icon'        => get_image_url('admin/scheduled-tasks.gif'),
      ),
    );
  } // system_handle_on_admin_sections

?>