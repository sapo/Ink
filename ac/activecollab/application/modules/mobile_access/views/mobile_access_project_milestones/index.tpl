{if is_foreachable($milestones)}
  <ul class="list_with_icons">
  {foreach from=$milestones item=milestone}
    <li class="obj_link discussions_obj_link">
      <a href="{mobile_access_get_view_url object=$milestone}">
        <span class="main_line main_line_smaller">
          {object_priority object=$milestone}
          {$milestone->getName()|clean|excerpt:18}
        </span>
        <span class="details">{due object=$milestone}, {$milestone->getStartOn()|date}{if $milestone->getStartOn()!=$milestone->getDueOn()} - {$milestone->getDueOn()|date}{/if}</span>
        </a>
    </li>
  {/foreach}
  </ul>
{else}
  <div class="wrapper">
    <div class="box">
      <ul class="menu">
        <li>{lang}No Milestones{/lang}</li>
      </ul>
    </div>
  </div>
{/if}