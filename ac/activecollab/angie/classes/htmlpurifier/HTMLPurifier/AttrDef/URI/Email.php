<?php

require_once HTML_PURIFIER_LIB_PATH . '/HTMLPurifier/AttrDef.php';

class HTMLPurifier_AttrDef_URI_Email extends HTMLPurifier_AttrDef
{
    
    /**
     * Unpacks a mailbox into its display-name and address
     */
    function unpack($string) {
        // needs to be implemented
    }
    
}

// sub-implementations
require_once HTML_PURIFIER_LIB_PATH . '/HTMLPurifier/AttrDef/URI/Email/SimpleCheck.php';
