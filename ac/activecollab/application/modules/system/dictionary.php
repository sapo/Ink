<?php

  /**
   * System module language index
   *
   * @package activeCollab.modules.system
   */
  
  // words from system module
  $lang_index = require(dirname(__FILE__) . '/lang_index.php');
  
  // words from angie
  $lang_index_common = require(dirname(__FILE__) . '/lang_index_common.php');
  
  $lang_index_js = require dirname(__FILE__) . '/lang_index_js.php';
  
  $lang_index_js_common = require dirname(__FILE__) . '/lang_index_js_common.php';
  
  // additional words not specified anywhere else
  $additional_words = array(
    'Created',
  	'Completed',
  	'Reopened',
  	'Moved to Trash',
  	'New revision',
  	'Restored from Trash',
  	'New version',
  	'Uploaded',
  	'Posted',
  	'Pinned',
  	'Unpinned',
  	'Locked',
  	'Unlocked',
  	'Added',
  	'Last time updated',
  	'Updated',
  	'Started',
    'Attachment',
    'Category',
    'Checklist',
    'Comment',
    'Discussion',
    'File',
    'Milestone',
    'Page',
    'Task',
    'Ticket',
    'Timerecord',
    'attachment',
    'category',
    'checklist',
    'comment',
    'discussion',
    'file',
    'milestone',
    'page',
    'task',
    'ticket',
    'timerecord',
    'System',
    'Mail',
    'Tools',
    'Other',
    EMAIL_SPLITTER
  );
  
  return array_unique(array_merge($lang_index, $lang_index_common, $additional_words, $lang_index_js, $lang_index_js_common));
?>