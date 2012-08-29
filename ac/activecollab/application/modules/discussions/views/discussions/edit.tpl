{title}Update Discussion{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

{form action=$active_discussion->getEditUrl() method=post id=editDiscussionForm class='big_form'}
{include_template name=_discussion_form module=discussions controller=discussions}
{wrap_buttons}
  {submit}Submit{/submit}
{/wrap_buttons}
{/form}