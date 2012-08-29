<fieldset id="upgrade_steps">
  <legend>Upgrade Steps</legend>
  
  <p>Current version is <?php echo $current_version ?>. When script is executed you will upgrade your setup to activeCollab v<?php echo $final_version ?>. Please <b style="text-decoration: underline">do not close this page</b>.</p>
  <p>Upgrade progress:</p>
<?php if(is_foreachable($scripts)) { ?>
  <ul>
    <li id="upgrade_step_1"><img src="assets/images/blank.gif" /> Create backup and prepare upgrade</li>
    <script type="text/javascript">
      UpgradeUtility.register_step('<?php echo $scripts[0]->getGroup() ?>', 'startUpgrade', 'upgrade_step_1');
    </script>
<?php $counter = 1; ?>
<?php foreach($scripts as $script) { ?>
<?php foreach($script->getActions() as $action => $description) { ?>
<?php $counter++; ?>
    <li id="upgrade_step_<?php echo $counter ?>"><img src="assets/images/blank.gif" /> <?php echo clean($description) ?></li>
    <script type="text/javascript">
      UpgradeUtility.register_step('<?php echo $script->getGroup() ?>', '<?php echo $action ?>', 'upgrade_step_<?php echo $counter ?>');
    </script>
<?php } // if ?>
<?php } // foreach?>
  </ul>
<?php } // if ?>
  <p style="display: none" id="all_done">All done! <a href="<?php echo ROOT_URL ?>">Enjoy</a>!</p>
</fieldset>
<script type="text/javascript">
  UpgradeUtility.next_step();
</script>