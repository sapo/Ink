<?php

  /**
   * User level configuration option manager
   *
   * This class represents the whole model for working with user level 
   * configuration options
   */
  class UserConfigOptions {
  
    /**
     * Return value of $name config option for a given user
     *
     * @param string $name
     * @param User $user
     * @return mixed
     */
    function getValue($name, $user) {
      $cache_id = 'user_config_options_' . $user->getId();
      
      $cached_value = cache_get($cache_id);
      if(is_array($cached_value) && isset($cached_value[$name])) {
        return $cached_value[$name];
      } // if
      
      $option = ConfigOptions::findByName($name, USER_CONFIG_OPTION);
      if(instance_of($option, 'ConfigOption')) {
        $record = db_execute_one('SELECT value FROM ' . TABLE_PREFIX . 'user_config_options WHERE user_id = ? AND name = ?', $user->getId(), $name);
        if(is_array($record) && isset($record['value'])) {
          $value = trim($record['value']) ? unserialize($record['value']) : null;
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
        return new InvalidParamError('name', $name, "User configuration option '$name' does not exist", true);
      } // if
    } // getValue
    
    /**
     * Return associative array with config option values by name and given user
     *
     * @param User $user
     * @return array
     */
    function getValues($names, $user) {
      $result = array();
      
      // lets get option definition instances
      $options = ConfigOptions::findByNames($names, USER_CONFIG_OPTION);
      
      if(is_foreachable($options)) {
        
        // Now we need all user specific values we can get
        $values = db_execute_all('SELECT name, value FROM ' . TABLE_PREFIX . 'user_config_options WHERE name IN (?) AND user_id = ?', $names, $user->getId());
        $foreachable = is_foreachable($values);
        
        // Populate result
        foreach($options as $name => $option) {
          if($foreachable) {
            foreach($values as $record) {
              if($record['name'] == $name) {
                $result[$name] = trim($record['value']) != '' ? unserialize($record['value']) : null;
                break;
              } // if
            } // foreach
          } // if
          
          if(!isset($result[$name])) {
            $result[$name] = $option->getValue();
          } // if
        } // foreach
      } // if
      
      return $result;
    } // getValues
    
    /**
     * Set value of $name config option for a given user
     *
     * @param string $name
     * @param mixed $value
     * @param User $user
     * @return mixed
     */
    function setValue($name, $value, $user) {
      $option = ConfigOptions::findByName($name, USER_CONFIG_OPTION);
      if(instance_of($option, 'ConfigOption')) {
        $table = TABLE_PREFIX . 'user_config_options';
        
        $count = db_execute_one("SELECT COUNT(*) AS 'row_num' FROM $table WHERE user_id = ? AND name = ?", $user->getId(), $name);
        if(isset($count) && $count['row_num'] > 0) {
          $result = db_execute("UPDATE $table SET value = ? WHERE user_id = ? AND name = ?", serialize($value), $user->getId(), $name);
        } else {
          $result = db_execute("INSERT INTO $table (user_id, name, value) VALUES (?, ?, ?)", $user->getId(), $name, serialize($value));
        } // if
        
        if($result && !is_error($result)) {
          $cache_id = 'user_config_options_' . $user->getId();
          $cached_values = cache_get($cache_id);
          
          if(is_array($cached_values)) {
            $cached_values[$name] = $value;
          } else {
            $cached_values = array($name => $value);
          } // if
          
          cache_set($cache_id, $cached_values);
          return $value;
        } else {
          return $result;
        } // if
      } else {
        return new InvalidParamError('name', $name, "User configuration option '$name' does not exist", true);
      } // if
    } // setValue
    
    /**
     * Remove specific value
     *
     * @param string $name
     * @param User $user
     * @return boolean
     */
    function removeValue($name, $user) {
      $cache_id = 'user_config_options_' . $user->getId();
      $cached_values = cache_get($cache_id);
      
      if(is_array($cached_values) && isset($cached_values[$name])) {
        unset($cached_values[$name]);
        cache_set($cache_id, $cached_values);
      } // if
      
      return db_execute('DELETE FROM ' . TABLE_PREFIX . 'user_config_options WHERE user_id = ? AND name = ?', $user->getId(), $name);
    } // removeValue
    
    /**
     * Returns true if there is a value set for this user
     *
     * @param string $name
     * @param User $user
     * @return boolean
     */
    function hasValue($name, $user) {
    	return (boolean) array_var(db_execute_one("SELECT COUNT(*) AS 'row_count' FROM " . TABLE_PREFIX . 'user_config_options WHERE user_id = ? AND name = ?', $user->getId(), $name), 'row_count');
    } // hasValue
    
    /**
     * Return number of users who have $name config option set to $value
     *
     * @param string $name
     * @param mixed $value
     * @param array $excude_ids
     * @return integer
     */
    function countByValue($name, $value, $excude_ids = null) {
      if($excude_ids && !is_array($excude_ids)) {
        $excude_ids = array($excude_ids);
      } // if
      
      $rows = db_execute_all('SELECT user_id, value FROM ' . TABLE_PREFIX . 'user_config_options WHERE name = ?', $name);
      if(is_foreachable($rows)) {
        $user_ids = array();
        foreach($rows as $row) {
          if(unserialize($row['value']) == $value) {
            if($excude_ids) {
              if(in_array($row['user_id'], $excude_ids)) {
                continue;
              } // if
            } // if
            $user_ids[] = (integer) $row['user_id'];
          } // if
        } // foreach
        
        if(is_foreachable($user_ids)) {
          return array_var(db_execute_one("SELECT COUNT(id) AS 'row_count' FROM " . TABLE_PREFIX . 'users WHERE id IN (?)', $user_ids), 'row_count');
        } // if
      } // if
      
      return 0;
    } // countByValue
    
    /**
     * Delete all values by config option name
     *
     * @param string $name
     * @return boolean
     */
    function deleteByOption($name) {
      $result = db_execute_all('SELECT user_id FROM ' . TABLE_PREFIX . 'user_config_options WHERE name = ?', $name);
      if(is_foreachable($result)) {
        foreach($result as $row) {
          cache_remove('user_config_options_' . $row['user_id']);
        } // foreach
      } // if
      
      return db_execute('DELETE FROM ' . TABLE_PREFIX . 'user_config_options WHERE name = ?', $name);
    } // deleteByOption
    
    /**
     * Delete values by user
     *
     * @param User $user
     * @return boolean
     */
    function deleteByUser($user) {
      return db_execute('DELETE FROM ' . TABLE_PREFIX . 'user_config_options WHERE user_id = ?', $user->getId());
    } // deleteByUser
  
  } // UserConfigOptions

?>