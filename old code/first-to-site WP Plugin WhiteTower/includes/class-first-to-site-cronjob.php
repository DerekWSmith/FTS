<?php

/**
 * Define the Wordpress Cronjob functionality
 *
 *
 * @link       https://whitetower.com.au/
 * @since      1.0.0
 *
 * @package    First_To_Site
 * @subpackage First_To_Site/includes
 */

/**
 * Define the Cronjob functionality.
 *
 * Loads and defines the Cronjob for this plugin
 *
 * @since      1.0.0
 * @package    First_To_Site
 * @subpackage First_To_Site/includes
 * @author     Whitetower Digital <services@whitetower.com.au>
 */
class First_To_Site_Cronjob {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_email_expire_checker() {
		// error_log('load_email_expire_checker start');

		// Custom WP query email_query
		$args_email_query = array(
			'post_type' => array('email'),
			'post_status' => array('publish'),
			'posts_per_page' => -1,
			'order' => 'DESC',
			'meta_query'	=> array(
				'relation'		=> 'AND',
				array(
					'key'	 	=> 'email_submit',
					'value'	  	=> '1',
					'compare' 	=> '=',
				),
				array(
					'key'	  	=> 'sla-status',
					'value'	  	=> 'late',
					'compare' 	=> '!=',
				),
			),
		);

		$email_query = new WP_Query( $args_email_query );

		if ( $email_query->have_posts() ) {
			while ( $email_query->have_posts() ) {

				$email_query->the_post();
				$post_id = get_the_ID();
				// error_log($post_id);
				$sla_id = get_field('sla');
				$sla_timeframe = get_field('sla_time_frame', $sla_id);

				$date = new DateTime("now", new DateTimeZone('Australia/Melbourne') );
				$current = $date->getTimestamp ();
				$email_submit_timestamp = get_post_meta($post_id, 'email_submit_date', true);
				$email_submit_date = new DateTime("now", new DateTimeZone('Australia/Melbourne') );
				$email_submit_date->setTimestamp($email_submit_timestamp);

				$interval = $date->diff($email_submit_date);
				// error_log(print_r($interval,true));
				if($interval->invert > 0 && $interval->days > $sla_timeframe) {
					update_field('sla-status', 'late', $post_id);

				}
			}
		} 

		wp_reset_postdata();
	}

}
