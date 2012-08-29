<?php
  //
  // +------------------------------------------------------------------------+
  // | PEAR :: Benchmark                                                      |
  // +------------------------------------------------------------------------+
  // | Copyright (c) 2001-2005 Sebastian Bergmann <sb@sebastian-bergmann.de>. |
  // +------------------------------------------------------------------------+
  // | This source file is subject to version 3.00 of the PHP License,        |
  // | that is available at http://www.php.net/license/3_0.txt.               |
  // | If you did not receive a copy of the PHP license and are unable to     |
  // | obtain it through the world-wide-web, please send a note to            |
  // | license@php.net so we can mail you a copy immediately.                 |
  // +------------------------------------------------------------------------+
  //
  // $Id: Timer.php,v 1.13 2005/05/24 13:42:06 toggg Exp $
  //

  /**
   * Provides timing and profiling information.
   *
   * Example 1: Automatic profiling start, stop, and output.
   *
   * <code>
   * <?php
   * require_once 'Benchmark/Timer.php';
   *
   * $timer = new Benchmark_Timer(TRUE);
   * $timer->setMarker('Marker 1');
   * ?>
   * </code>
   *
   * Example 2: Manual profiling start, stop, and output.
   *
   * <code>
   * <?php
   * require_once 'Benchmark/Timer.php';
   *
   * $timer = new Benchmark_Timer();
   * $timer->start();
   * $timer->setMarker('Marker 1');
   * $timer->stop();
   *
   * $timer->display(); // to output html formated
   * // AND/OR :
   * $profiling = $timer->getProfiling(); // get the profiler info as an associative array
   * ?>
   * </code>
   *
   * @author    Sebastian Bergmann <sb@sebastian-bergmann.de>
   * @author    Ludovico Magnocavallo <ludo@sumatrasolutions.com>
   * @copyright Copyright &copy; 2002-2005 Sebastian Bergmann <sb@sebastian-bergmann.de>
   * @license   http://www.php.net/license/3_0.txt The PHP License, Version 3.0
   * @category  Benchmarking
   * @package   Benchmark
   */
  class BenchmarkTimer extends AngieObject {
    
    /**
     * Contains the markers.
     *
     * @var    array
     * @access private
     */
    var $markers = array();
  
    /**
     * Auto-start and stop timer.
     *
     * @var    boolean
     * @access private
     */
    var $auto = FALSE;
  
    /**
     * Max marker name length for non-html output.
     *
     * @var    integer
     * @access private
     */
    var $maxStringLength = 0;
  
    /**
     * Constructor.
     *
     * @param  boolean $auto
     * @access public
     */
    function __construct($auto = FALSE) {
      $this->auto = $auto;
  
      if($this->auto) {
        $this->start();
      } // if
    } // __construct
  
    /**
     * Destructor
     */
    function _Benchmark_Timer() {
      if($this->auto) {
        $this->stop();
        $this->display();
      }
    }
  
    /**
     * Set "Start" marker.
     *
     * @see    setMarker(), stop()
     * @access public
     */
    function start() {
      $this->setMarker('Start');
    }
  
    /**
     * Set "Stop" marker.
     *
     * @see    setMarker(), start()
     * @access public
     */
    function stop() {
      $this->setMarker('Stop');
    }
  
    /**
     * Set marker.
     *
     * @param  string  $name Name of the marker to be set.
     * @see    start(), stop()
     * @access public
     */
    function setMarker($name) {
      $this->markers[$name] = $this->_getMicrotime();
    }
  
    /**
     * Returns the time elapsed betweens two markers.
     *
     * @param  string  $start        start marker, defaults to "Start"
     * @param  string  $end          end marker, defaults to "Stop"
     * @return double  $time_elapsed time elapsed between $start and $end
     * @access public
     */
    function timeElapsed($start = 'Start', $end = 'Stop') {
      if ($end == 'Stop' && !isset($this->markers['Stop'])) {
        $this->markers['Stop'] = $this->_getMicrotime();
      }
  
      if (extension_loaded('bcmath')) {
        return bcsub($this->markers[$end], $this->markers[$start], 6);
      } else {
        return $this->markers[$end] - $this->markers[$start];
      }
    }
  
    /**
     * Returns profiling information.
     *
     * $profiling[x]['name']  = name of marker x
     * $profiling[x]['time']  = time index of marker x
     * $profiling[x]['diff']  = execution time from marker x-1 to this marker x
     * $profiling[x]['total'] = total execution time up to marker x
     *
     * @return array
     * @access public
     */
    function getProfiling() {
      $i = $total = 0;
      $result = array();
      $temp = reset($this->markers);
      $this->maxStringLength = 0;
  
      foreach ($this->markers as $marker => $time) {
        if (extension_loaded('bcmath')) {
          $diff  = bcsub($time, $temp, 6);
          $total = bcadd($total, $diff, 6);
        } else {
          $diff  = $time - $temp;
          $total = $total + $diff;
        }
  
        $result[$i]['start']  = $marker;
        $result[$i]['time']  = $time;
        $result[$i]['diff']  = $diff;
        $result[$i]['total'] = $total;
  
        $this->maxStringLength = (strlen($marker) > $this->maxStringLength ? strlen($marker) + 1 : $this->maxStringLength);
  
        $temp = $time;
        $i++;
      }
      
      // Fix result
      foreach($result as $k => $v) {
        if($k > 0) {
          $result[$k - 1]['stop'] = $v['start'];
          $result[$k - 1]['time'] = $v['time'];
          $result[$k - 1]['diff'] = $v['diff'];
          $result[$k - 1]['total'] = $v['total'];
        }
      }
      unset($result[count($result) - 1]);
      // End result fix
  
      //$result[0]['diff'] = '-';
      //$result[0]['total'] = '-';
      
      $this->maxStringLength = (strlen('total') > $this->maxStringLength ? strlen('total') : $this->maxStringLength);
      $this->maxStringLength += 2;
  
      return $result;
    }
  
    /**
     * Prints the information returned by getOutput().
     *
     * @param boolean $full Full report
     * @return null
     */
    function display($full = true) {
      static $css_rendered = false;
      
      // Template sufix
      $sufix = $full ? 'full' : 'brief';
      
      // Include...
      include ANGIE_PATH . "/templates/benchmark_timer_$sufix.php";
    } // display...
  
    /**
     * Wrapper for microtime().
     *
     * @return float
     * @access private
     * @since  1.3.0
     */
    function _getMicrotime() {
      $microtime = explode(' ', microtime());
      return $microtime[1] . substr($microtime[0], 1);
    }
    
    /**
     * Return single BenchmarkTimer instance
     *
     * @param boolean $auto Auto strat
     * @return BenchmarkTimer
     */
    function &instance($auto = false) {
      static $instance;
      if(!instance_of($instance, 'BenchmarkTimer')) {
        $instance = new BenchmarkTimer($auto);
      } // if
      return $instance;
    } // instance
    
  } // BenchmarkTimer
  
  // -----------------------------------------------------------
  //  Shortcut methods
  // -----------------------------------------------------------
  
  /**
   * Start timer
   * 
   * @param void
   * @return null
   */
  function benchmark_timer_start() {
    $timer =& BenchmarkTimer::instance();
    $timer->start();
  } // benchmark_timer_start
  
  /**
   * Stop timer
   *
   * @param void
   * @return null
   */
  function benchmar_timer_stop() {
    $timer =& BenchmarkTimer::instance();
    $timer->stop();
  } // benchmar_timer_stop
  
  /**
   * Set marker
   *
   * @param string $marker_name Marker name
   * @return null
   */
  function benchmark_timer_set_marker($marker_name) {
    $timer =& BenchmarkTimer::instance();
    $timer->setMarker($marker_name);
  } // benchmark_timer_set_marker
  
?>
