<?php

  /**
   * due helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Print due on string (due in, due today or late) for a given object
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_due($params, &$smarty) {
    $object = array_var($params, 'object');
    
    $due_date = null;
    if(instance_of($object, 'ProjectObject')) {
      if($object->can_be_completed) {
        if($object->isCompleted()) {
          return lang('Completed');
        } // if
        
        $due_date = $object->getDueOn();
      } else {
        return '--';
      } // if
    } elseif(instance_of($object, 'Invoice')) {
      if($object->getStatus() == INVOICE_STATUS_ISSUED) {
        $due_date = $object->getDueOn();
      } else {
        return '--';
      } // if
    } else {
      return new InvalidParamError('object', $object, '$object is not expected to be an instance of ProjectObject or Invoice class', true);
    } // if
      
    $offset = get_user_gmt_offset();
    
    if(instance_of($due_date, 'DateValue')) {
      require_once SMARTY_PATH . '/plugins/modifier.date.php';
      
      $date = smarty_modifier_date($due_date, 0); // just printing date, offset is 0!
      
      if($due_date->isToday($offset)) {
        return '<span class="today">' . lang('Due Today') . '</span>';
      } elseif($due_date->isYesterday($offset)) {
        return '<span class="late" title="' . clean($date) . '">' . lang('1 Day Late') . '</span>';
      } elseif($due_date->isTomorrow($offset)) {
        return '<span class="upcoming" title="' . clean($date) . '">' . lang('Due Tomorrow') . '</span>';
      } else {
        $now = new DateTimeValue();
        $now->advance($offset);
        $now = $now->beginningOfDay();
        
        $due_date->beginningOfDay();
        
        if($due_date->getTimestamp() > $now->getTimestamp()) {
          return '<span class="upcoming" title="' . clean($date) . '">' . lang('Due in :days Days', array('days' => floor(($due_date->getTimestamp() - $now->getTimestamp()) / 86400))) . '</span>';
        } else {
          return '<span class="late" title="' . clean($date) . '">' . lang(':days Days Late', array('days' => floor(($now->getTimestamp() - $due_date->getTimestamp()) / 86400))) . '</span>';
        } // if
      } // if
    } else {
      return lang('No Due Date');
    } // if
  } // smarty_function_due

?>