{title}Edit Mailbox{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

{form action=$active_mailbox->getEditUrl() method=post id="mailbox_form"}
  {include_template name=_mailbox_form module=incoming_mail controller=incoming_mail_admin}
  
  {wrap_buttons}
	  {submit}Submit{/submit}
  {/wrap_buttons}
{/form}