<?php

  /**
   * Init bayesian library
   * 
   * @package angie.library.bayesian
   */

  // path related
  define('BAYESIAN_LIB_PATH', ANGIE_PATH . '/classes/bayesian');
  
  // database related
  define('BAYESIAN_TOKENS_TABLENAME', 'bayesian_tokens');
  
  // used constants
  define('BAYESIAN_MAXIMUM_TOKEN_LENGTH', 30);
  define('BAYESIAN_MINIMUM_TOKEN_LENGTH', 3);
  define('BAYESIAN_SPAM_PROBABILITY', 0.5);
  define('BAYESIAN_INITIAL_THRESHOLD', 10);

  // init bayesian class
  include_once BAYESIAN_LIB_PATH . '/bayesian.class.php';
?>