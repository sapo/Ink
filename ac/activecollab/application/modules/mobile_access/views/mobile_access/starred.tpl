{if is_foreachable($objects)}
  <ul class="list_with_icons">
  {foreach from=$objects item=object}
    <li class="obj_link discussions_obj_link starred_list">
      <a href="{mobile_access_get_view_url object=$object}">
        <span class="object_type">{$object->getType()}</span>
        <span class="main_line">
        {$object->getName()|clean|excerpt:28}
        </span>
        {assign var=project value=$object->getProject()}
        <span class="project_name">{$project->getName()|clean|excerpt:35}</span>
      </a>
    </li>
  {/foreach}
  </ul>
{else}
  <div class="wrapper">
    <div class="box">
      <ul class="menu">
        <li>{lang}No Starred objects{/lang}</li>
      </ul>
    </div>
  </div>
{/if}