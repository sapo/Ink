<div id="object_main_info" class="object_info">
  <h1>{lang}Pages{/lang}</h1>
</div>

<div class="category_sidebar">
   {project_exporter_list_categories categories=$categories current_category=$current_category}
</div>

<div class="category_objects">
  {if is_foreachable($objects)}
    {project_exporter_pages_tree objects=$objects visibility=$visibility}
  {else}
    <p>{lang}Choose Category{/lang}</p>
  {/if}
</div>
<div class="clear"></div>