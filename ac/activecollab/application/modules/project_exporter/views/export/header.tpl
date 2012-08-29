<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-us" lang="en-us">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="{$url_prefix}style/style.css" type="text/css" media="screen"/>
    <title>{$active_project->getName()|clean}</title>
  </head>
  
  <body>
    <div class="wrapper">
      <div id="project_name">
        <img src="{$url_prefix}uploaded_files/project_logo.gif" alt="project_logo" />{$active_project->getName()|clean}
      </div>
    