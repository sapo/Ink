{title}Add New Mailbox{/title}
{add_bread_crumb}Add New Mailbox{/add_bread_crumb}

{form action=$add_new_mailbox_url method=post id="mailbox_form"}
  {include_template name=_mailbox_form module=incoming_mail controller=incoming_mail_admin}
  
  {wrap_buttons}
	  {submit}Submit{/submit}
  {/wrap_buttons}
{/form}