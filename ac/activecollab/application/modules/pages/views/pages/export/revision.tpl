<div id="object_main_info" class="object_info">
  <h1>{lang}Page Revision{/lang} v{$revision->getVersion()}: {$revision->getName()|clean}</h1>
</div>

<div id="object_details" class="object_info"> 
  
    <dl class="properties">
    <dt>{lang}Created by{/lang}:</dt>
    <dd>{project_exporter_user_name user=$revision->getCreatedBy()}</dd>

    <dt>{lang}Created on{/lang}:</dt>
    <dd>{$revision->getCreatedOn()|date}</dd>
      
  {if $revision->getPageId()}
    <dt>{lang}Current Version{/lang}:</dt>
    <dd><a href="./page_{$revision->getPageId()}.html">{$revision->getName()|clean}</a></dd>
  {/if} 
 </dl>
  
  <div class="body">
  {$revision->getFormattedBody()}
  </div>
</div>