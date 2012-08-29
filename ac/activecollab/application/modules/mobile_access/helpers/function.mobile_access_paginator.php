<?php

  /**
   * mobile_access_paginator
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage helpers
   */

  /**
   * Render pagination block
   * 
   * Parameters:
   * 
   * - page - current_page
   * - total_pages - total pages
   * - route - route for URL assembly
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_mobile_access_paginator($params, &$smarty) {
    $url_params = '';
    if (is_foreachable($params)) {
      foreach ($params as $k => $v) {
      	if (strpos($k, 'url_param_')!== false && $v) {
      	  $url_params.='&amp;'.substr($k, 10).'='.$v;
      	} // if
      } // foreach
    } // if
    
    $paginator = array_var($params, 'paginator', new Pager());
    $paginator_url = array_var($params, 'url', ROOT_URL);
    $paginator_anchor = array_var($params,'anchor', '');
    $smarty->assign(array(
      "_mobile_access_paginator_url"  => $paginator_url,
      "_mobile_access_paginator"  => $paginator,
      '_mobile_access_paginator_anchor' => $paginator_anchor,
      "_mobile_access_paginator_url_params" => $url_params,
    ));
    
    $paginator_url = strpos($paginator_url, '?') === false ? $paginator_url.'?' : $paginator_url.'&';
    
    if (!$paginator->isFirst()) {
      $smarty->assign('_mobile_access_paginator_prev_url', $paginator_url.'page='.($paginator->getCurrentPage()-1).$url_params.$paginator_anchor);
    } // if
    
    if (!$paginator->isLast()) {
      $smarty->assign('_mobile_access_paginator_next_url', $paginator_url.'page='.($paginator->getCurrentPage()+1).$url_params.$paginator_anchor);
    } // if
    
    return $smarty->fetch(get_template_path('_paginator', null, MOBILE_ACCESS_MODULE));
  } // smarty_function_mobile_access_paginator

?>