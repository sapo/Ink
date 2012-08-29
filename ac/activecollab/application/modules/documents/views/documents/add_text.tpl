{title}Add Document{/title}
{add_bread_crumb}Add Text{/add_bread_crumb}

<div id="add_text_document">
  {form action=$add_text_url method=post class='big_form'}
    {include_template name=_document_form module=documents controller=documents}
    
    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>