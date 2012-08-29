<?php echo '<?php' ?>

<?php foreach($config_file_constants as $config_file_constant_key => $config_file_constant_value) { ?>
  define('<?php echo $config_file_constant_key ?>', <?php echo var_export($config_file_constant_value) ?>); 
<?php } // foreach ?>

  require_once 'defaults.php';
<?php echo '?>' ?>