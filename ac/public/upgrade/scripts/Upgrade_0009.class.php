<?php

  /**
   * Update activeCollab 2.0.1 to activeCollab 2.0.2
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0009 extends UpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    var $from_version = '2.0.1';
    
    /**
     * Final system version
     *
     * @var string
     */
    var $to_version = '2.0.2';
    
    /**
     * Return script actions
     *
     * @param void
     * @return array
     */
    function getActions() {
    	return array(
    	  'backupEmailTemplates' => 'Backup email templates in /work folder',
    	  'updateEmailTemplates' => 'Update email templates',
    	  'updateArchivePages' => 'Update archived pages',
    	  'fixPageRevisionNums' => 'Fix page revision numbers',
    	);
    } // getActions
    
    /**
     * Create backup of existing email templates
     *
     * @param void
     * @return boolean
     */
    function backupEmailTemplates() {
      $splitter = "================================================================================\n";
      
      $templates_table = TABLE_PREFIX . 'email_templates';
      $translations_table = TABLE_PREFIX . 'email_template_translations';
      
      $result = '';
      
      $rows = $this->utility->db->execute("SELECT name, module, subject, body FROM $templates_table ORDER BY module, name");
      if(is_foreachable($rows)) {
        foreach($rows as $row) {
          $name = $row['name'];
          $module = $row['module'];
          $subject = $row['subject'];
          $body = $row['body'];
          
          $result .= "$module/$name: $subject\n";
          $result .= "$splitter\n";
          $result .= "$body\n\n$splitter";
          
          $translation_rows = $this->utility->db->execute("SELECT locale, subject, body FROM $translations_table WHERE name = ? AND module = ?", array($name, $module));
          if(is_foreachable($translation_rows)) {
            foreach($translation_rows as $translation_row) {
              $locale = $translation_row['locale'];
              $translation_subject = $translation_row['subject'];
              $translation_body = $translation_row['body'];
              
              $result .= "$module/$name/$locale: $translation_subject\n";
              $result .= "$splitter\n";
              $result .= "$translation_body\n\n$splitter";
            } // foreach
          } // if
        } // foreach
      } // if
      
      $work_path = ENVIRONMENT_PATH . '/work';
      $filename = 'email-templates-' . date('Y-m-d-H-i-s') . '.txt';
      
      return file_put_contents("$work_path/$filename", $result) ? true : "Failed to backup templates into /work/$filename";
    } // backupEmailTemplates
    
    /**
     * Update email templates
     *
     * @param void
     * @return boolean
     */
    function updateEmailTemplates() {
      $all_templates = array(
      
        // Discussions module templates
        'discussions' => array(
          'new_discussion' => array("[:project_name] Discussion ':object_name' has been started", "<p>Hi,</p>
<p><a href=\":created_by_url\">:created_by_name</a> has started a new discussion:</p>\n
:details_body\n
<p>Best,<br />:owner_company_name</p>", array('owner_company_name', 'project_name', 'project_url', 'object_type', 'object_name', 'object_body', 'object_url', 'created_by_name', 'created_by_url', 'last_comment_body', 'details_body')),
        ),
        
        // Files module templates
        'files' => array(
          'new_file' => array("[:project_name] File ':object_name' has been uploaded", "<p>Hi,</p>\n
<p><a href=\":created_by_url\">:created_by_name</a> has uploaded a new file:</p>\n
:details_body\n
<p>Best,<br />:owner_company_name</p>", array('owner_company_name', 'project_name', 'project_url', 'object_type', 'object_name', 'object_body', 'object_url', 'created_by_name', 'created_by_url', 'details_body')),
          'new_revision' => array("[:project_name] New version of ':object_name' file is up", "<p>Hi,</p>
<p><a href=\":created_by_url\">:created_by_name</a> has uploaded a new version of <a href=\":object_url\">:object_name</a> file.</p>
<p>Best,<br />:owner_company_name</p>", array('owner_company_name', 'project_name', 'project_url', 'object_type', 'object_name', 'object_body', 'object_url', 'created_by_url', 'created_by_name')), 
        ),
        
        // Pages module templates
        'pages' => array(
          'new_page' => array("[:project_name] Page ':object_name' has been created", "<p>Hi,</p>\n
<p><a href=\":created_by_url\">:created_by_name</a> has created a new page:</p>\n
:details_body\n
<p>Best,<br />:owner_company_name</p>", array('owner_company_name', 'project_name', 'project_url', 'object_type', 'object_name', 'object_body', 'object_url', 'created_by_name', 'created_by_url', 'details_body')),
          'new_revision' => array("[:project_name] Revision #:revision_num of ':old_name' page has been posted", "<p>Hi,</p>\n
<p><a href=\":created_by_url\">:created_by_name</a> has created a new version of <a href=\":old_url\">:old_name</a> page:</p>\n
:details_body\n
<p>Best,<br />:owner_company_name</p>", array('owner_company_name', 'project_name', 'project_url', 'object_type', 'object_name', 'object_body', 'object_url', 'created_by_url', 'created_by_name', 'revision_num', 'old_url', 'old_name', 'old_body', 'new_url', 'new_name', 'new_body', 'details_body'))),
          
        // Resources modules templates
        'resources' => array(
          'new_comment' => array("[:project_name] New comment on ':object_name' :object_type has been posted", "<p>Hi,</p>\n
<p><a href=\":created_by_url\">:created_by_name</a> has replied to <a href=\":object_url\">:object_name</a> :object_type:</p>\n
<hr />\n
:comment_body
<hr />\n
<p><a href=\":object_url\">:object_name</a> :object_type details:</p>
:details_body\n
<p>Best,<br />:owner_company_name</p>", array('owner_company_name', 'project_name', 'project_url', 'object_type', 'object_name', 'object_body', 'object_url', 'comment_body', 'comment_url', 'created_by_url', 'created_by_name', 'details_body')),
          
          'task_assigned' => array("[:project_name] New task has been posted", "<p>Hi,</p>\n
<p>We have a new assignment for you:</p>\n
:details_body\n
<p>Best,<br />:owner_company_name</p>", array('owner_company_name', 'project_name', 'project_url', 'object_type', 'object_name', 'object_body', 'object_url', 'created_by_name', 'created_by_url', 'details_body')),
          
          'task_reassigned' => array('[:project_name] Task reassigned', "<p>Hi,</p>\n
<p>We have an update that you might be interested in: :object_type <a href=\":object_url\">:object_name</a> has been updated. Changes:\n
:changes_body\n
<p>Best,<br />:owner_company_name</p>", array('owner_company_name', 'project_name', 'project_url', 'object_type', 'object_name', 'object_body', 'object_url', 'changes_body')),
          
          'task_completed' => array("[:project_name] ':object_name' :object_type has been completed", "<p>Hi,</p>\n
<p><a href=\":completed_by_url\">:completed_by_name</a> has completed :object_type <a href=\":object_url\">:object_name</a>:</p>\n
:details_body\n
<p>Best,<br />:owner_company_name</p>", array('owner_company_name', 'project_name', 'project_url', 'object_type', 'object_name', 'object_body', 'object_url', 'created_by_name', 'created_by_url', 'completed_by_name', 'completed_by_url', 'details_body')),
          
          'task_completed_with_comment' => array("[:project_name] ':object_name' :object_type has been completed", "<p>Hi,</p>\n
<p><a href=\":completed_by_url\">:completed_by_name</a> has completed :object_type <a href=\":object_url\">:object_name</a> with a comment:</p>\n
<hr />\n
:completion_comment_body\n
<hr />\n
<p><a href=\":object_url\">:object_name</a> :object_type details:</p>
:details_body\n
<p>Best,<br />:owner_company_name</p>", array('owner_company_name', 'project_name', 'project_url', 'object_type', 'object_name', 'object_body', 'object_url', 'created_by_name', 'created_by_url', 'completed_by_name', 'completed_by_url', 'completion_comment_body', 'details_body')),
          
          'task_reopened' => array("[:project_name] ':object_name' :object_type has been reopened", "<p>Hi,</p>\n
<p><a href=\":reopened_by_url\">:reopened_by_name</a> has reopened :object_type <a href=\":object_url\">:object_name</a>:</p>\n
:details_body\n
<p>Best,<br />:owner_company_name</p>", array('owner_company_name', 'project_name', 'project_url', 'object_type', 'object_name', 'object_body', 'object_url', 'created_by_name', 'created_by_url', 'reopened_by_name', 'reopened_by_url', 'details_body'))
        ),
        
        // System module templates
        'system' => array(
          'forgot_password' => array("Reset your password", "<p>Hi,</p>
<p>Visit <a href=\":reset_url\">this page</a> to reset your password. This page will be valid for 2 days!</p>
<p>Best,<br />:owner_company_name</p>", array('owner_company_name', 'reset_url')),
      
          'new_user' => array("An account for you has been created", "<p>Hi,</p>\n
<p><a href=\":created_by_url\">:created_by_name</a> has created a new account for you. You can <a href=\":login_url\">log in</a> with these parameters:</p>\n
<p>Email: ':email' (without quotes)<br />Password: ':password' (without quotes)</p>\n
<hr />\n
<p>:welcome_body</p>\n
<hr />\n
<p>Best,<br />:owner_company_name</p>", array('owner_company_name', 'created_by_id', 'created_by_name', 'created_by_url', 'email', 'password', 'login_url', 'welcome_body')),
      
          'reminder' => array("[:project_name] Reminder ':object_name' :object_type", "<p>Hi,</p>\n
<p><a href=\":reminded_by_url\">:reminded_by_name</a> wants you to check out <a href=\":object_url\">:object_name</a> :object_type. Comment:</p>\n
<hr />\n
<p>:comment_body</p>\n
<hr />\n
<p>Best,<br />:owner_company_name</p>", array('owner_company_name', 'reminded_by_name', 'reminded_by_url', 'object_name', 'object_url', 'object_type', 'comment_body', 'project_name', 'project_url')))
      );
      
      $modules_table = TABLE_PREFIX . 'modules';
      $templates_table = TABLE_PREFIX . 'email_templates';
      $translations_table = TABLE_PREFIX . 'email_template_translations';
      
      foreach($all_templates as $module_name => $templates) {
        if(array_var($this->utility->db->execute_one("SELECT COUNT(*) AS 'row_count' FROM $modules_table WHERE name = ?", array($module_name)), 'row_count')) {
          foreach($templates as $template_name => $template) {
            list($subject, $body, $variables) = $template;
            
            if(array_var($this->utility->db->execute_one("SELECT COUNT(*) AS 'row_count' FROM $templates_table WHERE name = ? AND module = ?", array($template_name, $module_name)), 'row_count')) {
              $this->utility->db->execute("UPDATE $templates_table SET subject = ?, body = ?, variables = ? WHERE name = ? AND module = ?", array($subject, $body, implode("\n", $variables), $template_name, $module_name));
              $this->utility->db->execute("DELETE FROM $translations_table WHERE name = ? AND module = ?", array($template_name, $module_name));
            } else {
              $this->utility->db->execute("INSERT INTO $templates_table (name, module, subject, body, variables) VALUES (?, ?, ?, ?, ?)", array($template_name, $module_name, $subject, $body, implode("\n", $variables)));
            } // if
          } // foreach
        } // if
      } // foreach
      
      return true;
    } // updateEmailTemplates
    
    /**
     * Switch completed on to is_archived for pages
     *
     * @param void
     * @return null
     */
    function updateArchivePages() {
      $update = $this->utility->db->execute("UPDATE " . TABLE_PREFIX . 'project_objects SET completed_on = NULL, completed_by_id = NULL, completed_by_name = NULL, completed_by_email = NULL, boolean_field_1 = ? WHERE module = ? AND type = ? AND completed_on IS NOT NULL', array(true, 'pages', 'Page'));
      if($update && !is_error($update)) {
        return true;
      } else {
        return 'Failed to updated archived pages. Reason: ' . $update->getMessage();
      } // if
    } // updateArchivePages
    
    /**
     * Fix page revision numbers
     *
     * @param void
     * @return boolean
     */
    function fixPageRevisionNums() {
      $page_versions_table = TABLE_PREFIX . 'page_versions';
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      
      if(in_array($page_versions_table, $this->utility->db->listTables(TABLE_PREFIX))) {
        $rows = $this->utility->db->execute("SELECT DISTINCT page_id FROM $page_versions_table WHERE version = '0'");
        if(is_foreachable($rows)) {
          $page_ids = array();
          foreach($rows as $row) {
            $page_id = (integer) $row['page_id'];
            $page_ids[] = $page_id;
            
            $version_rows = $this->utility->db->execute("SELECT version FROM $page_versions_table WHERE page_id = ? ORDER BY version DESC", array($page_id));
            if(is_foreachable($version_rows)) {
              foreach($version_rows as $version_row) {
                $version = (integer) $version_row['version'];
                
                $this->utility->db->execute("UPDATE $page_versions_table SET version = ? WHERE page_id = ? AND version = ?", array($version + 1, $page_id, $version));
              } // foreach
            } // if
          } // foreach
          
          if(is_foreachable($page_ids)) {
            $this->utility->db->execute("UPDATE $project_objects_table SET integer_field_1 = integer_field_1 + 1 WHERE id IN (?) AND type = ? AND integer_field_1 IS NOT NULL", array($page_ids, 'Page'));
          } // if
        } // if
      } // if
      
      $this->utility->db->execute("UPDATE $project_objects_table SET integer_field_1 = '1' WHERE type = ? AND integer_field_1 IS NULL", array('Page'));
      
      return true;
    } // fixPageRevisionNums
    
  }

?>