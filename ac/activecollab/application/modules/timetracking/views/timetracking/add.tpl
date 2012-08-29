{title}New Time Record{/title}
{add_bread_crumb}New Time Record{/add_bread_crumb}

{form action=$add_url method=post}
  {include_template name=_time_add_form module=timetracking controller=timetracking}
  {wrap_buttons}
  	{submit}Submit{/submit}
  {/wrap_buttons}
{/form}