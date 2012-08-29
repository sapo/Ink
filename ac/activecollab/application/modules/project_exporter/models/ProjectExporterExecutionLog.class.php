<?php
  /**
   * Project Exporter Execution Log class
   *
   * @package activeCollab.modules.project_exporter
   * @subpackage models
   */
  class ProjectExporterExecutionLog extends AngieObject {
  
    /**
     * Array container for execution log
     *
     * @var array;
     */
    var $log = array();
    
    /**
     * Number of error messages
     *
     * @var integer;
     */
    var $error_count = 0;
    
    /**
     * Construct ProjectExporterExecutionLog
     *
     * @param void
     * @return ProjectExporterExecutionLog
     */
    function __construct() {
      
    } // __construct
    
    /**
     * Add error information in execution log
     *
     * @param string $error_string
     * @return void
     */
    function addError($error_string) {
      $this->log[] = array(
        "status" => 0,
        "message" => $error_string,
      );
      $this->error_count++;
    } // addError
    
    /**
     * Add success information in execution log
     *
     * @param string $success_string
     * @return void
     */
    function addSuccess($success_string) {
      $this->log[] = array(
        "status" => 2,
        "message" => $success_string,
      );
    } // addSuccess
    
    /**
     * Add warning information in execution log
     *
     * @param string $warning_string
     */
    function addWarning($warning_string) {
      $this->log[] = array(
        "status" => 1,
        "message" => $warning_string,
      );
    } // addWarning
    
    /**
     * Returns execution log
     *
     * @param void
     * @return array
     */
    function getExecutionLog() {
      return $this->log;
    }
    
  } // ProjectExporterExecutionLog
        
    
?>