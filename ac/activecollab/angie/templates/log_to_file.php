<?php if(isset($this) && instance_of($this, 'Logger')) { ?>
===================================================================================
| Logged on <?php echo date('F j, Y, g:i a') ?> (<?php echo array_var($_SERVER, 'REMOTE_ADDR') ?>)
===================================================================================

<?php $counter = 0; ?>
<?php foreach($this->getEntries() as $log_entry) { ?>
<?php $counter++; ?>
Log entry #<?php echo $counter ?>: <?php echo array_var($log_entry, 'label') ?> 
-----------------------------------------------------------------------------------
<?php echo array_var($log_entry, 'content') ?>

<?php } // foreach ?>

<?php } // if ?>