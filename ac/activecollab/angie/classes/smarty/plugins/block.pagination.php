<?php

  /**
   * Show page pagination
   * 
   * Parameters:
   * 
   * - pager - Pagination object
   * - page - Current page, used when Pagination description is not provided
   * - per_page - Number of items listed per page, used when Pagination 
   *   description is not provided
   * - total - Total number of pages, used when Pagination description is not 
   *   provided
   * -
   *
   * @param array $params
   * @param Smarty &$smarty
   * @return string
   */
  function smarty_block_pagination($params, $content, &$smarty, &$repeat) {
  
  	// ---------------------------------------------------
  	//  Collect parameters
  	// ---------------------------------------------------
  
  	if(isset($params['pager'])) {
  		$pager = array_var($params, 'pager');
  		if(instance_of($pager, 'Pager')) {
  			$current_page = $pager->getCurrentPage();
  			$total_items = $pager->getTotalItems();
  			$items_per_page = $pager->getItemsPerPage();
  		} else {
  			return new InvalidParamError('pager', $pager, "'pager' is expected to be an instance of Pager class", true);
  		} // if
  	} else {
  		$current_page = array_var($params, 'page');
  		$items_per_page = array_var($params, 'per_page');
  		$total_items = array_var($params, 'total');
  	} // if
  
  	if($current_page === null) {
  		return new InvalidParamError('page', $current_page, "'page' property is required for 'pagination' helper", true);
  	} // if
  
  	if($items_per_page === null) {
  		return new InvalidParamError('per_page', $items_per_page, "'per_page' property is required for 'pagination' helper", true);
  	} // if
  
  	if($total_items === null) {
  		return new InvalidParamError('total', $total_items, "'total' property is required for 'pagination' helper", true);
  	} // if
  
  	$page_placeholder = array_var($params, 'placeholder', '-PAGE-');
  
  	$url_base = $content;
  
  	$separator = array_var($params, 'separator', ', ');
  
  	// ---------------------------------------------------
  	//  Now render pagination
  	// ---------------------------------------------------
  
  	if($total_items < 1) {
  		$total_pages = 1;
  	} else {
  		$total_pages = ceil($total_items / $items_per_page);
  	} // if
  	$urls = array();
  	
  	if ((array_var($params, 'sensitive', false) == true) && ($total_pages < 2)) {
  	  return false;
  	} // if
  
  	// Prepare sorounding pages numbers (3 before current page and three after)
  	$sourounding = array();
  	$start_range = $current_page - 2;
  	if($start_range < 1) {
  		$start_range = 1;
  	} // if
  	$end_range = $current_page + 2;
  	if($end_range > $total_pages) {
  		$end_range = $total_pages;
  	} // if
  
  	for($i = $start_range; $i <= $end_range; $i++) {
  		$sourounding[] = $i;
  	} // for
  
  	// Render content into an array
  	$before_dots_rendered = false;
  	$after_dots_rendered = false;
  
  	if($total_pages > 3 && $current_page > 1 && $current_page !== false) {
  		$urls[] = '<a href="' . str_replace($page_placeholder, $current_page-1, $url_base) . '" title="' . lang('Previous Page') . '">&laquo; ' . lang('Prev.') . '</a> | ';
  	}
  
  	for($i = 1; $i <= $total_pages; $i++) {
  		// Print page...
  		if(($i == 1) || ($i == $total_pages) || in_array($i, $sourounding)) {
  			if($current_page == $i) {
  				$urls[] = '<span class="current"><strong>(' . $i . ')</strong></span>';
  			} else {
  				$urls[] = '<a href="' . str_replace($page_placeholder, $i, $url_base) . '">' . $i . '</a>';
  			} // if
  
  			if($i < $total_pages) {
  				$urls[count($urls) - 1] .= $separator;
  			} // if
  
  			// Print dots if they are not rendered
  		} else {
  			if($i < $current_page && !$before_dots_rendered) {
  				$before_dots_rendered = true;
  				$urls[] = '... ';
  			} elseif($i > $current_page && !$after_dots_rendered) {
  				$after_dots_rendered = true;
  				$urls[] = '... ';
  			} // if
  		} // if
  
  	} // for
  
  	if($total_pages > 3 && $current_page < $total_pages && $current_page !== false) {
  		$urls[] = ' | <a href="' . str_replace($page_placeholder, $current_page+1, $url_base) . '" title="' . lang('Next Page') . '">' . lang('Next') . ' &raquo;</a> ';
  	} // if
  
  	return implode('', $urls);
  } // smarty_function_pagination

?>