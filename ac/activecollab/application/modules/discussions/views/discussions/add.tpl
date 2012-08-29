{title}Add discussion{/title}
{add_bread_crumb}New Discussion{/add_bread_crumb}

{form action=$add_discussion_url method=post enctype="multipart/form-data" ask_on_leave=yes autofocus=yes class='big_form'}
  {include_template name=_discussion_form module=discussions controller=discussions}
  
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}