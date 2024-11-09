<?php
/**
 * Form base.
 *
 * @package WP_Feedback
 */

namespace WP_Feedback\Classes\Form;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Base Class.
 */
abstract class Base {

	/**
	 * Render the feedback form.
	 *
	 * @param array $attributes Form attributes.
	 * @return string
	 */
	protected function render_form( $attributes ) {
		// Enqueue required assets
		wp_enqueue_style( 'wp-feedback-form' );
		wp_enqueue_script( 'wp-feedback-form' );

		$post_id = get_the_ID();

		ob_start();

		?>

		<div 
			class="wp-feedback-form <?php echo esc_attr( $attributes['className'] ?? '' ); ?>" 
			data-post-id="<?php echo esc_attr( $post_id ); ?>"
		>
			
			<?php if ( ! empty( $attributes['title'] ) ) : ?>
				<h3 class="feedback-title">
					<?php echo esc_html( $attributes['title'] ); ?>
				</h3>
			<?php endif; ?>

			<div class="feedback-buttons">
				<button type="button" class="feedback-btn positive" data-type="positive">
					<img src="<?php echo esc_url( WP_FEEDBACK_PLUGIN_URL . '/assets/images/like.png' ); ?>" alt="<?php esc_attr_e( 'Like', 'wp-feedback' ); ?>">
					<?php esc_html_e( 'Like', 'wp-feedback' ); ?>
				</button>
				<button type="button" class="feedback-btn negative" data-type="negative">
					<img src="<?php echo esc_url( WP_FEEDBACK_PLUGIN_URL . '/assets/images/dislike.png' ); ?>" alt="<?php esc_attr_e( 'Dislike', 'wp-feedback' ); ?>">
					<?php esc_html_e( 'Dislike', 'wp-feedback' ); ?>
				</button>
			</div>

			<div class="feedback-comment-wrap" style="display: none;">
				<textarea 
					class="feedback-comment" 
					rows="3" 
					placeholder="<?php echo esc_attr( $attributes['commentPlaceholder'] ); ?>">
				</textarea>
				<div class="feedback-buttons">
					<button type="button" class="feedback-submit">
						<?php echo esc_html( $attributes['submitText'] ); ?>
					</button>
					<button type="button" class="feedback-cancel">
						<?php echo esc_html( $attributes['cancelText'] ); ?>
					</button>
				</div>
			</div>

			<div class="feedback-message" role="alert"></div>
		</div>

		<?php

		return ob_get_clean();
	}
}
