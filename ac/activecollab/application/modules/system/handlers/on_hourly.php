<?php

  /**
   * System handle hourly tasks
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */
  
  /**
   * Do hourly tasks
   *
   * @param void
   * @return null
   */
  function system_handle_on_hourly() {
    $cache =& Cache::instance();
    if(instance_of($cache->backend, 'CacheBackend')) {
      $cache->backend->cleanup();
    } // if
  } // system_handle_on_hourly

?>