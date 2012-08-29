{title}Update{/title}
{add_bread_crumb}Update{/add_bread_crumb}

{form action=$active_template->getEditUrl($locale) method=post}
  {wrap field=subject}
    {label for=templateSubject required=yes}Subject{/label}
    {text_field name='template[subject]' value=$template_data.subject id=templateSubject class='required long'}
  {/wrap}
  
  {wrap field=body}
    {label for=templateBody required=yes}Body{/label}
    {textarea_field name='template[body]' id=templateBody class='required editor'}{$template_data.body}{/textarea_field}
  {/wrap}
  
  {if is_foreachable($template_variables)}
  <p>{lang}Available variables{/lang}:</p>
  <ul>
  {foreach from=$template_variables item=template_variable}
    <li>{$template_variable|clean}</li>
  {/foreach}
  </ul>
  {/if}
  
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}