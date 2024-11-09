<?php
/**
 * Database table creation.
 *
 * @package WP_Feedback
 */

namespace WP_Feedback\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Database class.
 */
class Database {

	/**
	 * Table name without prefix.
	 *
	 * @var string
	 */
	const TABLE_NAME = 'post_feedback';

	/**
	 * Get table name with prefix.
	 *
	 * @return string
	 */
	public static function get_table_name() {
		global $wpdb;
		return $wpdb->prefix . self::TABLE_NAME;
	}

	/**
	 * Create tables.
	 */
	public static function create_tables() {
		global $wpdb;
		$table_name = self::get_table_name();

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            user_id bigint(20),
            feedback_type varchar(10) NOT NULL,
            comment text,
            ip_address varchar(45),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY post_id (post_id),
            KEY user_id (user_id),
            KEY feedback_type (feedback_type)
        ) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Drop tables.
	 */
	public static function drop_tables() {
		global $wpdb;
		$table_name = self::get_table_name();

		$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
	}
}
