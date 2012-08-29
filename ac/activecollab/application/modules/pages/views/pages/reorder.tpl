{title category=$active_category->getName()}Reorder Pages in :category Category{/title}
{add_bread_crumb}Reorder{/add_bread_crumb}

{form method=post action=$reorder_pages_url id="reorder_form"}
  <div id="pages_reorder">
    {reorder_pages_tree pages=$pages user=$logged_user}
  </div>

  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}

<script type="text/javascript">
  App.pages.reorder_page.init();
</script>