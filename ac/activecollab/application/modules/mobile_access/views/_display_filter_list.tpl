<div class="listing_options">
  <form action="{$_mobile_access_display_categories_action}" method="GET" class="center">
    <select name="{$_mobile_access_display_categories_variable_name}">
      <option value="">{lang}Any Category{/lang}</option>
      {if is_foreachable($_mobile_access_display_categories_objects)}
      {foreach from=$_mobile_access_display_categories_objects item=object}
        {if $_mobile_access_display_categories_active_object->getId() == $object->getId()}
        <option value="{$object->getId()}" selected="selected">{$object->getName()|clean}</option>
        {else}
        <option value="{$object->getId()}">{$object->getName()|clean}</option>
        {/if}
      {/foreach}
      {/if}
    </select>
    <button type="submit">{lang}Filter{/lang}</button>
  </form>
</div>