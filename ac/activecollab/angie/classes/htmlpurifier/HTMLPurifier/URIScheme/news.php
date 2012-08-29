<?php

require_once HTML_PURIFIER_LIB_PATH . '/HTMLPurifier/URIScheme.php';

/**
 * Validates news (Usenet) as defined by generic RFC 1738
 */
class HTMLPurifier_URIScheme_news extends HTMLPurifier_URIScheme {
    
    var $browsable = false;
    
    function validate(&$uri, $config, &$context) {
        parent::validate($uri, $config, $context);
        $uri->userinfo = null;
        $uri->host     = null;
        $uri->port     = null;
        $uri->query    = null;
        // typecode check needed on path
        return true;
    }
    
}

