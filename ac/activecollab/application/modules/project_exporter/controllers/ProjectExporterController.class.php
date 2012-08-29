<?php

  // We need projects controller
  use_controller('project', SYSTEM_MODULE);

  /**
   * Project Exporter controller
   *
   * @package activeCollab.modules.project_exporter
   * @subpackage controllers
   */
  class ProjectExporterController extends ProjectController {
    
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'project_exporter';
    
    /**
     * Active module
     *
     * @var string
     */
    var $active_module = PROJECT_EXPORTER_MODULE;
    
    /**
     * Constructor
     *
     * @param Request $request
     * @return TicketsController
     */
    function __construct($request) {
      parent::__construct($request);
      
      if(!$this->logged_user->isAdministrator() && !$this->logged_user->isProjectLeader($this->active_project) && !$this->logged_user->isProjectManager()) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $this->wireframe->print_button = false;
    }
    
    /**
     * Index - main page for project exporter
     *
     * @param void
     * @return null
     */
    function index() {      
      $is_writable = folder_is_writable(PROJECT_EXPORT_PATH);
      if (!$is_writable) {
        $this->wireframe->addPageMessage(lang("Folder <strong>:folder</strong> is not writable", array("folder"=>PROJECT_EXPORT_PATH)), PAGE_MESSAGE_ERROR);
        $this->smarty->assign(array(
          'is_writable' => false,
        ));
      } else {        
        $exportable_modules = array();
        event_trigger('on_project_export', array(&$exportable_modules, &$this->active_project));
        
        require_once(PROJECT_EXPORTER_MODULE_PATH.'/models/ProjectExporterOutputBuilder.class.php');
        $output_builder = new ProjectExporterOutputBuilder($this->active_project, $this->smarty, null);
    
        if (is_file($output_builder->getOutputArchivePath())) {
          $this->wireframe->addPageMessage(lang('Previous project archive already exists. You can download it using following <a href=":link"><strong>link</strong></a>', array('link' => assemble_url('project_exporter_download_export', array('project_id'=>$this->active_project->getId())))), PAGE_MESSAGE_INFO);
        } // if
        $this->smarty->assign(array(
          'visibility_normal_caption'   => lang('Only the data clients can'),
          'visibility_private_caption'  => lang('All project data, including data marked as private'),
          'exportable_modules'          => $exportable_modules,
          "project"                     => $this->active_project,
          'export_project_url'          => assemble_url('project_exporter' , array('project_id' => $this->active_project->getId())),
          'submitted'                   => false,
          'is_writable'                 => true,
        ));
        js_assign('download_url', assemble_url('project_exporter_download_export', array('project_id'=>$this->active_project->getId())));
        js_assign('download_ftp_url', PROJECT_EXPORT_PATH .'/project_' . $this->active_project->getId() . '/');
        
      }
    } // index
    
    
    /**
     * Finalize project export (compressing and cleanup)
     * 
     * @param void
     * @return null
     */
    function finish() {
        require_once(PROJECT_EXPORTER_MODULE_PATH.'/models/ProjectExporterOutputBuilder.class.php');
        $compress = $this->request->get('compress');
        $output_builder = new ProjectExporterOutputBuilder($this->active_project, $this->smarty, null);
        if ($compress) {
          $output_builder->compressOutput();
        } // if
        $this->serveData($output_builder->execution_log, 'execution_log', null, FORMAT_JSON);
    } // finish
    
    /**
     * Download exported data
     * 
     * @param void
     * @return null
     */
    function download() {
      require_once(PROJECT_EXPORTER_MODULE_PATH.'/models/ProjectExporterOutputBuilder.class.php');
      $output_builder = new ProjectExporterOutputBuilder($this->active_project, $this->smarty, null);
      $filename = $output_builder->getOutputArchivePath();
      
      if (!is_file($filename)) {
        $this->httpError(HTTP_NOT_FOUND);
      } // if
      
      download_file($filename, 'application/zip', basename($filename), true);
    } // download
     
  }

?>