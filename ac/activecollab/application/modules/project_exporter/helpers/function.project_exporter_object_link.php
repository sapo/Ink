<?php

  /**
   * project_exporter_object_link helper
   * 
   * @package activeCollab.modules.project_exporter
   * @subpackage helpers
   */

  /**
   * Returns a link to specified object
   * 
   * - object- object for wich link will be generated
   * - url_prefix - prefix for url
   *
   * @param array $params
   * @param Smarty $smarty
   */
  function smarty_function_project_exporter_object_link($params, &$smarty) {
    $object = array_var($params, 'object');
    return '<a href="'.array_var($params, 'url_prefix', '').$object->getModule().'/'.strtolower($object->getType()).'_'.$object->getId().'.html">'.$object->getName().'</a>';
  } // smarty_function_project_exporter_object_link

?>