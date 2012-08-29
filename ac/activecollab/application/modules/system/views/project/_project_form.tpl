<div class="form_left_col">
  {wrap field=name}
    {label for=projectName required=1}Name{/label}
    {text_field name='project[name]' value=$project_data.name id=projectName class=title}
  {/wrap}
  
  {wrap field=overview}
    {label for=projectOverview}Overview{/label}
    {editor_field name='project[overview]' id=projectOverview inline_attachments=$project_data.inline_attachments}{$project_data.overview}{/editor_field}
  {/wrap}

{if $logged_user->canSeePrivate()}
  {assign_var name=project_visibility_normal_caption}{lang}Normal &mdash; <span class="details">Visible to everyone involved with the project</span>{/lang}{/assign_var}
  {assign_var name=project_visibility_private_caption}{lang}Private &mdash; <span class="details">Visible only to people with can_see_private_objects role permission</span>{/lang}{/assign_var}

  {wrap field=default_visibility}
    {label for=projectVisibility}Default Visibility{/label}
    {select_visibility name='project[default_visibility]' value=$project_data.default_visibility id=projectVisibility normal_caption=$project_visibility_normal_caption private_caption=$project_visibility_private_caption}
  {/wrap}
{else}
  <input type="hidden" name="project[default_visibility]" value="{$project_data.default_visibility}" />
{/if}
</div>

<div class="form_right_col">
{if $logged_user->isOwner()}
  {wrap field=leader_id}
    {label for=projectLader required=yes}Leader{/label}
    {select_user name='project[leader_id]' value=$project_data.leader_id id="projectLader" optional=no}
  {/wrap}

  {wrap field=company_id}
    {label for=projectCompany}Client{/label}
    {select_company name='project[company_id]' value=$project_data.company_id id=projectCompany optional=yes exclude=$owner_company->getId()}
  {/wrap}
{else}
  {wrap field=leader_id}
    {label for=projectLader required=yes}Leader{/label}
    {select_user name='project[leader_id]' value=$project_data.leader_id id="projectLader" users=$logged_user->visibleUserIds() optional=no}
  {/wrap}

  {wrap field=company_id}
    {label for=projectCompany}Client{/label}
    {select_company name='project[company_id]' value=$project_data.company_id id=projectCompany companies=$logged_user->visibleCompanyIds() optional=yes exclude=$owner_company->getId()}
  {/wrap}
{/if}

  {wrap field=group_id}
    {label for=projectGroup}Group{/label}
    {select_project_group name='project[group_id]' value=$project_data.group_id id="projectGroup" optional=yes}
  {/wrap}
  
  {wrap field=starts_on}
    {label for=projectStartsOn}Starts On{/label}
    {select_date name='project[starts_on]' value=$project_data.starts_on id="projectStartsOn"}
  {/wrap}
</div>
<div class="clear"></div>