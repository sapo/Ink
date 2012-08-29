{title}Master Categories{/title}
{add_bread_crumb}Master Categories{/add_bread_crumb}

<div id="category_definitions">
{if is_foreachable($category_definitions)}
  <p>{lang}Changes you make will be applied only to projects created in the future, not the existing ones. Click Submit button to save changes.{/lang}</p>
  {form action='?route=admin_settings_categories' method=post}
    {foreach from=$category_definitions item=category_definition}
      <h2 class="section_name"><span class="section_name_span">{$category_definition.label}</span></h2>
      <div class="section_container">
        {string_list name=$category_definition.name value=$category_definition.value}
      </div>
    {/foreach}
  
    {wrap_buttons}
      {submit}Submit{/submit} {button href='?route=admin'}Cancel{/button}
    {/wrap_buttons}
  {/form}
{else}
  <p>{lang}There are no master category sets defined in the database{/lang}!</p>
{/if}
</div>