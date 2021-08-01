<?php

namespace Wb4Wp\Helpers;

/**
 * Class Performance_Helper
 * @package Wb4Wp\Helpers
 */
class Performance_Helper {

	private $start_time;
	private $current_time;
	private $delta_times;

	public function __construct() {
		$this->reset();
	}

	/**
	 * Starts the performance test and marks the current time as the start time.
	 */
	public function start() {
		$this->start_time   = microtime( true );
		$this->current_time = $this->start_time;
	}

	/**
	 * Marks the difference between the previous delta time (or start time) and now as a delta time entry.
	 *
	 * @param string $name Name of the delta time entry.
	 */
	public function get_delta_time( $name ) {
		$now   = microtime( true );
		$delta = $now - $this->current_time;

		$this->current_time         = $now;
		$this->delta_times[ $name ] = $delta;
	}

	/**
	 * Finishes the performance test and returns the values.
	 *
	 * @param string|null $name
	 *
	 * @return array
	 */
	public function finish( $name = null ) {
		$end_time = microtime( true );

		if ( null !== $name ) {
			$this->get_delta_time( $name );
			$end_time = $this->current_time;
		}

		$delta_times          = $this->delta_times;
		$delta_times['total'] = $end_time - $this->start_time;

		$this->reset();

		return $delta_times;
	}

	private function reset() {
		$this->start_time   = null;
		$this->current_time = null;
		$this->delta_times  = array();
	}

}
