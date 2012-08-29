<?php 
  
  /**
   * xml2array() will convert the given XML text to an array in the XML 
   * structure. Link: http://www.bin-co.com/php/scripts/xml2array/ 
   * 
   * If $get_attributes is 1 the function will get the attributes as well as the 
   * tag values - this results in a different array structure in the return 
   * value
   * 
   * This is because in specific cases we need that element to be array of 
   * arrays, otherwise we'd always need to check for an exception in analyzing 
   * the array
   * 
   * @param string $contents
   * @param boolean $get_attributes 
   * @param string $on_element_make_parent_array
   * @return array
   */
  function xml2array($contents, $get_attributes=1, $on_element_make_parent_array = array()) { 
    if(!extension_loaded('xml') || !function_exists('xml_parser_create')) {
      return new Error('XML extension is not available in your PHP setup (http://www.php.net/manual/en/ref.xml.php)', true);
    } // if
    
    if(!$contents) {
      return array(); 
    } // if
    
    //Get the XML parser of PHP - PHP must have this module for the parser to work 
    $parser = xml_parser_create(); 
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0); 
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); 
    xml_parse_into_struct($parser, $contents, $xml_values); 
    xml_parser_free( $parser ); 

    if(!$xml_values) return;//Hmm... 

    //Initializations 
    $xml_array = array(); 
    $parents = array(); 
    $opened_tags = array(); 
    $arr = array(); 

    $current = &$xml_array;

    //Go through the tags. 
    foreach($xml_values as $data) { 
      unset($attributes,$value);//Remove existing values, or there will be trouble 

      //This command will extract these variables into the foreach scope 
      // tag(string), type(string), level(int), attributes(array). 
      extract($data);//We could use the array by itself, but this cooler. 

      $result = ''; 
      if($get_attributes) {//The second argument of the function decides this. 
        $result = array(); 
        if(isset($value)) {
          $result['value'] = undo_htmlspecialchars($value); 
        } // if

        //Set the attributes too. 
        if(isset($attributes)) { 
          foreach($attributes as $attr => $val) { 
              if($get_attributes == 1) $result['attr'][$attr] = undo_htmlspecialchars($val); //Set all the attributes in a array called 'attr' 
              /**  :TODO: should we change the key name to '_attr'? Someone may use the tagname 'attr'. Same goes for 'value' too */ 
          } 
        } 
      } elseif(isset($value)) { 
        $result = undo_htmlspecialchars($value); 
      } 

      //See tag status and do the needed. 
      if($type == "open") {//The starting of the tag '<tag>' 
        $parent[$level-1] = &$current; 

        if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
          
          if (in_array($tag, $on_element_make_parent_array)) { // blame Oliver
            $current[$tag][0] = $result; 
            $current = &$current[$tag][0]; 
          }
          else {
            $current[$tag] = $result; 
            $current = &$current[$tag];
          }

        } else { //There was another element with the same tag name 
          if(isset($current[$tag][0])) { 
            array_push($current[$tag], $result); 
          } else { 
            $current[$tag] = array($current[$tag],$result); 
          } 
          $last = count($current[$tag]) - 1; 
          $current = &$current[$tag][$last]; 
        } 

      } elseif($type == "complete") { //Tags that ends in 1 line '<tag />' 
          //See if the key is already taken. 
          if(!isset($current[$tag])) { //New Key
            if (in_array($tag, $on_element_make_parent_array)) { // blame Oliver, too
              $current[$tag]['0'] = $result;
            } else {
              $current[$tag] = $result;
            }

          } else { //If taken, put all things inside a list(array) 
            if((is_array($current[$tag]) and $get_attributes == 0)//If it is already an array... 
                    or (isset($current[$tag][0]) and is_array($current[$tag][0]) and $get_attributes == 1)) { 
              array_push($current[$tag],$result); // ...push the new element into that array. 
            } else { //If it is not an array... 
              $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value 
            } 
          } 

      } elseif($type == 'close') { //End of tag '</tag>' 
        $current = &$parent[$level-1]; 
      } 
    } 

    return($xml_array);
    
  }
  
?>