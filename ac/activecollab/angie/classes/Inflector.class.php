<?php

  /**
  * Pluralize and singularize English words.
  *
  * Inflector pluralizes and singularizes English nouns.
  * 
  * Inflector is taken from CakePHP project. Thanks!
  * 
  * @static
  */
  class Inflector {
  
    /**
    * Return $word in plural form.
    *
    * @param string $word Word in singular
    * @return string Word in plural
    */
    function pluralize ($word) {
      $plural_rules = array(
        '/(s)tatus$/i'             => '\1\2tatuses',
        '/^(ox)$/i'                => '\1\2en',      # ox
        '/([m|l])ouse$/i'          => '\1ice',       # mouse, louse
        '/(matr|vert|ind)ix|ex$/i' =>  '\1ices',     # matrix, vertex, index
        '/(x|ch|ss|sh)$/i'         =>  '\1es',       # search, switch, fix, box, process, address
        '/([^aeiouy]|qu)y$/i'      =>  '\1ies',      # query, ability, agency
        '/(hive)$/i'               =>  '\1s',        # archive, hive
        '/(?:([^f])fe|([lr])f)$/i' =>  '\1\2ves',    # half, safe, wife
        '/sis$/i'                  =>  'ses',        # basis, diagnosis
        '/([ti])um$/i'             =>  '\1a',        # datum, medium
        '/(p)erson$/i'             =>  '\1eople',    # person, salesperson
        '/(m)an$/i'                =>  '\1en',       # man, woman, spokesman
        '/(c)hild$/i'              =>  '\1hildren',  # child
        '/(buffal|tomat)o$/i'      =>  '\1\2oes',    # buffalo, tomato
        '/(bu)s$/i'                =>  '\1\2ses',    # bus
        '/(alias)/i'               =>  '\1es',       # alias
        '/(octop|vir)us$/i'        =>  '\1i',        # octopus, virus - virus has no defined plural (according to Latin/dictionary.com), but viri is better than viruses/viruss
        '/(ax|cri|test)is$/i'      =>  '\1es',       # axis, crisis
        '/s$/'                     =>  's',          # no change (compatibility)
        '/$/'                      =>  's');
  
      foreach ($plural_rules as $rule => $replacement) {
        if (preg_match($rule, $word)){
          return preg_replace($rule, $replacement, $word);
        } // if
      } // foreach
      
      return $word;//false;
      
    } // pluralize
  
   /**
   * Return $word in singular form.
   *
   * @param string $word Word in plural
   * @return string Word in singular
   */
    function singularize ($word) {
      
      $singular_rules = array(
        '/(s)tatuses$/i'         => '\1\2tatus',
        '/(matr)ices$/i'         =>'\1ix',
        '/(vert|ind)ices$/i'     => '\1ex',
        '/^(ox)en/i'             => '\1',
        '/(alias)es$/i'          => '\1',
        '/([octop|vir])i$/i'     => '\1us',
        '/(cris|ax|test)es$/i'   => '\1is',
        '/(shoe)s$/i'            => '\1',
        '/(o)es$/i'              => '\1',
        '/(bus)es$/i'            => '\1',
        '/([m|l])ice$/i'         => '\1ouse',
        '/(x|ch|ss|sh)es$/i'     => '\1',
        '/(m)ovies$/i'           => '\1\2ovie',
        '/(s)eries$/i'           => '\1\2eries',
        '/([^aeiouy]|qu)ies$/i'  => '\1y',
        '/([lr])ves$/i'          => '\1f',
        '/(tive)s$/i'            => '\1',
        '/(hive)s$/i'            => '\1',
        '/([^f])ves$/i'          => '\1fe',
        '/(^analy)ses$/i'        => '\1sis',
        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
        '/([ti])a$/i'            => '\1um',
        '/(p)eople$/i'           => '\1\2erson',
        '/(m)en$/i'              => '\1an',
        '/(c)hildren$/i'         => '\1\2hild',
        '/(n)ews$/i'             => '\1\2ews',
        '/s$/i'                  => '');
  
      foreach ($singular_rules as $rule => $replacement) {
        if (preg_match($rule, $word)) {
          return preg_replace($rule, $replacement, $word);
        } // if
      } // foreach
      
      // should not return false is not matched
      return $word;//false;
      
    }
  
    /**
    * Returns given $lower_case_and_underscored_word as a camelCased word.
    *
    * @param string $lower_case_and_underscored_word Word to camelize
    * @return string Camelized word. likeThis.
    */
    function camelize($lower_case_and_underscored_word) {
      return str_replace(" ","",ucwords(str_replace("_"," ",$lower_case_and_underscored_word)));
    }
  
    /**
    * Returns an underscore-syntaxed ($like_this_dear_reader) version of the $camel_cased_word.
    *
    * @param string $camel_cased_word Camel-cased word to be "underscorized"
    * @return string Underscore-syntaxed version of the $camel_cased_word
    */
    function underscore($camel_cased_word) {
      $camel_cased_word = preg_replace('/([A-Z]+)([A-Z])/','\1_\2', $camel_cased_word);
      return strtolower(preg_replace('/([a-z])([A-Z])/','\1_\2', $camel_cased_word));
    }
  
    /**
    * Returns a human-readable string from $lower_case_and_underscored_word,
    * by replacing underscores with a space, and by upper-casing the initial characters.
    *
    * @param string $lower_case_and_underscored_word String to be made more readable
    * @return string Human-readable string
    */
    function humanize($lower_case_and_underscored_word) {
      return ucwords(str_replace("_"," ",$lower_case_and_underscored_word));
    }
    
    /**
     * Create slug from string
     *
     * @param string $string
     * @param string $replacement
     * @return string
     */
    function slug($string, $replacement = '-') {
      $string = strtolower_utf($string);
      
      $quoted_replacement = preg_quote($replacement, '/');
      
      $map = array(
        '/à|á|å|â|а/' => 'a',
        '/б/' => 'b',
        '/в/' => 'v',
        '/г/' => 'g',
        '/д/' => 'd',
        '/ђ|đ/' => 'dj',
        '/џ/' => 'dz',
        '/è|é|ê|ẽ|ë/' => 'e',
        '/ж|ž|з/' => 'z',
        '/ì|í|î|и/' => 'i',
        '/ј/' => 'j',
        '/к/' => 'k',
        '/л/' => 'l',
        '/љ/' => 'lj',
        '/м/' => 'm',
        '/њ/' => 'nj',
        '/ò|ó|ô|ø|о/' => 'o',
        '/п/' => 'p',
        '/р/' => 'r',
        '/с|š|ш/' => 's',
        '/т/' => 't',
        '/ù|ú|ů|û|у/' => 'u',
        '/ç|č|ć|ц|ч|ћ/' => 'c',
        '/ñ|н/' => 'n',
        '/ф/' => 'f',
        '/х/' => 'h',
        '/ä|æ/' => 'ae',
        '/ö/' => 'oe',
        '/ü/' => 'ue',
        '/[^\w\s]/u' => ' ',
        '/\\s+/' => $replacement,
        "'/^[$quoted_replacement]+|[$quoted_replacement]+$/'" => '',
      );
      return trim(preg_replace(array_keys($map), array_values($map), $string), '-');
    } // slug
    
  } // Inflector

?>