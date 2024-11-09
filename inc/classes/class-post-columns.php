<?php
/**
 * Post Columns for feedback
 *
 * @package WP_Feedback
 */

namespace WP_Feedback\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post Columns Class.
 */
class Post_Columns {

	/**
	 * Initialize post type columns.
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'init_post_type_columns' ) );
	}

	/**
	 * Initialize post type columns.
	 */
	public function init_post_type_columns() {

		$post_types = get_post_types( array( 'public' => true ), 'names' );

		foreach ( $post_types as $post_type ) {
			add_filter( "manage_{$post_type}_posts_columns", array( $this, 'add_feedback_column' ) );
			add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'render_feedback_column' ), 10, 2 );
		}
	}

	/**
	 * Add feedback column.
	 *
	 * @param array $columns Columns.
	 * @return array
	 */
	public function add_feedback_column( $columns ) {

		$columns['feedback'] = __( 'Feedback', 'wp-feedback' );
		return $columns;
	}

	/**
	 * Render feedback column.
	 *
	 * @param string $column Column name.
	 * @param int    $post_id Post ID.
	 */
	public function render_feedback_column( $column, $post_id ) {

		if ( 'feedback' === $column ) {
			$positive = get_post_meta( $post_id, '_feedback_count_positive', true );
			$negative = get_post_meta( $post_id, '_feedback_count_negative', true );

			echo '<strong>' . __( 'Positive:', 'wp-feedback' ) . '</strong> ' . $positive . '<br>';
			echo '<strong>' . __( 'Negative:', 'wp-feedback' ) . '</strong> ' . $negative;
		}
	}
}
