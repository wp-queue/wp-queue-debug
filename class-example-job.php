<?php
use WP_Queue\Job;

class Example_Job extends Job {

	/**
	 * @var int
	 */
	public $user_id;

	/**
	 * Subscribe_User_Job constructor.
	 *
	 * @param int $user_id
	 */
	public function __construct( $user_id ) {
		$this->user_id = $user_id;
	}

	/**
	 * Handle job logic.
	 */
	public function handle() {
		error_log( 	$this->user_id );
	}

}
