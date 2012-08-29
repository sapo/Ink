<?php if(!isset($this) || !instance_of($this, 'BenchmarkTimer')) return false; ?>
<?php if(!$css_rendered) { ?>
<style type="text/css">
  

  div.bencmark_timer_full_report table, div.bencmark_timer_full_report th, div.bencmark_timer_full_report tr, div.bencmark_timer_full_report td {
    font-family: verdana, helvetica, sans-serif; 
    font-size: 10px; 
    background: whote; 
    color: black; 
    border-collapse: collapse; 
    border: 3px solid black;
  }
  
  div.bencmark_timer_full_report th {
    background: red;
    color: white;
  }
  
  div.bencmark_timer_full_report tr, div.bencmark_timer_full_report th, div.bencmark_timer_full_report td {
    padding: 5px;
    border: 1px solid #ccc;
  }
  
  div.bencmark_timer_full_report .bold {
    font-weight: bolder;
  }
  
  div.bencmark_timer_full_report .monospace {
    font-family: "Courier new", monospace;
  }
  
  div.bencmark_timer_full_report .bencmark_timer_full_report {
    position: absolute;
    bottom: 5px;
    right: 5px;
  }
  
</style>
<?php $css_rendered = true; ?>
<?php } // if ?>

<?php $result = $this->getProfiling(); ?>
<div class="bencmark_timer_full_report">

<?php if(is_array($result)) { ?>
  <table class="bencmark_timer_full_report">
    <tr>
      <th>From</th>
      <th>To</th>
      <th>Time taken</th>
      <th>Step</th>
    </tr>
<?php foreach($result as $data) { ?>
    <tr>
      <td><?php echo $data['start'] ?></td>
      <td><?php echo $data['stop'] ?></td>
      <td><?php echo $data['diff'] ?> sec</td>
      <td><?php echo $data['total'] ?> sec</td>
    </tr>
<?php } // foreach?>
    <tr>
      <td colspan="3" style="text-align: right; font-weight: bolder">Total execution time:</td>
      <td><?php echo $this->TimeElapsed() ?> sec</td>
    </tr>
  </table>
<?php } else { ?>
  <p>Executed in: <?php echo $this->TimeElapsed() ?> seconds</p>
<?php } // if ?>
</div>