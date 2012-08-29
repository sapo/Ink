{title not_lang=true}{lang}Edit Ticket{/lang} #{$active_ticket->getTicketId()}{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

{form action=$active_ticket->getEditUrl() method=post ask_on_leave=yes class='big_form'}
  {include_template name=_ticket_form module=tickets controller=tickets}
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}