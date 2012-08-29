  <div id="object_details">
{if is_foreachable($_list_objects_categories)}
    <dl class="properties">
      <dt>{lang}Category{/lang}:</dt>
      <dd>
        <ul class="category_list">
          <li {if !$_list_objects_current_category}class="selected"{/if}><a href="{$_list_objects_url_prefix}index.html">{lang}All categories{/lang}</a>,</li>
        {foreach from=$_list_objects_categories item=category}
          {if instance_of($category, 'Category')}
          <li {if $_list_objects_current_category && ($_list_objects_current_category->getId()==$category->getId())}class="selected"{/if}><a href="{$_list_objects_url_prefix}category_{$category->getId()}.html">{$category->getName()|clean}</a>,</li>
          {/if}
        {/foreach}
        </ul>
      </dd>
    </dl>
  {/if}
  </div>