<?php

  /**
   * Update activeCollab 2.1.4 to activeCollab 2.2.1
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0016 extends UpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    var $from_version = '2.1.4';
    
    /**
     * Final system version
     *
     * @var string
     */
    var $to_version = '2.2.1';
    
    /**
     * Return script actions
     *
     * @param void
     * @return array
     */
    function getActions() {
    	return array(
    	  'updateInvoicingConfigOptions' => 'Update invoicing configuration options',
    	  'updateEmailTemplates' => 'Update Source module email templates', 
    	);
    } // getActions
    
    /**
     * Update parent type for old first discussion comments to Discussion
     *
     * @param void
     * @return boolean
     */
    function updateInvoicingConfigOptions() {
      if(array_var($this->utility->db->execute_one("SELECT COUNT(*) AS 'row_count' FROM " . TABLE_PREFIX . "modules WHERE name = 'invoicing'"), 'row_count') == 1) {
        $config_options_table = TABLE_PREFIX . 'config_options';
        
        $owner_company_id = null;
        $owner_company_name = null;
        $owner_company_address = null;
        
        $row = $this->utility->db->execute_one('SELECT id, name FROM ' . TABLE_PREFIX . 'companies WHERE is_owner = ? LIMIT 1', array(true));
        if(is_array($row) && isset($row['id']) && isset($row['name'])) {
          $owner_company_id = (integer) $row['id'];
          $owner_company_name = trim($row['name']);
        } // if
        
        if($owner_company_id) {
          $row = $this->utility->db->execute_one('SELECT value FROM ' . TABLE_PREFIX . 'company_config_options WHERE company_id = ? AND name = ?', array($owner_company_id, 'office_address'));
          if(is_array($row) && isset($row['value'])) {
            $owner_company_address = unserialize($row['value']);
          } // if
        } // if
        
        $company_details = array(
          'name' => $owner_company_name ? $owner_company_name : 'Owner Company',
          'details' => $owner_company_address ? $owner_company_address : null,
          'company_logo' => 'invoicing_logo.jpg', // [?]
        );
        $pdf_settings = array(
          'paper_format' => 'A4',
          'paper_orientation' => 'Portrait',
          'header_text_color' => '000000',
          'page_text_color' => '000000',
          'border_color' => '000000',
          'background_color' => 'c2c2c2',
        );
        $notes = null;
        
        $rows = $this->utility->db->execute_all("SELECT name, value FROM $config_options_table WHERE name IN ('invoicing_company_details', 'invoicing_notes', 'invoicing_pdf_settings')");
        if(is_foreachable($rows)) {
          foreach($rows as $row) {
            switch($row['name']) {
              case 'invoicing_company_details':
                $company_details = unserialize($row['value']);
                break;
              case 'invoicing_pdf_settings':
                $pdf_settings = unserialize($row['value']);
                break;
              case 'invoicing_notes':
                $notes = unserialize($row['value']);
                break;
            } // switch
          } // foreach
        } // if
        
        $this->utility->db->execute("DELETE FROM $config_options_table WHERE name IN ('invoicing_company_details', 'invoicing_notes', 'invoicing_pdf_settings')");
        
        $new_config_options = array(
          'invoicing_company_name' => $company_details['name'],
          'invoicing_company_details' => $company_details['details'],
          'invoicing_pdf_paper_format' => $pdf_settings['paper_format'],
          'invoicing_pdf_paper_orientation' => $pdf_settings['paper_orientation'],
          'invoicing_pdf_header_text_color' => $pdf_settings['header_text_color'],
          'invoicing_pdf_page_text_color' => $pdf_settings['page_text_color'],
          'invoicing_pdf_border_color' => $pdf_settings['border_color'],
          'invoicing_pdf_background_color' => $pdf_settings['background_color'],
        );
        
        $to_insert = array();
        foreach($new_config_options as $name => $value) {
          $to_insert[] = $this->utility->db->prepareSQL("(?, 'invoicing', 'system', ?)", array($name, serialize($value)));
        } // foreach
        
        $this->utility->db->execute("INSERT INTO $config_options_table (name, module, type, value) VALUES " . implode(', ', $to_insert));
      } // if
      return true;
    } // updateInvoicingConfigOptions
    
    /**
     * Update email template for Source module
     *
     * @param void
     * @return boolean
     */
    function updateEmailTemplates() {
      $modules_table = TABLE_PREFIX . 'modules';
      $templates_table = TABLE_PREFIX . 'email_templates';
      $translations_table = TABLE_PREFIX . 'email_template_translations';
      
      if(array_var($this->utility->db->execute_one("SELECT COUNT(*) AS 'row_count' FROM $modules_table WHERE name = ?", array('source')), 'row_count')) {
        $template = array("[:project_name] ':object_name' :object_type has just been updated", ':details_body
<p>Hi,</p>
<p>:object_type :object_name at :project_name project has just been updated with :commit_count new commits</p>
<div>:commits_body</div>
<p>Best,<br />:owner_company_name</p>', array('commits_body', 'details_body', 'project_name', 'object_name', 'object_type', 'object_url', 'project_url', 'commit_count'));
        
        list($subject, $body, $variables) = $template;

        if(array_var($this->utility->db->execute_one("SELECT COUNT(*) AS 'row_count' FROM $templates_table WHERE name = 'repository_updated' AND module = 'source'"), 'row_count')) {
          $this->utility->db->execute("UPDATE $templates_table SET body = ?, variables = ? WHERE name = 'repository_updated' AND module = 'source'", array($body, implode("\n", $variables)));
          $this->utility->db->execute("DELETE FROM $translations_table WHERE name = 'repository_updated' AND module = 'source'");
        } else {
          $this->utility->db->execute("INSERT INTO $templates_table (name, module, subject, body, variables) VALUES (?, ?, ?, ?, ?)", array('repository_updated', 'source', $subject, $body, implode("\n", $variables)));
        } // if
      } // if
      
      return true;
    } // updateEmailTemplates
    
  }

?>