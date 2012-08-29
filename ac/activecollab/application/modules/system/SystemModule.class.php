<?php

  /**
   * System module definition
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class SystemModule extends Module {
    
    /**
     * Plain module name
     *
     * @var string
     */
    var $name = 'system';
    
    /**
     * Is system module flag
     *
     * @var boolean
     */
    var $is_system = true;
    
    /**
     * Module version
     *
     * @var string
     */
    var $version = '2.0';
    
    // ---------------------------------------------------
    //  Events and Routes
    // ---------------------------------------------------
    
    /**
     * Define module routes
     *
     * @param Router $r
     * @return null
     */
    function defineRoutes(&$router) {
      
      // General
      $router->map('homepage', '');
      
      // API specific
      $router->map('info', 'info', array('controller' => 'api', 'action' => 'info'));
      $router->map('system_roles', 'roles/system', array('controller' => 'api', 'action' => 'system_roles'));
      $router->map('project_roles', 'roles/project', array('controller' => 'api', 'action' => 'project_roles'));
      $router->map('role_details', 'roles/:role_id', array('controller' => 'api', 'action' => 'role'), array('role_id' => '\d+'));
      
      // Auth
      $router->map('login', 'login', array('controller' => 'auth', 'action' => 'login'));
      $router->map('logout', 'logout', array('controller' => 'auth', 'action' => 'logout'));
      $router->map('forgot_password', 'lost-password', array('controller' => 'auth', 'action' => 'forgot_password'));
      $router->map('reset_password', 'reset-password', array('controller' => 'auth', 'action' => 'reset_password'));
      $router->map('refresh_session', 'refresh-session', array('controller' => 'auth', 'action' => 'refresh_session'));
      
      // Dashboard / Search / Starred / Trash
      $router->map('dashboard', 'dashboard', array('controller' => 'dashboard', 'action' => 'index'));
      $router->map('search', 'search', array('controller' => 'dashboard', 'action' => 'search'));
      $router->map('quick_search', 'quick-search', array('controller' => 'dashboard', 'action' => 'quick_search'));
      $router->map('new_since_last_visit', 'new-since-last-visit', array('controller' => 'dashboard', 'action' => 'new_since_last_visit'));;
      $router->map('mark_all_read', 'mark-all-read', array('controller' => 'dashboard', 'action' => 'mark_all_read'));;
      $router->map('recent_activities', 'recent-activities', array('controller' => 'dashboard', 'action' => 'recent_activities'));;
      $router->map('active_projects', 'active-projects', array('controller' => 'dashboard', 'action' => 'active_projects'));;
      $router->map('late_today', 'late-today', array('controller' => 'dashboard', 'action' => 'late_today'));;
      $router->map('starred', 'starred', array('controller' => 'dashboard', 'action' => 'starred'));
      $router->map('trash', 'trash', array('controller' => 'dashboard', 'action' => 'trash'));
      $router->map('trash_empty', 'trash_empty', array('controller' => 'dashboard', 'action' => 'trash_empty'));
      $router->map('quick_add', 'quick-add', array('controller' => 'dashboard', 'action' => 'quick_add'));
      $router->map('rss', 'rss', array('controller' => 'dashboard', 'action' => 'rss'));
      $router->map('ical', 'ical', array('controller' => 'dashboard', 'action' => 'ical'));
      $router->map('ical_subscribe', 'ical-subscribe', array('controller' => 'dashboard', 'action' => 'ical_subscribe'));
      $router->map('js_disabled', 'javascript-disabled', array('controller' => 'dashboard', 'action' => 'js_disabled'));
      
      // People
      $router->map('people', 'people', array('controller' => 'people', 'action' => 'index'));
      $router->map('people_archive', 'people/archive', array('controller' => 'people', 'action' => 'archive'));
      
      $router->map('people_companies_add', 'people/add-company', array('controller' => 'companies', 'action' => 'add'));
      $router->map('people_companies_quick_add', 'people/quick-add-company', array('controller' => 'companies', 'action' => 'quick_add'));
      $router->map('people_company', 'people/:company_id', array('controller' => 'companies', 'action' => 'view'), array('company_id' => '\d+'));
      $router->map('people_company_edit', 'people/:company_id/edit', array('controller' => 'companies', 'action' => 'edit'), array('company_id' => '\d+'));
      $router->map('people_company_delete', 'people/:company_id/delete', array('controller' => 'companies', 'action' => 'delete'), array('company_id' => '\d+'));
      $router->map('people_company_archive', 'people/:company_id/archive', array('controller' => 'companies', 'action' => 'archive'), array('company_id' => '\d+'));
      $router->map('people_company_edit_logo', 'people/:company_id/edit-logo', array('controller' => 'companies', 'action' => 'edit_logo'), array('company_id' => '\d+'));
      $router->map('people_company_delete_logo', 'people/:company_id/delete-logo', array('controller' => 'companies', 'action' => 'delete_logo'), array('company_id' => '\d+'));
      
      $router->map('people_company_user', 'people/:company_id/users/:user_id', array('controller' => 'users', 'action' => 'view'), array('company_id' => '\d+', 'user_id' => '\d+'));
      $router->map('people_company_user_add', 'people/:company_id/add-user', array('controller' => 'users', 'action' => 'add'), array('company_id' => '\d+'));
      $router->map('people_company_user_edit', 'people/:company_id/users/:user_id/edit', array('controller' => 'users', 'action' => 'edit'), array('company_id' => '\d+', 'user_id' => '\d+'));
      $router->map('people_company_user_edit_profile', 'people/:company_id/users/:user_id/edit-profile', array('controller' => 'users', 'action' => 'edit_profile'), array('company_id' => '\d+', 'user_id' => '\d+'));
      $router->map('people_company_user_edit_settings', 'people/:company_id/users/:user_id/edit-settings', array('controller' => 'users', 'action' => 'edit_settings'), array('company_id' => '\d+', 'user_id' => '\d+'));
      $router->map('people_company_user_edit_company_and_role', 'people/:company_id/users/:user_id/edit-company-and-role', array('controller' => 'users', 'action' => 'edit_company_and_role'), array('company_id' => '\d+', 'user_id' => '\d+'));
      $router->map('people_company_user_delete', 'people/:company_id/users/:user_id/delete', array('controller' => 'users', 'action' => 'delete'), array('company_id' => '\d+', 'user_id' => '\d+'));
      $router->map('people_company_user_edit_password', 'people/:company_id/users/:user_id/edit-password', array('controller' => 'users', 'action' => 'edit_password'), array('company_id' => '\d+', 'user_id' => '\d+'));
      $router->map('people_company_user_edit_avatar', 'people/:company_id/users/:user_id/edit-avatar', array('controller' => 'users', 'action' => 'edit_avatar'), array('company_id' => '\d+', 'user_id' => '\d+'));
      $router->map('people_company_user_delete_avatar', 'people/:company_id/users/:user_id/delete-avatar', array('controller' => 'users', 'action' => 'delete_avatar'), array('company_id' => '\d+', 'user_id' => '\d+'));
      $router->map('people_company_user_api', 'people/:company_id/users/:user_id/api', array('controller' => 'users', 'action' => 'api'), array('company_id' => '\d+', 'user_id' => '\d+'));
      $router->map('people_company_user_api_reset_key', 'people/:company_id/users/:user_id/reset-api-key', array('controller' => 'users', 'action' => 'api_reset_key'), array('company_id' => '\d+', 'user_id' => '\d+'));
      $router->map('people_company_user_recent_activities', 'people/:company_id/users/:user_id/recent-activities', array('controller' => 'users', 'action' => 'recent_activities'), array('company_id' => '\d+', 'user_id' => '\d+'));
      $router->map('people_company_user_add_to_projects', 'people/:company_id/users/:user_id/add-to-projects', array('controller' => 'users', 'action' => 'add_to_projects'), array('company_id' => '\d+', 'user_id' => '\d+'));
      $router->map('people_company_user_send_welcome_message', 'people/:company_id/users/:user_id/send-welcome-message', array('controller' => 'users', 'action' => 'send_welcome_message'), array('company_id' => '\d+', 'user_id' => '\d+'));
      
      // Projects
      $router->map('projects', 'projects', array('controller' => 'projects', 'action' => 'index'));
      $router->map('projects_all', 'projects/all', array('controller' => 'projects', 'action' => 'all_projects'));
      $router->map('projects_add', 'projects/add', array('controller' => 'project', 'action' => 'add'));
      $router->map('projects_archive', 'projects/archive', array('controller' => 'projects', 'action' => 'archive'));
      
      // Project groups
      $router->map('project_groups', 'projects/groups', array('controller' => 'project_groups', 'action' => 'index'));
      $router->map('project_groups_add', 'projects/groups/add', array('controller' => 'project_groups', 'action' => 'add'));
      $router->map('project_groups_quick_add', 'projects/groups/quick-add', array('controller' => 'project_groups', 'action' => 'quick_add'));
      $router->map('project_group', 'projects/groups/:project_group_id', array('controller' => 'project_groups', 'action' => 'view'), array('project_group_id' => '\d+'));
      $router->map('project_group_edit', 'projects/groups/:project_group_id/edit', array('controller' => 'project_groups', 'action' => 'edit'), array('project_group_id' => '\d+'));
      $router->map('project_group_delete', 'projects/groups/:project_group_id/delete', array('controller' => 'project_groups', 'action' => 'delete'), array('project_group_id' => '\d+'));
      
      // Single project
      $router->map('project_overview', 'projects/:project_id', array('controller' => 'project', 'action' => 'index'), array('project_id' => '\d+'));
      $router->map('project_user_tasks', 'projects/:project_id/user-tasks', array('controller' => 'project', 'action' => 'user_tasks'), array('project_id' => '\d+'));
      $router->map('project_rss', 'projects/:project_id/rss', array('controller' => 'project', 'action' => 'rss'), array('project_id' => '\d+'));
      $router->map('project_ical', 'projects/:project_id/ical', array('controller' => 'project', 'action' => 'ical'), array('project_id' => '\d+'));
      $router->map('project_ical_subscribe', 'projects/:project_id/ical-subscribe', array('controller' => 'project', 'action' => 'ical_subscribe'), array('project_id' => '\d+'));
      $router->map('project_edit', 'projects/:project_id/edit', array('controller' => 'project', 'action' => 'edit'), array('project_id' => '\d+'));
      $router->map('project_edit_status', 'projects/:project_id/edit-status', array('controller' => 'project', 'action' => 'edit_status'), array('project_id' => '\d+'));
      $router->map('project_edit_icon', 'projects/:project_id/edit-icon', array('controller' => 'project', 'action' => 'edit_icon'), array('project_id' => '\d+'));
      $router->map('project_delete', 'projects/:project_id/delete', array('controller' => 'project', 'action' => 'delete'), array('project_id' => '\d+'));
      $router->map('project_delete_icon', 'projects/:project_id/delete-icon', array('controller' => 'project', 'action' => 'delete_icon'), array('project_id' => '\d+'));
      $router->map('project_pin', 'projects/:project_id/pin', array('controller' => 'project', 'action' => 'pin'), array('project_id' => '\d+'));
      $router->map('project_unpin', 'projects/:project_id/unpin', array('controller' => 'project', 'action' => 'unpin'), array('project_id' => '\d+'));
      $router->map('project_export', 'projects/:project_id/export', array('controller' => 'project', 'action' => 'export'), array('project_id' => '\d+'));
      
      // Project people
      $router->map('project_people', 'projects/:project_id/people', array('controller' => 'project_people', 'action' => 'index'), array('project_id' => '\d+'));
      $router->map('project_people_add', 'projects/:project_id/people/add', array('controller' => 'project_people', 'action' => 'add_people'), array('project_id' => '\d+'));
      $router->map('project_remove_user', 'projects/:project_id/people/:user_id/remove-from-project', array('controller' => 'project_people', 'action' => 'remove_user'), array('project_id' => '\d+', 'user_id' => '\d+'));
      $router->map('project_user_permissions', 'projects/:project_id/people/:user_id/change-permissions', array('controller' => 'project_people', 'action' => 'user_permissions'), array('project_id' => '\d+', 'user_id' => '\d+'));
      
      // Project objects (generic, this stuff is usually overriden)
      $router->map('project_objects', 'projects/:project_id/objects', array('controller' => 'projects', 'action' => 'overview'), array('project_id' => '\d+'));
      $router->map('project_object_trash', 'projects/:project_id/objects/:object_id/move-to-trash', array('controller' => 'project_objects', 'action' => 'move_to_trash'), array('project_id' => '\d+', 'object_id' => '\d+'));
      $router->map('project_object_untrash', 'projects/:project_id/objects/:object_id/restore-from-trash', array('controller' => 'project_objects', 'action' => 'restore_from_trash'), array('project_id' => '\d+', 'object_id' => '\d+'));
      $router->map('project_object_change_visibility', 'projects/:project_id/objects/:object_id/change-visibility', array('controller' => 'project_objects', 'action' => 'change_visibility'), array('project_id' => '\d+', 'object_id' => '\d+'));
      $router->map('project_object_complete', 'projects/:project_id/objects/:object_id/complete', array('controller' => 'project_objects', 'action' => 'complete'), array('project_id' => '\d+', 'object_id' => '\d+'));
      $router->map('project_object_open', 'projects/:project_id/objects/:object_id/open', array('controller' => 'project_objects', 'action' => 'open'), array('project_id' => '\d+', 'object_id' => '\d+'));
      $router->map('project_object_move', 'projects/:project_id/objects/:object_id/move', array('controller' => 'project_objects', 'action' => 'move'), array('project_id' => '\d+', 'object_id' => '\d+'));
      $router->map('project_object_copy', 'projects/:project_id/objects/:object_id/copy', array('controller' => 'project_objects', 'action' => 'copy'), array('project_id' => '\d+', 'object_id' => '\d+'));
      $router->map('project_object_lock', 'projects/:project_id/objects/:object_id/lock', array('controller' => 'project_objects', 'action' => 'lock'), array('project_id' => '\d+', 'object_id' => '\d+'));
      $router->map('project_object_unlock', 'projects/:project_id/objects/:object_id/unlock', array('controller' => 'project_objects', 'action' => 'unlock'), array('project_id' => '\d+', 'object_id' => '\d+'));  
      $router->map('project_object_star', 'projects/:project_id/objects/:object_id/star', array('controller' => 'project_objects', 'action' => 'star'), array('project_id' => '\d+', 'object_id' => '\d+'));
      $router->map('project_object_unstar', 'projects/:project_id/objects/:object_id/unstar', array('controller' => 'project_objects', 'action' => 'unstar'), array('project_id' => '\d+', 'object_id' => '\d+'));  
      
      // Widgets
      $router->map('select_users_widget', 'select-users', array('controller' => 'widgets', 'action' => 'select_users'));
      $router->map('select_projects_widget', 'select-projects', array('controller' => 'widgets', 'action' => 'select_projects'));
      $router->map('jump_to_project_widget', 'jump-to-project', array('controller' => 'widgets', 'action' => 'jump_to_project'));
      $router->map('object_subscribers_widget', 'object-subscriptions', array('controller' => 'widgets', 'action' => 'object_subscribers'));
      $router->map('image_picker', 'image-picker', array('controller' => 'widgets', 'action' => 'image_picker'));
      $router->map('link_picker', 'link-picker', array('controller' => 'widgets', 'action' => 'link_picker'));
      
      // ---------------------------------------------------
      //  Administration
      // ---------------------------------------------------
      
      $router->map('admin', 'admin', array('controller' => 'admin', 'action' => 'index'));
      
      // Roles
      $router->map('admin_roles', 'admin/roles', array('controller' => 'roles_admin', 'action' => 'index'));
      $router->map('admin_roles_add_system', 'admin/roles/add-system', array('controller' => 'roles_admin', 'action' => 'add_system'));
      $router->map('admin_roles_add_project', 'admin/roles/add-project', array('controller' => 'roles_admin', 'action' => 'add_project'));
      
      $router->map('admin_role', 'admin/roles/:role_id', array('controller' => 'roles_admin', 'action' => 'view'), array('role_id' => '\d+'));
      $router->map('admin_role_edit', 'admin/roles/:role_id/edit', array('controller' => 'roles_admin', 'action' => 'edit'), array('role_id' => '\d+'));
      $router->map('admin_role_delete', 'admin/roles/:role_id/delete', array('controller' => 'roles_admin', 'action' => 'delete'), array('role_id' => '\d+'));
      $router->map('admin_role_set_as_default', 'admin/roles/:role_id/set-as-default', array('controller' => 'roles_admin', 'action' => 'set_as_default'), array('role_id' => '\d+'));
      $router->map('admin_role_set_permission_value', 'admin/roles/:role_id/set-permission-value/:permission_name', array('controller' => 'roles_admin', 'action' => 'set_permission_value'), array('role_id' => '\d+'));
      
      // Modules
      $router->map('admin_modules', 'admin/modules', array('controller' => 'modules_admin', 'action' => 'index'));
      $router->map('admin_module', 'admin/modules/:module_name', array('controller' => 'modules_admin', 'action' => 'module'));
      $router->map('admin_module_install', 'admin/modules/:module_name/install', array('controller' => 'modules_admin', 'action' => 'install'));
      $router->map('admin_module_uninstall', 'admin/modules/:module_name/uninstall', array('controller' => 'modules_admin', 'action' => 'uninstall'));
      
      // Settings
      $router->map('admin_settings', 'admin', array('controller' => 'admin', 'action' => 'index'));
      $router->map('admin_settings_general', 'admin/settings/general', array('controller' => 'settings', 'action' => 'general'));
      $router->map('admin_settings_mailing', 'admin/settings/mailing', array('controller' => 'settings', 'action' => 'mailing'));
      $router->map('admin_settings_mailing_test_connection', 'admin/settings/mailing/test_connection', array('controller' => 'settings', 'action' => 'mailing_test_connection'));
      $router->map('admin_settings_date_time', 'admin/settings/date-time', array('controller' => 'settings', 'action' => 'date_time'));
      $router->map('admin_settings_hide_welcome_message', 'admin/settings/hide-welcome-message', array('controller' => 'settings', 'action' => 'hide_welcome_message'));
      $router->map('admin_settings_categories', 'admin/settings/categories', array('controller' => 'categories_admin'));
      $router->map('admin_settings_maintenance', 'admin/settings/maintenance', array('controller' => 'settings', 'action' => 'maintenance'));
      
      $router->map('admin_settings_email_templates', 'admin/settings/email-templates', array('controller' => 'email_templates_admin', 'action' => 'index'));
      $router->map('admin_settings_email_template', 'admin/settings/email-templates/:module_name/:template_name', array('controller' => 'email_templates_admin', 'action' => 'details'));
      $router->map('admin_settings_email_template_edit', 'admin/settings/email-templates/:module_name/:template_name/edit', array('controller' => 'email_templates_admin', 'action' => 'edit'));
      
      // Languages
      $router->map('admin_languages', 'admin/languages', array('controller' => 'languages_admin'));
      $router->map('admin_languages_add', 'admin/languages/add', array('controller' => 'languages_admin', 'action' => 'add'));
      $router->map('admin_languages_import', 'admin/languages/import', array('controller' => 'languages_admin', 'action' => 'import'));
      $router->map('admin_language', 'admin/languages/:language_id', array('controller' => 'languages_admin', 'action' => 'view'), array('language_id' => '\d+'));
      $router->map('admin_language_export', 'admin/languages/:language_id/export', array('controller' => 'languages_admin', 'action' => 'export'), array('language_id' => '\d+'));
      $router->map('admin_language_edit', 'admin/languages/:language_id/edit', array('controller' => 'languages_admin', 'action' => 'edit'), array('language_id' => '\d+'));
      $router->map('admin_language_delete', 'admin/languages/:language_id/delete', array('controller' => 'languages_admin', 'action' => 'delete'), array('language_id' => '\d+'));
      $router->map('admin_language_set_default', 'admin/languages/:language_id/set-as-default', array('controller' => 'languages_admin', 'action' => 'set_as_default'), array('language_id' => '\d+'));
      $router->map('admin_language_add_translation_file', 'admin/languages/:language_id/add-translation-file', array('controller' => 'languages_admin', 'action' => 'add_translation_file'), array('language_id' => '\d+'));
      $router->map('admin_language_edit_translation_file', 'admin/languages/:language_id/edit-translation-file', array('controller' => 'languages_admin', 'action' => 'edit_translation_file'), array('language_id' => '\d+'));
      
      // Tools
      $router->map('admin_tools_test_email', 'admin/tools/test-email', array('controller' => 'system_tools', 'action' => 'test_email'));
      $router->map('admin_tools_mass_mailer', 'admin/tools/mass-mailer', array('controller' => 'system_tools', 'action' => 'mass_mailer'));
      
      // Other
      $router->map('admin_other_scheduled_tasks', 'admin/scheduled-tasks', array('controller' => 'system_tools', 'action' => 'scheduled_tasks'));
      
      // Cron
      $router->map('frequently', 'frequently', array('controller' => 'cron', 'action' => 'frequently'));
      $router->map('hourly', 'hourly', array('controller' => 'cron', 'action' => 'hourly'));
      $router->map('daily', 'daily', array('controller' => 'cron', 'action' => 'daily'));
      
    } // defineRoutes
    
    /**
     * Define event handlers
     *
     * @param EventsManager $events
     * @return null
     */
    function defineHandlers(&$events) {
      $events->listen('on_shutdown', 'on_shutdown');
      $events->listen(array(
        'on_object_inserted', 
        'on_object_deleted', 
        'on_project_object_opened', 
        'on_project_object_completed', 
        'on_project_object_trashed', 
        'on_project_object_restored', 
      ), 'project_task_status');
      
      $events->listen('on_before_object_insert', 'on_before_object_insert');
      $events->listen('on_before_object_update', 'on_before_object_update');
      $events->listen('on_project_object_ready', 'on_project_object_ready');
      $events->listen('on_project_object_completed', 'on_project_object_completed');
      $events->listen('on_project_object_opened', 'on_project_object_opened');
      $events->listen('on_project_export', 'on_project_export');
      $events->listen('on_project_overview_sidebars', 'on_project_overview_sidebars');
      
      $events->listen('on_user_cleanup', 'on_user_cleanup');
      
      $events->listen('on_build_menu', 'on_build_menu');
      
      $events->listen('on_settings_sections', 'on_settings_sections');
      $events->listen('on_admin_sections', 'on_admin_sections');
      
      $events->listen('on_email_templates', 'on_email_templates');
      
      $events->listen('on_daily', 'on_daily');
      $events->listen('on_hourly', 'on_hourly');
      
      $events->listen('on_system_permissions', 'on_system_permissions');
      $events->listen('on_dashboard_sections', 'on_dashboard_sections');
    } // defineHandlers
    
    // ---------------------------------------------------
    //  Names
    // ---------------------------------------------------
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('System');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @param void
     * @return string
     */
    function getDescription() {
      return lang('activeCollab foundation');
    } // getDescription
    
  }

?>