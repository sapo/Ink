{title}Create a New Page{/title}
{add_bread_crumb}New page{/add_bread_crumb}

{form action=$add_page_url method=post ask_on_leave=yes autofocus=yes enctype="multipart/form-data" class='big_form'}
  {include_template module=pages controller=pages name=_page_form}
    
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}