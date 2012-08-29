{title}New Category{/title}

{form action=$add_category_url method=post}
  {include_template module=resources controller=categories name=_category_form}
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}