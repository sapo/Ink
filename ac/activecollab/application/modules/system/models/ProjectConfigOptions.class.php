<?php

  /**
  * Configuration option manager
  *
  * This class represents the whole model for working with project level 
  * configuration options
  * 
  * @author Ilija Studen <ilija.studen@gmail.com>
  */
  class ProjectConfigOptions {
  
    /**
    * Return value of $name config option for a given project
    *
    * @param string $name
    * @param Project $project
    * @return mixed
    */
    function getValue($name, $project) {
      $cache_id = 'project_config_options_' . $project->getId();
      
      $cached_value = cache_get($cache_id);
      if(is_array($cached_value) && isset($cached_value[$name])) {
        return $cached_value[$name];
      } // if
      
      $option = ConfigOptions::findByName($name, PROJECT_CONFIG_OPTION);
      if(instance_of($option, 'ConfigOption')) {
        $record = db_execute_one('SELECT value FROM ' . TABLE_PREFIX . 'project_config_options WHERE project_id = ? AND name = ?', $project->getId(), $name);
        if(is_array($record) && isset($record['value'])) {
          $value = trim($record['value']) != '' ? unserialize($record['value']) : null;
        } else {
          $value = $option->getValue();
        } // if
        
        if(is_array($cached_value)) {
          $cached_value[$name] = $value;
        } else {
          $cached_value = array($name => $value);
        } // if
        
        cache_set($cache_id, $cached_value);
        
        return $value;
      } else {
        return new InvalidParamError('name', $name, "Project configuration option '$name' does not exist", true);
      } // if
    } // getValue
    
    /**
    * Set value of $name config option for a given project
    *
    * @param string $name
    * @param mixed $value
    * @param Project $project
    * @return mixed
    */
    function setValue($name, $value, $project) {
      $option = ConfigOptions::findByName($name, PROJECT_CONFIG_OPTION);
      if(instance_of($option, 'ConfigOption')) {
        $table = TABLE_PREFIX . 'project_config_options';
        
        $count = db_execute_one("SELECT COUNT(*) AS 'row_num' FROM $table WHERE project_id = ? AND name = ?", $project->getId(), $name);
        if(isset($count) && $count['row_num'] > 0) {
          $result = db_execute("UPDATE $table SET value = ? WHERE project_id = ? AND name = ?", serialize($value), $project->getId(), $name);
        } else {
          $result = db_execute("INSERT INTO $table (project_id, name, value) VALUES (?, ?, ?)", $project->getId(), $name, serialize($value));
        } // if
        
        return $result && !is_error($result) ? $value : $result;
      } else {
        return new InvalidParamError('name', $name, "Project configuration option '$name' does not exist", true);
      } // if
    } // setValue
    
    /**
    * Remove specific value
    *
    * @param string $name
    * @param Project $project
    * @return boolean
    */
    function removeValue($name, $project) {
      return db_execute('DELETE FROM ' . TABLE_PREFIX . 'project_config_options WHERE project_id = ? AND name = ?', $project->getId(), $name);
    } // removeValue
    
    /**
     * Delete all values by config option name
     *
     * @param string $name
     * @return boolean
     */
    function deleteByOption($name) {
      return db_execute('DELETE FROM ' . TABLE_PREFIX . 'project_config_options WHERE name = ?', $name);
    } // deleteByOption
  
  } // ProjectConfigOptions

?>