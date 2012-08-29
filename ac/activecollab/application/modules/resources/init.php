<?php

  /**
   * Comments module initialization file
   *
   * @package activeCollab.modules.resources
   */
  
  define('RESOURCES_MODULE', 'resources');
  define('RESOURCES_MODULE_PATH', APPLICATION_PATH . '/modules/resources');
  
  define('FILE_TYPE_DOCUMENT', 'document');
  define('FILE_TYPE_ARCHIVE', 'archive');
  define('FILE_TYPE_CODE', 'code');
  define('FILE_TYPE_IMAGE', 'image');
  define('FILE_TYPE_VIDEO', 'video');
  define('FILE_TYPE_AUDIO', 'audio');
  define('FILE_TYPE_UNKNOWN', 'unknown');
  
  define('DATE_FILTER_LATE', 'late');
  define('DATE_FILTER_TODAY', 'today');
  define('DATE_FILTER_TOMORROW', 'tomorrow');
  define('DATE_FILTER_LAST_WEEK', 'last_week');
  define('DATE_FILTER_THIS_WEEK', 'this_week');
  define('DATE_FILTER_NEXT_WEEK', 'next_week');
  define('DATE_FILTER_LAST_MONTH', 'last_month');
  define('DATE_FILTER_THIS_MONTH', 'this_month');
  define('DATE_FILTER_NEXT_MONTH', 'next_month');
  define('DATE_FILTER_SELECTED_DATE', 'selected_date');
  define('DATE_FILTER_SELECTED_RANGE', 'selected_range');
  
  define('PROJECT_FILTER_ACTIVE', 'active');
  define('PROJECT_FILTER_SELECTED', 'selected');
  
  define('USER_FILTER_ANYBODY', 'anybody');
  define('USER_FILTER_NOT_ASSIGNED', 'not_assigned');
  define('USER_FILTER_LOGGED_USER', 'logged_user');
  define('USER_FILTER_LOGGED_USER_RESPONSIBLE', 'logged_user_responsible');
  define('USER_FILTER_COMPANY', 'company');
  define('USER_FILTER_SELECTED', 'selected');
  
  define('ATTACHMENT_TYPE_ATTACHMENT', 'attachment');
  define('ATTACHMENT_TYPE_FILE_REVISION', 'file_revision');
  
  define('STATUS_FILTER_ACTIVE', 'active');
  define('STATUS_FILTER_COMPLETED', 'completed');
  define('STATUS_FILTER_ALL', 'all');
  
  define('COMPLETED_TASKS_PER_OBJECT', 3);
  
  use_model(array('assignments', 'subscriptions', 'assignment_filters', 'reminders', 'attachments'), RESOURCES_MODULE);
  
  set_for_autoload(array(
    'Comment' => RESOURCES_MODULE_PATH . '/models/comments/Comment.class.php',
    'Comments' => RESOURCES_MODULE_PATH . '/models/comments/Comments.class.php',
    'Category' => RESOURCES_MODULE_PATH . '/models/categories/Category.class.php',
    'Categories' => RESOURCES_MODULE_PATH . '/models/categories/Categories.class.php',
    'Task' => RESOURCES_MODULE_PATH . '/models/tasks/Task.class.php',
    'Tasks' => RESOURCES_MODULE_PATH . '/models/tasks/Tasks.class.php',
    'Attachment' => RESOURCES_MODULE_PATH . '/models/attachments/Attachment.class.php',
    'Attachments' => RESOURCES_MODULE_PATH . '/models/attachments/Attachments.class.php',
    'Tags' => RESOURCES_MODULE_PATH . '/models/tags/Tags.class.php',
    'NewTaskActivityLog' => RESOURCES_MODULE_PATH . '/models/activity_logs/NewTaskActivityLog.class.php',
    'TaskCompletedActivityLog' => RESOURCES_MODULE_PATH . '/models/activity_logs/TaskCompletedActivityLog.class.php',
    'TaskReopenedActivityLog' => RESOURCES_MODULE_PATH . '/models/activity_logs/TaskReopenedActivityLog.class.php',
    'NewCommentActivityLog' => RESOURCES_MODULE_PATH . '/models/activity_logs/NewCommentActivityLog.class.php',
  ));
  
  require_once RESOURCES_MODULE_PATH . '/functions.php';

?>