<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-us" lang="en-us">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="{$assets_url}/stylesheets/error.css" type="text/css" media="screen"/>
    <title>{page_title default="Error"}</title>
    <link rel="shortcut icon" href="{image_url name='favicon.png'}" type="image/x-icon" />
  </head>
  <body>
    <div id="company_logo">
    <a href="{assemble route=homepage}"><img src="{brand what=logo}" alt="" /></a>
    </div>
    <div id="error_box">
      {$content_for_layout}
    </div>
    <div id="footer">
      <p id="copyright">&copy;{year}</p>
    </div>
  </body>
</html>