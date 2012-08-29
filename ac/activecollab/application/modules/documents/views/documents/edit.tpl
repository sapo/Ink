{title}Edit Document{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

{form action=$active_document->getEditUrl() method=post}
{include_template name=_document_form module=documents controller=documents}
{wrap_buttons}
  {submit}Submit{/submit}
{/wrap_buttons}
{/form}