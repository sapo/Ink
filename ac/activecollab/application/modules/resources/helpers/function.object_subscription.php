<?php

  /**
   * Render object subscriptions icon and link
   * 
   * Params:
   * 
   * - object - Object user needs to be subscribed to
   * - user - User who is subscribed, if not set logged user is used
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_subscription($params, &$smarty) {
    $object = array_var($params, 'object');
    if(!instance_of($object, 'ProjectObject')) {
      return new InvalidParamError('$object', $object, '$object is expected to be a valid instance of ProjectObject class', true);
    } // if
    
    $user = array_var($params, 'user');
    if(!instance_of($user, 'User')) {
      return new InvalidParamError('$user', $user, '$user is expected to be a valid instance of User class', true);
    } // if
    
    $render_wrapper = (boolean) array_var($params, 'render_wrapper', true);
    
    $wrapper_id = 'object_subscription_widget_' . $object->getId() . '_user_' . $user->getId();
    
    require_once SMARTY_PATH . '/plugins/block.link.php';
    if(Subscriptions::isSubscribed($user, $object)) {
      $content = '<a href="' . $object->getUnsubscribeUrl($user) . '" title="' . lang('Click to unsubscribe') . '"><img src="' . get_image_url('subscribe-small.gif') . '" alt="" /></a>';
    } else {
      $content = '<a href="' . $object->getSubscribeUrl($user) . '" title="' . lang('Click to subscribe') . '"><img src="' . get_image_url('gray-subscribe-small.gif') . '" alt="" /></a>';
    } // if
    
    $content .= '<script type="text/javascript">App.layout.init_subscribe_unsubscribe_link("' . $wrapper_id . '")</script>';
    
    return $render_wrapper ? '<span id="' . $wrapper_id . '">' . $content . '</span>' : $content;
  } // smarty_function_object_subscription

?>