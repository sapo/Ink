{if is_foreachable($_subpages)}
<div class="resource object_subpages object_section">
  <div class="head">
    <h2 class="section_name"><span class="section_name_span">{lang}Subpages{/lang}</span></h2>
  </div>
  <div class="body">
    {pages_tree pages=$_subpages user=$logged_user show_visibility=no}
  </div>
</div>
{/if}