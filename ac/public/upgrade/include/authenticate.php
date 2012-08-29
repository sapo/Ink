<?php

  if(!defined('IN_UPGRADE_SCRIPT')) {
    die('Behave! :)');
  } // if

?><fieldset id="authenticate">
  <legend>Authenticate</legend>
  
<?php if(isset($authentication_error)) { ?>
  <p style="padding: 5px 10px; background: #e8e8e8;"><span style="color: red; font-weight: bold">Error:</span> <?php echo clean($authentication_error) ?></p>
<?php } // if ?>
  
  <form>
    <label for="adminEmail">Email Address</label>
    <input type="text" id="adminEmail" value="<?php echo isset($email) ? clean($email) : '' ?>" />
    
    <label for="adminPassword">Password</label>
    <input type="password" id="adminPassword" value="<?php echo isset($password) ? clean($password) : '' ?>" />
    
    <div id="buttons">
      <button type="submit"><span>Submit</span></button>
    </div>
  </form>
</fieldset>

<!-- Init authentication form -->
<script type="text/javascript">
  UpgradeUtility.init_authentication_form();
</script>