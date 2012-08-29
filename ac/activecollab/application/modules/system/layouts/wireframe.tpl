<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-us" lang="en-us">
  <head>
    {assign_var name=assets_query_string}v={$application->version}&modules={foreach from=$loaded_modules item=loaded_module}{$loaded_module->getName()},{/foreach}{/assign_var}
  
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="{$assets_url}/css.php?{$assets_query_string}" type="text/css" media="screen" id="style_main_css"/>
    <link rel="stylesheet" href="{$assets_url}/themes/{$theme_name}/theme.css" type="text/css" media="screen" id="style_theme_css"/>
    
    <!--[if IE]>
      <link rel="stylesheet" href="{$assets_url}/stylesheets/iefix.css" type="text/css" />
      <link rel="stylesheet" href="{$assets_url}/themes/{$theme_name}/iefix.css" type="text/css" media="screen"/>
    <![endif]-->
    
    <link rel="stylesheet" href="{$assets_url}/print.php?{$assets_query_string}" type="text/css" media="print" />
    <link rel="stylesheet" href="{$assets_url}/themes/{$theme_name}/print.css" type="text/css" media="print" />
    <link rel="alternate stylesheet" href="{$assets_url}/print_preview.php?theme_name={$theme_name}" type="text/css" media="screen" id="print_preview_css" disabled="true" />
    
    <script type="text/javascript" src="{$assets_url}/js.php?{$assets_query_string}"></script>
    {template_vars_to_js}
    
    {if instance_of($current_language, 'Language')}
      {js_langs locale=$current_language->getLocale()}
    {/if}
    <script type="text/javascript">
      if(App.{$request->getModule()} && App.{$request->getModule()}.controllers.{$request->getController()}) {ldelim}
        if(typeof(App.{$request->getModule()}.controllers.{$request->getController()}.{$request->getAction()}) == 'function') {ldelim}
          App.{$request->getModule()}.controllers.{$request->getController()}.{$request->getAction()}();
        {rdelim}
      {rdelim}
    </script>
    
    {page_head_tags}
    <title>{page_title default="Projects"} / {$owner_company->getName()|clean}</title>
    <link rel="shortcut icon" href="{brand what=favicon}" type="image/x-icon" />
    
    {if is_foreachable($wireframe->rss_feeds)}
      {foreach from=$wireframe->rss_feeds item=rss_feed}
        <link rel="alternate" type="{$rss_feed.feed_type}" title="{$rss_feed.title|clean}" href="{$rss_feed.url}" />
      {/foreach}
    {/if}
  </head>
  <body style="margin: 0">
  
    <!-- Print Preview -->
    <div id="print_preview_header">
      <ul>
        <li><button type="button" id="print_preview_print">{lang}Print{/lang}</button></li>
        <li><button type="button" id="print_preview_close">{lang}Close Preview{/lang}</button></li>
      </ul>
      <h2>{lang}Print Preview{/lang}</h2>
    </div>
    
    <!-- Top block -->
    <div id="top">
    	<div class="container">
    	  <p id="logged_user">
    	  {if $logged_user->getFirstName()}
    	    {assign var=_welcome_name value=$logged_user->getFirstName()}
    	  {else}
    	    {assign var=_welcome_name value=$logged_user->getDisplayName()}
    	  {/if}
    	    <span class="inner">
      	    {lang name=$_welcome_name}Welcome back :name{/lang} | {if $logged_user->isAdministrator()}<a href="{assemble route=admin}" class="{if $wireframe->current_menu_item == 'admin'}active{/if}">{lang}Admin{/lang}</a> | {/if} <a href="{$logged_user->getViewUrl()}" class="{if $wireframe->current_menu_item == 'profile'}active{/if}">{lang}Profile{/lang}</a> | {link href='?route=logout'}{lang}Logout{/lang}{/link}
    	    </span>
    	  </p>
        <div id="header">
        	<p id="site_log">
        	  <a href="{assemble route=homepage}" class="site_logo"><img src="{brand what=logo}" alt="{$owner_company->getName()|clean} logo" title="{lang}Back to start page{/lang}" /></a>
        	</p>
        	{menu}
        	{literal}
        	<script type="text/javascript">
        	   App.MainMenu.init('menu');
        	</script>
        	{/literal}
        </div>
      </div>
    </div>
    
    {if isset($page_tabs)}
    <div id="tabs">
    	<div class="container">
      	<ul>
      	{foreach from=$page_tabs->data key=current_tab_name item=current_tab name=page_tabs}
      	  <li {if $smarty.foreach.page_tabs.iteration == 1}class="first"{/if} id="page_tab_{$current_tab_name}"><a href="{$current_tab.url}" {if $current_tab_name == $page_tab}class="current"{/if}><span>{$current_tab.text|clean}</span></a></li>
      	{/foreach}
        </ul>
      </div>
    </div>
    {/if}
    
    <div id="page_header_container">
    	<div class="container">
        <div id="page_header" class="{if $wireframe->details}with_page_details{else}without_page_details{/if}">
	      <div class="page_info_container">
	      {if instance_of($wireframe->page_company, 'Company') && instance_of($wireframe->page_project, 'Project')}
	        <h1 id="page_title"><span>{$wireframe->page_company->getName()|clean} | {$wireframe->page_project->getName()|clean} | </span> {page_title default="Page"}</h1>
	      {elseif instance_of($wireframe->page_company, 'Company')}
	        <h1 id="page_title"><span>{$wireframe->page_company->getName()|clean} | </span> {page_title default="Page"}</h1>
	      {else}
	        <h1 id="page_title">{page_title default="Page"}</h1>
	      {/if}
	      
  				{if $wireframe->details}
  				<p id="page_details">{$wireframe->details}</p>
  				{/if}
				</div>
		  {assign var=page_actions value=$wireframe->getSortedPageActions()}
  	  {if $wireframe->print_button || is_foreachable($page_actions)}
  	    <ul id="page_actions">
  	    {if is_foreachable($page_actions)}
  	    {foreach from=$page_actions key=page_action_name item=page_action name=page_actions}
  	      {if count($page_actions) == 1}
  	        <li id="{$page_action_name}_page_action" class="single {if is_foreachable($page_action.subitems)}with_subitems hoverable{else}without_subitems{/if}">
  	      {else}
    	      <li id="{$page_action_name}_page_action" class="{if $smarty.foreach.page_actions.first}first{elseif $smarty.foreach.page_actions.last}last {/if} {if is_foreachable($page_action.subitems)}with_subitems hoverable{else}without_subitems{/if}">
  	      {/if}
  	        {link id=$page_action.id  href=$page_action.url method=$page_action.method confirm=$page_action.confirm not_lang=yes}<span>{$page_action.text|clean} {if is_foreachable($page_action.subitems)}<img src="{image_url name='dropdown_arrow.gif'}" alt="" />{/if}</span>{/link}
  	        
  	        {if is_foreachable($page_action.subitems)}
  	        <ul>
  	        {foreach from=$page_action.subitems key=page_action_subaction_name item=page_action_subaction}
  	          {if $page_action_subaction.text && $page_action_subaction.url}
  	          <li id="{$page_action_subaction_name}_page_action" class="subaction">{link href=$page_action_subaction.url method=$page_action_subaction.method id=$page_action_subaction.id confirm=$page_action_subaction.confirm}{$page_action_subaction.text|clean}{/link}</li>
  	          {else}
  	          <li id="{$page_action_subaction_name}_page_action" class="separator"></li>
  	          {/if}
  	        {/foreach}
  	        </ul>
  	        {/if}
  	      </li>
  	        {counter name=actions_counter_name assign=actions_counter}
  	      {/foreach}
  	    {/if}
  	    {if $wireframe->print_button}
  	      <li class="single"><a href="javascript:window.print();" id="print_button"><span><img src="{image_url name='icons/print.gif'}" alt="Print" /></span></a></li>
  	    {/if}
  			</ul>
  		{/if}
 		  <div class="clear"></div>

  		  
		  </div>
      </div>
    </div>
    
    <div id="page">
    	<div class="container">
    	  <div class="container_inner">
    	  {if WARN_WHEN_JAVASCRIPT_IS_DISABLED || is_foreachable($wireframe->page_messages)}
    	    <div id="page_messages">
    	    {if is_foreachable($wireframe->page_messages)}
    		    {foreach from=$wireframe->page_messages item=page_message name=page_messages}
  		      <div class="page_message {$page_message.class|clean} {if $smarty.foreach.page_messages.iteration == 1}first{/if}" style="background-image: url('{$page_message.icon}')">
  		        <p>{$page_message.body}</p>
  		      </div>
    		    {/foreach}
    		  {/if}
  		      <div class="page_message {if !is_foreachable($wireframe->page_messages)}first{/if}" id="javascript_required" style="background-image: url('{image_url name=messages/error.gif}')">
  		        <p>{lang url=$js_disabled_url}It appears that JavaScript is disabled in your web browser. Please enable it to have full system functionality available. <a href=":url">Read more</a>{/lang}.</p>
  		      </div>
  		      <script type="text/javascript">
  		        $('#javascript_required').hide();
  		      </script>
    		  </div>
    		{/if}
    		  
    		  <ul id="breadcrumbs">
    		    <li class="first"><a href="{assemble route=dashboard}">{lang}Dashboard{/lang}</a>&raquo;</li>
    		    {foreach from=$wireframe->bread_crumbs item=bread_crumb name=_bread_crumb}
    		    <li>
    		    {if $bread_crumb.url}
    		      <a href="{$bread_crumb.url}" title="{$bread_crumb.text|clean}">{$bread_crumb.text|clean|excerpt:20}</a>&raquo;
    		    {else}
    		      <span class="current">{$bread_crumb.text|clean}</span>
    		    {/if}
    		    </li>
    		    {/foreach}
    		  </ul>
    	     		  
    		  {flash_box}
    		  <div id="page_content">
    		    {$content_for_layout}
    		    <div class="clear"></div>
    		  </div>
    		  <div class="content_fix"></div>
        </div>
      </div>
    </div>
    
    <div id="footer">
    {if $application->copyright_removed()}
      <p id="copyright">&copy;{year} by {$owner_company->getName()|clean}</p>
    {else}
    	<p id="powered_by"><a href="http://www.vbsupport.org/forum/index.php" target="_blank"><img src="{image_url name=acpowered.gif}" alt="NulleD By FintMax" /></a></p>
    {/if}
    	{benchmark}
    </div>
  </body>
</html>