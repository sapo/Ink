<?php

  /**
   * object_star helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render star for a given object
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_star($params, &$smarty) {
    static $ids = array();
    
    $object = array_var($params, 'object');
    if(!instance_of($object, 'ProjectObject')) {
      return new InvalidParamError('object', $object, '$object is not valid instance of ProjectObject class', true);
    } // if
    
    $user = array_var($params, 'user');
    if(!instance_of($user, 'User')) {
      return new InvalidParamError('user', $user, '$user is expected to be an instance of User class', true);
    } // if
    
    $id = array_var($params, 'id');
    if(empty($id)) {
      do {
        $id = 'object_star_' . make_string(40);
      } while(in_array($id, $ids));
    } // if
    
    $ids[] = $id;
    
    if($object->can_be_starred) {
      if($object->isStarred($user)) {
        $params = array(
          'id'    => $id,
          'href'  => $object->getUnstarUrl(),
          'title' => lang('Unstar this object'),
          'class' => 'object_star'
        );
        
        $result = open_html_tag('a', $params) . '<img src="' . get_image_url('icons/star-small.gif') . '" alt="" /></a>';
      }  else {
        $params = array(
          'id'    => $id,
          'href' => $object->getStarUrl(),
          'title' => lang('Star this object'),
          'class' => 'object_star',
        );
        
        $result = open_html_tag('a', $params) . '<img src="' . get_image_url('icons/unstar-small.gif') . '" alt="" /></a>';
      } // if
      
      return $result . "\n<script type=\"text/javascript\">App.layout.init_star_unstar_link('" . $id . "')</script>";
    } else {
      return '';
    } // if
  } // smarty_function_object_star

?>