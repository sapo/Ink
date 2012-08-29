{title}Edit checklist{/title}
{add_bread_crumb}Edit checklist{/add_bread_crumb}

{form action=$active_checklist->getEditUrl() method=post class="big_form"}
{include_template name=_checklist_form module=checklists controller=checklists}
{wrap_buttons}
  {submit}Submit{/submit}
{/wrap_buttons}
{/form}