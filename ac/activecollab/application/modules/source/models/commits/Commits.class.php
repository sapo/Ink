<?php

/**
 * Commit record class
 *
 * @package activeCollab.modules.source
 * @subpackage models
 */
class Commits extends ProjectObjects {

  /**
   * Get recent activity for a repository
   *
   * @param Repository $repository
   * @param int $from_days_before
   * @return array
   */
  function getRecentActivity($repository, $from_days_before = 15) {
    $from = new DateTimeValue(($from_days_before-1).' days ago');
    
    $last_commit = $repository->getLastcommit();
    $beginning_of_day = $from->beginningOfDay();

    $max_commits = Commits::count(array("parent_id = ? AND created_on >= ? GROUP BY DAY(created_on) ORDER BY row_count DESC LIMIT 1", $repository->getId(), $beginning_of_day));

    $from_days_before--;
    for ($i = $from_days_before; $i >= 0; $i--) {
      $date = new DateTimeValue($i . 'days ago');
      $this_date_beginning = $date->beginningOfDay();
      $this_date_end = $date->endOfDay();
      
      $commits_count = Commits::count(array("parent_id = ? AND created_on >= ? AND created_on <= ?", $repository->getId(), $this_date_beginning, $this_date_end));

      $activity[$i]['commits'] = $commits_count;
      $activity[$i]['created_on'] = date('F d, Y', $date->getTimestamp());
      $activity[$i]['percentage'] = round($commits_count*100/$max_commits);
    }

    return $activity;
  } // get recent activity


  /**
   * Find commit by revision
   *
   * @param int $revision
   * @param Repository repository
   * @return Commit
   */
  function findByRevision($revision, $repository) {
    return Commits::find(array(
      'conditions'  => array('integer_field_1 = ? AND parent_id = ?', $revision, $repository->getId()),
      'one'         => true
    ));
  } // find by revision
  
  /**
   * Find all commits with $revision_ids ids in $repository
   *
   * @param array $revision_ids
   * @param Repository $repository
   * @return array
   */
  function findByRevisionIds($revision_ids, $repository) {
    return Commits::find(array(
      'conditions' => array('integer_field_1 IN (?) AND parent_id = ?', $revision_ids, $repository->getId()),
      'order'      => 'created_on DESC, integer_field_1 DESC',
    ));
  } // findByRevisionIds

  /**
   * Find by commit id
   *
   * @param integer $id
   * @return object
   */
  function findById($id) {
    return ProjectObjects::findById($id);
  } // find by id


  /**
   * Find last commit
   *
   * @param Repository $repository
   * @return mixed
   */
  function findLastCommit($repository) {
    return Commits::find(array(
    'conditions'  => array('parent_id = ? AND type = ?', $repository->getId(), 'Commit'),
    'order'       => 'integer_field_1 DESC',
    'one'         => true
    ));
  } // find last commit

  /**
   * Paginate commits by repository
   *
   * @param Repository $repository
   * @param integer $page
   * @param integer $per_page
   * @return array of objects
   */
  function paginateByRepository($repository, $page = 1, $per_page = 10, $filter_by_author = null) {
    $conditions = "parent_id = '".$repository->getId()."' AND type = 'Commit'";
    
    if (!is_null($filter_by_author)) {
      $conditions .= " AND created_by_name = '$filter_by_author'";
    } // if
    
    return Commits::paginate(array(
    'conditions'  => $conditions,
    'order'       => 'integer_field_1 DESC'
    ), $page, $per_page);
  } // paginate by repository

}

?>