<?php

  /**
   * Abstract search engine
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class SearchEngine extends AngieObject {
     
    /**
     * Search
     *
     * @param string $search_for
     * @param string $type
     * @param User $user
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    function search($search_for, $type, $user, $page = 1, $per_page = 30) {
      use_error('NotImplementedError');
      return new NotImplementedError('SearchEngine', 'search');
    } // search
    
    /**
     * Update search index
     *
     * @param integer $object_id
     * @param string $content
     * @param string $type
     * @param array $attributes
     * @return boolean
     */
    function update($object_id, $type, $content, $attributes) {
    	use_error('NotImplementedError');
      return new NotImplementedError('SearchEngine', 'update');
    } // update
    
    /**
     * Remove from search index
     *
     * @param integer $object_id
     * @param string $type
     * @return boolean
     */
    function remove($object_id, $type) {
      use_error('NotImplementedError');
      return new NotImplementedError('SearchEngine', 'remove');
    } // remove
    
    /**
     * Returns true if we already have an search index for a given entry
     *
     * @param integer $object_id
     * @param string $type
     * @return boolean
     */
    function hasObject($object_id, $type) {
      use_error('NotImplementedError');
      return new NotImplementedError('SearchEngine', 'hasObject');
    } // hasObject
    
  } // SearchEngine extends AngieObject 

?>