{title}New Task{/title}
{add_bread_crumb}Add task{/add_bread_crumb}

<div id="new_task">
  <p>{lang view_url=$active_task_parent->getViewUrl() name=$active_task_parent->getName() type=$active_task_parent->getVerboseType(true)}You are about to create a new task on "<a href=":view_url"><strong>:name</strong></a>" :type{/lang}.</p>
  
  {form action=$active_task_parent->getPostTaskUrl() method=post class=focusFirstField}
    {include_template name=_task_form module=resources controller=tasks}
    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>