<?php

  /**
   * Data object class
   *
   * This class enables easy implementation of any object that is based
   * on single database row. It enables reading, updating, inserting and 
   * deleting that row without writing any SQL. Also, it can chack if 
   * specific row exists in database.
   * 
   * This class supports PKs over multiple fields
   * 
   * @package angie.library.database
   */
  class DataObject extends AngieObject {
    
    /**
     * Name of the table
     *
     * @var string
     */
    var $table_name;
  
  	/**
     * Indicates if this is new object (not saved)
     *
     * @var boolean
     */
  	var $is_new = true;
  	
  	/**
     * This flag is set to true when data from row are inserted into fields
     *
     * @var boolean
     */
  	var $is_loading = false;
  	
  	/**
     * Array of field names
     *
     * @var array
     */
  	var $fields;
  	
  	/**
     * Field map let us use special field names to point to existing fields. For 
     * instance, we can set that started_on maps to date_field_1 and it will do 
     * that automatically in getter and setters functions. 
     * 
     * $field_map = array(
     *   'started_on' => 'date_field_1'
     * )
     *
     * @var array
     */
  	var $field_map = null;
  	
  	/**
     * Array of PK fields
     *
     * @var array
     */
  	var $primary_key = array();
  	
  	/**
     * Name of autoincrement field (if exists)
     *
     * @var string
     */
  	var $auto_increment = null;
  	
  	/**
     * Field values
     *
     * @var array
     */
  	var $values = array();
  	
  	/**
     * Array of modified field values
     * 
     * Elements of this array are populated on setter call. Real name is 
     * resolved, old value is saved here (if exists) and new one is set. Keys 
     * used in this array are real field names only!
     *
     * @var array
     */
  	var $old_values = array();
  	
  	/**
     * Array of modified fiels
     *
     * @var array
     */
  	var $modified_fields = array();
  	
  	/**
     * Primary key is updated
     *
     * @var boolean
     */
  	var $primary_key_updated = false;
  	
  	/**
     * List of protected fields (can't be set using setAttributes() method)
     *
     * @var array
     */
  	var $protect = null;
  	
  	/**
     * List of accepted fields
     *
     * @var array
     */
  	var $accept = null;
  	
  	/**
     * Construct data object and if $id is present load
     *
     * @param mixed $id
     * @return void
     */
  	function __construct($id = null) {
  	  $this->is_new = true; // new if not loaded
  	  if($id !== null) {
  	    $this->load($id);
  	  } // if
  	} // __construct
  	
  	/**
     * Validate object properties before object is saved 
     * 
     * This method is called before the item is saved and can be used to fetch 
     * errors in data before we really save it database. $errors is instance of 
     * ValidationErrors class that is used for error collection. If collection 
     * is empty object is considered valid and save process will continue
     *
     * @param ValidationErrors $errors
     * @return null
     */
  	function validate(&$errors) {
  	  
  	} // validate
  	
  	/**
     * Return object attributes
     * 
     * This function will return array of attribute name -> attribute value pairs 
     * for this specific project. By default it is array of object fields but 
     * returned list can be extended by implementing getAdditionalAttributes() 
     * method or redefining this one
     *
     * @param void
     * @return array
     */
  	function getAttributes() {
  	  $field_values = array();
  	  foreach($this->fields as $field) {
  	    $field_values[$field] = $this->getFieldValue($field);
  	  } // foreach
  	  
  	  $additional = $this->getAdditionalAttributes();
  	  if(is_array($additional)) {
  	    return array_merge($field_values, $additional);
  	  } else {
  	    return $field_values;
  	  } // if
  	} // getAttributes
  	
  	/**
     * Return array of additional attributes
     *
     * @param void
     * @return array
     */
  	function getAdditionalAttributes() {
  	  return array();
  	} // getAdditionalAttributes
  	
  	/**
     * Set object attributes / properties. This function will take hash and set 
     * value of all fields that she finds in the hash
     *
     * @param array $attributes
     * @return null
     */
  	function setAttributes($attributes) {
  	  if(is_array($attributes)) {
  	    foreach($attributes as $k => $v) {
  	      if(is_array($this->protect) && (in_array($k, $this->protect) || in_array($k, $this->protect))) {
  	        continue; // field is in list of protected fields
  	      } // if
  	      if(is_array($this->accept) && !(in_array($k, $this->accept) || in_array($k, $this->protect))) {
  	        continue; // not in list of acceptable fields
  	      } // if
  	      if($this->fieldExists($k)) {
  	        $this->setFieldValue($k, $attributes[$k]);
  	      } // if
  	    } // foreach
  	  } // if
  	} // setAttributes
  	
  	/**
     * Return primary key columns
     *
     * @param void
     * @return array
     */
  	function getPrimaryKey() {
  	  return $this->primary_key;
  	} // getPrimaryKey
  	
  	/**
     * Return value of primary key
     *
     * @param void
     * @return array
     */
  	function getPrimaryKeyValue() {
  		$pks = $this->getPrimaryKey();
  		if(!is_foreachable($pks)) {
  		  return null; // no PK for this object
  		} // if
  		
			$ret = array();
			foreach($pks as $pk) {
			  $ret[$pk] = $this->getFieldValue($pk);
			} // if
			return count($ret) > 1 ? $ret : $ret[$pks[0]];
  	} // getPrimaryKeyValue
  	
  	/**
     * Return value of table name
     *
     * @param null
     * @return string
     */
  	function getTableName() {
  	  return $this->table_name;
  	} // getTableName
  	
  	// ---------------------------------------------------
  	//  CRUD methods
  	// ---------------------------------------------------
  	
  	/**
     * Load object by specific ID
     *
     * @param mixed $id
     * @return boolean
     */
  	function load($id) {
  	  $sql = "SELECT " . implode(', ', $this->fields) . " FROM " . $this->getTableName() . " WHERE " . $this->getWherePartById($id) . " LIMIT 0, 1";
  	  $row = db_execute_one($sql);
  	  
  	  if(is_array($row)) {
  	    return $this->loadFromRow($row);
  	  } else {
  	    return false;
  	  } // if
  	} // load
  	
  	/**
     * Load data from database row
     * 
     * If $cache_row is set to true row data will be added to cache
     *
     * @param array $row
     * @param boolean $cache_row
     * @return boolean
     */
  	function loadFromRow($row, $cache_row = false) {
  	  $this->is_loading = true;
  	  if(is_array($row)) {
  	    foreach($row as $k => $v) {
  	      if($this->fieldExists($k)) {
  	        $this->setFieldValue($k, $v);
  	      } // if
  	    } // foreach
  	    
  	    if($cache_row) {
  	      cache_set($this->getCacheId(), $row);
  	    } // if
  	    
  	    $this->setLoaded(true);
  	    $this->is_loading = false;
  	    
  	    return true;
  	  } // if
  	  
  	  $this->is_loading = false;
  	  return false;
  	} // loadFromRow
  	
  	/**
     * Save object into database (insert or update)
     * 
     * If this object does not pass validation error object with all model errors 
     * will be returned (object of ValidationErrors class)
     *
     * @param void
     * @return boolean
     * @throws DBQueryError
     * @throws ValidationErrors
     */
  	function save() {
  	  event_trigger('on_before_object_validation', array(
  	    'object' => &$this,
  	  ));
  	  $errors = new ValidationErrors();
  	  $this->validate($errors);
  	  
  	  event_trigger('on_after_object_validation', array(
  	    'object' => &$this,
  	    'errors' => &$errors,
  	  ));
  	  
  	  // Invalid?
  	  if($errors->hasErrors()) {
  	    return $errors;
  	  } else {
  	    
  	    // Before save...
  	    event_trigger('on_before_object_save', array(
  	      'object' => &$this,
  	    ));
  	    
  	    $save = $this->doSave();
  	    if($save && !is_error($save)) {
  	      event_trigger('on_after_object_save', array('object' => &$this));
  	      cache_remove($this->getCacheId());
  	    } // if
  	    
  	    return $save;
  	  } // if
  	} // save
  	
  	/**
     * Delete specific object (and related objects if neccecery)
     *
     * @param void
     * @return boolean
     */
  	function delete() {
  		if($this->isNew()) {
  		  return false;
  		} // if
  		
  		$cache_id = $this->getCacheId();
  		
  		event_trigger('on_before_object_deleted', array('object' => &$this));
  		
  		$delete = $this->doDelete();
  		if($delete && !is_error($delete)) {
  		  $this->setNew(true);;
  		  $this->setLoaded(false);
  		  
  		  cache_remove($cache_id);
		    event_trigger('on_object_deleted', array('object' => &$this));
  		} // if
  		
  		return $delete;
  	} // delete
  	
  	// ---------------------------------------------------
  	//  Flags
  	// ---------------------------------------------------
  	
  	/**
     * Return value of $is_new variable
     *
     * @param void
     * @return boolean
     */
  	function isNew() {
  	  return (boolean) $this->is_new;
  	} // isNew
  	
  	/**
     * Set new stamp value
     *
     * @param boolean $value New value
     * @return void
     */
  	function setNew($value) {
  	  $this->is_new = (boolean) $value;
  	} // setNew
  	
  	/**
     * Returns true if this object have row in database
     *
     * @param void
     * @return boolean
     */
  	function isLoaded() {
  	  return !$this->is_new;
  	} // isLoaded
  	
  	/**
     * Set loaded stamp value
     *
     * @param boolean $value New value
     * @return void
     */
  	function setLoaded($value) {
  	  $this->is_new = !$value;
  	} // setLoaded
  	
  	/**
     * Return real field name
     * 
     * This function will return real field name. It will check if we have $field 
     * in field name map or in fields list and return appropriate value
     *
     * @param string $field
     * @return string
     */
  	function realFieldName($field) {
  	  return is_array($this->field_map) && isset($this->field_map[$field]) ? $this->field_map[$field] : $field;
  	} // realFieldName
  	
  	/**
     * Check if specific key is defined
     *
     * @param string $field Field name
     * @return boolean
     */
  	function fieldExists($field) {
  	  return in_array($this->realFieldName($field), $this->fields);
  	} // fieldExists
  	
  	/**
     * Check if this object has modified columns
     *
     * @param void
     * @return boolean
     */
  	function isModified() { 
  	  return (boolean) count($this->modified_fields);
  	} // isModified
  	
  	/**
     * Returns true if specific field is modified
     *
     * @param string $field
     * @return boolean
     */
  	function isModifiedField($field) {
  	  return in_array($this->realFieldName($field), $this->modified_fields);
  	} // isModifiedField
  	
  	/**
     * Check if selected field is primary key
     *
     * @param string $field Field that need to be checked
     * @return boolean
     */
  	function isPrimaryKey($field) {
  	  return in_array($this->realFieldName($field), $this->primary_key);
  	} // isPrimaryKey
  	
  	/**
     * Return value of specific field and typecast it...
     *
     * @param string $field Field value
     * @param midex $default Default value that is returned in case of any error
     * @return mixed
     */
  	function getFieldValue($field, $default = null) {
  	  return array_var($this->values, $this->realFieldName($field), $default);
  	} // getFieldValue
  	
  	/**
  	 * Return all field value
  	 *
  	 * @param string $field
  	 * @return mixed
  	 */
  	function getOldFieldValue($field) {
  	  $real_field_name = $this->realFieldName($field);
  	  return isset($this->old_values[$real_field_name]) ? $this->old_values[$real_field_name] : null;
  	} // getOldFieldValue
  	
  	/**
     * Set specific field value
     * 
     * Set value of the $field. This function will make sure that everything runs 
     * fine - modifications are saved, in case of primary key old value will be 
     * remembered in case we need to update the row and so on
     *
     * @param string $field Field name
     * @param mixed $value New field value
     * @return boolean
     */
  	function setFieldValue($field, $value) {
  	  $real_field_name = $this->realFieldName($field);
  	  
  		if(!$this->fieldExists($real_field_name)) {
  		  return new InvalidParamError('field', $field, "Field '$field' (mapped with '$real_field_name') does not exist");
  		} // if
  		
  		if(!isset($this->values[$real_field_name]) || ($this->values[$real_field_name] != $value)) {
  		  
  		  // If we are loading object there is no need to remember if this field 
  		  // was modified, if PK has been updated and old value. We just skip that
  		  if(!$this->is_loading) {
  		    
  		    // Remember old value
    		  if(isset($this->values[$real_field_name])) {
    		    $old_value = $this->values[$real_field_name];
    		  } // if
  		  
    		  // Save primary key value. Also make sure that only the first PK value is
  			  // saved as old. Not to save second value on third modification ;)
  			  if($this->isPrimaryKey($real_field_name) && !isset($this->primary_key_updated[$real_field_name])) {
  			    if(!is_array($this->primary_key_updated)) {
  			      $this->primary_key_updated = array();
  			    } // if
  			    $this->primary_key_updated[$real_field_name] = true;
  			  } // if
  			  
  			  // Save old value if we haven't done that already
  			  if(isset($old_value) && !isset($this->old_values[$real_field_name])) {
  			    $this->old_values[$real_field_name] = $old_value;
  			  } // if
    		  
  			  // Remember that this file was modified
    		  $this->addModifiedField($real_field_name);
  		  } // if
  		  
			  $this->values[$real_field_name] = $value;
			  return $value;
  		} else {
  		  return $value;
  		} // if
  	} // setFieldValue
  	
  	/**
     * Add new modified field
     *
     * @param string $field Field that need to be added
     * @return void
     */
  	function addModifiedField($field) {
  	  if(!in_array($field, $this->modified_fields)) {
  	    $this->modified_fields[] = $field;
  	  } // if
  	} // addModifiedField
  	
  	/**
     * Check if specific row exists in database
     *
     * @param mixed $id
     * @return boolean
     */
  	function exists($id) {
  	  $sql = "SELECT count(*) AS 'row_count' FROM " . $this->getTableName() . " WHERE " . $this->getWherePartById($id);
  	  $row = db_execute_one($sql);
  	  
  	  return (boolean) array_var($row, 'row_count', false);
  	} // exists
  	
  	/**
     * Save data into database
     *
     * @param void
     * @return integer or false
     */
  	function doSave() {
  	  $is_new = $this->isNew();
  	  
  	  // Insert...
  		if($is_new) {
  		  event_trigger('on_before_object_insert', array('object' => &$this));
  		  
  		  $sql = $this->getInsertSQL();
  		  $save = db_execute($sql);
  		  
  		  // Success...
  			if($save && !is_error($save)) {
  			  if(($this->auto_increment !== null) && (!isset($this->values[$this->auto_increment]) || !$this->values[$this->auto_increment])) {
				    $this->values[$this->auto_increment] = db_last_insert_id();
				  } // if
				  $this->resetModifiedFlags();
  				$this->setLoaded(true);
  				
  				event_trigger('on_object_inserted', array('object' => &$this));
  			  return true;
  			} else {
  			  return $save;
  			} // if
  			
  	  // Update...
  		} else {
  		  event_trigger('on_before_object_update', array('object' => &$this));
  		  
  		  $sql = $this->getUpdateSQL();
  		  
  		  if(is_null($sql)) {
  		    return true;
  		  } // if
  		  
  		  $save = db_execute($sql);
  		  if($save && !is_error($save)) {
  		    $this->resetModifiedFlags();
  		    $this->setLoaded(true);
  		    
  		    event_trigger('on_object_updated', array('object' => &$this));
  		    return true;
  		  } // if
  		  
  		  return $save;
  		} // if
  	} // doSave
  	
  	/**
     * Delete object row from database
     *
     * @param void
     * @return boolean
     * @throws DBQueryError
     */
  	function doDelete() {
  	  return db_execute("DELETE FROM " . $this->getTableName() . " WHERE " . $this->getWherePartById($this->getPrimaryKeyValue()));
  	} // doDelete
  	
  	/**
     * Prepare insert query
     *
     * @param void
     * @return string
     */
  	function getInsertSQL() {
  		$fields = array();
  		$values = array();
  		
  		// Any field value that is set and field exist is used in insert
  		foreach($this->values as $field_name => $field_value) {
  		  if($this->fieldExists($field_name)) {
  			  $fields[] = $field_name;
  			  $values[] = db_escape($field_value);
  		  } // if
  		} // foreach
  		
  		// And put it all together
  		return sprintf("INSERT INTO %s (%s) VALUES (%s)", 
  		  $this->getTableName(), 
  		  implode(', ', $fields), 
  		  implode(', ', $values)
  		); // sprintf
  	} // getInsertSQL
  	
  	/**
     * Prepare update query
     *
     * @param void
     * @return string
     */
  	function getUpdateSQL() {
  		$fields = array();
  		
  		if(!count($this->modified_fields)) {
  		  return null;
  		} // if
  		
  		foreach($this->fields as $field_name) {
  			if($this->isModifiedField($field_name)) {
  			  $fields[] = $field_name . ' = ' . db_escape($this->values[$field_name]);
  			} // if
  		} // foreach
  		
  		if(is_array($this->primary_key_updated)) {
  			$pks = $this->getPrimaryKey();
  			$old = array();
  			
  			foreach($pks as $pk) {
  			  $old[$pk] = isset($this->old_values[$pk]) ? $this->old_values[$pk] : $this->getFieldValue($pk);
  			} // foreach
  			
  			if(count($old) && $this->exists($old)) {
  			  return sprintf("UPDATE %s SET %s WHERE %s", $this->getTableName(), implode(', ', $fields), $this->getWherePartById($old));
  			} else {
  			  return $this->getInsertSQL();
  			} // if
  		} else {
  		  return sprintf("UPDATE %s SET %s WHERE %s", $this->getTableName(), implode(', ', $fields), $this->getWherePartById($this->getPrimaryKeyValue()));
  		} // if
  		
  	} // getUpdateSQL
  	
  	/**
     * Return where part of query
     *
     * @param mixed $value Array of values if we need them
     * @return string
     */
  	function getWherePartById($value = null) {
  	  $where = '';
  	  $pks = $this->getPrimaryKey();
  	  
  	  if(count($pks) > 1) {
  	  	$where = array();
  	  	foreach($pks as $field) {
  	  	  $field_value = array_var($value, $field) ? array_var($value, $field) : $this->getFieldValue($field);
  	  		$where[] = $field . ' = ' . db_escape($field_value);
  	  	} // foreach
  	  	
  	  	return count($where) > 1 ? implode(' AND ', $where) : $where[0];
  	  } else {
  	    $pk = $pks[0];
  	    $pk_value = is_array($value) ? $value[$pk] : $value;
  	    return $pk . ' = ' . db_escape($pk_value);
  	  } // if
  	} // getWherePartById
  	
  	/**
     * Reset modification idicators. Usefull when you use setXXX functions
     * but you dont want to modify anything (just loading data from database
     * in fresh object using setFieldValue function)
     *
     * @param void
     * @return void
     */
  	function resetModifiedFlags() {
  	  $this->modified_fields = array();
  	  $this->old_values = array();
  	  $this->primary_key_updated = false;
  	} // resetModifiedFlags
  	
  	/**
  	 * Return cache ID
  	 *
  	 * @param void
  	 * @return string
  	 */
  	function getCacheId() {
      $result = $this->table_name;
      
      $id = $this->getPrimaryKey();
      sort($id);
      
      foreach($id as $id_field) {
        $result .= '_' . $id_field . '_' . $this->getFieldValue($id_field);
      } // if
      
      return $result;
  	} // getCacheId
  	
  	// ---------------------------------------------------------------
  	//  Validators
  	// ---------------------------------------------------------------
  	
  	/**
     * Validates presence of specific field
     * 
     * In case of string value is trimmed and compared with the empty string. In 
     * case of any other type empty() function is used. If $min_value argument is 
     * provided value will also need to be larger or equal to it 
     * (validateMinValueOf validator is used)
     *
     * @param string $field Field name
     * @param mixed $min_value
     * @return boolean
     */
  	function validatePresenceOf($field, $min_value = null) {
  	  $value = $this->getFieldValue($field);
  	  if(is_string($value)) {
  	    if(trim($value) != '') {
  	      return $min_value === null ? true : $this->validateMinValueOf($field, $min_value);
  	    } else {
  	      return false;
  	    } // if
  	  } else {
  	    if(!empty($value)) {
  	      return $min_value === null ? true : $this->validateMinValueOf($field, $min_value);
  	    } else {
  	      return false;
  	    } // if
  	    return !empty($value);
  	  } // if
  	} // validatePresenceOf
  	
  	/**
     * This validator will return true if $value is unique (there is no row with such value in that field)
     *
     * @param string $field Filed name
     * @param mixed $value Value that need to be checked
     * @return boolean
     */
  	function validateUniquenessOf($field) {
  	  // Don't do COUNT(*) if we have one PK column
      $escaped_pk = is_array($pk_fields = $this->getPrimaryKey()) ? '*' : $pk_fields;
  	  
  	  // Get columns
  	  $fields = func_get_args();
  	  if(!is_array($fields) || count($fields) < 1) {
  	    return true;
  	  } // if
  	  
  	  // Check if we have existsing columns
  	  foreach($fields as $field) {
  	    if(!$this->fieldExists($field)) {
  	      return false;
  	    } // if
  	  } // foreach
  	  
  	  // Get where parets
  	  $where_parts = array();
  	  foreach($fields as $field) {
  	    $where_parts[] = $field . ' = ' . db_escape($this->values[$field]);
  	  } // if
  	  
  	  // If we have new object we need to test if there is any other object
  	  // with this value. Else we need to check if there is any other EXCEPT
  	  // this one with that value
  	  if($this->isNew()) {
  	    $sql = sprintf("SELECT COUNT($escaped_pk) AS 'row_count' FROM %s WHERE %s", $this->getTableName(), implode(' AND ', $where_parts));
  	  } else {
  	    
  	    // Prepare PKs part...
  	    $pks = $this->getPrimaryKey();
  	    $pk_values = array();
  	    if(is_array($pks)) {
  	      foreach($pks as $pk) {
  	        if(isset($this->primary_key_updated[$pk]) && $this->primary_key_updated[$pk]) {
  	          $primary_key_value = $this->old_values[$pk];
  	        } else {
  	          $primary_key_value = $this->values[$pk];
  	        } // if
  	        $pk_values[] = sprintf('%s <> %s', $pk, db_escape($primary_key_value));
  	      } // foreach
  	    } // if

  	    // Prepare SQL
  	    $sql = sprintf("SELECT COUNT($escaped_pk) AS 'row_count' FROM %s WHERE (%s) AND (%s)", $this->getTableName(), implode(' AND ', $where_parts), implode(' AND ', $pk_values));
  	  } // if
  	  
  	  $row = db_execute_one($sql);
  	  return array_var($row, 'row_count', 0) < 1;
  	} // validateUniquenessOf
  	
  	/**
     * Validate max value of specific field. If that field is string time 
     * max lenght will be validated
     *
     * @param string $filed
     * @param integer $max Maximal value
     * @return null
     */
  	function validateMaxValueOf($field, $max) {
  	  if(!$this->fieldExists($field)) {
  	    return false;
  	  } // if
  	  
  	  $value = $this->getFieldValue($field);

  	  if(is_string($value)) {
  	    return strlen(trim($value)) <= $max;
  	  } else {
  	    return $value >= $max;
  	  } // if
  	} // validateMaxValueOf
  	
  	/**
     * Valicate minimal value of specific field. 
     * 
     * If string minimal lenght is checked (string is trimmed before it is 
     * compared). In any other case >= operator is used
     *
     * @param string $field
     * @param integer $min Minimal value
     * @return boolean
     */
  	function validateMinValueOf($field, $min) {
  	  if(!$this->fieldExists($field)) {
  	    return false;
  	  } // if
  	  
  	  $value = $this->getFieldValue($field);
  	  
  	  if(is_string($value)) {
  	    return strlen(trim($value)) >= $min;
  	  } else {
  	    return $value >= $min;
  	  } // if
  	} // validateMinValueOf
  	
  } // end class DataObject

?>