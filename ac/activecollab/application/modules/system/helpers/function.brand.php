<?php

  /**
   * Return URL of a specific brand element
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_brand($params, &$smarty) {
  	if(isset($params['what'])) {
  	  switch($params['what']) {
  	    case 'logo':
  	      return URL_BASE == ROOT_URL . '/' ? ROOT_URL . '/public/brand/logo.gif' : ROOT_URL . '/brand/logo.gif';
  	    case 'favicon':
  	      return URL_BASE == ROOT_URL . '/' ? ROOT_URL . '/public/brand/favicon.png' : ROOT_URL . '/brand/favicon.png';
  	  } // switch
  	} // if
  } // smarty_function_brand

?>