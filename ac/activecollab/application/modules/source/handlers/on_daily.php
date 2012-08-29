<?php
  /**
   * Source version control module on_daily event handler
   *
   * @package activeCollab.modules.source
   * @subpackage handlers
   */

  /**
   * Daily update of repositories
   *
   * @param null
   * @return void
   */
  function source_handle_on_daily() {
    
    require_once(ANGIE_PATH.'/classes/xml/xml2array.php');
    
    $results = 'Repositories updated: ';
    
    $repositories = Repositories::findByUpdateType(REPOSITORY_UPDATE_DAILY);
    foreach ($repositories as $repository) {
      // don't update projects other than active ones
      $project = Projects::findById($repository->getProjectId());
      if ($project->getStatus() !== PROJECT_STATUS_ACTIVE) {
        continue;
      } // if
      
      $repository->loadEngine();
      $repository_engine = new RepositoryEngine($repository, true);
      
      $last_commit = $repository->getLastCommit();
      $revision_to = is_null($last_commit) ? 1 : $last_commit->getRevision();
    
      $logs = $repository_engine->getLogs($revision_to);
      
      if (!$repository_engine->has_errors) {
        $repository->update($logs['data']);
        $total_commits = $logs['total'];
      
        $results .= $repository->getName().' ('.$total_commits.' new commits); ';
      
        if ($total_commits > 0) {
          $repository->sendToSubscribers($total_commits, $repository_engine);
          $repository->createActivityLog($total_commits);
        } // if
      } // if
      
    } // foreach
    
    return is_foreachable($repositories) && count($repositories) > 0 ? $results : 'No repositories for daily update';
  } // source_handle_on_daily
?>