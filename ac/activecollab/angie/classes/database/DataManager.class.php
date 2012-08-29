<?php
  
  /**
   * Data manager class
   *
   * This class provides interface for extracting multiple rows form a specific 
   * table and population of item objects with extracted data
   * 
   * @package angie.library.database
   */
  class DataManager {
    
    /**
     * Do a SELECT query over database with specified arguments
     * 
     * This function can return single instance or array of instances that match 
     * requirements provided in $arguments associative array
     * 
     * $arguments is an associative array with following fields (all optional):
     * 
     *  - one        - select first row
     *  - conditions - additional conditions
     *  - group      - group by string
     *  - having     - having string
     *  - order      - order by string
     *  - offset     - limit offset, valid only if limit is present
     *  - limit      - number of rows that need to be returned
     *
     * @param array $arguments
     * @param string $table_name
     * @param string $item_class
     * @return mixed
     * @throws DBQueryError
     */
    function find($arguments = null, $table_name = null, $item_class = null) {
      return DataManager::findBySQL(DataManager::prepareSelectFromArguments($arguments, $table_name), null, array_var($arguments, 'one'), $table_name, $item_class);
    } // find
    
    /**
     * Return object of a specific class by SQL
     *
     * @param string $sql
     * @param array $arguments
     * @param boolean $one
     * @param string $table_name
     * @param string $item_class
     * @return array
     */
    function findBySQL($sql, $arguments = null, $one = false, $table_name = null, $item_class = null) {
      if($arguments !== null) {
        $sql = db_prepare_string($sql, $arguments);
      } // if
      
      $rows = db_execute_all($sql);
      
      if(is_error($rows)) {
        return $rows;
      } // if
      
      if(!is_array($rows) || !count($rows)) {
        return null;
      } // if
      
      if($one) {
        $item = new $item_class();
        $item->loadFromRow($rows[0], true);
        return $item;
      } else {
        $items = array();
        
        foreach($rows as $row) {
          $item = new $item_class();
          $item->loadFromRow($row, true);
          $items[] = $item;
        } // foreach
        
        return count($items) ? $items : null;
      } // if
    } // findBySQL
    
    /**
     * Return object by ID
     *
     * @param mixed $id
     * @param string $table_name
     * @param string $item_class
     * @return null
     */
    function findById($id, $table_name = null, $item_class = null) {
      if(is_array($id)) {
        ksort($id);
        
        $cache_id = $table_name;
        foreach($id as $k => $v) {
          $cache_id .= '_' . $k . '_' . $v;
        } // if
      } else {
        $cache_id = $table_name . '_id_' . $id;
      } // if
      
      $cached = cache_get($cache_id);
      if($cached) {
        if(!class_exists($item_class)) {
          event_trigger('on_class_missing', array($item_class));
        } // if
        
        $item = new $item_class();
        $item->loadFromRow($cached);
        return $item;
      } // if
      
      $conditions = array();
      if(is_array($id)) {
        foreach($id as $pk_field => $pk_field_value) {
          $conditions[] = $pk_field . ' = ' . db_escape($pk_field_value);
        } // foreach
      } else {
        $conditions[] = 'id = ' . db_escape($id);
      } // if
      
      $object = DataManager::find(array(
        'conditions' => implode(' AND ', $conditions),
        'one' => true
      ), $table_name, $item_class);
      
      return $object;
    } // findById
    
    /**
     * Return number of rows in this table
     *
     * @param string $conditions Query conditions
     * @param string $table_name
     * @return integer
     * @throws DBQueryError
     */
    function count($conditions = null, $table_name = null) {
      $conditions = DataManager::prepareConditions($conditions);
      $where_string = trim($conditions) == '' ? '' : "WHERE $conditions";
      $row = db_execute_one("SELECT COUNT(*) AS 'row_count' FROM `$table_name` $where_string");
      return (integer) array_var($row, 'row_count', 0);
    } // count
    
    /**
     * Update table
     * 
     * $updates is associative array where key is field name and value is new 
     * value
     *
     * @param array $updates
     * @param string $conditions
     * @param string $table_name
     * @return boolean
     * @throws DBQueryError
     */
    function update($updates, $conditions = null, $table_name = null) {
      $updates_part = array();
      foreach($updates as $field => $value) {
        $updates_part[] = $field . ' = ' . db_escape($value);
      } // foreach
      $updates_part = implode(',' , $updates_part);
      
      $conditions = DataManager::prepareConditions($conditions);
      
      $where_string = trim($conditions) == '' ? '' : "WHERE $conditions";
      return db_execute("UPDATE `$table_name` SET $updates_part $where_string");
    } // update
    
    /**
     * Delete all rows that match given conditions
     *
     * @param string $conditions Query conditions
     * @param string $table_name
     * @return boolean
     * @throws DBQueryError
     */
    function delete($conditions = null, $table_name = null) {
      $conditions = DataManager::prepareConditions($conditions);
      $where_string = trim($conditions) == '' ? '' : "WHERE $conditions";
      return db_execute("DELETE FROM `$table_name` $where_string");
    } // delete
    
    /**
     * Return paginated result
     * 
     * This function will return paginated result as array. First element of 
     * returned array is array of items that match the request. Second parameter 
     * is Pager class instance that holds pagination data (total pages, current 
     * and next page and so on)
     *
     * @param array $arguments
     * @param integer $page
     * @param integer $per_page
     * @param string $table_name
     * @param string $item_class
     * @return array
     * @throws DBQueryError
     */
    function paginate($arguments = null, $page = 1, $per_page = 10, $table_name = null, $item_class = null) {
      if(!is_array($arguments)) {
        $arguments = array();
      } // if
      
      $arguments['limit'] = $per_page;
      $arguments['offset'] = ($page - 1) * $per_page;
      
      $items = DataManager::find($arguments, $table_name, $item_class);
      $total_items = DataManager::count(array_var($arguments, 'conditions'), $table_name);
      
      return array(
        $items,
        new Pager($page, $total_items, $per_page)
      ); // array
    } // paginate
    
    /**
     * Prepare SELECT query string from arguments and table name
     *
     * @param array $arguments
     * @param string $table_name
     * @return string
     */
    function prepareSelectFromArguments($arguments = null, $table_name = null) {
      $one        = (boolean) array_var($arguments, 'one', false);
      $conditions = array_var($arguments, 'conditions') ? DataManager::prepareConditions(array_var($arguments, 'conditions')) : '';
      $group_by   = array_var($arguments, 'group', '');
      $having     = array_var($arguments, 'having', '');
      $order_by   = array_var($arguments, 'order', '');
      $offset     = (integer) array_var($arguments, 'offset', 0);
      $limit      = (integer) array_var($arguments, 'limit', 0);
      
      $where_string = trim($conditions) == '' ? '' : "WHERE $conditions";
      $group_by_string = trim($group_by) == '' ? '' : "GROUP BY $group_by";
      $having_string = trim($having) == '' ? '' : "HAVING $having";
      $order_by_string = trim($order_by) == '' ? '' : "ORDER BY $order_by";
      $limit_string = $limit > 0 ? "LIMIT $offset, $limit" : '';
      
      return "SELECT * FROM `$table_name` $where_string $group_by_string $having_string $order_by_string $limit_string";
    } // prepareSelectFromArguments
    
    /**
     * Get conditions as argument and return them in the string (if array walk 
     * through and escape values)
     *
     * @param mixed $conditions
     * @return string
     */
    function prepareConditions($conditions) {
      if(is_array($conditions)) {
        $conditions_sql = array_shift($conditions);
        $conditions_arguments = count($conditions) ? $conditions : null;
        return db_prepare_string($conditions_sql, $conditions_arguments);
      } // if
      return $conditions;
    } // prepareConditions
    
    /**
     * Get multiple conditions and combine them into one condition string
     * 
     * DataManager::combineConditions(array('project_id = ?', $project->getId()), 'is_visible = 1');
     * 
     * Will result in:
     * 
     * (project_id = '12') AND (is_visible = 1)
     * 
     * Empty conditions are ignored
     *
     * @param void
     * @return string
     */
    function combineConditions() {
      $args = func_get_args();
      if(!count($args)) {
        return '';
      } // if
      
      $conditions = array();
      foreach($args as $arg) {
        $prepared = DataManager::prepareConditions($arg);
        if($prepared) {
          $conditions[] = "($prepared)";
        } // if
      } // foeach
      
      return count($conditions) ? implode(' AND ', $conditions) : '';
    } // combineConditions
    
  } // DataManager

?>