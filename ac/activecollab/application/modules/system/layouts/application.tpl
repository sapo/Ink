<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-us" lang="en-us">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    {if $mobile_device_css}
      <link rel="stylesheet" href="{$mobile_device_css}" type="text/css" />
    {else}
      <link rel="stylesheet" href="{$assets_url}/css.php" type="text/css" />
      <link rel="stylesheet" href="{$assets_url}/themes/{$theme_name}/theme.css" type="text/css" />
      <!--[if IE]>
        <link rel="stylesheet" href="{$assets_url}/stylesheets/iefix.css" type="text/css" />
      <![endif]-->
    {/if}
    
    <script type="text/javascript" src="{$assets_url}/js.php"></script>
    <link rel="shortcut icon" href="{image_url name='favicon.png'}" type="image/x-icon" />

    <title>{page_title default="Projects"} / {$owner_company->getName()|clean}</title>
    {page_head_tags}
    {template_vars_to_js}
  </head>
  <body style="margin: 0;">
    <div id="wrapper">
      <!--<h1>{page_title default="Page"}</h1> -->
      {flash_box}
      <div id="content">{$content_for_layout}</div>
      <div id="footer">
      {if $application->copyright_removed()}
        <p id="copyright">&copy;{year} by {$owner_company->getName()|clean}</p>
      {else}
      	<p id="powered_by"><a href="http://www.vbsupport.org/forum/index.php" target="_blank"><img src="{image_url name=acpowered.gif}" alt="NulleD By FintMax" /></a></p>
      {/if}
      </div>
    </div>
  </body>
</html>