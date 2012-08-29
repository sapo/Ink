{title}Edit time record{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

{form action=$active_time->getEditUrl() method=post}
  {include_template name=_time_add_form module=timetracking controller=timetracking}
  
{wrap_buttons}
	{submit}Submit{/submit}
{/wrap_buttons}
{/form}