{title}New Version{/title}
{add_bread_crumb}New Version{/add_bread_crumb}

<div id="new_file_version">
  <p>{lang url=$active_file->getViewUrl() name=$active_file->getName()}You are about to upload new version of <a href=":url">":name"</a> file. Older versions of this file will not be deleted but saved for future reference{/lang}.</p>
  
  {form action=$active_file->getNewVersionUrl() method=post enctype="multipart/form-data"}
    {wrap field=file}
      {label for=newVersionFile required=yes}Select File{/label}
      {attach_files}
    {/wrap}
  
    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>