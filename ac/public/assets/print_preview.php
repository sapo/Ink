<?php
  header("Content-type: text/css; charset: UTF-8");
  header("Cache-Control: max-age=8640000");
  header("Expires: " . gmdate("D, d M Y H:i:s", time() + 8640000) . " GMT");
?>
@import "print.php";
@import "stylesheets/print_preview.css";
<?php 
$theme_print_style = isset($_GET['theme_name']) && $_GET['theme_name'] ? $_GET['theme_name'] : null;
if ($theme_print_style) {
  echo "@import \"themes/$theme_print_style/print.css\";\n";
  echo "@import \"themes/$theme_print_style/print_preview.css\";\n";
} // if
?>