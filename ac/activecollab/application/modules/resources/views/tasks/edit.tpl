{title}Edit task{/title}
{add_bread_crumb}Edit task{/add_bread_crumb}

<p>&laquo; {lang view_url=$active_task_parent->getViewUrl() name=$active_task_parent->getName()}Back to <a href=":view_url">:name</a>{/lang}.</p>

{form action=$active_task->getEditUrl() method=post}
  {include_template name=_task_form module=resources controller=tasks}
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}