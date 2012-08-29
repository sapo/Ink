<h1>{$message|clean}</h1>

{if isset($additional_error_info) && $additional_error_info}
<div class="description">
  <p><strong>{lang}Additional information{/lang}:</strong></p>
  <p>{$additional_error_info|clean|clickable|nl2br}</p>
</div>
{/if}