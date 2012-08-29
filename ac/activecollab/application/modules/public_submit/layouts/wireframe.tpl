<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>{lang company=$owner_company->getName()}Submit Ticket / :company{/lang}</title>
  <link rel="stylesheet" href="{$assets_url}/modules/public_submit/stylesheets/style.css" type="text/css" media="screen"/>
  <script type="text/javascript" src="{$assets_url}/js.php"></script>
  {page_head_tags}
  {template_vars_to_js}
</head>

<body>  
  <div id="wrapper">
      <div id="header">
        <h1>{lang}Submit Ticket{/lang}</h1>
      </div>
  
      <div class="content_container">
        {$content_for_layout}
      </div>
      
      <div id="footer">
      {if $application->copyright_removed()}
        <p id="copyright">&copy;{year} by {$owner_company->getName()|clean}</p>
      {else}
      	<p id="powered_by"><a href="http://www.vbsupport.org/forum/index.php" target="_blank"><img src="{image_url name=acpoweredwhite.gif}" alt="NulleD By FintMax" /></a></p>
      {/if}
      	<!-- {benchmark} -->
      </div>
  </div>
</body>
</html>
