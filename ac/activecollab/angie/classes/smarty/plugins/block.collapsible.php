<?php

  /**
  * Render collapsible fieldset block
  *
  * @param array $params
  * @param string $content
  * @param Smarty $smarty
  * @param boolean $repeat
  * @return string
  */
  function smarty_block_collapsible($params, $content, &$smarty, &$repeat) {
    $id = clean(trim(array_var($params, 'id')));
    if($id == '') {
      return new InvalidParamError('id', $id, "Requested paraeter for collapsible helper is missing: 'in'", true);
    } // if
    
    $title = clean(trim(array_var($params, 'title')));
    if($title == '') {
      return new InvalidParamError('title', $title, "Requested paraeter for collapsible helper is missing: 'title'", true);
    } // if
    
    return "<fieldset id=\"$id\">\n
      <legend>$title</legend>\n
      $content\n
      </fieldset>\n
      <script type=\"text/javascript\">\n
        $('#$id').collapsible();\n
      </script>";
  } // smarty_block_collapsible

?>