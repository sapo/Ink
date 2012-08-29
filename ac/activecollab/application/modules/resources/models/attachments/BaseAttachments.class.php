<?php

  /**
   * BaseAttachments class
   */
  class BaseAttachments extends DataManager {
  
    /**
     * Do a SELECT query over database with specified arguments
     * 
     * This function can return single instance or array of instances that match 
     * requirements provided in $arguments associative array
     *
     * @param array $arguments Array of query arguments. Fields:
     * 
     *  - one        - select first row
     *  - conditions - additional conditions
     *  - order      - order by string
     *  - offset     - limit offset, valid only if limit is present
     *  - limit      - number of rows that need to be returned
     * 
     * @return mixed
     * @throws DBQueryError
     */
    function find($arguments = null) {
      return DataManager::find($arguments, TABLE_PREFIX . 'attachments', 'Attachment');
    } // find
    
    /**
     * Return array of objects that match specific SQL
     *
     * @param string $sql
     * @param array $arguments
     * @param boolean $one
     * @return mixed
     */
    function findBySQL($sql, $arguments = null, $one = false) {
      return DataManager::findBySQL($sql, $arguments, $one, TABLE_PREFIX . 'attachments', 'Attachment');
    } // findBySQL
    
    /**
     * Return object by ID
     *
     * @param mixed $id
     * @return Attachment
     */
    function findById($id) {
      return DataManager::findById($id, TABLE_PREFIX . 'attachments', 'Attachment');
    } // findById
    
    /**
     * Return number of rows in this table
     *
     * @param string $conditions Query conditions
     * @return integer
     * @throws DBQueryError
     */
    function count($conditions = null) {
      return DataManager::count($conditions, TABLE_PREFIX . 'attachments');
    } // count
    
    /**
     * Update table
     * 
     * $updates is associative array where key is field name and value is new 
     * value
     *
     * @param array $updates
     * @param string $conditions
     * @return boolean
     * @throws DBQueryError
     */
    function update($updates, $conditions = null) {
      return DataManager::update($updates, $conditions, TABLE_PREFIX . 'attachments');
    } // update
    
    /**
     * Delete all rows that match given conditions
     *
     * @param string $conditions Query conditions
     * @param string $table_name
     * @return boolean
     * @throws DBQueryError
     */
    function delete($conditions = null) {
      return DataManager::delete($conditions, TABLE_PREFIX . 'attachments');
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
     * @return array
     * @throws DBQueryError
     */
    function paginate($arguments = null, $page = 1, $per_page = 10) {
      return DataManager::paginate($arguments, $page, $per_page, TABLE_PREFIX . 'attachments', 'Attachment');
    } // paginate
  
  }

?>