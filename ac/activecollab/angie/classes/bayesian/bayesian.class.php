<?php
  /**
   * class for bayesian spam filtering
   * 
   * Usage:
   * - Bayesian::isSpam($content) // test if $content is spam
   * - Bayesian::setAsSpam($content); // treat content as spam, and learn system
   * - Bayesian::setAsNotSpam($content) // treat content as not spam, and learn system
   * 
   * @package angie.library.bayesian
   */
  class Bayesian extends VisanaObject {   
    
    /**
     * bayesian construct method to prevent making instances of this class
     * (this class is soley intended to use statically)
     *
     * @param void
     * @return null
     */
    function __construct() {
      new Error(lang('This class is intended to be used statically'));
    } // __construct
    
    
    /**
     * chack if $content is spam
     *
     * @param string $content
     */
    function isSpam(&$content) {
      $tokens = Bayesian::getTokens($content);
      // if there is no tokens, email is most probably not spam
      if (!is_foreachable($tokens)) {
        return false;
      } // if
      
      $table_name = TABLE_PREFIX . BAYESIAN_TOKENS_TABLENAME;
      $total_count_ham = db_execute_one("SELECT count(*) as count FROM `$table_name` WHERE `type`='ham'"); $total_count_ham = array_var($total_count_ham,'count',0);
      $total_count_spam = db_execute_one("SELECT count(*) as count FROM `$table_name` WHERE `type`='spam'"); $total_count_spam = array_var($total_count_spam,'count',0);
      
      // there is no learning data
      if (!$total_count_ham || !$total_count_spam) {
        
      } // if
      
      $database_tokens = db_execute_all("SELECT * FROM `$table_name` WHERE `token` IN (" . implode(',', Bayesian::escapeTokens($tokens)) . ")");
      $probabilities = Bayesian::getIndividualProbabilities($tokens, $database_tokens, $total_count_ham, $total_count_spam);
      return Bayesian::getCombinedProbability($probabilities);
      
      // if we don't have good sample rate, we need to mark all $content as spam so we can learn something
      if (($count_ham + $count_spam) < BAYESIAN_INITIAL_THRESHOLD && false) {
        return true;
      } // if
    } // isSpam
    
    
    /**
     * set content as spam
     * 
     * @param string $content
     */
    function setAsSpam(&$content) {
      return Bayesian::learn($content, -1);
    } // setAsSpam
    
    
    /**
     * set content as not spam
     *
     * @param string $content
     */
    function setAsNotSpam(&$content) {
      return Bayesian::learn($content, +1);
    } // setAsNotSpam
    
    
    /**
     * write what we have learned to database
     *
     * @param string $content
     * @param integer $karma_step
     * @return boolean
     */
    function learn(&$content, $karma_step = 1) {
      $tokens = Bayesian::getTokens($content);
      if (!is_foreachable($tokens)) {
        return false;
      } // if
      
      if ($karma_step < 0) {
        $token_type = 'spam';
      } else {
        $token_type = 'ham';
      } // if
      
      $table_name = TABLE_PREFIX . BAYESIAN_TOKENS_TABLENAME;
      $database_tokens = db_execute("SELECT `token` as `token` FROM `$table_name` WHERE `token` IN (" . implode(',', Bayesian::escapeTokens($tokens)) . ") AND `type`='$token_type'");
      if (is_foreachable($database_tokens)) {
        $database_tokens_filtered = array();
        foreach ($database_tokens as $database_token) {
          $database_tokens_filtered[] = array_var($database_token, 'token');
        } // foreach
        
        $new_tokens = array();
        for ($x = 0; $x < count($tokens); $x++) {
          if (!in_array($tokens[$x], $database_tokens_filtered)) {
            $new_tokens[] = $tokens[$x];
            unset($tokens[$x]);
          } // if
        } // for        
      } else {
        $new_tokens = $tokens;
        unset($tokens);
      } // if
      
      // update existing tokens
      if (is_foreachable($tokens)) {
        db_execute("UPDATE `$table_name` SET `points` = `points` + 1 WHERE `token` IN (" . implode(',' , Bayesian::escapeTokens($tokens)) . ") AND `type`='$token_type'");
      } // if
      
      // create new tokens
      if (is_foreachable($new_tokens)) {
        $new_tokens = Bayesian::escapeTokens($new_tokens);
       foreach ($new_tokens as $new_token) {
          $parts[] = "($new_token, '$token_type', 1)";
       } // foreach
       db_execute("INSERT INTO `$table_name` (token,type,points) VALUES " . implode(',', $parts));
      } // if
      
      return true;
    } // learn
    
    
    /**
     * split $content into bayesian relevant words (tokens)
     *
     * @param string $content
     * @return array
     */
    function getTokens(&$content) {
      // if there is no content, there will be no tokens
      if (!$content) {
        return array();
      } // if
      
      $maximum_token_length = BAYESIAN_MAXIMUM_TOKEN_LENGTH;
      $minimum_token_length = BAYESIAN_MINIMUM_TOKEN_LENGTH;
      
      // container for all tokens
      $all_tokens = array();
      
      // we need lowercased content
      $content = strtolower($content);
      
      // list of matches that needs to be preserved (add criterias as much you need)
      $preserve_matches = array(
        array('expression' => '/([A-Za-z0-9\\_\\-\\.\\+]+\\@[A-Za-z0-9_\\-\\.]+)/', 'result_index' => 0, 'data' => 'email_address'), // preserve email addresses
        array('expression' => '/(\\d+\\.\\d+\\.\\d+.\\d+)/', 'result_index' => 0, 'data' => 'ip_address'), // preserve ip addresses
        array('expression' => '/http\\:\\/\\/([A-Za-z0-9\\_\\-\\.\\/]+)/', 'result_index' => 0, 'data' => 'url'), // preserve web url-s
      );
      
      $ignored_hosts = array(
        'gmail', 'google', 'hotmail', 'yahoo', 'mail', 'microsoft'
      );
      
      // extract matches from content, considering ignored hosts
      if (is_foreachable($preserve_matches)) {
        foreach ($preserve_matches as $preserve_match) {
          preg_match_all(array_var($preserve_match, 'expression'), $content, $matches);
          if (is_foreachable($matches) && isset($matches[array_var($preserve_match, 'result_index')])) {
            $matches = $matches[array_var($preserve_match, 'result_index')];
            $content = str_replace($matches, ' ', $content);
            switch (array_var($preserve_match, 'data')) {
            	case 'email_address':
            	  // we can't use public emails for consideration
            	  for ($x = 0; $x < count($matches); $x++) {
            	    $is_ignored = false;
            	    foreach ($ignored_hosts as $ignored_host) {
            	      if (strpos_utf($matches[$x], '@'.$ignored_host.'.')) {
            	        $is_ignored = true;
            	        continue;
            	      } // if
            	    } // if
            	    if ($is_ignored) {
            	      unset($matches[$x]);
            	    } // if
            	  } // for
            		break;
            		
          		case 'url':
            	  // we can't use public emails for consideration
            	  for ($x = 0; $x < count($matches); $x++) {
            	    $parse_url = parse_url($matches[$x]);
            	    $matches[$x] = array_var($parse_url, 'host');
            	    $is_ignored = false;
            	    foreach ($ignored_hosts as $ignored_host) {
            	      if (strpos_utf($matches[$x], $ignored_host.'.')) {
            	        $is_ignored = true;
            	        continue;
            	      } // if
            	    } // if
            	    if ($is_ignored) {
            	      unset($matches[$x]);
            	    } // if
            	  } // for
          		  break;
            }
            $all_tokens = array_merge($all_tokens, $matches);
          } // if
        } // foreach
      } // if
      
      // now when we have extracted valuable data, we need to strip rest of the content of html tags
      $content = strip_tags(trim($content));
      
      // break content string by any whitespace character, and choose words (tokens) that fits our needs
      // length must be in scope $maximum_token_length - $minimum_token_length and it cannot be empty string or some kind of numeric (integer or string)
      
      // $possible_tokens = preg_split( "/[\\s,.:;\"!?\\\`]+/", $content); // obsolete (it's better to treat all printable signs as part of token
      $possible_tokens = preg_split( "/[\\s]+/", $content);
      if (is_foreachable($possible_tokens)) {
        foreach ($possible_tokens as $possible_token) {
        	$possible_token = trim($possible_token);
        	$token_length = strlen_utf($possible_token);
        	if (($token_length > 0) && ($token_length >= $minimum_token_length) && ($token_length <= $maximum_token_length) && !is_numeric($possible_token) && !in_array($possible_token, $all_tokens)) {
            $all_tokens[] = $possible_token;
        	} // if
        } // foreach
      } // if
      return $all_tokens;
    } // getTokens
    
    
    /**
     * escape tokens for database access
     *
     * @param array $tokens
     * @return array
     */
    function escapeTokens($tokens) {
      if (!is_foreachable($tokens)) {
        return false;
      } // if
      for ($x = 0; $x < count($tokens); $x++) {
        $tokens[$x] = db_escape($tokens[$x]);
      } // for
      return $tokens;
    } // escapeTokens
    
    
    /**
     * Return total number of points for $token
     * 
     * @param $keyword
     * @param $token
     * @return array
     */
    function getTokenPoints(&$tokens, $token) {
      $return = array(0,0);
      $found_spam = false;
      $found_ham = false;
      for ($x = 0; $x < count($tokens); $x++) {
        if ($tokens[$x]['token'] == $token && $tokens[$x]['type'] == 'spam') {
          $return[0] = $tokens[$x]['points'];
          $found_spam = true;
        } // if
        if ($tokens[$x]['token'] == $token && $tokens[$x]['type'] == 'ham') {
          $return[1] = $tokens[$x]['points'];
          $found_ham = true;
        } // if
        if ($found_ham && $found_spam) {
          return $return;
        } // if
      } // for
      return $return;
    } // function
    
    
    /**
     * Calculate individual Probabilities for tokens
     * 
     * probability = (spamHits / totalSpam) / ((spamHits / totalSpam) + (hamHits / totalHam))
     * 
     * Read more
     * - http://www.paulgraham.com/spam.html
     * - http://en.wikipedia.org/wiki/Likelihood_function
     * - http://www.alikonweb.it/451/index.php?option=com_content&task=view&id=130&Itemid=2
     * - http://zachwingo.info/2007/08/15/bayesian-spam-filtering/
     *
     * @param array $tokens
     * @param array $database_tokens
     * @param array $total_count_ham
     * @param array $total_count_spam
     * @return array
     */
    function getIndividualProbabilities(&$tokens, &$database_tokens, $total_count_ham, $total_count_spam) {
      $probabilities = array();
      foreach ($tokens as $token) {
        list($token_spam_points, $token_ham_points) = Bayesian::getTokenPoints($database_tokens, $token);
         $probabilities[$token] = ($token_spam_points / $total_count_spam) / (($token_spam_points / $total_count_spam) + ($token_ham_points / $total_count_ham));
         $probabilities[$token] = $probabilities[$token] >=1 ? 0.99 : $probabilities[$token] <=0 ? 0.01 : $probabilities[$token];
      } // foreach
      return $probabilities;
    } // getIndividualProbabilities
    
    
    /**
     * Calculate combined probability
     * 
     *  P= (P1 * P2 * ... * P15) / ( (P1 * P2 * ... * P15) + ((1-P1) * (1-P2) * ... * (1-P15)));
     *  substitute (P1 * P2 * ... * P15) with $positive_probability
     *  substitute ((1-P1) * (1-P2) * ... * (1-P15)) with $negative_probability and we get this formula
     * 
     *  P = $positive_probability / ($positive_probability + $negative_probability)
     * 
     *  wanna read more on this subject:
     *  - http://www.paulgraham.com/naivebayes.html
     * 
     *  @param array $probabilities
     *  @return float
     */
    function getCombinedProbability(&$probabilities) {
      asort(&$probabilities);
      $positive_probability = 1;
      $negative_probability = 1;
      foreach ($probabilities as $probability) {
        // ose only words that are probable spam
        if ($probability > 0.5) {
        	$positive_probability = $positive_probability * $probability;
        	$negative_probability = $negative_probability * (1 -$probability);
        } // if
      } // if
      $total_probability = ($positive_probability) / (($positive_probability) + ($negative_probability));
      return $total_probability;
    } // getCombinedProbability
    
  } // Bayesian

?>