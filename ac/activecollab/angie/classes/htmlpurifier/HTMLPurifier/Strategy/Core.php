<?php

require_once HTML_PURIFIER_LIB_PATH . '/HTMLPurifier/Strategy/Composite.php';

require_once HTML_PURIFIER_LIB_PATH . '/HTMLPurifier/Strategy/RemoveForeignElements.php';
require_once HTML_PURIFIER_LIB_PATH . '/HTMLPurifier/Strategy/MakeWellFormed.php';
require_once HTML_PURIFIER_LIB_PATH . '/HTMLPurifier/Strategy/FixNesting.php';
require_once HTML_PURIFIER_LIB_PATH . '/HTMLPurifier/Strategy/ValidateAttributes.php';

/**
 * Core strategy composed of the big four strategies.
 */
class HTMLPurifier_Strategy_Core extends HTMLPurifier_Strategy_Composite
{
    
    function HTMLPurifier_Strategy_Core() {
        $this->strategies[] = new HTMLPurifier_Strategy_RemoveForeignElements();
        $this->strategies[] = new HTMLPurifier_Strategy_MakeWellFormed();
        $this->strategies[] = new HTMLPurifier_Strategy_FixNesting();
        $this->strategies[] = new HTMLPurifier_Strategy_ValidateAttributes();
    }
    
}

