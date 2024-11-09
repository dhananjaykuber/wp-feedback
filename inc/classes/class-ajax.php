<?php
/**
 * AJAX for feedback.
 *
 * @package WP_Feedback
 */

namespace WP_Feedback\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AJAX Class.
 */
class Ajax {

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_action( 'wp_ajax_submit_feedback', array( $this, 'handle_submit_feedback' ) );
		add_action( 'wp_ajax_nopriv_submit_feedback', array( $this, 'handle_submit_feedback' ) );
	}

	/**
	 * Handle feedback submission.
	 */
	public function handle_submit_feedback() {
		// Verify nonce.
		if ( ! check_ajax_referer( 'wp_feedback_nonce', 'nonce', false ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Security check failed.', 'wp-feedback' ),
				)
			);
		}

		// Validate required fields.
		if ( empty( $_POST['post_id'] ) || empty( $_POST['feedback_type'] ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Required fields are missing.', 'wp-feedback' ),
				)
			);
		}

		// Sanitize and validate input.
		$post_id       = absint( $_POST['post_id'] );
		$feedback_type = sanitize_text_field( $_POST['feedback_type'] );
		$comment       = ! empty( $_POST['comment'] ) ? sanitize_textarea_field( $_POST['comment'] ) : '';

		// Validate feedback type.
		if ( ! in_array( $feedback_type, array( 'positive', 'negative' ) ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid feedback type.', 'wp-feedback' ),
				)
			);
		}

		// Validate post existence.
		if ( ! get_post( $post_id ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid post ID.', 'wp-feedback' ),
				)
			);
		}

		// Prepare feedback data.
		$feedback_data = array(
			'post_id'       => $post_id,
			'feedback_type' => $feedback_type,
			'comment'       => $comment,
			'user_id'       => get_current_user_id(),
			'ip_address'    => $this->get_client_ip(),
			'created_at'    => current_time( 'mysql' ),
		);

		// Add feedback.
		$feedback_id = Feedback_Manager::add_feedback( $feedback_data );

		if ( ! $feedback_id ) {
			wp_send_json_error(
				array(
					'message' => __( 'Failed to save feedback.', 'wp-feedback' ),
				)
			);
		}

		// Update post meta count.
		$this->update_feedback_count( $post_id, $feedback_type );

		wp_send_json_success(
			array(
				'message'     => __( 'Thank you for your feedback!', 'wp-feedback' ),
				'feedback_id' => $feedback_id,
			)
		);
	}

	/**
	 * Update feedback count for a post.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $feedback_type Feedback type.
	 */
	private function update_feedback_count( $post_id, $feedback_type ) {
		$meta_key      = '_feedback_count_' . $feedback_type;
		$current_count = get_post_meta( $post_id, $meta_key, true );
		$new_count     = (int) $current_count + 1;
		update_post_meta( $post_id, $meta_key, $new_count );
	}

	/**
	 * Get client IP address.
	 *
	 * @return string
	 */
	private function get_client_ip() {
		$ipaddress = '';

		if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED'] ) ) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		} elseif ( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		} elseif ( isset( $_SERVER['HTTP_FORWARDED'] ) ) {
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		}

		return sanitize_text_field( $ipaddress );
	}
}
