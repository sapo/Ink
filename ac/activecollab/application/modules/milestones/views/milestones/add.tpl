{title}New Milestone{/title}
{add_bread_crumb}New Milestone{/add_bread_crumb}

{form action=$add_milestone_url method=post}
{include_template name=_milestone_form module=milestones controller=milestones}
{wrap_buttons}
  {submit}Submit{/submit}
{/wrap_buttons}
{/form}