<?php

  /**
   * action_on_by helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Renders action string with time when action was taken and link to profile of 
   * user who acted
   * 
   * Parameteres:
   * 
   * - action - Action string, default is 'Posted'. It is used for lang retrival
   * - user - User who took the action. Can be registered user or anonymous user
   * - datetime - Datetime object when action was taken
   * - format - Format in with time is displayed. Possible values are ago, 
   *   datetime, date and time. Default is 'ago'
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_action_on_by($params, &$smarty) {
    $action = clean(array_var($params, 'action', 'Posted'));
    
    $datetime = array_var($params, 'datetime');
    if(!instance_of($datetime, 'DateValue')) {
      return new InvalidParamError('datetime', $datetime, '$datetime is expected to be an instance of DateValue or DateTimeValue class', true);
    } // if
    
    $format = array_var($params, 'format', 'ago');
    if(!in_array($format, array('ago', 'date', 'datetime', 'time'))) {
      return new InvalidParamError('format', $format, 'Format is requred to be one of following four values: ago, date, datetime or time', true);
    } // if
    
    $offset = array_var($params, 'offset', null);
    
    switch($format) {
      case 'date':
        require_once SMARTY_DIR . '/plugins/modifier.date.php';
        $formatted_datetime = smarty_modifier_date($datetime, $offset);
        break;
      case 'time':
        require_once SMARTY_DIR . '/plugins/modifier.time.php';
        $formatted_datetime = smarty_modifier_time($datetime, $offset);
        break;
      case 'datetime':
        require_once SMARTY_DIR . '/plugins/modifier.datetime.php';
        $formatted_datetime = smarty_modifier_datetime($datetime, $offset);
        break;
      default:
        require_once SMARTY_DIR . '/plugins/modifier.ago.php';
        $formatted_datetime = smarty_modifier_ago($datetime, $offset);
    } // switch
    
    $user = array_var($params, 'user');
    
    if(instance_of($user, 'User')) {
      return lang($action) . ' ' . $formatted_datetime . ' ' . lang('by') . ' <a href="'. $user->getViewUrl() .'">' . clean($user->getDisplayName()) . '</a>';
    } elseif(instance_of($user, 'AnonymousUser')) {
      return lang($action) . ' ' . $formatted_datetime . ' ' . lang('by') . ' <a href="mailto:'. $user->getEmail() .'">' . clean($user->getName()) . '</a>';
    } else {
      return lang($action) . ' ' . $formatted_datetime . ' ' . lang('by unknown user');
    } // if
  } // smarty_function_action_on_by

?>