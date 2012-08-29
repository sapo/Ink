<?php

  /**
  * Render submit button
  *
  * @param array $params
  * @param string $content
  * @param Smarty $smarty
  * @param boolean $repeat
  * @return string
  */
  function smarty_block_submit($params, $content, &$smarty, &$repeat) {
    $params['type'] = 'submit';
    $accesskey = array_var($params, 'accesskey', 's');
    if($accesskey) {
      $params['accesskey'] = 's';
    } // if
    
    $caption = clean(isset($params['not_lang']) ? $content : lang($content));
    
    if($accesskey) {
      $first = null;
      $first_pos = null;
      
      $to_highlight = array(strtolower($accesskey), strtoupper($accesskey));
      foreach($to_highlight as $accesskey_to_highlight) {
        if(($pos = strpos($caption, $accesskey_to_highlight)) === false) {
          continue;
        } // if
        
        if(($first_pos === null) || ($pos < $first_pos)) {
          $first = $accesskey_to_highlight;
          $first_pos = $pos;
        } // if
      } // foreach
      
      if($first !== null) {
        $caption = str_replace_first($first, "<u>$first</u>", $caption);
      } // if
    } // if
    
    // And done...
    return open_html_tag('button', $params) .'<span><span>'. $caption . '</span></span></button>';
  } // smarty_block_submit

?>