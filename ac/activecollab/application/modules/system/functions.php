<?php

  /**
   * System module functions
   *
   * @package activeCollab.modules.system
   */

  /**
   * Clear all user permissions / project related chaches
   *
   * @param User $user
   * @return null
   */
  function clean_user_permissions_cache($user) {
    cache_remove('visible_types_filter_for_' . $user->getId());
  	cache_remove('visible_project_types_filter_for_' . $user->getId());
  	cache_remove('visible_project_types_for_' . $user->getId());
  } // clean_user_permissions_cache

  /**
   * Clear all permissions or caches when project gets updated or removed
   *
   * @param Project $project
   * @return null
   */
  function clean_project_permissions_cache($project) {
  	clean_permissions_cache();
  } // clean_project_permissions_cache

  /**
   * Clear entire user cache
   *
   * @param void
   * @return null
   */
  function clean_permissions_cache() {
    cache_remove_by_pattern('visible_types_filter_for_*');
  	cache_remove_by_pattern('visible_project_types_filter_for_*');
  	cache_remove_by_pattern('visible_project_types_for_*');
  } // clean_permissions_cache

  /**
   * Clean up assignments cache
   *
   * @param void
   * @return null
   */
  function clean_assignments_cache() {
    cache_remove_by_pattern('object_assignments_*');
    cache_remove_by_pattern('object_assignments_*_rendered');
    cache_remove_by_pattern('user_assignments_*');
  } // clean_assignments_cache

  // ---------------------------------------------------
  //  Date time
  // ---------------------------------------------------

  /**
   * Return offset based on a current user
   *
   * @param void
   * @return integer
   */
  function get_system_gmt_offset() {
    static $offset = null;

    if($offset === null) {
      $timezone_offset = ConfigOptions::getValue('time_timezone');
      $dst = ConfigOptions::getValue('time_dst');

      $offset = $dst ? $timezone_offset + 3600 : $timezone_offset;
    } // if

    return $offset;
  } // get_system_gmt_offset

  /**
   * Return user GMT offset
   *
   * Return number of seconds that current user is away from the GMT. If user is
   * not logged in this function should return system offset
   *
   * @param User $user
   * @return integer
   */
  function get_user_gmt_offset($user = null) {
    static $offset = array();

    if(!instance_of($user, 'User')) {
      $user = get_logged_user();
    } // if

    if(!instance_of($user, 'User')) {
      return get_system_gmt_offset();
    } // if

    if(!isset($offset[$user->getId()])) {
      $timezone_offset = UserConfigOptions::getValue('time_timezone', $user);
      $dst = UserConfigOptions::getValue('time_dst', $user);

      $offset[$user->getId()] = $dst ? $timezone_offset + 3600 : $timezone_offset;
    } // if

    return $offset[$user->getId()];
  } // get_user_gmt_offset

  // ---------------------------------------------------
  //  Project object types
  // ---------------------------------------------------

  /**
   * Return an array of project object types
   *
   * @param void
   * @return array
   */
  function get_project_object_types() {
    static $types = false;

    if($types === false) {
      $types = event_trigger('on_get_project_object_types', array(), array());
      if(is_foreachable($types)) {
        sort($types);
      } // if
    } // if

    return $types;
  } // get_project_object_types

  /**
   * Return array of object types that can be completed
   *
   * Object that can be completed are counted and we use that data to see how
   * far project has gone (completed vs open tasks)
   *
   * @param void
   * @return array
   */
  function get_completable_project_object_types() {
    static $types = false;

    if($types === false) {
      $types = event_trigger('on_get_completable_project_object_types', array(), array());
      if(is_foreachable($types)) {
        sort($types);
      } // if
    } // if

    return $types;
  } // get_completable_project_object_types

  /**
   * Return day project object types
   *
   * Day project object types are important day events - like milestones or
   * events. They are shown in calendar without the day zoom
   *
   * @param void
   * @return array
   */
  function get_day_project_object_types() {
    static $types = false;

    if($types === false) {
      $types = event_trigger('on_get_day_project_object_types', array(), array());
      if(is_foreachable($types)) {
        sort($types);
      } // if
    } // if

    return $types;
  } // get_day_project_object_types

  /**
   * Return URL-s of project icons, large and small
   *
   * @param Project $project
   * @return array
   */
  function get_project_icon_urls($project) {
    static $use_client_logos = null;

  	$project_id = instance_of($project, 'Project') ? $project->getId() : (integer) $project;
  	if($project_id) {
  	  $cached_values = cache_get('project_icons');
  	  if(is_array($cached_values)) {
      	if(isset($cached_values[$project_id])) {
      	  return $cached_values[$project_id];
      	} // if
  	  } else {
  	    $cached_values = array(); // initial if cache value is new
  	  } // if

    	if($use_client_logos === null) {
    	  $use_client_logos = ConfigOptions::getValue('projects_use_client_icons');
    	} // if

    	$client_id = null;
    	if($use_client_logos) {
    	  $client_id = instance_of($project, 'Project') ? $project->getCompanyId() : Projects::findClientId($project_id);
    	} // if

    	$icons = array();
    	$sizes = array('40x40', '16x16');
    	foreach($sizes as $size) {
    	  $supposed_project_icon_path = ENVIRONMENT_PATH . '/' . PUBLIC_FOLDER_NAME . "/projects_icons/$project_id.$size.gif";
    	  $supposed_client_icon_path = ENVIRONMENT_PATH . '/' . PUBLIC_FOLDER_NAME . "/logos/$client_id.$size.jpg";
    	      	  
    	  if(is_file($supposed_project_icon_path)) {
    	    $icons[] = ROOT_URL."/projects_icons/$project_id.$size.gif?updated_on=" . filemtime($supposed_project_icon_path);
    	  } elseif($use_client_logos && $client_id && is_file($supposed_client_icon_path)) {
    	    $icons[] = ROOT_URL . "/logos/$client_id.$size.jpg?updated_on=" . filemtime($supposed_client_icon_path);
    	  } else {
    	    $icons[] = ROOT_URL . "/projects_icons/default.$size.gif";
    	  } // if
    	} // foreach

    	$cached_values[$project_id] = $icons;
    	cache_set('project_icons', $cached_values);

    	return $icons;
  	} // if

  	return array('#', '#'); // no project?
  } // get_project_icon_urls

  // ---------------------------------------------------
  //  Roles
  // ---------------------------------------------------

  /**
   * Returns list of system roles which have can_see_private_objects set to Yes
   *
   * If $as_string is set to yes function returns list of names separated with
   * comma (like Adminstrator, Project Manager, People Manager or Member)
   *
   * @param boolean $as_string
   * @return array
   */
  function who_can_see_private_objects($as_string = false, $separator = null) {
  	$roles = Roles::findSystemRoles();

    $result = array();
    if(is_foreachable($roles)) {
      foreach($roles as $role) {
        if($role->getPermissionValue('admin_access') || $role->getPermissionValue('project_management') || $role->getPermissionValue('can_see_private_objects')) {
          $result[] = $as_string ? $role->getName() : $role;
        } // if
      } // foreach
    } // if

    if($as_string) {
      if($separator === null) {
        $separator = lang(' and ');
      } // if

      require_once SMARTY_PATH . '/plugins/function.join.php';
      return smarty_function_join(array('items' => $result, 'final_separator' => $separator), $smarty);
    } else {
      return $result;
    } // if
  } // who_can_see_private_objects

  // ---------------------------------------------------
  //  Custom
  // ---------------------------------------------------

  /**
   * Group objects by given date
   *
   * @param array $objects
   * @param User $user
   * @param string $getter
   * @param boolean $today_yesterday
   * @return array
   */
  function group_by_date($objects, $user = null, $getter = 'getCreatedOn', $today_yesterday = true) {
    $result = array();
    if(is_foreachable($objects)) {
      require_once SMARTY_PATH . '/plugins/modifier.date.php';

      $offset = instance_of($user, 'User') ? get_user_gmt_offset($user) : 0;

      foreach($objects as $object) {
        $gmt = $object->$getter();
        
        if(instance_of($gmt, 'DateValue')) {
          $date = $gmt->advance($offset, false); // advance, but don't mutate

          if($today_yesterday) {
            if($date->isToday($offset)) {
              $date_string = lang('Today');
            } elseif($date->isYesterday($offset)) {
              $date_string = lang('Yesterday');
            } else {
              $date_string = smarty_modifier_date($date);
            } // if
          } else {
            $date_string = smarty_modifier_date($date);
          } // if

          if(!isset($result[$date_string])) {
            $result[$date_string] = array();
          } // if

          $result[$date_string][] = $object;
        } // if
      } // foreach
    } // if
    return $result;
  } // group_by_date

  /**
   * Group $objects by month they were created
   *
   * @param array $objects
   * @param string $getter
   * @return array
   */
  function group_by_month($objects, $getter = 'getCreatedOn') {
    $months = array(
      1  => lang('January'),
      2  => lang('February'),
      3  => lang('March'),
      4  => lang('April'),
      5  => lang('May'),
      6  => lang('June'),
      7  => lang('July'),
      8  => lang('August'),
      9  => lang('September'),
      10 => lang('October'),
      11 => lang('November'),
      12 => lang('December')
    );

    $result = array();
    if(is_foreachable($objects)) {
      foreach($objects as $object) {
        $date = $object->$getter();

        $month_name = $months[$date->getMonth()];

        if(instance_of($date, 'DateValue')) {
          if(!isset($result[$date->getYear()])) {
            $result[$date->getYear()] = array();
          } // if

          if(!isset($result[$date->getYear()][$month_name])) {
            $result[$date->getYear()][$month_name] = array();
          } // if

          $result[$date->getYear()][$month_name][] = $object;
        } // if
      } // foreach
    } // if
    return $result;
  } // group_by_month

  /**
   * Render iCal data
   *
   * @param string $name iCalendar name
   * @param array $objects
   * @param boolean $include_project_name
   * @return void
   */
  function render_icalendar($name, $objects, $include_project_name = false) {
  	require_once ANGIE_PATH . '/classes/icalendar/iCalCreator.class.php';

    $calendar = new vcalendar();
    //$calendar->setProperty('VERSION', '1.0');
    $calendar->setProperty('X-WR-CALNAME', $name);
    $calendar->setProperty('METHOD', 'PUBLISH');

    $projects = array();
    foreach($objects as $object) {
      $summary = $object->getName();
      if($include_project_name) {
        $project_id = $object->getProjectId();
        if(isset($projects[$project_id])) {
          $summary .= ' | ' . $projects[$project_id]->getName();
        } else {
          $project = $object->getProject();
          if(instance_of($project, 'Project')) {
            $projects[$project_id] = $project;
            $summary .= ' | ' . $projects[$project_id]->getName();
          } // if
        } // if
      } // if

    	switch(strtolower($object->getType())) {
    		case 'milestone':
    			$start_on = $object->getStartOn();
    		  $due_on   = $object->getDueOn();

    		  $due_on->advance(24 * 60 * 60, true); // One day shift because iCal and Windows Calendar don't include last day

    		  $start_on_year = $start_on->getYear();
      		$start_on_month = $start_on->getMonth() < 10 ? '0' . $start_on->getMonth() : $start_on->getMonth();
      		$start_on_day = $start_on->getDay() < 10 ? '0' . $start_on->getDay() : $start_on->getDay();

      		$due_on_year = $due_on->getYear();
      		$due_on_month = $due_on->getMonth() < 10 ? '0' . $due_on->getMonth() : $due_on->getMonth();
      		$due_on_day = $due_on->getDay() < 10 ? '0' . $due_on->getDay() : $due_on->getDay();

      		$event = new vevent();

          $event->setProperty('dtstart', array($start_on_year, $start_on_month, $start_on_day), array('VALUE'=>'DATE'));
          $event->setProperty('dtend', array($due_on_year, $due_on_month, $due_on_day), array('VALUE'=>'DATE'));

          $event->setProperty('dtstamp', date('Ymd'));
          $event->setProperty('summary', $summary);

          if($object->getBody()) {
            $event->setProperty('description', html_to_text($object->getFormattedBody()) . "\n\n" . lang('Details: ') . $object->getViewUrl());
          } else {
            $event->setProperty('description', lang('Details') . ': ' . $object->getViewUrl());
          } // if

          switch($object->getPriority()) {
      		  case PRIORITY_HIGHEST:
      		    $event->setProperty('priority', 1);
      		    break;
      		  case PRIORITY_HIGH:
      		    $event->setProperty('priority', 3);
      		    break;
      		  case PRIORITY_LOW:
      		    $event->setProperty('priority', 7);
      		    break;
      		  case PRIORITY_LOWEST:
      		    $event->setProperty('priority', 9);
      		    break;
      		  default:
      		    $event->setProperty('priority', 5);
      		} // switch

    			$calendar->addComponent($event);
    		  break;
    		case 'ticket':
    		case 'task':
    		  $start_on = $object->getCreatedOn();
    		  $due_on   = $object->getDueOn();

      		$todo = new vtodo();

      		$todo->setProperty('summary', $summary);
      		$todo->setProperty('description', $object->getName() . "\n\n" . lang('Details') . ': ' . $object->getViewUrl());

      		switch($object->getPriority()) {
      		  case PRIORITY_HIGHEST:
      		    $todo->setProperty('priority', 1);
      		    break;
      		  case PRIORITY_HIGH:
      		    $todo->setProperty('priority', 3);
      		    break;
      		  case PRIORITY_LOW:
      		    $todo->setProperty('priority', 7);
      		    break;
      		  case PRIORITY_LOWEST:
      		    $todo->setProperty('priority', 9);
      		    break;
      		  default:
      		    $todo->setProperty('priority', 5);
      		} // switch

      		if(instance_of($due_on, 'DateValue')) {
        		$due_on_year = $due_on->getYear();
        		$due_on_month = $due_on->getMonth() < 10 ? '0' . $due_on->getMonth() : $due_on->getMonth();
        		$due_on_day = $due_on->getDay() < 10 ? '0' . $due_on->getDay() : $due_on->getDay();

        		$todo->setProperty('due', $due_on_year, $due_on_month, $due_on_day);
      		} // if

          $calendar->addComponent($todo);
    		  break;
    		default:
   			  break;
    	}
    } // foreach

    $cal = $calendar->createCalendar();

    header('Content-Type: text/calendar; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $name .'.ics"');
    header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Pragma: no-cache');

    print $cal;
    die();
  } // render_icalendar

  // ---------------------------------------------------
  //  API XML exporter
  // ---------------------------------------------------

  /**
   * Return encoded XML string
   *
   * @param array $data
   * @param string $as
   * @return null
   */
  function do_xml_encode($data, $as) {
    $encoder = new XmlEncoder();
    return $encoder->encode($data, $as);
  } // do_xml_encode

  /**
   * XML encoder used by activeCollab API
   *
   * @package activeCollab.modules.system
   */
  class XmlEncoder {

    /**
     * Encode $data as XML
     *
     * @param array $data
     * @param string $as
     * @return string
     */
    function encode($data, $as = null) {
      if($as === null) {
        $as = 'items';
      } // if

      $result = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
      $result .= $this->encodeNode($data, $as);
      return $result;
    } // encode

    /**
     * Encode data node
     *
     * @param mixed $data
     * @param string $as
     * @return string
     */
    function encodeNode($data, $as) {
      $result = "<$as>";

      if(is_foreachable($data)) {
        $has_numeric = false;
        foreach($data as $k => $v) {
          if(is_numeric($k)) {
            $has_numeric = true;
            break;
          } // if
        } // if

        $singular = null;
        if($has_numeric) {
          $singular = Inflector::singularize($as);
        } // if

        foreach($data as $k => $v) {
          if(is_numeric($k)) {
            $k = $singular;
          } // if
          $result .= $this->encodeNode($v, $k);
        } // if
      } else {
        if(is_int($data) || is_float($data)) {
          $result .= $data;
        } elseif(is_array($data)) {
          $result .= '';
        } elseif(instance_of($data, 'DateValue')) {
          $result .= $data->toMySQL();
        } elseif(is_null($data)) {
          $result .= '';
        } elseif(is_bool($data)) {
          $result .= $data ? '1' : '0';
        } else {
          $result .= '<![CDATA[' . $data . "]]>";
        } // if
      } // if

      return $result . "</$as>\n";
    } // encodeNode

  } // XmlEncoder

  // ---------------------------------------------------
  //  Modules
  // ---------------------------------------------------

  /**
   * Returns true if module $name is installed and loaded
   *
   * @param string $name
   * @return boolean
   */
  function module_loaded($name) {
    static $cache = array();
    if(!isset($cache[$name])) {
      $cache[$name] = (boolean) Modules::count(array('name = ?', $name));
    } // if

    return $cache[$name];
  } // module_loaded

  // ---------------------------------------------------
  //  Search index
  // ---------------------------------------------------

  /**
   * Search through search index
   *
   * @param string $search_for
   * @param string $type
   * @param User $user
   * @param integer $page
   * @param integer $per_page
   * @return array
   */
  function search_index_search($search_for, $type, $user, $page = 1, $per_page = 30) {
  	return call_user_func_array(array(SEARCH_ENGINE, 'search'), array($search_for, $type, $user, $page, $per_page));
  } // search_index_search

  /**
   * Set value in search index
   *
   * @param integer $object_id
   * @param string $type
   * @param string $content
   * @param array $attributes
   * @return boolean
   */
  function search_index_set($object_id, $type, $content, $attributes = null) {
    return call_user_func_array(array(SEARCH_ENGINE, 'update'), array($object_id, $type, $content, $attributes));
  } // search_index_set

  /**
   * Remove object from search instance
   *
   * @param integer $object_id
   * @param string $type
   * @return boolean
   */
  function search_index_remove($object_id, $type) {
    return call_user_func_array(array(SEARCH_ENGINE, 'remove'), array($object_id, $type));
  } // search_index_remove

  /**
   * Returns true if object of a given type exists in search index
   *
   * @param integer $object_id
   * @param string $type
   * @return boolean
   */
  function search_index_has($object_id, $type) {
    return call_user_func_array(array(SEARCH_ENGINE, 'hasObject'), array($object_id, $type));
  } // search_index_has

?>