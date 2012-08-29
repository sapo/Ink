<?php if(!isset($error) || !instance_of($error, 'Error')) return; ?>
<?php if(!$css_rendered) { ?>
<style type="text/css">
  div.error_dump table, div.error_dump th, div.error_dump tr, div.error_dump td {
    font-family: verdana, helvetica, sans-serif; 
    font-size: 12px; 
    background: whote; 
    color: black; 
    border-collapse: collapse; 
    border: 3px solid black;
  }
  
  div.error_dump th {
    background: red;
    color: white;
  }
  
  div.error_dump tr, div.error_dump th, div.error_dump td {
    padding: 5px;
    border: 1px solid #ccc;
  }
  
  div.error_dump .bold {
    font-weight: bolder;
  }
  
  div.error_dump .monospace {
    font-family: "Courier new", monospace;
  }
  
</style>
<?php $css_rendered = true; ?>
<?php } // if ?>
<div class="error_dump">
<table style="width: 900px; background: white;">
  <tr>
    <th colspan="2">Error (<?php echo get_class($error) ?>)</th>
  </tr>
  <tr>
    <td colspan="2" style="padding: 15px 7px"><?php echo clean($error->getMessage()) ?></td>
  </tr>
<?php if(is_array($error->getParams())) { ?>
  <tr>
    <td class="bold" colspan="2">Error params:</td>
  </tr>
<?php foreach($error->getParams() as $param_name => $param_value) { ?>
  <tr>
    <td style="width: 100px"><?php echo clean(ucfirst($param_name)) ?>:</td>
    <td class="monospace">
<?php if(is_scalar($param_value)) { ?>
      <?php echo clean($param_value) ?>
<?php } elseif(is_null($param_value)) { ?>
      NULL
<?php } else { ?>
      <?php echo pre_var_dump($param_value) ?>
<?php } // if ?>
    </td>
  </tr>
<?php } // foreachs ?>
<?php } ?>

  <tr>
    <td class="bold" colspan="2">Backtrace:</td>
  </tr>
  <tr>
    <td colspan="2" style="line-height: 150%;"><pre style="overflow: auto"><?php echo clean($error->getBacktrace()) ?></pre></td>
  </tr>
  <tr>
    <td class="bold" colspan="2">Autoglobal varibles:</td>
  </tr>
  <tr>
    <td>$_GET:</td>
    <td class="monospace">
<?php if(empty($_GET)) { ?>
      empty
<?php } else { ?>
    <?php echo pre_var_dump($_GET) ?></td>
<?php } // if ?>
  </tr>
  <tr>
    <td>$_POST:</td>
    <td class="monospace">
<?php if(empty($_POST)) { ?>
      empty
<?php } else { ?>
    <?php echo pre_var_dump($_POST) ?>
<?php } // if ?>
    </td>
  </tr>
  <tr>
    <td>$_COOKIE:</td>
    <td class="monospace">
<?php if(empty($_COOKIE)) { ?>
      empty
<?php } else { ?>
    <?php echo pre_var_dump($_COOKIE) ?>
<?php } // if ?>
    </td>
  </tr>
  <tr>
    <td>$_SESSION:</td>
    <td class="monospace">
<?php if(empty($_SESSION)) { ?>
      empty
<?php } else { ?>
    <?php echo pre_var_dump($_SESSION) ?>
<?php } // if ?>
    </td>
  </tr>
  
</table>
</div>