<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-us" lang="en-us">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="{$module_assets_url}/{$mobile_device}/stylesheets/style.css" type="text/css" media="screen" id="style_main_css"/>
    <script type="text/javascript" src="{$module_assets_url}/js.php?device={$mobile_device}"></script>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no, width=320"/>
    {page_head_tags}
    <title>
    {if $page_title}
      {$page_title|clean}
    {else}
      {lang}Projects{/lang}
    {/if} / {$owner_company->getName()|clean}</title>
  </head>
  
  <body>
    <div id="main_title">
      <h1>
      {if $page_title}
        {$page_title|clean|excerpt:18}
      {else}
        {lang}Projects{/lang}
      {/if}
      </h1>
      
      {if $page_back_url}
        <a href="{$page_back_url}" class="button_back" id="button_back"><span>
        {lang}Up{/lang}
        </span></a>
      {else}
        <a href="{assemble route=mobile_access}" class="button_back" id="button_back"><span>
        {lang}Home{/lang}
        </span></a>
      {/if}
      <a href="#" class="button_menu" id="button_menu"><span>{lang}Menu{/lang}</span></a>
    </div>
      
    <div id="app_body">     
      {mobile_access_project_breadcrumbs breadcrumbs=$page_breadcrumbs}
      {$content_for_layout}
    </div>
    
    <div id="overlay_menu">
      <div class="black_overlay"></div>
      <div id="overlay_profile_menu">
        <a href="{assemble route='mobile_access_logout'}">{lang}Logout{/lang}</a>
        <span>{$logged_user->getDisplayName()}</span>
      </div>
      <ul id="overlay_main_menu">
        <li><a href="{assemble route=mobile_access}" class="icon_big_home">{lang}Home{/lang}</a></li>
        <li><a href="{assemble route=mobile_access_assignments}" class="icon_big_assignments">{lang}Assignments{/lang}</a></li>
        <li><a href="{assemble route=mobile_access_starred}" class="icon_big_starred">{lang}Starred{/lang}</a></li>
        <li><a href="{assemble route=mobile_access_people}" class="icon_big_people">{lang}People{/lang}</a></li>
        <li><a href="{assemble route=mobile_access_projects}" class="icon_big_projects">{lang}Projects{/lang}</a></li>
      </ul>
      {if $project_sections}
      <div class="clear"></div>
      <div id="overlay_project_menu">
        <h3>{$active_project->getName()|clean}</h3>
        <ul>
          {foreach from=$project_sections item=project_section}
            {if $active_project_section==$project_section.name}
            <li><a href="{$project_section.url}" class="selected">{$project_section.full_name}</a></li>
            {else}
            <li><a href="{$project_section.url}">{$project_section.full_name}</a></li>
            {/if}
          {/foreach}
        </ul>
        <div class="clear"></div>
      </div>
      {/if}
    </div>
  </body>
</html>