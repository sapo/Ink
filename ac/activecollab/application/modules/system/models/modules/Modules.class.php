<?php

  /**
   * Modules class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Modules extends BaseModules {
    
    /**
     * Return array of module names
     *
     * @param void
     * @return array
     */
    function findNames() {
      $names = array();
      
      $rows = db_execute_all('SELECT name FROM ' . TABLE_PREFIX . 'modules ORDER BY is_system DESC, position');
      if(is_foreachable($rows)) {
        foreach($rows as $row) {
          $names[] = $row['name'];
        } // foreach
      } // if
      
      return $names;
    } // findNames
    
    /**
     * Return all modules that are installed
     *
     * @param void
     * @return array
     */
    function findAll() {
      $conditions = null;
      if(LICENSE_PACKAGE == 'smallbiz') {
        $conditions = array('name NOT IN (?)', array('tickets', 'timetracking', 'calendar', 'pages', 'project_exporter', 'status', 'invoicing', 'source'));
      } // if
      
      $modules = Modules::find(array( 
        'conditions' => $conditions, 
        'order' => 'is_system DESC, position', 
      ));
      
      return $modules;
    } // findAll
    
    /**
     * Return array of names of modules that are avilable, but not yet installed
     *
     * @param void
     * @return array
     */
    function findNotInstalled() {
      $names = Modules::findNames(); // names of installed modules
      
      $modules_path = APPLICATION_PATH . '/modules';
      $d = dir($modules_path);
      
      if($d) {
        $result = array();
    		while(($entry = $d->read()) !== false) {
    		  if(str_starts_with($entry, '.') || !is_dir(APPLICATION_PATH . '/modules/' . $entry)) {
    		    continue;
    		  } // if
    		  
    		  if(!in_array($entry, $names)) {
    		    $module_class = Inflector::camelize($entry) . 'Module';
    		    require_once "$modules_path/$entry/$module_class.class.php";
    		    
    		    $result[] = new $module_class();
    		  } // if
    		} // while
    		
    		return $result;
      } // if
      
  		return null;
    } // findNotInstalled
    
    // ---------------------------------------------------
    //  Override BaseProjectObjects methods
    // ---------------------------------------------------
    
    /**
     * Do a SELECT query over database with specified arguments
     * 
     * This function can return single instance or array of instances that match 
     * requirements provided in $arguments associative array
     *
     * @param array $arguments
     * @return mixed
     * @throws DBQueryError
     */
    function find($arguments = null) {
      return Modules::findBySQL(DataManager::prepareSelectFromArguments($arguments, TABLE_PREFIX . 'modules'), null, array_var($arguments, 'one'));
    } // find
    
    /**
     * Return paginated set of modules
     *
     * @param array $arguments
     * @param itneger $page
     * @param integer $per_page
     * @return array
     */
    function paginate($arguments = null, $page = 1, $per_page = 10) {
      if(!is_array($arguments)) {
        $arguments = array();
      } // if
      
      $arguments['limit'] = $per_page;
      $arguments['offset'] = ($page - 1) * $per_page;
      
      $modules = Modules::findBySQL(DataManager::prepareSelectFromArguments($arguments, TABLE_PREFIX . 'modules'), null, array_var($arguments, 'one'));
      $total_modules = Modules::count(array_var($arguments, 'conditions'));
      
      return array(
        $modules,
        new Pager($page, $total_modules, $per_page)
      );
    } // paginate
    
    /**
     * Return object of a specific class by SQL
     *
     * @param string $sql
     * @param array $arguments
     * @param boolean $one
     * @param string $table_name
     * @return array
     */
    function findBySQL($sql, $arguments = null, $one = false) {
      if($arguments !== null) {
        $sql = db_prepare_string($sql, $arguments);
      } // if
      
      $rows = db_execute_all($sql);
      
      if(is_error($rows)) {
        return $rows;
      } // if
      
      if(!is_foreachable($rows)) {
        return null;
      } // if
      
      if($one) {
        $row = $rows[0];
        
        $module_name = array_var($row, 'name');
        $module_class = Inflector::camelize($module_name) . 'Module';
        require_once APPLICATION_PATH . "/modules/$module_name/$module_class.class.php";
        
        $module = new $module_class();
        $module->loadFromRow($row);
        return $module;
      } else {
        $modules = array();
        
        foreach($rows as $row) {
          $module_name = array_var($row, 'name');
          $module_class = Inflector::camelize($module_name) . 'Module';
          require_once APPLICATION_PATH . "/modules/$module_name/$module_class.class.php";
          
          $module = new $module_class();
          $module->loadFromRow($row);
          $modules[] = $module;
        } // foreach
        
        return count($module) ? $modules : null;
      } // if
    } // findBySQL
  
    /**
     * Find and return a specific  module by name
     *
     * @param string $name
     * @return Module
     */
    function findById($name) {
      if(empty($name)) {
        return null;
      } // if
      
      $module_class = Inflector::camelize($name) . 'Module';
      require_once APPLICATION_PATH . "/modules/$name/$module_class.class.php";
      
      $cache_id = TABLE_PREFIX . 'modules_name_' . $name;
      $row = cache_get($cache_id);
      
      if($row) {
        $module = new $module_class();
        $module->loadFromRow($row);
        
        return $module;
      } else {
        $row = db_execute_one("SELECT * FROM " . TABLE_PREFIX . "modules WHERE name = ? LIMIT 0, 1", $name);
      
        if(is_array($row)) {
          $module = new $module_class();
          $module->loadFromRow($row);
          
          return $module;
        } // if
      } // if
      
      return null;
    } // findById
  
  }

?>