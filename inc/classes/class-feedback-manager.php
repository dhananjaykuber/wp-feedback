<?php
/**
 * Feedback Manager.
 *
 * @package WP_Feedback
 */

namespace WP_Feedback\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Feedback Manager Class.
 */
class Feedback_Manager {

	/**
	 * Get feedback for post.
	 *
	 * @param int   $post_id Post ID.
	 * @param array $args    Query arguments.
	 * @return array
	 */
	public static function get_feedback( $post_id, $args = array() ) {
		global $wpdb;
		$table_name = Database::get_table_name();

		$default_args = array(
			'orderby' => 'created_at',
			'order'   => 'DESC',
			'limit'   => 10,
			'offset'  => 0,
		);

		$args = wp_parse_args( $args, $default_args );

		$query = $wpdb->prepare(
			"SELECT * FROM $table_name
            WHERE post_id = %d
            ORDER BY {$args['orderby']} {$args['order']}
            LIMIT %d OFFSET %d",
			$post_id,
			$args['limit'],
			$args['offset']
		);

		return $wpdb->get_results( $query );
	}

	/**
	 * Add feedback
	 *
	 * @param array $data Feedback data.
	 * @return int|false
	 */
	public static function add_feedback( $data ) {
		global $wpdb;
		$table_name = Database::get_table_name();

		$defaults_args = array(
			'user_id'    => get_current_user_id(),
			'ip_address' => $_SERVER['REMOTE_ADDR'],
			'created_at' => current_time( 'mysql' ),
		);

		$data = wp_parse_args( $data, $defaults_args );

		$result = $wpdb->insert(
			$table_name,
			$data,
			array(
				'%d', // post_id.
				'%d', // user_id.
				'%s', // feedback_type.
				'%s', // comment.
				'%s', // ip_address.
				'%s', // created_at.
			)
		);

		return $result ? $wpdb->insert_id : false;
	}
}
