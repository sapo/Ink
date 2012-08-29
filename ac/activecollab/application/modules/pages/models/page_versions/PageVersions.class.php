<?php

  /**
   * PageVersions class
   * 
   * @package activeCollab.modules.pages
   * @subpackage models
   */
  class PageVersions extends BasePageVersions {
  
    /**
     * Return page versions
     *
     * @param Page $page
     * @param integer $version
     * @return array
     */
    function findByPage($page, $version = null) {
      if ($version === null) {
        return PageVersions::find(array(
          'conditions' => array('page_id = ?', $page->getId()),
          'order' => 'version DESC',
        ));
      } else {
        return PageVersions::find(array(
          'conditions' => array('page_id = ? AND version = ?', $page->getId(), $version),
          'order' => 'version DESC',
          'one'   => true
        ));        
      } // if
    } // findByPage
    
    /**
     * Return number of versions for a given page
     *
     * @param Page $page
     * @return integer
     */
    function countByPage($page) {
      return PageVersions::count(array('page_id = ?', $page->getId()));
    } // countByPage
    
    /**
     * Find previous version
     *
     * @param ApplicationObject $for
     * @return PageVersion
     */
    function findPrevious($for) {
      if(instance_of($for, 'Page')) {
        return PageVersions::find(array(
          'conditions' => array('page_id = ?', $for->getId()),
          'order'      => 'version DESC',
          'offset'     => 0,
          'limit'      => 1,
          'one'        => true,
        ));
      } elseif(instance_of($for, 'PageVersion')) {
        return PageVersions::find(array(
          'conditions' => array('page_id = ? AND version < ?', $for->getPageId(), $for->getVersion()),
          'order'      => 'version DESC',
          'offset'     => 0,
          'limit'      => 1,
          'one'        => true,
        ));
      } else {
        return null;
      } // if
    } // findPrevious
    
    /**
     * Find page versions by list of ID-s
     *
     * @param array $ids
     * @return array
     */
    function findByPageIds($ids) {
      return PageVersions::find(array(
        'conditions' => array('page_id IN (?)', $ids),
        'order' => 'created_on DESC',
      ));
    } // findByIds
  
  }

?>