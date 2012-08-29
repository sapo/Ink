<div id="repository_item_info">
  {if $error eq false}
  <div id="repository_update_progress">
    <div id="progress_content">
      <pre>{$info}</pre>
    </div>
  </div>
  {else}
  <p>{lang}Error getting information{/lang}:</p>
  <p>{$error_message}</p>
  {/if}
</div>