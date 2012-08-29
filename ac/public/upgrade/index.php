<?php

  /**
   * Upgrade script main file
   * 
   * Provides chrome for upgrade script execution.
   *
   * @package activeCollab.upgrade
   */

  define('IN_UPGRADE_SCRIPT', true);
  define('UPGRADE_SCRIPT_PATH', dirname(__FILE__));;
  
  require UPGRADE_SCRIPT_PATH . '/include/include.php';

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" /> 
  <title>activeCollab Upgrade Tool</title>
  <link href="assets/style.css" rel="Stylesheet" type="text/css" />
  <script type="text/javascript" src="assets/jquery.js"></script>
  <script type="text/javascript" src="assets/jquery.blockui.js"></script>
  <script type="text/javascript">
    UpgradeUtility = function() {
      
      /**
       * All steps that need to be executed in order to upgrade to the latest 
       * version
       *
       * @var array
       */
      var steps = [];
      
      /**
       * ID of next step, incremented on step execution success
       *
       * @var integer
       */
      var next_step_num = 0;
      
      // Public interface
      return {
        
        /**
         * Initialize authentication form
         */
        init_authentication_form : function() {
          $('#authenticate form').submit(function() {
            $('#work').block('Authenticating...');
            $.ajax({
              url  : 'execute.php',
              type : 'POST',
              data : {
                'what' : 'authenticate',
                'email' : $('#adminEmail').val(),
                'password' : $('#adminPassword').val()
              },
              success : function(response) {
                $('#work').empty().append(response).unblock();
              }
            });
            return false;
          });
        },
        
        /**
         * Register new authentication step
         *
         * @param string group
         * @param string step
         * @param string item_id
         */
        register_step : function(group, step, item_id) {
          steps.push({
            'group' : group,
            'step' : step,
            'item_id' : item_id
          });
        },
        
        /**
         * Execute registered steps
         */
        next_step : function() {
          if(next_step_num == steps.length) {
            $('#all_done').css('display', 'block');
          } else {
            var list_item = $('#' + steps[next_step_num].item_id);
            var indicator_image = list_item.find('img');
            
            indicator_image.attr('src', 'assets/images/indicator.gif');
            $.ajax({
              url : 'execute.php',
              type : 'POST',
              data : {
                'what'  : 'execute_step',
                'group' : steps[next_step_num].group,
                'step'  : steps[next_step_num].step
              },
              success : function(response) {
                if(response == 'all_ok') {
                  indicator_image.attr('src', 'assets/images/ok_indicator.gif');
                  next_step_num = next_step_num + 1;
                  UpgradeUtility.next_step();
                } else {
                  indicator_image.attr('src', 'assets/images/error_indicator.gif');
                  list_item.css('color', 'red').append('<br /><span>Error: ' + response + '</span>');
                } // if
              }
            });
          } // if
        }
        
      };
      
    }();
  </script>
</head>
<body>
  <div id="wrapper">
    <div id="headerWrapper">
      <div id="header"><a href="index.php"><span>activeCollab Upgrade Tool</span></a></div>
    </div>
  
    <div id="descriptionWrapper">
      <div id="description">
        <p>This tool is designed to automate activeCollab upgrade process. Please read the instruction below in order to upgrade your installation. If you have any question please check out <a href="http://anonym.to/?http://www.activecollab.com/support/index.php?pg=kb.chapter&id=9">upgrade section</a> of activeCollab documentation.</p>
      </div>
    </div>
    <div id="contentWrapper">
      <!-- contentBlock -->
      <div id="contentBlock">
        <h2>1. Backup First!</h2>
        <p><b style="text-decoration: underline">Always create a backup of your database and files before upgrading</b>. Instructions on what to backup and how are available in <a href="http://anonym.to/?http://www.activecollab.com/support/index.php?pg=kb.chapter&id=9">this section</a> of activeCollab documentation. Backup is used to revert back to working version in case on any problem with upgrade script and troubleshooting.</p>
        
        <h2>2. Run the Script</h2>
        <p>Authenticate with your email address and password and hit Submit to run upgrade script. Only administrators will be able to log in and execute the upgrade script.</p>
        <p>When authenticated successfully system will automatically figure out your current version and prepare all the necessery steps that need to be executed in order to upgrade to the latest version.</p>
        
        <div id="work"><?php require UPGRADE_SCRIPT_PATH . '/include/authenticate.php' ?></div>
      </div>
    </div>
    <p id="footer">&copy; <?php print date('Y') ?> <a href="http://www.vbsupport/org/forum/index.php" target="_blank">NulleD By FintMax</a>. Все права зацарапаны.</p>
  </div>
</body>