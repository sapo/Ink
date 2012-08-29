{title}Details{/title}
{add_bread_crumb}Details{/add_bread_crumb}

<div id="email_template_details">
  <h2 class="section_name"><span class="section_name_span">{lang}Default (English){/lang}</span></h2>
  <div class="section_container">
    <dl class="details_list">
      <dt>{lang}Subject{/lang}</dt>
      <dd>{$active_template->getSubject()|clean}</dd>
      
      <dt>{lang}Body{/lang}</dt>
      <dd>{$active_template->getBody()|clean|nl2br}</dd>
      
      <dt></dt>
      <dd>{link href=$active_template->getEditUrl()}Edit{/link}</dd>
    </dl>
  </div>
  
  {if LOCALIZATION_ENABLED && is_foreachable($languages)}
  <h2 class="section_name"><span class="section_name_span">{lang}Translations{/lang}</span></h2>
  <div class="section_container">
    <table id="email_template_translations">
    {foreach from=$languages item=language}
    {if !$language->isBuiltIn()}
      <tr class="{cycle values='odd,even'}">
        <td class="name">{$language->getName()|clean}</td>
        <td class="options">{link href=$active_template->getEditUrl($language->getLocale())}<img src="{image_url name=gray-edit.gif}" alt="" />{/link}</td>
      </tr>
    {/if}
    {/foreach}
    </table>
  </div>
  {/if}
</div>