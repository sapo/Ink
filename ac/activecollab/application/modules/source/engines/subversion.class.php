<?php

require_once(ANGIE_PATH.'/classes/xml/xml2array.php');

/**
 * Subversion commands library
 *
 * @package activecollab.modules.source
 * @subpackage version control engines
 * @author Oliver Maksimovic
 */
class RepositoryEngine extends Repository {

  /**
   * Eeach output from SVN is stored here as an array since exec() always
   * returns output in that form
   *
   * @var array
   */
  var $output = null;


  /**
   * Action is triggered by handler
   *
   * @var boolean
   */
  var $triggerred_by_handler = false;
  
  
  /**
   * If is async request
   *
   * @var unknown_type
   */
  var $error = null;
  
  /**
   * Path to svn executable
   *
   * @var string
   */
  var $executable_path = '';
  
  /**
   * SVN config dir
   *
   * @var string
   */
  var $config_dir = '';
  
  /**
   * Whether to ignore errors returned from SVN or not
   *
   * @var boolean
   */
  var $ignore_errors = false;
  
  /**
   * Class constructor
   *
   */
  function __construct($repository, $triggered_by_handler = false) {
    parent::__construct();
    // check if we have neccessary resources
    if (instance_of($repository, 'Repository')) {
      $this->active_repository = $repository;
      $this->active_project = Projects::findById($repository->getProjectId());
    } // if
    
    $this->executable_path = with_slash(ConfigOptions::getValue('source_svn_path'));
    
    $config_dir = ConfigOptions::getValue('source_svn_config_dir');
    $this->config_dir = is_null($config_dir) ? '' : '--config-dir '.$config_dir;
    
    $this->triggerred_by_handler = $triggered_by_handler;
  } // __construct
  
  /**
   * Check if path is directory
   *
   * @param string $path
   * @param int $revision
   */
  function getInfo($path, $revision = null, $raw_output = false, $peg_revision = null) {
    $revision_info = !is_null($revision) ? '-r '.$revision : '';
    
    if (intval($revision) !== intval($peg_revision)) {
      $peg = !is_null($peg_revision) ? "@".$peg_revision : '';
    } else {
      $peg = '';
    } // if
    
    $string = 'info '.$revision_info.' '.$this->getRealPath($path).$peg;
    $this->execute($string);
    
    $info = array();
    
    if ($raw_output) {
      $info = implode("\n", $this->output);
    } else {
      // get path
      if (strpos($this->output['0'], 'Path:') !== false) {
        $info['path'] = str_replace('Path: ', '', $this->output['0']);
      } else {
        $info['path'] = false;
      } // if

      // get type of the item
      if (strpos($this->output['5'], 'Node Kind:') !== false) {
        $info['type'] = str_replace('Node Kind: ', '', $this->output['5']);
      } else {
        $info['type'] = false;
      } // if
      
      // get type of the item
      if (strpos($this->output['2'], 'Repository Root:') !== false) {
        $info['root'] = str_replace('Repository Root: ', '', $this->output['2']);
      } else {
        $info['root'] = false;
      } // if

      // get revision
      if (strpos($this->output['4'], 'Revision:') !== false) {
        $info['revision'] = str_replace('Revision: ', '', $this->output['4']);
      } else {
        $info['revision'] = false;
      } // if
    } // if
    
    return $info;
  } // getInfo
  
  /**
   * Get properties of the item in repository
   *
   * @param string $path
   * @param string $revision
   * @return string
   */
  function getProperties($path, $revision = 'HEAD', $peg_revision = null) {
    $peg_revision = !is_null($peg_revision) ? '@'.$peg_revision : '';
    $string = 'proplist -r '.$revision.' --verbose '.$this->getRealPath($path).$peg_revision;
    $this->execute($string);
    return implode("\n", $this->output);
  } // getProperties

  /**
   * Group paths by type
   *
   * @param array $paths
   * @return array
   */
  function groupPaths($paths) {
    if (is_foreachable($paths)) {
      $grouped = array();
      if (is_foreachable($paths)) {
        foreach ($paths as $path) {
          $grouped[$path['action']][] = $path['path'];
        } // foreach
      } // if
      return $grouped;
    } //if
    return null;
  } // groupPaths


  /**
   * Get head revision number
   *
   * @param null
   * @return integer
   */
  function getHeadRevision($isAsync = false) {
    $this->triggerred_by_handler = $isAsync;
    
    $info = $this->getInfo('', null);
    
    if (isset($info['revision']) && $info['revision'] !== false) {
      return $info['revision'];
    } else {
      $error_message = lang('Could not obrain the highest revision number for the given repository');
      if (!$this->triggerred_by_handler) {
        flash_error($error_message);
        redirect_to_referer(source_module_url($this->active_project));
      } else {
        $this->error = $error_message;
      } // if
    } // if
    
  } // getHeadRevision


  /**
   * Compare one revision of a file to another revision
   *
   * @param string $path
   * @param int $revision_from
   * @param int $revision_to
   * @return string
   */
  function compareToRevision($path, $revision_from, $revision_to, $peg_revision = null) {
    $peg = !is_null($peg_revision) ? '@'.$peg_revision : '';
    
    $string = 'diff -r '.$revision_from.':'.$revision_to.' '.$this->getRealPath($path).$peg;
    $this->execute($string);
    return $this->output;
  } // compare to revision


  /**
   * Shown svn blame information for a file
   *
   * @param integer $revision
   * @param string $path
   * @return array
   */
  function getBlame($revision, $path) {
    $string = 'blame --xml -r '.$revision.' '.$this->getRealPath($path);
    $this->execute($string);

    $blame_data = xml2array(implode("\n", $this->output));

    $blame = array();
    foreach ($blame_data['blame']['target']['entry'] as $key => $blame_item) {
      $blame[$key]['line'] = $blame_item['attr']['line-number'];
      $blame[$key]['revision'] = $blame_item['commit']['attr']['revision'];
      $blame[$key]['author'] = $blame_item['commit']['author']['value'];
      $blame[$key]['date'] = new DateTimeValue($blame_item['commit']['date']['value']);
    }

    return $blame;
  } // get_blame


  /**
   * Get file history
   *
   * @param integer $revision
   * @param string $path
   * @return array
   */
  function getFileHistory($revision, $path, $peg_revision = null) {
    $peg = !is_null($peg_revision) ? '@'.$peg_revision : '';
    
    $string = 'log --xml -r '.$revision.':1 '.$this->getRealPath($path).$peg;
    $this->execute($string);

    $history_data = xml2array(implode("\n", $this->output), 1, array('logentry'));
    $log = array();
    $i=1;
    foreach ($history_data['log']['logentry'] as $history_item) {
      $log[$i]['revision'] = $history_item['attr']['revision'];
      $log[$i]['author'] = $history_item['author']['value'];
      $log[$i]['date'] = new DateTimeValue($history_item['date']['value']);
      $log[$i]['msg'] = $history_item['msg']['value'];
      $i++;
    }

    return $log;
  } // getFileHistory


  /**
   * Get file content
   *
   * @param Revision $revision
   * @param string $file
   * @return string
   */
  function cat($revision, $path, $peg_revision = null) {
    $peg = !is_null($peg_revision) ? '@'.$peg_revision : '';
    $string = 'cat -r '.$revision.' '.$this->getRealPath($path).$peg;
    $this->execute($string);
    return $this->output;
  } // get file content


  /**
   * Get diff changes for a specific commit
   *
   * @param Revision $revision
   * @param path $path
   * @return string
   */
  function getCommitDiff($revision, $path = '') {
    $from = $revision-1;

    $string = 'diff -r '.$from.':'.$revision.' '.$this->getRealPath($path);
    $this->execute($string);
    return $this->output;
  } // get commit diff


  /**
   * Browse repository
   *
   * @param Revision $revision
   * @param string $path
   * @return array 
   */
  function browse($revision, $path = '', $peg_revision = null) {
    if (intval($revision) !== intval($peg_revision)) {
      $peg = !is_null($peg_revision) ? "@".$peg_revision : '';
    } else {
      $peg = '';
    } // if
    
    $string = 'list -r '.$revision.' --xml '.$this->getRealPath($path).$peg;
    $this->execute($string);

    $list_data = xml2array(implode("\n",$this->output), 1, array('entry'));

    $list['current_dir'] = $list_data['lists']['list']['attr']['path'];

    $entries = array();
    $dirs = array();
    $files = array();

    $i=0;
    foreach ($list_data['lists']['list']['entry'] as $entry) {
      // put dirs and files into separate arrays
      if ($entry['attr']['kind'] == 'dir') {
        $dirs[$i]['kind'] = $entry['attr']['kind'];
        $dirs[$i]['name'] = $entry['name']['value'];
        $dirs[$i]['size'] = $entry['size']['value'];
        $dirs[$i]['revision'] = $entry['commit']['attr']['revision'];
        $dirs[$i]['author'] = $entry['commit']['author']['value'];
        $dirs[$i]['date'] = new DateTimeValue($entry['commit']['date']['value']);
      }
      else {
        $files[$i]['kind'] = $entry['attr']['kind'];
        $files[$i]['name'] = $entry['name']['value'];
        $files[$i]['size'] = format_file_size($entry['size']['value']);
        $files[$i]['revision'] = $entry['commit']['attr']['revision'];
        $files[$i]['author'] = $entry['commit']['author']['value'];
        $files[$i]['date'] = new DateTimeValue($entry['commit']['date']['value']);
      }
      $i++;
    }

    // merge dirs and files array into one array with each of them sorted by name, but
    // directories go first
    $list['entries'] = array_merge(sortByKey($dirs, 'name'), sortByKey($files, 'name'));

    return $list;

  } // browse repository


  /**
   * Get log data
   *
   * @param integer $revision_to
   * @param mixed $revision_from
   * @return array
   */
  function getLogs($revision_to,  $revision_from = 'HEAD', $logs_per_query = 100) {
    
    // get multiple logs or a single one
    if (!is_null($revision_from)) {
      $r = $revision_from.':'.$revision_to;
    }
    else {
      $r = $revision_to;
      $this->triggerred_by_handler = true;
    } // if
        
    $string = 'log -r '.$r.' --xml --verbose '.$this->active_repository->getUrl();
    $this->execute($string);
    $log_data = xml2array(implode("\n", $this->output), 1, array('path', 'logentry'));

    if ($revision_to >= 1 && !is_null($revision_from)) {
      unset($log_data['log']['logentry'][count($log_data['log']['logentry'])-1]); // oldest entry received is our newest, we don't need it here
    }

    $insert_data = array();
    $i=1;

    // this is because we get commits from SVN sorted from newest to oldest
    $logs = is_array($log_data['log']['logentry']) ? array_reverse($log_data['log']['logentry']) : array();

    // loop through array of log entries
    foreach ($logs as $key=>$log_entry) {
      // prevent duplicate entries in case when there are two separate update processes
      // (like, both scheduled task and aC user triggered the update
      if (Commits::count("parent_id = '".$this->active_repository->getId()."' AND integer_field_1 = '".$log_entry['attr']['revision']."'") > 0) {
        continue;
      } // if
      
      $paths = array();

      $k=0;
      foreach ($log_entry['paths']['path'] as $path) {
        $paths[$k]['path'] = mysql_real_escape_string($path['value']); // paths can contain file names with characters that can break the query
        $paths[$k]['action'] = $path['attr']['action'];
        $k++;
      } // foreach

      $date = new DateTimeValue($log_entry['date']['value']);
      $log_date = $date->getYear()."-".$date->getMonth().'-'.$date->getDay().' '.$date->getHour().':'.$date->getMinute().':'.$date->getSecond();

      $commit_message = Commit::analyze_message($log_entry['msg']['value'], $log_entry['author']['value'], $log_entry['attr']['revision'], $this->active_repository, $this->active_project);

      $insert_data[$i][$key] = "('Commit','Source','" . $this->active_project->getId() . "','" . $this->active_repository->getId() . "','Repository','".mysql_real_escape_string($commit_message)."','".$log_entry['attr']['revision']."','".serialize($paths)."','".mysql_real_escape_string($log_entry['author']['value'])."','$log_date', '".STATE_VISIBLE."', '".$this->active_repository->getVisibility()."')";

      $i = count($insert_data[$i]) < $logs_per_query ? $i : $i+1;
    }

    return array('data'=>$insert_data, 'total'=>count($logs));
  } // get logs


  /**
   * Check if there are any error messages in SVN response
   *
   * @param array $response
   * @return mixed
   */
  function checkResponse($response) {
    if ($this->ignore_errors) {
      return true;
    } // if
    
    if (is_array($response)) {
      if (strpos($response['0'], 'command not found')) {
        return new Error(lang('Unable to execute svn command. Please enter path to svn in Admin settings for Source module'));
      }
      elseif (strpos($response['0'], 'Unable to open')) {
        return new Error(isset($response['1']) ? implode('<br/>', $response) : $response['0']);
      }
      elseif (strpos($response['0'], 'Unable to find repository location for')) {
        return new Error($response['0']);
        return new Error(lang('Unable to find selected item in the requested revision'));
      }
      elseif (strpos($response['0'], 'is not a working copy')) {
        return new Error($response['0']);
      }
      elseif (strpos($response['0'], 'File not found')) {
        return new Error($response['0']);
      }
      elseif (strpos($response['0'], 'No such revision')) {
        return new Error($response['0']);
      }
      elseif (strpos($response['0'], 'No such file or directory')) {
        return new Error($response['0']);
      }
      elseif (strpos($response['0'], 'is not under version control')) {
        return new Error($response['0']);
      }
      elseif (strpos($response['0'], 'permission denied')) {
        return new Error($response['0']);
      }
      elseif (strpos($response['0'], 'invalid option')) {
        return new Error($response['0']);
      }
      elseif (strpos($response['0'], 'unexpected return value')) {
        return new Error($response['0']);
      }
      elseif (strpos($response['0'], "doesn't accept option")) {
        return new Error($response['0']);
      }
      elseif (strpos($response['0'], 'non-existent in that revision')) {
        return new Error(lang("Path does not exist in requested revision"));
      }
      elseif (strpos($response['0'], 'Syntax error')) {
        return new Error(lang('Syntax error in Subversion command'));
      }
      elseif (strpos($response['0'], 'refers to a directory')) {
        return new Error(lang('Selected item is a directory'));
      }
      elseif (strpos($response['2'], 'authorization failed')) {
        return new Error(lang("Authorization failed"));
      }
      elseif (strpos($response['1'], 'Host not found')) {
        return new Error(lang("Repository hostname not found"));
      }
      elseif (strpos($response['0'], "request failed")) {
        return new Error(count($response) > 1 ? implode('<br/>', $response) : $response['0']); // various "request failed" errors that may exist but we're not aware of
      } // if
    } else {
      return new Error("Invalid reponse received or no response received at all");
    } // if
  } // check response


  /**
   * Execute SVN command
   *
   * @param string $command
   * @return boolean
   */
  function execute($command) {
    $this->output = null;
    
    $authentication = '--username '.$this->active_repository->getUsername().' --password '.$this->active_repository->getPassword().' --non-interactive';

    $executable_path = empty($this->executable_path) ? '' : with_slash($this->executable_path);

    $escaped = escapeshellcmd($executable_path."svn ".$authentication." ".$this->config_dir." $command")." 2>&1";
    
    exec($escaped, $this->output);
    $error = $this->checkResponse($this->output);

    if (is_error($error)) {
      if (!$this->triggerred_by_handler) {
        flash_error($error->getMessage());
        redirect_to_referer(source_module_url($this->active_project));
      } else {
        $this->error = $error->getMessage();
      } // if
    } // if
    
    return true;
  } // execute command


  /**
   * Parse diff content
   *
   * @param diff $data
   * @return array
   */
  function parseDiff($data) {
    $diff = array();
    $i = 0;
    $files = array();
    foreach ($data as $key => $diff_line) {
      $add_line = true;
      $skip_file = false;
      
      // Diff ended, property changes are starting
      if (str_starts_with($diff_line, "Property changes on:")) {
        $add_line = false;
        $files[$i]['ended'] = true;
      }

      // We have a new file diff
      if (str_starts_with($diff_line, "Index: ")) {
        $i++;
        $files[$i]['started'] = true;
        $files[$i]['ended'] = false;
        
        $diff[$i]['file'] = str_replace("Index: ", "", $diff_line);
        $diff[$i]['content'] = "";
        $diff[$i]['lines'] = "";
        $add_line = false;
      }

      // we are ignoring beginning of diff for a file info about start/end revision
      if (str_starts_with($diff_line, "===") || str_starts_with($diff_line, "+++") || str_starts_with($diff_line, "---") || strcmp($diff_line, "\ No newline at end of file") == 0) {
        $add_line = false;

        // we need to close <pre> block at the end of diff content block
        if (strcmp($diff_line, "\ No newline at end of file") == 0) {
          $files[$i]['ended'] = true;
        }
      }

      // line numbers on the left side of the diff
      if (str_starts_with($diff_line, "@@")) {
        $diff[$i]['lines'] .= "... | ...\n";
        $diff[$i]['content'] .= " \n";

        $begin_lines = explode(" ", trim(str_replace("@@", "", $diff_line)));

        // start counting lines from these
        $left_line = abs(intval($begin_lines['0']));
        $right_line = abs(intval($begin_lines['1']));

        $add_line = false;
      }

      // we have a line of file content
      if ($add_line && !$skip_file && !$files[$i]['ended']) {
        $left_line++;
        $right_line++;

        // fix vertical line alignment when there is no number on the left or right side
        $add = str_pad("",strlen($left_line > $right_line ? $left_line : $right_line)," ");

        switch(substr($diff_line, 0, 1)) {
          case '+':
            $row_class = "line_added";
            $diff[$i]['lines'] .= $add.' | '.$right_line."\n";
            break;
          case '-':
            $row_class = "line_removed";
            $diff[$i]['lines'] .= $left_line." | ".$add."\n";
            break;
          case '!':
            $row_class = "wtf";
            $diff[$i]['lines'] .= $left_line." | ".$right_line."\n";
            break;

          default:
            $row_class = "default";
            $diff[$i]['lines'] .= $left_line." | ".$right_line."\n";
        }

        $diff[$i]['content'] .= "<span class=\"".$row_class."\">".clean($diff_line)." </span>";

        $line_number++;
      } //if
    } // foreach

    return $this->removeBinaryFiles(array_values($diff));
  } // parse diff content


  /**
   * Remove binary files from diff
   *
   * @param array $diff
   * @return array
   */
  function removeBinaryFiles($diff) {
    if (is_array($diff)) {
      foreach ($diff as $key=>$diff_item) {
        if (strpos($diff_item['content'], 'Cannot display: file marked as a binary type.') !== false) {
          unset($diff[$key]);
        } // if
      } // foreach
    } // if

    return $diff;
  } // remove binary files

  
  /**
   * Check if executable exists (if $path is provided system will check if executable exists in that folder, if not it will check system config option
   *
   * @param string $path
   * @return boolean
   */
  function executableExists($path = null) {
    $svn_path = '';
    if (!$path) {
      if (isset($this) && instance_of($this, 'RepositoryEngine')) {
        $svn_path = $this->executable_path;
      } else {
        $svn_path = ConfigOptions::getValue('source_svn_path');
      } // if
    } else {
        $svn_path = $path;
    } // if
    
    $svn_path = with_slash($svn_path);
    exec(escapeshellcmd($svn_path . 'svn --version --quiet'). " 2>&1", $output);
    $output = first($output);
    if ((boolean) version_compare($output, '1.0.0', '>')) {
      return true;
    } else {
      return $output;
    } // if
  } // if
  
  
  /**
   * Test connection by trying to retreive head revision
   *
   * @param null
   * @return void
   */
  function testRepositoryConnection() {
    $string = 'log -r HEAD --xml --verbose '.$this->active_repository->getUrl();
    $this->triggerred_by_handler = true;
    $this->execute($string);
    
    return is_null($this->error) ? true : $this->error;    
  } // testRepositoryConnection
  
  
  /**
   * Paths returned at the logs are relative to repository's ROOT url and
   * the repository needs to be queried with such paths, which makes the mess
   * in case that repository's root url added to activeCollab is actually
   * pointing to a subdirectory.
   * 
   * Contacatenating fails in that case and this method takes care of that by
   * prepending the repository's root url to the requested path.
   *
   * @param string $path
   * @return string
   */
  function getRealPath($path) {
    $path = str_replace("//", "/", $path); // weird issue that sometimes happens
    $path = str_replace(":/", "://", $path); // make http work again
    
    return str_replace(' ', '%20', without_slash($this->active_repository->getUrl()) . $path);
  } // getRealPath

  /**
   * Get formatted paths for notification e-mail
   *
   * @param array $grouped_paths
   * @return string
   */
  function getCommitTemplateBit($paths) {
    $details = "";
    
    $grouped_paths = $this->groupPaths($paths);
    if (is_foreachable($grouped_paths)) {
      foreach ($grouped_paths as $action=>$paths) {
        $details .= "\n<p><b>".clean(source_module_get_state_string($action))."</b></p><ul>\n";
        foreach ($paths as $path) {
          $details .= "<li>".clean($path)."</li>\n";
        } // foreach
        $details .= "</ul>\n";
      } // foreach
    } // if
    
    return $details;
  } // getCommitPathsTemplateBit

} // RepositoryEngine

?>