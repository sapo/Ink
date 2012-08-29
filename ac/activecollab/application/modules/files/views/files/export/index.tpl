<div id="object_main_info" class="object_info">
  <h1>{lang}Files{/lang}</h1>
</div>

<div class="category_sidebar">
  {project_exporter_list_categories categories=$categories current_category=$current_category title="Files" url_prefix="./"}
</div>
<div class="category_objects">
  {if is_foreachable($objects)}
    {project_exporter_list_objects objects=$objects url_prefix='./'}
  {else}
    <p>{lang}There are no files on this page{/lang}</p>
  {/if}
</div>
<div class="clear"></div>