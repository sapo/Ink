<?php

  /**
   * ConfigOptions class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ConfigOptions extends BaseConfigOptions {
    
    // ---------------------------------------------------
    //  Utility methods
    // ---------------------------------------------------
  
    /**
     * Return config option value
     *
     * @param string $name
     * @return mixed
     */
    function getValue($name) {
      $cached_values = cache_get('config_values');
      if(is_array($cached_values) && isset($cached_values[$name])) {
        return $cached_values[$name];
      } // if
      
      $option = ConfigOptions::findByName($name);
      if(instance_of($option, 'ConfigOption')) {
        $value = $option->getValue();
        
        if(is_array($cached_values)) {
          $cached_values[$name] = $value;
        } else {
          $cached_values = array($name => $value);
        } // if
        
        cache_set('config_values', $cached_values);
        return $value;
      } else {
        return new InvalidParamError('name', $name, "System configuration option '$name' does not exist", true);
      } // if
    } // getValue
    
    /**
     * Set config option value
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    function setValue($name, $value) {
      $option = ConfigOptions::findByName($name);
      if(instance_of($option, 'ConfigOption')) {
        $option->setValue($value);
        
        $save = $option->save();
        if($save && !is_error($save)) {
          $cached_values = cache_get('config_values');
          if(is_array($cached_values)) {
            $cached_values[$name] = $value;
          } else {
            $cached_values = array($name => $value);
          } // if
          
          cache_set('config_values', $cached_values);
          cache_remove(TABLE_PREFIX . 'config_options_name_' . $name);
          
          return $value;
        } else {
          return $save;
        } // if
      } else {
        return new InvalidParamError('name', $name, "System configuration option '$name' does not exist", true);
      } // if
    } // setValue
    
    // ---------------------------------------------------
    //  Finder
    // ---------------------------------------------------
    
    /**
     * Return config option by name
     * 
     * Set $type to filter name agains specific type (config options are used to 
     * store three types of options and this param is used as an error prevention 
     * to force speicfic option type)
     *
     * @param string $name
     * @param string $type
     * @return ConfigOption
     */
    function findByName($name, $type = null) {
      if($type === null) {
        $conditions = array('name = ?', $name);
      } else {
        $conditions = array('name = ? AND type = ?', $name, $type);
      } // if
      
      return ConfigOptions::find(array(
        'conditions' => $conditions,
        'one' => true,
      ));
    } // findByName
    
    /**
     * Return config options by names
     *
     * @param Array $names
     * @param string $type
     * @return ConfigOptions
     */
    function findByNames($names, $type = null) {
    	if(is_array($names)) {
    	  if($type === null) {
          $conditions = array('name IN (?)', $names);
        } else {
          $conditions = array('name IN (?) AND type = ?', $names, $type);
        } // if
    	  
        $options = ConfigOptions::find(array(
          'conditions' => $conditions,
        ));
        
        $results = array();
        if(is_foreachable($options)) {
        	foreach($options as $option) {
            $results[$option->getName()] = $option;		
        	} // foreach
        } // if
        
        return $results;
    	} else {
    		return null;
    	}
    } // findByNames
    
    /**
     * Delete option from database
     *
     * @param string $name
     * @return boolean
     */
    function removeOption($name) {
    	$delete = ConfigOptions::delete(array('name = ?', $name));
    	if($delete && !is_error($delete)) {
    	  cache_remove(TABLE_PREFIX . 'config_options_name_' . $name);
    	} // if
    	return $delete;
    } // removeOption
    
    /**
     * Delete config options by module
     *
     * @param string $name
     * @return boolean
     */
    function deleteByModule($name) {
      $options = ConfigOptions::find(array(
        'conditions' => array('module = ?', $name),
      ));
      
      if(is_foreachable($options)) {
        foreach($options as $option) {
          $option->delete();
        } // foreach
      } // if
    } // deleteByModule
    
  }

?>