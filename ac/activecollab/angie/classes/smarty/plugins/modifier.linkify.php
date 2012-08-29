<?php

  /**
   * Convert a $value to a valid link while making sure that it does not get too long
   *
   * @param string $value
   * @param string $text
   * @param string $rel
   * @return string
   */
  function smarty_modifier_linkify($value, $text = null, $rel = 'nofollow') {
    $full_url = str_replace(array(' ', '\'', '`', '"'), array('%20', '', '', ''), $value);
    
  	if(strpos($value, 'www.') === 0) {			   // If it starts with www, we add http://
  		$full_url = 'http://'.$full_url;
  	} elseif (strpos($value, 'ftp.') === 0) {	 // Else if it starts with ftp, we add ftp://
  		$full_url = 'ftp://'.$full_url;
    } elseif (!preg_match('#^([a-z0-9]{3,6})://#', $value, $bah)) {  // Else if it doesn't start with abcdef://, we add http://
  		$full_url = 'http://'.$full_url;
    } // if
  
  	// Ok, not very pretty :-)
  	$text = ($text == '' || $text == $value) ? ((strlen($value) > 55) ? substr($value, 0 , 39).' &hellip; '.substr($value, -10) : $value) : stripslashes($text);
  	
  	if($rel) {
  	  $rel = " rel=\"$rel\"";
  	} // if
  	
  	return "<a href=\"$full_url\"$rel>$text</a>";
  } // smarty_modifier_linkify

?>