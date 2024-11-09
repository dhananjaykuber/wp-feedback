<?php
/**
 * Plugin Name:       WP Feedback
 * Plugin URI:        https://github.com/dhananjaykuber/wp-feedback.git
 * Description:       Allow users to submit feedback on the front end.
 * Version:           1.0
 * Author:            Dhananjay Kuber
 * Author URI:        https://github.com/dhananjaykuber
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-feedback
 *
 * @package WP_Feedback
 */

namespace WP_Feedback;

use WP_Feedback\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define the plugin version.
if ( ! defined( 'WP_FEEDBACK_VERSION' ) ) {
	define( 'WP_FEEDBACK_VERSION', '1.0' );
}

if ( ! defined( 'WP_FEEDBACK_PLUGIN_URL' ) ) {
	define( 'WP_FEEDBACK_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
}

if ( ! defined( 'WP_FEEDBACK_PLUGIN_DIR' ) ) {
	define( 'WP_FEEDBACK_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
}

require_once WP_FEEDBACK_PLUGIN_DIR . '/inc/classes/class-plugin.php';
require_once WP_FEEDBACK_PLUGIN_DIR . '/inc/classes/class-database.php';

/**
 * Initialize the plugin.
 *
 * @return void
 */
function init() {
	new Classes\Plugin();
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\init' );

/**
 * Activation hook.
 */
function activate() {
	// Create tables.
	Classes\Database::create_tables();

	// Save the plugin version.
	update_option( 'wp_feedback_version', WP_FEEDBACK_VERSION );

	// Flush rewrite rules.
	flush_rewrite_rules();
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\activate' );

/**
 * Deactivation hook.
 */
function deactivate() {
	// Flush rewrite rules.
	flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, __NAMESPACE__ . '\deactivate' );


/**
 * Uninstall hook.
 */
function uninstall() {
	// Drop tables.
	Classes\Database::drop_tables();

	// Delete the plugin version.
	delete_option( 'wp_feedback_version' );

	// Flush rewrite rules.
	flush_rewrite_rules();
}

register_uninstall_hook( __FILE__, __NAMESPACE__ . '\uninstall' );
