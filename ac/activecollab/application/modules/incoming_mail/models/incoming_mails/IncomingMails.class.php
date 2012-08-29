<?php

  /**
   * IncomingMails class
   */
  class IncomingMails extends BaseIncomingMails {
  
    // Put custom methods here
    
    /**
     * Paginate pending emails
     *
     * @param integer $page
     * @param integer $per_page
     * @return integer
     */
    function paginatePending($page, $per_page) {
      return IncomingMails::paginate(array(
        'conditions' => array('state > 0'),
      ), $page, $per_page);
    } // paginatePending
    
    /**
     * Count pending emails
     *
     * @return integer
     */
    function countPending() {
      return IncomingMails::count(array('state > 0'));
    } // countPending
    
    /**
     * Find conflicts by ids
     *
     * @param array $ids
     * @return array
     */
    function findByIds($ids) {
      return IncomingMails::find(array(
        'conditions' => array('id IN (?)', $ids),
        'order' => 'created_on DESC',
      ));
    } // findByIds
    
  } // IncomingMails

?>