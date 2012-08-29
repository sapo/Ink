<?php

  /**
   * Init system module
   *
   * @package activeCollab.modules.system
   */
  
  define('SYSTEM_MODULE', 'system');
  define('SYSTEM_MODULE_PATH', APPLICATION_PATH . '/modules/system');
  
  require_once SYSTEM_MODULE_PATH . '/functions.php';
  
  define('SYSTEM_CONFIG_OPTION', 'system');
  define('PROJECT_CONFIG_OPTION', 'project');
  define('USER_CONFIG_OPTION', 'user');
  define('COMPANY_CONFIG_OPTION', 'company');
  
  define('PROJECT_TYPE_NORMAL', 'normal');
  define('PROJECT_TYPE_SYSTEM', 'system');
  
  define('PROJECT_STATUS_ACTIVE', 'active');
  define('PROJECT_STATUS_PAUSED', 'paused');
  define('PROJECT_STATUS_CANCELED', 'canceled');
  define('PROJECT_STATUS_COMPLETED', 'completed');
  
  define('MAILING_NATIVE', 'native');
  define('MAILING_SMTP', 'smtp');
  
  define('ADMIN_SECTION_SYSTEM', 'System');
  define('ADMIN_SECTION_MAIL', 'Mail');
  define('ADMIN_SECTION_TOOLS', 'Tools');
  define('ADMIN_SECTION_OTHER', 'Other');
  
  define('PROJECT_PERMISSION_NONE', 0);
  define('PROJECT_PERMISSION_ACCESS', 1);
  define('PROJECT_PERMISSION_CREATE', 2);
  define('PROJECT_PERMISSION_MANAGE', 3);
  
  define('API_DISABLED', 0);
  define('API_READ_ONLY', 1);
  define('API_READ_WRITE', 2);
  
  define('PAGE_MESSAGE_INFO', 'info');
  define('PAGE_MESSAGE_WARNING', 'warning');
  define('PAGE_MESSAGE_ERROR', 'error');
  define('PAGE_MESSAGE_PRIVATE', 'private');
  define('PAGE_MESSAGE_TRASHED', 'trashed');
  
  define('OBJECT_SOURCE_WEB', 'web');
  
  // first aid statuses
  define('FIRST_AID_STATUS_OK', '0');
  define('FIRST_AID_STATUS_WARNING', '1');
  define('FIRST_AID_STATUS_ERROR', '2');
  
  define('SCHEDULED_TASK_FREQUENTLY', 'frequently');
  define('SCHEDULED_TASK_HOURLY', 'hourly');
  define('SCHEDULED_TASK_DAILY', 'daily');
  
  // ---------------------------------------------------
  //  Load
  // ---------------------------------------------------
  
  require_once SYSTEM_MODULE_PATH . '/models/ApplicationObject.class.php';
  use_model(array(
    'modules', 
    'users', 
    'companies', 
    'roles', 
    'config_options', 
    'languages', 
    'email_templates', 
    'project_groups', 
    'projects', 
    'activity_logs', 
    'project_objects', 
    'project_users'
   ), SYSTEM_MODULE);
  
  require_once SYSTEM_MODULE_PATH . '/models/Wireframe.class.php';
  
  require_once SYSTEM_MODULE_PATH . '/models/UserConfigOptions.class.php';
  require_once SYSTEM_MODULE_PATH . '/models/StarredObjects.class.php';
  require_once SYSTEM_MODULE_PATH . '/controllers/ApplicationController.class.php';
  require_once SYSTEM_MODULE_PATH . '/models/search_engines/' . SEARCH_ENGINE . '.class.php';
  
  set_for_autoload(array(
    'CompanyConfigOptions' => SYSTEM_MODULE_PATH . '/models/CompanyConfigOptions.class.php',
    'PinnedProjects' => SYSTEM_MODULE_PATH . '/models/PinnedProjects.class.php',
    'AnonymousUser' => SYSTEM_MODULE_PATH . '/models/AnonymousUser.class.php',
    'Thumbnails' => SYSTEM_MODULE_PATH . '/models/Thumbnails.class.php',
    'ProjectObjectViews' => SYSTEM_MODULE_PATH . '/models/ProjectObjectViews.class.php',
    'Permissions' => SYSTEM_MODULE_PATH . '/models/Permissions.class.php',
    'ApplicationMailer' => SYSTEM_MODULE_PATH . '/models/ApplicationMailer.class.php',
    'ObjectCreatedActivityLog' => SYSTEM_MODULE_PATH . '/models/activity_logs/ObjectCreatedActivityLog.class.php',
    'ObjectUpdatedActivityLog' => SYSTEM_MODULE_PATH . '/models/activity_logs/ObjectUpdatedActivityLog.class.php',
    'ObjectTrashedActivityLog' => SYSTEM_MODULE_PATH . '/models/activity_logs/ObjectTrashedActivityLog.class.php',
    'ObjectRestoredActivityLog' => SYSTEM_MODULE_PATH . '/models/activity_logs/ObjectRestoredActivityLog.class.php',
    'CommentsLockedActivityLog' => SYSTEM_MODULE_PATH . '/models/activity_logs/CommentsLockedActivityLog.class.php',
    'CommentsUnlockedActivityLog' => SYSTEM_MODULE_PATH . '/models/activity_logs/CommentsUnlockedActivityLog.class.php',
  ));

?>