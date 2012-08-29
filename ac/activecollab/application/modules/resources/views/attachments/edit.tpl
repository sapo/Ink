{title}Update attachment{/title}

{form action=$active_attachment->getEditUrl() method=post}
  {include_template name=_attachment_form controller=attachments module=resources}
  
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}