{title}Document Categories{/title}
{add_bread_crumb}Document Categories{/add_bread_crumb}

<div id="manage_document_categories_list" class="manage_document_categories {if $request->isAsyncCall()}async{/if}">
  <div class="manage_document_categories_table_wrapper">
    <table class="common_table">
  {if is_foreachable($categories)}
    {foreach from=$categories item=document_category}
      {include_template name=_document_category_row controller=document_categories module=documents}
    {/foreach}
  {/if}
    </table>
    <p id="manage_document_categories_empty_list" class="empty_page" {if is_foreachable($categories)}style="display: none"{/if}>{lang}There are no document categories{/lang}</p>
  </div>
  
  {if $add_category_url}
  <form action="{assemble route=document_categories_add}" method="post" class="add_document_category_form">
    <input type="text" /> <img src="{image_url name='plus-small.gif'}" alt="" title="{lang}New Category{/lang}" />
  </form>
  {/if}
</div>

<script type="text/javascript">
  App.system.ManageDocumentCategories.init_page('manage_document_categories_list');
</script>