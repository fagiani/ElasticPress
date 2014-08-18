<?php

class EP_Cron {

	/**
	 * Placeholder method
	 *
	 * @since 0.1.0
	 */
	public function __construct() { }

	/**
	 * Setup actions and filters
	 *
	 * @since 0.1.2
	 */
	public function setup() {
		add_action( 'ep_sync', array( $this, 'sync' ) );
		add_action( 'init', array( $this, 'schedule_events' ) );
		add_filter( 'cron_schedules', array( $this, 'filter_cron_schedules' ) );
	}

	/**
	 * Setup cron jobs
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function schedule_events() {
		$timestamp = wp_next_scheduled( 'ep_sync' );

		if ( ! $timestamp ) {
			wp_schedule_event( time(), 'elasticpress', 'ep_sync' );
		}
	}

	/**
	 * Add custom cron schedule
	 *
	 * @param array $schedules
	 * @since 0.1.0
	 * @return array
	 */
	public function filter_cron_schedules( $schedules ) {
		$schedule = apply_filters( 'elasticpress_cron_schedule',
			array(
				'interval' => ( MINUTE_IN_SECONDS * 10 ),
				'display' => __( 'Every 10 minutes' , 'elasticpress' ),
			)
		);

		$schedules['elasticpress'] = $schedule;

		return $schedules;
	}

	/**
	 * Return singleton instance of class
	 *
	 * @since 0.1.0
	 * @return EP_Cron
	 */
	public static function factory() {
		static $instance = false;

		if ( ! $instance ) {
			$instance = new self();
			$instance->setup();
		}

		return $instance;
	}

	/**
	 * Send posts to Elasticsearch for indexing
	 *
	 * @since 0.1.0
	 */
	public function sync() {
		ep_do_scheduled_syncs();
	}

}

EP_Cron::factory();