{if is_foreachable($_mobile_access_list_objects_objects)}
  <ul class="menu list">
    {foreach from=$_mobile_access_list_objects_objects item=object}
      <li>
        <a href="{mobile_access_get_view_url object=$object}">
        {if $_mobile_access_show_object_type}
          <span class="object_type">{$object->getType()}</span><br />
        {/if}
          <span class="main_link"><span>{$object->getName()|clean|excerpt:27}</span></span>
          <span class="details"><strong>{$object->getCreatedByName()|clean}</strong><br />{$object->getCreatedOn()|date}</span>
        </a>
      </li>
    {/foreach}
  </ul>
{/if}