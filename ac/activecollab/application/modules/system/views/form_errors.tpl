<?php if(isset($error) && instance_of($error, 'Error')) { ?>
<!-- Form errors -->
<div id="formErrors">
  <!--<p><?= clean($error->getMessage()) ?></p>-->
  <p>Errors:</p>
<?php if(instance_of($error, 'DAOValidationError')) { ?>
  <ul>
<?php foreach($error->getErrors() as $err) { ?>
    <li><?= clean($err) ?></li>
<?php } // foreach ?>
  </ul>
<?php } elseif(instance_of($error, 'FormErrors')) { ?>
  <ul>
<?php foreach($error->getErrors() as $field_errors) { ?>
<?php if(is_foreachable($field_errors)) { ?>
<?php foreach($field_errors as $field_error) { ?>
    <li><?= clean($field_error) ?></li>
<?php } // foreach ?>
<?php } // if ?>
<?php } // foreach ?>
  </ul>
<?php } else { ?>
  <ul>
    <li><?= clean($error->getMessage()) ?></li>
  </ul>
<?php } // if ?>
</div>
<?php } // if ?>