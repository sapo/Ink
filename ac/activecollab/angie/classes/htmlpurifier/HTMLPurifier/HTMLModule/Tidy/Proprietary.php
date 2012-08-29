<?php

require_once HTML_PURIFIER_LIB_PATH . '/HTMLPurifier/HTMLModule/Tidy.php';

class HTMLPurifier_HTMLModule_Tidy_Proprietary extends
      HTMLPurifier_HTMLModule_Tidy
{
    
    var $name = 'Tidy_Proprietary';
    var $defaultLevel = 'light';
    
    function makeFixes() {
        return array();
    }
    
}

