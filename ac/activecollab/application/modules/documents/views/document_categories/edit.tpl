{title}Edit Category{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

{form action=$active_document_category->getEditUrl() method=post}
{include_template name=_document_category_form module=documents controller=document_categories}
{wrap_buttons}
  {submit}Submit{/submit}
{/wrap_buttons}
{/form}