<?php
  /**
   * Class for generating output files for 
   * ProjectExporter module
   * 
   * @package activeCollab.modules.project_exporter
   * @subpackage models
   *
   */
  class ProjectExporterOutputBuilder extends AngieObject {
    
    /**
     * Project that needs to be exported
     *
     * @var Project
     */
    var $active_project;
    
    /**
     * Smarty
     * 
     * @var Smarty
     * 
     */
    var $smarty;
    
    /**
     * sidebar for output
     *
     * @var string
     */
    var $html_sidebar;
    
    /**
     * header for output
     *
     * @var string
     */
    var $html_header;
    
    /**
     * footer for output
     *
     * @var string
     */
    var $html_footer;
    
    /**
     * Output folder
     *
     * @var string
     */
    var $output_folder;
    
    /**
     * File template
     * 
     * @var string
     */
    var $file_template;
    
    /**
     * Module name
     *
     * @var string
     */
    var $module_name;
    
    /**
     * folder for file attachments
     *
     * @var string
     */
    var $attachments_folder = 'uploaded_files';
    
    /**
     * main folder
     *
     * @var string
     */
    var $main_output_folder;
    
    /**
     * Output builder execution log
     *
     * @var ProjectExporterExecutionLog;
     */
    var $execution_log;
    
    /**
     * Array that contains list of exportable modules
     *
     * @var array
     */
    var $exportable_modules;
        
    /**
     * Construct ProjectExporterOutputBilder
     *
     * @param Project $project
     * @param Smarty $smarty
     */
    function __construct(&$project, &$smarty, $module_name, $export_modules = null) {
      $this->active_project = $project;
      $this->smarty = $smarty;
      
      if ($module_name) {
        $this->smarty->assign('url_prefix', '../');
      } else {
        $this->smarty->assign('url_prefix', './');
      }
      
      $this->module_name = $module_name;
      
      $this->main_output_folder = PROJECT_EXPORT_PATH.'/project_'.$this->active_project->getId();
      
      $this->setExportableModules($export_modules);
      
      $this->smarty->assign(array(
        "active_project" => $project,
        'project_group' => $this->active_project->getGroup(),
        'project_company' => $this->active_project->getCompany(),
        'project_leader'  => $this->active_project->getLeader(),
        'active_module' => $module_name ? $module_name : 'system',
        'exporting_milestones' => in_array('milestones',$export_modules),
      ));      
      
      $this->html_footer = $this->get_footer();
      $this->html_header = $this->get_header();
      $this->html_sidebar = $this->get_sidebar();
      
      require_once(PROJECT_EXPORTER_MODULE_PATH.'/models/ProjectExporterExecutionLog.class.php');
      $this->execution_log = new ProjectExporterExecutionLog();
      
    } // __construct
    
    /**
     * Creates output folder
     *
     * @return boolean
     */
    function createOutputFolder($alternative_module = null) {
      $module_name = $alternative_module ? $alternative_module : $this->module_name;
      
      if ($module_name) {
        $export_dir = $this->main_output_folder .'/'.$module_name;
      } else {
        $export_dir = $this->main_output_folder ;
      }
      
      $this->output_folder = $export_dir;
      
      if (!is_dir($export_dir)) {
          $result = recursive_mkdir($export_dir,0777,WORK_PATH);
      } else {
        return true;
      }
      
      if (!$result) {
        $this->execution_log->addError(lang("Failed to create output folder: ".$export_dir));
        return false;
      }
      return true;
    } // createOutputFolder
    
    /**
     * Create attachments folder
     *
     * @return boolean
     */
    function createAttachmentsFolder() {
      $dir = $this->getAttachmentsOutputFolder();
      
      if (!is_dir($dir)) {
          $result = recursive_mkdir($dir,0777,WORK_PATH);
      } else {
        return true;
      } // if
      
      if (!$result) {
        $this->execution_log->addWarning(lang("Failed to create attachments folder"));
        return false;
      }
      
      return true;
    } // createAttachmentsFolder
    
    /**
     * Set list of exportable modules
     *
     * @param array $exportable_modules
     */
    function setExportableModules($exportable_modules) {
      $this->exportable_modules = $exportable_modules;
    } // setExportableModuless
    
    /**
     * Reset list of exportable modules
     *
     */
    function resetExportableModules() {
      unset($this->exeportable_modules);
    } // resetExportableModules
    
    /**
     * Returns list of exportable modules
     *
     * @return array
     */
    function getExportableModules() {
      return $this->exportable_modules;
    } // getExportableModules
    
    /**
     *  Returns a sidebar for project export
     * 
     *  @return string
     *
     */
    function get_sidebar() {
      $exportable_modules = array();
      event_trigger('on_project_export', array(&$exportable_modules, &$this->active_project));
      
      $modules = $this->getExportableModules();     
      
      if (is_foreachable($modules) && is_foreachable($exportable_modules)) {
        $final_modules = array();
        for ($x=0; $x<count($exportable_modules); $x++) {
          if (in_array($exportable_modules[$x]['module'], $modules)) {
            $final_modules[] = $exportable_modules[$x];
          } // if
        } // for
      } else {
        $final_modules = $exportable_modules;
      } // if

      $this->smarty->assign(array(
        '_project_exporter_exportable_modules' => $final_modules,
      ));
      
      $path = get_template_path('sidebar', 'export', PROJECT_EXPORTER_MODULE);
      return is_file($path) ? $this->smarty->fetch($path) : null;
    } // getSidebar
    
    
    /**
     * Returns a header for project export
     * 
     *  @return string
     *
     */
    function get_header() {
      $path = get_template_path('header', 'export', PROJECT_EXPORTER_MODULE);
      return is_file($path) ? $this->smarty->fetch($path) : null;
    } // export header
    
    /**
     * Returns a header for project export
     * 
     *  @return string
     *
     */
    function get_footer() {
      $path = get_template_path('footer', 'export', PROJECT_EXPORTER_MODULE);
      return is_file($path) ? $this->smarty->fetch($path) : null;
    } // export header
    
    /**
     * Returns output file path for $file_name
     *  
     * @param string $file_name
     * 
     * @return string
     */
    function getOutputFilePath($file_name) {
      return $this->output_folder.'/'.$file_name.'.html';
    } // getOutputFilePath
    
    /**
     * outputs content to file
     *
     * @param string $file_name
     */
    function outputToFile($file_name) {
      $output = $this->html_header . $this->html_sidebar . $this->smarty->fetch($this->getFileTemplate()) . $this->html_footer;
      $result = file_put_contents($this->getOutputFilePath($file_name), $output);
      if ($result === false) {
        $this->execution_log->addWarning(lang("Failed to create output file").": ".$this->getOutputFilePath($file_name));
      } // if
      return $result;
    } // outputToFile
    
    /**
     * Set output folder
     *
     * @param string $folder
     */
    function setOutputFolder($folder) {
      $this->output_folder = $folder;
    } // setOutputFolder
    
    /**
     * Set file template
     *
     * @param string $file_template
     */
    function setFileTemplate($module, $controller, $export_template) {
      $this->file_template = get_template_path('export/' . $export_template, $controller, $module);
    } // setFileTemplate
    
    /**
     * get file template path
     *
     * @return string
     */
    function getFileTemplate() {
      return $this->file_template;
    } // getFileTemplate
    
    /**
     * Get Attachments output folder
     * 
     * @return string
     *
     */
    function getAttachmentsOutputFolder() {
      return $this->main_output_folder . '/' . $this->attachments_folder;
    } // getAttachmentsOutputFolder
    
    /**
     * Outputs attachments for array of project Objects
     *
     * @param array $objects
     * @return boolean
     */
    function outputObjectsAttachments($objects) {
      if (is_foreachable($objects)) {
        foreach ($objects as $object) {
          $this->outputObjectAttachments($object);
        } // if       
        return true;
      } // if
      return false;
    } // outputObjectAttachment
    
    /**
     * output object attachments
     *
     * @param ProjectObject $object
     * @return boolean
     */
    function outputObjectAttachments($object) {
      if (instance_of($object, 'ProjectObject')) {
        return $this->outputAttachments($object->getAttachments());
      } // if
    } // outputObjectAttachments
    
    /**
     * Outputs array of attachments
     *
     * @param array $attachments
     * @return boolean
     */
    function outputAttachments($attachments) {
      if (is_foreachable($attachments)) {
        foreach ($attachments as $attachment) {
        	$this->outputAttachment($attachment);
        } // foreach
        return true;
      } // if
      return false;
    } // outputAttachments
    
    /**
     * Outputs the attachment
     *
     * @param Attachment $attachment
     * @return boolean
     */
    function outputAttachment($attachment) {
      if (!instance_of($attachment, 'Attachment')) {
        return false;
      }
      $result = copy($attachment->getFilePath(), $this->getAttachmentsOutputFolder() . '/'.$attachment->getId().'_'.$attachment->getName());
	    if ($result===false) {
        $this->execution_log->addWarning(lang("Failed to copy file").": ".$attachment->getFilePath());
      } // if
      return $result;
    } // otputAttachment
    
    /**
     * returns root output folder
     *
     * @return string
     */
    function getRootOutputFolder() {
      return $this->main_output_folder;
    } // getRootOutputFolder
    
    /**
     * Outputs selected style to output folder
     *
     * @param string $style_name
     * @return boolean
     */
    function outputStyle($style_name = 'default') {
      $style_path = PROJECT_EXPORTER_MODULE_PATH . '/models/styles/'.$style_name;
      if (!is_dir($style_path)) {
        $this->execution_log->addWarning(lang('Style does not exists'));
        return false;
      } // if
      
      $output_style_folder = $this->getRootOutputFolder().'/style';
      if (!recursive_mkdir($output_style_folder, 0777, WORK_PATH)) {
        $this->execution_log->addWarning(lang('Could not create style directory'));
        return false;
      } // if
      
      return copy_dir($style_path, $output_style_folder);
    } // outputStyle
    
    /**
     * Returns output file path
     *
     * @return string
     */
    function getOutputArchivePath() {
      return PROJECT_EXPORT_PATH.'/project_'.$this->active_project->getId().'.zip';
    } // getOutputArchivePath
    
    /**
     * Compress all exported data and remove temporary files
     * 
     * @param void
     * @return void
     */
    function compressOutput() {
      require_once(ANGIE_PATH.'/classes/Zip.class.php');
      if (!CAN_USE_ZIP) {
        $this->execution_log->addWarning(lang('Zlib extension not loaded, could not create archive file :archive. Exported files are available in folder :folder', array("archive" => $this->getOutputArchivePath(), "folder" => $this->getRootOutputFolder())));
        return false;
      } // if
      ini_set('memory_limit', -1);
      if (ini_get('memory_limit') != -1) {
        $this->execution_log->addWarning(lang('Could not create archive file :archive. Exported files are available in folder :folder', array("archive" => $this->getOutputArchivePath(), "folder" => $this->getRootOutputFolder())));
        return false;
      } // if
      set_time_limit(0);
      $zip = new Zip();
      $zip->addDir($this->getRootOutputFolder(), '');
      if (!$zip->output($this->getOutputArchivePath())) {
        $this->execution_log->addWarning(lang('Could not create archive file :archive. Exported files are available in folder :folder', array("archive" => $this->getOutputArchivePath(), "folder" => $this->getRootOutputFolder())));
      } else {
        safe_delete_dir($this->getRootOutputFolder(), PROJECT_EXPORT_PATH);
      } // if
    } // compressOutput
    
    /**
     * Copies project icon to export folder
     *
     * @param void
     * @return void
     * 
     */
    function outputProjectIcon() {
      $path = $this->active_project->getIconPath(true);
      $path = is_file($path) ? $path : PUBLIC_PATH . "/projects_icons/default.40x40.gif";
      $result = copy($path, $this->getAttachmentsOutputFolder().'/project_logo.gif');
	    if ($result===false) {
        $this->execution_log->addWarning(lang("Failed to copy project icon from").": ".$this->active_project->getIconPath(true)." ".lang('to')." ".$this->getAttachmentsOutputFolder().'/project_logo.gif');
      } // if
      return true;
    } // outputProjectIcon
    
  } // ProjectExporterOutputBuilder
?>