{wrap field=name}
  {label for=repositoryName required=yes}{lang}Name{/lang}{/label}
  {text_field name='repository[name]' value=$repository_data.name id=repositoryName class='title required'}
{/wrap}

{wrap field=url aid="$aid_url"}
  {label for=repositoryUrl required=yes}{lang}Repository URL or directory:{/lang}{/label}
  {text_field name='repository[url]' readonly=$disable_url_and_type value=$repository_data.url id=repositoryUrl class='title required'}
{/wrap}

<div class="col">
{wrap field=username}
  {label for=repositoryUsername required=yes}{lang}Username{/lang}{/label}
  {text_field name='repository[username]' style='width:250px' value=$repository_data.username id=repositoryUsername class='title required'}
{/wrap}
</div>

<div class="col">
{wrap field=password}
  {label for=repositoryPassword required=yes}{lang}Password{/lang}{/label}
  {password_field name='repository[password]' value=$repository_data.password id=repositoryPassword}
{/wrap}
</div>
<div class="clear"></div>

{wrap field=type aid="$aid_engine"}
  {label for=repositoryType}{lang}Repository Type{/lang}{/label}
  {select_repository_type disabled=$disable_url_and_type name=repository[repositorytype] id=repositoryType data=$types selected=$repository_data.repositorytype}
{/wrap}

{wrap field=type}
  {label for=repositoryUpdateType}{lang}Commit History Update Type{/lang}{/label}
  {select_repository_update_type name=repository[updatetype] id=repositoryUpdateType data=$update_types selected=$repository_data.updatetype}
{/wrap}

<div id="test_connection">
  <button type="button"><span><span>{lang}Test Connection to Repository{/lang}</span></span></button>
  <span class="test_connection_results">
    <img src="{image_url name=pending_indicator.gif}" alt='' />
    <span></span>
  </span>
</div>

{if $logged_user->canSeePrivate()}
  {wrap field=visibility}
    {label for=repositoryVisibility}Visibility{/label}
    {select_visibility name=repository[visibility] value=$repository_data.visibility project=$active_project}
  {/wrap}
{else}
  <input type="hidden" name="repository[visibility]" value="1"/>
{/if}