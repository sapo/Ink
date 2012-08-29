<?php

  /**
   * Routes file for system module
   *
   * @package activeCollab.modules.discussions
   */
  
  $this->map('project_discussions', 'projects/:project_id/discussions', array('module' => DISCUSSIONS_MODULE, 'controller' => 'discussions', 'action' => 'index'), array('project_id' => '\d+'));
  $this->map('project_discussions_add', 'projects/:project_id/discussions/add', array('module' => DISCUSSIONS_MODULE, 'controller' => 'discussions', 'action' => 'add'), array('project_id' => '\d+'));
  $this->map('project_discussions_quick_add', 'projects/:project_id/discussions/quick-add', array('module' => DISCUSSIONS_MODULE, 'controller' => 'discussions', 'action' => 'quick_add'), array('project_id' => '\d+'));
  $this->map('project_discussions_export', 'projects/:project_id/discussions/export', array('module' => DISCUSSIONS_MODULE, 'controller' => 'discussions', 'action' => 'export'), array('project_id' => '\d+'));
   
  $this->map('project_discussion', 'projects/:project_id/discussions/:discussion_id', array('module' => DISCUSSIONS_MODULE, 'controller' => 'discussions', 'action' => 'view'), array('project_id' => '\d+', 'discussion_id' => '\d+'));
  $this->map('project_discussion_edit', 'projects/:project_id/discussions/:discussion_id/edit', array('module' => DISCUSSIONS_MODULE, 'controller' => 'discussions', 'action' => 'edit'), array('project_id' => '\d+', 'discussion_id' => '\d+'));
  
  $this->map('project_discussion_pin', 'projects/:project_id/discussions/:discussion_id/pin', array('module' => DISCUSSIONS_MODULE, 'controller' => 'discussions', 'action' => 'pin'), array('project_id' => '\d+', 'discussion_id' => '\d+'));
  $this->map('project_discussion_unpin', 'projects/:project_id/discussions/:discussion_id/unpin', array('module' => DISCUSSIONS_MODULE, 'controller' => 'discussions', 'action' => 'unpin'), array('project_id' => '\d+', 'discussion_id' => '\d+'));

?>