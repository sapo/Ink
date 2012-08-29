<?php

  /**
   * Application initialization file
   * 
   * If we need some application wide resources we can include them here. This 
   * file is included after the environment is inited
   */
  
  // Project object visibility
  define('VISIBILITY_PRIVATE', 0);
  define('VISIBILITY_NORMAL',  1);
  define('VISIBILITY_PUBLIC',  2);
  
  // Project object state
  define('STATE_SPAM',    0);
  define('STATE_DELETED', 1);
  define('STATE_DRAFT',   2);
  define('STATE_VISIBLE', 3);
  
  // Project object status
  define('STATUS_NEW', 'new');
  define('STATUS_ASSIGNED', 'assigned');
  define('STATUS_REOPENED', 'reopened');
  define('STATUS_CLOSED', 'closed');
  
  // Project object priority
  define('PRIORITY_LOWEST', -2);
  define('PRIORITY_LOW',    -1);
  define('PRIORITY_NORMAL',  0);
  define('PRIORITY_HIGH',    1);
  define('PRIORITY_HIGHEST', 2);
  
  // Role types
  define('ROLE_TYPE_SYSTEM', 'system');
  define('ROLE_TYPE_PROJECT', 'project');
  
?>