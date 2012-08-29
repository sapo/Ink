<?php

  /**
   * mobile_access_object_comments
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage helpers
   */

  /**
   * Render comments
   * 
   * Parameters:
   * 
   * - comments - comments that needs to be rendered
   * - page - current_page
   * - total_pages - total pages
   * - counter - counter for comment #
   * - url - base URL for link assembly
   * - parent - parent object
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_mobile_access_object_comments($params, &$smarty) {
    $url_params = '';
    if (is_foreachable($params)) {
      foreach ($params as $k => $v) {
      	if (strpos($k, 'url_param_')!== false && $v) {
      	  $url_params.='&amp;'.substr($k, 10).'='.$v;
      	} // if
      } // foreach
    } // if
    
    $object = array_var($params, 'object');
    if(!instance_of($object, 'ProjectObject')) {
      return new InvalidParamError('object', $object, '$object is expected to be an instance of ProjectObject class', true);
    } // if
    
    $user = array_var($params, 'user');
    if(!instance_of($user, 'User')) {
      return new InvalidParamError('object', $user, '$user is expected to be an instance of User class', true);
    } // if
    
    $page = array_var($params, 'page', 1);
    $page = (integer) array_var($_GET, 'page');
    if($page < 1) {
      $page = 1;
    } // if
    
    $counter = array_var($params, 'counter', 1);
    $counter = ($page-1) * $object->comments_per_page + $counter;
    
    list($comments, $pagination) = $object->paginateComments($page, $object->comments_per_page, $user->getVisibility());
        
    $smarty->assign(array(
      "_mobile_access_comments_comments" => $comments,
      "_mobile_access_comments_paginator" => $pagination,
      "_mobile_access_comments_url"  => mobile_access_module_get_view_url($object),
      "_mobile_access_comments_url_params" => $url_params,
      "_mobile_access_comments_counter" => $counter,
      "_mobile_access_comments_show_counter" => array_var($params, 'show_counter', true),
    ));
    
    return $smarty->fetch(get_template_path('_object_comments', null, MOBILE_ACCESS_MODULE));
  } // smarty_function_mobile_access_object_comments

?>