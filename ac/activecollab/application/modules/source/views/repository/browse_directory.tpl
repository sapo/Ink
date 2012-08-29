{title}Browse Directory{/title}
{page_object object=$latest_revision}

{add_bread_crumb url=$active_repository->getBrowseUrl($active_revision, $active_file)}{$active_file_basename}{/add_bread_crumb}
{add_bread_crumb}{lang}Browse{/lang}{/add_bread_crumb}

<div id="repository_browse">
  <div class="ticket main_object browse_directory" id="file_">
    <div class="body">
      <dl class="properties">
        <dt>{lang}Path{/lang}</dt>
        <dd><strong>{$navigation}</strong></dd>
        <dt>{lang}Revision Details{/lang}</dt>
        <dd>{$active_commit->getMessage()|nl2br}&nbsp;</dd>
      </dl>
    </div>
    <div class="resources">
      <div class="source_container">
        <table class="source_directory_browser common_table">
          <tr>
            <th>{lang}Name{/lang}</th>
            <th class="file_size">{lang}Size{/lang}</th>
            <th class="date">{lang}Date{/lang}</th>
            <th class="author">{lang}Author{/lang}</th>
            <th class="revision">{lang}Revision{/lang}</th>
            <th class="info">{lang}Info{/lang}</th>
          </tr>
          {if $can_go_up}
          <tr class="{cycle values='odd,even'}">
            <td colspan="6">
              <a title="{lang}Go to parent directory{/lang}" href="{$active_repository->getBrowseUrl($active_commit, $parent_directory)}">
              {image module=source name=folder_up.png}
              </a>
            </td>
          </tr>
          {/if}
          {if is_foreachable($list)}
            {foreach from=$list.entries item=entry}
            <tr class="{cycle values='odd,even'}">
              <td class="{if $entry.kind eq 'dir'}directory{else}file{/if}">
                {assign_var name=source_path}{$active_file|clean}/{$entry.name|clean}{/assign_var}
                {assign_var name=image_name}{if $entry.kind eq 'dir'}folder_icon.gif{else}file_icon.gif{/if}{/assign_var}
                <a href="{$active_repository->getBrowseUrl($active_commit, $source_path, $entry.revision)}">
                  {$entry.name|clean}
                </a>
              </td>
              <td class="file_size">{if $entry.size <> null}{$entry.size|clean}{/if}</td>
              <td class="date">{$entry.date|date}</td>
              <td class="author">{$entry.author|clean}</td>
              <td class="revision"><a title="View commit information" href="{$active_repository->getCommitUrl($entry.revision)}">{$entry.revision|clean}</a></td>
              <td>
                <a class="source_item_info" title="{lang}View item info at the repository{/lang}" href="{$active_repository->getItemInfoUrl($active_commit, $source_path, $entry.revision)}">
                  <img src="{image_url name='info.png' module='source'}"/>
                </a>
              </td>
            </tr>
            {/foreach}
          {/if}
        </table>
      </div>
    </div>
  </div>
</div>