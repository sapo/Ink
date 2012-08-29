<?php

  /**
  * Return formated time
  *
  * @param string $content
  * @param integer $offset
  * @return string
  */
  function smarty_modifier_time($content, $offset = null) {
    if(instance_of($content, 'DateValue')) {
      $timestamp = $content->getTimestamp();
    } else {
      $timestamp = (integer) $content;
      if($timestamp == 0) {
        return lang('unknown time');
      } // if
    } // if
    
    if($offset === null) {
      $offset = get_user_gmt_offset();
    } // if
    
    $format = defined('USER_FORMAT_TIME') && USER_FORMAT_TIME ? USER_FORMAT_TIME : FORMAT_TIME;
    
    return strftime($format, $timestamp + $offset);
  } // smarty_modifier_time

?>