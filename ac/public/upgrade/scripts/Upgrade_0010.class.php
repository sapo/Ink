<?php

  /**
   * Update activeCollab 2.0.2 to activeCollab 2.0.3
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0010 extends UpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    var $from_version = '2.0.2';
    
    /**
     * Final system version
     *
     * @var string
     */
    var $to_version = '2.0.3';
    
    /**
     * Return script actions
     *
     * @param void
     * @return array
     */
    function getActions() {
    	return array(
    	  'updateExistingTables' => 'Update existing tables', 
    	  'backupEmailTemplates' => 'Backup email templates in /work folder',
    	  'updateEmailTemplates' => 'Update email templates',
    	  'fixIsPinnedFlag' => 'Fix is_pinned flag value for discussions',
    	);
    } // getActions
    
    /**
     * Update existing tables
     *
     * @param void
     * @return boolean
     */
    function updateExistingTables() {
      $tables = $this->utility->db->listTables(TABLE_PREFIX);
      
    	$changes = array(
    	  "alter table " . TABLE_PREFIX . "assignment_filters change user_filter_data user_filter_data text",
    	  "alter table " . TABLE_PREFIX . "assignment_filters change project_filter_data project_filter_data text",
    	  "alter table " . TABLE_PREFIX . "reminders change comment comment text",
    	);
    	
    	if(in_array(TABLE_PREFIX . 'status_updates', $tables)) {
    	  $changes[] = "alter table " . TABLE_PREFIX . "status_updates drop parent_id";
    	} // if
    	
    	foreach($changes as $change) {
    	  $update = $this->utility->db->execute($change);
    	  if(is_error($update)) {
    	    return $update->getMessage();
    	  } // if
    	} // foreach
    	
    	return true;
    } // updateExistingTables
    
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
        
        // Files module templates
        'files' => array(
          'new_file' => array("[:project_name] File ':object_name' has been uploaded", "<p>Hi,</p>\n
<p><a href=\":created_by_url\">:created_by_name</a> has uploaded a new file:</p>\n
:details_body\n
<p>Best,<br />:owner_company_name</p>", array('owner_company_name', 'project_name', 'project_url', 'object_type', 'object_name', 'object_body', 'object_url', 'created_by_name', 'created_by_url', 'details_body')),
        ),
          
        // Resources modules templates
        'resources' => array(
          'new_comment' => array("[:project_name] New comment on ':object_name' :object_type has been posted", "<p>Hi,</p>\n
<p><a href=\":created_by_url\">:created_by_name</a> has replied to <a href=\":object_url\">:object_name</a> :object_type:</p>\n
<hr />\n
:comment_body
<hr />\n
<p><a href=\":comment_url\">Click here</a> to see the comment. <a href=\":object_url\">:object_name</a> :object_type details:</p>
:details_body\n
<p>Best,<br />:owner_company_name</p>", array('owner_company_name', 'project_name', 'project_url', 'object_type', 'object_name', 'object_body', 'object_url', 'comment_body', 'comment_url', 'created_by_url', 'created_by_name', 'details_body')),
        
          'task_assigned' => array("[:project_name] New :object_type has been posted", "<p>Hi,</p>\n
<p><a href=\":created_by_url\">:created_by_name</a> created a new :object_type:</p>\n
:details_body\n
<p>Best,<br />:owner_company_name</p>", array('owner_company_name', 'project_name', 'project_url', 'object_type', 'object_name', 'object_body', 'object_url', 'created_by_name', 'created_by_url', 'details_body')),
          
          'task_reassigned' => array("[:project_name] ':object_name' :object_type reassigned", "<p>Hi,</p>\n
<p>We have an update that you might be interested in: :object_type <a href=\":object_url\">:object_name</a> has been updated. Changes:\n
:changes_body\n
<p>Best,<br />:owner_company_name</p>", array('owner_company_name', 'project_name', 'project_url', 'object_type', 'object_name', 'object_body', 'object_url', 'changes_body')),
        ),
        
        // System module templates
        'system' => array(
          'reminder' => array("[:project_name] :reminded_by_name sent you a reminder", "<p>Hi,</p>\n
<p><a href=\":reminded_by_url\">:reminded_by_name</a> wants you to check out <a href=\":object_url\">:object_name</a> :object_type. Comment:</p>\n
<hr />\n
<p>:comment_body</p>\n
<hr />\n
<p>Best,<br />:owner_company_name</p>", array('owner_company_name', 'reminded_by_name', 'reminded_by_url', 'object_name', 'object_url', 'object_type', 'comment_body', 'project_name', 'project_url'))
        )
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
     * Make sure that is_pinned flag has boolean value
     *
     * @param void
     * @return boolean
     */
    function fixIsPinnedFlag() {
      return $this->utility->db->execute("UPDATE " . TABLE_PREFIX . "project_objects SET boolean_field_1 = '0' WHERE type = 'Discussion' AND boolean_field_1 IS NULL");
    } // fixIsPinnedFlag
    
  }

?>