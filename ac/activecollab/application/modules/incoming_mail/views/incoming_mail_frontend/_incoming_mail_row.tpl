<tr>
  <td>
    <a href="{$incoming_mail->getImportUrl()}" class="block"><strong>{$incoming_mail->getSubject()|clean|excerpt:45}</strong></a>
    <span class="details">{lang}From{/lang}: <a href="mailto:{$incoming_mail->getCreatedByEmail()|clean}">{$incoming_mail->getCreatedByEmail()|clean}</a></span>
  </td>
  <td>{incoming_mail_status_description code=$incoming_mail->getState()}</td>
  <td class="options">
      {link href=$incoming_mail->getImportUrl() title='Solve Conflict' class='import_button'}<img src='{image_url name=arrow-right-small.gif}' alt='' />{/link}
      {link href=$incoming_mail->getDeleteUrl() title='delete' method=post}<img src='{image_url name=gray-delete.gif}' alt='' />{/link}
  </td>
  <td class="checkbox"><input type="checkbox" name="conflicts[]" value="{$incoming_mail->getId()}" class="auto slave_checkbox input_checkbox" /></td>
</tr>