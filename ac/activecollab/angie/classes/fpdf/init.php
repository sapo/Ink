<?php

  /**
   * FPDF initialization file
   *
   * @package angie.library
   * @subpackage fpdf
   */
  
  define('FPDF_PATH', ANGIE_PATH . '/classes/fpdf');
  require_once FPDF_PATH . '/FPDF.class.php';

  // Paper sizes
  define('PAPER_FORMAT_A3' , 'A3');
  define('PAPER_FORMAT_A4' , 'A4');
  define('PAPER_FORMAT_A5' , 'A5');
  define('PAPER_FORMAT_LETTER' , 'Letter');
  define('PAPER_FORMAT_LEGAL' , 'Legal');
  
  define('DEFAULT_PAPER_FORMAT', PAPER_FORMAT_A3);
  
  // Paper orientation
  define('PAPER_ORIENTATION_PORTRAIT', 'Portrait');
  define('PAPER_ORIENTATION_LANDSCAPE', 'Landscape');
  
  define('DEFAULT_PAPER_ORIENTATION', PAPER_ORIENTATION_PORTRAIT);

?>