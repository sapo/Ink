{title}Visibility{/title}
{add_bread_crumb}Visibility Details{/add_bread_crumb}

<div id="object_visibility_details">
  <dl class="details_list" {if $active_object->getVisibility() <= VISIBILITY_PRIVATE}style="background-image: url('{image_url name=private.gif}')"{/if}>
    <dt>{lang}Object{/lang}</dt>
    <dd>{object_link object=$active_object}</dd>
    <dt>{lang}Project{/lang}</dt>
    <dd>{project_link project=$active_object->getProject()}</dd>
    <dt>{lang}Visibility{/lang}</dt>
  {if $active_object->getVisibility() == VISIBILITY_PRIVATE}
    <dd>{lang}Private{/lang}</dd>
  {else}
    <dd>{lang}Normal{/lang}</dd>
  {/if}
  </dl>
{if $active_object->getVisibility() <= VISIBILITY_PRIVATE}
  <p>{lang project=$active_project->getName()}This object is visible only to member with following roles involved with ":project" project{/lang}:</p>
  <ol>
  {foreach from=$private_roles item=private_role}
    <li>{$private_role->getName()|clean}</li>
  {/foreach}
  </ol>
{else}
  <p>{lang project=$active_project->getName()}This object is visible to anyone involved with ":project" project{/lang}.</p>
{/if}
</div>