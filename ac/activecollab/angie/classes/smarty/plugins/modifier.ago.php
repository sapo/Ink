<?php

  /**
   * Return '*** ago' message
   *
   * @param DateTimeValue $input
   * @param integer $offset
   * @return string
   */
  function smarty_modifier_ago($input, $offset = null) {
    if(!instance_of($input, 'DateValue')) {
      return '<span class="ago">'.lang('-- Unknown --').'</span>';
    } // if
    
    if($offset === null) {
      $offset = get_user_gmt_offset();
    } // if
    
    $datetime = new DateTimeValue($input->getTimestamp() + $offset);
    $reference = new DateTimeValue(time() + $offset);
    
    $diff = $reference->getTimestamp() - $datetime->getTimestamp();
    
    // Get exact number of seconds between current time and yesterday morning
    $reference_timestamp = $reference->getTimestamp();
    $yesterday_begins_at = 86400 + (date('G', $reference_timestamp) * 3600) + (date('i', $reference_timestamp) * 60) + date('s', $reference_timestamp);
    
    if($diff < 60) {
      $value = lang('Few seconds ago');
    } elseif($diff < 120) {
      $value = lang('A minute ago');
    } elseif($diff < 3600) {
      $value = lang(':num minutes ago', array('num' => floor($diff / 60)));
    } elseif($diff < 7200) {
      $value = lang('An hour ago');
    } elseif($diff < 86400) {
      if(date('j', $datetime->getTimestamp()) != date('j', $reference->getTimestamp())) {
        $value = lang('Yesterday');
      } else {
        $mod = $diff % 3600;
        if($mod < 900) {
          $value = lang(':num hours ago', array('num' => floor($diff / 3600)));
        } elseif($mod > 2700) {
          $value = lang(':num hours ago', array('num' => ceil($diff / 3600)));
        } else {
          $value = lang(':num and a half hours ago', array('num' => floor($diff / 3600)));
        } // if
      } // if
    } elseif($diff <= $yesterday_begins_at) {
      $value = lang('Yesterday');
    } elseif($diff < 2592000) {
      $value = lang(':num days ago', array('num' => floor($diff / 86400)));
    } else {
      require_once SMARTY_PATH . '/plugins/modifier.date.php';
      require_once SMARTY_PATH . '/plugins/modifier.datetime.php';
      return '<span class="ago" title="' . clean(smarty_modifier_datetime($datetime, 0)) . '">' . lang('On') . ' ' . smarty_modifier_date($datetime, 0) . '</span>';
    } // if
    
    require_once SMARTY_PATH . '/plugins/modifier.datetime.php';
    return '<span class="ago" title="' . clean(smarty_modifier_datetime($datetime, 0)) . '">' . $value . '</span>';
  } // smarty_modifier_ago

?>