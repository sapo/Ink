{title}New Checklist{/title}
{add_bread_crumb}New Checklist{/add_bread_crumb}

{form action=$add_checklist_url method=post ask_on_leave=yes autofocus=yes class="big_form"}
  {include_template name=_checklist_form module=checklists controller=checklists}
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}