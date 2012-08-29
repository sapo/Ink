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
    <!-- <link rel="shortcut icon" href="{image_url name='favicon.png'}" type="image/x-icon" /> -->
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
    </div>
      
    <div id="app_body">     
      {$content_for_layout}
    </div>

  </body>
</html>