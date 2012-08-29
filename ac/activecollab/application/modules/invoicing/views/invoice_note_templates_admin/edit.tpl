{title name=$active_note->getName()}Edit Note Template: :name{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

{form action=$active_note->getEditUrl() method=POST}
  {include_template name=_note_form module=invoicing controller=invoice_note_templates_admin}
  
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}