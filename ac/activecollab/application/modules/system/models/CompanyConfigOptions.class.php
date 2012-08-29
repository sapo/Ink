<?php

  /**
   * Company options manager
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class CompanyConfigOptions {
  
    /**
     * Return value of $name config option for a given company
     *
     * @param string $name
     * @param Company $company
     * @return mixed
     */
    function getValue($name, $company) {
      $cache_id = 'company_config_options_' . $company->getId();
      
      $cached_value = cache_get($cache_id);
      if(is_array($cached_value) && isset($cached_value[$name])) {
        return $cached_value[$name];
      } // if
      
      $option = ConfigOptions::findByName($name, COMPANY_CONFIG_OPTION);
      if(instance_of($option, 'ConfigOption')) {
        $record = db_execute_one('SELECT value FROM ' . TABLE_PREFIX . 'company_config_options WHERE company_id = ? AND name = ?', $company->getId(), $name);
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
        return new InvalidParamError('name', $name, "Company configuration option '$name' does not exist", true);
      } // if
    } // getValue
    
    /**
     * Return associative array with config option values by name and given 
     * company
     *
     * @param Company $company
     * @return array
     */
    function getValues($names, $company) {
      $result = array();
      
      // lets get option definition instances
      $options = ConfigOptions::findByNames($names, COMPANY_CONFIG_OPTION);
      
      if(is_foreachable($options)) {
        
        // Now we need all company specific values we can get
        $values = db_execute_all('SELECT name, value FROM ' . TABLE_PREFIX . 'company_config_options WHERE name IN (?) AND company_id = ?', $names, $company->getId());
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
     * Set value of $name config option for a given company
     *
     * @param string $name
     * @param mixed $value
     * @param Company $company
     * @return mixed
     */
    function setValue($name, $value, $company) {
      $option = ConfigOptions::findByName($name, COMPANY_CONFIG_OPTION);
      if(instance_of($option, 'ConfigOption')) {
        $table = TABLE_PREFIX . 'company_config_options';
        
        $count = db_execute_one("SELECT COUNT(*) AS 'row_num' FROM $table WHERE company_id = ? AND name = ?", $company->getId(), $name);
        if(isset($count) && $count['row_num'] > 0) {
          $result = db_execute("UPDATE $table SET value = ? WHERE company_id = ? AND name = ?", serialize($value), $company->getId(), $name);
        } else {
          $result = db_execute("INSERT INTO $table (company_id, name, value) VALUES (?, ?, ?)", $company->getId(), $name, serialize($value));
        } // if
        
        if($result && !is_error($result)) {
          $cache_id = 'company_config_options_' . $company->getId();
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
        return new InvalidParamError('name', $name, "Company configuration option '$name' does not exist", true);
      } // if
    } // setValue
    
    /**
     * Remove specific value
     *
     * @param string $name
     * @param Company $company
     * @return boolean
     */
    function removeValue($name, $company) {
      $cache_id = 'company_config_options_' . $company->getId();
      $cached_values = cache_get($cache_id);
      
      if(is_array($cached_values) && isset($cached_values[$name])) {
        unset($cached_values[$name]);
        cache_set($cache_id, $cached_values);
      } // if
      
      return db_execute('DELETE FROM ' . TABLE_PREFIX . 'company_config_options WHERE company_id = ? AND name = ?', $company->getId(), $name);
    } // removeValue
    
    /**
     * Delete all values by config option name
     *
     * @param string $name
     * @return boolean
     */
    function deleteByOption($name) {
      return db_execute('DELETE FROM ' . TABLE_PREFIX . 'company_config_options WHERE name = ?', $name);
    } // deleteByOption
  
  } // CompanyConfigOptions

?>