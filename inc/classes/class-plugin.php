<?php
/**
 * Bootstrap the plugin.
 *
 * @package WP_Feedback
 */

namespace WP_Feedback\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WP_Feedback\Classes\Feedback_Manager;
use WP_Feedback\Classes\Ajax;
use WP_Feedback\Classes\Form\Shortcode;
use WP_Feedback\Classes\Post_Columns;

require_once WP_FEEDBACK_PLUGIN_DIR . '/inc/classes/class-feedback-manager.php';
require_once WP_FEEDBACK_PLUGIN_DIR . '/inc/classes/class-ajax.php';
require_once WP_FEEDBACK_PLUGIN_DIR . '/inc/classes/form/class-shortcode.php';
require_once WP_FEEDBACK_PLUGIN_DIR . '/inc/classes/class-post-columns.php';

/**
 * Plugin Class.
 */
class Plugin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize the plugin.
	 */
	public function init() {
		new Feedback_Manager();
		new Ajax();
		new Shortcode();
		new Post_Columns();
	}
}
