<?php if(!isset($this) || !instance_of($this, 'BenchmarkTimer')) return false; ?>
<div class="brief_benchmark_timer_report">Executed in <?php echo $this->TimeElapsed() ?> seconds</div>