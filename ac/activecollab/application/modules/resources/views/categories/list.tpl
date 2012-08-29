{title}Categories{/title}
{add_bread_crumb}Categories{/add_bread_crumb}

<div id="manage_categories_list" class="manage_categories {if $request->isAsyncCall()}async{/if}">
  <div class="manage_categories_table_wrapper">
    <table class="common_table">
  {if is_foreachable($categories)}
    {foreach from=$categories item=category}
      {include_template name=_category_row controller=categories module=resources}
    {/foreach}
  {/if}
    </table>
    <p id="manage_categories_empty_list" class="empty_page" {if is_foreachable($categories)}style="display: none"{/if}>{lang}There are no categories in this section!{/lang}</p>
  </div>
  
  {if $can_add_category}
  <form action="{$add_category_url}" method="post" class="add_category_form">
    <input type="text" /> <img src="{image_url name='plus-small.gif'}" alt="" title="{lang}New Category{/lang}" />
  </form>
  {/if}
</div>

<script type="text/javascript">
  App.resources.ManageCategories.init_page('manage_categories_list');
</script>