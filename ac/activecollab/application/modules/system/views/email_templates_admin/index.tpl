{title}Email Templates{/title}
{add_bread_crumb}All{/add_bread_crumb}

<div id="email_templates_settings">
{if is_foreachable($grouped_templates)}
  {foreach from=$grouped_templates key=module_name item=templates}
    <h2 class="section_name"><span class="section_name_span">{lang}{$module_name}{/lang}</span></h2>
    <div class="section_container">
      <ul>
      {foreach from=$templates item=template}
        <li><a href="{$template->getUrl()}">{lang name=$template->getName()}Email template :name{/lang}</a></li>
      {/foreach}
      </ul>
    </div>
  {/foreach}
{else}
  <p class="details center">{lang}There are no email templates defined in database{/lang}</p>
{/if}
</div>