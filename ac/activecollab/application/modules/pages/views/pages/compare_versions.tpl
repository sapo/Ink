{title}Compare Versions{/title}
{add_bread_crumb}Compare Versions{/add_bread_crumb}

{if !$request->isAsyncCall()}
<div id="page_compare">
  {form method=get}
  <table id="versions_to_compare">
    <tr>
      <td class="select_versions">{lang}Compare{/lang} {select_page_version name=new page=$active_page version=$new_version_string id=new_version_select} {lang}with{/lang} {select_page_version name=old page=$active_page version=$old_version_string id=old_version_select}</td>
      <td class="go">{button type=submit class=grey_button}Go{/button}</td>
    </tr>
  </table>
  {/form}
  
  <div id="compared_versions">
{/if}
    <table>
      <tr>
        <th class="reference">
        {if instance_of($new_version, 'Page')}
          {lang}Latest{/lang}
        {else}
          {lang version=$new_version->getVersion()}Version #:version{/lang}
        {/if}
        </th>
        <th class="compared diff">
        {if instance_of($new_version, 'Page')}
          {lang old=$old_version->getVersion()}Diff between latest version and version #:old{/lang}
        {elseif instance_of($old_version, 'Page')}
          {lang new=$new_version->getVersion()}Diff between version #:new and latest version{/lang}
        {else}
          {lang new=$new_version->getVersion() old=$old_version->getVersion()}Diff between version #:new and version #:old{/lang}
        {/if}
        </th>
      </tr>
      <tr>
        <td class="reference"><h2>{$new_version->getName()|clean}</h2></td>
        <td class="compared diff"><h2>{$name_diff}</h2></td>
      </tr>
      <tr>
        <td class="reference details">
          {if instance_of($new_version, 'Page')}
            {lang}by{/lang} {user_link user=$new_version->getUpdatedBy()} {lang}on{/lang} {$new_version->getUpdatedOn()|datetime}
          {else}
            {lang}by{/lang} {user_link user=$new_version->getCreatedBy()} {lang}on{/lang} {$new_version->getCreatedOn()|datetime}
          {/if}
        </td>
        <td class="compared details">
        {if instance_of($old_version, 'Page')}
          {lang}by{/lang} {user_link user=$old_version->getUpdatedBy()} {lang}on{/lang} {$old_version->getUpdatedOn()|datetime}
        {else}
          {lang}by{/lang} {user_link user=$old_version->getCreatedBy()} {lang}on{/lang} {$old_version->getCreatedOn()|datetime}
        {/if}
        </td>
      </tr>
      <tr>
        <td class="reference">
          <div class="auto_overflow">{$new_version->getFormattedBody()|html2text|nl2br}</div>
        </td>
        <td class="compared diff">
          <div class="auto_overflow">{$body_diff|nl2br}</div>
        </td>
      </tr>
    </table>
{if !$request->isAsyncCall()}
  </div>
</div>
{/if}