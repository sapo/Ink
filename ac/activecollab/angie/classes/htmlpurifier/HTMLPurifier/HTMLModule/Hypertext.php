<?php

require_once HTML_PURIFIER_LIB_PATH . '/HTMLPurifier/HTMLModule.php';
require_once HTML_PURIFIER_LIB_PATH . '/HTMLPurifier/AttrDef/HTML/LinkTypes.php';

/**
 * XHTML 1.1 Hypertext Module, defines hypertext links. Core Module.
 */
class HTMLPurifier_HTMLModule_Hypertext extends HTMLPurifier_HTMLModule
{
    
    var $name = 'Hypertext';
    
    function setup($config) {
        $a =& $this->addElement(
            'a', true, 'Inline', 'Inline', 'Common',
            array(
                // 'accesskey' => 'Character',
                // 'charset' => 'Charset',
                'href' => 'URI',
                // 'hreflang' => 'LanguageCode',
                'rel' => new HTMLPurifier_AttrDef_HTML_LinkTypes('rel'),
                'rev' => new HTMLPurifier_AttrDef_HTML_LinkTypes('rev'),
                // 'tabindex' => 'Number',
                // 'type' => 'ContentType',
            )
        );
        $a->excludes = array('a' => true);
    }
    
}

