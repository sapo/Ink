<?php

  /**
   * Diff library
   *
   * @package angie.library.diff
   */
  
  define('DIFF_LIB_PATH', ANGIE_PATH . '/classes/diff');
  
  /**
   * Render diffence between strings
   *
   * @param string $string_1
   * @param string $string_2
   * @return string
   */
  function render_diff($string_1, $string_2) {
    require_once DIFF_LIB_PATH . '/Diff.php';
    require_once DIFF_LIB_PATH . '/Diff/Renderer.php';
    require_once DIFF_LIB_PATH . '/Diff/Renderer/inline.php';
    
    $lines_1 = strpos($string_1, "\n") ? explode("\n", $string_1) : array($string_1);
    $lines_2 = strpos($string_2, "\n") ? explode("\n", $string_2) : array($string_2);
    
    $diff = new Text_Diff('auto', array($lines_1, $lines_2));
    $renderer = new Text_Diff_Renderer_inline();
    
    return $renderer->render($diff);
  } // render_diff

?>