<?php

  /**
   * Public submit module definitions
   *
   * @package activeCollab.modules.public_submit
   */
  
  return array(
  
    // Config options
    'config_options' => array(
      new ConfigOptionDefinition('public_submit_default_project', 'public_submit', 'system', '0'),
      new ConfigOptionDefinition('public_submit_enabled', 'public_submit', 'system', false),
      new ConfigOptionDefinition('public_submit_enable_captcha', 'public_submit', 'system', true),
    ),
  
  );

?>