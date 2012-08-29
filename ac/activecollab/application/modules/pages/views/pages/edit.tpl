{title}Edit page{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

{form action=$active_page->getEditUrl() method=post ask_on_leave=yes class='big_form'}
  {include_template module=pages controller=pages name=_page_form}
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}