{title}Update repository{/title}
{add_bread_crumb}Update{/add_bread_crumb}

<div id="repository_update">
  <div id="repository_update_progress">
    <div id="progress_content"></div>
  </div>
</div>
<script type="text/javascript">
  App.data.repository_uptodate = {$uptodate};
  App.data.repository_head_revision = {$head_revision};
  App.data.repository_last_revision = {$last_revision};
  App.data.repository_update_url = '{$repository_update_url}';
  
  App.source.controllers.repository.update();
</script>