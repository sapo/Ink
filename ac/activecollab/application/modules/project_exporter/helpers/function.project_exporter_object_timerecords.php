<?php

  /**
   * object_time helper definition
   *
   * @package activeCollab.modules.project_exporter
   * @subpackage helpers
   */

  /**
   * Render object time widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_project_exporter_object_timerecords($params, &$smarty) {
    $timerecords = array_var($params, 'timerecords', null);

    $smarty->assign(array(
      '_project_exporter_object_timerecords'          => $timerecords,
      '_project_exporter_object_timerecords_total'    => array_var($params, 'total', 0),
    ));
    

    return $smarty->fetch(get_template_path('_project_exporter_object_timerecords', null, PROJECT_EXPORTER_MODULE));
  } // smarty_function_project_exporter_object_timerecords

?>