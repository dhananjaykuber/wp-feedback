<?php
/**
 * Shortcode handler.
 *
 * @package WP_Feedback
 */

namespace WP_Feedback\Classes\Form;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WP_Feedback\Classes\Form\Base;

require_once WP_FEEDBACK_PLUGIN_DIR . '/inc/classes/form/class-base.php';

/**
 * Shortcode Class.
 */
class Shortcode extends Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_shortcode( 'wp_feedback', array( $this, 'render_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
	}

	/**
	 * Render the shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function render_shortcode( $atts ) {
		$attributes = shortcode_atts(
			array(
				'title'               => __( 'Was this helpful?', 'wp-feedback' ),
				'comment_placeholder' => __( 'Tell us why (optional)', 'wp-feedback' ),
				'submit_text'         => __( 'Submit Feedback', 'wp-feedback' ),
				'cancel_text'         => __( 'Cancel', 'wp-feedback' ),
				'class'               => '',
			),
			$atts,
			'wp_feedback'
		);

		// Convert to format expected by render_form
		$form_attributes = array(
			'title'              => $attributes['title'],
			'commentPlaceholder' => $attributes['comment_placeholder'],
			'submitText'         => $attributes['submit_text'],
			'cancelText'         => $attributes['cancel_text'],
			'className'          => $attributes['class'],
		);

		return $this->render_form( $form_attributes );
	}

	/**
	 * Register assets.
	 */
	public function register_assets() {
		wp_register_style(
			'wp-feedback-form',
			WP_FEEDBACK_PLUGIN_URL . '/assets/css/feedback-form.css',
			array(),
			WP_FEEDBACK_VERSION
		);

		wp_register_script(
			'wp-feedback-form',
			WP_FEEDBACK_PLUGIN_URL . '/assets/js/feedback-form.js',
			array(),
			WP_FEEDBACK_VERSION,
			true
		);

		wp_localize_script(
			'wp-feedback-form',
			'wpFeedbackForm',
			array(
				'ajaxurl'  => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'wp_feedback_nonce' ),
				'messages' => array(
					'success'    => __( 'Thank you for your feedback!', 'wp-feedback' ),
					'error'      => __( 'Something went wrong. Please try again.', 'wp-feedback' ),
					'submitting' => __( 'Submitting...', 'wp-feedback' ),
					'submit'     => __( 'Submit Feedback', 'wp-feedback' ),
				),
			)
		);
	}
}
