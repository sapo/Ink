<?php

  /**
   * Main installer file
   *
   * @package activeCollab.installer
   */

  include_once 'include.php'; 
  $all_ok = true;
  
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" /> 
  <title>activeCollab installer</title>
  <link href="assets/style.css" rel="Stylesheet" type="text/css" />
</head>
<body>
  <div id="wrapper"><div id="headerWrapper">
    <div id="header">
      <a href="index.php"><span>activeCollab Installer</span></a>
    </div>
  </div>
  
  <div id="descriptionWrapper">
    <div id="description">
      <p>This tool will guide you through activeCollab installation process. If you have any question please check out <a href="http://anonym.to/?http://www.activecollab.com/support/index.php?pg=kb.page&id=2">installation section</a> of activeCollab documentation. If from any reason this tool fails to install activeCollab please contact <a href="http://anonym.to/?http://www.activecollab.com/support/index.php?pg=request">activeCollab support</a>.</p>
    </div>
  </div>
  <div id="contentWrapper">
    <!-- contentBlock -->
    <div id="contentBlock">
      <h2>1. Environment test</h2>
      <div id="legend">
        <ul>
          <li class="ok"><span>ok</span> &mdash; All OK</li>
          <li class="warning"><span>warning</span> &mdash; Not a deal breaker, but it's recommended to have this installed for some features to work</li>
          <li class="error"><span>error</span> &mdash; activeCollab require this feature and can't work without it</li>
        </ul>
      </div>
      <ul>
<?php

  // ---------------------------------------------------
  //  Probe
  // ---------------------------------------------------
  
  $results = array();
        
  validate_is_installed($results);
  validate_php($results);
  validate_extensions($results);
  validate_safe_mode($results);
  validate_zend_compatibility_mode($results);
  validate_is_writable($results);
  
  foreach($results as $result) {
    print '<li class="' . $result->status . '"><span>' . $result->status . '</span> &mdash; ' . $result->message . '</li>';
    
    if($result->status == STATUS_ERROR) {
      $all_ok = false;
    } // if
  } // foreach

?>
      </ul>
      
<?php

  if($all_ok) {
    
    $installed = false;
    
?>
    <h2>2. Install activeCollab</h2>
    
    <?php if(array_var($_POST, 'submitted') == 'submitted') { ?>
    <p>Installation process:</p>
    <ul>
    <?php
    
      $installation = new Installation();
      
      $installation->root_path = INSTALLATION_PATH . '/activecollab';
      $installation->company_name = array_var($_POST, 'company_name');
      $installation->absolute_url = array_var($_POST, 'absolute_url');
      $installation->email = array_var($_POST, 'email');
      $installation->password = array_var($_POST, 'password');
      $installation->database_host = array_var($_POST, 'db_host');
      $installation->database_username = array_var($_POST, 'db_user');
      $installation->database_password = array_var($_POST, 'db_pass');
      $installation->database_name = array_var($_POST, 'db_name');
      $installation->table_prefix = array_var($_POST, 'table_prefix');
      $installation->license_accepeted = (boolean) array_var($_POST, 'sla_agreed');
      
      $installed = $installation->execute();
      
    ?>
    </ul>
    <?php } // if ?>
    
    <?php if($installed) { ?>
    <h2>3. Success!</h2>
    <p>activeCollab has been <span style="background: green; color: white; font-weight: bold">successfully</span> installed. Please <strong>delete /public/installer folder</strong> and than go visit your <a href="<?php echo clean($installation->absolute_url) ?>" target="_blank">newly created installation</a>. You can login with:</p>
    <ul>
      <li>Email: <?php echo clean($installation->email) ?></li>
      <li>Password: <?php echo clean($installation->password) ?></li>
    </ul>
    <p>Don't forget the online resources: <a href="http://anonym.to/?http://www.activecollab.com/blogs/">Blogs</a>, <a href="http://anonym.to/?http://www.activecollab.com/community/">Community</a> and <a href="http://anonym.to/?http://www.activecollab.com/support/">Support</a>.</p>
    <?php } else { ?>
    <form action="index.php" method="post" enctype="multipart/form-data">
    
      <!-- System settings -->
      <fieldset>
        <legend>System Settings</legend>
        
        <img src="assets/images/system_settings.gif" alt="Database" />
        
        <div class="ctrlHolder">
          <label>Base Url</label>
        <?php if(isset($_POST['absolute_url'])) { ?>
          <span style="text-decoration: underline; color: black;"><?php echo clean(array_var($_POST, 'absolute_url')) ?></span>
          <input type="hidden" name="absolute_url" value="<?php echo clean(array_var($_POST, 'absolute_url')) ?>" />
        <?php } else { ?>
        <?php
          $port = array_var($_SERVER, 'SERVER_PORT', 80);
        
          if((strtolower(array_var($_SERVER, 'HTTPS')) == 'on') || ($port == 443)) {
            $protocol = 'https://';
          } else {
            $protocol = 'http://';
          } // if
          
          if($port != 80 && $port != 443) {
            $request_url = without_slash($protocol . dirname($_SERVER['HTTP_HOST'] . ':' . $port . $_SERVER['SCRIPT_NAME']));
          } else {
            $request_url = without_slash($protocol . dirname($_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']));
          } // if
          
          
          if(($rpos = strrpos($request_url, '/')) !== false) {
            $installation_url = substr($request_url, 0, $rpos); // remove /public ;)
          } else {
            $installation_url = '';
          } // if
        ?>
        <?php if($installation_url) { ?>
          <span style="text-decoration: underline; color: black;"><?php echo clean($installation_url) ?></span>
          <input type="hidden" name="absolute_url" value="<?php echo clean($installation_url) ?>" />
          <p class="aid">This value can be changed in config/config.php after installation. Please note that <u>/public part is mandatory</u> unless you are using URL rewriting to have <a href="http://anonym.to/?https://www.activecollab.com/support/index.php?pg=kb.page&id=26" target="_blank">clean URL-s</a>.</p>
        <?php } else { ?>
          <input type="text" name="absolute_url" value="" class="long" />
        <?php } // if ?>
        <?php } // if ?>
        </div>
      </fieldset>
      
      <!-- Company and administrator -->
      <fieldset>
        <legend>Company and Administrator</legend>
        
        <img src="assets/images/company_and_administrator.gif" alt="Database" />
        
        <div class="ctrlHolder">
          <label>Company Name</label>
          <input type="text" name="company_name" value="<?php echo clean(array_var($_POST, 'company_name')) ?>" />
        </div>
        
        <div class="ctrlHolder">
          <label>Administrators Email</label>
          <input type="text" name="email" value="<?php echo clean(array_var($_POST, 'email')) ?>" />
          <p class="aid">This address will be used as administrators login and for system notifications in case of any system failure.</p>
        </div>
        <div class="ctrlHolder">
          <label>Administrators Password</label>
          <input type="password" name="password" value="<?php echo clean(array_var($_POST, 'password')) ?>" />
        </div>
        
      </fieldset>
      
      <!-- Database connection -->
      <fieldset id="database_connection">
        <legend>Database Connection</legend>
        
        <img src="assets/images/database_connection.gif" alt="Database" />
        
        <div class="ctrlHolder">
          <label>Hostname</label>
          <input type="text" name="db_host" value="<?php echo clean(array_var($_POST, 'db_host', 'localhost')) ?>" />
        </div>
        <div class="ctrlHolder">
          <label>Username</label>
          <input type="text" name="db_user" value="<?php echo clean(array_var($_POST, 'db_user')) ?>" />
        </div>
        <div class="ctrlHolder">
          <label>Password</label>
          <input type="password" name="db_pass" value="<?php echo clean(array_var($_POST, 'db_pass')) ?>" />
        </div>
        <div class="ctrlHolder">
          <label>Database Name</label>
          <input type="text" name="db_name" value="<?php echo clean(array_var($_POST, 'db_name')) ?>" />
          <p class="aid">Installer <b>will not create the database</b> for you. It needs to be already created before you hit Submit button!</p>
        </div>
        <div class="ctrlHolder">
          <label>Table Prefix</label>
          <input type="text" name="table_prefix" value="<?php echo clean(array_var($_POST, 'table_prefix', 'acx_')) ?>" class="short" />
        </div>
        <input type="hidden" name="db_test" value="db_test" />
      </fieldset>
      
      <div id="license">
        <h2>License Agreement</h2>
        <textarea rows="10" cols="50" id="license_textarea"><?php echo file_get_contents('license.txt') ?></textarea>
        <p><label for="sla_agreed"><input type="checkbox" name="sla_agreed" id="sla_agreed" style="width: auto" /> I Accept</label></p>
      </div>
      
      <input type="hidden" name="submitted" value="submitted" />
      
      <div id="buttons">
        <button type="submit"><span>Install</span></button>
      </div>
    </form>
    <?php } // if ?>
<?php } else { ?>
    <p>There are some errors that you will need to fix before you can continue with installation process.</p>
<?php } // if ?>
      <div class="clear"></div>
    </div>
    <!-- / contentBlock -->
    <p id="footer">&copy; <?php print date('Y') ?> <a href="http://www.vbsupport.org/forum/index.php" target="_blank">NulleD By FintMax</a>. Все права зацарапаны.</p>
  </div>
</body>
</html>